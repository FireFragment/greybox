<?php

namespace App\Http\Controllers;

use App\Registration;
use http\Env\Response;
use Illuminate\Http\Request;

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
            if (23000 == $code) {
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
            $registration = Registration::findOrFail($id);
            $event = $registration->event()->first();

            $totalAmount = 0;
            $invoiceLines = [];
            foreach ($user->registrations()->select('role', \DB::raw('count(*) as quantity'))->where('event_id', $event->id)->groupBy('role')->get() as $reg) {
                $role = \App\Role::findOrFail($reg->role);
                $price = $role->prices()->where('event', $event->id)->first();
                $invoiceLines[] = [
                    'name' => $role->translation()->first()->cs, // TODO: vyřešit překlad
                    'quantity' => $reg->quantity,
                    'unit_name' => 'osob', // TODO: vymyslet něco chytřejšího
                    'unit_price' => $price->amount
                ];
                $totalAmount += $reg->quantity * $price->amount;
            }
            $registration->invoiceLines = $invoiceLines;
            $registration->totalAmount = $totalAmount;

            if ($totalAmount > 0) {
                $client = $user->clients()->first(); // TODO: default client? nebo to nějak udělat
                if ($client === null) {
                    // TODO: přidat data z Person
                    $clientData['name'] = $user->username;
                    $subject = $fc->createSubject($clientData);
                    $clientData['fakturoid_id'] = $subject->getBody()->id;
                    $clientData['country'] = $subject->getBody()->country;
                    $clientData['user'] = $user->id;
                    $client = \App\Client::create($clientData);
                }
                $registration->client = $client;

                // TODO: vyřešit už existující invoice
                $invoiceData = [
                    'subject_id' => $client->fakturoid_id,
                    // TODO: vyřešit, proč se nepropisuje do faktur
                    'taxable_fulfillment_due' => $event->end,
                    'client' => $client->id,
                    'lines' => $invoiceLines
                ];
                $fi = $fc->createInvoice($invoiceData);
                $fakturoidInvoice = $fi->getBody();
                $invoiceData = $this->fillInvoiceData($invoiceData, $fakturoidInvoice);
                $invoice = \App\Invoice::create($invoiceData);
                $invoice->update(['qr_url' => $invoice->generateQr()]);
                $invoice->qr_full_url = "https://debate-greybox.herokuapp.com/qrs/$invoice->qr_url.png"; // TODO: nastavovat adresu dynamicky
                if ($invoice->getPdf($fc)) {
                    $invoice->pdf_url = $invoice->qr_url;
                    $invoice->pdf_full_url = "https://debate-greybox.herokuapp.com/invoices/$invoice->pdf_url.pdf";
                }

                $registration->invoice = $invoice;
            }

            return response()->json($registration, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}