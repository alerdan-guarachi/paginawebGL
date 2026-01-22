<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClienteReferenciadoNotification extends Notification
{
    use Queueable;

    public $cliente;
    public $clienteReferenciador;

    public function __construct($cliente, $clienteReferenciador)
    {
        $this->cliente = $cliente;
        $this->clienteReferenciador = $clienteReferenciador;
    }

    // SOLO database
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'titulo' => 'Nuevo Cliente Referenciado',
            'mensaje' => 'Has ganado 20 GoodBits por referenciar a ' . $this->cliente->nombrecompleto,
        ];
    }
}
