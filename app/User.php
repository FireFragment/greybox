<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

const ORGANIZER_ROLE_ID = 4; // TODO: vyřešit lépe

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

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

    public function deletedAutofills()
    {
        return $this->hasMany(DeletedAutofill::class, 'user', 'id');
    }

    public function tokens()
    {
        return $this->hasMany(\App\Models\Token::class, 'user', 'id');
    }

    public function setRole()
    {
        $this->role = 'none';
        if ($this->isAdmin()) $this->role = 'admin';
        return $this->role;
    }

    /**
     * Creates new Token in the tokens table
     */
    public function setApiToken(): string
    {
        $apiToken = sha1($this->id.time());
        $this->tokens()->create([
            'api_token' => $apiToken,
            'user' => $this->id,
            'valid_until' => new \DateTime('+ 31 days')
        ]);
        return $apiToken;
    }

    public function getOrganizedEventsIds(): array
    {
        $person = $this->person()->first();
        if (empty($person))
        {
            return [];
        }
        $registrations = $person->registrations()->where('role', ORGANIZER_ROLE_ID)->get();

        $organizedEventsIds = array();
        foreach ($registrations as $registration)
        {
            $organizedEventsIds[] = $registration->event()->first()->id;
        }
        return $organizedEventsIds;
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

    /**
     * Returns User with Person details
     * @return User
     */
    public function withPerson(): User
    {
        $this->person = $this->person()->first();
        return $this;
    }
}
