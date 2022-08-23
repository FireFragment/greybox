<?php


namespace App\Services;


use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Integer;

class OldGreyboxService
{
    /**
     * @var string
     */
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://debatovani.cz/greybox/static.php?token=' . env('OLD_GREYBOX_SAFETY_TOKEN');
    }

    /**
     * @param int $competitionId
     * @param string $teamName
     * @return array
     */
    public function getPastTeamDebaters(int $competitionId, string $teamName): array
    {
        $url = $this->baseUrl;
        $url .= '&past_team_debaters=1';
        $url .= '&competition_id=' . $competitionId;
        $url .= '&team_name=' . $teamName;

        // TODO: obalit try-catch blokem
        $gb = file_get_contents($url);

        return(json_decode($gb));
    }

    public function getPastSharedTeamsInTheSameTournament(int $competitionId, int $person1Id, int $person2Id): array
    {
        $url = $this->baseUrl;
        $url .= '&past_shared_teams=1';
        $url .= '&competition_id=' . $competitionId;
        $url .= '&person1_id=' . $person1Id;
        $url .= '&person2_id=' . $person2Id;

        // TODO: obalit try-catch blokem
        $gb = file_get_contents($url);

        return(json_decode($gb));
    }

}