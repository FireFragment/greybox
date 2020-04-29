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
    // TODO: remove street2 and full_name
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
    public $user;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fcs = new FakturoidClientService();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }

    public function createFakturoidSubject(User $user)
    {
        // TODO: přidat data z Person
        $this->user = $user;
        $this->user()->associate($user);
        $this->name = $this->user->username;
        $subject = $this->fcs->createSubject(['name' => $this->name])->getBody();
        $this->fakturoid_id = $subject->id;
        $this->country = $subject->country;
        $this->fill(['name' => $this->name, 'fakturoid_id' => $this->fakturoid_id, 'country' => $this->country]);
        $this->save();
    }

    /**
     * Confirms whether the (greybox) client is different from Fakturoid subject
     *
     * @param $subject
     * @return boolean
     */
    public function isDifferentFromSubject($subject): bool
    {
        if ($this->name !== $subject->name) return true;
        if ($this->street !== $subject->street) return true;
        if ($this->email !== $subject->email) return true;
        return false;
    }

    /**
     * Updates client data fields from Fakturoid subject
     *
     * @param $subject
     * @return void
     */
    public function updateFromFakturoid($subject): void
    {
        $this->update([
            'name' => $subject->name,
            'street' => $subject->street,
            'city' => $subject->city,
            'zip' => $subject->zip,
            'country' => $subject->country,
            'registration_no' => $subject->registration_no,
            'email' => $subject->email
        ]);
    }
}
