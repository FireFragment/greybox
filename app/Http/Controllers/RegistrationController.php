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
            $user = \Auth::user();
            $reg = Registration::findOrFail($id);
            $event = $reg->event()->first();
            $data = new \stdClass();
            $people = [];
            $invoice = new Invoice($event->soft_deadline);

            $totalAmount = 0;
            // TODO: solve repetition with lazy/eager loading
            $registrations = $user->registrations()->where([['event_id', '=', $event->id],['confirmed', '=', false]]);
            if (0 === count($registrations->get())) {
                return response()->json(['message' => 'noRegistration'], 404);
            }
            foreach ($registrations->select('role', 'accommodation', \DB::raw('count(*) as quantity'))->groupBy('role', 'accommodation')->get() as $reg) {
                $role = \App\Role::findOrFail($reg->role);
                $prices = $role->prices()->where('event', $event->id)->get();
                // TODO: solve properly
                foreach ($prices as $price) {
                    $priceDescription = $price->translation()->first();
                    if ('Accommodation' == $priceDescription->en) {
                        if (false == $reg->accommodation) {
                            continue;
                        }
                        if ($event->isDiscountAvailable()) {
                            $invoice->setLine('sleva za včasnou platbu', $reg->quantity, 'osob', -150);
                        }
                    }
                    $unitPrice = $price->getAmount();
                    // TODO: vyřešit překlad
                    $invoice->setLine($role->translation()->first()->cs.' - '.$priceDescription->cs, $reg->quantity, 'osob', $unitPrice);
                    $totalAmount += $reg->quantity * $unitPrice;
                }
            }

            $membershipsCount = 0;
            // TODO: solve repetition with lazy/eager loading
            $registrations = $user->registrations()->where([['event_id', '=', $event->id],['confirmed', '=', false]]);
            foreach ($registrations->get() as $registration) {
                $person = $registration->person()->first();
                // TODO: to be deleted if person required in registration
                if (null !== $person) {
                    $membership = $person->membership()->first();
                    if (null === $membership) {
                        $membership = \App\Membership::create([
                            'person' => $person->id,
                            'beginning' => date('Y-m-d'),
                            'end' => \App\Membership::setForSeason()
                        ]);
                        $membershipsCount++;
                    } elseif ($membership->isExpired()) {
                        $this->updateColumn($membership, 'end', \App\Membership::setForSeason());
                        $membershipsCount++;
                    }
                    $roleName = $registration->role()->first()->translation()->first()->cs; // TODO: solve for English
                    $team = $registration->team()->first();
                    if (null !== $team) {
                        $people[$roleName][$team->name][] = $person->name . ' ' . $person->surname;
                    } else {
                        $people[$roleName]['emptyTeamName'][] = $person->name . ' ' . $person->surname;
                    }
                }
            }
            if ($membershipsCount > 0) {
                // TODO: solve translations and maybe add surnames, change unit a set price dynamically
                $invoice->setLine('členský příspěvek', $membershipsCount, 'osob', 50);
            }
            $totalAmount += ($membershipsCount * 50);

            $data->invoiceLines = $invoice->lines;
            $data->totalAmount = $totalAmount;

            if ($totalAmount > 0) {
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

                // TODO: vyřešit už existující invoice
                $invoiceData = [
                    'subject_id' => $client->fakturoid_id,
                    // TODO: vyřešit, proč se nepropisuje do faktur
                    'taxable_fulfillment_due' => $event->end,
                    'due' => $invoice->due,
                    'client' => $client->id,
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
            }

            // TODO: vyřešit jak nastavit locale pouze pro email / případně jak používat locale vůbec
            app('translator')->setLocale($user->preferredLocale());
            Mail::to($user->username)->bcc('info@debatovani.cz')->send(new RegistrationConfirmation($user->preferred_locale, $event, $people, $invoice));

            $registrations->update(['confirmed' => true]);

            return response()->json($data, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}