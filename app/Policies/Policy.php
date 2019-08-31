<?php


namespace App\Policies;

use App\User;

abstract class Policy
{
    public function isSuperAdmin(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
        return false;
    }

    public function create(User $user)
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }
    }

    public function update(User $user)
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }
    }

    public function delete(User $user)
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }
    }
}