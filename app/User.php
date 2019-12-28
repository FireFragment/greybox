<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'person', 'preferred_locale', 'admin', 'api_token'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password' // , 'api_token'  temporary workaround
    ];

    public $role;

    /**
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->admin;
    }

    /**
     * @return string
     */
    public function preferredLocale()
    {
        return $this->preferred_locale;
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'person', 'id');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'registered_by', 'id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'user', 'id');
    }

    public function setRole() {
        $this->role = 'none';
        if ($this->isAdmin()) $this->role = 'admin';
    }

    static public function normalizeUserName($username) {
        return strtolower($username);
    }
}
