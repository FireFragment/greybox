<?php

namespace App\Jobs;

use App\Invoice;
use App\Services\FakturoidClientService as Fakturoid;

class FakturoidInvoiceUpdateJob extends Job
{
    private $fakturoid;

    /**
     * Create a new job instance.
     *
     * @param Fakturoid $fakturoid
     * @return void
     */
    public function __construct(Fakturoid $fakturoid)
    {
        $this->fakturoid = $fakturoid;
    }

    /**
     * Get list of all open (= not in [paid, cancelled[] invoices,
     * compare them with Fakturoid and update their status
     *
     * @return void
     */
    public function handle()
    {
        $fakturoidInvoices = $this->fakturoid->getAllInvoices();

        foreach ($fakturoidInvoices as $fakturoidInvoice) {
            $invoice = Invoice::where('fakturoid_id', $fakturoidInvoice->id)->first();
            if (!empty($invoice)) {
                if ($invoice->isDifferentFromFakturoidInvoice($fakturoidInvoice)) {
                    $invoice->updateFromFakturoid($fakturoidInvoice);
                    $invoice->save();
                }
            }
        }
    }
}
