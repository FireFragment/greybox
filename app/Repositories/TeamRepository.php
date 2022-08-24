<?php


namespace App\Repositories;


use App\Team;

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

    public static function findDuplicate(\Illuminate\Http\Request $request)
    {
        return Team::where($request->only(['name','registered_by']))->first();
    }
}
