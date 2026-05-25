<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $alerts;

    /**
     * Create a new message instance.
     */
    public function __construct(array $alerts)
    {
        $this->alerts = $alerts;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('🔔 Alerta de Stock - UniStock')
                    ->view('emails.stock_alert');
    }
}
