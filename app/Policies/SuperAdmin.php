<?php

namespace App\Policies;

use App\User;

trait SuperAdmin
{
    public function isSuperAdmin(User $user): bool
    {
        if ($user->isAdmin()) return true;
        return false;
    }
}