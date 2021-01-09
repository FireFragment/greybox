<?php

namespace App\Http\Controllers;

use App\Client;
use App\Invoice;
use App\Mail\RegistrationConfirmation;
use App\Registration;
use Fakturoid\Exception as FakturoidException;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RegistrationController extends FakturoidController
{
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
    }

    public function showAll()
    {
        return response()->json(Registration::all());
    }

    public function showOne($id)
    {
        $registration = Registration::find($id);
        $this->authorize('showOne', $registration);

        $registration->person = $registration->person()->get();
        $registration->event = $registration->event()->get();
        $registration->role = $registration->role()->get();
        $registration->registered_by = $registration->registeredBy()->get();

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
        try {
            $registration = Registration::findOrFail($id);

            if ($request->has('person')) $this->updateColumn($registration, 'person', $request->input('person'));
            if ($request->has('note')) $this->updateColumn($registration, 'note', $request->input('note'));
            if ($request->has('event')) $this->updateColumn($registration, 'event', $request->input('event'));
            if ($request->has('role')) $this->updateColumn($registration, 'role', $request->input('role'));
            if ($request->has('accommodation')) $this->updateColumn($registration, 'accommodation', $request->input('accommodation'));
            if ($request->has('meals')) $this->updateColumn($registration, 'meals', $request->input('meals'));
            if ($request->has('team')) $this->updateColumn($registration, 'team', $request->input('team'));

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
        try {
            $fc = $this->getFakturoidClient();
            $registration = Registration::findOrFail($id);
            $registrationGroup = $registration->getRegistrationGroup();
            $event = $registration->event()->first();
            $data = new \stdClass();
            $invoice = new Invoice();
            $invoice->setDue(strtotime($event->soft_deadline));
            $user = $registration->registeredBy()->first();
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

            if (0 === count($registrationGroup)) {
                return response()->json(['message' => 'noRegistration'], 404);
            }
            $invoice->setRegistrationFeeLines($registration->getQuantifiedRoles(), $event);
            if ($event->membership_required) {
                $invoice->setMembershipFeeLines($registrationGroup);
            }
            $people = $invoice->getPeopleListForEmail($registrationGroup, $language);

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

            // TODO: vyřešit jak nastavit locale pouze pro email / případně jak používat locale vůbec
            app('translator')->setLocale($language);
            Mail::to($user->username)->bcc('info@debatovani.cz')->send(new RegistrationConfirmation($language, $event, $people, $invoice));

            $invoiceId = null;
            if (null !== $invoice) {
                $invoiceId = $invoice->id;
            }
            $registration->confirmRegistrationGroup($invoiceId);

            return response()->json($data, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}