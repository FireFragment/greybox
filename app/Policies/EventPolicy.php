<?php

namespace App\Policies;

use App\User;
use App\Event;

class EventPolicy
{
    use SuperAdmin, EventOrganizer;

    public function showAll(User $user): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return false;
    }

    public function update(User $user, Event $event): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }
        if ($this->isEventOrganizer($user, $event)) {
            return true;
        }

        return false;
    }

    public function delete(User $user): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return false;
    }

    public function showRegistrations(User $user, Event $event): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }
        if ($this->isEventOrganizer($user, $event)) {
            return true;
        }
        return false;
    }

    public function showUserRegistrations(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }
}