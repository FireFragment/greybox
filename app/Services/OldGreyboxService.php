<?php


namespace App\Services;


use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Integer;

class OldGreyboxService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://debatovani.cz/greybox/static.php?token=' . env('OLD_GREYBOX_SAFETY_TOKEN');
    }

    public function getPastTeamDebaters(int $competitionId, string $teamName): array
    {
        $url = $this->baseUrl;
        $url .= '&past_team_debaters=1';
        $url .= '&competition_id=' . $competitionId;
        $url .= '&team_name=' . $teamName;

        $gb = file_get_contents($url);

        return(json_decode($gb));
    }

}