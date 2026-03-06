<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmacionAsesoriaMail extends Mailable
{
    use SerializesModels;

    public $programacion;

    public function __construct($programacion)
    {
        $this->programacion = $programacion;
    }

    public function build()
    {
        return $this
            ->from('accesos.app@goodlife.com.bo', 'Asesorias Good Life')
            ->subject('Confirmación de asesoría')
            ->view('emails.confirmacion-asesoria');
    }
}