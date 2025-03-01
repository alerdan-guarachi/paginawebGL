<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class InformeFinalProveedorNotification extends Notification
{
    use Queueable;

    protected $informefinalcliente;

    public function __construct($informefinalcliente)
    {
        $this->informefinalcliente = $informefinalcliente;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => "{$this->informefinalcliente->usuarioregistro} ha subido su INFORME FINAL del cliente: {$this->informefinalcliente->clienteitanombre}{$this->informefinalcliente->clientecomunnombre}{$this->informefinalcliente->clienteauditorianombre}",
            'registro_id' => $this->informefinalcliente->id,
        ];
    }
}
