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
        $user = $registration->registeredBy()->first();

        $language = $user->preferredLocale();
        $event = $registration->event()->first();
        // TODO: Vytvořit registration group a přidat do něj všechny registrace, které mají stejný event a jsou vytvořeny v rámci jednoho requestu
        $person = $registration->person()->first();
        $people['Organizátor']['emptyTeamName'][] = $person->name . ' ' . $person->surname;
        // TODO: Převzít invoice z RegistrationConfirmed eventu
        $invoice = null;

        $mailer = Mail::mailer('default');
        if ($event->isPds()) {
            $mailer = Mail::mailer('pds');
        }
        $bccRecipients = $this->registrationRepository->getConfirmationEmailBccRecipients($event);

        // TODO: vyřešit jak nastavit locale pouze pro email / případně jak používat locale vůbec
        // app('translator')->setLocale($language); // Uvidíme, jestli bude potřeba. Viz. https://laravel.com/docs/5.4/localization#introduction
        $mailer->bcc($bccRecipients)->send(new RegistrationConfirmation($language, $event, $people, $invoice));
    }
}