<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Mensaje;
use Carbon\Carbon;
use App\Models\Anuncio;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('welcome', function ($view) {

            $now = Carbon::now()->startOfDay();

            $anuncios = Anuncio::where(function ($q) use ($now) {

                $q->where(function ($q1) use ($now) {
                    // CASO 1: sin fechas → siempre visible
                    $q1->whereNull('fecha_inicio')
                    ->whereNull('fecha_fin');
                })

                ->orWhere(function ($q2) use ($now) {
                    // CASO 2: con lógica de rango

                    $q2->where(function ($x) use ($now) {
                            $x->whereNull('fecha_inicio')
                            ->orWhereDate('fecha_inicio', '<=', $now);
                        })
                    ->where(function ($x) use ($now) {
                            $x->whereNull('fecha_fin')
                            ->orWhereDate('fecha_fin', '>=', $now);
                        });
                });

            })
            ->orderBy('orden')
            ->get();

            $view->with('anuncios', $anuncios);
        });
    }
}
