<?php

namespace App\Events;

class TeamsRegisteredEvent extends Event
{
    /**
     * @var int
     */
    public $competitionId;

    /**
     * @var array
     */
    public $debatersInTeams;

    /**
     * @var bool
     */
    public $finals;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(int $competitionId, array $debatersInTeams, bool $finals)
    {
        $this->competitionId = $competitionId;
        $this->debatersInTeams = $debatersInTeams;
        $this->finals = $finals;
    }
}
