<?php


namespace App\Repositories;


class TeamRepository extends AutofillRepository
{
    public static function getAutofillTeams($userRegisteredTeams, $deletedTeams, $eventRegisteredTeams)
    {
        return parent::getAutofill($userRegisteredTeams, $deletedTeams, $eventRegisteredTeams);
    }

    public static function getTeamsFromRegistrations($registrations)
    {
        $teams = array();
        foreach ($registrations as $registration)
        {
            $team = $registration->team()->first();
            if (!empty($team))
            {
                $teams[] = $team;
            }
        }
        return $teams;
    }
}
