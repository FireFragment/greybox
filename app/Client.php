<?php

namespace App;

use App\Services\FakturoidClientService;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Client extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fakturoid_id', 'name', 'street', 'street2', 'city', 'zip', 'country', 'registration_no', 'full_name', 'email', 'user'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'fakturoid_id'
    ];

    private $name;
    private $fcs;
    private $fakturoid_id;
    private $country;
    private $user;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fcs = new FakturoidClientService();
    }

    public function createFakturoidSubject(String $name, int $userId)
    {
        // TODO: přidat data z Person
        $this->name = $name;
        $subject = $this->fcs->createSubject(['name' => $this->name])->getBody();
        $this->fakturoid_id = $subject->id;
        $this->country = $subject->country;
        $this->user = $userId;
        $this->save();
    }
}
