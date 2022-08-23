<?php

namespace App\Events;

class TeamsRegisteredEvent extends Event
{
    public $debaters;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($debatersInTeams)
    {
        $this->debaters = $debatersInTeams;
    }
}
