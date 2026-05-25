<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Alerta de Acceso a UniStock')
                    ->html('<h3>Hola ' . $this->user->name . ',</h3><p>Has iniciado sesión en UniStock correctamente mediante Google.</p><p>Si no fuiste tú, por favor contacta al administrador.</p>');
    }
}
