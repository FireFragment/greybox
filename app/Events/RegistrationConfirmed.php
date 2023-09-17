<?php

namespace App\Events;

use App\Invoice,
    App\Registration;

class RegistrationConfirmed extends Event
{
    /* @var Registration */
    public $registration;

    /* @var string */
    public $language;

    /* @var Invoice */
    public $invoice;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Registration $registration, string $language = null, Invoice $invoice = null)
    {
        $this->registration = $registration;
        $this->language = $language;
        $this->invoice = $invoice;
    }
}