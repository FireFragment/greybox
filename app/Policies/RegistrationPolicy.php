<?php

namespace App\Policies;

use App\User;
use App\Registration;

class RegistrationPolicy extends Policy
{
    public function showOne(User $user, Registration $registration)
    {
        if ($user->isAdmin()) return true;
        return $registration->registered_by === $user->id;
    }

    public function update(User $user)
    {
        return parent::update($user);
    }
}