<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Price extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event', 'role', 'description', 'amount', 'currency', 'note'
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
        return $this->belongsTo(Event::class, 'event', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role', 'id');
    }

    public function translation()
    {
        return $this->belongsTo(Translation::class, 'description', 'id');
    }

    public function noteTranslation()
    {
        return $this->belongsTo(Translation::class, 'note', 'id');
    }

    public function getAmount()
    {
        if (isset($this->amount)) {
            return $this->amount;
        }
        return 0;
    }

    public function getCurrency()
    {
        return $this->currency;
    }
}