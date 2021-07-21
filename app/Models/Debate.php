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
}