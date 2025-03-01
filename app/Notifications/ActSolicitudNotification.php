<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ActSolicitudNotification extends Notification
{
    use Queueable;

    protected $solicitud;

    public function __construct($solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => "{$this->solicitud->usuarioactualizacion} te ha ofertado: {$this->solicitud->productoofertado} para tu solicitud de inventario.",
            'registro_id' => $this->solicitud->id,
        ];
    }
}
