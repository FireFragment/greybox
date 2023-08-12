<?php

namespace App\Policies;

use App\Event;
use App\User;

const ORGANIZER_ROLE_ID = 4;

trait EventOrganizer {

    public function isEventOrganizer(User $user, Event $event): bool
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