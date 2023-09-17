<?php

namespace App\Listeners;

use App\Events\RegistrationConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConfirmRegistrationAndLinkInvoice implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  RegistrationConfirmed $event
     * @return void
     */
    public function handle(RegistrationConfirmed $event)
    {
        $registration = $event->registration;
        $invoice = $event->invoice;
        $registrationGroup = $registration->getRegistrationGroup();

        $invoiceId = null;
        if (null !== $invoice) {
            $invoiceId = $invoice->id;
        }
        $registrationGroup->update($invoiceId);
    }
}