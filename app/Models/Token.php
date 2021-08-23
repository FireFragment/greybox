<?php

namespace App\Models;

class Token extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'api_token', 'user', 'valid_until'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public $primaryKey = 'api_token';

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user', 'id');
    }
}