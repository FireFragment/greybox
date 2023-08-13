<?php

namespace App\Policies;

use App\Event;
use App\User;

trait EventOrganizer {

    /**
     * Check if the user is an organizer of the event.
     * @param User $user
     * @param Event $event
     * @return bool
     */
    public function isEventOrganizer(User $user, Event $event): bool
    {
        $organizers = $event->registrations()->where('role', getenv('ORGANIZER_ROLE_ID'))->get();
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