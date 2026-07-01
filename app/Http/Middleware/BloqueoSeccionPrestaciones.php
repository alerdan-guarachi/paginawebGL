<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BloqueoSeccionPrestaciones
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // ✅ Solo aplicar a EJECUTIVO PRESTACIONES
        $tieneRol = $user->getRoleNames()
            ->map(fn($r) => strtoupper($r))
            ->contains('EJECUTIVO PRESTACIONES');

        if ($tieneRol) {

            // 🔥 SUBQUERY IGUAL A LA VISTA
            $sub = DB::table('procedimientotramites as pt1')
                ->select(
                    'pt1.clienteid',
                    'pt1.idtramite',
                    DB::raw('MAX(pt1.fecharetorno) as max_fecha')
                )
                ->whereNotNull('pt1.fecharetorno')
                ->groupBy('pt1.clienteid', 'pt1.idtramite');

            // 🔥 QUERY IGUAL A LA VISTA (PERO SOLO EXISTS)
            $hayVencidos = DB::table('procedimientotramites as pt')
                ->joinSub($sub, 't', function ($join) {
                    $join->on('pt.clienteid', '=', 't.clienteid')
                        ->on('pt.idtramite', '=', 't.idtramite')
                        ->on('pt.fecharetorno', '=', 't.max_fecha');
                })
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('procedimientotramites as pt2')
                        ->whereColumn('pt2.clienteid', 'pt.clienteid')
                        ->whereColumn('pt2.idtramite', 'pt.idtramite')
                        ->whereColumn('pt2.id', '>', 'pt.id');
                })
                ->where('pt.apoderado', $user->name)
                ->whereRaw('pt.fecharetorno < NOW()') // SOLO vencidos reales
                ->exists();

            // 🔓 Código ACTIVO o EXPIRADO (tu lógica)
            $tieneCodigoActivo = DB::table('permisos_codigo')
                ->where('usuarioSolicitante', $user->name)
                ->whereIn('estado', ['ACTIVO', 'EXPIRADO'])
                ->whereDate('fechaSolicitada', now()->toDateString())
                ->exists();

            // 🚫 BLOQUEO
            if ($hayVencidos && !$tieneCodigoActivo) {

                if (!$request->routeIs('bloqueado')) {
                    return redirect()->route('bloqueado');
                }
            }
        }

        return $next($request);
    }
}