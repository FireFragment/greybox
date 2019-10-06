<?php

namespace App\Http\Controllers;

use App\Client;
use App\Invoice;
use Fakturoid\Exception as FakturoidException;
use Illuminate\Http\Request;

class InvoiceController extends FakturoidController
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => [
            'showAll',
            'showOne',
            'create',
            'update',
            'delete'
        ]]);
    }

    public function showAll()
    {
        return response()->json(Invoice::all());
    }

    public function showOne($id)
    {
        $invoice = Invoice::find($id);
        return response()->json($invoice);
    }

    // TODO: pdf
    public function showOnePdf($id)
    {
        $invoice = Invoice::find($id);
        $fc = $this->getFakturoidClient();
        $invoicePdf = $fc->getInvoicePdf($invoice->fakturoid_id);
        //print_r($invoicePdf);
        return response()->download($invoicePdf->getBody());
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'client' => 'required'
        ]);

        try {
            $client = Client::find($request->input('client'));

            $data = [
                'subject_id' => $client->fakturoid_id,
                // TODO: nenastavovat automaticky
                'taxable_fulfillment_due' => "2019-07-21",
                'client' => $client->id
            ];

            try {
                $fc = $this->getFakturoidClient();

                $fi = $fc->createInvoice($data);
                $fakturoidInvoice = $fi->getBody();

                $data = $this->fillInvoiceData($data, $fakturoidInvoice);

                try {
                    $invoice = Invoice::create($data);
                    $invoice->update(['qr_url' => $invoice->generateQr()]);
                    return response()->json($invoice, 201);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['message' => $e->getMessage()], 500);
                }
            } catch (FakturoidException $e) {
                return response()->json($e->getMessage(), $e->getCode());
            }
        } catch (\Exception $e) {
            return response()->json('Client not found.', 404);
        }
    }
    
    public function update($id, Request $request)
    {
        $data = $request->all();

        try {
            $invoice = Invoice::findOrFail($id);
            $newClient = Client::find($request->input('client'));
            $data = [
                'subject_id' => $newClient->fakturoid_id,
                'client' => $newClient->id
            ];

            try {
                $fc = $this->getFakturoidClient();
                $fi = $fc->updateInvoice($invoice->fakturoid_id, $data);

                try {
                    $invoice->generateQr();
                    $invoice->update($data);
                    return response()->json($invoice, 200);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 500);
                }
            } catch (FakturoidException $e) {
                return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 404);
        }
    }

    public function delete($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            try {
                $fc = $this->getFakturoidClient();
                $fc->deleteInvoice($invoice->fakturoid_id);
                try {
                    $invoice->delete();
                    return response()->json(['message' => 'Deleted successfully.'], 204);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 500);
                }
            } catch (FakturoidException $e) {
                return response()->json([
                    'message' => 'invoiceNotFound',
                    'fakturoidMessage' => $e->getMessage(),
                    'fakturoidCode' => $e->getCode()
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 404);
        }
    }

    public function massUpdate() {
        $invoices = Invoice::all();
        $fc = $this->getFakturoidClient();

        foreach ($invoices as $invoice) {
            $invoice->fakturoidId = $invoice->fakturoid_id;
            $invoice->fakturoidData = $fc->getInvoice($invoice->fakturoid_id)->getBody();
        }

        return response()->json($invoices, 200);
    }
}