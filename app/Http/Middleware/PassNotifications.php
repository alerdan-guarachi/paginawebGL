<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;

class PassNotifications
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, \Closure $next)
    {
        // Pasa las notificaciones no leídas a todas las vistas
        view()->share('unreadNotifications', Auth::check() ? Auth::user()->unreadNotifications : []);

        return $next($request);
    }
}
