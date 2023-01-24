<?php

namespace App\Policies;

use App\User;
use App\Registration;

class RegistrationPolicy extends Policy
{
    public function showOne(User $user, Registration $registration)
    {
        if ($user->isAdmin()) return true;
        return $registration->registered_by === $user->id;
    }

    public function update(User $user, Registration $registration)
    {
        if (parent::update($user)) return true;
        
        $event = $registration->event()->first();
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