<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ControlProgController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:admin.users.index')->only('index');
    }

    public function index()
    {
        // Obtener todos los usuarios
        $usuarios = DB::table('users')
        ->select('id', 'name')
        ->where('estado', 'ACTIVO')
        ->orderBy('name')
        ->get();



        // Listado de tablas a combinar (esto es para el gráfico unificado)
        $tablas = [
            'aprobacioninformesfinales',
            'bateriaproveedores',
            'bateriasubclientes',
            'clientes',
            'documentacionsubclientes',
            'estadoprogramacionsubclientes',
            'estadocotizacionsubclientes',
            'programacionsubclientes',
            'requisitosubclientes'
        ];

        // Almacenamiento temporal de los resultados unificados
        $data = collect();

        // Iterar sobre cada tabla y obtener los datos
        foreach ($tablas as $tabla) {
            $registros = DB::table($tabla)
                ->select(DB::raw('DAYNAME(created_at) as dia'), DB::raw('count(*) as total'))
                ->whereNotNull('created_at') // Asegurarse de que 'created_at' no sea nulo
                ->where('created_at', '>=', now()->subDays(7)) // Filtrar los registros de la última semana
                ->groupBy('dia')
                ->get();

            // Unir los resultados en la colección $data
            $data = $data->merge($registros);
        }

        // Traducción de los días de la semana
        $diasTraduccion = [
            'Sunday' => 'Domingo',
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
        ];

        // Obtener el día de hoy en español
        $hoy = $diasTraduccion[date('l')];

        // Días de la semana en orden
        $diasEnOrden = ['Sábado', 'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

        // Encontrar la posición del día de hoy
        $indiceHoy = array_search($hoy, $diasEnOrden);

        // Reorganizar los días para que el día de hoy sea el último, y los anteriores aparezcan antes
        $diasEnOrden = array_merge(array_slice($diasEnOrden, $indiceHoy + 1), array_slice($diasEnOrden, 0, $indiceHoy), [$hoy]);

        // Agrupar los datos por día
        $unifiedData = $data->groupBy('dia')->map(function ($dayGroup) use ($diasTraduccion) {
            $day = $dayGroup->first()->dia;
            return [
                'dia' => $diasTraduccion[$day] ?? 'Día desconocido',
                'total' => $dayGroup->sum('total')
            ];
        });

        // Mapear los días y asignar valores de total, si no existe, lo asignamos a 0
        $finalDataGeneral = collect($diasEnOrden)->mapWithKeys(function ($dia) use ($unifiedData) {
            return [$dia => $unifiedData->firstWhere('dia', $dia)['total'] ?? 0];
        });

        // Asegurarse de que el datasetUsuario esté vacío para gráficos específicos de usuario
        $datasetUsuario = collect();

        // Pasar tanto los días en orden como los datos finales a la vista junto con los usuarios
        return view('admin.controlprogramacion.index', compact('finalDataGeneral', 'diasEnOrden', 'usuarios', 'datasetUsuario'));
    }

    public function buscarPorUsuario(Request $request)
    {
        // Obtener el usuario seleccionado
        $usuarioId = $request->input('usuario');

        // Obtener todos los usuarios para el dropdown
        $usuarios = DB::table('users')->select('id', 'name')->get();

        // Verificar si el usuario fue seleccionado
        if (is_null($usuarioId)) {
            // Si no hay usuario seleccionado, mostrar la gráfica general
            return $this->index(); // Llama al método index para mostrar la gráfica general
        }

        // Listado de tablas a combinar para gráficos individuales por usuario
        $tablas = [
            'clientes',
            'contactosubclientes',
            'tramitessubclientes',
            'requisitosubclientes',
            'bateriasubclientes',
            'estadocotizacionsubclientes',
            'programacionsubclientes',
            'estadoprogramacionsubclientes',
            'documentacionsubclientes',
            'aprobacioninformesfinales',
            'bateriaproveedores',
        ];

        // Apodos de las tablas para los gráficos individuales por usuario
        $apodosTablas = [
            'clientes' => 'Clientes',
            'contactosubclientes' => 'Contactos Clientes',
            'tramitessubclientes' => 'Trámites Clientes',
            'requisitosubclientes' => 'Requisitos Clientes',
            'bateriasubclientes' => 'Batería Clientes',
            'estadocotizacionsubclientes' => 'Estado Cotización Clientes',
            'programacionsubclientes' => 'Programación Clientes',
            'estadoprogramacionsubclientes' => 'Estado Programación Clientes',
            'documentacionsubclientes' => 'Documentación Clientes',
            'aprobacioninformesfinales' => 'Aprobación Informes Finales',
            'bateriaproveedores' => 'Batería Proveedores',
        ];

        // Almacenamiento temporal de los resultados por tabla
        $dataPorTabla = [];

        // Iterar sobre cada tabla y obtener los datos filtrados por el usuario seleccionado
        foreach ($tablas as $tabla) {
            if ($tabla == 'clientes') {
                $registros = DB::table($tabla)
                    ->select(DB::raw('DAYNAME(created_at) as dia'), DB::raw('count(*) as total'))
                    ->where('users_id', $usuarioId)
                    ->whereNotNull('created_at')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->groupBy('dia')
                    ->get();
            } elseif (Schema::hasColumn($tabla, 'usuarioid')) {
                $registros = DB::table($tabla)
                    ->select(DB::raw('DAYNAME(created_at) as dia'), DB::raw('count(*) as total'))
                    ->where('usuarioid', $usuarioId)
                    ->whereNotNull('created_at')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->groupBy('dia')
                    ->get();
            }

            // Agrupar los totales por día para cada tabla
            $dataPorTabla[$tabla] = collect();
            foreach ($registros as $registro) {
                $dia = $registro->dia;
                $total = $registro->total;

                // Sumar el total de registros para cada día en la tabla correspondiente
                $dataPorTabla[$tabla][$dia] = isset($dataPorTabla[$tabla][$dia]) ? $dataPorTabla[$tabla][$dia] + $total : $total;
            }
        }

        // Verificar si se seleccionó "general"
        if ($usuarioId == 'general') {
            return $this->index(); // Cargar la gráfica general sin mostrar mensajes
        }

        // Si no se encontraron registros para el usuario seleccionado
        if (empty($dataPorTabla)) {
            return redirect()->route('admin.controlprogramacion.index')->with('info', 'Este usuario tiene datos registrados, pero no ha tenido actividad que mostrar en el gráfico.');
        }

        // Traducción de los días de la semana
        $diasTraduccion = [
            'Sunday' => 'Domingo',
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
        ];

        // Obtener el día de hoy en español
        $hoy = $diasTraduccion[date('l')];
        $diasEnOrden = ['Sábado', 'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $indiceHoy = array_search($hoy, $diasEnOrden);
        $diasEnOrden = array_merge(array_slice($diasEnOrden, $indiceHoy + 1), array_slice($diasEnOrden, 0, $indiceHoy), [$hoy]);

        // Agrupar los datos por día de la semana para cada tabla
        $finalDataPorTablaUsuario = [];
        foreach ($dataPorTabla as $tabla => $data) {
            $finalDataPorTablaUsuario[$tabla] = collect($diasEnOrden)->mapWithKeys(function ($dia) use ($data, $diasTraduccion) {
                $dayInEnglish = array_search($dia, $diasTraduccion);
                $total = $data[$dayInEnglish] ?? 0;
                return [$dia => $total];
            });
        }

        // Verificación si todos los valores son cero por tabla
        $totalRegistros = collect($finalDataPorTablaUsuario)->flatten()->sum();

        if ($totalRegistros === 0) {
            return redirect()->route('admin.controlprogramacion.index')->with('info', 'Este usuario no tiene datos registrados');
        }

        // Inicializar datasetUsuario como una colección vacía para evitar errores en la vista
        $datasetUsuario = $finalDataPorTablaUsuario; // Aquí los datos específicos del usuario

        // Definir finalDataGeneral como una colección vacía para evitar errores en la vista
        $finalDataGeneral = collect();

        //dd($finalDataPorTablaUsuario); // Verificar si los datos están correctamente formateados

        return view('admin.controlprogramacion.index', compact('finalDataPorTablaUsuario', 'diasEnOrden', 'usuarios', 'usuarioId', 'datasetUsuario', 'finalDataGeneral', 'apodosTablas'));
    }

    public function resumen(Request $request)
    {
        $hoy = Carbon::today(); // fecha base (medianoche)
        $inicioBusqueda = $hoy->copy()->subDays(30)->startOfDay(); // rango amplio inicio
        $finBusqueda = $hoy->copy()->endOfDay(); // incluir TODO el día de hoy (23:59:59)

        // === RESUMEN GENERAL POR USUARIO Y FECHA ===
        $consultas = [
            DB::table('procedimientotramites')
                ->select(
                    DB::raw("usuarioregistro as usuario"),
                    DB::raw("DATE(created_at) as fecha"),
                    DB::raw("COUNT(*) as total")
                )
                ->whereBetween('created_at', [$inicioBusqueda, $finBusqueda])
                ->groupBy('usuario', 'fecha'),

            DB::table('subprocedimientotramites')
                ->select(
                    DB::raw("usuarioregistronombre as usuario"),
                    DB::raw("DATE(created_at) as fecha"),
                    DB::raw("COUNT(*) as total")
                )
                ->whereBetween('created_at', [$inicioBusqueda, $finBusqueda])
                ->groupBy('usuario', 'fecha'),

            DB::table('instructivaspoder')
                ->select(
                    DB::raw("usuarioregistronombre as usuario"),
                    DB::raw("DATE(created_at) as fecha"),
                    DB::raw("COUNT(*) as total")
                )
                ->whereBetween('created_at', [$inicioBusqueda, $finBusqueda])
                ->groupBy('usuario', 'fecha'),

            DB::table('agendamientoprocedimientos')
                ->select(
                    DB::raw("usuarioregistronombre as usuario"),
                    DB::raw("DATE(created_at) as fecha"),
                    DB::raw("COUNT(*) as total")
                )
                ->whereBetween('created_at', [$inicioBusqueda, $finBusqueda])
                ->groupBy('usuario', 'fecha'),

            DB::table('criteriosdictamen')
                ->select(
                    DB::raw("usuarioregistronombre as usuario"),
                    DB::raw("DATE(created_at) as fecha"),
                    DB::raw("COUNT(*) as total")
                )
                ->whereBetween('created_at', [$inicioBusqueda, $finBusqueda])
                ->groupBy('usuario', 'fecha'),

            DB::table('tramitessubclientes')
                ->select(
                    DB::raw("usuarioregistro as usuario"),
                    DB::raw("DATE(created_at) as fecha"),
                    DB::raw("COUNT(*) as total")
                )
                ->whereBetween('created_at', [$inicioBusqueda, $finBusqueda])
                ->groupBy('usuario', 'fecha'),
        ];

        // Unir subconsultas
        $query = array_shift($consultas);
        foreach ($consultas as $subquery) {
            $query->unionAll($subquery);
        }

        $resumenGeneral = DB::query()
            ->fromSub($query, 't')
            ->select('usuario', 'fecha', DB::raw('SUM(total) as total'))
            ->groupBy('usuario', 'fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        $fechas = [];
        $offset = 0;
        while (count($fechas) < 7) {
            $dia = $hoy->copy()->subDays($offset)->format('Y-m-d');
            $totalDia = $resumenGeneral->where('fecha', $dia)->sum('total');
            if ($totalDia > 0) {
                array_unshift($fechas, $dia);
            }
            $offset++;
            if ($offset > 60) break;
        }

        $usuarios = $resumenGeneral->pluck('usuario')->unique()->values();

        $datasets = [];
        foreach ($usuarios as $usuario) {
            $data = [];
            foreach ($fechas as $fecha) {
                $registro = $resumenGeneral
                            ->where('usuario', $usuario)
                            ->where('fecha', $fecha)
                            ->sum('total');
                $data[] = (int) $registro;
            }
            if (array_sum($data) === 0) continue;
            $datasets[] = [
                'label' => $usuario,
                'data' => $data,
                'backgroundColor' => 'rgba('.rand(0,255).','.rand(0,255).','.rand(0,255).',0.6)',
                'borderColor' => 'rgba(0,0,0,0.7)',
                'borderWidth' => 1
            ];
        }

        $tablas = [
            'tramitessubclientes' => 'usuarioregistro',
            'instructivaspoder' => 'usuarioregistronombre',
            'procedimientotramites' => 'usuarioregistro',
            'subprocedimientotramites' => 'usuarioregistronombre',
            'agendamientoprocedimientos' => 'usuarioregistronombre',
            'criteriosdictamen' => 'usuarioregistronombre',
        ];

        $datasetsPorTabla = [];

        foreach ($usuarios as $usuario) {
            foreach ($tablas as $tabla => $campoUsuario) {
                $data = [];
                foreach ($fechas as $fecha) {
                    $registro = DB::table($tabla)
                        ->where($campoUsuario, $usuario)
                        ->whereDate('created_at', $fecha)
                        ->count();
                    $data[] = (int) $registro;
                }

                if(array_sum($data) === 0) continue;

                $datasetsPorTabla[] = [
                    'usuario' => $usuario,
                    'tabla' => $tabla,
                    'data' => $data
                ];
            }
        }
        $tablasAmigables = [
            'tramitessubclientes' => 'TRÁMITES',
            'instructivaspoder' => 'INSTRUCTIVAS DE PODER',
            'procedimientotramites' => 'PROCEDIMIENTOS',
            'subprocedimientotramites' => 'SUBPROCEDIMIENTOS',
            'agendamientoprocedimientos' => 'AGENDAMIENTOS',
            'criteriosdictamen' => 'CRITERIOS DICTAMEN',
            
        ];

        // === RANGO DE FECHAS ===
        $fechaDesde = $request->input('desde', Carbon::now()->toDateString());
        $fechaHasta = $request->input('hasta', Carbon::now()->toDateString());
        $usuario = $request->input('usuario');

        // === FUNCIONARIOS DISPONIBLES ===
        $funcionarios = DB::table('procedimientotramites')
            ->select('usuarioregistro')
            ->distinct()
            ->orderBy('usuarioregistro')
            ->pluck('usuarioregistro');

        // === CONSULTA PRINCIPAL (AGRUPADA) ===
        $query = DB::table('procedimientotramites as p')
            ->select(
                'p.clientenombre',
                'p.tramite',
                DB::raw('COUNT(*) as total_registros'),
                DB::raw('MAX(p.created_at) as ultima_fecha')
            )
            ->whereBetween(DB::raw('DATE(p.created_at)'), [$fechaDesde, $fechaHasta]);

        if ($usuario) {
            $query->where('p.usuarioregistro', $usuario);
        }

        $resumenClientes = $query
            ->groupBy('p.clientenombre', 'p.tramite')
            ->orderBy(DB::raw('MIN(p.created_at)'), 'asc') // 🔹 Orden del más antiguo al más reciente
            ->get();

        // === DETALLE POR CLIENTE / TRÁMITE / USUARIO ===
        $detallesUsuarios = DB::table('procedimientotramites as p')
            ->select(
                'p.clientenombre',
                'p.tramite',
                'p.usuarioregistro as usuario',
                DB::raw('COUNT(*) as cantidad')
            )
            ->whereBetween(DB::raw('DATE(p.created_at)'), [$fechaDesde, $fechaHasta])
            ->when($usuario, fn($q) => $q->where('p.usuarioregistro', $usuario))
            ->groupBy('p.clientenombre', 'p.tramite', 'p.usuarioregistro')
            ->get()
            ->groupBy(['clientenombre', 'tramite']);


        // === ÚLTIMO NIVEL POR TRÁMITE ===
        $ultimoNivelProcedimiento = DB::table('procedimientotramites as p')
            ->select('p.clientenombre', 'p.tramite', 'p.subprocedimiento', 'p.created_at', 'p.tipo', 'p.tipocarta')
            ->whereIn('p.id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('procedimientotramites')
                    ->groupBy('clientenombre', 'tramite');
            })
            ->get()
            ->keyBy(fn($item) => $item->clientenombre . '||' . $item->tramite);

        $estadosTramites = DB::table('tramitessubclientes')
            ->select('clienteitanombre', 'tramite', 'estado')
            ->get()
            ->groupBy(['clienteitanombre','tramite']);

        // === DETALLE DE REGISTROS ===
        $detallesRegistrosQuery = DB::table('procedimientotramites')
            ->select(
                'id',
                'tramite',
                'idtramite',
                'tipo',
                'nivelprocedimiento',
                'subprocedimiento',
                'tipocarta',
                'clientenombre',
                'usuarioregistro',
                'created_at'
            )
            ->whereBetween(DB::raw('DATE(created_at)'), [$fechaDesde, $fechaHasta]);

        if ($usuario) {
            $detallesRegistrosQuery->where('usuarioregistro', $usuario);
        }

        $detallesRegistros = $detallesRegistrosQuery
            ->orderBy('created_at', 'asc') // 🔹 Orden del más antiguo al más reciente
            ->get()
            ->groupBy(['clientenombre', 'tramite']);// === RANGO DE FECHAS ===
        $fechaDesde = $request->input('desde', Carbon::now()->toDateString());
        $fechaHasta = $request->input('hasta', Carbon::now()->toDateString());
        $usuario = $request->input('usuario');

        // === FUNCIONARIOS DISPONIBLES ===
        $funcionarios = DB::table('procedimientotramites')
            ->select('usuarioregistro')
            ->distinct()
            ->orderBy('usuarioregistro')
            ->pluck('usuarioregistro');

        // === CONSULTA PRINCIPAL (AGRUPADA) ===
        $query = DB::table('procedimientotramites as p')
            ->select(
                'p.clientenombre',
                'p.tramite',
                DB::raw('COUNT(*) as total_registros'),
                DB::raw('MAX(p.created_at) as ultima_fecha')
            )
            ->whereBetween(DB::raw('DATE(p.created_at)'), [$fechaDesde, $fechaHasta]);

        if ($usuario) {
            $query->where('p.usuarioregistro', $usuario);
        }

        $resumenClientes = $query
            ->groupBy('p.clientenombre', 'p.tramite')
            ->orderBy(DB::raw('MAX(p.created_at)'), 'asc') // 🔹 Orden del más antiguo al más reciente
            ->get();

        // === DETALLE POR CLIENTE / TRÁMITE / USUARIO ===
        $detallesUsuarios = DB::table('procedimientotramites as p')
            ->select(
                'p.clientenombre',
                'p.tramite',
                'p.usuarioregistro as usuario',
                DB::raw('COUNT(*) as cantidad')
            )
            ->whereBetween(DB::raw('DATE(p.created_at)'), [$fechaDesde, $fechaHasta])
            ->when($usuario, fn($q) => $q->where('p.usuarioregistro', $usuario))
            ->groupBy('p.clientenombre', 'p.tramite', 'p.usuarioregistro')
            ->get()
            ->groupBy(['clientenombre', 'tramite']);

        // === ÚLTIMA FECHA GLOBAL DEL FUNCIONARIO ===
        $ultimaFechaFuncionario = null;
        if ($usuario) {
            $ultimaFechaFuncionario = DB::table('procedimientotramites')
                ->where('usuarioregistro', $usuario)
                ->max('created_at');
        }
        // Siempre calcular la última fecha total
        $ultimaFechaTotal = DB::table('procedimientotramites')
            ->max('created_at');

        // === ÚLTIMO NIVEL POR TRÁMITE ===
        $ultimoNivelProcedimiento = DB::table('procedimientotramites as p')
            ->select('p.clientenombre', 'p.tramite', 'p.subprocedimiento', 'p.created_at', 'p.tipo', 'p.tipocarta')
            ->whereIn('p.id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('procedimientotramites')
                    ->groupBy('clientenombre', 'tramite');
            })
            ->get()
            ->keyBy(fn($item) => $item->clientenombre . '||' . $item->tramite);

        $estadosTramites = DB::table('tramitessubclientes')
            ->select('clienteitanombre', 'tramite', 'estado')
            ->get()
            ->groupBy(['clienteitanombre','tramite']);

        // === DETALLE DE REGISTROS ===
        $detallesRegistrosQuery = DB::table('procedimientotramites')
            ->select(
                'id',
                'tramite',
                'idtramite',
                'tipo',
                'nivelprocedimiento',
                'subprocedimiento',
                'tipocarta',
                'clientenombre',
                'usuarioregistro',
                'created_at'
            )
            ->whereBetween(DB::raw('DATE(created_at)'), [$fechaDesde, $fechaHasta]);

        if ($usuario) {
            $detallesRegistrosQuery->where('usuarioregistro', $usuario);
        }
        $detallesRegistros = $detallesRegistrosQuery
            ->orderBy('created_at', 'asc')
            ->get()
        ->groupBy(['clientenombre', 'tramite']);
        // === GRAFICO DE ACTIVIDAD POR HORA ===
        $actividadPorHora = DB::table('procedimientotramites')
            ->select(
                DB::raw('HOUR(created_at) as hora'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween(DB::raw('DATE(created_at)'), [$fechaDesde, $fechaHasta])
            ->when($usuario, fn($q) => $q->where('usuarioregistro', $usuario))
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy(DB::raw('HOUR(created_at)'), 'asc')
            ->pluck('total', 'hora')
        ->toArray();

        // Generar un arreglo completo de 0 a 23 horas
        $horas = range(0, 23);
        $actividadFormateada = [];
        foreach ($horas as $h) {
            $actividadFormateada[] = $actividadPorHora[$h] ?? 0;
        }

        return view('admin.controlprogramacion.registrosprestaciones', compact(
            'resumenGeneral', 'fechas', 'datasets', 'resumenClientes','detallesUsuarios',
            'ultimoNivelProcedimiento','estadosTramites','funcionarios','usuario','fecha','fechaDesde', 'fechaHasta',
            'ultimaFechaFuncionario','detallesRegistros','actividadFormateada' ,'ultimaFechaTotal','datasetsPorTabla','tablasAmigables'
        ));

    }
    public function exportarResumenTramitesCSV(Request $request)
    {
        $usuario = $request->usuario;
        $fechaDesde = $request->desde;
        $fechaHasta = $request->hasta;

        // === CONSULTA BASE (resumen principal) ===
        $resumenClientes = DB::table('procedimientotramites as p')
            ->select(
                'p.clientenombre',
                'p.tramite',
                DB::raw('COUNT(*) as total_registros'),
                DB::raw('MAX(p.created_at) as ultima_fecha')
            )
            ->whereBetween(DB::raw('DATE(p.created_at)'), [$fechaDesde, $fechaHasta])
            ->when($usuario, fn($q) => $q->where('p.usuarioregistro', $usuario))
            ->groupBy('p.clientenombre', 'p.tramite')
            ->orderBy(DB::raw('MIN(p.created_at)'), 'asc')
            ->get();

        // === DETALLES POR USUARIO ===
        $detallesUsuarios = DB::table('procedimientotramites as p')
            ->select('p.clientenombre', 'p.tramite', 'p.usuarioregistro as usuario', DB::raw('COUNT(*) as cantidad'))
            ->whereBetween(DB::raw('DATE(p.created_at)'), [$fechaDesde, $fechaHasta])
            ->when($usuario, fn($q) => $q->where('p.usuarioregistro', $usuario))
            ->groupBy('p.clientenombre', 'p.tramite', 'p.usuarioregistro')
            ->get()
            ->groupBy(fn($r) => $r->clientenombre . '||' . $r->tramite);

        // === ÚLTIMO PROCEDIMIENTO ===
        $ultimoNivelProcedimiento = DB::table('procedimientotramites as p')
            ->select('p.clientenombre', 'p.tramite', 'p.tipo', 'p.subprocedimiento', 'p.tipocarta', 'p.created_at')
            ->where(function ($query) use ($resumenClientes) {
                foreach ($resumenClientes as $r) {
                    $query->orWhere(function ($q) use ($r) {
                        $q->where('p.clientenombre', $r->clientenombre)
                        ->where('p.tramite', $r->tramite);
                    });
                }
            })
            ->orderBy('p.created_at', 'desc')
            ->get()
            ->groupBy(fn($r) => $r->clientenombre . '||' . $r->tramite);

        // === ESTADOS ===
        $estadosTramites = DB::table('tramitessubclientes')
            ->select('clienteitanombre', 'tramite', 'estado')
            ->get()
            ->groupBy(fn($r) => $r->clienteitanombre . '||' . $r->tramite);

        // === DETALLES DE REGISTROS (lista expandible) ===
        $detallesRegistros = DB::table('procedimientotramites')
            ->select('id', 'tramite', 'tipo', 'nivelprocedimiento', 'subprocedimiento', 'tipocarta', 'clientenombre', 'usuarioregistro', 'created_at')
            ->whereBetween(DB::raw('DATE(created_at)'), [$fechaDesde, $fechaHasta])
            ->when($usuario, fn($q) => $q->where('usuarioregistro', $usuario))
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(fn($r) => $r->clientenombre . '||' . $r->tramite);

        // === GENERACIÓN DEL CSV ===
        $response = new StreamedResponse(function () use ($resumenClientes, $ultimoNivelProcedimiento, $estadosTramites, $detallesUsuarios, $detallesRegistros) {
            // 🔧 BOM UTF-8 para Excel
            echo "\xEF\xBB\xBF";

            $handle = fopen('php://output', 'w');

            // Encabezados
            fputcsv($handle, [
                'Nombre_Cliente',
                'Trámite',
                'Total_Reg.',
                'Total_Reg_por_Funcionario',
                'Fecha_Último_Reg.',
                'Último_Procedimiento',
                'Estado',
                'ID',
                'Nivel_Procedimiento',
                'Sub_Procedimiento',
                'Fecha_Reg'
            ], ';');

            foreach ($resumenClientes as $cliente) {
                $key = $cliente->clientenombre . '||' . $cliente->tramite;

                // Total por funcionario
                $detalleTexto = isset($detallesUsuarios[$key])
                    ? collect($detallesUsuarios[$key])->map(fn($d) => ($d->usuario ?? 'SIN USUARIO') . ': ' . $d->cantidad)->implode(', ')
                    : 'Sin detalle';

                // Último procedimiento
                $ultimo = $ultimoNivelProcedimiento[$key][0] ?? null;
                $fechaUltimo = $ultimo ? Carbon::parse($ultimo->created_at)->format('d/m/Y - H:i') : '-';
                $tipo = $ultimo->tipo ?? '-';
                $nivel = $ultimo->subprocedimiento ?? '-';
                $tipocarta = $ultimo->tipocarta ?? null;
                $ultimoProcedimiento = trim($tipo . ' - ' . $nivel . ($tipocarta ? ' - ' . $tipocarta : ''));

                // Estado
                $estado = $estadosTramites[$key][0]->estado ?? 'PENDIENTE';

                // Fila principal
                fputcsv($handle, [
                    $cliente->clientenombre,
                    $cliente->tramite,
                    $cliente->total_registros,
                    $detalleTexto,
                    $fechaUltimo,
                    $ultimoProcedimiento,
                    $estado,
                    '', '', '', '' // columnas vacías para expandible
                ], ';');

                // Filas de detalle (lista expandible)
                if (isset($detallesRegistros[$key])) {
                    foreach ($detallesRegistros[$key] as $d) {
                        $subprocedimiento = $d->tipo . ' - ' . $d->subprocedimiento;
                        if ($d->tipocarta) $subprocedimiento .= ' - ' . $d->tipocarta;

                        fputcsv($handle, [
                            '', '', '', '', '', '', '', // columnas vacías para alinear con fila principal
                            $d->id,
                            $d->nivelprocedimiento,
                            $subprocedimiento,
                            Carbon::parse($d->created_at)->format('d/m/Y - H:i')
                        ], ';');
                    }
                }
            }

            fclose($handle);
        });

        $filename = 'resumen_tramites_' . now()->format('Ymd_His') . '.csv';
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', "attachment; filename={$filename}");

        return $response;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $users)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->roles()->sync($request->roles);

        return redirect()->route('admin.users.edit', $user)->with('info', 'Se asignaron los roles correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index', $user)->with('eliminar', 'ok');
    }
}
