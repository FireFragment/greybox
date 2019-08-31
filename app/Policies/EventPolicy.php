<?php

namespace App\Policies;

use App\User;
use App\Event;

const ORGANIZER_ROLE_ID = 4;

class EventPolicy extends Policy
{
    public function create(User $user)
    {
        if (parent::create($user)) {
            return true;
        }

        return false;
    }

    public function update(User $user)
    {
        if (parent::update($user)) {
            return true;
        }

        return false;
    }

    public function delete(User $user)
    {
        if (parent::delete($user)) {
            return true;
        }

        return false;
    }

    public function showRegistrations(User $user, Event $event)
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

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