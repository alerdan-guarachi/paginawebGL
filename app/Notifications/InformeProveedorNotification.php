<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class InformeProveedorNotification extends Notification
{
    use Queueable;

    protected $documentacioncliente;

    public function __construct($documentacioncliente)
    {
        $this->documentacioncliente = $documentacioncliente;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => "{$this->documentacioncliente->usuarioregistro} ha subido su informe de: {$this->documentacioncliente->accion} del cliente: {$this->documentacioncliente->clienteitanombre}{$this->documentacioncliente->clientecomunnombre}{$this->documentacioncliente->clienteauditorianombre}",
            'registro_id' => $this->documentacioncliente->id,
        ];
    }
}
