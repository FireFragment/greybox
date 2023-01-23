<?php

namespace App\Services;

use App\Person;

class TeamRulesCheckingService
{
    /** @var int */
    const MINIMUM_TEAM_SIZE = 3;
    /** @var int */
    const MAXIMUM_TEAM_SIZE = 5;

    /** @var OldGreyboxService */
    private $oldGreybox;

    /** @return void */
    public function __construct()
    {
        $this->oldGreybox = new OldGreyboxService();
    }

    /**
     * Check rules for team participation in a Debate League tournament
     *
     * @param string $teamName
     * @param Person[] $members
     * @param int|null $competitionId
     * @param bool $finals
     * @return string[]
     */
    public function checkTeamRules(string $teamName, array $members, int $competitionId = null, bool $finals = false): array
    {
        $warnings = array();

        $warnings = array_merge($warnings, $this->checkMembersCount(count($members)));

        if (null !== $competitionId)
        {
            $warnings = array_merge($warnings, $this->addWarningsAboutPastSharedTeams($teamName, $members, $competitionId));

            $pastMembersIds = $this->oldGreybox->getPastTeamDebaters($competitionId, $teamName);

            // old greybox unreachable
            if (false === $pastMembersIds)
            {
                $warnings[] = trans('messages.team.rules.breach.impossible_check.team');
            }

            if (!$this->teamRegistrationContainsPastTeamDebaters($pastMembersIds, $members))
            {
                $warnings[] = trans('messages.team.rules.breach.new_debaters');
            }

            if ($finals)
            {
                $notPastTeamDebaters = $this->teamRegistrationContainsOnlyPastTeamDebaters($pastMembersIds, $members);
                if (false === $notPastTeamDebaters)
                {
                    $warnings[] = trans('messages.team.rules.breach.finals.no_past_debaters');
                } elseif (count($notPastTeamDebaters))
                {
                    $warnings[] = trans('messages.team.rules.breach.finals.new_debaters') . $this->listDebaters($members) . '.';
                }
            }
        }
        return $warnings;
    }

    /**
     * Check the rules for the number of members
     *
     * @param int $membersCount
     * @return string[]
     */
    private function checkMembersCount(int $membersCount): array
    {
        $warnings = array();
        if (self::MINIMUM_TEAM_SIZE > $membersCount) $warnings[] = trans('messages.team.rules.breach.minimum');
        if (self::MAXIMUM_TEAM_SIZE < $membersCount) $warnings[] = trans('messages.team.rules.breach.maximum');
        return $warnings;
    }

    /**
     * Check the rules for sharing teams in the past tournaments
     *
     * @param string $teamName
     * @param array $members
     * @param int $competitionId
     * @return array
     */
    private function addWarningsAboutPastSharedTeams(string $teamName, array $members, int $competitionId): array
    {
        $warnings = array();
        $membersCount = count($members);
        for ($i = 0; $i < $membersCount; $i++)
        {
            $iOldGreyboxId = $members[$i]->old_greybox_id;
            if (null === $iOldGreyboxId)
            {
                $warnings[] = trans('messages.team.rules.breach.missing_old_greybox') . $this->listDebaters([$members[$i]]) . '.';
                continue;
            }

            for ($j = $i + 1; $j < $membersCount; $j++)
            {
                $jOldGreyboxId = $members[$j]->old_greybox_id;
                if (null === $jOldGreyboxId)
                {
                    $warnings[] = trans('messages.team.rules.breach.missing_old_greybox') . $this->listDebaters([$members[$j]]) . '.';
                    continue;
                }

                $sharedTeams = $this->oldGreybox->getPastSharedTeamsInTheSameTournament($competitionId, $iOldGreyboxId, $jOldGreyboxId);
                $debaters = array($members[$i], $members[$j]);

                // old greybox unreachable
                if (false === $sharedTeams)
                {
                    $warnings[] = trans('messages.team.rules.breach.impossible_check.debaters') . $this->listDebaters($debaters) . '.';
                    continue;
                }

                // These two people were not together in a team before
                if (empty($sharedTeams))
                {
                    continue;
                }

                // These two people were in multiple teams together -> something went wrong
                if (1 !== count($sharedTeams))
                {
                    $warnings[] = trans('messages.team.rules.breach.shared_team1') . $this->listDebaters($debaters)
                        . trans('messages.team.rules.breach.shared_teams2') . implode(', ', $sharedTeams)
                        . trans('messages.team.rules.breach.shared_team3') . $teamName . '.';
                    continue;
                }

                // These two people were together in the same team before
                if ($teamName === $sharedTeams[0])
                {
                    continue;
                }

                // These two people were together in a different team before
                $warnings[] = trans('messages.team.rules.breach.shared_team1') . $this->listDebaters($debaters)
                    . trans('messages.team.rules.breach.shared_team2') . implode(', ', $sharedTeams)
                    . trans('messages.team.rules.breach.shared_team3') . $teamName . '.';
            }
        }
        return $warnings;
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