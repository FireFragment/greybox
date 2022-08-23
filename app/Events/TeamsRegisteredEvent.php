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
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(int $competitionId, array $debatersInTeams)
    {
        $this->competitionId = $competitionId;
        $this->debatersInTeams = $debatersInTeams;
    }
}
