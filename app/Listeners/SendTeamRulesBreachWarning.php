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
        $debatersInTeams = $event->debatersInTeams;

        foreach ($debatersInTeams as $teamName => $members)
        {
            $this->addWarningsAboutPastSharedTeams($members, $event->competitionId, $teamName);

            $pastTeamDebatersIds = $this->oldGreybox->getPastTeamDebaters($event->competitionId, $teamName);

            if (!$this->teamRegistrationContainsPastTeamDebaters($pastTeamDebatersIds, $members))
            {
                $this->warnings[] = 'Za tým ' . $teamName . ' na předchozích turnajích nikdo z této registrace nedebatoval. (Debatéři v registraci: ' . $this->listDebaters($members) . '.)';
            }
            if ($event->finals)
            {
                $notPastTeamDebaters = $this->teamRegistrationContainsOnlyPastTeamDebaters($pastTeamDebatersIds, $members);
                if (false === $notPastTeamDebaters)
                {
                    $this->warnings[] = 'Tým ' . $teamName . ' je přihlášený na finále, ale na předchozích turnajích nemá žádné debatéry.';
                }
                elseif (count($notPastTeamDebaters))
                {
                    $this->warnings[] = 'Za tým ' . $teamName . ' přihlášený na finále na předchozích turnajích nedebatovali tito debatéři z této registrace: ' . $this->listDebaters($members) . '.';
                }
            }
        }

        Mail::to('info@debatovani.cz')->send(new TeamRulesBreachWarning($this->warnings));
    }

    private function addWarningsAboutPastSharedTeams(array $teamRegisteredDebaters, int $competitionId, string $teamName): void
    {
        $teamRegisteredDebatersCount = count($teamRegisteredDebaters);

        for ($i = 0; $i < $teamRegisteredDebatersCount; $i++)
        {
            for ($j = $i + 1; $j < $teamRegisteredDebatersCount; $j++)
            {
                $sharedTeams = $this->oldGreybox->getPastSharedTeamsInTheSameTournament($competitionId, $teamRegisteredDebaters[$i]->old_greybox_id, $teamRegisteredDebaters[$j]->old_greybox_id);
                $person1 = $teamRegisteredDebaters[$i]->name . ' ' . $teamRegisteredDebaters[$i]->surname;
                $person2 = $teamRegisteredDebaters[$j]->name . ' ' . $teamRegisteredDebaters[$j]->surname;

                // These two people were not together in a team before
                if (empty($sharedTeams))
                {
                    continue;
                }

                // These two people were in multiple teams together -> something went wrong
                if (1 !== count($sharedTeams))
                {
                    $this->warnings[] = 'Debatéři ' . $person1  . ' a ' . $person2 . ' spolu debatovali ve více týmech ' . implode(', ', $sharedTeams) . ' a teď jsou přihlášeni za tým ' . $teamName . '.';
                    continue;
                }

                // These two people were together in the same team before
                if ($teamName === $sharedTeams[0]) {
                    continue;
                }

                // These two people were together in a different team before
                $this->warnings[] = 'Lidi ' . $person1  . ' a ' . $person2 . ' spolu debatovali v týmu ' . $sharedTeams[0] . ', ale teď jsou přihlášeni za tým ' . $teamName . '.';
            }
        }
    }

    /**
     * @param int[] $pastTeamDebaters
     * @param Person[] $teamRegisteredDebaters
     * @return bool
     *
     * Odstavec 3.1: Není přípustné, aby pod stejným názvem týmu přijeli na turnaj pouze debatéři, kteří na žádném z předchozích turnajů v témže jazyce ještě za tento tým nedebatovali.
     */
    private function teamRegistrationContainsPastTeamDebaters(array $pastTeamDebaters, array $teamRegisteredDebaters): bool
    {
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

    /**
     * @param int[] $pastTeamDebaters
     * @param Person[] $teamRegisteredDebaters
     * @return false|array
     *
     * Odstavec 3.2: Na finálovém turnaji smí za tým debatovat jen ti debatéři, kteří za tým debatovali v některém z kvalifikačních turnajů ve stejném jazyce.
     */
    private function teamRegistrationContainsOnlyPastTeamDebaters(array $pastTeamDebaters, array $teamRegisteredDebaters)
    {
        // New team -> can't be for finals
        if (!count($pastTeamDebaters)) {
            return false;
        }

        $notPastTeamDebaters = [];

        foreach ($teamRegisteredDebaters as $registeredDebater)
        {
            // This person was debating for this team before
            if (!in_array($registeredDebater->old_greybox_id, $pastTeamDebaters))
            {
                $notPastTeamDebaters[] = $registeredDebater;
            }
        }

        return $notPastTeamDebaters;
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