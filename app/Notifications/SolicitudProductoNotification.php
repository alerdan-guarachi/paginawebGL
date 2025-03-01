<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class SolicitudProductoNotification extends Notification
{
    use Queueable;

    protected $producto;

    public function __construct($producto)
    {
        $this->producto = $producto;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => "{$this->producto->usuariosolicitante} ha solicitado: {$this->producto->productosolicitado} - Cantidad: {$this->producto->cantidad}",
            'registro_id' => $this->producto->id,
        ];
    }
}
