<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class SoportetecnicorespuestaNotification extends Notification
{
    use Queueable;

    protected $soporte;

    public function __construct($soporte)
    {
        $this->soporte = $soporte;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => "{$this->soporte->usuariosoporte} ha atentido tu solicitud de Soporte.",
            'producto_id' => $this->soporte->id,
        ];
    }
}
