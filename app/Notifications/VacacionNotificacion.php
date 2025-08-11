<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class VacacionNotificacion extends Notification
{
    use Queueable;

    protected $solicitudvacacion;

    public function __construct($solicitudvacacion)
    {
        $this->solicitudvacacion = $solicitudvacacion;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => "{$this->solicitudvacacion->proveedornombre} ha enviado una solicitud de vacaciones.",
            'registro_id' => $this->solicitudvacacion->id,
        ];
    }
}
