<?php

namespace App\Events;

use App\Membership,
    App\Person;

class MembershipInvoiced extends Event
{
    /**
     * @var Person;
     */
    public $person;

    /**
     * @var Membership;
     */
    public $membership;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Person $person = null, Membership $membership = null)
    {
        $this->person = $person;
        $this->membership = $membership;
    }
}