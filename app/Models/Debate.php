<?php

namespace App\Models;

class Debate extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event', 'motion', 'date', 'place', 'affirmativeWinner', 'unanimousDecision'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function event()
    {
        return $this->belongsTo(\App\Event::class, 'event', 'id');
    }

    public function motion()
    {
        return $this->belongsTo(Motion::class, 'motion', 'id');
    }

    public function teams()
    {
        return $this->belongsToMany(\App\Team::class, 'debates_teams', 'debate', 'team');
    }

    public function getAffirmativeTeam()
    {
        return $this->teams()->where(['side' => 'a'])->first();
    }

    public function getNegativeTeam()
    {
        return $this->teams()->where(['side' => 'n'])->first();
    }

    public static function prepareOldGreyboxData(array $lines, bool $adjudicator): array
    {
        $debates = array();
        foreach ($lines as $line)
        {
            $canUploadBallot = ('r' === $line->role && $adjudicator); // if adjudicator in debate and logged in
            if ('r' === $line->role)
            {
                $role = 'rozhodčí';
                $win = null;
            }
            else
            {
                $role = strtoupper($line->role);
                $win = ('A' === substr($role,0,1)) ? (bool) $line->vitez : !$line->vitez;
            }
            $result = ($line->vitez ? 'AFF ' : 'NEG ') . ($line->debata_presvedcive ? '3:0' : '2:1');

            $debates[] = array(
                'oldGreyboxId' => $line->debata_ID,
                'date' => $line->datum,
                'affirmativeTeam' => $line->afirmace,
                'negativeTeam' => $line->negace,
                'motion' => $line->tx_short,
                'result' => $result,
                'link' => 'https://debatovani.cz/greybox/?page=debata&debata_id=' . $line->debata_ID,
                'role' => $role,
                'win' => $win,
                'ballots' => self::addBallots($line->debata_ID),
                'canUploadBallot' => $canUploadBallot
            );
        }

        return $debates;
    }

    public static function groupByMonth(array $debates): array
    {
        $months = array();
        foreach ($debates as $debate)
        {
            $year = (int) substr($debate['date'], 0, 4);
            $month = (int) substr($debate['date'], 5, 2);

            $months[$year.'-'.$month]['year'] = $year;
            $months[$year.'-'.$month]['month'] = self::translateMonths($month);
            $months[$year.'-'.$month]['debates'][] = $debate;
        }
        return $months;
    }

    private static function translateMonths(int $number): array
    {
        switch ($number)
        {
            case 1:
                return ['cs' => 'leden', 'en' => 'January'];
            case 2:
                return ['cs' => 'únor', 'en' => 'February'];
            case 3:
                return ['cs' => 'březen', 'en' => 'March'];
            case 4:
                return ['cs' => 'duben', 'en' => 'April'];
            case 5:
                return ['cs' => 'květen', 'en' => 'May'];
            case 6:
                return ['cs' => 'červen', 'en' => 'June'];
            case 7:
                return ['cs' => 'červenec', 'en' => 'July'];
            case 8:
                return ['cs' => 'srpen', 'en' => 'August'];
            case 9:
                return ['cs' => 'září', 'en' => 'September'];
            case 10:
                return ['cs' => 'říjen', 'en' => 'October'];
            case 11:
                return ['cs' => 'listopad', 'en' => 'November'];
            default:
                return ['cs' => 'prosinec', 'en' => 'December'];
        }
    }

    private static function addBallots(int $debateId): array
    {
        $ballots = Ballot::where('old_greybox_id', $debateId)->get();
        $list = array();
        foreach ($ballots as $ballot) {
            $adjudicator = $ballot->adjudicator()->first();
            $adjudicatorName = 'TBD';
            if (null !== $adjudicator)
            {
                $adjudicatorName = $adjudicator->name . ' ' . $adjudicator->surname;
            }

            $list[] = array(
                'url' => $ballot->url,
                'adjudicator' => $adjudicatorName
            );
        }
        return $list;
    }
}