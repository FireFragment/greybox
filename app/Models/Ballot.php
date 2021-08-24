<?php

namespace App\Models;

class Ballot extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     * TODO: Delete filename field from the DB
     *
     * @var array
     */
    protected $fillable = [
        'debate', 'adjudicator', 'filename', 'url', 'old_greybox_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function debate()
    {
        return $this->belongsTo(Debate::class, 'debate', 'id');
    }

    public function adjudicator()
    {
        return $this->belongsTo(\App\Person::class, 'adjudicator', 'id');
    }
}