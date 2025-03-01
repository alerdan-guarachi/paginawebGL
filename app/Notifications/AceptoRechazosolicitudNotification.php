<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class AceptoRechazosolicitudNotification extends Notification
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
            'mensaje' => "{$this->solicitud->usuariosolicitante} ha {$this->solicitud->estado} la oferta de solicitud de: Producto: {$this->solicitud->productoofertado} - Cantidad: {$this->solicitud->cantidadofertado}.",
            'registro_id' => $this->solicitud->id,
        ];
    }
}
