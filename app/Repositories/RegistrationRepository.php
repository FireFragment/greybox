<?php

namespace App\Repositories;

use App\Event;

class RegistrationRepository
{
    /**
     * Return array of email addresses to which the confirmation email should be sent.
     * @param Event $event
     * @return string[]
     */
    public function getConfirmationEmailBccRecipients(Event $event): array
    {
        $bccRecipients = array(getenv('MAIL_FROM_ADDRESS'));
        $organizers = $event->organizers();
        foreach ($organizers as $organizer) {
            $users = $organizer->user()->get();
            foreach ($users as $user) {
                $bccRecipients[] = $user->username;
            }
        }
        return $bccRecipients;
    }
}