<?php

namespace App\Listeners;

use App\Events\TeamsRegisteredEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\TeamRulesBreachWarning;

class SendTeamRulesBreachWarning
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param TeamsRegisteredEvent $event
     * @return void
     */
    public function handle(TeamsRegisteredEvent $event)
    {
        $debaters = $event->debaters;

        Mail::to('info@debatovani.cz')->send(new TeamRulesBreachWarning($debaters));
    }
}