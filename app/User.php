<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
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
        'password'
    ];

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
        return $this->role;
    }

    /**
     * Sets Api Token parameter and saves to the DB
     */
    public function setApiToken()
    {
        $this->api_token = sha1($this->id.time());
        $this->save();
    }

    /**
     * Converts username to lowercase
     * @param string $username
     * @return string
     */
    static public function normalizeUserName(string $username): string
    {
        return strtolower($username);
    }

    /**
     * Check password
     * @param $password
     * @return bool
     */
    public function isPasswordCorrect($password): bool
    {
        return Hash::check($password, $this->password);
    }
}
