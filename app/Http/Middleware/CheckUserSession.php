<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserSession
{
    public function handle($request, Closure $next)
    {
        // Verifica si el usuario está autenticado
        if (!Auth::check()) {
            // Redirige al inicio de sesión con un mensaje si la sesión ha expirado
            return redirect()->route('login')->with('error', 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.');
        }

        return $next($request);
    }
}