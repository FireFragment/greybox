<?php

namespace App\Http\Controllers;

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
                'name' => $request->input('name'),
                'surname' => $request->input('surname'),
                'birthdate' => $request->input('birthdate'),
                'id_number' => $request->input('id_number'),
                'street' => $request->input('street'),
                'city' => $request->input('city'),
                'zip' => $request->input('zip'),
                'note' => $request->input('note'),
                'event' => $request->input('event'),
                'event_id' => $request->input('event'),
                'role' => $request->input('role'),
                'accommodation' => $request->input('accommodation'),
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
            if ($request->has('event')) $this->updateColumn($registration, 'event_id', $request->input('event'));
            if ($request->has('role')) $this->updateColumn($registration, 'role', $request->input('role'));
            if ($request->has('accommodation')) $this->updateColumn($registration, 'accommodation', $request->input('accommodation'));
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

    public function confirm($id)
    {
        try {
            $fc = $this->getFakturoidClient();
            $registration = Registration::findOrFail($id);
            $registrationGroup = $registration->getRegistrationGroup();
            $event = $registration->event()->first();
            $data = new \stdClass();
            $invoice = new Invoice($event->soft_deadline);
            $user = $registration->registeredBy()->first();

            $registrations = $registrationGroup;

            if (0 === count($registrationGroup)) {
                return response()->json(['message' => 'noRegistration'], 404);
            }
            $invoice->setRegistrationFeeLines($registration->getQuantifiedRoles(), $event);
            $people = $invoice->setMembershipFeeLines($registrationGroup);

            $data->invoiceLines = $invoice->lines;
            $data->totalAmount = $invoice->getTotalAmount();

            if ($invoice->getTotalAmount() > 0) {
                $client = $user->clients()->first(); // TODO: default client? nebo to nějak udělat
                if ($client === null) {
                    
                    // TODO: přidat data z Person
                    $clientData['name'] = $user->username;
                    try {
                        $subject = $fc->createSubject($clientData);
                    } catch (FakturoidException $e) {
                        return response()->json([
                            'message' => 'fakturoidFull',
                            'fakturoidMessage' => $e->getMessage(),
                            'fakturoidCode' => $e->getCode()
                        ], 403);
                    }
                    $clientData['fakturoid_id'] = $subject->getBody()->id;
                    $clientData['country'] = $subject->getBody()->country;
                    $clientData['user'] = $user->id;
                    $client = \App\Client::create($clientData);
                }
                $data->client = $client;
                $invoice->client = $client->id;
                $invoice->taxable_fulfillment_due = $event->end;

                $invoiceText = null;
                $invoiceTextTranslation = $event->invoiceTextTranslation()->first();
                if (!empty($invoiceTextTranslation)) {
                    $invoiceText = $invoiceTextTranslation->cs;
                }

                // TODO: vyřešit už existující invoice
                $invoiceData = [
                    'subject_id' => $client->fakturoid_id,
                    // TODO: vyřešit, proč se nepropisuje do faktur
                    'taxable_fulfillment_due' => $event->end,
                    'due' => $invoice->due,
                    'client' => $client->id,
                    'note' => $invoiceText,
                    'lines' => $invoice->lines
                ];
                try {
                    $fi = $fc->createInvoice($invoiceData);
                } catch (FakturoidException $e) {
                    return response()->json([
                        'message' => 'clientNotFound',
                        'fakturoidMessage' => $e->getMessage(),
                        'fakturoidCode' => $e->getCode()
                    ], 404);
                }
                $fakturoidInvoice = $fi->getBody();
                $invoice->setFakturoidData($fakturoidInvoice);
                $invoice->generateQr();
                $invoice->save();
                $invoice->setFullUrls($fc);
                $data->invoice = $invoice;
            } else {
                $invoice = null;
            }

            // TODO: vyřešit jak nastavit locale pouze pro email / případně jak používat locale vůbec
            app('translator')->setLocale($user->preferredLocale());
            Mail::to($user->username)->bcc('info@debatovani.cz')->send(new RegistrationConfirmation($user->preferred_locale, $event, $people, $invoice));

            $invoiceId = null;
            if (null !== $invoice) {
                $invoiceId = $invoice->id;
            }
            //$registrations->update(['confirmed' => true, 'invoice' => $invoiceId]);

            return response()->json($data, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}