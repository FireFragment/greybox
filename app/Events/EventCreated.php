<?php

namespace App\Events;

class EventCreated extends Event
{
    /**
     * @var \App\Event;
     */
    public $event;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(\App\Event $event)
    {
        $this->event = $event;
    }
}