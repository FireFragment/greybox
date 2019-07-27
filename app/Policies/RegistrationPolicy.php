<?php

namespace App\Policies;

use App\User;
use App\Registration;

class RegistrationPolicy
{
    public function showOne(User $user, Registration $registration)
    {
        if ($user->isAdmin()) return true;
        return $registration->registered_by === $user->id;
    }
}