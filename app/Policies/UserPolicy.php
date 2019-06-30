<?php

namespace App\Policies;

use App\User;

class UserPolicy
{
    public function showOne(User $logged_in, User $user)
    {
        if ($logged_in->isAdmin()) return $logged_in->isAdmin();
        return $user->id === $logged_in->id;
    }


    public function update(User $logged_in, User $user)
    {
        if ($logged_in->isAdmin()) return $logged_in->isAdmin();
        return $user->id === $logged_in->id;
    }
}