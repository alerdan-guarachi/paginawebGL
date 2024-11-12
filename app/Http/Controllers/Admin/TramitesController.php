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
use App\Models\Personal;
use App\Models\Aprobacioninformefinal;
use App\Models\ProveedorInformefinal;
use App\Models\Informefinal;
use App\Models\Proveedor;
use App\Models\Tramite;
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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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
            'fechabateria' => 'required|date', // Asegúrate de validar que sea una fecha válida
        ]);

        // Obtener los valores validados
        $clienteID = $validatedData['clienteitaid'];
        $apoderadoAsignado = $validatedData['apoderadoasignado'];
        $fechaBateria = $validatedData['fechabateria'];

        // Encontrar el registro específico para actualizar
        $tramitesubcliente = Tramitesubcliente::where('clienteitaid', $clienteID)
            ->where('fechabateria', $fechaBateria)
            ->first();

        if ($tramitesubcliente) {
            // Actualizar el registro
            $tramitesubcliente->update([
                'apoderadoasignado' => $apoderadoAsignado,
            ]);
            // Mensaje de éxito
            return redirect()->route('admin.tramites.index')->with('info', 'Apoderado asignado exitosamente.');
        } else {
            // Manejar el caso donde el registro no se encuentra
            return redirect()->route('admin.tramites.index')->with('error', 'Registro no encontrado.');
        }
    }

    public function index(Cliente $cliente, Request $request, Tramite $tramite)
    {
        // Obtener proveedores, aprobaciones y fechas únicas
        $proveedores = Proveedor::orderBy('proveedor')->get(['id', 'proveedor', 'celular']);
        $aprobaciones = AprobacionInformeFinal::all();
        $fechas = Programacionsubcliente::pluck('fechabateria')->unique()->sort()->toArray();

        // Construir la consulta con los filtros aplicados
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

        // Obtener datos agrupados por cliente y fecha
        $programacionclientes = $query->get();
        $grouped = $programacionclientes->groupBy(function($item) {
            return $item->clienteitanombre . '|' . $item->fechabateria;
        });

        $result = [];

        foreach ($grouped as $key => $items) {
            // Descomponer el grupo en cliente y fecha
            list($clienteNombre, $fechabateria) = explode('|', $key);
            $clienteitaid = $items->first()->clienteitaid;

            $usuarioAutenticado = auth()->user()->name; // O el atributo que necesites

            $clientes = Tramitesubcliente::where('clienteitanombre', $clienteNombre)
                ->where('fechabateria', $fechabateria)
                
                ->get();


            $tipocliente = $clientes->map(function($clienteObj) {
                return $clienteObj->tramite;
            })->unique();

            foreach ($tipocliente as $tipo) {
                // Obtener el último trámite para el tipo actual
                $ultimoTramite = Tramite::where('clienteitaid', $clienteitaid)
                    ->where('tramite', $tipo) // Asumiendo que hay un campo 'tipo_tramite'
                    ->whereNotIn('nivelprocedimiento', ['CARTAS / RECLAMOS', 'ADJUNTOS Y RESPUESTAS', 'SEGUIMIENTO'])
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Obtener la última carta basada en el nivel de procedimiento 'CARTAS / RECLAMOS'
                $ultimacarta = Tramite::where('clienteitaid', $clienteitaid)
                    ->where('tramite', $tipo)
                    ->where('nivelprocedimiento', 'CARTAS / RECLAMOS')
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Obtener el último trámite para el tipo actual
                $ultimosubTramite = Tramite::where('clienteitaid', $clienteitaid)
                    ->where('tramite', $tipo) // Asumiendo que hay un campo 'tipo_tramite'
                    ->whereNotIn('nivelprocedimiento', ['CARTAS / RECLAMOS', 'ADJUNTOS Y RESPUESTAS', 'SEGUIMIENTO'])
                    ->orderBy('created_at', 'desc')
                    ->first();

                $iniciotramite = Tramite::where('clienteitaid', $clienteitaid)
                    ->where('tramite', $tipo) // Asumiendo que hay un campo 'tipo_tramite'
                    ->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')
                    ->orderBy('created_at', 'asc')
                    ->first();

                $estadotramite = Tramitesubcliente::where('clienteitaid', $clienteitaid)
                    ->where('tramite', $tipo)
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Asignar valores a las variables si existen, o un valor por defecto
                $nivelprocedimientotramite = $ultimoTramite ? $ultimoTramite->nivelprocedimiento : 'NO INICIADO';
                $ultimacartatramite = $ultimacarta ? $ultimacarta->subprocedimiento : 'NINGUNA CARTA';
                $nivelsubprocedimientotramite = $ultimosubTramite ? $ultimosubTramite->subprocedimiento : 'NO INICIADO';
                $iniciotramitecliente = $iniciotramite ? $iniciotramite->fechasubida : 'NO INICIADO';
                $estadotramitecliente = $estadotramite ? $estadotramite->estado : 'NO INICIADO';
                
                // Obtener datos del proveedor asignado y documentos subidos
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
                    ->value('apoderadoasignado');

                // Calcular el mensaje de días restantes
                $mensajeDias = 'N/A';
                if ($ultimoTramite) {
                    if ($ultimoTramite->nivelprocedimiento == 'DICTAMEN' && $ultimoTramite->subprocedimiento == 'NOTIFICACIÓN DE DICTAMEN') {
                        // Si el nivel de procedimiento es DICTAMEN y el subprocedimiento es NOTIFICACIÓN DE DICTAMEN
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
                        $recepcionTramite = Tramite::where('clienteitaid', $clienteitaid)
                            ->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')
                            ->orderBy('fechasubida', 'desc')
                            ->first();
                        if ($recepcionTramite && $recepcionTramite->fechasubida) {
                            $fechaSubida = \Carbon\Carbon::parse($recepcionTramite->fechasubida);
                            $diasRestantes = max(0, 10 - $fechaSubida->diffInDays(\Carbon\Carbon::now()));
                            $mensajeDias = $diasRestantes == 1 ? '1 DÍA RESTANTE' : "$diasRestantes DÍAS RESTANTES";
                        }
                    } elseif ($ultimoTramite->subprocedimiento == 'ESTADO DE AHORRO PREVISIONAL') {
                        $estadoAhorroPrevisional = Tramite::where('clienteitaid', $clienteitaid)
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

                // Preparar la lista de acciones con su estado
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

                // Añadir los resultados al array final solo si el estado es completo
                if ($estado === 'COMPLETO') {
                    $result[] = [
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
                    ];
                }
            }
        }

        
        // RELLENAR CON SIGUIENTE APDOERADO
            $apoderados = Personal::orderBy('nombrecompleto')
            ->where('cargo', 'EJECUTIVO PRESTACIONES')
            ->pluck('nombrecompleto', 'nombrecompleto');

            $apoderadosArray = $apoderados->keys()->toArray();

            $ultimoApoderado = Tramitesubcliente::value('apoderadoasignado');

            $indiceActual = array_search($ultimoApoderado, $apoderadosArray);

            if ($indiceActual === false) {
            $indiceActual = -1;
            }

            $indiceSiguiente = ($indiceActual + 1) % count($apoderadosArray);
            $apoderadoSiguiente = $apoderadosArray[$indiceSiguiente];

            session(['indice_apoderado' => $indiceSiguiente]);
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
    
        return view('admin.tramites.index', compact('apelacionCount', 'derivarCount', 'finalizadoCount', 'pendienteCount', 'noIniciadoCount', 'usuarioAutenticado','proveedores', 'result', 'cliente', 'fechas', 'aprobaciones','apoderados', 'apoderadoSiguiente'));
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

    public function procmasahereditaria(Request $request, Cliente $cliente)
        {
            $nombrecompleto = $cliente->nombrecompleto;
            $id = $cliente->id;
            $personal = Personal::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();

            $nombreclienteita = $cliente->nombrecompleto;
            $procedimientotramites = Tramite::where('clienteitanombre', $nombreclienteita)
                                    ->where('tramite', 'MASA HEREDITARIA')
                                    ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
                                    ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
                                    ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
                                    ->simplePaginate(10000);
            
            return view('admin.tramites.procmasahereditaria', compact('procedimientotramites','id','cliente','nombrecompleto', 'personal'));
        }
    public function procapelacion(Request $request, Cliente $cliente)
        {
            $nombrecompleto = $cliente->nombrecompleto;
            $id = $cliente->id;
            $personal = Personal::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
            
            return view('admin.tramites.procapelacion', compact('id','cliente','nombrecompleto', 'personal'));
        }
    public function proccompensacionsenasir(Request $request, Cliente $cliente)
        {
            $nombrecompleto = $cliente->nombrecompleto;
            $id = $cliente->id;
            $personal = Personal::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
            
            $nombreclienteita = $cliente->nombrecompleto;
            $procedimientotramites = Tramite::where('clienteitanombre', $nombreclienteita)
                                    ->where('tramite', 'COMPENZACIÓN SENASIR')
                                    ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
                                    ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
                                    ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
                                    ->simplePaginate(10000);

            return view('admin.tramites.proccompensacionsenasir', compact('procedimientotramites','id','cliente','nombrecompleto', 'personal'));
        }
    public function procinvalidez(Request $request, Cliente $cliente)
        {
            $nombrecompleto = $cliente->nombrecompleto;
            $id = $cliente->id;
            $personal = Personal::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
            $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')->pluck('tipocarta', 'id');

            $inicioocontinuidad = Tramite::where('clienteitaid', $cliente->id)
                                        ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
                                        ->exists();
            $tramiteinicio = Tramite::where('clienteitaid', $cliente->id)
                                        ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
                                        ->where('tramite', 'INVALIDEZ')
                                        ->exists();
            $tramitecontinuidad = Tramite::where('clienteitaid', $cliente->id)
                                        ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
                                        ->where('tramite', 'INVALIDEZ')
                                        ->exists();
            
            $nombreclienteita = $cliente->nombrecompleto;
            $procedimientotramites = Tramite::where('clienteitanombre', $nombreclienteita)
                                    ->where('tramite', 'INVALIDEZ')
                                    ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
                                    ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
                                    ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
                                    ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
                                    ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
                                    ->simplePaginate(10000);
            
            $cartasreclamos = Tramite::where('clienteitanombre', $nombreclienteita)
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

            return view('admin.tramites.procinvalidez', compact('modelocartasreclamos','tramiteinicio','tramitecontinuidad','inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto', 'personal'));
        }
    public function guardariniciotramiteclienteita(Request $request, Cliente $cliente)
        {
            // Validar los datos recibidos, asegurando que el nivel de procedimiento esté presente
            $request->validate([
                'nivelprocedimiento' => 'required|in:INICIO DE TRÁMITE,CONTINUIDAD DE TRÁMITE',
                'clienteitaid' => 'required|exists:clientes,id',
                'usuarioid' => 'required|exists:users,id',
                // Agrega validaciones adicionales según lo necesario
            ]);
    
            // Crear un nuevo trámite con los datos recibidos
            $tramite = new Tramite();
            $tramite->clienteitaid = $request->clienteitaid;
            $tramite->usuarioid = $request->usuarioid;
            $tramite->usuarioregistro = $request->usuarioregistro;
            $tramite->clienteitanombre = $request->clienteitanombre;
            $tramite->apoderado = $request->apoderado;
            $tramite->tramite = $request->tramite;
            $tramite->nivelprocedimiento = $request->nivelprocedimiento; // INICIO DE TRAMITE o CONTINUIDAD DE TRAMITE
            $tramite->save();  // Guardar el trámite en la base de datos
    
             // Mensaje de éxito
            $mensaje = "REGISTRO DE {$request->nivelprocedimiento} DE INVALIDEZ EXITOSO";
            return redirect()->route('admin.tramites.procinvalidez', $cliente)->with('success', $mensaje);

            // Redirigir a una página o retornar un mensaje de éxito
            /* return redirect()->route('admin.tramites.procinvalidez', $cliente)->with('success', 'El trámite ha sido guardado correctamente.'); */
        }
    public function actualizarEstado($id, $clienteId)
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
        }
    public function subirArchivo(Request $request, $id, $clienteId)
        {
            $request->validate([
                'documento' => 'required|file|mimes:jpg|max:2048',
            ]);

            $tramite = Tramite::find($id);
            $cliente = Cliente::find($clienteId);
            if (!$tramite || !$cliente) {
                return redirect()->back()->with('error', 'Trámite o cliente no encontrado.');
            }

            $archivo = $request->file('documento');
            $archivo_name = null;

            if ($archivo) {
                $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }
                $archivo_name = time() . '_' . $archivo->getClientOriginalName();
                $archivo->move($carpetaCliente, $archivo_name);
            }

            $tramite->capturacomunicacion = $archivo_name;
            $tramite->save();

            $previousUrl = url()->previous();
            if (Str::contains($previousUrl, 'procmasahereditaria')) {
                return redirect()->route('admin.tramites.procmasahereditaria', $cliente)->with('info', 'La captura se subió con éxito');
            } elseif (Str::contains($previousUrl, 'procinvalidez')) {
                return redirect()->route('admin.tramites.procinvalidez', $cliente)->with('info', 'La captura se subió con éxito');
            } elseif (Str::contains($previousUrl, 'procapelacion')) {
                return redirect()->route('admin.tramites.procapelacion', $cliente)->with('info', 'La captura se subió con éxito');
            }elseif (Str::contains($previousUrl, 'proccompensacionsenasir')) {
                return redirect()->route('admin.tramites.proccompensacionsenasir', $cliente)->with('info', 'La captura se subió con éxito');
            }elseif (Str::contains($previousUrl, 'procjubilacion')) {
                return redirect()->route('admin.tramites.procjubilacion', $cliente)->with('info', 'La captura se subió con éxito');
            }elseif (Str::contains($previousUrl, 'procpensionpormuerte')) {
                return redirect()->route('admin.tramites.procpensionpormuerte', $cliente)->with('info', 'La captura se subió con éxito');
            }elseif (Str::contains($previousUrl, 'procretiroaportesparcial')) {
                return redirect()->route('admin.tramites.procretiroaportesparcial', $cliente)->with('info', 'La captura se subió con éxito');
            }elseif (Str::contains($previousUrl, 'procretiroaportestotal')) {
                return redirect()->route('admin.tramites.procretiroaportestotal', $cliente)->with('info', 'La captura se subió con éxito');
            }elseif (Str::contains($previousUrl, 'procsegundasolicitud')) {
                return redirect()->route('admin.tramites.procsegundasolicitud', $cliente)->with('info', 'La captura se subió con éxito');
            }else {
                return redirect()->route('admin.tramites.index')->with('info', 'La captura se subió con éxito');
            }
        }
    public function procjubilacion(Request $request, Cliente $cliente)
        {
            $nombrecompleto = $cliente->nombrecompleto;
            $id = $cliente->id;
            $personal = Personal::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
            
            $nombreclienteita = $cliente->nombrecompleto;
            $procedimientotramites = Tramite::where('clienteitanombre', $nombreclienteita)
                                    ->where('tramite', 'JUBILACIÓN')
                                    ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
                                    ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
                                    ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
                                    ->simplePaginate(10000);

            return view('admin.tramites.procjubilacion', compact('procedimientotramites','id','cliente','nombrecompleto', 'personal'));
        }
    public function procpensionpormuerte(Request $request, Cliente $cliente)
        {
            $nombrecompleto = $cliente->nombrecompleto;
            $id = $cliente->id;
            $personal = Personal::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
            
            $nombreclienteita = $cliente->nombrecompleto;
            $procedimientotramites = Tramite::where('clienteitanombre', $nombreclienteita)
                                    ->where('tramite', 'PENSIÓN POR MUERTE')
                                    ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
                                    ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
                                    ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
                                    ->simplePaginate(10000);

            return view('admin.tramites.procpensionpormuerte', compact('procedimientotramites','id','cliente','nombrecompleto', 'personal'));
        }
    public function procretiroaportesparcial(Request $request, Cliente $cliente)
        {
            $nombrecompleto = $cliente->nombrecompleto;
            $id = $cliente->id;
            $personal = Personal::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
            
            $nombreclienteita = $cliente->nombrecompleto;
            $procedimientotramites = Tramite::where('clienteitanombre', $nombreclienteita)
                                    ->where('tramite', 'RETIRO DE APORTES PARCIAL')
                                    ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
                                    ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
                                    ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
                                    ->simplePaginate(10000);

            return view('admin.tramites.procretiroaportesparcial', compact('procedimientotramites','id','cliente','nombrecompleto', 'personal'));
        }
    public function procretiroaportestotal(Request $request, Cliente $cliente)
        {
            $nombrecompleto = $cliente->nombrecompleto;
            $id = $cliente->id;
            $personal = Personal::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
            
            $nombreclienteita = $cliente->nombrecompleto;
            $procedimientotramites = Tramite::where('clienteitanombre', $nombreclienteita)
                                    ->where('tramite', 'RETIRO DE APORTES TOTAL')
                                    ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
                                    ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
                                    ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
                                    ->simplePaginate(10000);

            return view('admin.tramites.procretiroaportestotal', compact('procedimientotramites','id','cliente','nombrecompleto', 'personal'));
        }
    public function procsegundasolicitud(Request $request, Cliente $cliente)
        {
            $nombrecompleto = $cliente->nombrecompleto;
            $id = $cliente->id;
            $personal = Personal::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
            
            $nombreclienteita = $cliente->nombrecompleto;
            $procedimientotramites = Tramite::where('clienteitanombre', $nombreclienteita)
                                    ->where('tramite', 'INVALIDEZ')
                                    ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
                                    ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
                                    ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
                                    ->simplePaginate(10000);

            return view('admin.tramites.procsegundasolicitud', compact('procedimientotramites','id','cliente','nombrecompleto', 'personal'));
        }
    public function generarcartareclamo(Request $request, Cliente $cliente, Tramite $tramite)
        {
            $clienteid = $cliente->id;
            $tipoPdf = $request->input('tipo_pdf');
            $fechaactual = Carbon::parse($request->input('fechaactual'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
            $apoderadoId = $request->input('apoderado');
            $personal = Personal::findOrFail($apoderadoId);
            $tipocartareclamo = $request->input('tipocartareclamo');
            $notaseguimiento = $request->input('notaseguimiento');
            $folio = $request->input('folio');
            

            // Obtener el primer registro de Requisitosubcliente para el cliente especificado
            $numeropodercliente = Requisitosubcliente::where('clienteitaid', $clienteid)->first();
            $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

            // Buscar el primer registro de Tramite que cumpla con las condiciones
            $fechaingresotramite = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'INGRESO DE TRAMITE')
                ->where('subprocedimiento', 'RECEPCIÓN DE TRAMITE')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechaingresotramite = $fechaingresotramite ? Carbon::parse($fechaingresotramite->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

            // Buscar el primer registro de Firma EAP que cumpla con las condiciones
            $fechafirmaeap = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'FIRMA EAP')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechafirmaeap = $fechafirmaeap ? Carbon::parse($fechafirmaeap->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;


            $fechafirmaeap30 = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'FIRMA EAP')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechaeap30 = $fechafirmaeap ? Carbon::parse($fechafirmaeap30->fechasubida)->addDays(30)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
            

            $fechaprimeracartasit = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'CARTAS / RECLAMOS')
                ->where('subprocedimiento', 'PRIMERA CARTA SIT')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechaprimeracartasit = $fechaprimeracartasit ? Carbon::parse($fechaprimeracartasit->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

            $fechasegundacartasit = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'CARTAS / RECLAMOS')
                ->where('subprocedimiento', 'SEGUNDA CARTA SIT')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechasegundacartasit = $fechasegundacartasit ? Carbon::parse($fechasegundacartasit->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

            $fechaterceracartasit = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'CARTAS / RECLAMOS')
                ->where('subprocedimiento', 'TERCERA CARTA SIT')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechaterceracartasit = $fechaterceracartasit ? Carbon::parse($fechaterceracartasit->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

            $fechaprimeracartareclamo = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'CARTAS / RECLAMOS')
                ->where('subprocedimiento', 'PRIMERA CARTA RECLAMO')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechaprimeracartareclamo = $fechaprimeracartareclamo ? Carbon::parse($fechaprimeracartareclamo->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

            $fechasegundacartareclamo = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'CARTAS / RECLAMOS')
                ->where('subprocedimiento', 'SEGUNDA CARTA RECLAMO')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechasegundacartareclamo = $fechasegundacartareclamo ? Carbon::parse($fechasegundacartareclamo->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

            $fechaterceracartareclamo = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'CARTAS / RECLAMOS')
                ->where('subprocedimiento', 'TERCERA CARTA RECLAMO')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechaterceracartareclamo = $fechaterceracartareclamo ? Carbon::parse($fechaterceracartareclamo->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

            
            // Cargar la vista del PDF según el tipo seleccionado
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
                case 'PRIMERA CARTA DE RECLAMO':
                    $pdfView = 'admin.tramites.cartasyreclamos.reclamoprimeracarta';
                    break;
                case 'SEGUNDA CARTA DE RECLAMO':
                    $pdfView = 'admin.tramites.cartasyreclamos.reclamosegundacarta';
                    break;
                case 'TERCERA CARTA DE RECLAMO':
                    $pdfView = 'admin.tramites.cartasyreclamos.reclamoterceracarta';
                    break;
                case 'CARTA DE RECLAMO APS':
                    $pdfView = 'admin.tramites.cartasyreclamos.reclamoaps';
                    break;
                default:
                    return response()->json(['error' => 'Tipo de PDF no válido'], 400);
            }

            $pdf = PDF::loadView($pdfView, compact('cliente', 'fechaactual', 'personal', 'fechaingresotramite', 'fechafirmaeap', 'tipocartareclamo', 'numeropoder', 'fechaeap30', 
            'fechaprimeracartasit', 'fechasegundacartasit', 'fechaterceracartasit', 'fechaprimeracartareclamo', 'fechasegundacartareclamo', 'fechaterceracartareclamo', 'folio'/* , 'detalle1', 'cantidad1', 'especialista2', 'detalle2', 'cantidad2' */));

            // Generar un nombre único para el PDF basado en el tipo y la fecha
            $timestamp = now()->format('Ymd_His'); // Genera un timestamp para asegurar unicidad
            $pdfName = "{$tipoPdf}_{$cliente->nombrecompleto}_{$timestamp}.pdf";

            // Guardar el PDF en la carpeta del cliente
            $carpetaCliente = public_path("/tramitesclientesita/{$clienteid}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $pdfPath = "{$carpetaCliente}/{$pdfName}";
            file_put_contents($pdfPath, $pdf->output());

            // Registrar el trámite en la base de datos
            Tramite::create([
                'usuarioid' => $request->usuarioid,
                'usuarioregistro' => $request->usuarioregistro,
                'fechasubida' => $request->fechasubida,
                'tramite' => $request->tramite,
                'apoderado' => $request->usuarioregistro,
                'nivelprocedimiento' => 'CARTAS / RECLAMOS',
                'subprocedimiento' => $tipoPdf . ' - ' . $notaseguimiento,
                'clienteitaid' => $clienteid,
                'clienteitanombre' => $cliente->nombrecompleto,
                'document' => $pdfName
            ]);

            // Descargar el PDF generado
            return response()->download($pdfPath);
        }

        public function generaradjuntoyrespuesta(Request $request, Cliente $cliente, Tramite $tramite)
        {
            $clienteid = $cliente->id;
            $tipoPdf = $request->input('tipo_pdf');
            $fechaactual = Carbon::parse($request->input('fechaactual'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
            $apoderadoId = $request->input('apoderado');
            $personal = Personal::findOrFail($apoderadoId);
            $tipocartareclamo = $request->input('tipocartareclamo');
            $folio = $request->input('folio');

            $notatecnicomedico = $request->input('notatecnicomedico');
            $fechanotatecnicomedico = Carbon::parse($request->input('fechanotatecnicomedico'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
            
            $especialistas = [];
            for ($i = 1; $i <= 10; $i++) {
                $especialista = $request->input("especialista$i");
                $detalle = $request->input("detalle$i");
                $cantidad = $request->input("cantidad$i");

                // Verificar si los valores son no vacíos y agregar al array
                if (!empty($especialista) && !empty($detalle) && !empty($cantidad)) {
                    $especialistas[] = [
                        'especialista' => $especialista,
                        'detalle' => $detalle,
                        'cantidad' => $cantidad,
                    ];
                }
            }

            // Obtener el primer registro de Requisitosubcliente para el cliente especificado
            $numeropodercliente = Requisitosubcliente::where('clienteitaid', $clienteid)->first();
            $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

            // Buscar el primer registro de Tramite que cumpla con las condiciones
            $fechaingresotramite = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'INGRESO DE TRAMITE')
                ->where('subprocedimiento', 'RECEPCIÓN DE TRAMITE')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechaingresotramite = $fechaingresotramite ? Carbon::parse($fechaingresotramite->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

            // Buscar el primer registro de Firma EAP que cumpla con las condiciones
            $fechafirmaeap = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'FIRMA EAP')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechafirmaeap = $fechafirmaeap ? Carbon::parse($fechafirmaeap->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;


            $fechafirmaeap30 = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'FIRMA EAP')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechaeap30 = $fechafirmaeap ? Carbon::parse($fechafirmaeap30->fechasubida)->addDays(30)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
            

            $adyretecnicomedico = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
                ->where('subprocedimiento', 'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $adyretecnicomedico = $adyretecnicomedico ? Carbon::parse($adyretecnicomedico->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
            
            $adyrecomplementario = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
                ->where('subprocedimiento', 'ADJUNTO Y RESPUESTA COMPLEMENTARIO')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $adyrecomplementario = $adyrecomplementario ? Carbon::parse($adyrecomplementario->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
            
            $adyreactatmc = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
                ->where('subprocedimiento', 'ADJUNTO Y RESPUESTA AL ACTA TMC')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $adyreactatmc = $adyreactatmc ? Carbon::parse($adyreactatmc->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
            
            $adinformeempleador = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
                ->where('subprocedimiento', 'ADJUNTO INFORME DEL EMPLEADOR')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $adinformeempleador = $adinformeempleador ? Carbon::parse($adinformeempleador->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
            
            $addocumentacionmedica = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
                ->where('subprocedimiento', 'ADJUNTO DOCUMENTACIÓN MÉDICA')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $addocumentacionmedica = $addocumentacionmedica ? Carbon::parse($addocumentacionmedica->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

            // Cargar la vista del PDF según el tipo seleccionado
            $pdfView = '';
            switch ($tipoPdf) {
                case 'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO':
                    $pdfView = 'admin.tramites.adjuntosyrespuestas.adyretecnicomedico';
                    break;
                case 'ADJUNTO Y RESPUESTA COMPLEMENTARIO':
                    $pdfView = 'admin.tramites.adjuntosyrespuestas.adyrecomplementario';
                    break;
                case 'ADJUNTO Y RESPUESTA ACTA TMC':
                    $pdfView = 'admin.tramites.adjuntosyrespuestas.adyreactatmc';
                    break;
                case 'ADJUNTO INFORME DEL EMPLEADOR':
                    $pdfView = 'admin.tramites.adjuntosyrespuestas.adinformeempleador';
                    break;
                case 'ADJUNTO DOCUMENTACIÓN MÉDICA':
                    $pdfView = 'admin.tramites.adjuntosyrespuestas.addocumentacionmedica';
                    break;
                default:
                    return response()->json(['error' => 'Tipo de PDF no válido'], 400);
            }

            $pdf = PDF::loadView($pdfView, compact('cliente', 'fechaactual', 'personal', 'fechaingresotramite', 'fechafirmaeap', 'tipocartareclamo', 'numeropoder', 'fechaeap30', 
             'folio', 'adyretecnicomedico', 'adyrecomplementario', 'adyreactatmc', 'adinformeempleador', 'addocumentacionmedica', 'notatecnicomedico', 'fechanotatecnicomedico', 'especialistas'/* , 'detalle1', 'cantidad1', 'especialista2', 'detalle2', 'cantidad2' */));

            // Generar un nombre único para el PDF basado en el tipo y la fecha
            $timestamp = now()->format('Ymd_His'); // Genera un timestamp para asegurar unicidad
            $pdfName = "{$tipoPdf}_{$cliente->nombrecompleto}_{$timestamp}.pdf";

            // Guardar el PDF en la carpeta del cliente
            $carpetaCliente = public_path("/tramitesclientesita/{$clienteid}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $pdfPath = "{$carpetaCliente}/{$pdfName}";
            file_put_contents($pdfPath, $pdf->output());

            // Registrar el trámite en la base de datos
            Tramite::create([
                'usuarioid' => $request->usuarioid,
                'usuarioregistro' => $request->usuarioregistro,
                'fechasubida' => $request->fechasubida,
                'tramite' => $request->tramite,
                'apoderado' => $request->usuarioregistro,
                'nivelprocedimiento' => 'ADJUNTOS Y RESPUESTAS',
                'subprocedimiento' => $tipoPdf,
                'clienteitaid' => $clienteid,
                'clienteitanombre' => $cliente->nombrecompleto,
                'document' => $pdfName // Solo el nombre del archivo
            ]);

            // Descargar el PDF generado
            return response()->download($pdfPath);
        }
        public function generarsolicitud(Request $request, Cliente $cliente, Tramite $tramite)
        {
            $clienteid = $cliente->id;
            $tipoPdf = $request->input('tipo_pdf');
            $fechaactual = Carbon::parse($request->input('fechaactual'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
            $apoderadoId = $request->input('apoderado');
            $personal = Personal::findOrFail($apoderadoId);
            $tipocartareclamo = $request->input('tipocartareclamo');
            $folio = $request->input('folio');
            $cambioactualizacion = $request->input('cambioactualizacion');
            $matricula = $request->input('matricula');
            $fechainformeestudio = Carbon::parse($request->input('fechainformeestudio'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');

            $notatecnicomedico = $request->input('notatecnicomedico');
            $fechanotatecnicomedico = Carbon::parse($request->input('fechanotatecnicomedico'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
            
            $adjuntos = [];
            for ($i = 1; $i <= 10; $i++) {
                $requerimiento = $request->input("requerimiento$i");
                $tipo = $request->input("tipo$i");
                /* $cantidad = $request->input("cantidad$i"); */

                // Verificar si los valores son no vacíos y agregar al array
                if (!empty($requerimiento) && !empty($tipo)/*  && !empty($cantidad) */) {
                    $adjuntos[] = [
                        'requerimiento' => $requerimiento,
                        'tipo' => $tipo,
                        /* 'cantidad' => $cantidad, */
                    ];
                }
            }
            $especialistas = [];
            for ($i = 1; $i <= 10; $i++) {
                $especialista = $request->input("especialista$i");
                $detalle = $request->input("detalle$i");
                $cantidad = $request->input("cantidad$i");

                // Verificar si los valores son no vacíos y agregar al array
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
                /* $cantidad = $request->input("cantidad$i"); */

                // Verificar si los valores son no vacíos y agregar al array
                if (!empty($requerimiento2) && !empty($tipo2)/*  && !empty($cantidad) */) {
                    $adjuntos2[] = [
                        'requerimiento2' => $requerimiento2,
                        'tipo2' => $tipo2,
                        /* 'cantidad' => $cantidad, */
                    ];
                }
            }
            // Obtener el primer registro de Requisitosubcliente para el cliente especificado
            $numeropodercliente = Requisitosubcliente::where('clienteitaid', $clienteid)->first();
            $numeropoder = $numeropodercliente ? $numeropodercliente->numeropoder : null;

            // Buscar el primer registro de Tramite que cumpla con las condiciones
            $fechaingresotramite = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'INGRESO DE TRAMITE')
                ->where('subprocedimiento', 'RECEPCIÓN DE TRAMITE')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechaingresotramite = $fechaingresotramite ? Carbon::parse($fechaingresotramite->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;

            // Buscar el primer registro de Firma EAP que cumpla con las condiciones
            $fechafirmaeap = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'FIRMA EAP')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechafirmaeap = $fechafirmaeap ? Carbon::parse($fechafirmaeap->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;


            $fechafirmaeap30 = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'FIRMA EAP')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $fechaeap30 = $fechafirmaeap ? Carbon::parse($fechafirmaeap30->fechasubida)->addDays(30)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null;
            

            /* $soactualizaciondatos = Tramite::where('clienteitaid', $clienteid)
                ->where('nivelprocedimiento', 'ADJUNTOS Y RESPUESTAS')
                ->where('subprocedimiento', 'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO')
                ->orderBy('fechasubida', 'desc')
                ->first();
            $soactualizaciondatos = $soactualizaciondatos ? Carbon::parse($soactualizaciondatos->fechasubida)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') : null; */
            

            $nivelProcedimiento = '';
            $subProcedimiento = '';

            // Cargar la vista del PDF según el tipo seleccionado
            $pdfView = '';
            switch ($tipoPdf) {
                case 'ACTUALIZACIÓN DE DATOS':
                    $pdfView = 'admin.tramites.solicitudes.actualizaciondatos';
                    $nivelProcedimiento = 'ACTUALIZACIÓN DE DATOS';
                    $subProcedimiento = 'ACTUALIZACIÓN DE DATOS';
                    break;
                case 'SITM COMPRA DE SERVICIOS':
                    $pdfView = 'admin.tramites.solicitudes.compraservicios';
                    $nivelProcedimiento = 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO';
                    $subProcedimiento = 'ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS';
                    break;
                case 'EVALUACIÓN POR MEDICINA DEL TRABAJO':
                    $pdfView = 'admin.tramites.solicitudes.evaluacionmedicinatrabajo';
                    $nivelProcedimiento = 'EVALUACIÓN POR MEDICINA DEL TRABAJO';
                    $subProcedimiento = 'EVALUACIÓN POR MEDICINA DEL TRABAJO';
                    break;
                case 'SITM EVALUACIÓN POR MEDICINA DEL TRABAJO EGS':
                    $pdfView = 'admin.tramites.solicitudes.evaluacionmedicinatrabajoegs';
                    $nivelProcedimiento = 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO';
                    $subProcedimiento = 'ENTE GESTOR DE SALUD _ SOLICITUD DE EVALUACIÓN POR MEDICINA DEL TRABAJO EGS';
                    break;
                case 'SITM HISTORIA CLÍNICA':
                    $pdfView = 'admin.tramites.solicitudes.historiaclinica';
                    $nivelProcedimiento = 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO';
                    $subProcedimiento = 'ENTE GESTOR DE SALUD _ SOLICITUD DE HISTORIA CLÍNICA';
                    break;
                case 'INCLUSIÓN DE INFORMES MÉDICOS':
                    $pdfView = 'admin.tramites.solicitudes.inclusioninformesmedicos';
                    $nivelProcedimiento = 'INCLUSIÓN DE INFORMES MÉDICOS';
                    $subProcedimiento = 'INCLUSIÓN DE INFORMES MÉDICOS';
                    break;
                case 'SITM INFORME AL EMPLEADOR':
                    $pdfView = 'admin.tramites.solicitudes.informeempleador';
                    $nivelProcedimiento = 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO';
                    $subProcedimiento = 'EMPLEADOR _ SOLICITUD DE INFORME AL EMPLEADOR';
                    break;


                case 'SIC COMPRA DE SERVICIOS':
                        $pdfView = 'admin.tramites.solicitudes.compraservicios';
                        $nivelProcedimiento = 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA';
                        $subProcedimiento = 'ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS';
                        break;
                case 'SIC EVALUACIÓN POR MEDICINA DEL TRABAJO EGS':
                        $pdfView = 'admin.tramites.solicitudes.evaluacionmedicinatrabajoegs';
                        $nivelProcedimiento = 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA';
                        $subProcedimiento = 'ENTE GESTOR DE SALUD _ SOLICITUD DE EVALUACIÓN POR MEDICINA DEL TRABAJO EGS';
                        break;
                case 'SIC HISTORIA CLÍNICA':
                        $pdfView = 'admin.tramites.solicitudes.historiaclinica';
                        $nivelProcedimiento = 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA';
                        $subProcedimiento = 'ENTE GESTOR DE SALUD _ SOLICITUD DE HISTORIA CLÍNICA';
                        break;
                case 'SIC INFORME AL EMPLEADOR':
                        $pdfView = 'admin.tramites.solicitudes.informeempleador';
                        $nivelProcedimiento = 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA';
                        $subProcedimiento = 'EMPLEADOR _ SOLICITUD DE INFORME AL EMPLEADOR';
                        break;
                default:
                    return response()->json(['error' => 'Tipo de PDF no válido'], 400);
            }

            $pdf = PDF::loadView($pdfView, compact('cliente', 'fechaactual', 'personal', 'fechaingresotramite', 'fechafirmaeap', 'tipocartareclamo', 'numeropoder', 'fechaeap30', 
             'folio', 'cambioactualizacion', 'notatecnicomedico', 'fechanotatecnicomedico', 'adjuntos', 'matricula', 'fechainformeestudio', 'especialistas', 'adjuntos2'));

            // Generar un nombre único para el PDF basado en el tipo y la fecha
            $timestamp = now()->format('Ymd_His'); // Genera un timestamp para asegurar unicidad
            $pdfName = "{$tipoPdf}_{$cliente->nombrecompleto}_{$timestamp}.pdf";

            // Guardar el PDF en la carpeta del cliente
            $carpetaCliente = public_path("/tramitesclientesita/{$clienteid}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $pdfPath = "{$carpetaCliente}/{$pdfName}";
            file_put_contents($pdfPath, $pdf->output());

            // Registrar el trámite en la base de datos
            Tramite::create([
                'usuarioid' => $request->usuarioid,
                'usuarioregistro' => $request->usuarioregistro,
                'fechasubida' => $request->fechasubida,
                'tramite' => $request->tramite,
                'apoderado' => $request->usuarioregistro,
                'nivelprocedimiento' => $nivelProcedimiento ,
                'subprocedimiento' => $subProcedimiento ,
                'clienteitaid' => $clienteid,
                'clienteitanombre' => $cliente->nombrecompleto,
                'document' => $pdfName
            ]);

            // Descargar el PDF generado
            return response()->download($pdfPath);
        }

/* $documento = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->first(); */
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
                }elseif (Str::contains($previousUrl, 'procpensionpormuerte')) {
                    return redirect()->route('admin.tramites.procpensionpormuerte', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }elseif (Str::contains($previousUrl, 'procretiroaportesparcial')) {
                    return redirect()->route('admin.tramites.procretiroaportesparcial', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }elseif (Str::contains($previousUrl, 'procretiroaportestotal')) {
                    return redirect()->route('admin.tramites.procretiroaportestotal', $cliente)->with('info', 'El estado del dictamen se actualizó con éxitoo');
                }elseif (Str::contains($previousUrl, 'procsegundasolicitud')) {
                    return redirect()->route('admin.tramites.procsegundasolicitud', $cliente)->with('info', 'El estado del dictamen se actualizó con éxito');
                }else {
                    return redirect()->route('admin.tramites.index')->with('info', 'El estado del dictamen se actualizó con éxito');
                }
            }
        }

        foreach ($request->file('archivo') as $key => $archivo) {
            $archivo_name = null;

            if ($archivo) {
                $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }
                $archivo_name = time() . '_' . $archivo->getClientOriginalName();
                $archivo->move($carpetaCliente, $archivo_name);
            }

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
            $riesgodictamen = $request->input('riesgodictamen', []);
            $tiporiesgodictamen = $request->input('tiporiesgodictamen', []);

            Tramite::create([
                'document' => $archivo_name,
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
                'riesgodictamen' => $riesgodictamen[$key] ?? null,
                'tiporiesgodictamen' => $tiporiesgodictamen[$key] ?? null,
            ]);

            $departamentocliente = $cliente->sucursal;

            $ultimoTramite = Tramitesubcliente::where('clienteitaid', $request->clienteitaid)
                ->orderBy('created_at', 'desc')
                ->first();

            $usuarioAsignado = $ultimoTramite ? $ultimoTramite->usuarioasignado : $request->usuarioasignado;

            if ($subprocedimiento[$key] === 'INICIO PROCESO DE APELACIÓN') {
                Tramitesubcliente::create([
                    'usuarioid' => $request->usuarioid,
                    'usuarioregistro' => $request->usuarioregistro,
                    'clienteitaid' => $request->clienteitaid,
                    'clienteitanombre' => $request->clienteitanombre,
                    'usuarioasignado' => $usuarioAsignado,
                    'tramite' => 'APELACIÓN',
                    'ciudad' => $departamentocliente,
                    'estado' => 'PENDIENTE',
                    'observaciones' => '',
                ]);
            }

            if ($nivelprocedimiento[$key] === 'CONTRATO' && $subprocedimiento[$key] === 'FIRMA DE CONTRATO') {
                Tramitesubcliente::where([
                    ['clienteitaid', $request->clienteitaid],
                    ['tramite', 'MASA HEREDITARIA'],
                    ['estado', 'PENDIENTE']
                ])->update(['estado' => 'FINALIZADO']);
            }

            if ($nivelprocedimiento[$key] === 'CONTRATO' && $subprocedimiento[$key] === 'NOTA DE RECHAZO DE TRÁMITEs') {
                Tramitesubcliente::where([
                    ['clienteitaid', $request->clienteitaid],
                    ['tramite', 'MASA HEREDITARIA'],
                    ['estado', 'PENDIENTE']
                ])->update(['estado' => 'FINALIZADO']);
            }

            if ($nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'INICIO PROCESO DE APELACIÓN') {
                Tramitesubcliente::where([
                    ['clienteitaid', $request->clienteitaid],
                    ['tramite', 'INVALIDEZ'],
                    ['estado', 'PENDIENTE']
                ])->update(['estado' => 'FINALIZADO']);
            }

        }

        $previousUrl = url()->previous();
        if (Str::contains($previousUrl, 'procmasahereditaria')) {
            return redirect()->route('admin.tramites.procmasahereditaria', $cliente)->with('info', 'Los documentos se subieron con éxito');
        } elseif (Str::contains($previousUrl, 'procinvalidez')) {
            return redirect()->route('admin.tramites.procinvalidez', $cliente)->with('info', 'Los documentos se subieron con éxito');
        } elseif (Str::contains($previousUrl, 'procapelacion')) {
            return redirect()->route('admin.tramites.procapelacion', $cliente)->with('info', 'Los documentos se subieron con éxito');
        }elseif (Str::contains($previousUrl, 'proccompensacionsenasir')) {
            return redirect()->route('admin.tramites.proccompensacionsenasir', $cliente)->with('info', 'Los documentos se subieron con éxito');
        }elseif (Str::contains($previousUrl, 'procjubilacion')) {
            return redirect()->route('admin.tramites.procjubilacion', $cliente)->with('info', 'Los documentos se subieron con éxito');
        }elseif (Str::contains($previousUrl, 'procpensionpormuerte')) {
            return redirect()->route('admin.tramites.procpensionpormuerte', $cliente)->with('info', 'Los documentos se subieron con éxito');
        }elseif (Str::contains($previousUrl, 'procretiroaportesparcial')) {
            return redirect()->route('admin.tramites.procretiroaportesparcial', $cliente)->with('info', 'Los documentos se subieron con éxito');
        }elseif (Str::contains($previousUrl, 'procretiroaportestotal')) {
            return redirect()->route('admin.tramites.procretiroaportestotal', $cliente)->with('info', 'Los documentos se subieron con éxito');
        }elseif (Str::contains($previousUrl, 'procsegundasolicitud')) {
            return redirect()->route('admin.tramites.procsegundasolicitud', $cliente)->with('info', 'Los documentos se subieron con éxito');
        }else {
            return redirect()->route('admin.tramites.index')->with('info', 'Los documentos se subieron con éxito');
        }
    }
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
        } elseif (Str::contains($previousUrl, 'procpensionpormuerte')) {
            return redirect()->route('admin.tramites.procpensionpormuerte', $cliente)->with('info', 'La nota de seguimiento se registró con éxito');
        } elseif (Str::contains($previousUrl, 'procretiroaportesparcial')) {
            return redirect()->route('admin.tramites.procretiroaportesparcial', $cliente)->with('info', 'La nota de seguimiento se registró con éxito');
        } elseif (Str::contains($previousUrl, 'procretiroaportestotal')) {
            return redirect()->route('admin.tramites.procretiroaportestotal', $cliente)->with('info', 'La nota de seguimiento se registró con éxito');
        } elseif (Str::contains($previousUrl, 'procsegundasolicitud')) {
            return redirect()->route('admin.tramites.procsegundasolicitud', $cliente)->with('info', 'La nota de seguimiento se registró con éxito');
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
