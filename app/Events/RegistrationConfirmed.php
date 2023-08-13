<?php

namespace App\Events;

use App\Registration;

class RegistrationConfirmed extends Event
{
    /**
     * @var Registration;
     */
    public $registration;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }
}