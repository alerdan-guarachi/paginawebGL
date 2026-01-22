<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Empresa;
use App\Models\Asociado;
use App\Models\Bateriaproveedor;
use App\Models\Bateriaclientecomun;
use App\Models\Areaaccion;
use App\Models\Departamento;
use App\Models\Area;
use App\Models\Banco;
use App\Models\Tipoareaaccion;
use App\Models\ClienteAuditoria;
use App\Models\ClienteComun;
use App\Models\ClienteBanco;
use App\Models\Pais;
use App\Models\Ciudad;
use App\Models\Cliente;
use App\Models\Tramitesubcliente;
use App\Models\Proveedoresservicios;
use App\Models\Aprobacioninformefinal;
use App\Models\ProveedorInformefinal;
use App\Models\Informefinal;
use App\Models\Proveedor;
use App\Models\Tramite;
use App\Models\SubTramite;
use App\Models\Aseguradora;
use App\Models\Contactosubcliente;
use App\Models\Requisitosubcliente;
use App\Models\Afp;
use App\Models\Bateriasubcliente;
use App\Models\Estadoprogramacionsubcliente;
use App\Models\Estadocotizacionsubcliente;
use App\Models\Programacionsubcliente;
use App\Models\Modelocartareclamo;
use App\Models\Documentacionsubcliente;
use App\Http\Requests\StoreInformefinalRequest;
use App\Http\Requests\StoreTramiteRequest;
use App\Http\Requests\StoreProveedorInformefinalRequest;
use App\Http\Requests\UpdateProveedorInformefinalRequest;
use PDF;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TablaExport;
use App\Models\InstructivasPoder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Models\PermisoCodigo;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateClienteitaRequest;
use App\Models\ModificacionesDatos;
use App\Models\AgendamientoProcedimiento;
use App\Models\CriteriosDictamen;
use App\Notifications\AsignacionApoderado;
use App\Models\RecomendacionBaterias;
use App\Models\CambiosApoderados;
use App\Notifications\RecordarSubirReqNotification;
use App\Notifications\SolicitarCreacionBateria;

//NUEVO APP MOVIL
use App\Notifications\AvanceTramiteNotification;
use App\Services\FirebaseService;

class TramitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /* public function __construct() { 
        $this->middleware ('can:admin.empresas.index')->only('index');
    } */

    public function documentosprogramaciones(Request $request)
    {
        // Obtener los registros de Programacionsubcliente junto con sus relaciones
        $programacionclientes = Programacionsubcliente::with(['estadoprogramacionsubcliente', 'documentacionsubcliente'])
            ->whereNotNull('clienteitaid')
            ->get();
    
        // Procesar los datos para agregar las fechas de atención y creación
        $programacionclientes->each(function ($programacioncliente) {
            $programacioncliente->fechaatencionprogramacion = null;
            $programacioncliente->created_at = null;
    
            // Verificar los estados relacionados
            if ($programacioncliente->estadoprogramacionsubcliente->isNotEmpty()) {
                foreach ($programacioncliente->estadoprogramacionsubcliente as $estado) {
                    if ($estado->clienteitaid == $programacioncliente->clienteitaid
                        && $estado->accionnombre == $programacioncliente->accionnombre
                        && $estado->fechabateria == $programacioncliente->fechabateria
                        && $estado->fechaatencionprogramacion) {
                        $programacioncliente->fechaatencionprogramacion = $estado->fechaatencionprogramacion;
                        break; // Salir del bucle una vez que encontramos la coincidencia
                    }
                }
            }
    
            // Verificar los documentos relacionados
            if ($programacioncliente->documentacionsubcliente->isNotEmpty()) {
                foreach ($programacioncliente->documentacionsubcliente as $documento) {
                    if ($documento->clienteitaid == $programacioncliente->clienteitaid
                        && $documento->accion == $programacioncliente->accionnombre
                        && $documento->fechabateria == $programacioncliente->fechabateria
                        && $documento->document) {
                        $programacioncliente->document = $documento->document;
                        break; // Salir del bucle una vez que encontramos la coincidencia
                    }
                }
            }
        });
    
        return view('admin.informesfinales.documentosprogramaciones', compact('programacionclientes'));
    }

    public function asignarapoderadotramiteclienteita(Request $request)
    {
        // Validar la solicitud
        $validatedData = $request->validate([
            'clienteitaid' => 'required',
            'apoderadoasignado' => 'required',
            'fechabateria' => '',
            'tramite' => 'required',
        ]);

        $clienteID = $validatedData['clienteitaid'];
        $apoderadoAsignado = $validatedData['apoderadoasignado'];
        $fechaBateria = $validatedData['fechabateria'];
        $tramiteCliente = $validatedData['tramite'];

        $tramitesubcliente = Tramitesubcliente::where('clienteitaid', $clienteID)
            ->where('tramite', $tramiteCliente)
            ->where('fechabateria', $fechaBateria)
            ->first();

        if ($tramitesubcliente) {
            $tramitesubcliente->update([
                'apoderadoasignado' => $apoderadoAsignado,
                'fechaasignacion' => now(),
            ]);

            if (!empty($apoderadoAsignado)) {
                $usuarioDestino = User::where('name', $apoderadoAsignado)->first();
                if ($usuarioDestino) {
                    $usuarioDestino->notify(new AsignacionApoderado($tramitesubcliente));
                }
            }


            return redirect()->route('admin.tramites.derivacionapoderados')->with('info', 'Apoderado asignado exitosamente.');
        } else {
            return redirect()->route('admin.tramites.derivacionapoderados')->with('error', 'Registro no encontrado.');
        }
    }

    public function index(Cliente $cliente, Request $request, Tramite $tramite)
    {
        $proveedores = Proveedor::orderBy('proveedor')->get(['id', 'proveedor', 'celular']);
        $aprobaciones = AprobacionInformeFinal::all();
        $todosclientes = Cliente::orderBy('nombrecompleto', 'asc')->get();
        $agendamientos = AgendamientoProcedimiento::all();

        $base = AgendamientoProcedimiento::query();

        if (auth()->user()->hasRole('EJECUTIVO PRESTACIONES')) {
            $base->where('usuarioregistronombre', auth()->user()->name);
        }

        $agendamientosNoAsistidos = (clone $base)->where('asistencia', '!=', 'SI')->get();
        $agendamientosAsistidos   = (clone $base)->where('asistencia', 'SI')->get();

        $todostramites = Tramitesubcliente::whereNotIn('tramite', ['AUDITORIA MEDICA', 'AUDITORIA MÉDICA'])->get();

        $user = Auth::user();
        $userRoles = $user->roles->pluck('name')->toArray();
        $rolesPermitidos = ['MAESTRO', 'ADMINISTRADOR', 'SUPERVISOR PRESTACIONES'];

        /* $todostramitesnoiniciado = Tramitesubcliente::whereNotIn('tramite', ['AUDITORIA MEDICA', 'AUDITORIA MÉDICA'])
            ->where('estado', 'PENDIENTE')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('procedimientotramites')
                    ->whereRaw('procedimientotramites.idtramite = tramitessubclientes.id');
            });
        if (empty(array_intersect($userRoles, $rolesPermitidos))) {
            $todostramitesnoiniciado->where('apoderadoasignado', $user->name);
        } else {
            $todostramitesnoiniciado->whereNotNull('apoderadoasignado');
        }
        $todostramitesnoiniciado = $todostramitesnoiniciado->get(); */

        $todostramitesnoiniciado = Tramitesubcliente::whereNotIn('tramitessubclientes.tramite', ['AUDITORIA MEDICA', 'AUDITORIA MÉDICA'])
            ->where('tramitessubclientes.estado', 'PENDIENTE')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('procedimientotramites')
                    ->whereRaw('procedimientotramites.idtramite = tramitessubclientes.id');
            })
            ->leftJoin('agendamientoprocedimientos as ag', function($join) {
                $join->on('ag.tramite', '=', 'tramitessubclientes.tramite')
                    ->on('ag.clientenombre', '=', 'tramitessubclientes.clienteitanombre');
            })
            ->select('tramitessubclientes.*', 'ag.asistencia as asistencia');

        // Condición según roles
        if (empty(array_intersect($userRoles, $rolesPermitidos))) {
            $todostramitesnoiniciado->where('tramitessubclientes.apoderadoasignado', $user->name);
        } else {
            $todostramitesnoiniciado->whereNotNull('tramitessubclientes.apoderadoasignado');
        }

        // Finalmente ejecutar la consulta
        $todostramitesnoiniciado = $todostramitesnoiniciado->get();


        $todostramitesiniciado = Tramitesubcliente::whereNotIn('tramite', ['AUDITORIA MEDICA', 'AUDITORIA MÉDICA'])->where('estado', 'PENDIENTE')
            ->whereIn('id', function($query) {
                $query->select('idtramite')->from('procedimientotramites');
            });
        if (empty(array_intersect($userRoles, $rolesPermitidos))) {
            $todostramitesiniciado->where('apoderadoasignado', $user->name);
        } else {
            $todostramitesiniciado->whereNotNull('apoderadoasignado');
        }
        $todostramitesiniciado = $todostramitesiniciado
        ->with(['procedimientos' => function ($q) {
            $q->orderBy('created_at');
        }])
        ->get();

        $todostramitesfinalizados = Tramitesubcliente::whereNotIn('tramite', ['AUDITORIA MEDICA', 'AUDITORIA MÉDICA'])->where('estado', 'FINALIZADO');
        if (empty(array_intersect($userRoles, $rolesPermitidos))) {
            $todostramitesfinalizados->where('apoderadoasignado', $user->name);
        } else {
            $todostramitesfinalizados->whereNotNull('apoderadoasignado');
        }
        $todostramitesfinalizados = $todostramitesfinalizados->get();

        $todostramitesinterrumpidos = Tramitesubcliente::whereNotIn('tramite', ['AUDITORIA MEDICA', 'AUDITORIA MÉDICA'])->where('estado', 'INTERRUMPIDO')
            ->whereIn('id', function($query) {
                $query->select('idtramite')->from('procedimientotramites');
            });
        if (empty(array_intersect($userRoles, $rolesPermitidos))) {
            $todostramitesinterrumpidos->where('apoderadoasignado', $user->name);
        } else {
            $todostramitesinterrumpidos->whereNotNull('apoderadoasignado');
        }
        $todostramitesinterrumpidos = $todostramitesinterrumpidos->get();

        $fechas = Programacionsubcliente::pluck('fechabateria')->unique()->sort()->toArray();

        $query = Programacionsubcliente::with([
            'estadoprogramacionsubcliente',
            'documentacionsubcliente',
            'proveedorinformesfinales',
            'informesfinales',
            'clienteIta'
        ])->whereNotNull('clienteitaid');

        if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
            $query->where('fechabateria', $request->buscarporfecha);
        }
        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('clienteita', function ($q) use ($request) {
                $q->where('clienteitanombre', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        $programacionclientes = $query->get();
        $grouped = $programacionclientes->groupBy(function($item) {
            return $item->clienteitanombre . '|' . $item->fechabateria;
        });
        $result = [];

        foreach ($grouped as $key => $items) {
            list($clienteNombre, $fechabateria) = explode('|', $key);
            $clienteitaid = $items->first()->clienteitaid;
            $usuarioAutenticado = auth()->user()->name;
            $clientes = Tramitesubcliente::where('clienteitanombre', $clienteNombre)
                ->where('fechabateria', $fechabateria)
            ->get();

            $tipocliente = $clientes->map(function($clienteObj) {
                return $clienteObj->tramite;
            })->unique();

            foreach ($tipocliente as $tipo) {
                $ultimoTramite = Tramite::where('clienteid', $clienteitaid)
                    ->where('tramite', $tipo)
                    ->whereNotIn('nivelprocedimiento', ['CARTAS / RECLAMOS', 'ADJUNTOS Y RESPUESTAS', 'SEGUIMIENTO'])
                    ->orderBy('created_at', 'desc')
                ->first();

                $idTramite = Tramitesubcliente::where('clienteitaid', $clienteitaid)
                    ->where('tramite', $tipo)
                ->first();

                $ultimacarta = Tramite::where('clienteid', $clienteitaid)
                    ->where('tramite', $tipo)
                    ->where('nivelprocedimiento', 'CARTAS / RECLAMOS')
                    ->orderBy('created_at', 'desc')
                ->first();

                $ultimosubTramite = Tramite::where('clienteid', $clienteitaid)
                    ->where('tramite', $tipo)
                    ->whereNotIn('nivelprocedimiento', ['CARTAS / RECLAMOS', 'ADJUNTOS Y RESPUESTAS', 'SEGUIMIENTO'])
                    ->orderBy('created_at', 'desc')
                ->first();

                $iniciotramite = Tramite::where('clienteid', $clienteitaid)
                    ->where('tramite', $tipo)
                    ->whereIn('nivelprocedimiento', ['RECEPCIÓN DE TRÁMITE', 'INGRESO DE TRÁMITE'])
                    ->orderBy('created_at', 'asc')
                ->first();

                $estadotramite = Tramitesubcliente::where('clienteitaid', $clienteitaid)
                    ->where('tramite', $tipo)
                    ->orderBy('created_at', 'desc')
                ->first();

                $nivelprocedimientotramite = $ultimoTramite ? $ultimoTramite->nivelprocedimiento : 'NO INICIADO';
                $ultimacartatramite = $ultimacarta ? $ultimacarta->subprocedimiento : 'NINGUNA CARTA';
                $nivelsubprocedimientotramite = $ultimosubTramite ? $ultimosubTramite->subprocedimiento : 'NO INICIADO';
                $iniciotramitecliente = $iniciotramite ? $iniciotramite->fechasubida : 'NO INICIADO';
                $estadotramitecliente = $estadotramite ? $estadotramite->estado : 'NO INICIADO';
                
                $proveedorAsignado = ProveedorInformeFinal::where('clienteitaid', $clienteitaid)
                    ->where('fechabateria', $fechabateria)
                ->first();

                $documentosubido = Informefinal::where('clienteitaid', $clienteitaid)
                    ->where('fechabateria', $fechabateria)
                ->first();

                $ultimoInforme = InformeFinal::withTrashed()
                    ->where('clienteitaid', $clienteitaid)
                    ->where('fechabateria', $fechabateria)
                    ->orderBy('created_at', 'desc')
                ->first();

                $apoderadoasignado = Tramitesubcliente::withTrashed()
                    ->where('clienteitaid', $clienteitaid)
                    ->where('fechabateria', $fechabateria)
                    ->where('tramite', $tipo)
                ->value('apoderadoasignado');

                $idtramite = Tramitesubcliente::withTrashed()
                    ->where('clienteitaid', $clienteitaid)
                    ->where('fechabateria', $fechabateria)
                    ->where('tramite', $tipo)
                ->value('id');

                $mensajeDias = 'N/A';
                if ($ultimoTramite) {
                    if ($ultimoTramite->nivelprocedimiento == 'DICTAMEN' && $ultimoTramite->subprocedimiento == 'NOTIFICACIÓN DE DICTAMEN') {
                        $fechaSubida = \Carbon\Carbon::parse($ultimoTramite->fechasubida);
                        $diasRestantes = max(0, 30 - $fechaSubida->diffInDays(\Carbon\Carbon::now()));
                        $mensajeDias = $diasRestantes == 1 ? '1 DÍA RESTANTE' : "$diasRestantes DÍAS RESTANTES";
                    } elseif (in_array($ultimoTramite->nivelprocedimiento, [
                        'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO',
                        'COMPRA DE SERVICIOS',
                        'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA'
                    ])) {
                        $fechaSubida = \Carbon\Carbon::now();
                        $diasRestantes = max(0, 30 - $fechaSubida->diffInDays($fechaSubida));
                        $mensajeDias = $diasRestantes == 1 ? '1 DÍA RESTANTE' : "$diasRestantes DÍAS RESTANTES";
                    } elseif ($ultimoTramite->subprocedimiento == 'RECEPCIÓN DE TRÁMITE') {
                        $recepcionTramite = Tramite::where('clienteid', $clienteitaid)
                            ->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')
                            ->orderBy('fechasubida', 'desc')
                            ->first();
                        if ($recepcionTramite && $recepcionTramite->fechasubida) {
                            $fechaSubida = \Carbon\Carbon::parse($recepcionTramite->fechasubida);
                            $diasRestantes = max(0, 10 - $fechaSubida->diffInDays(\Carbon\Carbon::now()));
                            $mensajeDias = $diasRestantes == 1 ? '1 DÍA RESTANTE' : "$diasRestantes DÍAS RESTANTES";
                        }
                    } elseif ($ultimoTramite->subprocedimiento == 'ESTADO DE AHORRO PREVISIONAL') {
                        $estadoAhorroPrevisional = Tramite::where('clienteid', $clienteitaid)
                            ->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')
                            ->orderBy('fechasubida', 'desc')
                            ->first();
                        if ($estadoAhorroPrevisional && $estadoAhorroPrevisional->fechasubida) {
                            $fechaSubida = \Carbon\Carbon::parse($estadoAhorroPrevisional->fechasubida);
                            $diasRestantes = max(0, 30 - $fechaSubida->diffInDays(\Carbon\Carbon::now()));
                            $mensajeDias = $diasRestantes == 1 ? '1 DÍA RESTANTE' : "$diasRestantes DÍAS RESTANTES";
                        }
                    }
                }

                $accionesConEstado = [];
                $estado = 'COMPLETO';

                foreach ($items as $item) {
                    $documentacion = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                    $accionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';

                    if ($accionEstado === 'PENDIENTE') {
                        $estado = 'INCOMPLETO';
                    }

                    $accionesConEstado[] = [
                        'accion' => $item->accionnombre,
                        'estado' => $accionEstado,
                        'document' => $documentacion,
                        'proveedornombre' => $item->proveedornombre,
                    ];
                }

                if ($estado === 'COMPLETO') {
                    $result[] = [
                        'idtramite' => $idtramite,
                        'clienteitanombre' => $clienteNombre,
                        'tipocliente' => $tipo,
                        'iniciotramite' => $iniciotramitecliente,
                        'fechabateria' => $fechabateria,
                        'apoderadoasignado' => $apoderadoasignado,
                        'estado' => $estado,
                        'acciones' => $accionesConEstado,
                        'clienteitaid' => $clienteitaid,
                        'proveedornombre' => $proveedorAsignado ? $proveedorAsignado->proveedorasignado : null,
                        'celularproveedor' => $proveedorAsignado ? $proveedorAsignado->celularproveedor : null,
                        'document' => $documentosubido ? $documentosubido->document : null,
                        'idinformefinal' => $documentosubido ? $documentosubido->id : null,
                        'ultima_observacion' => $ultimoInforme ? $ultimoInforme->observaciones : null,
                        'estado_informefinal' => $ultimoInforme ? $ultimoInforme->estado : null,
                        'nivelprocedimientotramite' => $nivelprocedimientotramite,
                        'nivelsubprocedimientotramite' => $nivelsubprocedimientotramite,
                        'ultimacartatramite' => $ultimacartatramite,
                        'tiempo_proximo' => $mensajeDias,
                        'estadotramite' => $estadotramitecliente,
                        'idproceso' => $idTramite ? $idTramite->id : null,
                    ];
                }
            }
        }

        // RELLENAR CON SIGUIENTE APDOERADO
            $apoderadosList = Proveedoresservicios::where('cargo', 'EJECUTIVO DE PRESTACIONES')
                ->orderBy('razonsocial')
                ->pluck('razonsocial')
                ->toArray();

            if (count($apoderadosList) === 0) {
                $apoderadoSiguiente = null;
                session(['indice_apoderado' => -1]);
            } else {
                $ultimoApoderado = Tramitesubcliente::orderBy('fechaasignacion', 'desc')
                    ->value('apoderadoasignado'); 

                $indiceActual = $ultimoApoderado !== null
                    ? array_search(trim($ultimoApoderado), $apoderadosList, true)
                    : false;

                if ($indiceActual === false) {
                    $indiceActual = -1;
                }
                $indiceSiguiente = ($indiceActual + 1) % count($apoderadosList);
                $apoderadoSiguiente = $apoderadosList[$indiceSiguiente];
                session(['indice_apoderado' => $indiceSiguiente]);
            }
            $apoderadosSelect = count($apoderadosList) 
                ? array_combine($apoderadosList, $apoderadosList) 
                : [];
        //

        //CONTEO DE REGISTROS DE CADA PESTAÑA
            $noIniciadoCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
                if ($item['apoderadoasignado'] === $usuarioAutenticado && $item['nivelprocedimientotramite'] === 'NO INICIADO' && $item['tipocliente'] !== 'APELACIÓN') {
                    $count++;
                }
                return $count;
            }, 0);

            $pendienteCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
                if ($item['apoderadoasignado'] === $usuarioAutenticado && $item['estadotramite'] === 'PENDIENTE' && $item['nivelprocedimientotramite'] !== 'NO INICIADO') {
                    $count++;
                }
                return $count;
            }, 0);

            $finalizadoCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
                if ($item['apoderadoasignado'] === $usuarioAutenticado && $item['estadotramite'] === 'FINALIZADO') {
                    $count++;
                }
                return $count;
            }, 0);

            $derivarCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
                if (!$item['apoderadoasignado']) {
                    $count++;
                }
                return $count;
            }, 0);

            $apelacionCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
                if ($item['tipocliente'] === 'APELACIÓN' && $item['estadotramite'] === 'PENDIENTE' && $item['apoderadoasignado'] === $usuarioAutenticado) {
                    $count++;
                }
                return $count;
            }, 0);
        //
    
        /* ->where('c.aseguradora', 'CAJA PETROLERA DE SALUD') */

        $tipos = [  
            'PROGRAMACIONES SITM ENTE GESTOR DE SALUD',
            'PROGRAMACIONES SITM NOTIFICACIÓN TMC',
            'PROGRAMACIONES COMPRA DE SERVICIOS',
            'PROGRAMACIONES SIC ENTE GESTOR DE SALUD',
            'PROGRAMACIONES SIC NOTIFICACIÓN TMC'
        ];

        $now = Carbon::now();

        $progcajapetrolera = DB::table('subprocedimientotramites as spt')
            ->join('clientes as c', 'c.id', '=', 'spt.clienteid')
            ->whereIn('spt.tipo', $tipos)
            ->when(!array_intersect($rolesPermitidos, $userRoles), function ($query) use ($user) {
                $query->where('spt.usuarioregistronombre', $user->name);
            })
            ->select(
                'c.nombrecompleto as cliente',
                'c.id as idcliente',
                'spt.tramite',
                'spt.tipo',

                DB::raw("
                    SUM(
                        CASE 
                        WHEN (spt.fechareprogramacion IS NOT NULL AND spt.horareprogramacion IS NOT NULL)
                                OR (spt.fechaprogramacion IS NOT NULL AND spt.horaprogramacion IS NOT NULL)
                        THEN 0 
                        ELSE 1 
                        END
                    ) as pendientes
                "),

                DB::raw("
                    MAX(
                        CASE 
                        WHEN (spt.fechareprogramacion IS NOT NULL AND spt.horareprogramacion IS NOT NULL)
                        THEN CONCAT(spt.fechareprogramacion, ' ', spt.horareprogramacion)
                        ELSE CONCAT(spt.fechaprogramacion, ' ', spt.horaprogramacion)
                        END
                    ) as ultima_dt
                ")
            )
            ->groupBy('spt.clienteid', 'spt.tramite', 'spt.tipo', 'c.nombrecompleto', 'c.id')
            ->having('pendientes', '>', 0)
            ->get()
            ->map(function ($item) use ($now) {
                $item->ultima_fecha = null;
                $item->estado = '';

                if (!empty($item->ultima_dt)) {
                    $dt = Carbon::parse($item->ultima_dt);
                    $item->ultima_fecha = $dt->format('Y-m-d H:i:s');

                    $diffHoursPassed = $dt->diffInHours($now);

                    $diffHoursRemaining = $now->diffInHours($dt, false);

                    if ($diffHoursRemaining >= 0 && $diffHoursRemaining <= 24) {
                        $item->estado = 'SEGUIMIENTO A CLIENTE';
                    } elseif ($diffHoursPassed > 24 && $diffHoursPassed <= 48) {
                        $item->estado = 'PROGRAMACIÓN PENDIENTE';
                    } elseif ($diffHoursPassed > 48 && $diffHoursPassed < 168) {
                        $item->estado = 'PROGRAMACIÓN RETRASADA';
                    } elseif ($diffHoursPassed >= 168) {
                        $item->estado = 'PROGRAMACIÓN EN MORA';
                    } else {
                        $item->estado = 'PROGRAMACIÓN PENDIENTE';
                    }
                } else {
                    $item->ultima_fecha = '—';
                    $item->estado = 'PENDIENTE';
                }

                return $item;
            });


        return view('admin.tramites.index', compact('todostramitesinterrumpidos','todostramitesfinalizados','todostramitesiniciado','agendamientosNoAsistidos','agendamientosAsistidos','todostramitesnoiniciado','todostramites','agendamientos','todosclientes','progcajapetrolera','apelacionCount', 'derivarCount', 'finalizadoCount', 'pendienteCount', 'noIniciadoCount', 'usuarioAutenticado','proveedores', 'result', 'cliente', 'fechas', 'aprobaciones','apoderadosSelect', 'apoderadoSiguiente'));
    }

    public function derivacionapoderados(Cliente $cliente, Request $request, Tramite $tramite)
    {
        $agendamientos = AgendamientoProcedimiento::all();
        $todosclientes = Cliente::orderBy('nombrecompleto', 'asc')->get();
        $base = AgendamientoProcedimiento::query();

        if (auth()->user()->hasRole('EJECUTIVO PRESTACIONES')) {
            $base->where('usuarioregistronombre', auth()->user()->name);
        }

        $agendamientosNoAsistidos = (clone $base)->where('asistencia', '!=', 'SI')->get();
        $agendamientosAsistidos   = (clone $base)->where('asistencia', 'SI')->get();

        $todostramites = Tramitesubcliente::whereNotIn('tramite', ['AUDITORIA MEDICA', 'AUDITORIA MÉDICA'])
        ->leftJoin('requisitosubclientes as req', function($join) {
            $join->on('tramitessubclientes.clienteitaid', '=', 'req.clienteitaid')
                ->on('tramitessubclientes.tramite', '=', 'req.servicio')
                ->whereNull('req.deleted_at');
        })
        ->select(
            'tramitessubclientes.*',
            'req.contrato as contrato_req',
            'req.poder as poder_req'
        )
        ->get();

        $apoderadosList = [];

        foreach ($todostramites as $tramite) {
            $clienteEsPar = ((int) $tramite->clienteitaid % 2 === 0);

            $proveedor = Proveedoresservicios::whereNotNull('nroderivtramites')
                ->where('ciudad', $tramite->ciudad)
                ->where('nroderivtramites', $clienteEsPar ? 'PARES' : 'IMPARES')
                ->first();

            if ($proveedor) {
                $apoderadosList[$tramite->id] = [
                    'lista' => [$proveedor->razonsocial => $proveedor->razonsocial],
                    'siguiente' => $proveedor->razonsocial
                ];
            } else {
                $apoderadosList[$tramite->id] = [
                    'lista' => [],
                    'siguiente' => null
                ];
            }
        }

        $notificaciones = DB::table('notifications')
        ->where('type', RecordarSubirReqNotification::class)
        ->get()
        ->map(function ($n) {
            $data = json_decode($n->data, true);
            return isset($data['registro_id']) ? (int) $data['registro_id'] : null;
        })
        ->filter()
        ->unique()
        ->values()
        ->all();

        return view('admin.tramites.derivacionapoderados', compact('notificaciones','todosclientes','todostramites','agendamientos','apoderadosList' /* ,'apoderadosSelect', 'apoderadoSiguiente' */));
    }
    /* public function recordarSubirRequisitos(Request $request, $recordarId)
    {
        $recordar = Tramitesubcliente::find($recordarId);

        if ($recordar && $request->apoderadoasignado) {
            $usuarioDestino = User::where('name', $request->apoderadoasignado)->first();

            if ($usuarioDestino) {
                $usuarioDestino->notify(new RecordarSubirReqNotification([
                    'usuario' => auth()->user()->name,
                    'estadoReq' => $request->estadoReq,
                    'cliente' => $recordar->clienteitanombre,
                    'registro_id' => $recordar->id
                ]));
            }
        }

        return redirect()->back()->with('info', 'Recordatorio enviado con éxito');
    } */
    public function recordarSubirRequisitos(Request $request, $recordarId)
    {
        $recordar = Tramitesubcliente::find($recordarId);

        if (!$recordar) {
            return redirect()->back()->with('error', 'No se encontró el trámite');
        }

        $estadoReq = $request->estadoReq;
        $apoderadoAsignado = $request->apoderadoasignado;

        if (in_array($estadoReq, ['PODER', 'CONTRATO Y PODER']) && $apoderadoAsignado) {
            $usuarioApoderado = User::where('name', $apoderadoAsignado)->first();

            if ($usuarioApoderado) {
                $usuarioApoderado->notify(new RecordarSubirReqNotification([
                    'usuario' => auth()->user()->name,
                    'estadoReq' => $estadoReq,
                    'cliente' => $recordar->clienteitanombre,
                    'registro_id' => $recordar->id
                ]));
            }
        }

        if (in_array($estadoReq, ['CONTRATO', 'CONTRATO Y PODER'])) {
            $usuariosAdmin = User::role('ADMINISTRADOR')->get();

            foreach ($usuariosAdmin as $admin) {
                $admin->notify(new RecordarSubirReqNotification([
                    'usuario' => auth()->user()->name,
                    'estadoReq' => $estadoReq,
                    'cliente' => $recordar->clienteitanombre,
                    'registro_id' => $recordar->id
                ]));
            }
        }

        return redirect()->back()->with('info', 'Recordatorio enviado con éxito');
    }


    public function buscarPendiente($id)
    {
        $tramite = Tramitesubcliente::where('id', $id)
            ->where('estado','PENDIENTE')
            ->first();

        if(!$tramite) return response()->json(null);

        $apoderados = InstructivasPoder::where('clienteid', $tramite->clienteitaid)
            ->where('tramite', $tramite->tramite)
            ->first([
                'apoderado1','apoderado2','apoderado3','apoderado4','apoderado5',
                'apoderado6','apoderado7','apoderado8','apoderado9','apoderado10'
            ]);

        $apoderadosList = collect($apoderados)->filter()->values();

        return response()->json([
            'id' => $tramite->id,
            'tramite' => $tramite->tramite,
            'clienteitaid' => $tramite->clienteitaid,
            'clienteitanombre' => $tramite->clienteitanombre,
            'apoderadoasignado' => $tramite->apoderadoasignado,
            'estado' => $tramite->estado,
            'observaciones' => $tramite->observaciones,
            'ciudad' => $tramite->ciudad,
            'fechaasignacion' => $tramite->fechaasignacion,
            'apoderados' => $apoderadosList
        ]);
    }
    public function cambiarApoderado(Request $request)
    {
        $user = Auth::user();

        $tramite = Tramitesubcliente::findOrFail($request->tramiteid);
        $anterior = $tramite->apoderadoasignado;
        $fechaAnterior = $tramite->fechaasignacion;

        // actualizar trámite
        $tramite->apoderadoasignado = $request->apoderadoactual;
        $tramite->fechaasignacion = now();
        $tramite->save();

        // registrar cambio
        CambiosApoderados::create([
            'clienteid' => $request->clienteid,
            'clientenombre' => $request->clientenombre,
            'tramite' => $tramite->tramite,
            'tramiteid' => $tramite->id,
            'apoderadoanterior' => $anterior,
            'apoderadoactual' => $request->apoderadoactual,
            'motivocambio' => $request->motivocambio,
            'usuarioregistroid' => $user->id,
            'usuarioregistronombre' => $user->name,
            'fechaasignacionanterior' => $fechaAnterior,
            'fechaasignacionactual' => now()
        ]);

        return redirect()->back()->with('info','Apoderado actualizado correctamente.');
    }
    public function interrumpirTramite(Request $request)
    {
        $user = Auth::user();

        $tramite = Tramitesubcliente::findOrFail($request->tramiteid);

        $tramite->estado = $request->estadointerrupcion;
        $tramite->fechafinalizacion = now();
        $tramite->usuariointerid = $user->id;
        $tramite->usuariointernombre = $user->name;
        $tramite->motivointerrupcion = $request->motivocambio;
        $tramite->save();

        return redirect()->back()->with('info','Trámite finalizado/interrumpido correctamente.');
    }
    public function modelocartareclamo(Request $request)
    {
        $tipocarta = $request->get('buscarpor');

        $mdoelocartasreclamos = Modelocartareclamo::where('tipocarta', 'LIKE', "%$tipocarta%")
                        ->simplePaginate(1000);

        return view('admin.tramites.modelocartareclamo', compact('mdoelocartasreclamos'));
    }
        
    /* public function index(Cliente $cliente, Request $request)
    {
        $proveedores = Proveedor::orderBy('proveedor')->get(['id', 'proveedor', 'celular']);
        $aprobaciones = AprobacionInformeFinal::all();
        $fechas = Programacionsubcliente::pluck('fechabateria')->unique()->sort()->toArray();

        $query = Programacionsubcliente::with([
            'estadoprogramacionsubcliente',
            'documentacionsubcliente',
            'proveedorinformesfinales',
            'informesfinales',
            'clienteIta'
        ])->whereNotNull('clienteitaid');

        if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
            $query->where('fechabateria', $request->buscarporfecha);
        }

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('clienteita', function ($q) use ($request) {
                $q->where('clienteitanombre', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        $programacionclientes = $query->get();

        $result = [];

        foreach ($programacionclientes as $item) {
            $clienteNombre = $item->clienteitanombre;
            $fechabateria = $item->fechabateria;

            // Consultas para obtener información relevante
            $proveedorAsignado = ProveedorInformeFinal::where('clienteitaid', $item->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->first();

            $documentosubido = Informefinal::where('clienteitaid', $item->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->first();

            $ultimoInforme = InformeFinal::withTrashed()
                ->where('clienteitaid', $item->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('created_at', 'desc')
                ->first();

            $ultimoSubprocedimiento = Tramite::where('clienteitaid', $item->clienteitaid)
                ->where('nivelprocedimiento', '<>', 'CARTAS / RECLAMOS')
                ->where('nivelprocedimiento', '<>', 'ADJUNTOS Y RESPUESTAS')
                ->orderBy('created_at', 'desc')
                ->value('subprocedimiento');

            $ultimaCartaTramite = Tramite::where('clienteitaid', $item->clienteitaid)
                ->where('nivelprocedimiento', 'CARTAS / RECLAMOS')
                ->orderBy('created_at', 'desc')
                ->value('subprocedimiento');

            $estadoAhorroPrevisional = Tramite::where('clienteitaid', $item->clienteitaid)
                ->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')
                ->exists();

            $dictamen = Tramite::where('clienteitaid', $item->clienteitaid)
                ->where('nivelprocedimiento', 'DICTAMEN')
                ->exists();

            $nivelesInformacion = Tramite::where('clienteitaid', $item->clienteitaid)
                ->whereIn('nivelprocedimiento', [
                    'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO',
                    'COMPRA DE SERVICIOS',
                    'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA'
                ])
                ->exists();

            if ($dictamen) {
                $mensajeDias = 'N/A';
            } elseif ($nivelesInformacion) {
                $mensajeDias = 'N/A';
                $fechaSubida = \Carbon\Carbon::now();
                $diasRestantes = max(0, 30 - $fechaSubida->diffInDays($fechaSubida));
                $mensajeDias = $diasRestantes == 1 ? '1 DÍA RESTANTE' : "$diasRestantes DÍAS RESTANTES";
            } elseif (!$estadoAhorroPrevisional) {
                $recepcionTramite = Tramite::where('clienteitaid', $item->clienteitaid)
                    ->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')
                    ->orderBy('fechasubida', 'desc')
                    ->first();

                $mensajeDias = 'N/A';
                if ($recepcionTramite && $recepcionTramite->fechasubida) {
                    $fechaSubida = \Carbon\Carbon::parse($recepcionTramite->fechasubida);
                    $diasRestantes = max(0, 10 - $fechaSubida->diffInDays(\Carbon\Carbon::now()));
                    $mensajeDias = $diasRestantes == 1 ? '1 DÍA RESTANTE' : "$diasRestantes DÍAS RESTANTES";
                }
            } elseif ($estadoAhorroPrevisional) {
                $estadoAhorroPrevisional = Tramite::where('clienteitaid', $item->clienteitaid)
                    ->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')
                    ->orderBy('fechasubida', 'desc')
                    ->first();

                $mensajeDias = 'N/A';
                if ($estadoAhorroPrevisional && $estadoAhorroPrevisional->fechasubida) {
                    $fechaSubida = \Carbon\Carbon::parse($estadoAhorroPrevisional->fechasubida);
                    $diasRestantes = max(0, 30 - $fechaSubida->diffInDays(\Carbon\Carbon::now()));
                    $mensajeDias = $diasRestantes == 1 ? '1 DÍA RESTANTE' : "$diasRestantes DÍAS RESTANTES";
                }
            }

            // Procesar cada trámite del cliente
            $clientes = Tramitesubcliente::where('clienteitanombre', $clienteNombre)->get();

            foreach ($clientes as $cliente) {
                $tipocliente = $cliente->tramite;

                // Verificar si hay una observación
                $ultimaObservacion = $ultimoInforme ? $ultimoInforme->observaciones : null;
                $ultimoEstado = $ultimoInforme ? $ultimoInforme->estado : null;

                $estado = 'COMPLETO'; // Inicialmente establecido como COMPLETO
                $accionesConEstado = [];

                // Determinar el estado de cada acción
                $documentacion = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                $accionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';

                if ($accionEstado === 'PENDIENTE') {
                    $estado = 'INCOMPLETO'; // Si alguna acción está PENDIENTE, el estado general será INCOMPLETO
                }

                $accionesConEstado[] = [
                    'accion' => $item->accionnombre,
                    'estado' => $accionEstado,
                    'document' => $documentacion,
                    'proveedornombre' => $item->proveedornombre,
                ];

                // Añadir al resultado
                $result[] = [
                    'clienteitanombre' => $clienteNombre,
                    'tipocliente' => $tipocliente,
                    'fechabateria' => $fechabateria,
                    'estado' => $estado,
                    'acciones' => $accionesConEstado,
                    'clienteitaid' => $item->clienteitaid,
                    'proveedornombre' => $proveedorAsignado ? $proveedorAsignado->proveedorasignado : null,
                    'celularproveedor' => $proveedorAsignado ? $proveedorAsignado->celularproveedor : null,
                    'document' => $documentosubido ? $documentosubido->document : null,
                    'idinformefinal' => $documentosubido ? $documentosubido->id : null,
                    'ultima_observacion' => $ultimaObservacion,
                    'estado_informefinal' => $ultimoEstado,
                    'ultimo_subprocedimiento' => $ultimoSubprocedimiento,
                    'ultima_cartareclamo' => $ultimaCartaTramite,
                    'tiempo_proximo' => $mensajeDias
                ];
            }
        }

        // Eliminación de duplicados basados en cliente y trámite
        $result = array_unique($result, SORT_REGULAR);

        return view('admin.tramites.index', compact('proveedores', 'result', 'cliente', 'fechas', 'aprobaciones'));
    } */

    // TRAMITE COMPENSACIÓN DE COTIZACIONES (SENASIR)
    public function proccompensacionsenasir(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
        
        $nombreclienteita = $cliente->nombrecompleto;
        $procedimientotramites = Tramite::where('clienteitanombre', $nombreclienteita)
                                ->where('tramite', 'COMPENZACIÓN SENASIR')
                                ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
                                ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
                                ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
                                ->simplePaginate(10000);

        return view('admin.tramites.proccompensacionsenasir', compact('procedimientotramites','id','cliente','nombrecompleto', 'personal'));
    }
    public function cartasproccompensacionsenasir(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'COMPENSACIÓN DE COTIZACIONES (SENASIR)')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'COMPENSACIÓN DE COTIZACIONES (SENASIR)')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        $apoderados = collect($apoderadosData)
            ->filter(fn($valor) => !is_null($valor) && $valor !== '')
            ->values()
            ->toArray();
        if ($apoderadoAsignado && !in_array($apoderadoAsignado, $apoderados)) {
            array_unshift($apoderados, $apoderadoAsignado);
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_merge($apoderados, $apoderadosExtra);
        $apoderados = array_unique($apoderados);

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'COMPENSACIÓN DE COTIZACIONES (SENASIR)')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });


        return view('admin.tramites.cartasproccompensacionsenasir', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }

    public function guardarseguimientoclienteita(Request $request, $cliente)
    {
        $request->validate([
            'comdetalle' => 'required|string',
            'commodo2' => 'nullable|string',
            'comusuemisor2' => 'nullable|string',
            'comusureceptor2' => 'nullable|string',
            'comtipointerac2' => 'nullable|string',
            'comtipoentrega2' => 'nullable|string',
            'comtipodoc2' => 'nullable|string',
            'documentoseguimiento2' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
        ]);

        $nombreArchivo = null;

        if ($request->hasFile('documentoseguimiento2')) {

            $archivo = $request->file('documentoseguimiento2');

            // USAR DIRECTAMENTE $cliente COMO ID
            $carpetaDestino = public_path("tramitesclientesita/{$cliente}/{$request->comtramitenombre}/COMUNICACIONES");

            if (!file_exists($carpetaDestino)) {
                mkdir($carpetaDestino, 0755, true);
            }

            $nombreArchivo = Str::random(10) . '_' . $archivo->getClientOriginalName();

            $archivo->move($carpetaDestino, $nombreArchivo);
        }

        Tramite::create([
            'tramite'           => $request->comtramitenombre,
            'clienteid'         => $request->clienteid,
            'clientenombre'     => $request->clientenombre,
            'usuarioid'         => $request->usuarioid,
            'usuarioregistro'   => $request->usuarioregistro,
            'idtramite'         => $request->idtramite,
            'apoderado'         => $request->apoderado,
            'comdetalle'        => $request->comdetalle,
            'commodo'           => $request->commodo2,
            'comusuemisor'      => $request->comusuemisor2,
            'comusureceptor'    => $request->comusureceptor2,
            'comtipointerac'    => $request->comtipointerac2,
            'comtipoentrega'    => $request->comtipoentrega2,
            'comtipodoc'        => $request->comtipodoc2,
            'nivelprocedimiento'=> 'SEGUIMIENTO',
            'subprocedimiento'  => 'SEGUIMIENTO',
            'tipo'              => 'SEGUIMIENTO',
            'fechasubida'       => now(),
            'capturacomunicacion'=> $nombreArchivo,
        ]);

        return back()->with('info', 'Seguimiento registrado correctamente.');
    }

    // TRAMITE INVALIDEZ
    public function procinvalidez(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        /* NUEVO 021225 */
        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'INVALIDEZ')
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'INVALIDEZ')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'INVALIDEZ')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'INVALIDEZ')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
        ->value('id');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id) 
            ->where('tramite', 'INVALIDEZ')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
        ]);

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'INVALIDEZ')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'INVALIDEZ')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'INVALIDEZ')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMC')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMC')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        //NUEVO 101125
        $registroInfoSITMEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMR')
        ->get();

        $registroInfoCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - COMPRA DE SERVICIOS')
        ->get();

        $registroInfoSICEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMR')
        ->get();

        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'INVALIDEZ')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'INVALIDEZ')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'INVALIDEZ')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'INVALIDEZ')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'INVALIDEZ')->get();

        /* NUEVO 281125 */
        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'INVALIDEZ')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        $documentos = $programacionesRaw->merge($informes);

        foreach ($documentos as $doc) {
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            } else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 : 2);

                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $regITprog = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'IT - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();
        
        return view('admin.tramites.procinvalidez', compact('regITprog','programaciones','contactos','listacartas',
        'listaadjuntos','mescierreinicio','diasRestantescom2','registrosGuardadosProgramacioncom2','diasRestantescom',
        'registrosGuardadosProgramacioncom','nuacuacliente','cicliente','ciexpcliente','diasRestantes2',
        'registrosGuardadosProgSITMtmc','diasRestantes','listasolicitudes','matriculacliente','imagenCliente','aseguradoras',
        'estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS','registrosGuardadosProgramacionSIC',
        'registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas','permisoContinuidad','numeropoder',
        'apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado','programaciones','puedeEditarArchivo',
        'puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio','tramitecontinuidad',
        'inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto', 'personal',
        'registroInfoSITMEGS','registroInfoSITMTMC','registroInfoSITMTMR','registroInfoCS','registroInfoSICEGS',
        'registroInfoSICTMC','registroInfoSICTMR','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasprocinvalidez(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));


        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'INVALIDEZ')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
        ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
        ->get();

        /* $documentos = $programacionesRaw->merge($informes);

        foreach ($documentos as $doc) {
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            } else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 : 2);

                return [$ordenTipoarea, $item->areanombre];
            });
        }); */

        /* NUEVO 051225 */
        $subprogramaciones = DB::table('subprocedimientotramites')
            ->select(
                'tipo',
                DB::raw("NULL as fechabateria"),
                DB::raw("estudioespecialidad as areanombre"),
                'estudioespecialidad as accionnombre',
                'nombremedico as proveedornombre',
                'informeprogramacion as document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("CASE WHEN tipo LIKE 'PROGRAMACIONES%' THEN 'PROGRAMACIONES' ELSE 'INFORMES ADICIONALES' END as tipoarea"),
                'nombremedico as proveedor_real'
            )
            ->where('clienteid', $cliente->id)
            ->where(function($query) {
                $query->where('tipo', 'LIKE', 'PROGRAMACIONES%')
                    ->orWhere('tipo', 'LIKE', 'INFORMES ADICIONALES%');
            })
            ->whereNotNull('informeprogramacion')
        ->get();

        $documentos = $programacionesRaw
            ->merge($informes)
            ->merge($subprogramaciones);

        foreach ($documentos as $doc) {
            $tipoarea = strtoupper(trim($doc->tipoarea ?? ''));
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            }
            elseif (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                $path = public_path("tramitesclientesita/{$cliente->id}/INVALIDEZ/INFORMES/{$doc->document}");
            }
            else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy(function ($item) {
            $tipoarea = strtoupper($item->tipoarea ?? '');
            if (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                return $item->tipo;
            }
            return $item->fechabateria;
        })
        ->map(function ($grupo) {
            return $grupo->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';

                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 :
                    (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES']) ? 2 : 3));

                return [$ordenTipoarea, $item->areanombre, $item->accionnombre];
            })->values();
        });


        return view('admin.tramites.cartasprocinvalidez', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones', 'apoderados'));
    }
    public function actualizardatoscliente(UpdateClienteitaRequest $request, Cliente $cliente)
    {
        $clienteData = $request->validated();

        foreach ($clienteData as $campo => $nuevoValor) {
            $valorAnterior = $cliente->$campo;
            if ($valorAnterior != $nuevoValor) {
                ModificacionesDatos::create([
                    'tabla' => 'clientes',
                    'clienteid' => $cliente->id,
                    'clientenombre' => $cliente->nombrecompleto,
                    'columna' => $campo,
                    'datoantiguo' => $valorAnterior,
                    'datonuevo' => $nuevoValor,
                    'usuarioedicionid' => Auth::id(),
                    'usuarioedicionnombre' => Auth::user()->name,
                ]);
            }
        }

        $cliente->update($clienteData);

        return redirect()->route('admin.tramites.procinvalidez', $cliente)->with('info', 'Los datos del cliente se actualizó con éxito');
    }

    /* INICIO DE TRAMITES */
    public function guardariniciotramiteclienteita(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nivelprocedimiento' => 'required|in:INICIO DE TRÁMITE,CONTINUIDAD DE TRÁMITE',
            'clienteid' => '',
            'usuarioid' => '',
            'idtramite' => '',
        ]);
    
        $tramite = new Tramite();
        $tramite->idtramite = $request->idtramite;
        $tramite->clienteid = $request->clienteid;
        $tramite->usuarioid = $request->usuarioid;
        $tramite->usuarioregistro = $request->usuarioregistro;
        $tramite->clientenombre = $request->clientenombre;
        $tramite->apoderado = $request->apoderado;
        $tramite->tramite = $request->tramite;
        $tramite->nivelprocedimiento = $request->nivelprocedimiento;
        $tramite->subprocedimiento = $request->nivelprocedimiento;
        $tramite->fechasubida = $request->fechasubida;
        $tramite->tipo = 'PROCEDIMIENTO';
        $tramite->save();
    
        return redirect()->back()->with('info', 'Registro exitoso.');
    }
    public function guardartramitesclienteita(StoreTramiteRequest $request, Cliente $cliente)
    {
        if ($request->has('guardar_estado_dictamen')) {
            $documento = $cliente->tramites()
                ->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')
                ->latest()
                ->first();
    
            if ($documento) {
                $documento->estadodictamen = $request->estadodictamen;
                $documento->save();
    
                $previousUrl = url()->previous();
                if (Str::contains($previousUrl, 'procmasahereditaria')) {
                    return redirect()->route('admin.tramites.procmasahereditaria', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                } elseif (Str::contains($previousUrl, 'procinvalidez')) {
                    return redirect()->route('admin.tramites.procinvalidez', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                } elseif (Str::contains($previousUrl, 'procapelacion')) {
                    return redirect()->route('admin.tramites.procapelacion', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }elseif (Str::contains($previousUrl, 'proccompensacionsenasir')) {
                    return redirect()->route('admin.tramites.proccompensacionsenasir', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }elseif (Str::contains($previousUrl, 'procjubilacion')) {
                    return redirect()->route('admin.tramites.procjubilacion', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }elseif (Str::contains($previousUrl, 'procpensionmuerte')) {
                    return redirect()->route('admin.tramites.procpensionmuerte', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }elseif (Str::contains($previousUrl, 'procretiroaportesparcial')) {
                    return redirect()->route('admin.tramites.procretiroaportesparcial', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }elseif (Str::contains($previousUrl, 'procretiroaportestotal')) {
                    return redirect()->route('admin.tramites.procretiroaportestotal', $cliente)->with('info', 'El estado del dictamen se actualizó con éxitoo');
                }elseif (Str::contains($previousUrl, 'procsegundasolicitud')) {
                    return redirect()->route('admin.tramites.procsegundasolicitud', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }elseif (Str::contains($previousUrl, 'proctercerasolicitud')) {
                    return redirect()->route('admin.tramites.proctercerasolicitud', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }elseif (Str::contains($previousUrl, 'procrecalificacion')) {
                    return redirect()->route('admin.tramites.procrecalificacion', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }elseif (Str::contains($previousUrl, 'procapelsegsolicitud')) {
                    return redirect()->route('admin.tramites.procapelsegsolicitud', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }elseif (Str::contains($previousUrl, 'procapeltercersolicitud')) {
                    return redirect()->route('admin.tramites.procapeltercersolicitud', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }elseif (Str::contains($previousUrl, 'procapelrecalificacion')) {
                    return redirect()->route('admin.tramites.procapelrecalificacion', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }elseif (Str::contains($previousUrl, 'procrecalsegsolicitud')) {
                    return redirect()->route('admin.tramites.procrecalsegsolicitud', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }elseif (Str::contains($previousUrl, 'procapelrecalsegsolicitud')) {
                    return redirect()->route('admin.tramites.procapelrecalsegsolicitud', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }else {
                    return redirect()->route('admin.tramites.index')->with('info', 'El estado del dictamen se actualizó con éxito');
                }
            }
        }

        // MODIFICACION DE ARCHIVOS
        if ($request->input('accion') === 'reemplazarArchivo') {
            $request->validate([
                'archivo_reemplazo' => 'required',
                'tramite_reemplazo_id' => 'required',
            ]);

            $tramite = Tramite::findOrFail($request->tramite_reemplazo_id);
            $cliente_id = $tramite->clienteitaid;
            $usuarioId = auth()->user()->id;
            $carpetaCliente = public_path("/tramitesclientesita/{$cliente_id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            if ($tramite->document) {
                $rutaAnterior = $carpetaCliente . '/' . $tramite->document;
                if (file_exists($rutaAnterior)) {
                    unlink($rutaAnterior);
                }
            }
            $archivo = $request->file('archivo_reemplazo');
            $archivo_name = time() . '_' . $archivo->getClientOriginalName();
            $archivo->move($carpetaCliente, $archivo_name);
            $tramite->update([
                'document' => $archivo_name,
                'usuarioregistro' => $usuarioId,
                'updated_at' => now(),
            ]);
            return back()->with('info', 'Archivo reemplazado correctamente.');
        }

        $archivos = $request->file('archivo');
        $tramites = $request->input('tramite', []);
        $niveles = $request->input('nivelprocedimiento', []);

        if (is_array($archivos) && count($archivos) > 0) {
            foreach ($archivos as $key => $archivo) {
                if (!$archivo) continue;

                $nombreTramite = $tramites[$key] ?? 'SIN_TRAMITE';
                $nivel = $niveles[$key] ?? 'SIN_NIVEL';
                $carpetaCliente = public_path("/tramitesclientesita/{$request->clienteid}/{$nombreTramite}/{$nivel}");

                if (!file_exists($carpetaCliente)) mkdir($carpetaCliente, 0755, true);
                $archivo_name = time() . '_' . $archivo->getClientOriginalName();
                $archivo->move($carpetaCliente, $archivo_name);
                $archivo2 = $request->file('archivo2');
                $archivo2_name = null;
                if ($archivo2) {
                    $archivo2_name = time() . '_2_' . $archivo2->getClientOriginalName();
                    $archivo2->move($carpetaCliente, $archivo2_name);
                }
                
                //DATOS A INGRESAR
                    $tramite = $request->input('tramite', []);
                    $nivelprocedimiento = $request->input('nivelprocedimiento', []);
                    $subprocedimiento = $request->input('subprocedimiento', []);
                    $fechasubida = $request->input('fechasubida', []);
                    $horasubida = $request->input('horasubida', []);
                    $fechaviaje = $request->input('fechaviaje', []);
                    $horaviaje = $request->input('horaviaje', []);
                    $citenotificacion = $request->input('citenotificacion', []);
                    $fechacitenotificacion = $request->input('fechacitenotificacion', []);
                    $citenota = $request->input('citenota', []);
                    $fechacitenota = $request->input('fechacitenota', []);
                    $fecharetorno = $request->input('fecharetorno', []);
                    $fechainclusion = $request->input('fechainclusion', []);
                    $fechaestadotramite = $request->input('fechaestadotramite', []);
                    $tipodocumentacion = $request->input('tipodocumentacion', []);
                    $recojodocumentacion = $request->input('recojodocumentacion', []);
                    $motivonoseguro = $request->input('motivonoseguro', []);
                    $mescierre = $request->input('mescierre', []);
                    $tipodocumento = $request->input('tipodocumento', []);
                    $tipomedico = $request->input('tipomedico', []);
                    $nombremedico = $request->input('nombremedico', []);
                    $nombremedico2 = $request->input('nombremedico2', []);
                    $nombremedico3 = $request->input('nombremedico3', []);
                    $nombremedico4 = $request->input('nombremedico4', []);
                    $nombremedico5 = $request->input('nombremedico5', []);
                    $viaticos = $request->input('viaticos', []);
                    $decisionviaja = $request->input('decisionviaja', []);
                    $transporteviaja = $request->input('transporteviaja', []);
                    $estadotramite = $request->input('estadotramite', []);
                    $corsolicitud = $request->input('corsolicitud', []);
                    $opcioncorsolicitud = $request->input('opcioncorsolicitud', []);
                    $usuarioingreso = $request->input('usuarioingreso', []);
                    $seguro = $request->input('seguro', []);
                    $estadodictamen = $request->input('estadodictamen', []);
                    $porcentajeriesgodictamen = $request->input('porcentajeriesgodictamen', []);
                    $nrodictamen = $request->input('nrodictamen', []);
                    $nroformulario = $request->input('nroformulario', []);
                    $viaja = $request->input('viaja', []);
                    $accesopension = $request->input('accesopension', []);
                    $motivonopension = $request->input('motivonopension', []);
                    $departamentoviaja = $request->input('departamentoviaja', []);
                    $fechagestoradictamen = $request->input('fechagestoradictamen', []);
                    $fechasinestro = $request->input('fechasinestro', []);
                    $fechacobrocontrato = $request->input('fechacobrocontrato', []);
                    $montocontrato = $request->input('montocontrato', []);
                    $motivorechazo = $request->input('motivorechazo', []);
                    $notaseguimiento = $request->input('notaseguimiento', []);
                    $riesgodictamen = $request->input('riesgodictamen', []);
                    $tiporiesgodictamen = $request->input('tiporiesgodictamen', []);
                    $mescobro = $request->input('mescobro', []);
                //

                $conteo = Tramite::where('clienteid', $request->clienteid)
                    ->where('nivelprocedimiento', $nivelprocedimiento[$key] ?? null)
                    ->where('subprocedimiento', $subprocedimiento[$key] ?? null)
                    ->where('tramite', $tramite[$key] ?? null)
                    ->count();

                $nro = $conteo + 1;

                //NUEVO APP MOVIL
                $tramiteCreado =  Tramite::create([
                    'document' => $archivo_name,
                    'document2' => $archivo2_name,
                    'usuarioid' => $request->usuarioid,
                    'usuarioregistro' => $request->usuarioregistro,
                    'clienteid' => $request->clienteid,
                    'clientenombre' => $request->clientenombre,
                    'apoderado' => $request->apoderado,
                    'tramite' => $tramite[$key] ?? null,
                    'nivelprocedimiento' => $nivelprocedimiento[$key] ?? null,
                    'subprocedimiento' => $subprocedimiento[$key] ?? null,
                    'fechasubida' => $fechasubida[$key] ?? null,
                    'horasubida' => $horasubida[$key] ?? null,
                    'mescobro' => $mescobro[$key] ?? null,
                    'fechacitenotificacion' => $fechacitenotificacion[$key] ?? null,
                    'fechacitenota' => $fechacitenota[$key] ?? null,
                    'fecharetorno' => $fecharetorno[$key] ?? null,
                    'fechainclusion' => $fechainclusion[$key] ?? null,
                    'fechaestadotramite' => $fechaestadotramite[$key] ?? null,
                    'tipodocumentacion' => $tipodocumentacion[$key] ?? null,
                    'recojodocumentacion' => $recojodocumentacion[$key] ?? null,
                    'motivonoseguro' => $motivonoseguro[$key] ?? null,
                    'citenotificacion' => $citenotificacion[$key] ?? null,
                    'citenota' => $citenota[$key] ?? null,
                    'mescierre' => $mescierre[$key] ?? null,
                    'tipodocumento' => $tipodocumento[$key] ?? null,
                    'corsolicitud' => $corsolicitud[$key] ?? null,
                    'opcioncorsolicitud' => $opcioncorsolicitud[$key] ?? null,
                    'tipomedico' => $tipomedico[$key] ?? null,
                    'nombremedico' => $nombremedico[$key] ?? null,
                    'nombremedico2' => $nombremedico2[$key] ?? null,
                    'nombremedico3' => $nombremedico3[$key] ?? null,
                    'nombremedico4' => $nombremedico4[$key] ?? null,
                    'nombremedico5' => $nombremedico5[$key] ?? null,
                    'viaticos' => $viaticos[$key] ?? null,
                    'decisionviaja' => $decisionviaja[$key] ?? null,
                    'transporteviaja' => $transporteviaja[$key] ?? null,
                    'estadotramite' => $estadotramite[$key] ?? null,
                    'idtramite' => $request->idtramite,
                    'usuarioingreso' => $usuarioingreso[$key] ?? null,
                    'seguro' => $seguro[$key] ?? null,
                    'estadodictamen' => $estadodictamen[$key] ?? null,
                    'porcentajeriesgodictamen' => isset($porcentajeriesgodictamen[$key]) ? $porcentajeriesgodictamen[$key] . '%' : null,
                    'nrodictamen' => $nrodictamen[$key] ?? null,
                    'accesopension' => $accesopension[$key] ?? null,
                    'motivonopension' => $motivonopension[$key] ?? null,
                    'nroformulario' => $nroformulario[$key] ?? null,
                    'viaja' => $viaja[$key] ?? null,
                    'departamentoviaja' => $departamentoviaja[$key] ?? null,
                    'fechagestoradictamen' => $fechagestoradictamen[$key] ?? null,
                    'fechasinestro' => $fechasinestro[$key] ?? null,
                    'fechacobrocontrato' => $fechacobrocontrato[$key] ?? null,
                    'montocontrato' => $montocontrato[$key] ?? null,
                    'motivorechazo' => $motivorechazo[$key] ?? null,
                    'notaseguimiento' => $notaseguimiento[$key] ?? null,
                    'riesgodictamen' => $riesgodictamen[$key] ?? null,
                    'tiporiesgodictamen' => $tiporiesgodictamen[$key] ?? null,
                    'tipo' => 'PROCEDIMIENTO',
                    'nro' => $nro,
                ]);

                $departamentocliente = $cliente->sucursal;

                $ultimoTramite = Tramitesubcliente::where('clienteitaid', $request->clienteitaid)
                    ->orderBy('created_at', 'desc')
                ->first();

                $usuarioAsignado = $ultimoTramite ? $ultimoTramite->usuarioasignado : $request->usuarioasignado;

                if ($subprocedimiento[$key] === 'INICIO PROCESO DE APELACIÓN') {
                    $existe01 = Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'APELACIÓN'],
                    ])->exists();
                    if (!$existe01) {
                        Tramitesubcliente::create([
                            'usuarioid' => $request->usuarioid,
                            'usuarioregistro' => $request->usuarioregistro,
                            'clienteid' => $request->clienteid,
                            'clientenombre' => $request->clientenombre,
                            'usuarioasignado' => $usuarioAsignado,
                            'tramite' => 'APELACIÓN',
                            'ciudad' => $departamentocliente,
                            'estado' => 'PENDIENTE',
                            'observaciones' => '',
                        ]);
                    }
                }

                if ($nivelprocedimiento[$key] === 'CONTRATO' && $subprocedimiento[$key] === 'FIRMA DE CONTRATO') {
                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'MASA HEREDITARIA'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }

                if ($nivelprocedimiento[$key] === 'CONTRATO' && $subprocedimiento[$key] === 'NOTA DE RECHAZO DE TRÁMITEs') {
                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'MASA HEREDITARIA'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }

                if ($nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'INICIO PROCESO DE APELACIÓN') {
                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'INVALIDEZ'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }

                /* EN INVALIDEZ DERIVAR A APELACION SI EL CLIENTE RECHAZA EL DICTAMEN */
                if ($tramite[$key] === 'INVALIDEZ' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE DICTAMEN' && $estadodictamen[$key] === 'RECHAZADO') {
                    $existe = Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'APELACIÓN'],
                    ])->exists();
                    if (!$existe) {
                        Tramitesubcliente::create([
                            'usuarioid' => $request->usuarioid,
                            'usuarioregistro' => $request->usuarioregistro,
                            'clienteitaid' => $request->clienteid,
                            'clienteitanombre' => $request->clientenombre,
                            'usuarioasignado' => $usuarioAsignado,
                            'tramite' => 'APELACIÓN',
                            'ciudad' => $departamentocliente,
                            'estado' => 'PENDIENTE',
                            'observaciones' => null,
                        ]);
                    }

                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'INVALIDEZ'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }

                /* EN INVALIDEZ EN DICTAMEN FINALIZAR TRAMITE SI NO ACCEDE A TRAMITE */
                if ($tramite[$key] === 'INVALIDEZ' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'RECHAZO DE SOLICITUD DE PENSIÓN POR INVALIDEZ (FALTA DE COBERTURA)' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE COBERTURA' && $opcioncorsolicitud[$key] === 'NO' && $motivonopension[$key] === 'CLIENTE JUBILADO') {
                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'INVALIDEZ'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }

                /* EN INVALIDEZ EN DICTAMEN FINALIZAR TRAMITE SI ACCEDE A TRAMITE A JUBILACIÓN */
                if ($tramite[$key] === 'INVALIDEZ' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'RECHAZO DE SOLICITUD DE PENSIÓN POR INVALIDEZ (FALTA DE COBERTURA)' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE COBERTURA' && $opcioncorsolicitud[$key] === 'SI' && $motivonopension[$key] === 'JUBILACIÓN') {
                    $existe2 = Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'JUBILACIÓN'],
                    ])->exists();
                    if (!$existe2) {
                        Tramitesubcliente::create([
                            'usuarioid' => $request->usuarioid,
                            'usuarioregistro' => $request->usuarioregistro,
                            'clienteitaid' => $request->clienteid,
                            'clienteitanombre' => $request->clientenombre,
                            'usuarioasignado' => $usuarioAsignado,
                            'tramite' => 'JUBILACIÓN',
                            'ciudad' => $departamentocliente,
                            'estado' => 'PENDIENTE',
                            'observaciones' => null,
                        ]);
                    }
                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'INVALIDEZ'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }

                /* EN INVALIDEZ EN DICTAMEN FINALIZAR TRAMITE SI ACCEDE A TRAMITE A RETIRO DE APORTES PARCIAL */
                if ($tramite[$key] === 'INVALIDEZ' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'RECHAZO DE SOLICITUD DE PENSIÓN POR INVALIDEZ (FALTA DE COBERTURA)' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE COBERTURA' && $opcioncorsolicitud[$key] === 'SI' && $motivonopension[$key] === 'RETIRO DE APORTES PARCIAL') {
                    $existe3 = Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'RETIRO DE APORTES PARCIAL'],
                    ])->exists();
                    if (!$existe3) {
                        Tramitesubcliente::create([
                            'usuarioid' => $request->usuarioid,
                            'usuarioregistro' => $request->usuarioregistro,
                            'clienteitaid' => $request->clienteid,
                            'clienteitanombre' => $request->clientenombre,
                            'usuarioasignado' => $usuarioAsignado,
                            'tramite' => 'RETIRO DE APORTES PARCIAL',
                            'ciudad' => $departamentocliente,
                            'estado' => 'PENDIENTE',
                            'observaciones' => null,
                        ]);
                    }

                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'INVALIDEZ'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }

                /* EN INVALIDEZ EN DICTAMEN FINALIZAR TRAMITE SI ACCEDE A TRAMITE A RETIRO DE APORTES TOTAL */
                if ($tramite[$key] === 'INVALIDEZ' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'RECHAZO DE SOLICITUD DE PENSIÓN POR INVALIDEZ (FALTA DE COBERTURA)' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE COBERTURA' && $opcioncorsolicitud[$key] === 'SI' && $motivonopension[$key] === 'RETIRO DE APORTES TOTAL') {
                    $existe4 = Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'RETIRO DE APORTES TOTAL'],
                    ])->exists();
                    if (!$existe4) {
                        Tramitesubcliente::create([
                            'usuarioid' => $request->usuarioid,
                            'usuarioregistro' => $request->usuarioregistro,
                            'clienteitaid' => $request->clienteid,
                            'clienteitanombre' => $request->clientenombre,
                            'usuarioasignado' => $usuarioAsignado,
                            'tramite' => 'RETIRO DE APORTES TOTAL',
                            'ciudad' => $departamentocliente,
                            'estado' => 'PENDIENTE',
                            'observaciones' => null,
                        ]);
                    }

                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'INVALIDEZ'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }

                /* EN INVALIDEZ FINALIZAR TRAMITE SI SE DESISTE A CANCELACION DE TRAMITE */
                if ($tramite[$key] === 'INVALIDEZ' && $nivelprocedimiento[$key] === 'CANCELACIÓN' && $subprocedimiento[$key] === 'DESISTIMIENTO A CANCELACIÓN DE TRÁMITE') {
                    $existe5 = Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'SEGUNDA SOLICITUD'],
                    ])->exists();
                    if (!$existe5) {
                        Tramitesubcliente::create([
                            'usuarioid' => $request->usuarioid,
                            'usuarioregistro' => $request->usuarioregistro,
                            'clienteitaid' => $request->clienteid,
                            'clienteitanombre' => $request->clientenombre,
                            'usuarioasignado' => $usuarioAsignado,
                            'tramite' => 'SEGUNDA SOLICITUD',
                            'ciudad' => $departamentocliente,
                            'estado' => 'PENDIENTE',
                            'observaciones' => null,
                        ]);
                    }

                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'INVALIDEZ'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }

                /* EN INVALIDEZ FINALIZAR TRAMITE SI EL CLIENTE CANCELA EL TRAMITE */
                if ($tramite[$key] === 'INVALIDEZ' && $nivelprocedimiento[$key] === 'CANCELACIÓN' && $subprocedimiento[$key] === 'CANCELACIÓN DE TRAMITE'  && $usuarioingreso[$key] === $request->clientenombre) {
                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'INVALIDEZ'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }
                
                /* EN INVALIDEZ FINALIZAR TRAMITE SI EL CLIENTE NO ACCEDE A PENSIÓN Y ELIJE FINALIZAR TRAMITE */
                if ($tramite[$key] === 'INVALIDEZ' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE DICTAMEN' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE PORCENTAJE' && $opcioncorsolicitud[$key] === 'FINALIZAR TRÁMITE') {
                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'INVALIDEZ'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }
                /* EN INVALIDEZ DERIVAR A APELACION Y FINALIZAR TRAMITE SI EL CLIENTE NO ACCEDE A PENSIÓN Y ELIJE DERIVAR A APELACIÓN */
                if ($tramite[$key] === 'INVALIDEZ' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE DICTAMEN' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE PORCENTAJE' && $opcioncorsolicitud[$key] === 'SE DERIVÓ A APELACIÓN') {
                    $existe6 = Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'APELACIÓN'],
                    ])->exists();
                    if (!$existe6) {
                        Tramitesubcliente::create([
                            'usuarioid' => $request->usuarioid,
                            'usuarioregistro' => $request->usuarioregistro,
                            'clienteitaid' => $request->clienteid,
                            'clienteitanombre' => $request->clientenombre,
                            'usuarioasignado' => $usuarioAsignado,
                            'tramite' => 'APELACIÓN',
                            'ciudad' => $departamentocliente,
                            'estado' => 'PENDIENTE',
                            'observaciones' => null,
                        ]);
                    }

                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'INVALIDEZ'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }


                /* EN APELACIÓN FINALIZAR TRAMITE SI EL CLIENTE NO ACCEDE A PENSIÓN Y ELIJE FINALIZAR TRAMITE */
                if ($tramite[$key] === 'APELACIÓN' && $nivelprocedimiento[$key] === 'RESOLUCIÓN ADMINISTRATIVA' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE RESOLUCIÓN ADMINISTRATIVA' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE PORCENTAJE' && $opcioncorsolicitud[$key] === 'FINALIZAR TRÁMITE') {
                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'APELACIÓN'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }
                /* EN APELACIÓN DERIVAR A SEGUNDA SOLICITUD Y FINALIZAR TRAMITE SI EL CLIENTE NO ACCEDE A PENSIÓN Y ELIJE DERIVAR A APELACIÓN */
                if ($tramite[$key] === 'APELACIÓN' && $nivelprocedimiento[$key] === 'RESOLUCIÓN ADMINISTRATIVA' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE RESOLUCIÓN ADMINISTRATIVA' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE PORCENTAJE' && $opcioncorsolicitud[$key] === 'SE DERIVÓ A SEGUNDA SOLICITUD') {
                    $existe7 = Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'SEGUNDA SOLICITUD'],
                    ])->exists();
                    if (!$existe7) {
                        Tramitesubcliente::create([
                            'usuarioid' => $request->usuarioid,
                            'usuarioregistro' => $request->usuarioregistro,
                            'clienteitaid' => $request->clienteid,
                            'clienteitanombre' => $request->clientenombre,
                            'usuarioasignado' => $usuarioAsignado,
                            'tramite' => 'SEGUNDA SOLICITUD',
                            'ciudad' => $departamentocliente,
                            'estado' => 'PENDIENTE',
                            'observaciones' => null,
                        ]);
                    }

                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'APELACIÓN'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }

                /* EN SEGUNDA SOLICITUD FINALIZAR TRAMITE SI EL CLIENTE NO ACCEDE A PENSIÓN Y ELIJE FINALIZAR TRAMITE */
                if ($tramite[$key] === 'SEGUNDA SOLICITUD' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE DICTAMEN' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE PORCENTAJE' && $opcioncorsolicitud[$key] === 'FINALIZAR TRÁMITE') {
                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'SEGUNDA SOLICITUD'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }
                /* EN SEGUNDA SOLICITUD DERIVAR A TERCERA SOLICITUD Y FINALIZAR TRAMITE SI EL CLIENTE NO ACCEDE A PENSIÓN Y ELIJE DERIVAR A APELACIÓN SEGUNDA SOLICITUD */
                if ($tramite[$key] === 'SEGUNDA SOLICITUD' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE DICTAMEN' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE PORCENTAJE' && $opcioncorsolicitud[$key] === 'SE DERIVÓ A APELACIÓN SEGUNDA SOLICITUD') {
                    $existe8 = Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'APELACIÓN SEGUNDA SOLICITUD'],
                    ])->exists();
                    if (!$existe8) {
                        Tramitesubcliente::create([
                            'usuarioid' => $request->usuarioid,
                            'usuarioregistro' => $request->usuarioregistro,
                            'clienteitaid' => $request->clienteid,
                            'clienteitanombre' => $request->clientenombre,
                            'usuarioasignado' => $usuarioAsignado,
                            'tramite' => 'APELACIÓN SEGUNDA SOLICITUD',
                            'ciudad' => $departamentocliente,
                            'estado' => 'PENDIENTE',
                            'observaciones' => null,
                        ]);
                    }

                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'SEGUNDA SOLICITUD'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }
                /* EN APELACIÓN SEGUNDA SOLICITUD FINALIZAR TRAMITE SI EL CLIENTE NO ACCEDE A PENSIÓN Y ELIJE FINALIZAR TRAMITE */
                if ($tramite[$key] === 'APELACIÓN SEGUNDA SOLICITUD' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE DICTAMEN' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE PORCENTAJE' && $opcioncorsolicitud[$key] === 'FINALIZAR TRÁMITE') {
                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'APELACIÓN SEGUNDA SOLICITUD'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }
                /* EN APELACIÓN SEGUNDA SOLICITUD DERIVAR A TERCERA SOLICITUD Y FINALIZAR TRAMITE SI EL CLIENTE NO ACCEDE A PENSIÓN Y ELIJE DERIVAR A TERCERA SOLICITUD */
                if ($tramite[$key] === 'APELACIÓN SEGUNDA SOLICITUD' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE DICTAMEN' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE PORCENTAJE' && $opcioncorsolicitud[$key] === 'SE DERIVÓ A TERCERA SOLICITUD') {
                    $existe9 = Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'TERCERA SOLICITUD'],
                    ])->exists();
                    if (!$existe9) {
                        Tramitesubcliente::create([
                            'usuarioid' => $request->usuarioid,
                            'usuarioregistro' => $request->usuarioregistro,
                            'clienteitaid' => $request->clienteid,
                            'clienteitanombre' => $request->clientenombre,
                            'usuarioasignado' => $usuarioAsignado,
                            'tramite' => 'TERCERA SOLICITUD',
                            'ciudad' => $departamentocliente,
                            'estado' => 'PENDIENTE',
                            'observaciones' => null,
                        ]);
                    }

                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'APELACIÓN SEGUNDA SOLICITUD'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }
                /* EN TERCERA SOLICITUD FINALIZAR TRAMITE SI EL CLIENTE NO ACCEDE A PENSIÓN Y ELIJE FINALIZAR TRAMITE */
                if ($tramite[$key] === 'TERCERA SOLICITUD' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE DICTAMEN' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE PORCENTAJE' && $opcioncorsolicitud[$key] === 'FINALIZAR TRÁMITE') {
                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'TERCERA SOLICITUD'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }
                /* EN TERCERA SOLICITUD DERIVAR A APELACIÓN TERCERA SOLICITUD Y FINALIZAR TRAMITE SI EL CLIENTE NO ACCEDE A PENSIÓN Y ELIJE DERIVAR A APELACIÓN TERCERA SOLICITUD */
                if ($tramite[$key] === 'TERCERA SOLICITUD' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE DICTAMEN' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE PORCENTAJE' && $opcioncorsolicitud[$key] === 'SE DERIVÓ A APELACIÓN TERCERA SOLICITUD') {
                    $existe10 = Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'APELACIÓN TERCERA SOLICITUD'],
                    ])->exists();
                    if (!$existe10) {
                        Tramitesubcliente::create([
                            'usuarioid' => $request->usuarioid,
                            'usuarioregistro' => $request->usuarioregistro,
                            'clienteitaid' => $request->clienteid,
                            'clienteitanombre' => $request->clientenombre,
                            'usuarioasignado' => $usuarioAsignado,
                            'tramite' => 'APELACIÓN TERCERA SOLICITUD',
                            'ciudad' => $departamentocliente,
                            'estado' => 'PENDIENTE',
                            'observaciones' => null,
                        ]);
                    }

                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'TERCERA SOLICITUD'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }
                /* EN APELACIÓN TERCERA SOLICITUD FINALIZAR TRAMITE SI EL CLIENTE NO ACCEDE A PENSIÓN Y ELIJE FINALIZAR TRAMITE */
                if ($tramite[$key] === 'APELACIÓN TERCERA SOLICITUD' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE DICTAMEN' 
                && $accesopension[$key] === 'NO' && $motivonopension[$key] === 'FALTA DE PORCENTAJE' && $opcioncorsolicitud[$key] === 'FINALIZAR TRÁMITE') {
                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'APELACIÓN TERCERA SOLICITUD'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }


                /* EN PENSIÓN POR MUERTE DERIVAR A APELACION SI EL CLIENTE RECHAZA EL DICTAMEN */
                if ($tramite[$key] === 'PENSIÓN POR MUERTE' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE DICTAMEN' && $estadodictamen[$key] === 'RECHAZADO') {
                    $existe11 = Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'APELACIÓN'],
                    ])->exists();
                    if (!$existe11) {
                        Tramitesubcliente::create([
                            'usuarioid' => $request->usuarioid,
                            'usuarioregistro' => $request->usuarioregistro,
                            'clienteitaid' => $request->clienteid,
                            'clienteitanombre' => $request->clientenombre,
                            'usuarioasignado' => $usuarioAsignado,
                            'tramite' => 'APELACIÓN',
                            'ciudad' => $departamentocliente,
                            'estado' => 'PENDIENTE',
                            'observaciones' => null,
                        ]);
                    }

                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'PENSIÓN POR MUERTE'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }
                /* EN SEGUNDA SOLICITUD EN DICTAMEN FINALIZAR TRAMITE Y DERIVAR A APELACIÓN SI NO ACCEDE A PENSIÓN */
                if ($tramite[$key] === 'SEGUNDA SOLICITUD' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE DICTAMEN' && $accesopension[$key] === 'NO' ) {
                    $existe12 = Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'APELACIÓN'],
                    ])->exists();
                    if (!$existe12) {
                        Tramitesubcliente::create([
                            'usuarioid' => $request->usuarioid,
                            'usuarioregistro' => $request->usuarioregistro,
                            'clienteitaid' => $request->clienteid,
                            'clienteitanombre' => $request->clientenombre,
                            'usuarioasignado' => $usuarioAsignado,
                            'tramite' => 'APELACIÓN',
                            'ciudad' => $departamentocliente,
                            'estado' => 'PENDIENTE',
                            'observaciones' => null,
                        ]);
                    }

                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'SEGUNDA SOLICITUD'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }
                /* EN TERCERA SOLICITUD EN DICTAMEN FINALIZAR TRAMITE Y DERIVAR A APELACIÓN SI NO ACCEDE A PENSIÓN */
                if ($tramite[$key] === 'TERCERA SOLICITUD' && $nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'NOTIFICACIÓN DE DICTAMEN' && $accesopension[$key] === 'NO' ) {
                    $existe13 = Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'APELACIÓN'],
                    ])->exists();
                    if (!$existe13) {
                        Tramitesubcliente::create([
                            'usuarioid' => $request->usuarioid,
                            'usuarioregistro' => $request->usuarioregistro,
                            'clienteitaid' => $request->clienteid,
                            'clienteitanombre' => $request->clientenombre,
                            'usuarioasignado' => $usuarioAsignado,
                            'tramite' => 'APELACIÓN',
                            'ciudad' => $departamentocliente,
                            'estado' => 'PENDIENTE',
                            'observaciones' => null,
                        ]);
                    }
                    
                    Tramitesubcliente::where([
                        ['clienteitaid', $request->clienteid],
                        ['tramite', 'TERCERA SOLICITUD'],
                        ['estado', 'PENDIENTE']
                    ])->update(['estado' => 'FINALIZADO']);
                }
            }
        }

        $razonsocialempleador = $request->input('razonsocialempleador', []);
        $periodos = $request->input('periodo', []);
        $observacion = $request->input('observacion', []);
        $usuarioAutenticadoid = Auth::user()->id;
        $usuarioAutenticadonombre = Auth::user()->name;
        $idtramitecliente = $request->input('idtramite');
        $apoderadoAsignado = $request->input('apoderado');
        $nombreTramite = $request->input('tramitenombreprog');

        for ($i = 0; $i < count($razonsocialempleador); $i++) {
            $periodosEmpleador = $periodos[$i] ?? [];

            if (!is_array($periodosEmpleador) || count($periodosEmpleador) === 0) {
                continue;
            }

            foreach ($periodosEmpleador as $periodoIndividual) {
                SubTramite::create([
                    'razonsocialempleador' => $razonsocialempleador[$i] ?? null,
                    'periodo' => $periodoIndividual,
                    'observacion' => $observacion[$i] ?? null,
                    'clienteid' => $cliente->id,
                    'clientenombre' => $cliente->nombrecompleto,
                    'tramite' => $nombreTramite,
                    'idtramite' => $idtramitecliente,
                    'tipo' => 'OBSERVACIONES FIRMA EAP',
                    'usuarioregistroid' => $usuarioAutenticadoid,
                    'usuarioregistronombre' => $usuarioAutenticadonombre,
                    'apoderado' => $apoderadoAsignado,
                ]);
            }
        }

        $estudioespecialidad = $request->input('estudioespecialidad', $request->input('estudioespecialidad2', $request->input('estudioespecialidad3', $request->input('estudioespecialidad4', $request->input('estudioespecialidad6', $request->input('estudioespecialidad7', $request->input('estudioespecialidad8', [])))))));
        $fechaprogramacion = $request->input('fechaprogramacion', $request->input('fechaprogramacion2', $request->input('fechaprogramacion3', $request->input('fechaprogramacion4', $request->input('fechaprogramacion6', $request->input('fechaprogramacion7', $request->input('fechaprogramacion8', [])))))));
        $horaprogramacion = $request->input('horaprogramacion', $request->input('horaprogramacion2', $request->input('horaprogramacion3', $request->input('horaprogramacion4', $request->input('horaprogramacion6', $request->input('horaprogramacion7', $request->input('horaprogramacion8', [])))))));
        $subtramite_ids = $request->input('subtramite_id', $request->input('subtramite_id2', $request->input('subtramite_id3', $request->input('subtramite_id4', $request->input('subtramite_id6', $request->input('subtramite_id7', $request->input('subtramite_id8', [])))))));
        $nombremedicoprog = $request->input('nombremedicoprog', $request->input('nombremedicoprog2', $request->input('nombremedicoprog3', $request->input('nombremedicoprog4', $request->input('nombremedicoprog6', $request->input('nombremedicoprog7', $request->input('nombremedicoprog8', [])))))));
        $medicoderivador = $request->input('medicoderivador', $request->input('medicoderivador2', $request->input('medicoderivador3', $request->input('medicoderivador4', $request->input('medicoderivador6', $request->input('medicoderivador7', $request->input('medicoderivador8', [])))))));
        $asistencias = $request->input('asistenciaprogramacion', $request->input('asistenciaprogramacion2', $request->input('asistenciaprogramacion3', $request->input('asistenciaprogramacion4', $request->input('asistenciaprogramacion6', $request->input('asistenciaprogramacion7', $request->input('asistenciaprogramacion8', [])))))));
        $opcionatencion = $request->input('opcionatencion', $request->input('opcionatencion2', $request->input('opcionatencion3', $request->input('opcionatencion4', $request->input('opcionatencion6', $request->input('opcionatencion7', $request->input('opcionatencion8', [])))))));
        $ordenes = $request->file('ordenprogramacion', $request->file('ordenprogramacion2', $request->file('ordenprogramacion3', $request->file('ordenprogramacion4', $request->file('ordenprogramacion6', $request->file('ordenprogramacion7', $request->file('ordenprogramacion8', [])))))));
        $informes = $request->file('informeprogramacion', $request->file('informeprogramacion2', $request->file('informeprogramacion3', $request->file('informeprogramacion4', $request->file('informeprogramacion6', $request->file('informeprogramacion7', $request->file('informeprogramacion8', [])))))));
        $fechareprogramacion = $request->input('fechareprogramacion', $request->input('fechareprogramacion2', $request->input('fechareprogramacion3', $request->input('fechareprogramacion4', $request->input('fechareprogramacion6', $request->input('fechareprogramacion7', $request->input('fechareprogramacion8', [])))))));
        $horareprogramacion = $request->input('horareprogramacion', $request->input('horareprogramacion2', $request->input('horareprogramacion3', $request->input('horareprogramacion4', $request->input('horareprogramacion6', $request->input('horareprogramacion7', $request->input('horareprogramacion8', [])))))));
        $motivoreprogramacion = $request->input('motivoreprogramacion', $request->input('motivoreprogramacion2', $request->input('motivoreprogramacion3', $request->input('motivoreprogramacion4', $request->input('motivoreprogramacion6', $request->input('motivoreprogramacion7', $request->input('motivoreprogramacion8', [])))))));
        $observacionprog = $request->input('observacion', $request->input('observacion2', $request->input('observacion3', $request->input('observacion4', $request->input('observacion6', $request->input('observacion7', $request->input('observacion8', [])))))));

        if ($request->has('estudioespecialidad3')) {
            $tipo = 'PROGRAMACIONES COMPRA DE SERVICIOS';
        } elseif ($request->has('estudioespecialidad2')) {
            $tipo = 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD';
        } elseif ($request->has('estudioespecialidad6')) {
            $tipo = 'PROGRAMACIONES SIC NOTIFICACIÓN TMC';
        } elseif ($request->has('estudioespecialidad4')) {
            $tipo = 'PROGRAMACIONES SITM NOTIFICACIÓN TMC';
        } elseif ($request->has('estudioespecialidad7')) {
            $tipo = 'PROGRAMACIONES SIC NOTIFICACIÓN TMR';
        } elseif ($request->has('estudioespecialidad8')) {
            $tipo = 'PROGRAMACIONES SITM NOTIFICACIÓN TMR';
        }elseif ($request->has('estudioespecialidad5')) {
            $tipo = 'SRD - ADJUNTO DOCUMENTACIÓN MÉDICA';
        } else {
            $tipo = 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD';
        }

        /* NUEVO 141125 */
        $debeNotificar = false;
        $solicitudParaNotificar = null;

        for ($i = 0; $i < count($estudioespecialidad); $i++) {
            $id = $subtramite_ids[$i] ?? null;
            $asistio = in_array($id, $asistencias) ? 1 : 0;

            // Archivos
            $archivo_name = null;
            $archivo2_name = null;

            // Buscar archivos usando ID
            if ($id && $request->hasFile("ordenprogramacion.$id")) {
                $archivo = $request->file("ordenprogramacion.$id");
                $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$nombreTramite}/ORDENES");
                if (!file_exists($carpetaCliente)) mkdir($carpetaCliente, 0755, true);

                $archivo_name = time() . '_' . $archivo->getClientOriginalName();
                $archivo->move($carpetaCliente, $archivo_name);
            }

            if ($id && $request->hasFile("informeprogramacion.$id")) {
                $archivo2 = $request->file("informeprogramacion.$id");
                $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$nombreTramite}/INFORMES");
                if (!file_exists($carpetaCliente)) mkdir($carpetaCliente, 0755, true);

                $archivo2_name = time() . '_' . $archivo2->getClientOriginalName();
                $archivo2->move($carpetaCliente, $archivo2_name);
            }

            if ($id && $registro = SubTramite::find($id)) {
                $registro->update([
                    'fechaprogramacion' => $fechaprogramacion[$i] ?? $registro->fechaprogramacion,
                    'horaprogramacion' => $horaprogramacion[$i] ?? $registro->horaprogramacion,
                    'nombremedico' => $nombremedicoprog[$i] ?? $registro->nombremedico,
                    'medicoderivador' => $medicoderivador[$i] ?? $registro->medicoderivador,
                    'asistenciaprogramacion' => $asistio,
                    'ordenprogramacion' => $archivo_name ?? $registro->ordenprogramacion,
                    'informeprogramacion' => $archivo2_name ?? $registro->informeprogramacion,
                    'opcionatencion' => $opcionatencion[$i] ?? $registro->opcionatencion,
                    'fechareprogramacion' => $fechareprogramacion[$id] ?? $registro->fechareprogramacion,
                    'horareprogramacion' => $horareprogramacion[$id] ?? $registro->horareprogramacion,
                    'motivoreprogramacion' => $motivoreprogramacion[$id] ?? $registro->motivoreprogramacion,
                    'observacion' => $observacionprog[$i] ?? $registro->observacion,
                ]);
                /* NUEVO 141125 */
                if (($opcionatencion[$i] ?? null) === 'DERIVACIÓN A PROGRAMACIÓN') {
                    $debeNotificar = true;
                    $solicitudParaNotificar = $registro;
                }

            } else {
                SubTramite::create([
                    'clienteid' => $cliente->id,
                    'clientenombre' => $cliente->nombrecompleto,
                    'tramite' => $nombreTramite,
                    'idtramite' => $idtramitecliente,
                    'apoderado' => $apoderadoAsignado,
                    'tipo' => $tipo,
                    'estudioespecialidad' => $estudioespecialidad[$i] ?? null,
                    'fechaprogramacion' => $fechaprogramacion[$i] ?? null,
                    'horaprogramacion' => $horaprogramacion[$i] ?? null,
                    'nombremedico' => $nombremedicoprog[$i] ?? null,
                    'medicoderivador' => $medicoderivador[$i] ?? null,
                    'opcionatencion' => $tipo === 'PROGRAMACIONES COMPRA DE SERVICIOS' ? 'COMPRA DE SERVICIOS' : ($opcionatencion[$i] ?? null),
                    'asistenciaprogramacion' => $asistio,
                    'ordenprogramacion' => $archivo_name,
                    'informeprogramacion' => $archivo2_name,
                    'usuarioregistroid' => $usuarioAutenticadoid,
                    'usuarioregistronombre' => $usuarioAutenticadonombre,
                    'fechareprogramacion' => $fechareprogramacion[$i] ?? null,
                    'horareprogramacion' => $horareprogramacion[$i] ?? null,
                    'motivoreprogramacion' => $motivoreprogramacion[$i] ?? null,
                    'observacion' => $observacionprog[$i] ?? null,
                ]);
            }
        }
        /* NUEVO 141125 */
        if ($debeNotificar && $solicitudParaNotificar) {
            $usuariosNotificar = User::role(['OPERATIVO'])
                ->where('sucursal', $cliente->sucursal)
                ->get();
            foreach ($usuariosNotificar as $usuarioDestino) {
                $usuarioDestino->notify(new SolicitarCreacionBateria($solicitudParaNotificar));
            }
        }

        /* NUEVO 081125 */
        $estesp = $request->input('1estudioespecialidad', $request->input('2estudioespecialidad', $request->input('3estudioespecialidad', $request->input('4estudioespecialidad', $request->input('5estudioespecialidad', [])))));
        $espcentromedico = $request->input('1nombremedicoprog', $request->input('2nombremedicoprog', $request->input('3nombremedicoprog', $request->input('4nombremedicoprog', $request->input('5nombremedicoprog', [])))));
        $fechaemision = $request->input('1fechaprogramacion', $request->input('2fechaprogramacion', $request->input('3fechaprogramacion', $request->input('4fechaprogramacion', $request->input('5fechaprogramacion', [])))));
        $informeadic = $request->file('1informeprogramacion', $request->file('2informeprogramacion', $request->file('3informeprogramacion', $request->file('4informeprogramacion', $request->file('5informeprogramacion', [])))));
        
        if ($request->has('1estudioespecialidad')) {
            $tipo = 'INFORMES ADICIONALES - SITM ENTE GESTOR DE SALUD';
        } elseif ($request->has('2estudioespecialidad')) {
            $tipo = 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMC';
        } elseif ($request->has('3estudioespecialidad')) {
            $tipo = 'INFORMES ADICIONALES - SIC ENTE GESTOR DE SALUD';
        } elseif ($request->has('4estudioespecialidad')) {
            $tipo = 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMC';
        } elseif ($request->has('5estudioespecialidad')) {
            $tipo = 'INFORMES ADICIONALES - COMPRA DE SERVICIOS';
        } elseif ($request->has('6estudioespecialidad')) {
            $tipo = 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMR';
        } elseif ($request->has('7estudioespecialidad')) {
            $tipo = 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMR';
        } else {
            $tipo = 'NO DEFINIDO';
        }

        if (!empty($estesp)) {
            for ($i = 0; $i < count($estesp); $i++) {
                if (empty($estesp[$i])) continue;
                $archivo2_name = null;

                if (isset($informeadic[$i]) && $informeadic[$i] instanceof \Illuminate\Http\UploadedFile) {
                    $archivo2 = $informeadic[$i];

                    $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$nombreTramite}/INFORMES");
                    if (!file_exists($carpetaCliente)) {
                        mkdir($carpetaCliente, 0755, true);
                    }
                    $archivo2_name = time() . '_' . $archivo2->getClientOriginalName();
                    $archivo2->move($carpetaCliente, $archivo2_name);
                }
                SubTramite::create([
                    'clienteid' => $cliente->id,
                    'clientenombre' => $cliente->nombrecompleto,
                    'tramite' => $nombreTramite,
                    'idtramite' => $idtramitecliente,
                    'apoderado' => $apoderadoAsignado,
                    'tipo' => $tipo,
                    'estudioespecialidad' => $estesp[$i] ?? null,
                    'fechaprogramacion' => $fechaemision[$i] ?? null,
                    'nombremedico' => $espcentromedico[$i] ?? null,
                    'informeprogramacion' => $archivo2_name,
                    'usuarioregistroid' => $usuarioAutenticadoid,
                    'usuarioregistronombre' => $usuarioAutenticadonombre,
                ]);
            }
        }


        
        // Procesar informes de SRD - ADJUNTO DOCUMENTACIÓN MÉDICA
        if ($request->hasFile('informeprogramacion5')) {
            $estudios5 = $request->input('estudioespecialidad5', []);
            $informes5 = $request->file('informeprogramacion5', []);

            foreach ($informes5 as $key => $archivo5) {
                if ($archivo5) {
                    $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$nombreTramite}/SRD - ADJUNTO DOCUMENTACIÓN MÉDICA");
                    if (!file_exists($carpetaCliente)) mkdir($carpetaCliente, 0755, true);

                    $archivo5_name = time() . '_' . $archivo5->getClientOriginalName();
                    $archivo5->move($carpetaCliente, $archivo5_name);

                    SubTramite::create([
                        'clienteid' => $cliente->id,
                        'clientenombre' => $cliente->nombrecompleto,
                        'tramite' => $nombreTramite,
                        'idtramite' => $idtramitecliente,
                        'apoderado' => $apoderadoAsignado,
                        'tipo' => 'SRD - ADJUNTO DOCUMENTACIÓN MÉDICA',
                        'estudioespecialidad' => $estudios5[$key] ?? null,
                        'informeprogramacion' => $archivo5_name,
                        'usuarioregistroid' => $usuarioAutenticadoid,
                        'usuarioregistronombre' => $usuarioAutenticadonombre,
                    ]);
                }
            }
        }

        // Procesar informes de RSRD - ADJUNTO DOCUMENTACIÓN MÉDICA
        if ($request->hasFile('informeprogramacion6')) {
            $estudios5 = $request->input('estudioespecialidad6', []);
            $informes5 = $request->file('informeprogramacion6', []);

            foreach ($informes5 as $key => $archivo5) {
                if ($archivo5) {
                    $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$nombreTramite}/RSRD - ADJUNTO DOCUMENTACIÓN MÉDICA");
                    if (!file_exists($carpetaCliente)) mkdir($carpetaCliente, 0755, true);

                    $archivo5_name = time() . '_' . $archivo5->getClientOriginalName();
                    $archivo5->move($carpetaCliente, $archivo5_name);

                    SubTramite::create([
                        'clienteid' => $cliente->id,
                        'clientenombre' => $cliente->nombrecompleto,
                        'tramite' => $nombreTramite,
                        'idtramite' => $idtramitecliente,
                        'apoderado' => $apoderadoAsignado,
                        'tipo' => 'RSRD - ADJUNTO DOCUMENTACIÓN MÉDICA',
                        'estudioespecialidad' => $estudios5[$key] ?? null,
                        'informeprogramacion' => $archivo5_name,
                        'usuarioregistroid' => $usuarioAutenticadoid,
                        'usuarioregistronombre' => $usuarioAutenticadonombre,
                    ]);
                }
            }
        }


        /* NUEVO 281025 */
        $especialistasit = $request->input('especialistait', []);
        $documentosit   = $request->input('documentoit', []);
        $imagesit       = $request->input('imageit', []);
        $images2it      = $request->input('image2it', []);

        if (!empty($especialistasit)) {
            foreach ($especialistasit as $key => $accionNombre) {
                $documentoNombre = $documentosit[$key] ?? null;
                $imageNombre     = $imagesit[$key] ?? null;
                $image2Nombre    = $images2it[$key] ?? null;

                if ($documentoNombre) {
                    SubTramite::create([
                        'clienteid' => $cliente->id,
                        'clientenombre' => $cliente->nombrecompleto,
                        'tramite' => $nombreTramite,
                        'idtramite' => $idtramitecliente,
                        'apoderado' => $apoderadoAsignado,
                        'tipo' => 'IT - ADJUNTO DOCUMENTACIÓN MÉDICA',
                        'estudioespecialidad' => $accionNombre,
                        'informeprogramacion' => $documentoNombre,
                        'solicitante1' => $imageNombre,
                        'solicitante2' => $image2Nombre,
                        'usuarioregistroid' => $usuarioAutenticadoid,
                        'usuarioregistronombre' => $usuarioAutenticadonombre,
                    ]);
                }
            }
        }

        /* NUEVO 281125 */
        $notreq = $request->input('notreq1', []);
        if (!empty($notreq)) {
            foreach ($notreq as $key => $especialidad) {
                if ($especialidad) {
                    SubTramite::create([
                        'clienteid' => $cliente->id,
                        'clientenombre' => $cliente->nombrecompleto,
                        'tramite' => $nombreTramite,
                        'idtramite' => $idtramitecliente,
                        'apoderado' => $apoderadoAsignado,
                        'tipo' => 'NR - SITM ENTE GESTOR DE SALUD',
                        'estudioespecialidad' => $especialidad,
                        'usuarioregistroid' => $usuarioAutenticadoid,
                        'usuarioregistronombre' => $usuarioAutenticadonombre,
                    ]);
                }
            }
        }

        
        // NUEVO APP MOVIL
        if ($tramiteCreado) {

            $userReferenciador = User::where('clienteid', $request->clienteid)->first();

            if ($userReferenciador) {

                // Notificación BD
                $userReferenciador->notify(
                    new AvanceTramiteNotification(
                        $request->clienteid,
                        $nombreTramite
                    )
                );

                // Push Firebase
                if (!empty($userReferenciador->fcm_token)) {
                    FirebaseService::sendNotification(
                        $userReferenciador->fcm_token,
                        'Nuevo Proceso',
                        'Se ha subido un nuevo proceso de su trámite en curso'
                    );
                }
            }
        }


        $previousUrl = url()->previous();
        if (Str::contains($previousUrl, 'procmasahereditaria')) {
            return redirect()->route('admin.tramites.procmasahereditaria', $cliente)->with('info', 'El registro se guardó con éxito');
        } elseif (Str::contains($previousUrl, 'procinvalidez')) {
            return redirect()->route('admin.tramites.procinvalidez', $cliente)->with('info', 'El registro se guardó con éxito');
        } elseif (Str::contains($previousUrl, 'procapelacion')) {
            return redirect()->route('admin.tramites.procapelacion', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'proccompensacionsenasir')) {
            return redirect()->route('admin.tramites.proccompensacionsenasir', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'procjubilacion')) {
            return redirect()->route('admin.tramites.procjubilacion', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'procpensionmuerte')) {
            return redirect()->route('admin.tramites.procpensionmuerte', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'procretiroaportesparcial')) {
            return redirect()->route('admin.tramites.procretiroaportesparcial', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'procretiroaportestotal')) {
            return redirect()->route('admin.tramites.procretiroaportestotal', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'procsegundasolicitud')) {
            return redirect()->route('admin.tramites.procsegundasolicitud', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'proctercerasolicitud')) {
            return redirect()->route('admin.tramites.proctercerasolicitud', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'procrecalificacion')) {
            return redirect()->route('admin.tramites.procrecalificacion', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'procapelsegsolicitud')) {
            return redirect()->route('admin.tramites.procapelsegsolicitud', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'procapeltercersolicitud')) {
            return redirect()->route('admin.tramites.procapeltercersolicitud', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'procapelrecalificacion')) {
            return redirect()->route('admin.tramites.procapelrecalificacion', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'procrecalsegsolicitud')) {
            return redirect()->route('admin.tramites.procrecalsegsolicitud', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'procapelrecalsegsolicitud')) {
            return redirect()->route('admin.tramites.procapelrecalsegsolicitud', $cliente)->with('info', 'El registro se guardó con éxito');
        }else {
            return redirect()->route('admin.tramites.index')->with('info', 'El registro se guardó con éxito');
        }
    }

    /* public function actualizarCamposDictamen(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:procedimientotramites,id',
            'estadodictamen' => 'nullable|string',
            'opcioncorsolicitud' => 'nullable|string',
        ]);

        $registro = Tramite::find($request->id);
        $registro->estadodictamen = $request->estadodictamen ?? $registro->estadodictamen;
        $registro->opcioncorsolicitud = $request->opcioncorsolicitud ?? $registro->opcioncorsolicitud;
        $registro->save();

        $departamentocliente = $cliente->sucursal;

        $ultimoTramite = Tramitesubcliente::where('clienteitaid', $request->clienteitaid)
            ->orderBy('created_at', 'desc')
        ->first();

        $usuarioAsignado = $ultimoTramite ? $ultimoTramite->usuarioasignado : $request->usuarioasignado;

        $existe = Tramitesubcliente::where([
            ['clienteitaid', $request->clienteid],
            ['tramite', ''],
        ])->exists();
        if (!$existe) {
            Tramitesubcliente::create([
                'usuarioid' => $request->usuarioid,
                'usuarioregistro' => $request->usuarioregistro,
                'clienteitaid' => $request->clienteid,
                'clienteitanombre' => $request->clientenombre,
                'usuarioasignado' => $usuarioAsignado,
                'tramite' => '',
                'ciudad' => $departamentocliente,
                'estado' => 'PENDIENTE',
                'observaciones' => null,
            ]);
        }

        Tramitesubcliente::where([
            ['clienteitaid', $request->clienteid],
            ['tramite', ''],
            ['estado', 'PENDIENTE']
        ])->update(['estado' => 'FINALIZADO']);


        return response()->json(['success' => true]);
    } */

    /* NUEVO 131125 */
    public function actualizarCamposDictamen(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:procedimientotramites,id',
            'estadodictamen' => 'nullable|string',
            'opcioncorsolicitud' => 'nullable|string',
        ]);

        // 🔹 1. Actualizar el registro principal
        $registro = Tramite::findOrFail($request->id);
        $registro->estadodictamen = $request->estadodictamen ?? $registro->estadodictamen;
        $registro->opcioncorsolicitud = $request->opcioncorsolicitud ?? $registro->opcioncorsolicitud;
        $registro->save();

        // 🔹 2. Buscar cliente asociado
        $cliente = Cliente::find($registro->clienteid);
        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        $departamentocliente = $cliente->sucursal;
        $clienteID = $cliente->id;
        $clienteNombre = $cliente->nombrecompleto;

        // 🔹 3. Determinar el nuevo tipo de trámite
        $tramiteNuevo = '';
        switch ($request->opcioncorsolicitud) {
            case 'SE DERIVÓ A APELACIÓN':
                $tramiteNuevo = 'APELACIÓN';
                break;
            case 'SE DERIVÓ A SEGUNDA SOLICITUD':
                $tramiteNuevo = 'SEGUNDA SOLICITUD';
                break;
            case 'SE DERIVÓ A APELACIÓN SEGUNDA SOLICITUD':
                $tramiteNuevo = 'APELACIÓN SEGUNDA SOLICITUD';
                break;
            case 'SE DERIVÓ A TERCERA SOLICITUD':
                $tramiteNuevo = 'TERCERA SOLICITUD';
                break;
            case 'SE DERIVÓ A APELACIÓN TERCERA SOLICITUD':
                $tramiteNuevo = 'APELACIÓN TERCERA SOLICITUD';
                break;
            default:
                $tramiteNuevo = '';
                break;
        }

        $usuario = Auth::user();

        if ($request->opcioncorsolicitud === 'FINALIZAR TRÁMITE') {

            Tramitesubcliente::where([
                ['clienteitaid', $clienteID],
                ['tramite', $registro->tramite],
                ['estado', 'PENDIENTE']
            ])->update(['estado' => 'FINALIZADO']);

        } elseif (!empty($request->opcioncorsolicitud)) {

            $existe = Tramitesubcliente::where([
                ['clienteitaid', $clienteID],
                ['tramite', $tramiteNuevo],
                ['estado', 'PENDIENTE']
            ])->exists();

            if (!$existe) {
                $ultimoTramite = Tramitesubcliente::where('clienteitaid', $clienteID)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $usuarioAsignado = $ultimoTramite ? $ultimoTramite->usuarioasignado : ($usuario->name ?? 'SIN ASIGNAR');

                Tramitesubcliente::create([
                    'usuarioid' => $usuario->id,
                    'usuarioregistro' => $usuario->name,
                    'clienteitaid' => $clienteID,
                    'clienteitanombre' => $clienteNombre,
                    'usuarioasignado' => $usuarioAsignado,
                    'tramite' => $tramiteNuevo,
                    'ciudad' => $departamentocliente,
                    'estado' => 'PENDIENTE',
                    'observaciones' => null,
                ]);
            }
            Tramitesubcliente::where([
                ['clienteitaid', $clienteID],
                ['tramite', $registro->tramite],
                ['estado', 'PENDIENTE']
            ])->update(['estado' => 'FINALIZADO']);
        }

        return response()->json(['success' => true]);
    }


    public function guardarcriterios(Request $request)
    {
        if ($request->descripcion && $request->porcentaje) {
            foreach ($request->descripcion as $index => $desc) {
                if (!empty($desc)) {
                    DB::table('criteriosdictamen')->insert([
                        'usuarioregistroid'      => $request->usuarioid,
                        'usuarioregistronombre'  => $request->usuarioregistro,
                        'clienteid'              => $request->clienteid,
                        'clientenombre'          => $request->clientenombre,
                        'apoderado'              => $request->apoderado,
                        'idtramite'              => $request->idtramite,
                        'tramite'                => $request->tramite,
                        'nivel'                  => 'NRO ORDEN POR GRAVEDAD DEL DETERIORO',
                        'subnivel'               => $desc,
                        'porcentaje'             => $request->porcentaje[$index] . '%',
                        'subtotal'               => null,
                        'totalasignar'           => null,
                        'created_at'             => now(),
                        'updated_at'             => now(),
                    ]);
                }
            }
        }

        if ($request->desempeno) {
            foreach ($request->desempeno as $subnivel => $valor) {
                [$nrocriterio, $porcentaje] = explode('|', $valor);

                DB::table('criteriosdictamen')->insert([
                    'usuarioregistroid' => $request->usuarioid,
                    'usuarioregistronombre' => $request->usuarioregistro,
                    'clienteid' => $request->clienteid,
                    'clientenombre' => $request->clientenombre,
                    'apoderado' => $request->apoderado,
                    'idtramite' => $request->idtramite,
                    'tramite' => $request->tramite,
                    'nivel' => 'CALIFICACIÓN DE LAS VARIABLES DEPENDIENTES: DESEMPEÑO OCUPACIONAL',
                    'subnivel' => $subnivel,
                    'porcentaje' => $porcentaje . '%',
                    'nrocriterio' => $nrocriterio,
                    /* 'subtotal' => $request->subtotal,
                    'totalasignar' => $request->totalasignar, */
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if ($request->ocupaciontrabajo) {
            foreach ($request->ocupaciontrabajo as $subnivel => $valor) {
                [$nrocriterio, $porcentaje] = explode('|', $valor);

                DB::table('criteriosdictamen')->insert([
                    'usuarioregistroid' => $request->usuarioid,
                    'usuarioregistronombre' => $request->usuarioregistro,
                    'clienteid' => $request->clienteid,
                    'clientenombre' => $request->clientenombre,
                    'apoderado' => $request->apoderado,
                    'idtramite' => $request->idtramite,
                    'tramite' => $request->tramite,
                    'nivel' => 'CALIFICACIÓN DE LAS VARIABLES DEPENDIENTES: OCUPACIÓN - TRABAJO',
                    'subnivel' => $subnivel,
                    'porcentaje' => $porcentaje . '%',
                    'nrocriterio' => $nrocriterio,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if ($request->actividadessociales) {
            foreach ($request->actividadessociales as $subnivel => $valor) {
                [$nrocriterio, $porcentaje] = explode('|', $valor);
                
                DB::table('criteriosdictamen')->insert([
                    'usuarioregistroid' => $request->usuarioid,
                    'usuarioregistronombre' => $request->usuarioregistro,
                    'clienteid' => $request->clienteid,
                    'clientenombre' => $request->clientenombre,
                    'apoderado' => $request->apoderado,
                    'idtramite' => $request->idtramite,
                    'tramite' => $request->tramite,
                    'nivel' => 'CALIFICACIÓN DE LAS VARIABLES DEPENDIENTES: ACTIVIDADES SOCIALES',
                    'subnivel' => $subnivel,
                    'porcentaje' => $porcentaje . '%',
                    'nrocriterio' => $nrocriterio,
                    'subtotal' => null,
                    'totalasignar' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if ($request->factorajusteeconomico) {
            foreach ($request->factorajusteeconomico as $subnivel => $valor) {
                [$nrocriterio, $porcentaje] = explode('|', $valor);
                
                DB::table('criteriosdictamen')->insert([
                    'usuarioregistroid' => $request->usuarioid,
                    'usuarioregistronombre' => $request->usuarioregistro,
                    'clienteid' => $request->clienteid,
                    'clientenombre' => $request->clientenombre,
                    'apoderado' => $request->apoderado,
                    'idtramite' => $request->idtramite,
                    'tramite' => $request->tramite,
                    'nivel' => 'APLICACIÓN DE FACTORES DE AJUSTE: FACTOR DE AJUSTE ECONÓMICO',
                    'subnivel' => $subnivel,
                    'porcentaje' => $porcentaje,
                    'nrocriterio' => $nrocriterio,
                    'subtotal' => null,
                    'totalasignar' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if ($request->calificacionfinal) {
            $vtr = $request->calificacionfinal['vtr'] ?? null;
            $vtr1 = $request->calificacionfinal['vtr1'] ?? null;
            $vtr2 = $request->calificacionfinal['vtr2'] ?? null;

            if ($vtr !== null && $vtr !== '' && $vtr1 !== null && $vtr1 !== '' && $vtr2 !== null && $vtr2 !== '') {
                DB::table('criteriosdictamen')->insert([
                    'usuarioregistroid' => $request->usuarioid,
                    'usuarioregistronombre' => $request->usuarioregistro,
                    'clienteid' => $request->clienteid,
                    'clientenombre' => $request->clientenombre,
                    'apoderado' => $request->apoderado,
                    'idtramite' => $request->idtramite,
                    'tramite' => $request->tramite,
                    'nivel' => 'APLICACIÓN DE FACTORES DE AJUSTE: CALIFICACIÓN FINAL',
                    'vtr' => $vtr,
                    'vtr1' => $vtr1,
                    'vtr2' => $vtr2,
                    'subtotal' => $request->subtotal['calificacion_final'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if (!empty($request->necesidadrecalificacion['decisionrecal'])) {
            DB::table('criteriosdictamen')->insert([
                'usuarioregistroid' => $request->usuarioid,
                'usuarioregistronombre' => $request->usuarioregistro,
                'clienteid' => $request->clienteid,
                'clientenombre' => $request->clientenombre,
                'apoderado' => $request->apoderado,
                'idtramite' => $request->idtramite,
                'tramite' => $request->tramite,
                'nivel' => 'NECESIDAD DE RECALIFICACIÓN',
                'decisionrecal' => $request->necesidadrecalificacion['decisionrecal'],
                'mes' => $request->necesidadrecalificacion['mes'] ?? null,
                'anno' => $request->necesidadrecalificacion['anno'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($request->fechasiniestro) {
            $fechasiniestro1 = $request->fechasiniestro['fechasiniestro1'] ?? null;
            $fechasiniestro2 = $request->fechasiniestro['fechasiniestro2'] ?? null;

            if ($fechasiniestro1 !== null && $fechasiniestro1 !== '' && $fechasiniestro2 !== null && $fechasiniestro2 !== '') {
                DB::table('criteriosdictamen')->insert([
                    'usuarioregistroid' => $request->usuarioid,
                    'usuarioregistronombre' => $request->usuarioregistro,
                    'clienteid' => $request->clienteid,
                    'clientenombre' => $request->clientenombre,
                    'apoderado' => $request->apoderado,
                    'idtramite' => $request->idtramite,
                    'tramite' => $request->tramite,
                    'nivel' => 'FECHA SINIESTRO',
                    'fechasiniestro1' => $fechasiniestro1,
                    'fechasiniestro2' => $fechasiniestro2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->back()->with('info', 'Datos guardados correctamente');
    }

    public function reemplazarArchivo(Request $request)
    {
        $request->validate([
            'tramite_id' => 'required',
            'archivo' => 'required',
        ]);

        $tramite = Tramite::findOrFail($request->tramite_id);
        $cliente_id = $tramite->clienteitaid;
        $usuarioId = auth()->user()->id;

        $carpetaCliente = public_path("/tramitesclientesita/{$cliente_id}");
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }

        // Eliminar archivo anterior si existe
        if ($tramite->document) {
            $rutaAnterior = $carpetaCliente . '/' . $tramite->document;
            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }
        }

        // Subir nuevo archivo
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $archivo_name = time() . '_' . $archivo->getClientOriginalName();
            $archivo->move($carpetaCliente, $archivo_name);

            // Actualizar solo el campo 'document'
            $tramite->update([
                'document' => $archivo_name,
                'usuarioregistro' => $usuarioId, // si quieres registrar quién lo cambió
                'updated_at' => now(),
            ]);
        }

        return back()->with('info', 'Archivo reemplazado correctamente.');
    }
    public function codigocambiofechaarchivoprestaciones(Request $request)
    {
        $codigo = $request->input('codigo');

        $permiso = PermisoCodigo::where('codigo', $codigo)->first();

        if (!$permiso) {
            return response()->json(['success' => false, 'message' => 'CODIGO INVALIDO']);
        }

        $permiso->estado = 'Expirado';
        $permiso->save();

        return response()->json(['success' => true]);
    }
    /* public function actualizarEstado($id, $clienteId)
    {
        $tramite = Tramite::find($id);

        if ($tramite) {
            $tramite->estadocomunicado = 'COMUNICADO';
            $tramite->save();
        }

        $cliente = Cliente::find($clienteId);
        $mensaje = "Hola, le hablo de la empresa GOOD LIFE, comunicarle que en fecha: " .
                    $tramite->fechasubida . ", se realizo: " .
                    $tramite->nivelprocedimiento . ", " .
                    $tramite->subprocedimiento . ".";
        $mensajeCodificado = urlencode($mensaje);

        return redirect()->away("https://wa.me/{$cliente->celular}?text={$mensajeCodificado}");
    } */

    public function actualizarEstado($id, $clienteId)
    {
        $tramite = Tramite::find($id);

        if ($tramite) {
            $tramite->estadocomunicado = 'COMUNICADO';
            $tramite->save();
        }

        $cliente = Cliente::find($clienteId);

        // Si nivelprocedimiento = subprocedimiento, solo mostramos uno
        if ($tramite->nivelprocedimiento === $tramite->subprocedimiento) {
            $detalle = $tramite->nivelprocedimiento;
        } else {
            $detalle = $tramite->nivelprocedimiento . ", " . $tramite->subprocedimiento;
        }

        // Fecha en formato día/mes/año
        $fechaFormateada = Carbon::parse($tramite->fechasubida)->format('d/m/Y');

        // Mensaje completo
        $mensaje = "Hola, le hablo de la empresa GOOD LIFE, comunicarle que en fecha: " .
                $fechaFormateada . ", se realizó: " . $detalle .
                " del procedimiento de su trámite de " . $tramite->tramite . ".";

        $mensajeCodificado = urlencode($mensaje);

        return redirect()->away("https://wa.me/{$cliente->celular}?text={$mensajeCodificado}");
    }

    public function subirArchivo(Request $request, $id, $clienteId)
    {
        $request->validate([
            'documento' => '',
            'comusuemisor' => '',
            'comusureceptor' => '',
            'commodo' => '',
            'comtipointerac' => '',
            'comdetalle' => '',
            'comtipoentrega' => '',
            'comtipodoc' => '',
        ]);

        $tramite = Tramite::find($id);
        $cliente = Cliente::find($clienteId);
        if (!$tramite || !$cliente) {
            return redirect()->back()->with('error', 'Trámite o cliente no encontrado.');
        }

        $archivo = $request->file('documento');
        $tramitenombre = $request->input('tramitenombre');
        $comusuemisor = $request->input('comusuemisor');
        $comusureceptor = $request->input('comusureceptor');
        $commodo = $request->input('commodo');
        $comtipointerac = $request->input('comtipointerac');
        $comdetalle = $request->input('comdetalle');
        $comtipoentrega = $request->input('comtipoentrega');
        $comtipodoc = $request->input('comtipodoc');
        $archivo_name = null;

        if ($archivo) {
            $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$tramitenombre}/COMUNICACIONES");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $archivo_name = time() . '_' . $archivo->getClientOriginalName();
            $archivo->move($carpetaCliente, $archivo_name);
        }

        $tramite->capturacomunicacion = $archivo_name ?? 'VACIO';
        $tramite->comusuemisor = $comusuemisor;
        $tramite->comusureceptor = $comusureceptor;
        $tramite->commodo = $commodo;
        $tramite->comtipointerac = $comtipointerac;
        $tramite->comdetalle = $comdetalle;
        $tramite->comtipoentrega = $comtipoentrega;
        $tramite->comtipodoc = $comtipodoc;
        $tramite->save();

        $previousUrl = url()->previous();
        if (Str::contains($previousUrl, 'procmasahereditaria')) {
            return redirect()->route('admin.tramites.procmasahereditaria', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'procinvalidez')) {
            return redirect()->route('admin.tramites.procinvalidez', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'procapelacion')) {
            return redirect()->route('admin.tramites.procapelacion', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'proccompensacionsenasir')) {
            return redirect()->route('admin.tramites.proccompensacionsenasir', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'procjubilacion')) {
            return redirect()->route('admin.tramites.procjubilacion', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'procpensionmuerte')) {
            return redirect()->route('admin.tramites.procpensionmuerte', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'procretiroaportesparcial')) {
            return redirect()->route('admin.tramites.procretiroaportesparcial', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'procretiroaportestotal')) {
            return redirect()->route('admin.tramites.procretiroaportestotal', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'procsegundasolicitud')) {
            return redirect()->route('admin.tramites.procsegundasolicitud', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'proctercerasolicitud')) {
            return redirect()->route('admin.tramites.proctercerasolicitud', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'procrecalificacion')) {
            return redirect()->route('admin.tramites.procrecalificacion', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'procapelsegsolicitud')) {
            return redirect()->route('admin.tramites.procapelsegsolicitud', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'procapeltercersolicitud')) {
            return redirect()->route('admin.tramites.procapeltercersolicitud', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'procapelrecalificacion')) {
            return redirect()->route('admin.tramites.procapelrecalificacion', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'procrecalsegsolicitud')) {
            return redirect()->route('admin.tramites.procrecalsegsolicitud', $cliente)->with('info', 'La comunicación se subió con éxito');
        }elseif (Str::contains($previousUrl, 'procapelrecalsegsolicitud')) {
            return redirect()->route('admin.tramites.procapelrecalsegsolicitud', $cliente)->with('info', 'La comunicación se subió con éxito');
        }else {
            return redirect()->route('admin.tramites.index')->with('info', 'La comunicación se subió con éxito');
        }
    }

    // TRAMITE APELACION
    public function procapelacion(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');

        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'APELACIÓN')
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'APELACIÓN')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'APELACIÓN')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'APELACIÓN')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
        ->value('id');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $existeinvalidez = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->exists();

        $tramiteBuscado = $existeinvalidez ? 'INVALIDEZ' : 'APELACIÓN';

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', $tramiteBuscado)
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'APELACIÓN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'APELACIÓN')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'INVALIDEZ')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMR')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMR')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        $registrosGuardadosSRDadjuntos = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'SRD - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        $registrosGuardadosRSRDadjuntos = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'RSRD - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        //NUEVO 101125
        $registroInfoSITMEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMR')
        ->get();

        $registroInfoCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - COMPRA DE SERVICIOS')
        ->get();

        $registroInfoSICEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMR')
        ->get();

        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN')->get();

        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        $bateriaProveedores = Bateriaproveedor::select('tipoarea', 'area', 'accion')
            ->orderBy('area')
        ->get();

        $ultimosRegistros = RecomendacionBaterias::where('clienteid', $cliente->id)
            ->get();


        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'APELACIÓN')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        $documentos = $programacionesRaw->merge($informes);

        foreach ($documentos as $doc) {
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            } else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 : 2);

                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $regITprog = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'IT - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        return view('admin.tramites.procapelacion', compact('regITprog','programaciones','bateriaProveedores','ultimosRegistros','listacartas',
        'listaadjuntos','mescierreinicio','diasRestantescom2','registrosGuardadosProgramacioncom2','diasRestantescom',
        'registrosGuardadosProgramacioncom','nuacuacliente','cicliente','ciexpcliente','diasRestantes2',
        'registrosGuardadosProgSITMtmc','diasRestantes','listasolicitudes','matriculacliente','imagenCliente','aseguradoras',
        'estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS','registrosGuardadosProgramacionSIC',
        'registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas','permisoContinuidad','numeropoder',
        'apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado','programaciones','puedeEditarArchivo',
        'puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio','tramitecontinuidad',
        'inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto','personal',
        'existeinvalidez','registrosGuardadosSRDadjuntos','registrosGuardadosRSRDadjuntos','contactos',
        'registroInfoSITMEGS','registroInfoSITMTMC','registroInfoSITMTMR','registroInfoCS','registroInfoSICEGS',
        'registroInfoSICTMC','registroInfoSICTMR','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasprocapelacion(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'INVALIDEZ')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));
        
        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        /* $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        }); */

        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'APELACIÓN')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        /* NUEVO 051225 */
        $subprogramaciones = DB::table('subprocedimientotramites')
            ->select(
                'tipo',
                DB::raw("NULL as fechabateria"),
                DB::raw("estudioespecialidad as areanombre"),
                'estudioespecialidad as accionnombre',
                'nombremedico as proveedornombre',
                'informeprogramacion as document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("CASE WHEN tipo LIKE 'PROGRAMACIONES%' THEN 'PROGRAMACIONES' ELSE 'INFORMES ADICIONALES' END as tipoarea"),
                'nombremedico as proveedor_real'
            )
            ->where('clienteid', $cliente->id)
            ->where(function($query) {
                $query->where('tipo', 'LIKE', 'PROGRAMACIONES%')
                    ->orWhere('tipo', 'LIKE', 'INFORMES ADICIONALES%');
            })
            ->whereNotNull('informeprogramacion')
        ->get();

        $documentos = $programacionesRaw
            ->merge($informes)
            ->merge($subprogramaciones);

        foreach ($documentos as $doc) {
            $tipoarea = strtoupper(trim($doc->tipoarea ?? ''));
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            }
            elseif (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                $path = public_path("tramitesclientesita/{$cliente->id}/APELACIÓN/INFORMES/{$doc->document}");
            }
            else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy(function ($item) {
            $tipoarea = strtoupper($item->tipoarea ?? '');
            if (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                return $item->tipo;
            }
            return $item->fechabateria;
        })
        ->map(function ($grupo) {
            return $grupo->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';

                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 :
                    (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES']) ? 2 : 3));

                return [$ordenTipoarea, $item->areanombre, $item->accionnombre];
            })->values();
        });

        return view('admin.tramites.cartasprocapelacion', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }

    // TRAMITE SEGUNDA SOLICITUD
    public function procsegundasolicitud(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');
        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'SEGUNDA SOLICITUD')
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'SEGUNDA SOLICITUD')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'SEGUNDA SOLICITUD')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'SEGUNDA SOLICITUD')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
        ->value('id');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id) 
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
        ]);

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'SEGUNDA SOLICITUD')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMC')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMC')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        //NUEVO 101125
        $registroInfoSITMEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMR')
        ->get();

        $registroInfoCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - COMPRA DE SERVICIOS')
        ->get();

        $registroInfoSICEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMR')
        ->get();

        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'SEGUNDA SOLICITUD')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'SEGUNDA SOLICITUD')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'SEGUNDA SOLICITUD')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'SEGUNDA SOLICITUD')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'SEGUNDA SOLICITUD')->get();

        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        /* NUEVO 281025 */
        /* $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        }); */

        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'SEGUNDA SOLICITUD')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        $documentos = $programacionesRaw->merge($informes);

        foreach ($documentos as $doc) {
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            } else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 : 2);

                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $regITprog = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'IT - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        return view('admin.tramites.procsegundasolicitud', compact('regITprog','programaciones','contactos','listacartas',
        'listaadjuntos','mescierreinicio','diasRestantescom2','registrosGuardadosProgramacioncom2','diasRestantescom',
        'registrosGuardadosProgramacioncom','nuacuacliente','cicliente','ciexpcliente','diasRestantes2',
        'registrosGuardadosProgSITMtmc','diasRestantes','listasolicitudes','matriculacliente','imagenCliente','aseguradoras',
        'estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS','registrosGuardadosProgramacionSIC',
        'registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas','permisoContinuidad','numeropoder',
        'apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado','programaciones','puedeEditarArchivo',
        'puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio','tramitecontinuidad',
        'inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto', 'personal',
        'registroInfoSITMEGS','registroInfoSITMTMC','registroInfoSITMTMR','registroInfoCS','registroInfoSICEGS',
        'registroInfoSICTMC','registroInfoSICTMR','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasprocsegsolicitud(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'SEGUNDA SOLICITUD')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        /* NUEVO 051225 */
        $subprogramaciones = DB::table('subprocedimientotramites')
            ->select(
                'tipo',
                DB::raw("NULL as fechabateria"),
                DB::raw("estudioespecialidad as areanombre"),
                'estudioespecialidad as accionnombre',
                'nombremedico as proveedornombre',
                'informeprogramacion as document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("CASE WHEN tipo LIKE 'PROGRAMACIONES%' THEN 'PROGRAMACIONES' ELSE 'INFORMES ADICIONALES' END as tipoarea"),
                'nombremedico as proveedor_real'
            )
            ->where('clienteid', $cliente->id)
            ->where(function($query) {
                $query->where('tipo', 'LIKE', 'PROGRAMACIONES%')
                    ->orWhere('tipo', 'LIKE', 'INFORMES ADICIONALES%');
            })
            ->whereNotNull('informeprogramacion')
        ->get();

        $documentos = $programacionesRaw
            ->merge($informes)
            ->merge($subprogramaciones);

        foreach ($documentos as $doc) {
            $tipoarea = strtoupper(trim($doc->tipoarea ?? ''));
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            }
            elseif (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                $path = public_path("tramitesclientesita/{$cliente->id}/SEGUNDA SOLICITUD/INFORMES/{$doc->document}");
            }
            else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy(function ($item) {
            $tipoarea = strtoupper($item->tipoarea ?? '');
            if (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                return $item->tipo;
            }
            return $item->fechabateria;
        })
        ->map(function ($grupo) {
            return $grupo->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';

                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 :
                    (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES']) ? 2 : 3));

                return [$ordenTipoarea, $item->areanombre, $item->accionnombre];
            })->values();
        });


        return view('admin.tramites.cartasprocsegsolicitud', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }

    // TRAMITE TERCERA SOLICITUD
    public function proctercerasolicitud(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');

        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'TERCERA SOLICITUD')
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'TERCERA SOLICITUD')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'TERCERA SOLICITUD')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'TERCERA SOLICITUD')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
        ->value('id');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id) 
            ->where('tramite', 'TERCERA SOLICITUD')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
        ]);

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'TERCERA SOLICITUD')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMC')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMC')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        //NUEVO 101125
        $registroInfoSITMEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMR')
        ->get();

        $registroInfoCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - COMPRA DE SERVICIOS')
        ->get();

        $registroInfoSICEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMR')
        ->get();

        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'TERCERA SOLICITUD')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'TERCERA SOLICITUD')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'TERCERA SOLICITUD')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'TERCERA SOLICITUD')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'TERCERA SOLICITUD')->get();

        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'TERCERA SOLICITUD')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        $documentos = $programacionesRaw->merge($informes);

        foreach ($documentos as $doc) {
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            } else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 : 2);

                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $regITprog = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'IT - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        return view('admin.tramites.proctercerasolicitud', compact('regITprog','programaciones','contactos','listacartas',
        'listaadjuntos','mescierreinicio','diasRestantescom2','registrosGuardadosProgramacioncom2','diasRestantescom',
        'registrosGuardadosProgramacioncom','nuacuacliente','cicliente','ciexpcliente','diasRestantes2',
        'registrosGuardadosProgSITMtmc','diasRestantes','listasolicitudes','matriculacliente','imagenCliente','aseguradoras',
        'estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS','registrosGuardadosProgramacionSIC',
        'registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas','permisoContinuidad','numeropoder',
        'apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado','programaciones','puedeEditarArchivo',
        'puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio','tramitecontinuidad',
        'inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto', 'personal',
        'registroInfoSITMEGS','registroInfoSITMTMC','registroInfoSITMTMR','registroInfoCS','registroInfoSICEGS',
        'registroInfoSICTMC','registroInfoSICTMR','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasproctercerasolicitud(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'TERCERA SOLICITUD')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        /* NUEVO 051225 */
        $subprogramaciones = DB::table('subprocedimientotramites')
            ->select(
                'tipo',
                DB::raw("NULL as fechabateria"),
                DB::raw("estudioespecialidad as areanombre"),
                'estudioespecialidad as accionnombre',
                'nombremedico as proveedornombre',
                'informeprogramacion as document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("CASE WHEN tipo LIKE 'PROGRAMACIONES%' THEN 'PROGRAMACIONES' ELSE 'INFORMES ADICIONALES' END as tipoarea"),
                'nombremedico as proveedor_real'
            )
            ->where('clienteid', $cliente->id)
            ->where(function($query) {
                $query->where('tipo', 'LIKE', 'PROGRAMACIONES%')
                    ->orWhere('tipo', 'LIKE', 'INFORMES ADICIONALES%');
            })
            ->whereNotNull('informeprogramacion')
        ->get();

        $documentos = $programacionesRaw
            ->merge($informes)
            ->merge($subprogramaciones);

        foreach ($documentos as $doc) {
            $tipoarea = strtoupper(trim($doc->tipoarea ?? ''));
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            }
            elseif (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                $path = public_path("tramitesclientesita/{$cliente->id}/TERCERA SOLICITUD/INFORMES/{$doc->document}");
            }
            else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy(function ($item) {
            $tipoarea = strtoupper($item->tipoarea ?? '');
            if (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                return $item->tipo;
            }
            return $item->fechabateria;
        })
        ->map(function ($grupo) {
            return $grupo->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';

                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 :
                    (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES']) ? 2 : 3));

                return [$ordenTipoarea, $item->areanombre, $item->accionnombre];
            })->values();
        });


        return view('admin.tramites.cartasproctercerasolicitud', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }

    // TRAMITE JUBILACION
    public function procjubilacion(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');

        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'JUBILACIÓN')
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'JUBILACIÓN')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'JUBILACIÓN')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'JUBILACIÓN')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'JUBILACIÓN')
        ->value('id');

        $tipojubilacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'JUBILACIÓN')
        ->value('observaciones');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'JUBILACIÓN')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $numhijosmenorescliente = Cliente::where('id', $cliente->id)
        ->value('numhijosmenores');

        $estadocivilcliente = Cliente::where('id', $cliente->id)
        ->value('estadocivil');

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id) 
            ->where('tramite', 'JUBILACIÓN')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
        ]);

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'JUBILACIÓN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'JUBILACIÓN')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'JUBILACIÓN')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'JUBILACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'JUBILACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'JUBILACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'JUBILACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMC')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'JUBILACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMC')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'JUBILACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'JUBILACIÓN')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'JUBILACIÓN')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'JUBILACIÓN')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'JUBILACIÓN')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'JUBILACIÓN')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'JUBILACIÓN')->get();

        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        $subtramites = SubTramite::all(); 
        $aporteslimitesley = DB::table('aporteslimitesley')->get();

        return view('admin.tramites.procjubilacion', compact('listacartas','listaadjuntos','mescierreinicio','diasRestantescom2',
        'registrosGuardadosProgramacioncom2','diasRestantescom','registrosGuardadosProgramacioncom','nuacuacliente','cicliente',
        'ciexpcliente','diasRestantes2','registrosGuardadosProgSITMtmc','diasRestantes','listasolicitudes','matriculacliente',
        'imagenCliente','aseguradoras','estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS',
        'registrosGuardadosProgramacionSIC','registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas',
        'permisoContinuidad','numeropoder','apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado',
        'programaciones','puedeEditarArchivo','puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio',
        'tramitecontinuidad','inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto',
        'personal','tipojubilacion','subtramites','numhijosmenorescliente','estadocivilcliente','aporteslimitesley','contactos','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasprocjubilacion(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'JUBILACIÓN')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'JUBILACIÓN')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'JUBILACIÓN')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });


        return view('admin.tramites.cartasprocjubilacion', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }
    public function guardardatosafiliado(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'cantidad_cuotas' => 'nullable',
            'saldo_acumulado' => 'nullable',
            'anios_servicio'  => 'nullable',
            'aporte_independiente' => '',
            'aportes.cantidad.*' => 'nullable',
            'aportes.fecha.*' => 'nullable|date',
            'montoaprox' => 'nullable',
            'leyannios' => 'nullable',
            'leymeses' => 'nullable',
            'leyminimo' => 'nullable',
            'leymaximo' => 'nullable',
            'leyporcentajeref' => 'nullable',
        ]);

        SubTramite::updateOrCreate(
            [
                'clienteid' => $request->clienteid,
                'idtramite' => $request->idtramite,
                'tipo' => 'DATOS DE AFILIADO',
            ],
            [
                'usuarioregistroid' => $request->usuarioid,
                'usuarioregistronombre' => $request->usuarioregistro,
                'clientenombre' => $request->clientenombre,
                'tramite' => $request->tramite,
                'apoderado' => $request->apoderado,
                'cantidadcuotas' => $data['cantidad_cuotas'] ?? null,
                'saldoacumulado' => $data['saldo_acumulado'] ?? null,
                'anniosservicio'  => $data['anios_servicio'] ?? null,
                'aporteindependiente' => $data['aporte_independiente'] ?? null,
                'montoaprox' => $data['montoaprox'] ?? null,
                'leyannios' => $data['leyannios'] ?? null,
                'leymeses' => $data['leymeses'] ?? null,
                'leyminimo' => $data['leyminimo'] ?? null,
                'leymaximo' => $data['leymaximo'] ?? null,
                'leyporcentajeref' => $data['leyporcentajeref'] ?? null,
            ]
        );

        /* if ($data['aporte_independiente'] === "SI" && !empty($data['aportes']['cantidad'])) { */
        if (($data['aporte_independiente'] ?? null) === "SI" && !empty($data['aportes']['cantidad'])) {
            foreach ($data['aportes']['cantidad'] as $i => $cantidad) {
                if ($cantidad && !empty($data['aportes']['fecha'][$i])) {
                    SubTramite::create([
                        'usuarioregistroid' => $request->usuarioid,
                        'usuarioregistronombre' => $request->usuarioregistro,
                        'clienteid' => $request->clienteid,
                        'clientenombre' => $request->clientenombre,
                        'idtramite' => $request->idtramite,
                        'tramite' => $request->tramite,
                        'apoderado' => $request->apoderado,
                        'tipo' => 'APORTE MENSUAL',
                        'cantidadaporte' => $cantidad,
                        'fechaaporte' => $data['aportes']['fecha'][$i],
                    ]);
                }
            }
        }

        return back()->with('info', 'Registro guardado exitosamente.');
    }

    // TRAMITE PENSIÓN POR MUERTE
    public function procpensionmuerte(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');

        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'PENSIÓN POR MUERTE')
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'PENSIÓN POR MUERTE')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'PENSIÓN POR MUERTE')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'PENSIÓN POR MUERTE')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'PENSIÓN POR MUERTE')
        ->value('id');

        $tipopensionmuerte = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'PENSIÓN POR MUERTE')
        ->value('observaciones');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'PENSIÓN POR MUERTE')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $empresacliente = Cliente::where('id', $cliente->id)
        ->value('empresa');

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id) 
            ->where('tramite', 'PENSIÓN POR MUERTE')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
        ]);

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'PENSIÓN POR MUERTE')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'PENSIÓN POR MUERTE')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'PENSIÓN POR MUERTE')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'PENSIÓN POR MUERTE')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'PENSIÓN POR MUERTE')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'PENSIÓN POR MUERTE')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'PENSIÓN POR MUERTE')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMC')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'PENSIÓN POR MUERTE')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMC')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'PENSIÓN POR MUERTE')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'PENSIÓN POR MUERTE')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'PENSIÓN POR MUERTE')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'PENSIÓN POR MUERTE')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'PENSIÓN POR MUERTE')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'PENSIÓN POR MUERTE')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'PENSIÓN POR MUERTE')->get();

        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        $subtramites = SubTramite::all(); 
        return view('admin.tramites.procpensionmuerte', compact('listacartas','listaadjuntos','mescierreinicio','diasRestantescom2',
        'registrosGuardadosProgramacioncom2','diasRestantescom','registrosGuardadosProgramacioncom','nuacuacliente','cicliente',
        'ciexpcliente','diasRestantes2','registrosGuardadosProgSITMtmc','diasRestantes','listasolicitudes','matriculacliente',
        'imagenCliente','aseguradoras','estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS',
        'registrosGuardadosProgramacionSIC','registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas',
        'permisoContinuidad','numeropoder','apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado',
        'programaciones','puedeEditarArchivo','puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio',
        'tramitecontinuidad','inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto',
        'personal','tipopensionmuerte','subtramites','empresacliente','contactos','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasprocpensionmuerte(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'PENSIÓN POR MUERTE')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'PENSIÓN POR MUERTE')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'PENSIÓN POR MUERTE')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });


        return view('admin.tramites.cartasprocpensionmuerte', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }
    public function guardardatosafiliadopensionmuerte(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombreafiliado' => 'nullable|string',
            'edadfallecimiento' => 'nullable|integer',
            'estadolaboralfallec'  => 'nullable|string',
            'ultimafechalaboral' => 'nullable|date',
            'percibiaservicio1' => 'nullable|string',
            'percibiaservicio2' => 'nullable|string',
            'percibiaservicio3' => 'nullable|string',
        ]);

        SubTramite::create(
            [
                'clienteid' => $request->clienteid,
                'idtramite' => $request->idtramite,
                'tipo' => 'DATOS DE AFILIADO',
                'usuarioregistroid' => $request->usuarioid,
                'usuarioregistronombre' => $request->usuarioregistro,
                'clientenombre' => $request->clientenombre,
                'tramite' => $request->tramite,
                'apoderado' => $request->apoderado,
                'nombreafiliado' => $data['nombreafiliado'] ?? null,
                'edadfallecimiento' => $data['edadfallecimiento'] ?? null,
                'estadolaboralfallec'  => $data['estadolaboralfallec'] ?? null,
                'ultimafechalaboral' => $data['ultimafechalaboral'] ?? null,
                'percibiaservicio1' => $data['percibiaservicio1'] ?? null,
                'percibiaservicio2' => $data['percibiaservicio2'] ?? null,
                'percibiaservicio3' => $data['percibiaservicio3'] ?? null,
            ]
        );

        return back()->with('info', 'Registro guardado exitosamente.');
    }

    // TRAMITE MASA HEREDITARIA
    public function procmasahereditaria(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');
        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'MASA HEREDITARIA')
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'MASA HEREDITARIA')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'MASA HEREDITARIA')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'MASA HEREDITARIA')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'MASA HEREDITARIA')
        ->value('id');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'MASA HEREDITARIA')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id) 
            ->where('tramite', 'MASA HEREDITARIA')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
        ]);

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'MASA HEREDITARIA')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'MASA HEREDITARIA')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'MASA HEREDITARIA')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'MASA HEREDITARIA')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'MASA HEREDITARIA')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'MASA HEREDITARIA')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'MASA HEREDITARIA')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMC')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'MASA HEREDITARIA')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMC')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'MASA HEREDITARIA')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'MASA HEREDITARIA')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'MASA HEREDITARIA')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'MASA HEREDITARIA')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'MASA HEREDITARIA')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'MASA HEREDITARIA')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'MASA HEREDITARIA')->get();

        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        $subtramites = SubTramite::all(); 
        return view('admin.tramites.procmasahereditaria', compact('listacartas','listaadjuntos','mescierreinicio',
        'diasRestantescom2','registrosGuardadosProgramacioncom2','diasRestantescom','registrosGuardadosProgramacioncom',
        'nuacuacliente','cicliente','ciexpcliente','diasRestantes2','registrosGuardadosProgSITMtmc','diasRestantes',
        'listasolicitudes','matriculacliente','imagenCliente','aseguradoras','estlab','afpgestora','estadolaboral',
        'registrosGuardadosProgramacioCS','registrosGuardadosProgramacionSIC','registrosGuardadosProgramacion','todasareas',
        'registrosAgrupados','empresas','permisoContinuidad','numeropoder','apoderadosList','proveedoresmedicos','aseguradora',
        'apoderadoAsignado','programaciones','puedeEditarArchivo','puedeEditarFecha','proveedores','idTramite',
        'modelocartasreclamos','tramiteinicio','tramitecontinuidad','inicioocontinuidad','cartasreclamos','procedimientotramites',
        'id','cliente','nombrecompleto', 'personal','subtramites', 'contactos','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasprocmasahereditaria(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'MASA HEREDITARIA')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'MASA HEREDITARIA')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'MASA HEREDITARIA')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });


        return view('admin.tramites.cartasprocmasahereditaria', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }
    public function guardardatosafiliadomasahereditaria(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'percibiaservicio1' => 'nullable|string',
            'percibiaservicio2' => 'nullable|string',
            'percibiaservicio3' => 'nullable|string',

            'solicitante1' => 'nullable|string',
            'solicitante2' => 'nullable|string',

            'dh123grado1' => 'nullable|string',
            'dh123grado2' => 'nullable|string',
            'dh123grado3' => 'nullable|string', // ✅ corregido
        ]);

        SubTramite::create([
            'clienteid' => $request->clienteid,
            'idtramite' => $request->idtramite,
            'tipo' => 'DATOS DE AFILIADO',
            'usuarioregistroid' => $request->usuarioid,
            'usuarioregistronombre' => $request->usuarioregistro,
            'clientenombre' => $request->clientenombre,
            'tramite' => $request->tramite,
            'apoderado' => $request->apoderado,

            'percibiaservicio1' => $data['percibiaservicio1'] ?? null,
            'percibiaservicio2' => $data['percibiaservicio2'] ?? null,
            'percibiaservicio3' => $data['percibiaservicio3'] ?? null,

            'solicitante1' => $data['solicitante1'] ?? null,
            'solicitante2' => $data['solicitante2'] ?? null,

            'dh123grado1' => $data['dh123grado1'] ?? null,
            'dh123grado2' => $data['dh123grado2'] ?? null,
            'dh123grado3' => $data['dh123grado3'] ?? null,
        ]);

        return back()->with('info', 'Registro guardado exitosamente.');
    }

    // TRAMITE RETIRO DE APORTES TOTAL
    public function procretiroaportestotal(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
        ->exists();
        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
        ->value('id');

        $tiporetiroaportestotal = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
        ->value('observaciones');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id) 
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
        ]);

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'RETIRO DE APORTES TOTAL')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMC')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMC')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'RETIRO DE APORTES TOTAL')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'RETIRO DE APORTES TOTAL')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'RETIRO DE APORTES TOTAL')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'RETIRO DE APORTES TOTAL')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'RETIRO DE APORTES TOTAL')->get();

        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        $subtramites = SubTramite::all(); 
        return view('admin.tramites.procretiroaportestotal', compact('listacartas','listaadjuntos','mescierreinicio','diasRestantescom2',
        'registrosGuardadosProgramacioncom2','diasRestantescom','registrosGuardadosProgramacioncom','nuacuacliente','cicliente',
        'ciexpcliente','diasRestantes2','registrosGuardadosProgSITMtmc','diasRestantes','listasolicitudes','matriculacliente',
        'imagenCliente','aseguradoras','estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS',
        'registrosGuardadosProgramacionSIC','registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas',
        'permisoContinuidad','numeropoder','apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado',
        'programaciones','puedeEditarArchivo','puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio',
        'tramitecontinuidad','inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto',
        'personal','tiporetiroaportestotal','subtramites','contactos','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasprocretiroaportestotal(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES TOTAL')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });


        return view('admin.tramites.cartasprocretiroaportestotal', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }


    // TRAMITE RETIRO DE APORTES PARCIAL
    public function procretiroaportesparcial(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');

        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
        ->value('id');

        $tiporetiroaportesparcial = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
        ->value('observaciones');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id) 
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
        ]);

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'RETIRO DE APORTES PARCIAL')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMC')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMC')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'RETIRO DE APORTES PARCIAL')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'RETIRO DE APORTES PARCIAL')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'RETIRO DE APORTES PARCIAL')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'RETIRO DE APORTES PARCIAL')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'RETIRO DE APORTES PARCIAL')->get();

        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        $subtramites = SubTramite::all(); 
        return view('admin.tramites.procretiroaportesparcial', compact('listacartas','listaadjuntos','mescierreinicio','diasRestantescom2',
        'registrosGuardadosProgramacioncom2','diasRestantescom','registrosGuardadosProgramacioncom','nuacuacliente','cicliente',
        'ciexpcliente','diasRestantes2','registrosGuardadosProgSITMtmc','diasRestantes','listasolicitudes','matriculacliente',
        'imagenCliente','aseguradoras','estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS',
        'registrosGuardadosProgramacionSIC','registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas',
        'permisoContinuidad','numeropoder','apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado',
        'programaciones','puedeEditarArchivo','puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio',
        'tramitecontinuidad','inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto',
        'personal','tiporetiroaportesparcial','subtramites','contactos','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasprocretiroaportesparcial(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RETIRO DE APORTES PARCIAL')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });


        return view('admin.tramites.cartasprocretiroaportesparcial', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }

    // TRAMITE RECALIFICACIÓN
    public function procrecalificacion(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');

        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'RECALIFICACIÓN')
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'RECALIFICACIÓN')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'RECALIFICACIÓN')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'RECALIFICACIÓN')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
        ->value('id');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id) 
            ->where('tramite', 'RECALIFICACIÓN')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
        ]);

        $existeinvalidez = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
        ->exists();

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'RECALIFICACIÓN')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMC')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMC')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        $registrosGuardadosSRDadjuntos = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'SRD - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        $registrosGuardadosRSRDadjuntos = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'RSRD - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        //NUEVO 101125
        $registroInfoSITMEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMR')
        ->get();

        $registroInfoCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - COMPRA DE SERVICIOS')
        ->get();

        $registroInfoSICEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMR')
        ->get();

        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'RECALIFICACIÓN')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'RECALIFICACIÓN')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'RECALIFICACIÓN')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'RECALIFICACIÓN')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'RECALIFICACIÓN')->get();

        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        $bateriaProveedores = Bateriaproveedor::select('tipoarea', 'area', 'accion')
            ->orderBy('area')
        ->get();

        $ultimosRegistros = RecomendacionBaterias::where('clienteid', $cliente->id)
            ->get();

        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'RECALIFICACIÓN')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        $documentos = $programacionesRaw->merge($informes);

        foreach ($documentos as $doc) {
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            } else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 : 2);

                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $regITprog = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'IT - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        return view('admin.tramites.procrecalificacion', compact('regITprog','programaciones','bateriaProveedores','ultimosRegistros','listacartas',
        'listaadjuntos','mescierreinicio','diasRestantescom2','registrosGuardadosProgramacioncom2','diasRestantescom',
        'registrosGuardadosProgramacioncom','nuacuacliente','cicliente','ciexpcliente','diasRestantes2',
        'registrosGuardadosProgSITMtmc','diasRestantes','listasolicitudes','matriculacliente','imagenCliente','aseguradoras',
        'estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS','registrosGuardadosProgramacionSIC',
        'registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas','permisoContinuidad','numeropoder',
        'apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado','programaciones','puedeEditarArchivo',
        'puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio','tramitecontinuidad',
        'inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto','personal',
        'existeinvalidez','registrosGuardadosSRDadjuntos','registrosGuardadosRSRDadjuntos','contactos',
        'registroInfoSITMEGS','registroInfoSITMTMC','registroInfoSITMTMR','registroInfoCS','registroInfoSICEGS',
        'registroInfoSICTMC','registroInfoSICTMR','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasprocrecalificacion(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'RECALIFICACIÓN')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        /* NUEVO 051225 */
        $subprogramaciones = DB::table('subprocedimientotramites')
            ->select(
                'tipo',
                DB::raw("NULL as fechabateria"),
                DB::raw("estudioespecialidad as areanombre"),
                'estudioespecialidad as accionnombre',
                'nombremedico as proveedornombre',
                'informeprogramacion as document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("CASE WHEN tipo LIKE 'PROGRAMACIONES%' THEN 'PROGRAMACIONES' ELSE 'INFORMES ADICIONALES' END as tipoarea"),
                'nombremedico as proveedor_real'
            )
            ->where('clienteid', $cliente->id)
            ->where(function($query) {
                $query->where('tipo', 'LIKE', 'PROGRAMACIONES%')
                    ->orWhere('tipo', 'LIKE', 'INFORMES ADICIONALES%');
            })
            ->whereNotNull('informeprogramacion')
        ->get();

        $documentos = $programacionesRaw
            ->merge($informes)
            ->merge($subprogramaciones);

        foreach ($documentos as $doc) {
            $tipoarea = strtoupper(trim($doc->tipoarea ?? ''));
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            }
            elseif (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                $path = public_path("tramitesclientesita/{$cliente->id}/RECALIFICACIÓN/INFORMES/{$doc->document}");
            }
            else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy(function ($item) {
            $tipoarea = strtoupper($item->tipoarea ?? '');
            if (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                return $item->tipo;
            }
            return $item->fechabateria;
        })
        ->map(function ($grupo) {
            return $grupo->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';

                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 :
                    (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES']) ? 2 : 3));

                return [$ordenTipoarea, $item->areanombre, $item->accionnombre];
            })->values();
        });

        return view('admin.tramites.cartasprocrecalificacion', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }

    // TRAMITE APELACIÓN DE RECALIFICACIÓN
    public function procapelrecalificacion(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');

        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
        ->value('id');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $existeinvalidez = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->exists();

        $tramiteBuscado = $existeinvalidez ? 'RECALIFICACIÓN' : 'APELACIÓN DE RECALIFICACIÓN';

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', $tramiteBuscado)
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'RECALIFICACIÓN')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMR')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMR')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        $registrosGuardadosSRDadjuntos = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'SRD - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        $registrosGuardadosRSRDadjuntos = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'RSRD - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        //NUEVO 101125
        $registroInfoSITMEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMR')
        ->get();

        $registroInfoCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - COMPRA DE SERVICIOS')
        ->get();

        $registroInfoSICEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMR')
        ->get();

        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')->get();

        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        $bateriaProveedores = Bateriaproveedor::select('tipoarea', 'area', 'accion')
            ->orderBy('area')
        ->get();

        $ultimosRegistros = RecomendacionBaterias::where('clienteid', $cliente->id)
            ->get();

        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        $documentos = $programacionesRaw->merge($informes);

        foreach ($documentos as $doc) {
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            } else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 : 2);

                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $regITprog = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'IT - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        return view('admin.tramites.procapelrecalificacion', compact('regITprog','programaciones','bateriaProveedores','ultimosRegistros','listacartas',
        'listaadjuntos','mescierreinicio','diasRestantescom2','registrosGuardadosProgramacioncom2','diasRestantescom',
        'registrosGuardadosProgramacioncom','nuacuacliente','cicliente','ciexpcliente','diasRestantes2',
        'registrosGuardadosProgSITMtmc','diasRestantes','listasolicitudes','matriculacliente','imagenCliente','aseguradoras',
        'estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS','registrosGuardadosProgramacionSIC',
        'registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas','permisoContinuidad','numeropoder',
        'apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado','programaciones','puedeEditarArchivo',
        'puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio','tramitecontinuidad',
        'inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto','personal',
        'existeinvalidez','registrosGuardadosSRDadjuntos','registrosGuardadosRSRDadjuntos','contactos',
        'registroInfoSITMEGS','registroInfoSITMTMC','registroInfoSITMTMR','registroInfoCS','registroInfoSICEGS',
        'registroInfoSICTMC','registroInfoSICTMR','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasprocapelrecalificacion(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        /* NUEVO 051225 */
        $subprogramaciones = DB::table('subprocedimientotramites')
            ->select(
                'tipo',
                DB::raw("NULL as fechabateria"),
                DB::raw("estudioespecialidad as areanombre"),
                'estudioespecialidad as accionnombre',
                'nombremedico as proveedornombre',
                'informeprogramacion as document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("CASE WHEN tipo LIKE 'PROGRAMACIONES%' THEN 'PROGRAMACIONES' ELSE 'INFORMES ADICIONALES' END as tipoarea"),
                'nombremedico as proveedor_real'
            )
            ->where('clienteid', $cliente->id)
            ->where(function($query) {
                $query->where('tipo', 'LIKE', 'PROGRAMACIONES%')
                    ->orWhere('tipo', 'LIKE', 'INFORMES ADICIONALES%');
            })
            ->whereNotNull('informeprogramacion')
        ->get();

        $documentos = $programacionesRaw
            ->merge($informes)
            ->merge($subprogramaciones);

        foreach ($documentos as $doc) {
            $tipoarea = strtoupper(trim($doc->tipoarea ?? ''));
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            }
            elseif (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                $path = public_path("tramitesclientesita/{$cliente->id}/APELACIÓN DE RECALIFICACIÓN/INFORMES/{$doc->document}");
            }
            else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy(function ($item) {
            $tipoarea = strtoupper($item->tipoarea ?? '');
            if (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                return $item->tipo;
            }
            return $item->fechabateria;
        })
        ->map(function ($grupo) {
            return $grupo->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';

                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 :
                    (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES']) ? 2 : 3));

                return [$ordenTipoarea, $item->areanombre, $item->accionnombre];
            })->values();
        });


        return view('admin.tramites.cartasprocapelrecalificacion', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }

    // TRAMITE APELACIÓN SEGUNDA SOLICITUD
    public function procapelsegsolicitud(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');

        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
        ->value('id');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $existeinvalidez = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->exists();

        $tramiteBuscado = $existeinvalidez ? 'SEGUNDA SOLICITUD' : 'APELACIÓN SEGUNDA SOLICITUD';

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', $tramiteBuscado)
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'SEGUNDA SOLICITUD')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMR')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMR')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        $registrosGuardadosSRDadjuntos = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'SRD - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        $registrosGuardadosRSRDadjuntos = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'RSRD - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        //NUEVO 101125
        $registroInfoSITMEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMR')
        ->get();

        $registroInfoCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - COMPRA DE SERVICIOS')
        ->get();

        $registroInfoSICEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMR')
        ->get();

        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')->get();

        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        $bateriaProveedores = Bateriaproveedor::select('tipoarea', 'area', 'accion')
            ->orderBy('area')
        ->get();

        $ultimosRegistros = RecomendacionBaterias::where('clienteid', $cliente->id)
            ->get();

        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        $documentos = $programacionesRaw->merge($informes);

        foreach ($documentos as $doc) {
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            } else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 : 2);

                return [$ordenTipoarea, $item->areanombre];
            });
        });
        $regITprog = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'IT - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        return view('admin.tramites.procapelsegsolicitud', compact('regITprog','programaciones','bateriaProveedores','ultimosRegistros','listacartas',
        'listaadjuntos','mescierreinicio','diasRestantescom2','registrosGuardadosProgramacioncom2','diasRestantescom',
        'registrosGuardadosProgramacioncom','nuacuacliente','cicliente','ciexpcliente','diasRestantes2',
        'registrosGuardadosProgSITMtmc','diasRestantes','listasolicitudes','matriculacliente','imagenCliente','aseguradoras',
        'estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS','registrosGuardadosProgramacionSIC',
        'registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas','permisoContinuidad','numeropoder',
        'apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado','programaciones','puedeEditarArchivo',
        'puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio','tramitecontinuidad',
        'inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto','personal',
        'existeinvalidez','registrosGuardadosSRDadjuntos','registrosGuardadosRSRDadjuntos','contactos',
        'registroInfoSITMEGS','registroInfoSITMTMC','registroInfoSITMTMR','registroInfoCS','registroInfoSICEGS',
        'registroInfoSICTMC','registroInfoSICTMR','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasprocapelsegsolicitud(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'SEGUNDA SOLICITUD')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'APELACIÓN SEGUNDA SOLICITUD')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        /* NUEVO 051225 */
        $subprogramaciones = DB::table('subprocedimientotramites')
            ->select(
                'tipo',
                DB::raw("NULL as fechabateria"),
                DB::raw("estudioespecialidad as areanombre"),
                'estudioespecialidad as accionnombre',
                'nombremedico as proveedornombre',
                'informeprogramacion as document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("CASE WHEN tipo LIKE 'PROGRAMACIONES%' THEN 'PROGRAMACIONES' ELSE 'INFORMES ADICIONALES' END as tipoarea"),
                'nombremedico as proveedor_real'
            )
            ->where('clienteid', $cliente->id)
            ->where(function($query) {
                $query->where('tipo', 'LIKE', 'PROGRAMACIONES%')
                    ->orWhere('tipo', 'LIKE', 'INFORMES ADICIONALES%');
            })
            ->whereNotNull('informeprogramacion')
        ->get();

        $documentos = $programacionesRaw
            ->merge($informes)
            ->merge($subprogramaciones);

        foreach ($documentos as $doc) {
            $tipoarea = strtoupper(trim($doc->tipoarea ?? ''));
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            }
            elseif (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                $path = public_path("tramitesclientesita/{$cliente->id}/APELACIÓN SEGUNDA SOLICITUD/INFORMES/{$doc->document}");
            }
            else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy(function ($item) {
            $tipoarea = strtoupper($item->tipoarea ?? '');
            if (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                return $item->tipo;
            }
            return $item->fechabateria;
        })
        ->map(function ($grupo) {
            return $grupo->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';

                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 :
                    (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES']) ? 2 : 3));

                return [$ordenTipoarea, $item->areanombre, $item->accionnombre];
            })->values();
        });


        return view('admin.tramites.cartasprocapelsegsolicitud', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }

    // TRAMITE APELACIÓN TERCERA SOLICITUD
    public function procapeltercersolicitud(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');

        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
        ->value('id');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $existeinvalidez = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->exists();

        $tramiteBuscado = $existeinvalidez ? 'TERCERA SOLICITUD' : 'APELACIÓN TERCERA SOLICITUD';

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', $tramiteBuscado)
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'TERCERA SOLICITUD')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMR')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMR')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        $registrosGuardadosSRDadjuntos = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'SRD - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        $registrosGuardadosRSRDadjuntos = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'RSRD - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        //NUEVO 101125
        $registroInfoSITMEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMR')
        ->get();

        $registroInfoCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - COMPRA DE SERVICIOS')
        ->get();

        $registroInfoSICEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMR')
        ->get();

        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN TERCERA SOLICITUD')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN TERCERA SOLICITUD')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN TERCERA SOLICITUD')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN TERCERA SOLICITUD')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN TERCERA SOLICITUD')->get();

        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        $bateriaProveedores = Bateriaproveedor::select('tipoarea', 'area', 'accion')
            ->orderBy('area')
        ->get();

        $ultimosRegistros = RecomendacionBaterias::where('clienteid', $cliente->id)
            ->get();

        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        $documentos = $programacionesRaw->merge($informes);

        foreach ($documentos as $doc) {
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            } else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 : 2);

                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $regITprog = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'IT - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        return view('admin.tramites.procapeltercersolicitud', compact('regITprog','programaciones','bateriaProveedores','ultimosRegistros','listacartas',
        'listaadjuntos','mescierreinicio','diasRestantescom2','registrosGuardadosProgramacioncom2','diasRestantescom',
        'registrosGuardadosProgramacioncom','nuacuacliente','cicliente','ciexpcliente','diasRestantes2',
        'registrosGuardadosProgSITMtmc','diasRestantes','listasolicitudes','matriculacliente','imagenCliente','aseguradoras',
        'estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS','registrosGuardadosProgramacionSIC',
        'registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas','permisoContinuidad','numeropoder',
        'apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado','programaciones','puedeEditarArchivo',
        'puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio','tramitecontinuidad',
        'inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto','personal',
        'existeinvalidez','registrosGuardadosSRDadjuntos','registrosGuardadosRSRDadjuntos','contactos',
        'registroInfoSITMEGS','registroInfoSITMTMC','registroInfoSITMTMR','registroInfoCS','registroInfoSICEGS',
        'registroInfoSICTMC','registroInfoSICTMR','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasprocapeltercersolicitud(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'TERCERA SOLICITUD')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'APELACIÓN TERCERA SOLICITUD')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        /* NUEVO 051225 */
        $subprogramaciones = DB::table('subprocedimientotramites')
            ->select(
                'tipo',
                DB::raw("NULL as fechabateria"),
                DB::raw("estudioespecialidad as areanombre"),
                'estudioespecialidad as accionnombre',
                'nombremedico as proveedornombre',
                'informeprogramacion as document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("CASE WHEN tipo LIKE 'PROGRAMACIONES%' THEN 'PROGRAMACIONES' ELSE 'INFORMES ADICIONALES' END as tipoarea"),
                'nombremedico as proveedor_real'
            )
            ->where('clienteid', $cliente->id)
            ->where(function($query) {
                $query->where('tipo', 'LIKE', 'PROGRAMACIONES%')
                    ->orWhere('tipo', 'LIKE', 'INFORMES ADICIONALES%');
            })
            ->whereNotNull('informeprogramacion')
        ->get();

        $documentos = $programacionesRaw
            ->merge($informes)
            ->merge($subprogramaciones);

        foreach ($documentos as $doc) {
            $tipoarea = strtoupper(trim($doc->tipoarea ?? ''));
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            }
            elseif (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                $path = public_path("tramitesclientesita/{$cliente->id}/APELACIÓN TERCERA SOLICITUD/INFORMES/{$doc->document}");
            }
            else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy(function ($item) {
            $tipoarea = strtoupper($item->tipoarea ?? '');
            if (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                return $item->tipo;
            }
            return $item->fechabateria;
        })
        ->map(function ($grupo) {
            return $grupo->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';

                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 :
                    (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES']) ? 2 : 3));

                return [$ordenTipoarea, $item->areanombre, $item->accionnombre];
            })->values();
        });


        return view('admin.tramites.cartasprocapeltercersolicitud', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }

    // TRAMITE RECALIFICACIÓN SEGUNDA SOLICITUD
    public function procrecalsegsolicitud(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');

        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->value('id');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id) 
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
        ]);

        $existeinvalidez = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->exists();

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMC')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMC')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        $registrosGuardadosSRDadjuntos = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'SRD - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        $registrosGuardadosRSRDadjuntos = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'RSRD - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        //NUEVO 101125
        $registroInfoSITMEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMR')
        ->get();

        $registroInfoCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - COMPRA DE SERVICIOS')
        ->get();

        $registroInfoSICEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMR')
        ->get();

        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')->get();

        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        $bateriaProveedores = Bateriaproveedor::select('tipoarea', 'area', 'accion')
            ->orderBy('area')
        ->get();

        $ultimosRegistros = RecomendacionBaterias::where('clienteid', $cliente->id)
            ->get();

        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        $documentos = $programacionesRaw->merge($informes);

        foreach ($documentos as $doc) {
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            } else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 : 2);

                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $regITprog = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'IT - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        return view('admin.tramites.procrecalsegsolicitud', compact('regITprog','programaciones','bateriaProveedores','ultimosRegistros','listacartas',
        'listaadjuntos','mescierreinicio','diasRestantescom2','registrosGuardadosProgramacioncom2','diasRestantescom',
        'registrosGuardadosProgramacioncom','nuacuacliente','cicliente','ciexpcliente','diasRestantes2',
        'registrosGuardadosProgSITMtmc','diasRestantes','listasolicitudes','matriculacliente','imagenCliente','aseguradoras',
        'estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS','registrosGuardadosProgramacionSIC',
        'registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas','permisoContinuidad','numeropoder',
        'apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado','programaciones','puedeEditarArchivo',
        'puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio','tramitecontinuidad',
        'inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto','personal',
        'existeinvalidez','registrosGuardadosSRDadjuntos','registrosGuardadosRSRDadjuntos','contactos',
        'registroInfoSITMEGS','registroInfoSITMTMC','registroInfoSITMTMR','registroInfoCS','registroInfoSICEGS',
        'registroInfoSICTMC','registroInfoSICTMR','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasprocrecalsegsolicitud(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        /* NUEVO 051225 */
        $subprogramaciones = DB::table('subprocedimientotramites')
            ->select(
                'tipo',
                DB::raw("NULL as fechabateria"),
                DB::raw("estudioespecialidad as areanombre"),
                'estudioespecialidad as accionnombre',
                'nombremedico as proveedornombre',
                'informeprogramacion as document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("CASE WHEN tipo LIKE 'PROGRAMACIONES%' THEN 'PROGRAMACIONES' ELSE 'INFORMES ADICIONALES' END as tipoarea"),
                'nombremedico as proveedor_real'
            )
            ->where('clienteid', $cliente->id)
            ->where(function($query) {
                $query->where('tipo', 'LIKE', 'PROGRAMACIONES%')
                    ->orWhere('tipo', 'LIKE', 'INFORMES ADICIONALES%');
            })
            ->whereNotNull('informeprogramacion')
        ->get();

        $documentos = $programacionesRaw
            ->merge($informes)
            ->merge($subprogramaciones);

        foreach ($documentos as $doc) {
            $tipoarea = strtoupper(trim($doc->tipoarea ?? ''));
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            }
            elseif (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                $path = public_path("tramitesclientesita/{$cliente->id}/RECALIFICACIÓN SEGUNDA SOLICITUD/INFORMES/{$doc->document}");
            }
            else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy(function ($item) {
            $tipoarea = strtoupper($item->tipoarea ?? '');
            if (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                return $item->tipo;
            }
            return $item->fechabateria;
        })
        ->map(function ($grupo) {
            return $grupo->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';

                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 :
                    (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES']) ? 2 : 3));

                return [$ordenTipoarea, $item->areanombre, $item->accionnombre];
            })->values();
        });

        return view('admin.tramites.cartasprocrecalsegsolicitud', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }

    // TRAMITE APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD
    public function procapelrecalsegsolicitud(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $provintext = Proveedoresservicios::where('estado', 'ACTIVO')
        ->whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
        ->orderBy('razonsocial', 'asc')
        ->pluck('razonsocial');

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');

        $contactos = Contactosubcliente::where('clienteitaid', $cliente->id)
        ->pluck('nombrecontacto');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->exists();

        $mescierreinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->value('mescierre');

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->value('id');

        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->value('apoderadoasignado');

        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
    
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');

        $estadolaboral = Cliente::where('id', $cliente->id)
        ->value('estadolaboral');

        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');

        $nuacuacliente = Cliente::where('id', $cliente->id)
        ->value('nuacua');

        $cicliente = Cliente::where('id', $cliente->id)
        ->value('ci');

        $ciexpcliente = Cliente::where('id', $cliente->id)
        ->value('ciexp');

        $existeinvalidez = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->exists();

        $tramiteBuscado = $existeinvalidez ? 'RECALIFICACIÓN SEGUNDA SOLICITUD' : 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD';

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', $tramiteBuscado)
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);

        $apoderadosList = collect($apoderados)->filter()->values();
        $apoderadosList->push('DENISSE MAUREN LOPEZ FLORES');
        $apoderadosList->push('FABRICIO ORLANDO PRADO PARRADO');
        
        if (!empty($apoderadoAsignado) && !$apoderadosList->contains($apoderadoAsignado)) {
            $apoderadosList->push($apoderadoAsignado);
        }

        $nombreclienteita = $cliente->nombrecompleto;

        $procedimientotramites = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
            ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
        ->simplePaginate(10000);
        
        $cartasreclamos = Tramite::where('clientenombre', $nombreclienteita)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('nivelprocedimiento', '!=', 'INICIO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'INGRESO DE TRÁMITE')
            ->where('nivelprocedimiento', '!=', 'NOTIFICACIÓN DE PODER')
            ->where('nivelprocedimiento', '!=', 'FIRMA EAP')
            ->where('nivelprocedimiento', '!=', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
            ->where('nivelprocedimiento', '!=', 'COMPRA DE SERVICIOS')
            ->where('nivelprocedimiento', '!=', 'SOCILICITUD DE INFORMACIÓN COMPLEMENTARIA')
            ->where('nivelprocedimiento', '!=', 'DICTAMEN')
            ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
            ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
        ->simplePaginate(10000);
        
        $proveedores = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR INTERNO', 'PROVEEDOR EXTERNO'])
            ->orderBy('razonsocial')
        ->get();

        $empresas = Empresa::orderBy('nombreempresa')->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $permisos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.cambiarfechaprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosFechas = [];
        foreach ($permisos as $permiso) {
            $ultimoProcedimiento = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoProcedimiento || $ultimoProcedimiento->updated_at < $permiso->created_at) {
                $codigosPermitidosFechas[] = $permiso->clienteid;
            }
        }
        $puedeEditarFecha = in_array($cliente->id, $codigosPermitidosFechas);

        $permisosArchivos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.editararchivoprestaciones')
            ->where('estado', 'expirado')
        ->get();

        $codigosPermitidosArchivos = [];

        foreach ($permisosArchivos as $permiso) {
            $ultimoTramiteCliente = Tramite::where('clienteid', $permiso->clienteid)
                ->orderByDesc('updated_at')
                ->first();

            if (!$ultimoTramiteCliente || $ultimoTramiteCliente->updated_at < $permiso->created_at) {
                $codigosPermitidosArchivos[] = $permiso->clienteid;
            }
        }

        $puedeEditarArchivo = in_array($cliente->id, $codigosPermitidosArchivos);

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real")

            )
            ->where('d.clienteitaid', $cliente->id)
            ->orderBy('p.fechabateria')
        ->get();

        foreach ($programacionesRaw as $doc) {
            $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");

            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $programacionesRaw->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea = $tipoarea === 'ESPECIALIDAD' ? 0 : 1;
                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $proveedoresmedicos = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->first();
        
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $permisoContinuidad = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.tramites.continuidadtramiteprestaciones')
            ->where('estado', 'expirado')
        ->exists();

        $registrosGuardados = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'OBSERVACIONES FIRMA EAP')
        ->get();
        $agrupados = [];

        foreach ($registrosGuardados as $registro) {
            $clave = $registro->razonsocialempleador . '||' . $registro->observacion;

            if (!isset($agrupados[$clave])) {
                $agrupados[$clave] = [
                    'razonsocialempleador' => $registro->razonsocialempleador,
                    'observacion' => $registro->observacion,
                    'periodos' => [],
                ];
            }

            $agrupados[$clave]['periodos'][] = \Carbon\Carbon::parse($registro->periodo)->format('Y-m');
        }

        $registrosAgrupados = array_values($agrupados);

        $todasareas = DB::table('bateriaproveedores')
            ->select('area')
            ->distinct()
            ->orderBy('area')
        ->get();

        $registrosGuardadosProgramacion = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SITM ente gestor de salud
            $todosConAsistencia = $registrosGuardadosProgramacion->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes = null;
            if ($todosConAsistencia && $registrosGuardadosProgramacion->count() > 0) {
                $fechaMasReciente = $registrosGuardadosProgramacion->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente) {
                    $fechaFinal = Carbon::parse($fechaMasReciente)->addDays(10);
                    $diasRestantes = now()->diffInDays($fechaFinal, false);
                }
            }
        //

        $registrosGuardadosProgramacioncom = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC ente gestor de salud
            $todosConAsistenciacom = $registrosGuardadosProgramacioncom->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom = null;
            if ($todosConAsistenciacom && $registrosGuardadosProgramacioncom->count() > 0) {
                $fechaMasRecientecom = $registrosGuardadosProgramacioncom->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom) {
                    $fechaFinalcom = Carbon::parse($fechaMasRecientecom)->addDays(10);
                    $diasRestantescom = now()->diffInDays($fechaFinalcom, false);
                }
            }
        //

        $registrosGuardadosProgSITMtmc = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SITM NOTIFICACIÓN TMR')
        ->get();

        //CUENTA REGRESIVA 10 DIAS SITM notificacion tmc
            $todosConAsistencia2 = $registrosGuardadosProgSITMtmc->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantes2 = null;
            if ($todosConAsistencia2 && $registrosGuardadosProgSITMtmc->count() > 0) {
                $fechaMasReciente2 = $registrosGuardadosProgSITMtmc->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasReciente2) {
                    $fechaFinal2 = Carbon::parse($fechaMasReciente2)->addDays(10);
                    $diasRestantes2 = now()->diffInDays($fechaFinal2, false);
                }
            }
        //
        
        $registrosGuardadosProgramacioncom2 = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC NOTIFICACIÓN TMR')
        ->get();
        //CUENTA REGRESIVA 10 DIAS SIC notificacion tmc
            $todosConAsistenciacom2 = $registrosGuardadosProgramacioncom2->every(function ($registro) {
                return $registro->asistenciaprogramacion == 1;
            });
            $diasRestantescom2 = null;
            if ($todosConAsistenciacom2 && $registrosGuardadosProgramacioncom2->count() > 0) {
                $fechaMasRecientecom2 = $registrosGuardadosProgramacioncom2->max(function ($registro) {
                    return max(
                        $registro->fechaprogramacion ? Carbon::parse($registro->fechaprogramacion) : null,
                        $registro->fechareprogramacion ? Carbon::parse($registro->fechareprogramacion) : null
                    );
                });
                if ($fechaMasRecientecom2) {
                    $fechaFinalcom2 = Carbon::parse($fechaMasRecientecom2)->addDays(10);
                    $diasRestantescom2 = now()->diffInDays($fechaFinalcom2, false);
                }
            }
        //

        $registrosGuardadosProgramacionSIC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD')
        ->get();

        $registrosGuardadosProgramacioCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('opcionatencion', 'COMPRA DE SERVICIOS')
        ->get();

        $registrosGuardadosSRDadjuntos = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'SRD - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        $registrosGuardadosRSRDadjuntos = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'RSRD - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        //NUEVO 101125
        $registroInfoSITMEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SITM NOTIFICACIÓN TMR')
        ->get();

        $registroInfoCS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - COMPRA DE SERVICIOS')
        ->get();

        $registroInfoSICEGS = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC ENTE GESTOR DE SALUD')
        ->get();

        $registroInfoSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMC')
        ->get();

        $registroInfoSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'INFORMES ADICIONALES - SIC NOTIFICACIÓN TMR')
        ->get();
        
        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');
        $imagenCliente = null;

        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        $listasolicitudes = Tramite::where('tipo', 'SOLICITUD')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')->get();
        $listaadjuntos = Tramite::where('tipo', 'ADJUNTO / RESPUESTA')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')->get();
        $listacartas = Tramite::where('tipo', 'CARTA / RECLAMO')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')->get();
        /* NUEVO 241125 */
        $listamisivas = Tramite::where('tipo', 'MISIVA LIBRE')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')->get();
        $comseguimientos = Tramite::where('nivelprocedimiento', 'SEGUIMIENTO')->where('clienteid', $cliente->id)->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')->get();

        $nrSITMEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM ENTE GESTOR DE SALUD')
        ->get();
        $nrSITMTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMC')
        ->get();
        $nrSITMTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SITM NOTIFICACIÓN TMR')
        ->get();
        $nrSICEG = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC ENTE GESTOR DE SALUD')
        ->get();
        $nrSICTMC = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMC')
        ->get();
        $nrSICTMR = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('idtramite', $idTramite)
            ->where('tipo', 'NR - SIC NOTIFICACIÓN TMR')
        ->get();

        $bateriaProveedores = Bateriaproveedor::select('tipoarea', 'area', 'accion')
            ->orderBy('area')
        ->get();

        $ultimosRegistros = RecomendacionBaterias::where('clienteid', $cliente->id)
            ->get();

        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        $documentos = $programacionesRaw->merge($informes);

        foreach ($documentos as $doc) {
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            } else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy('fechabateria')->map(function ($grupoPorFecha) {
            return $grupoPorFecha->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';
                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 : 2);

                return [$ordenTipoarea, $item->areanombre];
            });
        });

        $regITprog = DB::table('subprocedimientotramites')
            ->where('clienteid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->where('idtramite', $idTramite)
            ->where('tipo', 'IT - ADJUNTO DOCUMENTACIÓN MÉDICA')
        ->get();

        return view('admin.tramites.procapelrecalsegsolicitud', compact('regITprog','programaciones','bateriaProveedores','ultimosRegistros','listacartas',
        'listaadjuntos','mescierreinicio','diasRestantescom2','registrosGuardadosProgramacioncom2','diasRestantescom',
        'registrosGuardadosProgramacioncom','nuacuacliente','cicliente','ciexpcliente','diasRestantes2',
        'registrosGuardadosProgSITMtmc','diasRestantes','listasolicitudes','matriculacliente','imagenCliente','aseguradoras',
        'estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS','registrosGuardadosProgramacionSIC',
        'registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas','permisoContinuidad','numeropoder',
        'apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado','programaciones','puedeEditarArchivo',
        'puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio','tramitecontinuidad',
        'inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto','personal',
        'existeinvalidez','registrosGuardadosSRDadjuntos','registrosGuardadosRSRDadjuntos','contactos',
        'registroInfoSITMEGS','registroInfoSITMTMC','registroInfoSITMTMR','registroInfoCS','registroInfoSICEGS',
        'registroInfoSICTMC','registroInfoSICTMR','listamisivas','comseguimientos','nrSITMEG','nrSITMTMC','nrSITMTMR',
        'nrSICEG','nrSICTMC','nrSICTMR','provintext'));
    }
    public function cartasprocapelrecalsegsolicitud(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;

        /* NUEVO 111125 */
        $apoderadoAsignado = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->value('apoderadoasignado');

        $apoderadosData = InstructivasPoder::where('clienteid', $cliente->id)
            ->where('tramite', 'RECALIFICACIÓN SEGUNDA SOLICITUD')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
            ]);
        if ($apoderadosData) {
            $apoderados = collect($apoderadosData->toArray())
                ->filter(fn($valor) => !is_null($valor) && trim($valor) !== '')
                ->values()
                ->all();
        } else {
            $apoderados = [];
        }
        $apoderadosNorm = array_map(fn($a) => mb_strtolower(trim($a)), $apoderados);
        $apoderadoAsignadoNorm = $apoderadoAsignado ? mb_strtolower(trim($apoderadoAsignado)) : null;
        if ($apoderadoAsignadoNorm && !in_array($apoderadoAsignadoNorm, $apoderadosNorm, true)) {
            $apoderadoAsignado = null;
        }
        $apoderadosExtra = ['FABRICIO ORLANDO PRADO PARRADO', 'DENISSE MAUREN LOPEZ FLORES'];
        $apoderados = array_values(array_unique(array_merge($apoderados, $apoderadosExtra)));

        $idTramite = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->value('id');
        $aseguradora = Cliente::where('id', $cliente->id)
        ->value('aseguradora');
        $afpgestora = Cliente::where('id', $cliente->id)
        ->value('afp');
        $matriculacliente = Cliente::where('id', $cliente->id)
        ->value('matricula');


        /* CARTAS Y RECLAMOS */
        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');


        /* NUEVO 231125 */
        $fechaBateriaApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
        ->value('fechabateria');

        $programacionesRaw = DB::table('programacionsubclientes as p')
            ->join('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
            ->select(
                'p.fechabateria',
                'p.areanombre',
                'p.accionnombre',
                'p.proveedornombre',
                'd.document',
                'd.image',
                'd.image2',
                'd.id as doc_id',
                DB::raw("(SELECT b.tipoarea FROM bateriaproveedores b WHERE b.area = p.areanombre LIMIT 1) as tipoarea"),
                /* DB::raw("(SELECT pr.nombreproveedor FROM proveedores pr WHERE pr.proveedor = p.proveedornombre LIMIT 1) as proveedor_real") */
                'p.proveedornombre as proveedor_real'
            )
            ->where('d.clienteitaid', $cliente->id)
            ->where('p.fechabateria', $fechaBateriaApelacion)
            ->orderBy('p.fechabateria')
            ->get();

        $informes = DB::table('informesfinales')
            ->select(
                'fechabateria',
                DB::raw("'INFORME FINAL' as areanombre"),
                DB::raw("'INFORME FINAL' as accionnombre"),
                'proveedorasignado as proveedornombre',
                'document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("'INFORME FINAL' as tipoarea"),
                'proveedorasignado as proveedor_real'
            )
            ->where('clienteitaid', $cliente->id)
            ->where('fechabateria', $fechaBateriaApelacion) 
            ->get();

        /* NUEVO 051225 */
        $subprogramaciones = DB::table('subprocedimientotramites')
            ->select(
                'tipo',
                DB::raw("NULL as fechabateria"),
                DB::raw("estudioespecialidad as areanombre"),
                'estudioespecialidad as accionnombre',
                'nombremedico as proveedornombre',
                'informeprogramacion as document',
                DB::raw("NULL as image"),
                DB::raw("NULL as image2"),
                'id as doc_id',
                DB::raw("CASE WHEN tipo LIKE 'PROGRAMACIONES%' THEN 'PROGRAMACIONES' ELSE 'INFORMES ADICIONALES' END as tipoarea"),
                'nombremedico as proveedor_real'
            )
            ->where('clienteid', $cliente->id)
            ->where(function($query) {
                $query->where('tipo', 'LIKE', 'PROGRAMACIONES%')
                    ->orWhere('tipo', 'LIKE', 'INFORMES ADICIONALES%');
            })
            ->whereNotNull('informeprogramacion')
        ->get();

        $documentos = $programacionesRaw
            ->merge($informes)
            ->merge($subprogramaciones);

        foreach ($documentos as $doc) {
            $tipoarea = strtoupper(trim($doc->tipoarea ?? ''));
            if ($doc->accionnombre === 'INFORME FINAL') {
                $path = public_path("informesfinalesclientesita/{$cliente->id}/{$doc->document}");
            }
            elseif (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                $path = public_path("tramitesclientesita/{$cliente->id}/APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD/INFORMES/{$doc->document}");
            }
            else {
                $path = public_path("documentacionclientesita/{$cliente->id}/{$doc->document}");
            }
            if (file_exists($path)) {
                try {
                    $content = file_get_contents($path);
                    preg_match_all("/\/Type\s*\/Page\b/", $content, $matches);
                    $doc->nro_hojas = count($matches[0]);
                } catch (\Exception $e) {
                    $doc->nro_hojas = 'Error';
                }
            } else {
                $doc->nro_hojas = 'No encontrado';
            }
        }

        $programaciones = $documentos->groupBy(function ($item) {
            $tipoarea = strtoupper($item->tipoarea ?? '');
            if (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES'])) {
                return $item->tipo;
            }
            return $item->fechabateria;
        })
        ->map(function ($grupo) {
            return $grupo->sortBy(function ($item) {
                $tipoarea = $item->tipoarea ?? 'Z';

                $ordenTipoarea =
                    $tipoarea === 'ESPECIALIDAD' ? 0 :
                    ($tipoarea === 'INFORME FINAL' ? 1 :
                    (in_array($tipoarea, ['PROGRAMACIONES', 'INFORMES ADICIONALES']) ? 2 : 3));

                return [$ordenTipoarea, $item->areanombre, $item->accionnombre];
            })->values();
        });

        return view('admin.tramites.cartasprocapelrecalsegsolicitud', compact('id','cliente','apoderadoAsignado','idTramite','aseguradora',
        'afpgestora','matriculacliente','modelocartasreclamos','programaciones','apoderados'));
    }

    // GENERACION DE MISIVAS
    public function generarcartareclamo(Request $request, Cliente $cliente, Tramite $tramite)
    {
        $clienteid = $cliente->id;
        $tipoPdf = $request->input('tipo_pdf3');
        $fechaactual = Carbon::parse($request->input('fechaactual3'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $apoderadoId = $request->input('apoderado3');
        $nivelprocedimiento = $request->input('nivelprocedimiento3');
        $subProcedimiento = $request->input('subnivelprocedimiento3');
        $nombretramite = $request->input('tramite3');
        $apoderadoNombre = $request->input('apoderado3');
        $nombremedico = $request->input('nombremedico3');
        $cargomedico = $request->input('cargomedico3');
        $fontSize = $request->input('fontsize3', '15px');
        $marginSize = $request->input('marginsize3', '1.5cm 3cm 1.5cm 3cm');

        $tipoadjunto = $request->input('tipoadjunto3');
        $fechaadjuntoInput = $request->input('fechaadjunto3');
        $fechaadjunto = null;
        if ($fechaadjuntoInput) {
            $fechaadjunto = Carbon::parse($fechaadjuntoInput)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        $solmodificar3 = $request->input('solmodificar3');
        $nronota3 = $request->input('nronota3');
        $fechanota3Input = $request->input('fechanota3');
        $fechanota3 = null;
        if ($fechanota3Input) {
            $fechanota3 = Carbon::parse($fechanota3Input)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }
        $dirigidoa3 = $request->input('dirigidoa3');
        $estadolab3 = $request->input('estadolab3');
        $afiliadoa3 = $request->input('afiliadoa3');
        $textocomplementario3 = $request->input('textocomplementario3');

        $fechasolrevdictamenInput = $request->input('fechasolrevdictamen3');
        $fechasolrevdictamen = null;
        if ($fechasolrevdictamenInput) {
            $fechasolrevdictamen = Carbon::parse($fechasolrevdictamenInput)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }
        $nrorevisiondictamen = $request->input('nrorevisiondictamen3');
        $fecharevdictamenInput = $request->input('fecharevdictamen3');
        $fecharevdictamen = null;
        if ($fecharevdictamenInput) {
            $fecharevdictamen = Carbon::parse($fecharevdictamenInput)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }
        $porcentajedictamen = $request->input('porcentajedictamen3');
        $origendictamen = $request->input('origendictamen3');
        $motivoorigendictamen = $request->input('motivoorigendictamen3');

        $fechaconclusionprog3Input = $request->input('fechaconclusionprog3');
        $fechaconclusionprog3 = null;
        if ($fechaconclusionprog3Input) {
            $fechaconclusionprog3 = Carbon::parse($fechaconclusionprog3Input)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        /* PRIMERA CARTA SIT */
            $fecha1sitInput = $request->input('fecha1sit');
            $fecha1sit = null;
            if ($fecha1sitInput) {
                $fecha1sit = Carbon::parse($fecha1sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite1sit = $request->input('cite1sit');
            $fecharesp1sitInput = $request->input('fecharesp1sit');
            $fecharesp1sit = null;
            if ($fecharesp1sitInput) {
                $fecharesp1sit = Carbon::parse($fecharesp1sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fechacite1sitInput = $request->input('fechacite1sit');
            $fechacite1sit = null;
            if ($fechacite1sitInput) {
                $fechacite1sit = Carbon::parse($fechacite1sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto1sit = $request->input('texto1sit');
        //

        /* SEGUNDA CARTA SIT */
            $fecha2sitInput = $request->input('fecha2sit');
            $fecha2sit = null;
            if ($fecha2sitInput) {
                $fecha2sit = Carbon::parse($fecha2sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite2sit = $request->input('cite2sit');
            $fecharesp2sitInput = $request->input('fecharesp2sit');
            $fecharesp2sit = null;
            if ($fecharesp2sitInput) {
                $fecharesp2sit = Carbon::parse($fecharesp2sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fechacite2sitInput = $request->input('fechacite2sit');
            $fechacite2sit = null;
            if ($fechacite2sitInput) {
                $fechacite2sit = Carbon::parse($fechacite2sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto2sit = $request->input('texto2sit');
        //

        /* TERCERA CARTA SIT */
            $fecha3sitInput = $request->input('fecha3sit');
            $fecha3sit = null;
            if ($fecha3sitInput) {
                $fecha3sit = Carbon::parse($fecha3sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite3sit = $request->input('cite3sit');
            $fecharesp3sitInput = $request->input('fecharesp3sit');
            $fecharesp3sit = null;
            if ($fecharesp3sitInput) {
                $fecharesp3sit = Carbon::parse($fecharesp3sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fechacite3sitInput = $request->input('fechacite3sit');
            $fechacite3sit = null;
            if ($fechacite3sitInput) {
                $fechacite3sit = Carbon::parse($fechacite3sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto3sit = $request->input('texto3sit');
        //

        /* PRIMERA CARTA DE RECLAMO GP */
            $fecha1reclamogpInput = $request->input('fecha1reclamogp');
            $fecha1reclamogp = null;
            if ($fecha1reclamogpInput) {
                $fecha1reclamogp = Carbon::parse($fecha1reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite1reclamogp = $request->input('cite1reclamogp');
            $fechacite1reclamogpInput = $request->input('fechacite1reclamogp');
            $fechacite1reclamogp = null;
            if ($fechacite1reclamogpInput) {
                $fechacite1reclamogp = Carbon::parse($fechacite1reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fecharesp1reclamogpInput = $request->input('fecharesp1reclamogp');
            $fecharesp1reclamogp = null;
            if ($fecharesp1reclamogpInput) {
                $fecharesp1reclamogp = Carbon::parse($fecharesp1reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto1reclamogp = $request->input('texto1reclamogp');
        //

        /* PRIMERA CARTA DE RECLAMO APS */
            $fecha1reclamoapsInput = $request->input('fecha1reclamoaps');
            $fecha1reclamoaps = null;
            if ($fecha1reclamoapsInput) {
                $fecha1reclamoaps = Carbon::parse($fecha1reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite1reclamoaps = $request->input('cite1reclamoaps');
            $fechacite1reclamoapsInput = $request->input('fechacite1reclamoaps');
            $fechacite1reclamoaps = null;
            if ($fechacite1reclamoapsInput) {
                $fechacite1reclamoaps = Carbon::parse($fechacite1reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fecharesp1reclamoapsInput = $request->input('fecharesp1reclamoaps');
            $fecharesp1reclamoaps = null;
            if ($fecharesp1reclamoapsInput) {
                $fecharesp1reclamoaps = Carbon::parse($fecharesp1reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto1reclamoaps = $request->input('texto1reclamoaps');
        //

        /* SEGUNDA CARTA DE RECLAMO GP */
            $fecha2reclamogpInput = $request->input('fecha2reclamogp');
            $fecha2reclamogp = null;
            if ($fecha2reclamogpInput) {
                $fecha2reclamogp = Carbon::parse($fecha2reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite2reclamogp = $request->input('cite2reclamogp');
            $fechacite2reclamogpInput = $request->input('fechacite2reclamogp');
            $fechacite2reclamogp = null;
            if ($fechacite2reclamogpInput) {
                $fechacite2reclamogp = Carbon::parse($fechacite2reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fecharesp2reclamogpInput = $request->input('fecharesp2reclamogp');
            $fecharesp2reclamogp = null;
            if ($fecharesp2reclamogpInput) {
                $fecharesp2reclamogp = Carbon::parse($fecharesp2reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto2reclamogp = $request->input('texto2reclamogp');
        //

        /* SEGUNDA CARTA DE RECLAMO APS */
            $fecha2reclamoapsInput = $request->input('fecha2reclamoaps');
            $fecha2reclamoaps = null;
            if ($fecha2reclamoapsInput) {
                $fecha2reclamoaps = Carbon::parse($fecha2reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite2reclamoaps = $request->input('cite2reclamoaps');
            $fechacite2reclamoapsInput = $request->input('fechacite2reclamoaps');
            $fechacite2reclamoaps = null;
            if ($fechacite2reclamoapsInput) {
                $fechacite2reclamoaps = Carbon::parse($fechacite2reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fecharesp2reclamoapsInput = $request->input('fecharesp2reclamoaps');
            $fecharesp2reclamoaps = null;
            if ($fecharesp2reclamoapsInput) {
                $fecharesp2reclamoaps = Carbon::parse($fecharesp2reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto2reclamoaps = $request->input('texto2reclamoaps');
        //

        /* TERCERA CARTA DE RECLAMO GP */
            $fecha3reclamogpInput = $request->input('fecha3reclamogp');
            $fecha3reclamogp = null;
            if ($fecha3reclamogpInput) {
                $fecha3reclamogp = Carbon::parse($fecha3reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite3reclamogp = $request->input('cite3reclamogp');
            $fechacite3reclamogpInput = $request->input('fechacite3reclamogp');
            $fechacite3reclamogp = null;
            if ($fechacite3reclamogpInput) {
                $fechacite3reclamogp = Carbon::parse($fechacite3reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fecharesp3reclamogpInput = $request->input('fecharesp3reclamogp');
            $fecharesp3reclamogp = null;
            if ($fecharesp3reclamogpInput) {
                $fecharesp3reclamogp = Carbon::parse($fecharesp3reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto3reclamogp = $request->input('texto3reclamogp');
        //

        /* TERCERA CARTA DE RECLAMO APS */
            $fecha3reclamoapsInput = $request->input('fecha3reclamoaps');
            $fecha3reclamoaps = null;
            if ($fecha3reclamoapsInput) {
                $fecha3reclamoaps = Carbon::parse($fecha3reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite3reclamoaps = $request->input('cite3reclamoaps');
            $fechacite3reclamoapsInput = $request->input('fechacite3reclamoaps');
            $fechacite3reclamoaps = null;
            if ($fechacite3reclamoapsInput) {
                $fechacite3reclamoaps = Carbon::parse($fechacite3reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fecharesp3reclamoapsInput = $request->input('fecharesp3reclamoaps');
            $fecharesp3reclamoaps = null;
            if ($fecharesp3reclamoapsInput) {
                $fecharesp3reclamoaps = Carbon::parse($fecharesp3reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto3reclamoaps = $request->input('texto3reclamoaps');
        //

        $apoderado = DB::table('proveedoresservicios')
            ->where('razonsocial', $apoderadoNombre)
            ->first();
        if ($apoderado) {
            $nombre = $apoderado->razonsocial;
            $ci = $apoderado->ci;
            $ciexp = $apoderado->ciexp;
            $telefono = $apoderado->celularcorporativo;
            $sexo = strtolower($apoderado->sexo);
        }

        $generocliente = Cliente::where('id', $clienteid)->value('genero');
        $afiliadoTexto = (strtoupper($generocliente) === 'FEMENINO') ? 'de la Afiliada' : 'del Afiliado';

        // Obtener el primer registro de Requisitosubcliente para el cliente especificado
        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $clienteid)->where('servicio', $nombretramite)->first();
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        // FECHA REGISTRO INGRESO DE TRAMITE
        $fechaingresotramiteRegistro = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'INGRESO DE TRAMITE')
            ->whereIn('subprocedimiento', ['RECEPCIÓN DE TRAMITE', 'INCLUSIÓN DE PODER'])
            ->where('tramite', $nombretramite)
            ->orderBy('fechasubida', 'desc')
        ->first();
        $fechaingresotramite = null;
        if ($fechaingresotramiteRegistro && $fechaingresotramiteRegistro->fechasubida) {
            $fechaingresotramite = Carbon::parse($fechaingresotramiteRegistro->fechasubida)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        // FECHA RETORNO INGRESO DE TRAMITE
        $fecharetornoingresotramiteRegistro = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'INGRESO DE TRAMITE')
            ->whereIn('subprocedimiento', ['RECEPCIÓN DE TRAMITE', 'INCLUSIÓN DE PODER'])
            ->where('tramite', $nombretramite)
            ->orderBy('fecharetorno', 'desc')
        ->first();
        $fecharetornoingresotramite = null;
        if ($fecharetornoingresotramiteRegistro && $fecharetornoingresotramiteRegistro->fecharetorno) {
            $fecharetornoingresotramite = Carbon::parse($fecharetornoingresotramiteRegistro->fecharetorno)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        // FECHA RETORNO VALIDACIÓN DE PODER
        $fecharetornovalidacionRegistro = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'NOTIFICACIÓN DE PODER')
            ->where('subprocedimiento', 'VALIDACIÓN DE PODER')
            ->where('tramite', $nombretramite)
            ->orderBy('fecharetorno', 'desc')
        ->first();
        $fecharetornovalidacion = null;
        if ($fecharetornovalidacionRegistro && !empty($fecharetornovalidacionRegistro->fecharetorno)) {
            $fecharetornovalidacion = Carbon::parse($fecharetornovalidacionRegistro->fecharetorno)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        // FECHA REGISTRO FIRMA EAP
        $fechafirmaeapReg = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'FIRMA EAP')
            ->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')
            ->where('tramite', $nombretramite)
            ->orderBy('fechasubida', 'desc')
        ->first();
        $fechafirmaeap = null;
        if ($fechafirmaeapReg && $fechafirmaeapReg->fechasubida) {
            $fechafirmaeap = Carbon::parse($fechafirmaeapReg->fechasubida)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        // FECHA RETORNO FIRMA EAP
        $fecharetornofirmaeapReg = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'FIRMA EAP')
            ->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')
            ->where('tramite', $nombretramite)
            ->orderBy('fecharetorno', 'desc')
        ->first();
        $fecharetornofirmaeap = null;
        if ($fecharetornofirmaeapReg && $fecharetornofirmaeapReg->fecharetorno) {
            $fecharetornofirmaeap = Carbon::parse($fecharetornofirmaeapReg->fecharetorno)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }       
        
        // FECHA MODIFICACIÓN DE CITE
        $fechamodificacionciteReg = Tramite::where('clienteid', $clienteid)
            ->where('tipo', 'SOLICITUD')
            ->where('subprocedimiento', 'MODIFICACIÓN DE CITE')
            ->where('tramite', $nombretramite)
            ->orderBy('fechasubida', 'desc')
        ->first();
        $fechamodificacioncite = null;
        if ($fechamodificacionciteReg && $fechamodificacionciteReg->fechasubida) {
            $fechamodificacioncite = Carbon::parse($fechamodificacionciteReg->fechasubida)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }
        
        /* NUEVO 201125 */
        $fechaadjuntodocumentoReg = Tramite::where('clienteid', $clienteid)
            ->where('tipo', 'PROCEDIMIENTO')
            ->whereIn('subprocedimiento', [
                'ENTE GESTOR DE SALUD _ ADJUNTO DE DOCUMENTACIÓN MEDICA',
                'NOTIFICACIÓN TMC _ ADJUNTO DE DOCUMENTACIÓN MEDICA'
            ])
            ->where('tramite', $nombretramite)
            ->orderBy('fechasubida', 'desc')
            ->first();

        if (!$fechaadjuntodocumentoReg) {
            $fechaadjuntodocumentoReg = Tramite::where('clienteid', $clienteid)
                ->where('tipo', 'ARDJUNTO / ESPUESTA')
                ->whereIn('subprocedimiento', [
                    'ADJUNTO DE DOCUMENTOS',
                    'ADJUNTO DE DOCUMENTACIÓN MÉDICA'
                ])
                ->where('tramite', $nombretramite)
                ->orderBy('fechasubida', 'desc')
                ->first();
        }

        $fechaadjuntodocumento = null;
        if ($fechaadjuntodocumentoReg && $fechaadjuntodocumentoReg->fechasubida) {
            $fechaadjuntodocumento = Carbon::parse($fechaadjuntodocumentoReg->fechasubida)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        } else {
            if ($request->filled('fechaadjmedica')) {
                $fechaadjuntodocumento = Carbon::parse($request->fechaadjmedica)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
        }

        // FECHA MODIFICACIÓN DE CITE
        $fechasolcompraserviciosReg = Tramite::where('clienteid', $clienteid)
            ->where('tipo', 'SOLICITUD')
            ->where('subprocedimiento', 'COMPRA DE SERVICIOS')
            ->where('tramite', $nombretramite)
            ->orderBy('fechasubida', 'desc')
        ->first();
        $fechasolcompraservicios = null;
        if ($fechasolcompraserviciosReg && $fechasolcompraserviciosReg->fechasubida) {
            $fechasolcompraservicios = Carbon::parse($fechasolcompraserviciosReg->fechasubida)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        $pdfView = '';
        switch ($tipoPdf) {
            case 'PRIMERA CARTA SIT':
                $pdfView = 'admin.tramites.cartasyreclamos.sitprimeracarta';
                break;
            case 'SEGUNDA CARTA SIT':
                $pdfView = 'admin.tramites.cartasyreclamos.sitsegundacarta';
                break;
            case 'TERCERA CARTA SIT':
                $pdfView = 'admin.tramites.cartasyreclamos.sitterceracarta';
                break;
            case 'PRIMERA CARTA DE RECLAMO GP':
                $pdfView = 'admin.tramites.cartasyreclamos.gpreclamoprimeracarta';
                break;
            case 'PRIMERA CARTA DE RECLAMO APS':
                $pdfView = 'admin.tramites.cartasyreclamos.apsreclamoprimeracarta';
                break;
            case 'SEGUNDA CARTA DE RECLAMO GP':
                $pdfView = 'admin.tramites.cartasyreclamos.gpreclamosegundacarta';
                break;
            case 'SEGUNDA CARTA DE RECLAMO APS':
                $pdfView = 'admin.tramites.cartasyreclamos.apsreclamosegundacarta';
                break;
            case 'TERCERA CARTA DE RECLAMO GP':
                $pdfView = 'admin.tramites.cartasyreclamos.gpreclamoterceracarta';
                break;
            case 'TERCERA CARTA DE RECLAMO APS':
                $pdfView = 'admin.tramites.cartasyreclamos.apsreclamoterceracarta';
                break;
            case 'REITERACIÓN A CARTAS DE RECLAMO GP':
                $pdfView = 'admin.tramites.cartasyreclamos.gpreiteracioncartasreclamo';
                break;
            case 'REITERACIÓN A CARTAS DE RECLAMO APS':
                $pdfView = 'admin.tramites.cartasyreclamos.apsreiteracioncartasreclamo';
                break;
            default:
                return response()->json(['error' => 'Tipo de PDF no válido'], 400);
        }

        $pdf = PDF::loadView($pdfView, compact('cliente','fechaactual', 'fechaingresotramite','fechafirmaeap','afiliadoTexto',
        'nombre','ci','ciexp','telefono','sexo','numeropoder','nombretramite','fecharetornoingresotramite','subProcedimiento',
        'fontSize', 'marginSize','fecharetornovalidacion','fecharetornofirmaeap','fechaadjunto','tipoadjunto','solmodificar3',
        'nronota3','fechanota3','dirigidoa3','estadolab3','afiliadoa3','fechamodificacioncite','textocomplementario3',
        'fechaadjuntodocumento','fechaconclusionprog3','fechasolcompraservicios','nombremedico','cargomedico',
        'fecha1sit','cite1sit','fecharesp1sit','fechacite1sit','texto1sit',
        'fecha2sit','cite2sit','fecharesp2sit','fechacite2sit','texto2sit',
        'fecha3sit','cite3sit','fecharesp3sit','fechacite3sit','texto3sit',
        'fecha1reclamogp','cite1reclamogp','fechacite1reclamogp','fecharesp1reclamogp','texto1reclamogp',
        'fecha2reclamogp','cite2reclamogp','fechacite2reclamogp','fecharesp2reclamogp','texto2reclamogp',
        'fecha3reclamogp','cite3reclamogp','fechacite3reclamogp','fecharesp3reclamogp','texto3reclamogp',
        'fecha1reclamoaps','cite1reclamoaps','fechacite1reclamoaps','fecharesp1reclamoaps','texto1reclamoaps',
        'fecha2reclamoaps','cite2reclamoaps','fechacite2reclamoaps','fecharesp2reclamoaps','texto2reclamoaps',
        'fecha3reclamoaps','cite3reclamoaps','fechacite3reclamoaps','fecharesp3reclamoaps','texto3reclamoaps',
        'fechasolrevdictamen','nrorevisiondictamen','fecharevdictamen','porcentajedictamen','origendictamen','motivoorigendictamen'));

        // Generar un nombre único para el PDF basado en el tipo y la fecha
        $timestamp = now()->format('Ymd_His'); // Genera un timestamp para asegurar unicidad
        $pdfName = "{$tipoPdf}_{$cliente->nombrecompleto}_{$timestamp}.pdf";

        // Guardar el PDF en la carpeta del cliente
        $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$nombretramite}/CARTAS Y RECLAMOS");
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }
        $pdfPath = "{$carpetaCliente}/{$pdfName}";
        file_put_contents($pdfPath, $pdf->output());

        $registrosExistentes = Tramite::where('nivelprocedimiento', $nivelprocedimiento)
            ->where('clienteid', $clienteid)
            ->where('tramite', $request->tramite)
            ->where(function ($query) use ($subProcedimiento) {
                $query->where('subprocedimiento', $subProcedimiento)
                    ->orWhere('subprocedimiento', 'LIKE', $subProcedimiento . '%');
            })
            ->get();

        if ($registrosExistentes->isEmpty()) {
            $nro = 1;
        } else {
            $nro = $registrosExistentes->count() + 1;
        }

        Tramite::create([
            'usuarioid' => $request->usuarioid3,
            'usuarioregistro' => $request->usuarioregistro3,
            'fechasubida' => $request->fechasubida3,
            'tramite' => $request->tramite3,
            'idtramite' => $request->idtramite3,
            'apoderado' => $request->apoderado3,
            'nivelprocedimiento' => $nivelprocedimiento,
            'subprocedimiento' => $subProcedimiento,
            'tipocarta' => $tipoPdf,
            'tipo' => 'CARTA / RECLAMO',
            'nro' => $nro,
            'clienteid' => $clienteid,
            'clientenombre' => $cliente->nombrecompleto,
            'document' => $pdfName
        ]);

        // Descargar el PDF generado
        return response()->download($pdfPath);
    }
    public function generaradjuntoyrespuesta(Request $request, Cliente $cliente, Tramite $tramite)
    {
        $clienteid = $cliente->id;
        $tipoPdf = $request->input('tipo_pdf2');
        $nombretramite = $request->input('tramite2');
        $fechaactual = Carbon::parse($request->input('fechaactual2'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $nivelprocedimiento = $request->input('nivelprocedimiento2');
        $subProcedimiento = $request->input('tipo_pdf2');
        $nombremedico = $request->input('nombremedico2');
        $cargomedico = $request->input('cargomedico2');
        $textocomplementario = $request->input('textocomplementario2');
        $apoderadoNombre = $request->input('apoderado2');
        $documentoadjunto = $request->input('documentoadjunto2');
        $fontSize = $request->input('fontsize2', '15px');
        $marginSize = $request->input('marginsize2', '1.5cm 3cm 1.5cm 3cm');
        $notatecnicomedico = $request->input('notatecnicomedico2');

        $fechanotatecnicomedicoInput = $request->input('fechanotatecnicomedico2');
        $fechanotatecnicomedico = null;
        if ($fechanotatecnicomedicoInput) {
            $fechanotatecnicomedico = Carbon::parse($fechanotatecnicomedicoInput)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }


        /* NUEVO 221125 */
        $nrosolrevdic2 = $request->input('nrosolrevdic2');

        $fechasolrevdic2Input = $request->input('fechasolrevdic2');
        $fechasolrevdic2 = null;
        if ($fechasolrevdic2Input) {
            $fechasolrevdic2 = Carbon::parse($fechasolrevdic2Input)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        $fechapressolrevdic2Input = $request->input('fechapressolrevdic2');
        $fechapressolrevdic2 = null;
        if ($fechapressolrevdic2Input) {
            $fechapressolrevdic2 = Carbon::parse($fechapressolrevdic2Input)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        $fechaautoadmision2Input = $request->input('fechaautoadmision2');
        $fechaautoadmision2 = null;
        if ($fechaautoadmision2Input) {
            $fechaautoadmision2 = Carbon::parse($fechaautoadmision2Input)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }


        $especialistas = [];
        for ($i = 1; $i <= 10; $i++) {
            $especialista = $request->input("especialista2$i");
            $detalle = $request->input("detalle2$i");
            $cantidad = $request->input("cantidad2$i");

            if (!empty($especialista) && !empty($detalle) && !empty($cantidad)) {
                $especialistas[] = [
                    'especialista2' => $especialista,
                    'detalle2' => $detalle,
                    'cantidad2' => $cantidad,
                ];
            }
        }

        $apoderado = DB::table('proveedoresservicios')
            ->where('razonsocial', $apoderadoNombre)
            ->first();
        if ($apoderado) {
            $nombre = $apoderado->razonsocial;
            $ci = $apoderado->ci;
            $ciexp = $apoderado->ciexp;
            $telefono = $apoderado->celularcorporativo;
            $sexo = strtolower($apoderado->sexo);
        }

        // Obtener el primer registro de Requisitosubcliente para el cliente especificado
        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $clienteid)->where('servicio', $nombretramite)->first();
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        // Buscar el primer registro de Tramite que cumpla con las condiciones
        $fechaingresotramite = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'INGRESO DE TRAMITE')
            ->where('subprocedimiento', 'RECEPCIÓN DE TRAMITE')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $fechaingresotramite = $fechaingresotramite ? Carbon::parse($fechaingresotramite->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

        // Buscar el primer registro de Firma EAP que cumpla con las condiciones
        $fechafirmaeap = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'FIRMA EAP')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $fechafirmaeap = $fechafirmaeap ? Carbon::parse($fechafirmaeap->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

        $fechafirmaeap30 = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'FIRMA EAP')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $fechaeap30 = $fechafirmaeap ? Carbon::parse($fechafirmaeap30->fechasubida)->addDays(30)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
        
        $adyretecnicomedico = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
            ->where('subprocedimiento', 'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $adyretecnicomedico = $adyretecnicomedico ? Carbon::parse($adyretecnicomedico->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
        
        $adyrecomplementario = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
            ->where('subprocedimiento', 'ADJUNTO Y RESPUESTA COMPLEMENTARIO')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $adyrecomplementario = $adyrecomplementario ? Carbon::parse($adyrecomplementario->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
        
        $adyreactatmc = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
            ->where('subprocedimiento', 'ADJUNTO Y RESPUESTA AL ACTA TMC')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $adyreactatmc = $adyreactatmc ? Carbon::parse($adyreactatmc->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
        
        $adinformeempleador = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
            ->where('subprocedimiento', 'ADJUNTO INFORME DEL EMPLEADOR')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $adinformeempleador = $adinformeempleador ? Carbon::parse($adinformeempleador->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
        
        $addocumentacionmedica = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
            ->where('subprocedimiento', 'ADJUNTO DOCUMENTACIÓN MÉDICA')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $addocumentacionmedica = $addocumentacionmedica ? Carbon::parse($addocumentacionmedica->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

        $pdfView = '';
        switch ($tipoPdf) {
            case 'ADJUNTO DE DOCUMENTOS':
                $pdfView = 'admin.tramites.adjuntosyrespuestas.adjdocumentos';
                break;
            case 'ADJUNTO DE DOCUMENTACIÓN MÉDICA':
                $pdfView = 'admin.tramites.adjuntosyrespuestas.adjdocumentacionmedica';
                break;
            case 'ADJUNTO Y RESPUESTA DE INFORME DEL EMPLEADOR':
                $pdfView = 'admin.tramites.adjuntosyrespuestas.adjrespinformeempleador';
                break;
            case 'ADJUNTO Y RESPUESTA A NOTIFICACIÓN TMC':
                $pdfView = 'admin.tramites.adjuntosyrespuestas.adjrespnotificaciontmc';
                break;
            case 'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO':
                $pdfView = 'admin.tramites.adjuntosyrespuestas.adjresptecnicomedico';
                break;
            case 'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO':
                $pdfView = 'admin.tramites.adjuntosyrespuestas.adjrespcomplementario';
                break;
            default:
                return response()->json(['error' => 'Tipo de PDF no válido'], 400);
        }

        $generocliente = Cliente::where('id', $clienteid)->value('genero');
        $afiliadoTexto = (strtoupper($generocliente) === 'FEMENINO') ? 'de la Afiliada' : 'del Afiliado';

        $pdf = PDF::loadView($pdfView, compact('cliente', 'fechaactual', 'fechaingresotramite', 'fechafirmaeap',
            'numeropoder', 'fechaeap30', 'adyretecnicomedico', 'adyrecomplementario', 'nivelprocedimiento',
            'adyreactatmc', 'adinformeempleador', 'addocumentacionmedica', 'notatecnicomedico', 'fechanotatecnicomedico', 
            'especialistas','nombremedico','cargomedico','fontSize','marginSize','documentoadjunto','textocomplementario',
            'nombretramite','nombre','ci','ciexp','telefono','sexo','afiliadoTexto','nrosolrevdic2','fechasolrevdic2',
            'fechapressolrevdic2','fechaautoadmision2'));

        // Generar un nombre único para el PDF basado en el tipo y la fecha
        $timestamp = now()->format('Ymd_His'); // Genera un timestamp para asegurar unicidad
        $pdfName = "{$tipoPdf}_{$cliente->nombrecompleto}_{$timestamp}.pdf";

        // Guardar el PDF en la carpeta del cliente
        $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$nombretramite}/ADJUNTOS Y RESPUESTAS");
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }
        $pdfPath = "{$carpetaCliente}/{$pdfName}";
        file_put_contents($pdfPath, $pdf->output());

        $registrosExistentes = Tramite::where('nivelprocedimiento', $nivelprocedimiento)
            ->where('clienteid', $clienteid)
            ->where('tramite', $request->tramite)
            ->where(function ($query) use ($subProcedimiento) {
                $query->where('subprocedimiento', $subProcedimiento)
                    ->orWhere('subprocedimiento', 'LIKE', $subProcedimiento . '%');
            })
            ->get();

        if ($registrosExistentes->isEmpty()) {
            $nro = 1;
        } else {
            $nro = $registrosExistentes->count() + 1;
        }

        // Registrar el trámite en la base de datos
        Tramite::create([
            'usuarioid' => $request->usuarioid2,
            'usuarioregistro' => $request->usuarioregistro2,
            'fechasubida' => $request->fechasubida2,
            'tramite' => $request->tramite2,
            'idtramite' =>$request->idtramite2,
            'apoderado' => $request->apoderado2,
            'nivelprocedimiento' => $nivelprocedimiento,
            'subprocedimiento' => $subProcedimiento,
            /* 'tipocarta' => $request->tipocarta2, */
            'tipo' => 'ADJUNTO / RESPUESTA',
            'nro' => $nro,
            'clienteid' => $clienteid,
            'clientenombre' => $cliente->nombrecompleto,
            'document' => $pdfName
        ]);
        // Descargar el PDF generado
        return response()->download($pdfPath);
    }
    /* NUEVO 011125 */
    public function generarsolicitud(Request $request, Cliente $cliente, Tramite $tramite)
    {
        $clienteid = $cliente->id;
        $tipoPdf = $request->input('tipo_pdf');
        $fechaactual = Carbon::parse($request->input('fechaactual'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $tipocartareclamo = $request->input('tipocartareclamo');
        $folio = $request->input('folio');
        $cambioactualizacion = $request->input('cambioactualizacion');
        $matricula = $request->input('matricula');
        $nombremedico = $request->input('nombremedico');
        $nivelprocedimiento = $request->input('nivelprocedimiento');
        $cargomedico = $request->input('cargomedico');
        $nombretramite = $request->input('tramite');
        $aseguradora = $request->input('aseguradora');
        $afpgestora = $request->input('afpgestora');
        $campodirigidoa = $request->input('campodirigidoa');
        $campoestadolab = $request->input('campoestadolab');
        $campoafiliadoa = $request->input('campoafiliadoa');
        $solicitudmodificar = $request->input('solicitudmodificar');
        $medicotratante = $request->input('medicotratante');
        $especialidadinforme = $request->input('especialidadinforme');
        $emisor = $request->input('emisor');
        $fechainformeestudio = Carbon::parse($request->input('fechainformeestudio'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $notatecnicomedico = $request->input('notatecnicomedico');
        $fechanotatecnicomedico = Carbon::parse($request->input('fechanotatecnicomedico'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $texto1 = $request->input('texto1');
        $fechacontrato = Carbon::parse($request->input('fechacontrato'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $firmadoen = $request->input('firmadoen');
        $nrodictamen = $request->input('nrodictamen');
        $fechatramite = Carbon::parse($request->input('fechatramite'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $notificadoen = $request->input('notificadoen');
        $tipoorigen = $request->input('tipoorigen');
        $origendictamen = $request->input('origendictamen');
        $entidadcalificante = $request->input('entidadcalificante');
        $porcentajedictamen = $request->input('porcentajedictamen');
        $fechanotificacion = Carbon::parse($request->input('fechanotificacion'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $nropasaporte = $request->input('nropasaporte');
        $nrocua1 = $request->input('nrocua1');
        $nombreafp1 = $request->input('nombreafp1');
        $nroci1 = $request->input('nroci1');
        $nrocua2 = $request->input('nrocua2');
        $nombreafp2 = $request->input('nombreafp2');
        $nroci2 = $request->input('nroci2');
        $nrocuaunificado = $request->input('nrocuaunificado');
        $nrociunificado = $request->input('nrociunificado');
        $fechainiciotramite = Carbon::parse($request->input('fechainiciotramite'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $fechafirmaeap2 = Carbon::parse($request->input('fechafirmaeap'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $fechaformulario = Carbon::parse($request->input('fechaformulario'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $fecharesolucion = Carbon::parse($request->input('fecharesolucion'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $nroresolucion = $request->input('nroresolucion');

        /* NUEVO 11112025 */
        $txtcomple1 = $request->input('txtcomple1');
        $txtcomple2 = $request->input('txtcomple2');
        $txtcomple3 = $request->input('txtcomple3');
        $tituloop1 = $request->input('tituloop1');
        $tituloop2 = $request->input('tituloop2');

        $empresacliente = Cliente::where('id', $cliente->id)
        ->value('empresa');

        $adjuntos = [];
        for ($i = 1; $i <= 10; $i++) {
            $requerimiento = $request->input("requerimiento$i");
            $tipo = $request->input("tipo$i");
            if (!empty($requerimiento) && !empty($tipo)) {
                $adjuntos[] = [
                    'requerimiento' => $requerimiento,
                    'tipo' => $tipo,
                ];
            }
        }

        $especialistas = [];
        for ($i = 1; $i <= 10; $i++) {
            $especialista = $request->input("especialista$i");
            $detalle = $request->input("detalle$i");
            $cantidad = $request->input("cantidad$i");
            if (!empty($especialista) && !empty($detalle) && !empty($cantidad)) {
                $especialistas[] = [
                    'especialista' => $especialista,
                    'detalle' => $detalle,
                    'cantidad' => $cantidad,
                ];
            }
        }

        $adjuntos2 = [];
        for ($i = 1; $i <= 10; $i++) {
            $requerimiento2 = $request->input("requerimiento2$i");
            $tipo2 = $request->input("tipo2$i");
            if (!empty($requerimiento2) && !empty($tipo2)) {
                $adjuntos2[] = [
                    'requerimiento2' => $requerimiento2,
                    'tipo2' => $tipo2,
                ];
            }
        }

        $informaciones = [];
        for ($i = 1; $i <= 10; $i++) {
            $informacion = $request->input("informacion$i");
            if (!empty($informacion)) {
                $informaciones[] = [
                    'informacion' => $informacion,
                ];
            }
        }

        $abonos = [];
        for ($i = 1; $i <= 10; $i++) {
            $entidadbancaria = $request->input("entidadbancaria$i");
            $tipocuenta = $request->input("tipocuenta$i");
            $nrocuenta = $request->input("nrocuenta$i");
            if (!empty($entidadbancaria) && !empty($tipocuenta) && !empty($nrocuenta)) {
                $abonos[] = [
                    'entidadbancaria' => $entidadbancaria,
                    'tipocuenta' => $tipocuenta,
                    'nrocuenta' => $nrocuenta,
                ];
            }
        }

        $ceapasaportes = [];
        for ($i = 1; $i <= 10; $i++) {
            $appaterno = $request->input("appaterno$i");
            $apmaterno = $request->input("apmaterno$i");
            $primernombre = $request->input("primernombre$i");
            $segundonombre = $request->input("segundonombre$i");
            $cua = $request->input("cua$i");
            $ce = $request->input("ce$i");
            $pasaporte = $request->input("pasaporte$i");
            if (!empty($appaterno) && !empty($apmaterno) /* && !empty($primernombre) && !empty($segundonombre) */ && !empty($cua) && !empty($ce)) {
                $ceapasaportes[] = [
                    'appaterno' => $appaterno,
                    'apmaterno' => $apmaterno,
                    'primernombre' => $primernombre,
                    'segundonombre' => $segundonombre,
                    'cua' => $cua,
                    'ce' => $ce,
                    'pasaporte' => $pasaporte,
                ];
            }
        }

        $ceapasaportes2 = [];
        for ($i = 1; $i <= 10; $i++) {
            $appaterno2 = $request->input("appaterno$i");
            $apmaterno2 = $request->input("apmaterno$i");
            $primernombre2 = $request->input("primernombre$i");
            $segundonombre2 = $request->input("segundonombre$i");
            $cua2 = $request->input("cua$i");
            $ce2 = $request->input("ce$i");
            $pasaporte2 = $request->input("pasaporte$i");
            if (!empty($appaterno2) && !empty($apmaterno2) /* && !empty($primernombre2) && !empty($segundonombre2) */ && !empty($cua2) && !empty($pasaporte2)) {
                $ceapasaportes2[] = [
                    'appaterno2' => $appaterno2,
                    'apmaterno2' => $apmaterno2,
                    'primernombre2' => $primernombre2,
                    'segundonombre2' => $segundonombre2,
                    'cua2' => $cua2,
                    'ce2' => $ce2,
                    'pasaporte2' => $pasaporte2,
                ];
            }
        }

        $unificacioncuas = [];
        for ($i = 1; $i <= 10; $i++) {
            $appaterno3 = $request->input("appaterno$i");
            $apmaterno3 = $request->input("apmaterno$i");
            $primernombre3 = $request->input("primernombre$i");
            $segundonombre3 = $request->input("segundonombre$i");
            $fechanacimiento3 = $request->input("fechanacimiento$i");
            $ci3 = $request->input("ci$i");
            $cua3 = $request->input("cua$i");
            $cuaotro3 = $request->input("cuaotro$i");
            if (!empty($appaterno3) && !empty($apmaterno3) /* && !empty($primernombre3) && !empty($segundonombre3) */ && !empty($fechanacimiento3) && !empty($ci3) && !empty($cuaotro3)) {
                $unificacioncuas[] = [
                    'appaterno3' => $appaterno3,
                    'apmaterno3' => $apmaterno3,
                    'primernombre3' => $primernombre3,
                    'segundonombre3' => $segundonombre3,
                    'fechanacimiento3' => $fechanacimiento3,
                    'ci3' => $ci3,
                    'cua3' => $cua3,
                    'cuaotro3' => $cuaotro3,
                ];
            }
        }

        $cambiounificacioncuas = [];
        for ($i = 1; $i <= 10; $i++) {
            $appaterno4 = $request->input("appaterno$i");
            $apmaterno4 = $request->input("apmaterno$i");
            $primernombre4 = $request->input("primernombre$i");
            $segundonombre4 = $request->input("segundonombre$i");
            $fechanacimiento4 = $request->input("fechanacimiento$i");
            $ci4 = $request->input("ci$i");
            $cua4 = $request->input("cua$i");
            $cuaotro4 = $request->input("cuaotro$i");
            if (!empty($appaterno4) && !empty($apmaterno4) /* && !empty($primernombre4) && !empty($segundonombre4) */ && !empty($fechanacimiento4) && !empty($ci4) && !empty($cua4) && empty($cuaotro4)) {
                $cambiounificacioncuas[] = [
                    'appaterno4' => $appaterno4,
                    'apmaterno4' => $apmaterno4,
                    'primernombre4' => $primernombre4,
                    'segundonombre4' => $segundonombre4,
                    'fechanacimiento4' => $fechanacimiento4,
                    'ci4' => $ci4,
                    'cua4' => $cua4,
                    'cuaotro4' => $cuaotro4,
                ];
            }
        }

        $prestaciones = [];
        for ($i = 1; $i <= 10; $i++) {
            $prestacion = $request->input("prestacion$i");
            $periodo = $request->input("periodo$i");

            if (!empty($prestacion) && !empty($periodo)) {
                // Convertir de 'YYYY-MM' a 'MM/YYYY'
                $periodo_formateado = date('m/Y', strtotime($periodo . '-01'));

                $prestaciones[] = [
                    'prestacion' => $prestacion,
                    'periodo' => $periodo_formateado,
                ];
            }
        }

        /* NUEVO 11112025 */
        $opcionesuno = [];
        for ($i = 1; $i <= 20; $i++) {
            $opcionuno = $request->input("opcionuno$i");
            if (!empty($opcionuno)) {
                $opcionesuno[] = [
                    'opcionuno' => $opcionuno,
                ];
            }
        }

        $opcionesdos = [];
        for ($i = 1; $i <= 20; $i++) {
            $opciondos = $request->input("opciondos$i");
            if (!empty($opciondos)) {
                $opcionesdos[] = [
                    'opciondos' => $opciondos,
                ];
            }
        }

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $clienteid)->where('servicio', $nombretramite)->first();
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;
        $fechaingresotramite = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'INGRESO DE TRAMITE')
            ->where('subprocedimiento', 'RECEPCIÓN DE TRAMITE')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $fechaingresotramite = $fechaingresotramite ? Carbon::parse($fechaingresotramite->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

        $fechafirmaeap = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'FIRMA EAP')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $fechafirmaeap = $fechafirmaeap ? Carbon::parse($fechafirmaeap->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

        $fechafirmaeap30 = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'FIRMA EAP')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $fechaeap30 = $fechafirmaeap ? Carbon::parse($fechafirmaeap30->fechasubida)->addDays(30)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
        
        $nivelProcedimiento = '';
        $subProcedimiento = '';

        $nombre = '';
        $ci = '';
        $ciexp = '';
        $telefono = '';
        $sexo = '';

        if ($emisor === 'CLIENTE') {
            $nombre = $cliente->nombrecompleto;
            $ci = $cliente->ci;
            $ciexp = $cliente->ciexp;
            $telefono = Str::startsWith($cliente->celular, '591') 
            ? substr($cliente->celular, 3) 
            : $cliente->celular;
            $sexo = strtolower($cliente->genero);
        } elseif ($emisor === 'APODERADO') {
            $apoderadoNombre = $request->input('apoderado');
            $apoderado = DB::table('proveedoresservicios')
                ->where('razonsocial', $apoderadoNombre)
                ->first();
            if ($apoderado) {
                $nombre = $apoderado->razonsocial;
                $ci = $apoderado->ci;
                $ciexp = $apoderado->ciexp;
                $telefono = $apoderado->celularcorporativo;
                $sexo = strtolower($apoderado->sexo);
            }
        }

        $pdfView = '';
        switch ($tipoPdf) {
            case 'EVALUACIÓN POR MEDICINA DEL TRABAJO':
                $pdfView = 'admin.tramites.solicitudes.evaluacionmedicinatrabajo';
                $subProcedimiento = 'EVALUACIÓN POR MEDICINA DEL TRABAJO';
                break;
            case 'INCLUSIÓN DE INFORMES MÉDICOS':
                $pdfView = 'admin.tramites.solicitudes.inclusioninformesmedicos';
                $subProcedimiento = 'INCLUSIÓN DE INFORMES MÉDICOS';
                break;
            case 'HISTORIA CLÍNICA LEGALIZADA':
                $pdfView = 'admin.tramites.solicitudes.historiaclinicalegalizada';
                $subProcedimiento = 'HISTORIA CLÍNICA LEGALIZADA';
                break;
            case 'ACTUALIZACIÓN DE DATOS':
                $pdfView = 'admin.tramites.solicitudes.actualizaciondatos';
                $subProcedimiento = 'ACTUALIZACIÓN DE DATOS';
                break;
            case 'COMPRA DE SERVICIOS':
                $pdfView = 'admin.tramites.solicitudes.compraservicios';
                $subProcedimiento = 'COMPRA DE SERVICIOS';
                break;
            case 'INFORME DEL EMPLEADOR':
                $pdfView = 'admin.tramites.solicitudes.informeempleador';
                $subProcedimiento = 'INFORME DEL EMPLEADOR';
                break;
            case 'MODIFICACIÓN DE CITE':
                $pdfView = 'admin.tramites.solicitudes.modificacioncite';
                $subProcedimiento = 'MODIFICACIÓN DE CITE';
                break;
            case 'INFORME MÉDICO':
                $pdfView = 'admin.tramites.solicitudes.informemedico';
                $subProcedimiento = 'INFORME MÉDICO';
                break;
            case 'ABONO EN CUENTA':
                $pdfView = 'admin.tramites.solicitudes.abonoencuenta';
                $subProcedimiento = 'ABONO EN CUENTA';
                break;
            case 'COPIA LEGALIZADA DE CONTRATO':
                $pdfView = 'admin.tramites.solicitudes.copialegalizadacontrato';
                $subProcedimiento = 'COPIA LEGALIZADA DE CONTRATO';
                break;
            case 'NO DESCUENTO 3%':
                $pdfView = 'admin.tramites.solicitudes.nodescuentotresporciento';
                $subProcedimiento = 'NO DESCUENTO 3%';
                break;
            case 'COPIA LEGALIZADA DE DICTAMEN':
                $pdfView = 'admin.tramites.solicitudes.copialegalizadadictamen';
                $subProcedimiento = 'COPIA LEGALIZADA DE DICTAMEN';
                break;
            case 'REACTIVACIÓN DE TRÁMITE':
                $pdfView = 'admin.tramites.solicitudes.reactivaciontramite';
                $subProcedimiento = 'REACTIVACIÓN DE TRÁMITE';
                break;
            case 'RECALIFICACIÓN DE DICTAMEN':
                $pdfView = 'admin.tramites.solicitudes.recalificaciondictamen';
                $subProcedimiento = 'RECALIFICACIÓN DE DICTAMEN';
                break;
            case 'CAMBIO DE C.E. A PASAPORTE':
                $pdfView = 'admin.tramites.solicitudes.cambioceapasaporte';
                $subProcedimiento = 'CAMBIO DE C.E. A PASAPORTE';
                break;
            case 'UNIFICACIÓN DE CUA':
                $pdfView = 'admin.tramites.solicitudes.unificacioncua';
                $subProcedimiento = 'UNIFICACIÓN DE CUA';
                break;
            case 'ACTA DE COBROS':
                $pdfView = 'admin.tramites.solicitudes.actacobros';
                $subProcedimiento = 'ACTA DE COBROS';
                break;
            /* NUEVO 11112025 */
            case 'REVISIÓN DE DICTAMEN DE INVALIDEZ':
                $pdfView = 'admin.tramites.solicitudes.revisiondictameninvalidez';
                $subProcedimiento = 'REVISIÓN DE DICTAMEN DE INVALIDEZ';
                break;
            default:
                return response()->json(['error' => 'Tipo de PDF no válido'], 400);
        }

        $generocliente = Cliente::where('id', $clienteid)->value('genero');
        $afiliadoTexto = (strtoupper($generocliente) === 'FEMENINO') ? 'de la Afiliada' : 'del Afiliado';

        $pdf = PDF::loadView($pdfView, compact('cliente', 'fechaactual', 'fechaingresotramite', 'fechafirmaeap', 'tipocartareclamo', 'numeropoder', 'fechaeap30', 
            'folio', 'cambioactualizacion', 'notatecnicomedico', 'fechanotatecnicomedico', 'adjuntos', 'matricula', 
            'fechainformeestudio', 'especialistas', 'adjuntos2','nombremedico','cargomedico','aseguradora','afpgestora','tramite',
            'nombretramite','nombre', 'ci', 'ciexp', 'telefono', 'emisor', 'sexo', 'nivelprocedimiento', 'empresacliente', 'texto1',
            'campodirigidoa','campoestadolab','campoafiliadoa','solicitudmodificar','informaciones','medicotratante','especialidadinforme', 
            'abonos','fechacontrato','firmadoen','nrodictamen','fechatramite','fechanotificacion','notificadoen','tipoorigen',
            'origendictamen','entidadcalificante','porcentajedictamen', 'nropasaporte','ceapasaportes','ceapasaportes2',
            'nrocua1','nombreafp1','nroci1','nrocua2','nombreafp2','nroci2','nrocuaunificado','nrociunificado',
            'unificacioncuas','cambiounificacioncuas','prestaciones','fechainiciotramite','fechafirmaeap2','fechaformulario',
            'fecharesolucion','nroresolucion','txtcomple1','txtcomple2','txtcomple3','tituloop1','tituloop2',
            'opcionesuno','opcionesdos','afiliadoTexto'));

        $timestamp = now()->format('Ymd_His');
        $pdfName = "{$tipoPdf}_{$cliente->nombrecompleto}_{$timestamp}.pdf";

        $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$nombretramite}/SOLICITUDES");
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }
        $pdfPath = "{$carpetaCliente}/{$pdfName}";
        file_put_contents($pdfPath, $pdf->output());
        
        $registrosExistentes = Tramite::where('nivelprocedimiento', $nivelprocedimiento)
            ->where('clienteid', $clienteid)
            ->where('tramite', $request->tramite)
            ->where(function ($query) use ($subProcedimiento) {
                $query->where('subprocedimiento', $subProcedimiento)
                    ->orWhere('subprocedimiento', 'LIKE', $subProcedimiento . '%');
            })
            ->get();

        if ($registrosExistentes->isEmpty()) {
            $nro = 1;
        } else {
            $nro = $registrosExistentes->count() + 1;
        }

        Tramite::create([
            'usuarioid' => $request->usuarioid,
            'usuarioregistro' => $request->usuarioregistro,
            'fechasubida' => $request->fechasubida,
            'tramite' => $request->tramite,
            'idtramite' => $request->idtramite,
            'apoderado' => $request->apoderado,
            'nivelprocedimiento' => $nivelprocedimiento,
            'subprocedimiento' => $subProcedimiento,
            'tipocarta' => $request->tipocarta,
            'tipo' => 'SOLICITUD',
            'nro' => $nro,
            'clienteid' => $clienteid,
            'clientenombre' => $cliente->nombrecompleto,
            'document' => $pdfName
        ]);

        return response()->download($pdfPath);
    }


    public function guardarVariasSolicitudes(Request $request, Cliente $cliente)
    {
        $solicitudes = $request->input('solicitudes', []);
        $apoderado = $request->input('apoderado');
        $tramite = $request->input('tramite');
        $idtramite = $request->input('idtramite');

        $carpetaDestino = public_path("tramitesclientesita/{$cliente->id}/{$tramite}/SOLICITUDES");
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0755, true);
        }

        foreach ($solicitudes as $index => $sol) {
            $nivelprocedimiento = $sol['nivelprocedimiento'] ?? null;
            $subProcedimiento   = $sol['subprocedimiento'] ?? null;

            $registrosExistentes = Tramite::where('nivelprocedimiento', $nivelprocedimiento)
                ->where('clienteid', $cliente->id)
                ->where('tramite', $tramite)
                ->where(function ($query) use ($subProcedimiento) {
                    $query->where('subprocedimiento', $subProcedimiento)
                        ->orWhere('subprocedimiento', 'LIKE', $subProcedimiento . '%');
                })
                ->get();

            $nro = $registrosExistentes->isEmpty() ? 1 : $registrosExistentes->count() + 1;

            $archivo1 = $request->file("solicitudes.{$index}.document");
            $archivo2 = $request->file("solicitudes.{$index}.document2");
            $nombreArchivo1 = null;
            $nombreArchivo2 = null;

            if ($archivo1) {
                $timestamp = now()->format('Ymd_His');
                $unique = uniqid();
                $nombreArchivo1 = "SOLICITUD_{$cliente->id}_{$timestamp}_{$unique}.pdf";
                $archivo1->move($carpetaDestino, $nombreArchivo1);
            }

            if ($archivo2) {
                $timestamp = now()->format('Ymd_His');
                $unique = uniqid();
                $nombreArchivo2 = "RSOLICITUD_{$cliente->id}_{$timestamp}_{$unique}.pdf";
                $archivo2->move($carpetaDestino, $nombreArchivo2);
            }

            Tramite::create([
                'clienteid'          => $cliente->id,
                'clientenombre'      => $cliente->nombrecompleto,
                'tramite'            => $tramite,
                'idtramite'          => $idtramite,
                'apoderado'          => $apoderado,
                'nivelprocedimiento' => $nivelprocedimiento,
                'subprocedimiento'   => $subProcedimiento,
                'observaciones'      => $sol['observacion'] ?? null,
                'citenota'           => $sol['citenota'] ?? null,
                'fechacitenota'      => $sol['fechacitenota'] ?? null,
                'fechainclusion'     => $sol['fechainclusion'] ?? null,
                'document'           => $nombreArchivo1,
                'document2'          => $nombreArchivo2,
                'tipo'               => 'SOLICITUD',
                'nro'                => $nro,
                'fechasubida'        => $sol['fechasubida'] ?? null,
                'usuarioregistro'    => auth()->user()->name,
                'usuarioid'          => auth()->id(),
            ]);
        }

        return redirect()->back()->with('info', 'Solicitudes guardadas correctamente.');
    }
    public function guardarVariasAdjuntos(Request $request, Cliente $cliente)
    {
        $solicitudes = $request->input('solicitudes', []);
        $apoderado = $request->input('apoderado');
        $tramite = $request->input('tramite');
        $idtramite = $request->input('idtramite');

        $carpetaDestino = public_path("tramitesclientesita/{$cliente->id}/{$tramite}/ADJUNTOS Y RESPUESTAS");
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0755, true);
        }

        foreach ($solicitudes as $index => $sol) {
            $nivelprocedimiento = $sol['nivelprocedimiento'] ?? null;
            $subProcedimiento   = $sol['subprocedimiento'] ?? null;

            $registrosExistentes = Tramite::where('nivelprocedimiento', $nivelprocedimiento)
                ->where('clienteid', $cliente->id)
                ->where('tramite', $tramite)
                ->where(function ($query) use ($subProcedimiento) {
                    $query->where('subprocedimiento', $subProcedimiento)
                        ->orWhere('subprocedimiento', 'LIKE', $subProcedimiento . '%');
                })
                ->get();

            $nro = $registrosExistentes->isEmpty() ? 1 : $registrosExistentes->count() + 1;

            $archivo1 = $request->file("solicitudes.{$index}.document");
            $archivo2 = $request->file("solicitudes.{$index}.document2");
            $nombreArchivo1 = null;
            $nombreArchivo2 = null;

            if ($archivo1) {
                $timestamp = now()->format('Ymd_His');
                $unique = uniqid();
                $nombreArchivo1 = "ADJUNTO-RESPUESTA_{$cliente->id}_{$timestamp}_{$unique}.pdf";
                $archivo1->move($carpetaDestino, $nombreArchivo1);
            }

            if ($archivo2) {
                $timestamp = now()->format('Ymd_His');
                $unique = uniqid();
                $nombreArchivo2 = "RADJUNTO-RESPUESTA_{$cliente->id}_{$timestamp}_{$unique}.pdf";
                $archivo2->move($carpetaDestino, $nombreArchivo2);
            }

            Tramite::create([
                'clienteid'          => $cliente->id,
                'clientenombre'      => $cliente->nombrecompleto,
                'tramite'            => $tramite,
                'idtramite'          => $idtramite,
                'apoderado'          => $apoderado,
                'nivelprocedimiento' => $nivelprocedimiento,
                'subprocedimiento'   => $subProcedimiento,
                'observaciones'      => $sol['observacion'] ?? null,
                'citenota'           => $sol['citenota'] ?? null,
                'fechacitenota'      => $sol['fechacitenota'] ?? null,
                'fechainclusion'     => $sol['fechainclusion'] ?? null,
                'document'           => $nombreArchivo1,
                'document2'          => $nombreArchivo2,
                'tipo'               => 'ADJUNTO / RESPUESTA',
                'nro'                => $nro,
                'fechasubida'        => $sol['fechasubida'] ?? null,
                'usuarioregistro'    => auth()->user()->name,
                'usuarioid'          => auth()->id(),
            ]);
        }

        return redirect()->back()->with('info', 'Adjuntos/Respuestas guardadas correctamente.');
    }
    public function guardarVariasCartas(Request $request, Cliente $cliente)
    {
        $solicitudes = $request->input('solicitudes', []);
        $apoderado = $request->input('apoderado');
        $tramite = $request->input('tramite');
        $idtramite = $request->input('idtramite');

        $carpetaDestino = public_path("tramitesclientesita/{$cliente->id}/{$tramite}/CARTAS Y RECLAMOS");
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0755, true);
        }

        foreach ($solicitudes as $index => $sol) {
            $nivelprocedimiento = $sol['nivelprocedimiento'] ?? null;
            $subProcedimiento   = $sol['subprocedimiento'] ?? null;
            $tipocarta   = $sol['tipocarta'] ?? null;

            $registrosExistentes = Tramite::where('nivelprocedimiento', $nivelprocedimiento)
                ->where('clienteid', $cliente->id)
                ->where('tramite', $tramite)
                ->where(function ($query) use ($subProcedimiento) {
                    $query->where('subprocedimiento', $subProcedimiento)
                        ->orWhere('subprocedimiento', 'LIKE', $subProcedimiento . '%');
                })
                ->get();

            $nro = $registrosExistentes->isEmpty() ? 1 : $registrosExistentes->count() + 1;

            $archivo1 = $request->file("solicitudes.{$index}.document");
            $archivo2 = $request->file("solicitudes.{$index}.document2");
            $archivo3 = $request->file("solicitudes.{$index}.document3");
            $nombreArchivo1 = null;
            $nombreArchivo2 = null;
            $nombreArchivo3 = null;

            if ($archivo1) {
                $timestamp = now()->format('Ymd_His');
                $unique = uniqid();
                $nombreArchivo1 = "CARTA-RECLAMO_{$cliente->id}_{$timestamp}_{$unique}.pdf";
                $archivo1->move($carpetaDestino, $nombreArchivo1);
            }

            if ($archivo2) {
                $timestamp = now()->format('Ymd_His');
                $unique = uniqid();
                $nombreArchivo2 = "FCARTA-RECLAMO_{$cliente->id}_{$timestamp}_{$unique}.pdf";
                $archivo2->move($carpetaDestino, $nombreArchivo2);
            }

            if ($archivo3) {
                $timestamp = now()->format('Ymd_His');
                $unique = uniqid();
                $nombreArchivo3 = "RCARTA-RECLAMO_{$cliente->id}_{$timestamp}_{$unique}.pdf";
                $archivo3->move($carpetaDestino, $nombreArchivo3);
            }

            Tramite::create([
                'clienteid'          => $cliente->id,
                'clientenombre'      => $cliente->nombrecompleto,
                'tramite'            => $tramite,
                'idtramite'          => $idtramite,
                'apoderado'          => $apoderado,
                'nivelprocedimiento' => $nivelprocedimiento,
                'subprocedimiento'   => $subProcedimiento,
                'tipocarta'          => $tipocarta,
                'observaciones'      => $sol['observacion'] ?? null,
                'citenota'           => $sol['citenota'] ?? null,
                'fechacitenota'      => $sol['fechacitenota'] ?? null,
                'fechainclusion'     => $sol['fechainclusion'] ?? null,
                'document'           => $nombreArchivo1,
                'document2'          => $nombreArchivo2,
                'document3'          => $nombreArchivo3,
                'tipo'               => 'CARTA / RECLAMO',
                'nro'                => $nro,
                'fechasubida'        => $sol['fechasubida'] ?? null,
                'usuarioregistro'    => auth()->user()->name,
                'usuarioid'          => auth()->id(),
            ]);
        }

        return redirect()->back()->with('info', 'Adjuntos/Respuestas guardadas correctamente.');
    }

    public function actualizarSolicitud(Request $request, $id)
    {
        $solicitud = DB::table('procedimientotramites')->where('id', $id)->first();

        if (!$solicitud) {
            return back()->with('error', 'Registro no encontrado');
        }

        $tramite = $request->input("tramite");

        $data = [
            'observaciones' => $request->input("observaciones_$id"),
            'citenota' => $request->input("citenota_$id"),
            'fechacitenota' => $request->input("fechacitenota_$id"),
            'fechainclusion' => $request->input("fechainclusion_$id"),
            'updated_at' => now(),
        ];

        if ($request->hasFile("document2_$id")) {
            $archivo = $request->file("document2_$id");
            $nombreArchivo = Str::random(10) . '_' . $archivo->getClientOriginalName();
            $archivo->move(public_path("tramitesclientesita/{$solicitud->clienteid}/{$tramite}/SOLICITUDES"), $nombreArchivo);
            $data['document2'] = $nombreArchivo;
        }

        DB::table('procedimientotramites')->where('id', $id)->update($data);

        return back()->with('info', 'Misiva actualizada correctamente.');
    }
    public function actualizarAdjunto(Request $request, $id)
    {
        $solicitud = DB::table('procedimientotramites')->where('id', $id)->first();

        if (!$solicitud) {
            return back()->with('error', 'Registro no encontrado');
        }

        $tramite = $request->input("tramite");

        $data = [
            'observaciones' => $request->input("observaciones_$id"),
            'citenota' => $request->input("citenota_$id"),
            'fechacitenota' => $request->input("fechacitenota_$id"),
            'fechainclusion' => $request->input("fechainclusion_$id"),
            'updated_at' => now(),
        ];

        if ($request->hasFile("document2_$id")) {
            $archivo = $request->file("document2_$id");
            $nombreArchivo = Str::random(10) . '_' . $archivo->getClientOriginalName();
            $archivo->move(public_path("tramitesclientesita/{$solicitud->clienteid}/{$tramite}/ADJUNTOS Y RESPUESTAS"), $nombreArchivo);
            $data['document2'] = $nombreArchivo;
        }

        DB::table('procedimientotramites')->where('id', $id)->update($data);

        return back()->with('info', 'Misiva actualizada correctamente.');
    }
    public function actualizarCarta(Request $request, $id)
    {
        $solicitud = DB::table('procedimientotramites')->where('id', $id)->first();

        if (!$solicitud) {
            return back()->with('error', 'Registro no encontrado');
        }

        $tramite = $request->input("tramite");

        $data = [
            'observaciones' => $request->input("observaciones_$id"),
            'citenota' => $request->input("citenota_$id"),
            'fechacitenota' => $request->input("fechacitenota_$id"),
            'fechainclusion' => $request->input("fechainclusion_$id"),
            'updated_at' => now(),
        ];

        // Procesar document2 si se envió
        if ($request->hasFile("document2_$id")) {
            $archivo = $request->file("document2_$id");
            $nombreArchivo = Str::random(10) . '_' . $archivo->getClientOriginalName();

            $archivo->move(
                public_path("tramitesclientesita/{$solicitud->clienteid}/{$tramite}/CARTAS Y RECLAMOS"),
                $nombreArchivo
            );

            $data['document2'] = $nombreArchivo;
        }

        // Procesar document3 si se envió
        if ($request->hasFile("document3_$id")) {
            $archivo = $request->file("document3_$id");
            $nombreArchivo = Str::random(10) . '_' . $archivo->getClientOriginalName();

            $archivo->move(
                public_path("tramitesclientesita/{$solicitud->clienteid}/{$tramite}/CARTAS Y RECLAMOS"),
                $nombreArchivo
            );

            $data['document3'] = $nombreArchivo;
        }

        DB::table('procedimientotramites')->where('id', $id)->update($data);

        return back()->with('info', 'Misiva actualizada correctamente.');
    }


    // VISTA PREVIA DE MISIVAS
    /* NUEVO 011125 */
    public function previewPDF(Request $request, Cliente $cliente, Tramite $tramite)
    {
        $clienteid = $cliente->id;
        $tipoPdf = $request->input('tipo_pdf');
        $fechaactual = Carbon::parse($request->input('fechaactual'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $tipocartareclamo = $request->input('tipocartareclamo');
        $folio = $request->input('folio');
        $cambioactualizacion = $request->input('cambioactualizacion');
        $matricula = $request->input('matricula');
        $nombremedico = $request->input('nombremedico');
        $nivelprocedimiento = $request->input('nivelprocedimiento');
        $cargomedico = $request->input('cargomedico');
        $nombretramite = $request->input('tramite');
        $aseguradora = $request->input('aseguradora');
        $afpgestora = $request->input('afpgestora');
        $campodirigidoa = $request->input('campodirigidoa');
        $campoestadolab = $request->input('campoestadolab');
        $campoafiliadoa = $request->input('campoafiliadoa');
        $solicitudmodificar = $request->input('solicitudmodificar');
        $medicotratante = $request->input('medicotratante');
        $especialidadinforme = $request->input('especialidadinforme');
        $emisor = $request->input('emisor');
        $fechainformeestudio = Carbon::parse($request->input('fechainformeestudio'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $notatecnicomedico = $request->input('notatecnicomedico');
        $fechanotatecnicomedico = Carbon::parse($request->input('fechanotatecnicomedico'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $texto1 = $request->input('texto1');
        $fechacontrato = Carbon::parse($request->input('fechacontrato'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $firmadoen = $request->input('firmadoen');
        $nrodictamen = $request->input('nrodictamen');
        $fechatramite = Carbon::parse($request->input('fechatramite'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $notificadoen = $request->input('notificadoen');
        $tipoorigen = $request->input('tipoorigen');
        $origendictamen = $request->input('origendictamen');
        $entidadcalificante = $request->input('entidadcalificante');
        $porcentajedictamen = $request->input('porcentajedictamen');
        $fechanotificacion = Carbon::parse($request->input('fechanotificacion'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $nropasaporte = $request->input('nropasaporte');

        $nrocua1 = $request->input('nrocua1');
        $nombreafp1 = $request->input('nombreafp1');
        $nroci1 = $request->input('nroci1');
        $nrocua2 = $request->input('nrocua2');
        $nombreafp2 = $request->input('nombreafp2');
        $nroci2 = $request->input('nroci2');
        $nrocuaunificado = $request->input('nrocuaunificado');
        $nrociunificado = $request->input('nrociunificado');

        $fechainiciotramite = Carbon::parse($request->input('fechainiciotramite'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $fechafirmaeap2 = Carbon::parse($request->input('fechafirmaeap'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $fechaformulario = Carbon::parse($request->input('fechaformulario'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $fecharesolucion = Carbon::parse($request->input('fecharesolucion'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $nroresolucion = $request->input('nroresolucion');

        /* NUEVO 11112025 */
        $txtcomple1 = $request->input('txtcomple1');
        $txtcomple2 = $request->input('txtcomple2');
        $txtcomple3 = $request->input('txtcomple3');
        $tituloop1 = $request->input('tituloop1');
        $tituloop2 = $request->input('tituloop2');

        $empresacliente = Cliente::where('id', $cliente->id)
        ->value('empresa');

        $adjuntos = [];
        for ($i = 1; $i <= 10; $i++) {
            $requerimiento = $request->input("requerimiento$i");
            $tipo = $request->input("tipo$i");
            if (!empty($requerimiento) && !empty($tipo)) {
                $adjuntos[] = [
                    'requerimiento' => $requerimiento,
                    'tipo' => $tipo,
                ];
            }
        }

        $especialistas = [];
        for ($i = 1; $i <= 10; $i++) {
            $especialista = $request->input("especialista$i");
            $detalle = $request->input("detalle$i");
            $cantidad = $request->input("cantidad$i");
            if (!empty($especialista) && !empty($detalle) && !empty($cantidad)) {
                $especialistas[] = [
                    'especialista' => $especialista,
                    'detalle' => $detalle,
                    'cantidad' => $cantidad,
                ];
            }
        }

        $adjuntos2 = [];
        for ($i = 1; $i <= 10; $i++) {
            $requerimiento2 = $request->input("requerimiento2$i");
            $tipo2 = $request->input("tipo2$i");
            if (!empty($requerimiento2) && !empty($tipo2)) {
                $adjuntos2[] = [
                    'requerimiento2' => $requerimiento2,
                    'tipo2' => $tipo2,
                ];
            }
        }

        $informaciones = [];
        for ($i = 1; $i <= 10; $i++) {
            $informacion = $request->input("informacion$i");
            if (!empty($informacion)) {
                $informaciones[] = [
                    'informacion' => $informacion,
                ];
            }
        }

        $abonos = [];
        for ($i = 1; $i <= 10; $i++) {
            $entidadbancaria = $request->input("entidadbancaria$i");
            $tipocuenta = $request->input("tipocuenta$i");
            $nrocuenta = $request->input("nrocuenta$i");
            if (!empty($entidadbancaria) && !empty($tipocuenta) && !empty($nrocuenta)) {
                $abonos[] = [
                    'entidadbancaria' => $entidadbancaria,
                    'tipocuenta' => $tipocuenta,
                    'nrocuenta' => $nrocuenta,
                ];
            }
        }

        $ceapasaportes = [];
        for ($i = 1; $i <= 10; $i++) {
            $appaterno = $request->input("appaterno$i");
            $apmaterno = $request->input("apmaterno$i");
            $primernombre = $request->input("primernombre$i");
            $segundonombre = $request->input("segundonombre$i");
            $cua = $request->input("cua$i");
            $ce = $request->input("ce$i");
            $pasaporte = $request->input("pasaporte$i");
            if (!empty($appaterno) && !empty($apmaterno) /* && !empty($primernombre) && !empty($segundonombre) */ && !empty($cua) && !empty($ce)) {
                $ceapasaportes[] = [
                    'appaterno' => $appaterno,
                    'apmaterno' => $apmaterno,
                    'primernombre' => $primernombre,
                    'segundonombre' => $segundonombre,
                    'cua' => $cua,
                    'ce' => $ce,
                    'pasaporte' => $pasaporte,
                ];
            }
        }

        $ceapasaportes2 = [];
        for ($i = 1; $i <= 10; $i++) {
            $appaterno2 = $request->input("appaterno$i");
            $apmaterno2 = $request->input("apmaterno$i");
            $primernombre2 = $request->input("primernombre$i");
            $segundonombre2 = $request->input("segundonombre$i");
            $cua2 = $request->input("cua$i");
            $ce2 = $request->input("ce$i");
            $pasaporte2 = $request->input("pasaporte$i");
            if (!empty($appaterno2) && !empty($apmaterno2) /* && !empty($primernombre2) && !empty($segundonombre2) */ && !empty($cua2) && !empty($pasaporte2)) {
                $ceapasaportes2[] = [
                    'appaterno2' => $appaterno2,
                    'apmaterno2' => $apmaterno2,
                    'primernombre2' => $primernombre2,
                    'segundonombre2' => $segundonombre2,
                    'cua2' => $cua2,
                    'ce2' => $ce2,
                    'pasaporte2' => $pasaporte2,
                ];
            }
        }

        $unificacioncuas = [];
        for ($i = 1; $i <= 10; $i++) {
            $appaterno3 = $request->input("appaterno$i");
            $apmaterno3 = $request->input("apmaterno$i");
            $primernombre3 = $request->input("primernombre$i");
            $segundonombre3 = $request->input("segundonombre$i");
            $fechanacimiento3 = $request->input("fechanacimiento$i");
            $ci3 = $request->input("ci$i");
            $cua3 = $request->input("cua$i");
            $cuaotro3 = $request->input("cuaotro$i");
            if (!empty($appaterno3) && !empty($apmaterno3) /* && !empty($primernombre3) && !empty($segundonombre3) */ && !empty($fechanacimiento3) && !empty($ci3) && !empty($cuaotro3)) {
                $unificacioncuas[] = [
                    'appaterno3' => $appaterno3,
                    'apmaterno3' => $apmaterno3,
                    'primernombre3' => $primernombre3,
                    'segundonombre3' => $segundonombre3,
                    'fechanacimiento3' => $fechanacimiento3,
                    'ci3' => $ci3,
                    'cua3' => $cua3,
                    'cuaotro3' => $cuaotro3,
                ];
            }
        }

        $cambiounificacioncuas = [];
        for ($i = 1; $i <= 10; $i++) {
            $appaterno4 = $request->input("appaterno$i");
            $apmaterno4 = $request->input("apmaterno$i");
            $primernombre4 = $request->input("primernombre$i");
            $segundonombre4 = $request->input("segundonombre$i");
            $fechanacimiento4 = $request->input("fechanacimiento$i");
            $ci4 = $request->input("ci$i");
            $cua4 = $request->input("cua$i");
            $cuaotro4 = $request->input("cuaotro$i");
            if (!empty($appaterno4) && !empty($apmaterno4) /* && !empty($primernombre4) && !empty($segundonombre4) */ && !empty($fechanacimiento4) && !empty($ci4) && !empty($cua4) && empty($cuaotro4)) {
                $cambiounificacioncuas[] = [
                    'appaterno4' => $appaterno4,
                    'apmaterno4' => $apmaterno4,
                    'primernombre4' => $primernombre4,
                    'segundonombre4' => $segundonombre4,
                    'fechanacimiento4' => $fechanacimiento4,
                    'ci4' => $ci4,
                    'cua4' => $cua4,
                    'cuaotro4' => $cuaotro4,
                ];
            }
        }

        $prestaciones = [];
        for ($i = 1; $i <= 10; $i++) {
            $prestacion = $request->input("prestacion$i");
            $periodo = $request->input("periodo$i");

            if (!empty($prestacion) && !empty($periodo)) {
                // Convertir de 'YYYY-MM' a 'MM/YYYY'
                $periodo_formateado = date('m/Y', strtotime($periodo . '-01'));

                $prestaciones[] = [
                    'prestacion' => $prestacion,
                    'periodo' => $periodo_formateado,
                ];
            }
        }

        /* NUEVO 11112025 */
        $opcionesuno = [];
        for ($i = 1; $i <= 20; $i++) {
            $opcionuno = $request->input("opcionuno$i");
            if (!empty($opcionuno)) {
                $opcionesuno[] = [
                    'opcionuno' => $opcionuno,
                ];
            }
        }

        $opcionesdos = [];
        for ($i = 1; $i <= 20; $i++) {
            $opciondos = $request->input("opciondos$i");
            if (!empty($opciondos)) {
                $opcionesdos[] = [
                    'opciondos' => $opciondos,
                ];
            }
        }

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $clienteid)->where('servicio', $nombretramite)->first();
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;
        $fechaingresotramite = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'INGRESO DE TRAMITE')
            ->where('subprocedimiento', 'RECEPCIÓN DE TRAMITE')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $fechaingresotramite = $fechaingresotramite ? Carbon::parse($fechaingresotramite->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

        $fechafirmaeap = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'FIRMA EAP')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $fechafirmaeap = $fechafirmaeap ? Carbon::parse($fechafirmaeap->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

        $fechafirmaeap30 = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'FIRMA EAP')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $fechaeap30 = $fechafirmaeap ? Carbon::parse($fechafirmaeap30->fechasubida)->addDays(30)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
        
        $nivelProcedimiento = '';
        $subProcedimiento = '';

        $nombre = '';
        $ci = '';
        $ciexp = '';
        $telefono = '';
        $sexo = '';

        if ($emisor === 'CLIENTE') {
            $nombre = $cliente->nombrecompleto;
            $ci = $cliente->ci;
            $ciexp = $cliente->ciexp;
            $telefono = Str::startsWith($cliente->celular, '591') 
            ? substr($cliente->celular, 3) 
            : $cliente->celular;
            $sexo = strtolower($cliente->genero);
        } elseif ($emisor === 'APODERADO') {
            $apoderadoNombre = $request->input('apoderado');
            $apoderado = DB::table('proveedoresservicios')
                ->where('razonsocial', $apoderadoNombre)
                ->first();
            if ($apoderado) {
                $nombre = $apoderado->razonsocial;
                $ci = $apoderado->ci;
                $ciexp = $apoderado->ciexp;
                $telefono = $apoderado->celularcorporativo;
                $sexo = strtolower($apoderado->sexo);
            }
        }

        $pdfView = '';
        switch ($tipoPdf) {
            case 'EVALUACIÓN POR MEDICINA DEL TRABAJO':
                $pdfView = 'admin.tramites.solicitudes.evaluacionmedicinatrabajo';
                $subProcedimiento = 'EVALUACIÓN POR MEDICINA DEL TRABAJO';
                break;
            case 'INCLUSIÓN DE INFORMES MÉDICOS':
                $pdfView = 'admin.tramites.solicitudes.inclusioninformesmedicos';
                $subProcedimiento = 'INCLUSIÓN DE INFORMES MÉDICOS';
                break;
            case 'HISTORIA CLÍNICA LEGALIZADA':
                $pdfView = 'admin.tramites.solicitudes.historiaclinicalegalizada';
                $subProcedimiento = 'HISTORIA CLÍNICA LEGALIZADA';
                break;
            case 'ACTUALIZACIÓN DE DATOS':
                $pdfView = 'admin.tramites.solicitudes.actualizaciondatos';
                $subProcedimiento = 'ACTUALIZACIÓN DE DATOS';
                break;
            case 'COMPRA DE SERVICIOS':
                $pdfView = 'admin.tramites.solicitudes.compraservicios';
                $subProcedimiento = 'COMPRA DE SERVICIOS';
                break;
            case 'INFORME DEL EMPLEADOR':
                $pdfView = 'admin.tramites.solicitudes.informeempleador';
                $subProcedimiento = 'INFORME DEL EMPLEADOR';
                break;
            case 'MODIFICACIÓN DE CITE':
                $pdfView = 'admin.tramites.solicitudes.modificacioncite';
                $subProcedimiento = 'MODIFICACIÓN DE CITE';
                break;
            case 'INFORME MÉDICO':
                $pdfView = 'admin.tramites.solicitudes.informemedico';
                $subProcedimiento = 'INFORME MÉDICO';
                break;
            case 'ABONO EN CUENTA':
                $pdfView = 'admin.tramites.solicitudes.abonoencuenta';
                $subProcedimiento = 'ABONO EN CUENTA';
                break;
            case 'COPIA LEGALIZADA DE CONTRATO':
                $pdfView = 'admin.tramites.solicitudes.copialegalizadacontrato';
                $subProcedimiento = 'COPIA LEGALIZADA DE CONTRATO';
                break;
            case 'NO DESCUENTO 3%':
                $pdfView = 'admin.tramites.solicitudes.nodescuentotresporciento';
                $subProcedimiento = 'NO DESCUENTO 3%';
                break;
            case 'COPIA LEGALIZADA DE DICTAMEN':
                $pdfView = 'admin.tramites.solicitudes.copialegalizadadictamen';
                $subProcedimiento = 'COPIA LEGALIZADA DE DICTAMEN';
                break;
            case 'REACTIVACIÓN DE TRÁMITE':
                $pdfView = 'admin.tramites.solicitudes.reactivaciontramite';
                $subProcedimiento = 'REACTIVACIÓN DE TRÁMITE';
                break;
            case 'RECALIFICACIÓN DE DICTAMEN':
                $pdfView = 'admin.tramites.solicitudes.recalificaciondictamen';
                $subProcedimiento = 'RECALIFICACIÓN DE DICTAMEN';
                break;
            case 'CAMBIO DE C.E. A PASAPORTE':
                $pdfView = 'admin.tramites.solicitudes.cambioceapasaporte';
                $subProcedimiento = 'CAMBIO DE C.E. A PASAPORTE';
                break;
            case 'UNIFICACIÓN DE CUA':
                $pdfView = 'admin.tramites.solicitudes.unificacioncua';
                $subProcedimiento = 'UNIFICACIÓN DE CUA';
                break;
            case 'ACTA DE COBROS':
                $pdfView = 'admin.tramites.solicitudes.actacobros';
                $subProcedimiento = 'ACTA DE COBROS';
                break;
            /* NUEVO 11112025 */
            case 'REVISIÓN DE DICTAMEN DE INVALIDEZ':
                $pdfView = 'admin.tramites.solicitudes.revisiondictameninvalidez';
                $subProcedimiento = 'REVISIÓN DE DICTAMEN DE INVALIDEZ';
                break;
            default:
                return response()->json(['error' => 'Tipo de PDF no válido'], 400);
        }

        $generocliente = Cliente::where('id', $clienteid)->value('genero');
        $afiliadoTexto = (strtoupper($generocliente) === 'FEMENINO') ? 'de la Afiliada' : 'del Afiliado';

        $pdf = PDF::loadView($pdfView, compact('cliente', 'fechaactual', 'fechaingresotramite', 'fechafirmaeap', 'tipocartareclamo', 'numeropoder', 'fechaeap30', 
            'folio', 'cambioactualizacion', 'notatecnicomedico', 'fechanotatecnicomedico', 'adjuntos', 'matricula', 
            'fechainformeestudio', 'especialistas', 'adjuntos2','nombremedico','cargomedico','aseguradora','afpgestora','tramite',
            'nombretramite','nombre', 'ci', 'ciexp', 'telefono', 'emisor', 'sexo', 'nivelprocedimiento', 'empresacliente', 'texto1',
            'campodirigidoa','campoestadolab','campoafiliadoa','solicitudmodificar','informaciones','medicotratante','especialidadinforme', 
            'abonos','fechacontrato','firmadoen','nrodictamen','fechatramite','fechanotificacion','notificadoen','tipoorigen',
            'origendictamen','entidadcalificante','porcentajedictamen', 'nropasaporte','ceapasaportes','ceapasaportes2',
            'nrocua1','nombreafp1','nroci1','nrocua2','nombreafp2','nroci2','nrocuaunificado','nrociunificado',
            'unificacioncuas','cambiounificacioncuas','prestaciones','fechainiciotramite','fechafirmaeap2','fechaformulario',
            'fecharesolucion','nroresolucion','txtcomple1','txtcomple2','txtcomple3','tituloop1','tituloop2',
            'opcionesuno','opcionesdos','afiliadoTexto'));

        return $pdf->stream('preview.pdf');
    }
    public function previewCarta(Request $request, Cliente $cliente, Tramite $tramite)
    {
        $clienteid = $cliente->id;
        $tipoPdf = $request->input('tipo_pdf3');
        $fechaactual = Carbon::parse($request->input('fechaactual3'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $apoderadoId = $request->input('apoderado3');
        $nivelprocedimiento = $request->input('nivelprocedimiento3');
        $subProcedimiento = $request->input('subnivelprocedimiento3');
        $nombretramite = $request->input('tramite3');
        $apoderadoNombre = $request->input('apoderado3');
        $nombremedico = $request->input('nombremedico3');
        $cargomedico = $request->input('cargomedico3');
        $fontSize = $request->input('fontsize3', '15px');
        $marginSize = $request->input('marginsize3', '1.5cm 3cm 1.5cm 3cm');

        $tipoadjunto = $request->input('tipoadjunto3');
        $fechaadjuntoInput = $request->input('fechaadjunto3');
        $fechaadjunto = null;
        if ($fechaadjuntoInput) {
            $fechaadjunto = Carbon::parse($fechaadjuntoInput)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        $solmodificar3 = $request->input('solmodificar3');
        $nronota3 = $request->input('nronota3');
        $fechanota3Input = $request->input('fechanota3');
        $fechanota3 = null;
        if ($fechanota3Input) {
            $fechanota3 = Carbon::parse($fechanota3Input)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }
        $dirigidoa3 = $request->input('dirigidoa3');
        $estadolab3 = $request->input('estadolab3');
        $afiliadoa3 = $request->input('afiliadoa3');
        $textocomplementario3 = $request->input('textocomplementario3');

        $fechaconclusionprog3Input = $request->input('fechaconclusionprog3');
        $fechaconclusionprog3 = null;
        if ($fechaconclusionprog3Input) {
            $fechaconclusionprog3 = Carbon::parse($fechaconclusionprog3Input)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        $fechasolrevdictamenInput = $request->input('fechasolrevdictamen3');
        $fechasolrevdictamen = null;
        if ($fechasolrevdictamenInput) {
            $fechasolrevdictamen = Carbon::parse($fechasolrevdictamenInput)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }
        $nrorevisiondictamen = $request->input('nrorevisiondictamen3');
        $fecharevdictamenInput = $request->input('fecharevdictamen3');
        $fecharevdictamen = null;
        if ($fecharevdictamenInput) {
            $fecharevdictamen = Carbon::parse($fecharevdictamenInput)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }
        $porcentajedictamen = $request->input('porcentajedictamen3');
        $origendictamen = $request->input('origendictamen3');
        $motivoorigendictamen = $request->input('motivoorigendictamen3');

        /* PRIMERA CARTA SIT */
            $fecha1sitInput = $request->input('fecha1sit');
            $fecha1sit = null;
            if ($fecha1sitInput) {
                $fecha1sit = Carbon::parse($fecha1sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite1sit = $request->input('cite1sit');
            $fecharesp1sitInput = $request->input('fecharesp1sit');
            $fecharesp1sit = null;
            if ($fecharesp1sitInput) {
                $fecharesp1sit = Carbon::parse($fecharesp1sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fechacite1sitInput = $request->input('fechacite1sit');
            $fechacite1sit = null;
            if ($fechacite1sitInput) {
                $fechacite1sit = Carbon::parse($fechacite1sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto1sit = $request->input('texto1sit');
        //

        /* SEGUNDA CARTA SIT */
            $fecha2sitInput = $request->input('fecha2sit');
            $fecha2sit = null;
            if ($fecha2sitInput) {
                $fecha2sit = Carbon::parse($fecha2sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite2sit = $request->input('cite2sit');
            $fecharesp2sitInput = $request->input('fecharesp2sit');
            $fecharesp2sit = null;
            if ($fecharesp2sitInput) {
                $fecharesp2sit = Carbon::parse($fecharesp2sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fechacite2sitInput = $request->input('fechacite2sit');
            $fechacite2sit = null;
            if ($fechacite2sitInput) {
                $fechacite2sit = Carbon::parse($fechacite2sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto2sit = $request->input('texto2sit');
        //

        /* TERCERA CARTA SIT */
            $fecha3sitInput = $request->input('fecha3sit');
            $fecha3sit = null;
            if ($fecha3sitInput) {
                $fecha3sit = Carbon::parse($fecha3sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite3sit = $request->input('cite3sit');
            $fecharesp3sitInput = $request->input('fecharesp3sit');
            $fecharesp3sit = null;
            if ($fecharesp3sitInput) {
                $fecharesp3sit = Carbon::parse($fecharesp3sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fechacite3sitInput = $request->input('fechacite3sit');
            $fechacite3sit = null;
            if ($fechacite3sitInput) {
                $fechacite3sit = Carbon::parse($fechacite3sitInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto3sit = $request->input('texto3sit');
        //

        /* PRIMERA CARTA DE RECLAMO GP */
            $fecha1reclamogpInput = $request->input('fecha1reclamogp');
            $fecha1reclamogp = null;
            if ($fecha1reclamogpInput) {
                $fecha1reclamogp = Carbon::parse($fecha1reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite1reclamogp = $request->input('cite1reclamogp');
            $fechacite1reclamogpInput = $request->input('fechacite1reclamogp');
            $fechacite1reclamogp = null;
            if ($fechacite1reclamogpInput) {
                $fechacite1reclamogp = Carbon::parse($fechacite1reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fecharesp1reclamogpInput = $request->input('fecharesp1reclamogp');
            $fecharesp1reclamogp = null;
            if ($fecharesp1reclamogpInput) {
                $fecharesp1reclamogp = Carbon::parse($fecharesp1reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto1reclamogp = $request->input('texto1reclamogp');
        //

        /* PRIMERA CARTA DE RECLAMO APS */
            $fecha1reclamoapsInput = $request->input('fecha1reclamoaps');
            $fecha1reclamoaps = null;
            if ($fecha1reclamoapsInput) {
                $fecha1reclamoaps = Carbon::parse($fecha1reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite1reclamoaps = $request->input('cite1reclamoaps');
            $fechacite1reclamoapsInput = $request->input('fechacite1reclamoaps');
            $fechacite1reclamoaps = null;
            if ($fechacite1reclamoapsInput) {
                $fechacite1reclamoaps = Carbon::parse($fechacite1reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fecharesp1reclamoapsInput = $request->input('fecharesp1reclamoaps');
            $fecharesp1reclamoaps = null;
            if ($fecharesp1reclamoapsInput) {
                $fecharesp1reclamoaps = Carbon::parse($fecharesp1reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto1reclamoaps = $request->input('texto1reclamoaps');
        //

        /* SEGUNDA CARTA DE RECLAMO GP */
            $fecha2reclamogpInput = $request->input('fecha2reclamogp');
            $fecha2reclamogp = null;
            if ($fecha2reclamogpInput) {
                $fecha2reclamogp = Carbon::parse($fecha2reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite2reclamogp = $request->input('cite2reclamogp');
            $fechacite2reclamogpInput = $request->input('fechacite2reclamogp');
            $fechacite2reclamogp = null;
            if ($fechacite2reclamogpInput) {
                $fechacite2reclamogp = Carbon::parse($fechacite2reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fecharesp2reclamogpInput = $request->input('fecharesp2reclamogp');
            $fecharesp2reclamogp = null;
            if ($fecharesp2reclamogpInput) {
                $fecharesp2reclamogp = Carbon::parse($fecharesp2reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto2reclamogp = $request->input('texto2reclamogp');
        //

        /* SEGUNDA CARTA DE RECLAMO APS */
            $fecha2reclamoapsInput = $request->input('fecha2reclamoaps');
            $fecha2reclamoaps = null;
            if ($fecha2reclamoapsInput) {
                $fecha2reclamoaps = Carbon::parse($fecha2reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite2reclamoaps = $request->input('cite2reclamoaps');
            $fechacite2reclamoapsInput = $request->input('fechacite2reclamoaps');
            $fechacite2reclamoaps = null;
            if ($fechacite2reclamoapsInput) {
                $fechacite2reclamoaps = Carbon::parse($fechacite2reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fecharesp2reclamoapsInput = $request->input('fecharesp2reclamoaps');
            $fecharesp2reclamoaps = null;
            if ($fecharesp2reclamoapsInput) {
                $fecharesp2reclamoaps = Carbon::parse($fecharesp2reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto2reclamoaps = $request->input('texto2reclamoaps');
        //

        /* TERCERA CARTA DE RECLAMO GP */
            $fecha3reclamogpInput = $request->input('fecha3reclamogp');
            $fecha3reclamogp = null;
            if ($fecha3reclamogpInput) {
                $fecha3reclamogp = Carbon::parse($fecha3reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite3reclamogp = $request->input('cite3reclamogp');
            $fechacite3reclamogpInput = $request->input('fechacite3reclamogp');
            $fechacite3reclamogp = null;
            if ($fechacite3reclamogpInput) {
                $fechacite3reclamogp = Carbon::parse($fechacite3reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fecharesp3reclamogpInput = $request->input('fecharesp3reclamogp');
            $fecharesp3reclamogp = null;
            if ($fecharesp3reclamogpInput) {
                $fecharesp3reclamogp = Carbon::parse($fecharesp3reclamogpInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto3reclamogp = $request->input('texto3reclamogp');
        //

        /* TERCERA CARTA DE RECLAMO APS */
            $fecha3reclamoapsInput = $request->input('fecha3reclamoaps');
            $fecha3reclamoaps = null;
            if ($fecha3reclamoapsInput) {
                $fecha3reclamoaps = Carbon::parse($fecha3reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $cite3reclamoaps = $request->input('cite3reclamoaps');
            $fechacite3reclamoapsInput = $request->input('fechacite3reclamoaps');
            $fechacite3reclamoaps = null;
            if ($fechacite3reclamoapsInput) {
                $fechacite3reclamoaps = Carbon::parse($fechacite3reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $fecharesp3reclamoapsInput = $request->input('fecharesp3reclamoaps');
            $fecharesp3reclamoaps = null;
            if ($fecharesp3reclamoapsInput) {
                $fecharesp3reclamoaps = Carbon::parse($fecharesp3reclamoapsInput)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
            $texto3reclamoaps = $request->input('texto3reclamoaps');
        //

        $apoderado = DB::table('proveedoresservicios')
            ->where('razonsocial', $apoderadoNombre)
            ->first();
        if ($apoderado) {
            $nombre = $apoderado->razonsocial;
            $ci = $apoderado->ci;
            $ciexp = $apoderado->ciexp;
            $telefono = $apoderado->celularcorporativo;
            $sexo = strtolower($apoderado->sexo);
        }

        $generocliente = Cliente::where('id', $clienteid)->value('genero');

        $afiliadoTexto = (strtoupper($generocliente) === 'FEMENINO') ? 'de la Afiliada' : 'del Afiliado';

        // Obtener el primer registro de Requisitosubcliente para el cliente especificado
        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $clienteid)->where('servicio', $nombretramite)->first();
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        // FECHA REGISTRO INGRESO DE TRAMITE
        $fechaingresotramiteRegistro = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'INGRESO DE TRAMITE')
            ->whereIn('subprocedimiento', ['RECEPCIÓN DE TRAMITE', 'INCLUSIÓN DE PODER'])
            ->where('tramite', $nombretramite)
            ->orderBy('fechasubida', 'desc')
        ->first();
        $fechaingresotramite = null;
        if ($fechaingresotramiteRegistro && $fechaingresotramiteRegistro->fechasubida) {
            $fechaingresotramite = Carbon::parse($fechaingresotramiteRegistro->fechasubida)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        // FECHA RETORNO INGRESO DE TRAMITE
        $fecharetornoingresotramiteRegistro = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'INGRESO DE TRAMITE')
            ->whereIn('subprocedimiento', ['RECEPCIÓN DE TRAMITE', 'INCLUSIÓN DE PODER'])
            ->where('tramite', $nombretramite)
            ->orderBy('fecharetorno', 'desc')
        ->first();
        $fecharetornoingresotramite = null;
        if ($fecharetornoingresotramiteRegistro && $fecharetornoingresotramiteRegistro->fecharetorno) {
            $fecharetornoingresotramite = Carbon::parse($fecharetornoingresotramiteRegistro->fecharetorno)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        // FECHA RETORNO VALIDACIÓN DE PODER
        $fecharetornovalidacionRegistro = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'NOTIFICACIÓN DE PODER')
            ->where('subprocedimiento', 'VALIDACIÓN DE PODER')
            ->where('tramite', $nombretramite)
            ->orderBy('fecharetorno', 'desc')
        ->first();
        $fecharetornovalidacion = null;
        if ($fecharetornovalidacionRegistro && !empty($fecharetornovalidacionRegistro->fecharetorno)) {
            $fecharetornovalidacion = Carbon::parse($fecharetornovalidacionRegistro->fecharetorno)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        // FECHA REGISTRO FIRMA EAP
        $fechafirmaeapReg = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'FIRMA EAP')
            ->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')
            ->where('tramite', $nombretramite)
            ->orderBy('fechasubida', 'desc')
        ->first();
        $fechafirmaeap = null;
        if ($fechafirmaeapReg && $fechafirmaeapReg->fechasubida) {
            $fechafirmaeap = Carbon::parse($fechafirmaeapReg->fechasubida)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        // FECHA RETORNO FIRMA EAP
        $fecharetornofirmaeapReg = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'FIRMA EAP')
            ->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')
            ->where('tramite', $nombretramite)
            ->orderBy('fecharetorno', 'desc')
        ->first();
        $fecharetornofirmaeap = null;
        if ($fecharetornofirmaeapReg && $fecharetornofirmaeapReg->fecharetorno) {
            $fecharetornofirmaeap = Carbon::parse($fecharetornofirmaeapReg->fecharetorno)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }       
        
        // FECHA MODIFICACIÓN DE CITE
        $fechamodificacionciteReg = Tramite::where('clienteid', $clienteid)
            ->where('tipo', 'SOLICITUD')
            ->where('subprocedimiento', 'MODIFICACIÓN DE CITE')
            ->where('tramite', $nombretramite)
            ->orderBy('fechasubida', 'desc')
        ->first();
        $fechamodificacioncite = null;
        if ($fechamodificacionciteReg && $fechamodificacionciteReg->fechasubida) {
            $fechamodificacioncite = Carbon::parse($fechamodificacionciteReg->fechasubida)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }
        
        /* NUEVO 201125 */
        $fechaadjuntodocumentoReg = Tramite::where('clienteid', $clienteid)
            ->where('tipo', 'PROCEDIMIENTO')
            ->whereIn('subprocedimiento', [
                'ENTE GESTOR DE SALUD _ ADJUNTO DE DOCUMENTACIÓN MEDICA',
                'NOTIFICACIÓN TMC _ ADJUNTO DE DOCUMENTACIÓN MEDICA'
            ])
            ->where('tramite', $nombretramite)
            ->orderBy('fechasubida', 'desc')
            ->first();

        if (!$fechaadjuntodocumentoReg) {
            $fechaadjuntodocumentoReg = Tramite::where('clienteid', $clienteid)
                ->where('tipo', 'ARDJUNTO / ESPUESTA')
                ->whereIn('subprocedimiento', [
                    'ADJUNTO DE DOCUMENTOS',
                    'ADJUNTO DE DOCUMENTACIÓN MÉDICA'
                ])
                ->where('tramite', $nombretramite)
                ->orderBy('fechasubida', 'desc')
                ->first();
        }

        $fechaadjuntodocumento = null;
        if ($fechaadjuntodocumentoReg && $fechaadjuntodocumentoReg->fechasubida) {
            $fechaadjuntodocumento = Carbon::parse($fechaadjuntodocumentoReg->fechasubida)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        } else {
            if ($request->filled('fechaadjmedica')) {
                $fechaadjuntodocumento = Carbon::parse($request->fechaadjmedica)
                    ->locale('es')
                    ->isoFormat('D [de] MMMM [del] YYYY');
            }
        }


        // FECHA MODIFICACIÓN DE CITE
        $fechasolcompraserviciosReg = Tramite::where('clienteid', $clienteid)
            ->where('tipo', 'SOLICITUD')
            ->where('subprocedimiento', 'COMPRA DE SERVICIOS')
            ->where('tramite', $nombretramite)
            ->orderBy('fechasubida', 'desc')
        ->first();
        $fechasolcompraservicios = null;
        if ($fechasolcompraserviciosReg && $fechasolcompraserviciosReg->fechasubida) {
            $fechasolcompraservicios = Carbon::parse($fechasolcompraserviciosReg->fechasubida)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        $pdfView = '';
        switch ($tipoPdf) {
            case 'PRIMERA CARTA SIT':
                $pdfView = 'admin.tramites.cartasyreclamos.sitprimeracarta';
                break;
            case 'SEGUNDA CARTA SIT':
                $pdfView = 'admin.tramites.cartasyreclamos.sitsegundacarta';
                break;
            case 'TERCERA CARTA SIT':
                $pdfView = 'admin.tramites.cartasyreclamos.sitterceracarta';
                break;
            case 'PRIMERA CARTA DE RECLAMO GP':
                $pdfView = 'admin.tramites.cartasyreclamos.gpreclamoprimeracarta';
                break;
            case 'PRIMERA CARTA DE RECLAMO APS':
                $pdfView = 'admin.tramites.cartasyreclamos.apsreclamoprimeracarta';
                break;
            case 'SEGUNDA CARTA DE RECLAMO GP':
                $pdfView = 'admin.tramites.cartasyreclamos.gpreclamosegundacarta';
                break;
            case 'SEGUNDA CARTA DE RECLAMO APS':
                $pdfView = 'admin.tramites.cartasyreclamos.apsreclamosegundacarta';
                break;
            case 'TERCERA CARTA DE RECLAMO GP':
                $pdfView = 'admin.tramites.cartasyreclamos.gpreclamoterceracarta';
                break;
            case 'TERCERA CARTA DE RECLAMO APS':
                $pdfView = 'admin.tramites.cartasyreclamos.apsreclamoterceracarta';
                break;
            case 'REITERACIÓN A CARTAS DE RECLAMO GP':
                $pdfView = 'admin.tramites.cartasyreclamos.gpreiteracioncartasreclamo';
                break;
            case 'REITERACIÓN A CARTAS DE RECLAMO APS':
                $pdfView = 'admin.tramites.cartasyreclamos.apsreiteracioncartasreclamo';
                break;
            default:
                return response()->json(['error' => 'Tipo de PDF no válido'], 400);
        }

        $pdf = PDF::loadView($pdfView, compact('cliente','fechaactual', 'fechaingresotramite','fechafirmaeap','afiliadoTexto',
        'nombre','ci','ciexp','telefono','sexo','numeropoder','nombretramite','fecharetornoingresotramite','subProcedimiento',
        'fontSize', 'marginSize','fecharetornovalidacion','fecharetornofirmaeap','fechaadjunto','tipoadjunto','solmodificar3',
        'nronota3','fechanota3','dirigidoa3','estadolab3','afiliadoa3','fechamodificacioncite','textocomplementario3',
        'fechaadjuntodocumento','fechaconclusionprog3','fechasolcompraservicios','nombremedico','cargomedico',
        'fecha1sit','cite1sit','fecharesp1sit','fechacite1sit','texto1sit',
        'fecha2sit','cite2sit','fecharesp2sit','fechacite2sit','texto2sit',
        'fecha3sit','cite3sit','fecharesp3sit','fechacite3sit','texto3sit',
        'fecha1reclamogp','cite1reclamogp','fechacite1reclamogp','fecharesp1reclamogp','texto1reclamogp',
        'fecha2reclamogp','cite2reclamogp','fechacite2reclamogp','fecharesp2reclamogp','texto2reclamogp',
        'fecha3reclamogp','cite3reclamogp','fechacite3reclamogp','fecharesp3reclamogp','texto3reclamogp',
        'fecha1reclamoaps','cite1reclamoaps','fechacite1reclamoaps','fecharesp1reclamoaps','texto1reclamoaps',
        'fecha2reclamoaps','cite2reclamoaps','fechacite2reclamoaps','fecharesp2reclamoaps','texto2reclamoaps',
        'fecha3reclamoaps','cite3reclamoaps','fechacite3reclamoaps','fecharesp3reclamoaps','texto3reclamoaps',
        'fechasolrevdictamen','nrorevisiondictamen','fecharevdictamen','porcentajedictamen','origendictamen','motivoorigendictamen'));

        return $pdf->stream('preview.carta');
    }
    public function previewAdjunto(Request $request, Cliente $cliente, Tramite $tramite)
    {
        $clienteid = $cliente->id;
        $tipoPdf = $request->input('tipo_pdf2');
        $nombretramite = $request->input('tramite2');
        $fechaactual = Carbon::parse($request->input('fechaactual2'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $nivelprocedimiento = $request->input('nivelprocedimiento2');
        $subProcedimiento = $request->input('subnivelprocedimiento2');
        $nombremedico = $request->input('nombremedico2');
        $cargomedico = $request->input('cargomedico2');
        $textocomplementario = $request->input('textocomplementario2');
        $apoderadoNombre = $request->input('apoderado2');
        $documentoadjunto = $request->input('documentoadjunto2');
        $fontSize = $request->input('fontsize2', '15px');
        $marginSize = $request->input('marginsize2', '1.5cm 3cm 1.5cm 3cm');
        $notatecnicomedico = $request->input('notatecnicomedico2');

        $fechanotatecnicomedicoInput = $request->input('fechanotatecnicomedico2');
        $fechanotatecnicomedico = null;
        if ($fechanotatecnicomedicoInput) {
            $fechanotatecnicomedico = Carbon::parse($fechanotatecnicomedicoInput)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        /* NUEVO 221125 */
        $nrosolrevdic2 = $request->input('nrosolrevdic2');

        $fechasolrevdic2Input = $request->input('fechasolrevdic2');
        $fechasolrevdic2 = null;
        if ($fechasolrevdic2Input) {
            $fechasolrevdic2 = Carbon::parse($fechasolrevdic2Input)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        $fechapressolrevdic2Input = $request->input('fechapressolrevdic2');
        $fechapressolrevdic2 = null;
        if ($fechapressolrevdic2Input) {
            $fechapressolrevdic2 = Carbon::parse($fechapressolrevdic2Input)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }

        $fechaautoadmision2Input = $request->input('fechaautoadmision2');
        $fechaautoadmision2 = null;
        if ($fechaautoadmision2Input) {
            $fechaautoadmision2 = Carbon::parse($fechaautoadmision2Input)
                ->locale('es')
                ->isoFormat('D [de] MMMM [del] YYYY');
        }


        $especialistas = [];
        for ($i = 1; $i <= 10; $i++) {
            $especialista = $request->input("especialista2$i");
            $detalle = $request->input("detalle2$i");
            $cantidad = $request->input("cantidad2$i");

            if (!empty($especialista) && !empty($detalle) && !empty($cantidad)) {
                $especialistas[] = [
                    'especialista2' => $especialista,
                    'detalle2' => $detalle,
                    'cantidad2' => $cantidad,
                ];
            }
        }

        $apoderado = DB::table('proveedoresservicios')
            ->where('razonsocial', $apoderadoNombre)
            ->first();
        if ($apoderado) {
            $nombre = $apoderado->razonsocial;
            $ci = $apoderado->ci;
            $ciexp = $apoderado->ciexp;
            $telefono = $apoderado->celularcorporativo;
            $sexo = strtolower($apoderado->sexo);
        }

        // Obtener el primer registro de Requisitosubcliente para el cliente especificado
        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $clienteid)->where('servicio', $nombretramite)->first();
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        // Buscar el primer registro de Tramite que cumpla con las condiciones
        $fechaingresotramite = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'INGRESO DE TRAMITE')
            ->where('subprocedimiento', 'RECEPCIÓN DE TRAMITE')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $fechaingresotramite = $fechaingresotramite ? Carbon::parse($fechaingresotramite->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

        // Buscar el primer registro de Firma EAP que cumpla con las condiciones
        $fechafirmaeap = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'FIRMA EAP')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $fechafirmaeap = $fechafirmaeap ? Carbon::parse($fechafirmaeap->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

        $fechafirmaeap30 = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'FIRMA EAP')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $fechaeap30 = $fechafirmaeap ? Carbon::parse($fechafirmaeap30->fechasubida)->addDays(30)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
        
        $adyretecnicomedico = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
            ->where('subprocedimiento', 'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $adyretecnicomedico = $adyretecnicomedico ? Carbon::parse($adyretecnicomedico->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
        
        $adyrecomplementario = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
            ->where('subprocedimiento', 'ADJUNTO Y RESPUESTA COMPLEMENTARIO')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $adyrecomplementario = $adyrecomplementario ? Carbon::parse($adyrecomplementario->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
        
        $adyreactatmc = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
            ->where('subprocedimiento', 'ADJUNTO Y RESPUESTA AL ACTA TMC')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $adyreactatmc = $adyreactatmc ? Carbon::parse($adyreactatmc->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
        
        $adinformeempleador = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
            ->where('subprocedimiento', 'ADJUNTO INFORME DEL EMPLEADOR')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $adinformeempleador = $adinformeempleador ? Carbon::parse($adinformeempleador->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
        
        $addocumentacionmedica = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
            ->where('subprocedimiento', 'ADJUNTO DOCUMENTACIÓN MÉDICA')
            ->orderBy('fechasubida', 'desc')
            ->first();
        $addocumentacionmedica = $addocumentacionmedica ? Carbon::parse($addocumentacionmedica->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

        $pdfView = '';
        switch ($tipoPdf) {
            case 'ADJUNTO DE DOCUMENTOS':
                $pdfView = 'admin.tramites.adjuntosyrespuestas.adjdocumentos';
                break;
            case 'ADJUNTO DE DOCUMENTACIÓN MÉDICA':
                $pdfView = 'admin.tramites.adjuntosyrespuestas.adjdocumentacionmedica';
                break;
            case 'ADJUNTO Y RESPUESTA DE INFORME DEL EMPLEADOR':
                $pdfView = 'admin.tramites.adjuntosyrespuestas.adjrespinformeempleador';
                break;
            case 'ADJUNTO Y RESPUESTA A NOTIFICACIÓN TMC':
                $pdfView = 'admin.tramites.adjuntosyrespuestas.adjrespnotificaciontmc';
                break;
            case 'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO':
                $pdfView = 'admin.tramites.adjuntosyrespuestas.adjresptecnicomedico';
                break;
            case 'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO':
                $pdfView = 'admin.tramites.adjuntosyrespuestas.adjrespcomplementario';
                break;
            default:
                return response()->json(['error' => 'Tipo de PDF no válido'], 400);
        }

        $generocliente = Cliente::where('id', $clienteid)->value('genero');
        $afiliadoTexto = (strtoupper($generocliente) === 'FEMENINO') ? 'de la Afiliada' : 'del Afiliado';

        $pdf = PDF::loadView($pdfView, compact('cliente', 'fechaactual', 'fechaingresotramite', 'fechafirmaeap',
            'numeropoder', 'fechaeap30', 'adyretecnicomedico', 'adyrecomplementario', 'nivelprocedimiento',
            'adyreactatmc', 'adinformeempleador', 'addocumentacionmedica', 'notatecnicomedico', 'fechanotatecnicomedico', 
            'especialistas','nombremedico','cargomedico','fontSize','marginSize','documentoadjunto','textocomplementario',
            'nombretramite','nombre','ci','ciexp','telefono','sexo','afiliadoTexto','nrosolrevdic2','fechasolrevdic2',
            'fechapressolrevdic2','fechaautoadmision2'));

        return $pdf->stream('preview.adjunto');
    }

    /* NUEVO 241125 */
    public function previewLibre(Request $request, Cliente $cliente, Tramite $tramite)
    {
        $clienteid = $cliente->id;
        $tipoPdf = $request->input('tipo_pdf4');
        $nombretramite = $request->input('tramite4');
        $fechaactual = Carbon::parse($request->input('fechaactual4'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $nivelprocedimiento = $request->input('nivelprocedimiento4');
        $subProcedimiento = $request->input('subnivelprocedimiento4');
        $nombremedico = $request->input('nombremedico4');
        $mostrarencabezado = $request->input('mostrarencabezado4');
        $cargomedico = $request->input('cargomedico4');
        $textocomplementario = $request->input('textocomplementario4');
        $documentoadjunto = $request->input('documentoadjunto4');
        $fontSize = $request->input('fontsize4', '15px');
        $marginSize = $request->input('marginsize4', '1.5cm 3cm 1.5cm 3cm');
        $notatecnicomedico = $request->input('notatecnicomedico4');
        $contenidoLibre = $request->input('contenidoLibre');
        $emisor = $request->input('emisor4');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $clienteid)->where('servicio', $nombretramite)->first();
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $nombre = '';
        $ci = '';
        $ciexp = '';
        $telefono = '';
        $sexo = '';

        if ($emisor === 'CLIENTE') {
            $nombre = $cliente->nombrecompleto;
            $ci = $cliente->ci;
            $ciexp = $cliente->ciexp;
            $telefono = Str::startsWith($cliente->celular, '591') 
            ? substr($cliente->celular, 3) 
            : $cliente->celular;
            $sexo = strtolower($cliente->genero);
        } elseif ($emisor === 'APODERADO') {
            $apoderadoNombre = $request->input('apoderado4');
            $apoderado = DB::table('proveedoresservicios')
                ->where('razonsocial', $apoderadoNombre)
                ->first();
            if ($apoderado) {
                $nombre = $apoderado->razonsocial;
                $ci = $apoderado->ci;
                $ciexp = $apoderado->ciexp;
                $telefono = $apoderado->celularcorporativo;
                $sexo = strtolower($apoderado->sexo);
            }
        }

        $pdfView = '';
        $pdfView = 'admin.tramites.misivaslibres.misivalibre';

        $generocliente = Cliente::where('id', $clienteid)->value('genero');
        $afiliadoTexto = (strtoupper($generocliente) === 'FEMENINO') ? 'de la Afiliada' : 'del Afiliado';

        $pdf = PDF::loadView($pdfView, compact('cliente', 'fechaactual', 'nivelprocedimiento', 'notatecnicomedico', 
            'nombremedico','cargomedico','fontSize','marginSize','documentoadjunto','textocomplementario',
            'nombretramite','afiliadoTexto','nombre','ci','ciexp','telefono','sexo','numeropoder','contenidoLibre','tipoPdf',
            'emisor','mostrarencabezado'));

        return $pdf->stream('preview.libre');
    }
    public function generarlibre(Request $request, Cliente $cliente, Tramite $tramite)
    {
        $clienteid = $cliente->id;
        $tipoPdf = $request->input('tipo_pdf4');
        $nombretramite = $request->input('tramite4');
        $fechaactual = Carbon::parse($request->input('fechaactual4'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $nivelprocedimiento = $request->input('nivelprocedimiento4');
        $subProcedimiento = $request->input('subnivelprocedimiento4');
        $nombremedico = $request->input('nombremedico4');
        $cargomedico = $request->input('cargomedico4');
        $mostrarencabezado = $request->input('mostrarencabezado4');
        $textocomplementario = $request->input('textocomplementario4');
        $documentoadjunto = $request->input('documentoadjunto4');
        $fontSize = $request->input('fontsize4', '15px');
        $marginSize = $request->input('marginsize4', '1.5cm 3cm 1.5cm 3cm');
        $notatecnicomedico = $request->input('notatecnicomedico4');
        $contenidoLibre = $request->input('contenidoLibre');
        $emisor = $request->input('emisor4');

        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $clienteid)->where('servicio', $nombretramite)->first();
        $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

        $nombre = '';
        $ci = '';
        $ciexp = '';
        $telefono = '';
        $sexo = '';

        if ($emisor === 'CLIENTE') {
            $nombre = $cliente->nombrecompleto;
            $ci = $cliente->ci;
            $ciexp = $cliente->ciexp;
            $telefono = Str::startsWith($cliente->celular, '591') 
            ? substr($cliente->celular, 3) 
            : $cliente->celular;
            $sexo = strtolower($cliente->genero);
        } elseif ($emisor === 'APODERADO') {
            $apoderadoNombre = $request->input('apoderado4');
            $apoderado = DB::table('proveedoresservicios')
                ->where('razonsocial', $apoderadoNombre)
                ->first();
            if ($apoderado) {
                $nombre = $apoderado->razonsocial;
                $ci = $apoderado->ci;
                $ciexp = $apoderado->ciexp;
                $telefono = $apoderado->celularcorporativo;
                $sexo = strtolower($apoderado->sexo);
            }
        }

        $pdfView = '';
        $pdfView = 'admin.tramites.misivaslibres.misivalibre';

        $generocliente = Cliente::where('id', $clienteid)->value('genero');
        $afiliadoTexto = (strtoupper($generocliente) === 'FEMENINO') ? 'de la Afiliada' : 'del Afiliado';

        $pdf = PDF::loadView($pdfView, compact('cliente', 'fechaactual', 'nivelprocedimiento', 'notatecnicomedico', 
            'nombremedico','cargomedico','fontSize','marginSize','documentoadjunto','textocomplementario',
            'nombretramite','afiliadoTexto','nombre','ci','ciexp','telefono','sexo','numeropoder','contenidoLibre','tipoPdf',
            'emisor','mostrarencabezado'));

        $timestamp = now()->format('Ymd_His');
        
        $pdfName = "{$cliente->nombrecompleto}_{$timestamp}.pdf";

        $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$nombretramite}/MISIVAS LIBRES");
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }
        $pdfPath = "{$carpetaCliente}/{$pdfName}";
        file_put_contents($pdfPath, $pdf->output());
        
        $registrosExistentes = Tramite::where('nivelprocedimiento', $nivelprocedimiento)
            ->where('clienteid', $clienteid)
            ->where('tramite', $request->tramite)
            ->where(function ($query) use ($subProcedimiento) {
                $query->where('subprocedimiento', $subProcedimiento)
                    ->orWhere('subprocedimiento', 'LIKE', $subProcedimiento . '%');
            })
            ->get();

        if ($registrosExistentes->isEmpty()) {
            $nro = 1;
        } else {
            $nro = $registrosExistentes->count() + 1;
        }

        Tramite::create([
            'usuarioid' => $request->usuarioid4,
            'usuarioregistro' => $request->usuarioregistro4,
            'fechasubida' => $request->fechasubida4,
            'tramite' => $request->tramite4,
            'idtramite' => $request->idtramite4,
            'apoderado' => $request->apoderado4,
            'nivelprocedimiento' => $nivelprocedimiento,
            'subprocedimiento' => $subProcedimiento,
            'tipocarta' => $request->tipo_pdf4,
            'tipo' => 'MISIVA LIBRE',
            'nro' => $nro,
            'clienteid' => $clienteid,
            'clientenombre' => $cliente->nombrecompleto,
            'document' => $pdfName
        ]);

        return response()->download($pdfPath);
    }
    public function guardarrespuestamisivalibre(Request $request, Cliente $cliente)
    {
        $request->validate([
            'document2ml' => 'nullable|file|mimes:pdf',
            'document3ml' => 'nullable|file|mimes:pdf',
            'observacionesml' => '',
            'citenotaml' => '',
            'fechacitenotaml' => '',
            'fechainclusionml' => '',
        ]);

        $tramite = Tramite::findOrFail($request->tramite_id);

        if ($request->hasFile('document2ml')) {
            $archivo = $request->file('document2ml');
            $archivoNombre = time() . '_' . $archivo->getClientOriginalName();

            $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$request->nombretramite}/MISIVAS LIBRES");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            $archivo->move($carpetaCliente, $archivoNombre);
            $tramite->document2 = $archivoNombre;
        }
        if ($request->hasFile('document3ml')) {
            $archivo = $request->file('document3ml');
            $archivoNombre2 = time() . '_' . $archivo->getClientOriginalName();

            $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$request->nombretramite}/MISIVAS LIBRES");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            $archivo->move($carpetaCliente, $archivoNombre2);
            $tramite->document3 = $archivoNombre2;
        }

        $tramite->observaciones = $request->observacionesml;
        $tramite->citenota = $request->citenotaml;
        $tramite->fechacitenota = $request->fechacitenotaml;
        $tramite->fechainclusion = $request->fechainclusionml;
        $tramite->save();

        return back()->with('info', 'Respuesta guardada correctamente.');
    }

    // AGENDAMIENTO
    public function guardaragendamiento(Request $request)
    {
        $request->validate([
            'clienteidvisible' => '',
            'clientenombre2' => '',
            'tramite' => '',
            'fechaprogramacion' => '',
            'horaprogramacion' => '',
            'documentoagendamiento' => ''
        ]);

        $archivoNombre = null;
        if ($request->hasFile('documentoagendamiento')) {
            $archivo = $request->file('documentoagendamiento');
            $archivoNombre = time() . '_' . $archivo->getClientOriginalName();

            $carpeta = public_path("agendamiento/{$request->clienteidvisible}/{$request->tramite}");
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0755, true);
            }

            $archivo->move($carpeta, $archivoNombre);
        }

        AgendamientoProcedimiento::create([
            'clienteid' => $request->clienteidvisible,
            'clientenombre' => $request->clientenombre2,
            'tramite' => $request->tramite,
            'fechaprogramacion' => $request->fechaprogramacion,
            'horaprogramacion' => $request->horaprogramacion,
            'documentoagendamiento' => $archivoNombre,
            'asistencia' => 'NO',
            'usuarioregistroid' => auth()->id(),
            'usuarioregistronombre' => auth()->user()->name,
        ]);

        return back()->with('info', 'Agendamiento guardado correctamente');
    }
    public function reprogramaragendamiento(Request $request, $id)
    {
        $request->validate([
            'fechaprogramacion' => '',
            'horaprogramacion' => '',
            'documentoagendamiento' => '',
            'motivoreprogramacion' => ''
        ]);

        $ag = AgendamientoProcedimiento::findOrFail($id);

        // Guardar datos anteriores
        $ag->fechaanterior = $ag->fechaprogramacion;
        $ag->horaanterior = $ag->horaprogramacion;
        $ag->documentoanterior = $ag->documentoagendamiento;

        // Nuevos datos
        $ag->fechaprogramacion = $request->fechaprogramacion;
        $ag->horaprogramacion = $request->horaprogramacion;
        $ag->motivoreprogramacion = $request->motivoreprogramacion;

        if ($request->hasFile('documentoagendamiento')) {
            $archivo = $request->file('documentoagendamiento');
            $archivoNombre = time() . '_' . $archivo->getClientOriginalName();

            $carpeta = public_path("agendamiento/{$ag->clienteid}/{$ag->tramite}");
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0755, true);
            }

            $archivo->move($carpeta, $archivoNombre);
            $ag->documentoagendamiento = $archivoNombre;
        }

        $ag->save();

        return back()->with('info', 'Agendamiento reprogramado correctamente');
    }
    public function confirmarasistencia(Request $request)
    {
        if ($request->has('asistencia')) {
            AgendamientoProcedimiento::whereIn('id', $request->asistencia)
                ->update(['asistencia' => 'SI']);
        }

        return back()->with('info', 'Asistencia confirmada');
    }


    public function buscarprocedimiento(Request $request)
    {
        $tramite = $request->tramite3;
        $clienteid = $request->clienteid3;

        $primera = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', $request->nivelprocedimiento)
            ->where('subprocedimiento', $request->subnivelprocedimiento)
            ->where('tipocarta', 'PRIMERA CARTA SIT')
            ->where('tipo', 'CARTA / RECLAMO')
            ->where('tramite', $tramite)
            ->orderBy('id', 'desc')
        ->first();
        $segunda = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', $request->nivelprocedimiento)
            ->where('subprocedimiento', $request->subnivelprocedimiento)
            ->where('tipocarta', 'SEGUNDA CARTA SIT')
            ->where('tipo', 'CARTA / RECLAMO')
            ->where('tramite', $tramite)
            ->orderBy('id', 'desc')
        ->first();
        $tercera = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', $request->nivelprocedimiento)
            ->where('subprocedimiento', $request->subnivelprocedimiento)
            ->where('tipocarta', 'TERCERA CARTA SIT')
            ->where('tipo', 'CARTA / RECLAMO')
            ->where('tramite', $tramite)
            ->orderBy('id', 'desc')
        ->first();
        $primerareclamogp = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', $request->nivelprocedimiento)
            ->where('subprocedimiento', $request->subnivelprocedimiento)
            ->where('tipocarta', 'PRIMERA CARTA DE RECLAMO GP')
            ->where('tipo', 'CARTA / RECLAMO')
            ->where('tramite', $tramite)
            ->orderBy('id', 'desc')
        ->first();
        $primerareclamoaps = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', $request->nivelprocedimiento)
            ->where('subprocedimiento', $request->subnivelprocedimiento)
            ->where('tipocarta', 'PRIMERA CARTA DE RECLAMO APS')
            ->where('tipo', 'CARTA / RECLAMO')
            ->where('tramite', $tramite)
            ->orderBy('id', 'desc')
        ->first();
        $segundareclamogp = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', $request->nivelprocedimiento)
            ->where('subprocedimiento', $request->subnivelprocedimiento)
            ->where('tipocarta', 'SEGUNDA CARTA DE RECLAMO GP')
            ->where('tipo', 'CARTA / RECLAMO')
            ->where('tramite', $tramite)
            ->orderBy('id', 'desc')
        ->first();
        $segundareclamoaps = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', $request->nivelprocedimiento)
            ->where('subprocedimiento', $request->subnivelprocedimiento)
            ->where('tipocarta', 'SEGUNDA CARTA DE RECLAMO APS')
            ->where('tipo', 'CARTA / RECLAMO')
            ->where('tramite', $tramite)
            ->orderBy('id', 'desc')
        ->first();
        $tercerareclamogp = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', $request->nivelprocedimiento)
            ->where('subprocedimiento', $request->subnivelprocedimiento)
            ->where('tipocarta', 'TERCERA CARTA DE RECLAMO GP')
            ->where('tipo', 'CARTA / RECLAMO')
            ->where('tramite', $tramite)
            ->orderBy('id', 'desc')
        ->first();
        $tercerareclamoaps = Tramite::where('clienteid', $clienteid)
            ->where('nivelprocedimiento', $request->nivelprocedimiento)
            ->where('subprocedimiento', $request->subnivelprocedimiento)
            ->where('tipocarta', 'TERCERA CARTA DE RECLAMO APS')
            ->where('tipo', 'CARTA / RECLAMO')
            ->where('tramite', $tramite)
            ->orderBy('id', 'desc')
        ->first();

        if ($primera || $segunda || $tercera || $primerareclamogp || $primerareclamoaps || $segundareclamogp || $segundareclamoaps || $tercerareclamogp || $tercerareclamoaps) {
            return response()->json([
                'success' => true,
            // PRIMERA CARTA SIT
                'fechacitenota_primera' => $primera->fechacitenota ?? null,
                'citenota_primera' => $primera->citenota ?? null,
                'fechasubida_primera' => $primera->fechasubida ?? null,
                'fecharespuesta_primera' => $primera->fechainclusion ?? null,
            //
            // SEGUNDA CARTA SIT
                'fechacitenota_segunda' => $segunda->fechacitenota ?? null,
                'citenota_segunda' => $segunda->citenota ?? null,
                'fechasubida_segunda' => $segunda->fechasubida ?? null,
                'fecharespuesta_segunda' => $segunda->fechainclusion ?? null,
            //
            // TERCERA CARTA SIT
                'fechacitenota_tercera' => $tercera->fechacitenota ?? null,
                'citenota_tercera' => $tercera->citenota ?? null,
                'fechasubida_tercera' => $tercera->fechasubida ?? null,
                'fecharespuesta_tercera' => $tercera->fechainclusion ?? null,
            //
            // PRIMERA CARTA DE RECLAMO GP
                'fechacitenota_primerareclamogp' => $primerareclamogp->fechacitenota ?? null,
                'citenota_primerareclamogp' => $primerareclamogp->citenota ?? null,
                'fechasubida_primerareclamogp' => $primerareclamogp->fechasubida ?? null,
                'fecharespuesta_primerareclamogp' => $primerareclamogp->fechainclusion ?? null,
            //
            // PRIMERA CARTA DE RECLAMO APS
                'fechacitenota_primerareclamoaps' => $primerareclamoaps->fechacitenota ?? null,
                'citenota_primerareclamoaps' => $primerareclamoaps->citenota ?? null,
                'fechasubida_primerareclamoaps' => $primerareclamoaps->fechasubida ?? null,
                'fecharespuesta_primerareclamoaps' => $primerareclamoaps->fechainclusion ?? null,
            //
            // SEGUNDA CARTA DE RECLAMO GP
                'fechacitenota_segundareclamogp' => $segundareclamogp->fechacitenota ?? null,
                'citenota_segundareclamogp' => $segundareclamogp->citenota ?? null,
                'fechasubida_segundareclamogp' => $segundareclamogp->fechasubida ?? null,
                'fecharespuesta_segundareclamogp' => $segundareclamogp->fechainclusion ?? null,
            //
            // SEGUNDA CARTA DE RECLAMO APS
                'fechacitenota_segundareclamoaps' => $segundareclamoaps->fechacitenota ?? null,
                'citenota_segundareclamoaps' => $segundareclamoaps->citenota ?? null,
                'fechasubida_segundareclamoaps' => $segundareclamoaps->fechasubida ?? null,
                'fecharespuesta_segundareclamoaps' => $segundareclamoaps->fechainclusion ?? null,
            //
            // TERCERA CARTA DE RECLAMO GP
                'fechacitenota_tercerareclamogp' => $tercerareclamogp->fechacitenota ?? null,
                'citenota_tercerareclamogp' => $tercerareclamogp->citenota ?? null,
                'fechasubida_tercerareclamogp' => $tercerareclamogp->fechasubida ?? null,
                'fecharespuesta_tercerareclamogp' => $tercerareclamogp->fechainclusion ?? null,
            //
            // TERCERA CARTA DE RECLAMO APS
                'fechacitenota_tercerareclamoaps' => $tercerareclamoaps->fechacitenota ?? null,
                'citenota_tercerareclamoaps' => $tercerareclamoaps->citenota ?? null,
                'fechasubida_tercerareclamoaps' => $tercerareclamoaps->fechasubida ?? null,
                'fecharespuesta_tercerareclamoaps' => $tercerareclamoaps->fechainclusion ?? null,
            //
            ]);
        }

        return response()->json(['success' => false]);
    }
    public function guardarrespuesta(Request $request, Cliente $cliente)
    {
        $request->validate([
            'document2solicitud' => 'nullable|file|mimes:pdf',
            'observacionessolicitud' => '',
            'citenotasolicitud' => '',
            'fechacitenotasolicitud' => '',
            'fechainclusionsolicitud' => '',
        ]);

        $tramite = Tramite::findOrFail($request->tramite_id);

        if ($request->hasFile('document2solicitud')) {
            $archivo = $request->file('document2solicitud');
            $archivoNombre = time() . '_' . $archivo->getClientOriginalName();

            $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$request->nombretramite}/SOLICITUDES");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            $archivo->move($carpetaCliente, $archivoNombre);
            $tramite->document2 = $archivoNombre;
        }

        $tramite->observaciones = $request->observacionessolicitud;
        $tramite->citenota = $request->citenotasolicitud;
        $tramite->fechacitenota = $request->fechacitenotasolicitud;
        $tramite->fechainclusion = $request->fechainclusionsolicitud;
        $tramite->save();

        return back()->with('info', 'Respuesta guardada correctamente.');
    }
    /* public function guardarrespuestaadjunto(Request $request, Cliente $cliente)
    {
        $request->validate([
            'document2adjunto' => 'nullable|file|mimes:pdf',
            'observacionesadjunto' => '',
            'citenotaadjunto' => '',
            'fechacitenotaadjunto' => '',
            'fechainclusionadjunto' => '',
        ]);

        $tramite = Tramite::findOrFail($request->tramite_id);

        if ($request->hasFile('document2adjunto')) {
            $archivo = $request->file('document2adjunto');
            $archivoNombre = time() . '_' . $archivo->getClientOriginalName();

            $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$request->nombretramite}/ADJUNTOS Y RESPUESTAS");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            $archivo->move($carpetaCliente, $archivoNombre);
            $tramite->document2 = $archivoNombre;
        }

        $tramite->observaciones = $request->observacionesadjunto;
        $tramite->citenota = $request->citenotaadjunto;
        $tramite->fechacitenota = $request->fechacitenotaadjunto;
        $tramite->fechainclusion = $request->fechainclusionadjunto;
        $tramite->save();

        return back()->with('info', 'Respuesta guardada correctamente.');
    } */
    public function guardarrespuestaadjunto(Request $request, Cliente $cliente)
    {
        $request->validate([
            'document2adjunto' => 'nullable|file|mimes:pdf',
            'document3adjunto' => 'nullable|file|mimes:pdf',
            'observacionesadjunto' => '',
            'citenotaadjunto' => '',
            'fechacitenotaadjunto' => '',
            'fechainclusionadjunto' => '',
        ]);

        $tramite = Tramite::findOrFail($request->tramite_id);

        if ($request->hasFile('document2adjunto')) {
            $archivo = $request->file('document2adjunto');
            $archivoNombre = time() . '_' . $archivo->getClientOriginalName();

            $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$request->nombretramite}/ADJUNTOS Y RESPUESTAS");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            $archivo->move($carpetaCliente, $archivoNombre);
            $tramite->document2 = $archivoNombre;
        }
        if ($request->hasFile('document3adjunto')) {
            $archivo = $request->file('document3adjunto');
            $archivoNombre2 = time() . '_' . $archivo->getClientOriginalName();

            $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$request->nombretramite}/ADJUNTOS Y RESPUESTAS");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            $archivo->move($carpetaCliente, $archivoNombre2);
            $tramite->document3 = $archivoNombre2;
        }

        $tramite->observaciones = $request->observacionesadjunto;
        $tramite->citenota = $request->citenotaadjunto;
        $tramite->fechacitenota = $request->fechacitenotaadjunto;
        $tramite->fechainclusion = $request->fechainclusionadjunto;
        $tramite->save();

        return back()->with('info', 'Respuesta guardada correctamente.');
    }
    public function guardarrespuestacarta(Request $request, Cliente $cliente)
    {
        $request->validate([
            'document2carta' => 'nullable|file|mimes:pdf',
            'document3carta' => 'nullable|file|mimes:pdf',
            'observacionescarta' => '',
            'citenotacarta' => '',
            'fechacitenotacarta' => '',
            'fechainclusioncarta' => '',
        ]);

        $tramite = Tramite::findOrFail($request->tramite_id);

        if ($request->hasFile('document2carta')) {
            $archivo = $request->file('document2carta');
            $archivoNombre = time() . '_' . $archivo->getClientOriginalName();

            $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$request->nombretramite}/CARTAS Y RECLAMOS");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            $archivo->move($carpetaCliente, $archivoNombre);
            $tramite->document2 = $archivoNombre;
        }
        if ($request->hasFile('document3carta')) {
            $archivo = $request->file('document3carta');
            $archivoNombre2 = time() . '_' . $archivo->getClientOriginalName();

            $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$request->nombretramite}/CARTAS Y RECLAMOS");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            $archivo->move($carpetaCliente, $archivoNombre2);
            $tramite->document3 = $archivoNombre2;
        }

        $tramite->observaciones = $request->observacionescarta;
        $tramite->citenota = $request->citenotacarta;
        $tramite->fechacitenota = $request->fechacitenotacarta;
        $tramite->fechainclusion = $request->fechainclusioncarta;
        $tramite->save();

        return back()->with('info', 'Respuesta guardada correctamente.');
    }
    public function guardarrespuestacartaformulario(Request $request, Cliente $cliente)
    {
        $request->validate([
            'document4carta' => 'nullable|file|mimes:pdf',
            'corsolicitudcarta' => '',
            'nroformulariocarta' => '',
            'fechaestadotramitecarta' => '',
        ]);

        $tramite = Tramite::findOrFail($request->tramite_id);

        if ($request->hasFile('document4carta')) {
            $archivo = $request->file('document4carta');
            $archivoNombre = time() . '_' . $archivo->getClientOriginalName();

            $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$request->nombretramite}/{$request->proceso}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            $archivo->move($carpetaCliente, $archivoNombre);
            $tramite->document4 = $archivoNombre;
        }

        $tramite->corsolicitud = $request->corsolicitudcarta;
        $tramite->nroformulario = $request->nroformulariocarta;
        $tramite->fechaestadotramite = $request->fechaestadotramitecarta;
        $tramite->save();

        return back()->with('info', 'Respuesta guardada correctamente.');
    }

    public function guardarsoloform(Request $request, Cliente $cliente)
    {
        $request->validate([
            'sdocumento' => 'nullable|file|mimes:pdf',
            'snroformulario' => 'required',
            'sfecha' => 'required|date',
        ]);
        $nivelProcedimiento = $request->input('snivelprocedimiento');
        $subProcedimiento = $request->input('ssubprocedimiento');
        // 👉 NUEVO REGISTRO
        $tramite = new Tramite();

        if ($request->hasFile('sdocumento')) {
            $archivo = $request->file('sdocumento');
            $archivoNombre = time() . '_' . $archivo->getClientOriginalName();

            $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$request->nombretramite}/CARTAS Y RECLAMOS");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            $archivo->move($carpetaCliente, $archivoNombre);
            $tramite->document = $archivoNombre;
        }

        $registrosExistentes = Tramite::where('nivelprocedimiento', $nivelProcedimiento)
            ->where('clienteid', $request->clienteid)
            ->where('tramite', $request->nombretramite)
            ->where(function ($query) use ($subProcedimiento) {
                $query->where('subprocedimiento', $subProcedimiento)
                    ->orWhere('subprocedimiento', 'LIKE', $subProcedimiento . '%');
            })
            ->get();

        if ($registrosExistentes->isEmpty()) {
            $nro = 1;
        } else {
            $nro = $registrosExistentes->count() + 1;
        }

        // Datos del nuevo trámite
        $tramite->usuarioid = $request->usuarioid;
        $tramite->usuarioregistro = $request->usuarioregistro;
        $tramite->clienteid = $request->clienteid;
        $tramite->clientenombre = $request->clientenombre;
        $tramite->apoderado = $request->apoderado;
        $tramite->idtramite = $request->idtramite;
        $tramite->tramite = $request->nombretramite;
        $tramite->nivelprocedimiento = $nivelProcedimiento;
        $tramite->subprocedimiento = $subProcedimiento;
        $tramite->fechasubida = $request->sfecha;
        $tramite->tipo = 'CARTA / RECLAMO';
        $tramite->tipocarta = 'FORMULARIO - ' . $request->snroformulario;
        $tramite->nro = $nro;
        $tramite->save();

        return back()->with('info', 'Formulario guardado correctamente.');
    }




/* $documento = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->first(); */
    
    public function guardartramitesclienteitaseguimiento(StoreTramiteRequest $request, Cliente $cliente)
    {
        $tramite = $request->input('tramite', []);
        $nivelprocedimiento = $request->input('nivelprocedimiento', []);
        $subprocedimiento = $request->input('subprocedimiento', []);
        $fechasubida = $request->input('fechasubida', []);
        $seguro = $request->input('seguro', []);
        $estadodictamen = $request->input('estadodictamen', []);
        $porcentajeaceptorechazodictamen = $request->input('porcentajeaceptorechazodictamen', []);
        $viaja = $request->input('viaja', []);
        $departamentoviaja = $request->input('departamentoviaja', []);
        $dep1viaja = $request->input('dep1viaja', []);
        $fechadep1viaja = $request->input('fechadep1viaja', []);
        $dep2viaja = $request->input('dep2viaja', []);
        $fechadep2viaja = $request->input('fechadep2viaja', []);
        $dep3viaja = $request->input('dep3viaja', []);
        $fechadep3viaja = $request->input('fechadep3viaja', []);
        $dep4viaja = $request->input('dep4viaja', []);
        $fechadep4viaja = $request->input('fechadep4viaja', []);
        $dep5viaja = $request->input('dep5viaja', []);
        $fechadep5viaja = $request->input('fechadep5viaja', []);
        $dep6viaja = $request->input('dep6viaja', []);
        $fechadep6viaja = $request->input('fechadep6viaja', []);
        $dep7viaja = $request->input('dep7viaja', []);
        $fechadep7viaja = $request->input('fechadep7viaja', []);
        $dep8viaja = $request->input('dep8viaja', []);
        $fechadep8viaja = $request->input('fechadep8viaja', []);

        $fechagestoradictamen = $request->input('fechagestoradictamen', []);
        $fechasinestro = $request->input('fechasinestro', []);
        $fechacobrocontrato = $request->input('fechacobrocontrato', []);
        $montocontrato = $request->input('montocontrato', []);
        $motivorechazo = $request->input('motivorechazo', []);
        $notaseguimiento = $request->input('notaseguimiento', []);

        foreach ($tramite as $key => $item) {
            Tramite::create([
                'document' => null,
                'usuarioid' => $request->usuarioid,
                'usuarioregistro' => $request->usuarioregistro,
                'clienteitaid' => $request->clienteitaid,
                'clienteitanombre' => $request->clienteitanombre,
                'apoderado' => $request->apoderado,
                'tramite' => $tramite[$key] ?? null,
                'nivelprocedimiento' => $nivelprocedimiento[$key] ?? null,
                'subprocedimiento' => $subprocedimiento[$key] ?? null,
                'fechasubida' => $fechasubida[$key] ?? null,
                'seguro' => $seguro[$key] ?? null,
                'estadodictamen' => $estadodictamen[$key] ?? null,
                'porcentajeaceptorechazodictamen' => $porcentajeaceptorechazodictamen[$key] ?? null,
                'viaja' => $viaja[$key] ?? null,
                'departamentoviaja' => $departamentoviaja[$key] ?? null,
                'dep1viaja' => $dep1viaja[$key] ?? null,
                'fechadep1viaja' => $fechadep1viaja[$key] ?? null,
                'dep2viaja' => $dep2viaja[$key] ?? null,
                'fechadep2viaja' => $fechadep2viaja[$key] ?? null,
                'dep3viaja' => $dep3viaja[$key] ?? null,
                'fechadep3viaja' => $fechadep3viaja[$key] ?? null,
                'dep4viaja' => $dep4viaja[$key] ?? null,
                'fechadep4viaja' => $fechadep4viaja[$key] ?? null,
                'dep5viaja' => $dep5viaja[$key] ?? null,
                'fechadep5viaja' => $fechadep5viaja[$key] ?? null,
                'dep6viaja' => $dep6viaja[$key] ?? null,
                'fechadep6viaja' => $fechadep6viaja[$key] ?? null,
                'dep7viaja' => $dep7viaja[$key] ?? null,
                'fechadep7viaja' => $fechadep7viaja[$key] ?? null,
                'dep8viaja' => $dep8viaja[$key] ?? null,
                'fechadep8viaja' => $fechadep8viaja[$key] ?? null,
                'fechagestoradictamen' => $fechagestoradictamen[$key] ?? null,
                'fechasinestro' => $fechasinestro[$key] ?? null,
                'fechacobrocontrato' => $fechacobrocontrato[$key] ?? null,
                'montocontrato' => $montocontrato[$key] ?? null,
                'motivorechazo' => $motivorechazo[$key] ?? null,
                'notaseguimiento' => $notaseguimiento[$key] ?? null,
            ]);
        }

        // Redirección basada en la URL previa
        $previousUrl = url()->previous();
        if (Str::contains($previousUrl, 'procmasahereditaria')) {
            return redirect()->route('admin.tramites.procmasahereditaria', $cliente)->with('info', 'La nota de seguimiento se registró con éxito');
        } elseif (Str::contains($previousUrl, 'procinvalidez')) {
            return redirect()->route('admin.tramites.procinvalidez', $cliente)->with('info', 'La nota de seguimiento se registró con éxito');
        } elseif (Str::contains($previousUrl, 'procapelacion')) {
            return redirect()->route('admin.tramites.procapelacion', $cliente)->with('info', 'La nota de seguimiento se registró con éxito');
        } elseif (Str::contains($previousUrl, 'proccompensacionsenasir')) {
            return redirect()->route('admin.tramites.proccompensacionsenasir', $cliente)->with('info', 'La nota de seguimiento se registró con éxito');
        } elseif (Str::contains($previousUrl, 'procjubilacion')) {
            return redirect()->route('admin.tramites.procjubilacion', $cliente)->with('info', 'La nota de seguimiento se registró con éxito');
        } elseif (Str::contains($previousUrl, 'procpensionmuerte')) {
            return redirect()->route('admin.tramites.procpensionmuerte', $cliente)->with('info', 'La nota de seguimiento se registró con éxito');
        } elseif (Str::contains($previousUrl, 'procretiroaportesparcial')) {
            return redirect()->route('admin.tramites.procretiroaportesparcial', $cliente)->with('info', 'La nota de seguimiento se registró con éxito');
        } elseif (Str::contains($previousUrl, 'procretiroaportestotal')) {
            return redirect()->route('admin.tramites.procretiroaportestotal', $cliente)->with('info', 'La nota de seguimiento se registró con éxito');
        } elseif (Str::contains($previousUrl, 'procsegundasolicitud')) {
            return redirect()->route('admin.tramites.procsegundasolicitud', $cliente)->with('info', 'La nota de seguimiento se registró con éxito');
        } elseif (Str::contains($previousUrl, 'proctercerasolicitud')) {
            return redirect()->route('admin.tramites.proctercerasolicitud', $cliente)->with('info', 'La nota de seguimiento se registró con éxito');
        } else {
            return redirect()->route('admin.tramites.index')->with('info', 'La nota de seguimiento se registró con éxito');
        }
    }


public function solrevisioninformefinal(Request $request, Informefinal $informefinal)
{
    // Validar los datos del formulario
    $request->validate([
        'observaciones' => 'required|string|max:255',
    ]);

    // Obtener el informe final por su ID
    $informeFinal = Informefinal::findOrFail($request->idinformefinal);

    // Asignar las observaciones al informe final
    $informeFinal->observaciones = $request->observaciones;
    $informeFinal->estado = 'SOLICITO REVISION';
    // Guardar los cambios en el informe final
    $informeFinal->save();

    // Eliminar el informe final
    $informeFinal->delete();

    // No elimines el informe final aquí si planeas usarlo después
    // $informefinal->delete();

    // Redirigir a la ruta adecuada, ajusta según sea necesario
    // Suponiendo que $informefinal->clienteitaid es el ID del cliente
    $cliente = Cliente::find($informefinal->clienteitaid);

    return redirect()->route('admin.informesfinales.index', $informefinal)->with('info', 'Solicitud enviada exitosamente.');
}
public function aprobarinformefinalfs(Request $request, Informefinal $informefinal)
{
    $informeFinal = Informefinal::findOrFail($request->idinformefinal);
    $informeFinal->estado = 'APROBADO';
    $informeFinal->save();

    $cliente = Cliente::find($informefinal->clienteitaid);

    return redirect()->route('admin.informesfinales.index', $informefinal)->with('info', 'Informe aprobado exitosamente.');
}

public function guardaraprobacioninformefinal(Request $request, $id)
{
    $request->validate([
        'cliente' => 'required|string',
        'fechabateria' => 'required|date',
        'estado' => 'required|string',
        'proveedornombre' => 'required|string',
        'cliente' => 'required|string',
    ]);
    $usuarioId = auth()->user()->id;
    $usuarioRegistro = auth()->user()->name;

    AprobacionInformeFinal::create([
        'cliente' => $request->cliente,
        'fechabateria' => $request->fechabateria,
        'estado' => $request->estado,
        'proveedorasignado' => $request->proveedornombre,
        'clienteitaid' => $id,
        'clienteitanombre' => $request->cliente,
        'usuarioid' => $usuarioId,
        'usuarioregistro' => $usuarioRegistro,
    ]);

    return redirect()->route('admin.informesfinales.index')->with('info', 'Aprobación guardada exitosamente.');
}
public function guardarproveedorinformefinal(StoreProveedorInformefinalRequest $request, $id)
{
    $request->validate([
        'cliente' => 'required|string',
        'fechabateria' => 'required|date',
        'celularproveedor' => 'required|string',
        // 'proveedorasignado' => 'required', // No necesitas validar este campo si estás cambiando la lógica
    ]);

    $usuarioId = auth()->user()->id;
    $usuarioRegistro = auth()->user()->name;

    // Buscar el nombre del proveedor basado en el ID seleccionado
    $proveedorAsignado = Proveedor::findOrFail($request->proveedorasignado)->proveedor;

    // Crear el registro en ProveedorInformefinal utilizando el nombre del proveedor
    ProveedorInformefinal::create([
        'cliente' => $request->cliente,
        'fechabateria' => $request->fechabateria,
        'proveedorasignado' => $proveedorAsignado, // Guardar el nombre del proveedor en lugar del ID
        'celularproveedor' => $request->celularproveedor,
        'clienteitaid' => $id,
        'clienteitanombre' => $request->cliente,
        'usuarioid' => $usuarioId,
        'usuarioregistro' => $usuarioRegistro,
    ]);

    return redirect()->route('admin.informesfinales.index')->with('info', 'Proveedor asignado exitosamente.');
}

public function guardarinformefinal(StoreInformefinalRequest $request, $id, Cliente $cliente)
{
    $clienteitaid = $cliente->id;
    
    $archivo_name = null;
    if ($request->hasFile('document')) {
        $file = $request->file('document');
        
        // Ruta de la carpeta del cliente
        $carpetaCliente = public_path("/informesfinalesclientesita/{$clienteitaid}");
        
        // Crear la carpeta si no existe
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }
        
        // Nombre único para el archivo
        $archivo_name = time() . '_' . $file->getClientOriginalName();
        
        // Mover el archivo a la carpeta del cliente
        $file->move($carpetaCliente, $archivo_name);
    }
    $usuarioId = auth()->user()->id;
    $usuarioRegistro = auth()->user()->name;

        InformeFinal::create([
            'cliente' => $request->cliente,
            'fechabateria' => $request->fechabateria,
            'estado' => $request->estado,
            'clienteitaid' => $id,
            'clienteitanombre' => $request->cliente,
            'document' => $archivo_name,
            'usuarioid' => $usuarioId,
            'usuarioregistro' => $usuarioRegistro,
        ]);

    return redirect()->route('admin.informesfinales.index')->with('info', 'Documento subido exitosamente.');
}


public function buscarprogramacionesclienteita(Cliente $cliente, Request $request)
    {
        return $this->index($cliente, $request);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.empresas.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Empresa $empresa)
    {
        $empresa = Empresa::create($request->all());

        return redirect()->route('admin.empresas.index', $empresa)->with('info', 'La empresa se creó con exito');
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
    public function edit(Empresa $empresa)
    {
        return view('admin.empresas.edit', compact('empresa'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Empresa $empresa)
    {

        $empresa->update($request->all());

        return redirect()->route('admin.empresas.index', $empresa)->with('info', 'La empresa se actualizó con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Empresa $empresa)
    {
        $empresa->delete();

        return redirect()->route('admin.empresas.index', $empresa)->with('eliminar', 'ok');
    }
    
    
}
