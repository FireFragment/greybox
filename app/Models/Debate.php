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

    public static function parseOldGreybox(string $text): array
    {

        $text = preg_split('/<h2 id="debaty">debaty<\/h2>/m', $text);
        $text = preg_split('/<h2 id="ibody">/m', $text[1]);
        $lines = preg_split('/\<tr\>/', $text[0]);
        $numberOfDebates = count($lines);

        $debates = array();
        for ($i = 2; $i < $numberOfDebates; $i++)
        {
            $fields = preg_split('/<\/td>/m', $lines[$i]);
            $debates[$i-2]['date'] = substr($fields[0], 4);
            $debates[$i-2]['affirmativeTeam'] = self::removeLink($fields[1]);
            $debates[$i-2]['negativeTeam'] = self::removeLink($fields[2]);
            $debates[$i-2]['motion'] = self::removeLink($fields[3]);
            $debates[$i-2]['result'] = substr($fields[4], 4);
            $debates[$i-2]['role'] = substr($fields[6], 4);
            $debates[$i-2]['score'] = substr($fields[7], 4);
        }

        return $debates;
    }

    private static function removeLink(string $text): string
    {
        $text = preg_split('/>/m', $text);
        return substr($text[2], 0, -3);
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
            case 12:
                return ['cs' => 'prosinec', 'en' => 'December'];
        }
    }
}