<?php

namespace App\Http\Controllers;

abstract class FakturoidController extends Controller
{
    /*
     * @return Fakturoid\Client
     * TODO: To be deleted
     */
    public function getFakturoidClient()
    {
        return new \Fakturoid\Client(env('FAKTUROID_SLUG'), env('FAKTUROID_EMAIL'), env('FAKTUROID_API_KEY'), env('FAKTUROID_USER_AGENT'));
    }

    public function fillInvoiceData(array $data, $fakturoidInvoice)
    {
        $data['fakturoid_id'] = $fakturoidInvoice->id;
        $data['number'] = $fakturoidInvoice->number;
        $data['status'] = $fakturoidInvoice->status;
        $data['issued_on'] = $fakturoidInvoice->issued_on;
        $data['due_on'] = $fakturoidInvoice->due_on;
        $data['currency'] = $fakturoidInvoice->currency;
        $data['language'] = $fakturoidInvoice->language;
        $data['total'] = $fakturoidInvoice->total;
        $data['paid_amount'] = $fakturoidInvoice->paid_amount;

        return $data;
    }
}
