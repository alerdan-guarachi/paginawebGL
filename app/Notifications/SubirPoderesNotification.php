<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class SubirPoderesNotification extends Notification
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
            'mensaje' => "{$this->registro->usuarioregistronombre} ha generado la Instructiva de Poder del cliente: {$this->registro->clientenombre}. Debes subir su PODER.",
            'registro_id' => $this->registro->id,
        ];
    }
}
