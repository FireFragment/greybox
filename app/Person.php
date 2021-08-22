<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Person extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname', 'email', 'institution', 'school_year', 'birthdate', 'id_number', 'street', 'city', 'zip', 'vegetarian', 'dietary_requirement', 'speaker_status', 'school', 'note'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function membership()
    {
        // TODO: to be changed to hasMany maybe - in case of repeated membership
        return $this->hasOne('App\Membership', 'person', 'id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution', 'id');
    }

    /**
     * @return string
     */
    public function getOldGreyboxId()
    {
        return $this->old_greybox_id;
    }
}
