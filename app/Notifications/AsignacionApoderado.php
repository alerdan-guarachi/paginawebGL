<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class AsignacionApoderado extends Notification
{
    use Queueable;

    protected $tramitesubcliente;

    public function __construct($tramitesubcliente)
    {
        $this->tramitesubcliente = $tramitesubcliente;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => "Has sido asignado como Apoderado del cliente {$this->tramitesubcliente->clienteitanombre} para el trámite de {$this->tramitesubcliente->tramite}.",
            'registro_id' => $this->tramitesubcliente->id,
        ];
    }
}
