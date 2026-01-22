<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AvanceTramiteNotification extends Notification
{
    use Queueable;

    public $clienteId;
    public $nombreTramite;

    public function __construct($clienteId, $nombreTramite)
    {
        $this->clienteId = $clienteId;
        $this->nombreTramite = $nombreTramite;
    }

    // SOLO database
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'titulo'  => 'Nuevo Proceso',
            'mensaje' => 'Se ha subido un nuevo proceso de su trámite en curso ',
        ];
    }
}
