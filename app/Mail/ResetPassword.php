<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    private $token;
    private $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $token, bool $pds)
    {
        $this->token = $token;
        $this->url = ($pds) ? 'https://debatovani.cz/pds/registration/' : 'https://debatovani.cz/greybox/registrace/';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(Lang::get('messages.password.reset.subject'))
            ->view('email.resetpassword')
            ->with([
                'token' => $this->token,
                'url' => $this->url
            ]);
    }
}