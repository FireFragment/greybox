<?php

namespace App\Policies;

use App\User;

class UserPolicy
{
    public function showOne(User $user, User $logged_in)
    {
        return $user->id === $logged_in->id;
    }
}