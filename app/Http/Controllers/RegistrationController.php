<?php

namespace App\Http\Controllers;

use App\Client;
use App\Events\RegistrationConfirmed;
use App\Events\TeamsRegisteredEvent;
use App\Invoice;
use App\Mail\RegistrationConfirmation;
use App\Objects\RegistrationGroup;
use App\Registration;
use App\Repositories\RegistrationRepository;
use App\Services\PriceCalculatingService;
use Fakturoid\Exception as FakturoidException;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event as EventFacade;
use Illuminate\Support\Facades\Mail;

class RegistrationController extends FakturoidController
{
    private $repository;

    public function __construct()
    {
        // TBD
        $this->middleware('auth', ['only' => [
            'showAll',
            'showOne',
            'create',
            'update',
            'delete',
            'confirm'
        ]]);

        $this->repository = new RegistrationRepository();
        $this->setFakturoidClient();
    }

    public function showAll()
    {
        return response()->json(Registration::all());
    }

    public function showOne($id)
    {
        $registration = Registration::find($id);
        $this->authorize('showOne', $registration);

        $registration->person = $registration->person()->first();
        $registration->event = $registration->event()->first();
        $registration->role = $registration->role()->first();
        $registration->registered_by = $registration->registeredBy()->first();

        return response()->json($registration);
    }

