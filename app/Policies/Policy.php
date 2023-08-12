<?php


namespace App\Policies;

use App\User;

abstract class Policy
{
    use SuperAdmin;

    public function create(User $user)
    {
        if ($this->isSuperAdmin($user)) return true;
        return false;
    }

    public function update(User $user)
    {
        if ($this->isSuperAdmin($user)) return true;
        return false;
    }

    public function delete(User $user)
    {
        if ($this->isSuperAdmin($user)) return true;
        return false;
    }
}