<?php

namespace App\Policies;

class PricePolicy
{
    use SuperAdmin, EventOrganizer;

    public function create(User $user, Event $event): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }
        if ($this->isEventOrganizer($user, $event)) {
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

    public function delete(User $user, Event $event): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }
        if ($this->isEventOrganizer($user, $event)) {
            return true;
        }

        return false;
    }
}