    public function create(Request $request)
    {
        // TODO: add person
        $this->validate($request, [
            'event' => 'required'
        ]);

        $event = \App\Event::find($request->input('event'));
        if (null !== $event) {
            // TODO: allow for superadmin
            if (strtotime($event->hard_deadline) < time()) {
                return response()->json(['message' => 'missedDeadline'], 403);
            }
        } else {
            return response()->json(['message' => 'eventNotFound'], 404);
        }

        try {
            $registration = Registration::create([
                'person' => $request->input('person'),
                'note' => $request->input('note'),
                'event' => $request->input('event'),
                'role' => $request->input('role'),
                'accommodation' => $request->input('accommodation', 1),
                'meals' => $request->input('meals', 1),
                'team' => $request->input('team'),
                'novice' => $request->input('novice'),
                'registered_by' => \Auth::user()->id // to be checked
            ]);
            // TODO: refresh from DB https://laravel.com/docs/5.8/eloquent#retrieving-models
            return response()->json($registration, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            $code = $e->getCode();
            if (23000 == $code || 23505 == $code) {
                return response()->json(['message' => 'duplicateRegistration'], 409);
            }
            return response()->json(['message' => $e->getMessage(), 'code' => $code], 500);
        }        
    }
    
    public function update($id, Request $request)
    {
        $registration = Registration::findOrFail($id);
        $this->authorize('updateRegistration', $registration);

        try {
            if ($request->has('person')) $this->updateColumn($registration, 'person', $request->input('person'));
            if ($request->has('note')) $this->updateColumn($registration, 'note', $request->input('note'));
            if ($request->has('event')) $this->updateColumn($registration, 'event', $request->input('event'));
            if ($request->has('role')) $this->updateColumn($registration, 'role', $request->input('role'));
            if ($request->has('accommodation')) $this->updateColumn($registration, 'accommodation', $request->input('accommodation'));
            if ($request->has('meals')) $this->updateColumn($registration, 'meals', $request->input('meals'));
            if ($request->has('team')) $this->updateColumn($registration, 'team', $request->input('team'));
            if ($request->has('novice')) $this->updateColumn($registration, 'novice', $request->input('novice'));

            $registration->person = $registration->person()->first();
            $registration->person->dietary_requirement = $registration->person->dietaryRequirement()->first();
            if (!empty($registration->person->dietary_requirement)) $registration->person->dietary_requirement->name = $registration->person->dietary_requirement->translation()->first();
            $registration->team = $registration->team()->first();
            $registration->role = $registration->role()->first();
            $registration->role->name = $registration->role->translation()->first();

            return response()->json($registration, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            Registration::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function confirm($id, Request $request)
    {
        $registration = Registration::findOrFail($id);
        $event = $registration->event()->first();
        $user = $registration->registeredBy()->first();

        $registrationGroup = $registration->getRegistrationGroup();

        if ($registrationGroup->isEmpty()) {
            return response()->json(['message' => 'noRegistration'], 404);
        }

        $language = $request->input('lang', $user->preferredLocale());

        $calculator = new PriceCalculatingService($registrationGroup, $event, $language);

        $data = new \stdClass();
        $data->invoiceLines = $calculator->getInvoiceLines();
        $data->totalAmount = $calculator->getTotalPrice();

        $invoice = null;

        if ($data->totalAmount >= 0) {
            if ($request->has('client')) {
                $client = Client::findOrFail($request->input('client'));
            } else {
                // TODO: default client, nebo pořadí, nebo podle aktuálnosti?
                $client = $user->clients()->first();
                if ($client === null) {
                    $client = new Client();
                    $client->createFakturoidSubject($user);
                }
            }
            $data->client = $client;

            $invoice = new Invoice();
            $invoice->setDue(strtotime($event->soft_deadline));
            $invoice->setLanguage($language);
            $invoice->addUpToTotalAmount($data->totalAmount);
            $invoice->addLines($data->invoiceLines);
            $invoice->client = $client;
            $invoice->taxable_fulfillment_due = $event->end;
            $invoice->setTextNew($event->invoiceTextTranslation()->first());
            $invoice->createFakturoidInvoice();
            $invoice->setFullUrls($this->fakturoidClient);
            $data->invoice = $invoice;
        }

        EventFacade::dispatch(new RegistrationConfirmed($registration, $language, $invoice));



        // Temporary just for testing purposes
        return response()->json($data, 200);

        try {
            $fc = $this->fakturoidClient;
            $registration = Registration::findOrFail($id);
            $registrationGroup = $registration->getRegistrationGroup();
            $event = $registration->event()->first();
            $data = new \stdClass();

            $invoice = new Invoice();
            $invoice->setDue(strtotime($event->soft_deadline));

            $user = $registration->registeredBy()->first();

            // solved
            $language = $user->preferredLocale();
            if ($request->has('lang'))
            {
                $language = $request->input('lang');
                if ('cs' === $language)
                {
                    $invoice->setLanguage('cz');
                }
                else
                {
                    $invoice->setLanguage($language);
                }
            }

            // solved
            if (0 === count($registrationGroup)) {
                return response()->json(['message' => 'noRegistration'], 404);
            }
            $invoice->setRegistrationFeeLines($registration->getQuantifiedRoles(), $event, $language);
            if ($event->membership_required) {
                $invoice->setMembershipFeeLines($registrationGroup);
            }

            /*if ($event->pds)
            {
                $invoice->setMissingAdjudicatorFeeLine($registration->countTeams(), $registration->countAdjudicators());
            }*/

            // TODO
            $people = $invoice->getPeopleListForEmail($registrationGroup, $language);

            // solved
            $data->invoiceLines = $invoice->lines;
            $data->totalAmount = $invoice->getTotalAmount();

            if ($invoice->getTotalAmount() > 0) {
                if ($request->has('client')) {
                    $client = Client::findOrFail($request->input('client'));
                } else {
                    // TODO: default client, nebo pořadí, nebo podle aktuálnosti?
                    $client = $user->clients()->first();
                    if ($client === null) {
                        $client = new Client();
                        $client->createFakturoidSubject($user);
                    }
                }
                $data->client = $client;
                $invoice->client = $client;
                $invoice->taxable_fulfillment_due = $event->end;
                $invoiceTextTranslation = $event->invoiceTextTranslation()->first();
                if (!empty($invoiceTextTranslation)) {
                    $invoice->setText($invoiceTextTranslation->cs);
                }
                $invoice->createFakturoidInvoice();
                $invoice->setFullUrls($fc);
                $data->invoice = $invoice;
            } else {
                $invoice = null;
            }

            // TODO
            $bccRecipients = $this->repository->getConfirmationEmailBccRecipients($event);
            // TODO: vyřešit jak nastavit locale pouze pro email / případně jak používat locale vůbec
            app('translator')->setLocale($language);
            Mail::to($user->username)->bcc($bccRecipients)->send(new RegistrationConfirmation($language, $event, $people, $invoice));

            // TODO
            $invoiceId = null;
            if (null !== $invoice) {
                $invoiceId = $invoice->id;
            }
            //$registration->confirmRegistrationGroup($invoiceId);

            return response()->json($data, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}