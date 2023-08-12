<?php

namespace App\Policies;

use App\User;
use App\Event;

const ORGANIZER_ROLE_ID = 4;

class EventPolicy
{
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

    private function isSuperAdmin(User $user)
    {
        if ($user->isAdmin()) return true;
        return false;
    }

    private function isEventOrganizer(User $user, Event $event): bool
    {
        $organizers = $event->registrations()->where('role', ORGANIZER_ROLE_ID)->get();
        $userPerson = $user->person()->first();
        foreach ($organizers as $organizer) {
            $organizerPerson = $organizer->person()->first();
            if ($userPerson->id === $organizerPerson->id) {
                return true;
            }
        }

        return false;
    }
}