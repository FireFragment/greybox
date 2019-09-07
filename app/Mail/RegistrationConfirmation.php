<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

class RegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $people;
    public $invoice;
    private $locale;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\Event $event, $people, $invoice, $locale)
    {
        $this->event = $event;
        $this->people = $people;
        $this->invoice = $invoice;
        $this->locale = $locale;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $eventNameTranslations = $this->event->nameTranslation()->first();
        if ('en' === $this->locale) {
            $eventName = $eventNameTranslations->en;
        } else {
            $eventName = $eventNameTranslations->cs;
        }
        $subject = Lang::get('messages.registration.confirmation') . ' - ' . $eventName;

        // TODO: solve attachment, invoiceLines

        return $this
            ->subject($subject)
            ->view('email.registrationconfirmation')
            ->with([
                'eventName' => $eventName,
                'people' => $this->people,
                'invoice' => $this->invoice
            ]);
    }
}