<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ComprobanteNotification extends Notification
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
            'mensaje' => "Se ha subido el comprobante de cuenta por pagar de la Orden: {$this->solicitud->ordenid} de: {$this->solicitud->proveedornombre}{$this->solicitud->proveedorasignado}.",
            'registro_id' => $this->solicitud->id,
            'tipo' => class_basename($this->solicitud),
        ];
    }
}