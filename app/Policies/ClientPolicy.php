<?php

namespace App\Policies;

use App\User;
use App\Client;

class ClientPolicy extends Policy
{
    public function showAllFromFakturoid(User $user)
    {
        return $this->isSuperAdmin($user);
    }
}