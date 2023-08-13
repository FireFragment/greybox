<?php

namespace App\Listeners;

use App\Registration;

class RegisterEventOrganizer
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\EventCreated  $event
     * @return void
     */
    public function handle(\App\Events\EventCreated $event)
    {
        $user = \Auth::user();
        $organizedEvent = $event->event;

        $registration = Registration::create([
            'person' => $user->person()->first()->id,
            'event' => $organizedEvent->id,
            'role' => getenv('ORGANIZER_ROLE_ID'),
            'confirmed' => true,
            'registered_by' => $user->id
        ]);
    }
}