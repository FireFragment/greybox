<?php

namespace App\Listeners;

use App\Events\TeamsRegisteredEvent;
use App\Services\OldGreyboxService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\TeamRulesBreachWarning;

class SendTeamRulesBreachWarning
{
    /**
     * @var OldGreyboxService
     */
    private $oldGreybox;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->oldGreybox = new OldGreyboxService();
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

        $data = $this->oldGreybox->getPastTeamDebaters(38, 'Ewoci');

        Mail::to('info@debatovani.cz')->send(new TeamRulesBreachWarning($data));
    }
}