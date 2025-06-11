<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class RespuestaVacacionNotificacion extends Notification
{
    use Queueable;

    protected $vacacion;

    public function __construct($vacacion)
    {
        $this->vacacion = $vacacion;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $mensaje = "{$this->vacacion->usuarioautorizacion} ha {$this->vacacion->estado} tu solicitud de vacaciones.";

        if ($this->vacacion->estado === 'RECHAZADO' && $this->vacacion->motivorechazo) {
            $mensaje .= " Por el motivo: {$this->vacacion->motivorechazo}.";
        }

        return [
            'mensaje' => $mensaje,
            'registro_id' => $this->vacacion->id,
        ];
    }
}
