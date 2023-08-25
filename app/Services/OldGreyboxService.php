<?php


namespace App\Services;


use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Integer;
use Log;

class OldGreyboxService
{
    /**
     * @var string
     */
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://statistiky.debatovani.cz/static.php?token=' . env('OLD_GREYBOX_SAFETY_TOKEN');
    }

    /**
     * @param string $url
     * @return array|false
     */
    private function getDataFromOldGreybox(string $url)
    {
        try
        {
            $gb = file_get_contents($url);
            return json_decode($gb);
        }
        catch (\Exception $e)
        {
            Log::warning('Old greybox connection error: '.$e);
            return false;
        }
    }

    /**
     * @param int $competitionId
     * @param string $teamName
     * @return array|false
     */
    public function getPastTeamDebaters(int $competitionId, string $teamName)
    {
        $url = $this->baseUrl;
        $url .= '&past_team_debaters=1';
        $url .= '&competition_id=' . $competitionId;
        $url .= '&team_name=' . urlencode($teamName);

        return $this->getDataFromOldGreybox($url);
    }

    /**
     * @param int $competitionId
     * @param int $person1Id
     * @param int $person2Id
     * @return array|false
     */
    public function getPastSharedTeamsInTheSameTournament(int $competitionId, int $person1Id, int $person2Id)
    {
        $url = $this->baseUrl;
        $url .= '&past_shared_teams=1';
        $url .= '&competition_id=' . $competitionId;
        $url .= '&person1_id=' . $person1Id;
        $url .= '&person2_id=' . $person2Id;

        return $this->getDataFromOldGreybox($url);
    }

    /**
     * @param int $personId
     * @return array|false
     */
    public function getPastDebates(int $personId)
    {
        $url = $this->baseUrl;
        $url .= '&past_debates=1';
        $url .= '&person_id=' . $personId;

        return $this->getDataFromOldGreybox($url);
    }
}