<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class SolicitarCreacionBateria extends Notification
{
    use Queueable;

    protected $registro;

    public function __construct($registro)
    {
        $this->registro = $registro;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => "Tienes bateria pendientes por crear del cliente: {$this->registro->clientenombre}",
            'registro_id' => $this->registro->id,
        ];
    }
}
