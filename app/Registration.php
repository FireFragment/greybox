<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Registration extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'person', 'name', 'surname', 'birthdate', 'id_number', 'street', 'city', 'zip', 'note', 'event', 'event_id', 'role', 'accommodation', 'confirmed', 'team', 'registered_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /*
     * @return App\User
     */
    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by', 'id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'person', 'id');
    }
}
