<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class AceptaSolicitudNotification extends Notification
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
            'mensaje' => "{$this->solicitud->usuarioactualizacion} ha aceptado tu solicitud de petición de: Producto: {$this->solicitud->productosolicitado}. Dirígete hacia el área de Caja para recoger lo solicitado",
            'registro_id' => $this->solicitud->id,
        ];
    }
}
