<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class StockBajoNotification extends Notification
{
    use Queueable;

    protected $bien;

    public function __construct($bien)
    {
        $this->bien = $bien;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => "El producto {$this->bien->nombreproducto} - {$this->bien->marca} tiene un stock bajo: {$this->bien->stockactual}",
            'producto_id' => $this->bien->id,
        ];
    }
}
