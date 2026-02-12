<?php

namespace App\Mail;

use App\Models\Cliente;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CredencialesClienteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cliente;
    public $password;

    public function __construct(Cliente $cliente, $password)
    {
        $this->cliente = $cliente;
        $this->password = $password;
    }

    public function build()
    {
        return $this
            ->from('accesos.app@goodlife.com.bo', 'App Good Life')
            ->subject('Credenciales de acceso a la aplicación móvil')
            ->view('emails.credenciales-cliente')
            ->with([
                'cliente'  => $this->cliente,
                'password' => $this->password,
            ]);
    }
}
