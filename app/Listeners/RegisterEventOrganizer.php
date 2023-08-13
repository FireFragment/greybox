<?php

namespace App\Listeners;

use App\Registration,
    App\Events\EventCreated,
    App\Events\RegistrationConfirmed;
use Illuminate\Support\Facades\Event as EventFacade;

class RegisterEventOrganizer
{
    /**
     * Handle the event.
     *
     * @param  EventCreated  $event
     * @return void
     */
    public function handle(EventCreated $event)
    {
        $user = \Auth::user();
        $organizedEvent = $event->event;

        $registration = Registration::create([
            'person' => $user->person()->first()->id,
            'event' => $organizedEvent->id,
            'role' => env('ORGANIZER_ROLE_ID'),
            'confirmed' => true,
            'registered_by' => $user->id
        ]);

        EventFacade::dispatch(new RegistrationConfirmed($registration));
    }
}