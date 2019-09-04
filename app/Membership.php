<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Membership extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'person', 'beginning', 'end', 'application', 'legal_guardian'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person', 'id');
    }

    public function isExpired()
    {
        return strtotime($this->end) < time();
    }

    public static function setForSeason()
    {
        $year = date('Y') + 1;
        // TODO: solve as option
        return "$year-08-31";
    }
}
