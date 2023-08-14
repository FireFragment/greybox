<?php

namespace App\Mail;

use App\Event;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

class RegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $locale;
    public $event;
    public $people;
    public $invoice;
    /**
     * @var string
     */
    private $mailFromAddress;
    /**
     * @var string
     */
    private $mailFromName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($locale, Event $event, $people, $invoice = null)
    {
        $this->locale = $locale;
        $this->event = $event;
        $this->people = $people;
        $this->invoice = $invoice;

        $this->mailFromAddress = env('MAIL_FROM_ADDRESS');
        $this->mailFromName = env('MAIL_FROM_NAME');
        if ($event->isPds()) {
            $this->mailFromAddress = env('PDS_MAIL_FROM_ADDRESS');
            $this->mailFromName = env('PDS_MAIL_FROM_NAME');
        }
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

        if (null !== $this->invoice) {
            $this->attach(getcwd().'/invoices/'.$this->invoice->pdf_url.'.pdf', [
                'as' => 'adk-' . substr($this->invoice->pdf_url, 0, 8) . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        // TODO: solve invoiceLines, late registrations

        return $this
            ->from($this->mailFromAddress, $this->mailFromName)
            ->subject($subject)
            ->view('email.registrationconfirmation')
            ->with([
                'eventName' => $eventName,
                'people' => $this->people,
                'invoice' => $this->invoice
            ]);
    }
}