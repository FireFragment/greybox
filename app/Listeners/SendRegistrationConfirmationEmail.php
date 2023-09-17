<?php

namespace App\Listeners;

use App\Events\RegistrationConfirmed,
    App\Mail\RegistrationConfirmation,
    App\Repositories\RegistrationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendRegistrationConfirmationEmail implements ShouldQueue
{
    /**
     * @var RegistrationRepository
     */
    private $registrationRepository;

    public function __construct()
    {
        $this->registrationRepository = new RegistrationRepository();
    }

    /**
     * Handle the event.
     *
     * @param  RegistrationConfirmed $event
     * @return void
     */
    public function handle(RegistrationConfirmed $event)
    {
        $registration = $event->registration;
        $invoice = $event->invoice;
        $user = $registration->registeredBy()->first();
        if (null !== $event->language) {
            $language = $event->language;
        } else {
            $language = $user->preferredLocale();
        }

        $event = $registration->event()->first();
        $people = $registration->getRegistrationGroup()->getPeopleForEmail($language);

        $mailer = Mail::mailer('default');
        if ($event->isPds()) {
            $mailer = Mail::mailer('pds');
        }
        $bccRecipients = $this->registrationRepository->getConfirmationEmailBccRecipients($event);

        $mailer->to($user->username)->bcc($bccRecipients)->send(new RegistrationConfirmation($language, $event, $people, $invoice));
    }
}