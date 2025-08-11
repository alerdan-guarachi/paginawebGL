<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class CodigoPermisoContinuidadPres extends Notification
{
    use Queueable;

    protected $permisoCodigo;

    public function __construct($permisoCodigo)
    {
        $this->permisoCodigo = $permisoCodigo;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => "{$this->permisoCodigo->usuarioAutorizador} te ha asignado el código: {$this->permisoCodigo->codigo} para dar continuidad al trámite del cliente: {$this->permisoCodigo->clienteid}.",
            'registro_id' => $this->permisoCodigo->id,
        ];
    }
}
