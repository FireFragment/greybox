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
     * @var int
     */
    private $competitionId;

    /**
     * @var string[]
     */
    private $warnings = array();

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
        $this->competitionId = $event->competitionId;
        $debatersInTeams = $event->debatersInTeams;

        foreach ($debatersInTeams as $teamName => $members)
        {
            if (!$this->teamRegistrationContainsPastTeamDebaters($members, $teamName))
            {
                $this->warnings[] = 'Za tým ' . $teamName . ' na předchozích turnajích nikdo z této registrace nedebatoval. (Debatéři v registraci: ' . $this->listDebaters($members) . '.)';
            }
        }

        Mail::to('info@debatovani.cz')->send(new TeamRulesBreachWarning($this->warnings));
    }

    /**
     * @param array $teamRegisteredDebaters
     * @param string $teamName
     * @param int $competitionId
     * @return bool
     *
     * Odstavec 3.1: Není přípustné, aby pod stejným názvem týmu přijeli na turnaj pouze debatéři, kteří na žádném z předchozích turnajů v témže jazyce ještě za tento tým nedebatovali.
     */
    private function teamRegistrationContainsPastTeamDebaters(array $teamRegisteredDebaters, string $teamName): bool
    {
        $pastTeamDebaters = $this->oldGreybox->getPastTeamDebaters($this->competitionId, $teamName);

        // New team
        if (!count($pastTeamDebaters))
        {
            return true;
        }

        foreach ($teamRegisteredDebaters as $registeredDebater)
        {
            // This person was debating for this team before
            if (in_array($registeredDebater->old_greybox_id, $pastTeamDebaters))
            {
                return true;
            }
        }

        return false;
    }

    private function listDebaters(array $debaters): string
    {
        $list = '';
        foreach ($debaters as $debater)
        {
            $list .= $debater->name . ' ' . $debater->surname . ', ';
        }
        return rtrim($list, ', ');
    }
}