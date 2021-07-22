<?php

namespace App\Events;

use App\Models\Debate;

class DebateDeletedEvent extends Event
{
    /*
     * @var Debate
     */
    public $debate;

    /**
     * Create a new event instance.
     *
     * @param   Debate  $debate
     * @return  void
     */
    public function __construct(Debate $debate)
    {
        $this->debate = $debate;
    }
}
