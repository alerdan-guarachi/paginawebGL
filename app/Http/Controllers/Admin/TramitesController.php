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
use App\Models\CriteriosDictamen;

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
            'tramite' => 'required', // Asegúrate de validar que sea una fecha válida
        ]);

        // Obtener los valores validados
        $clienteID = $validatedData['clienteitaid'];
        $apoderadoAsignado = $validatedData['apoderadoasignado'];
        $fechaBateria = $validatedData['fechabateria'];
        $tramiteCliente = $validatedData['tramite'];

        // Encontrar el registro específico para actualizar
        $tramitesubcliente = Tramitesubcliente::where('clienteitaid', $clienteID)
            ->where('tramite', $tramiteCliente)
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
            $apoderados = Proveedoresservicios::orderBy('razonsocial')
            ->where('cargo', 'EJECUTIVO DE PRESTACIONES')
            ->pluck('razonsocial', 'razonsocial');

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
        $personal = Proveedoresservicios::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();

        $nombreclienteita = $cliente->nombrecompleto;
        $procedimientotramites = Tramite::where('clienteitanombre', $nombreclienteita)
                                ->where('tramite', 'MASA HEREDITARIA')
                                ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
                                ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
                                ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
                                ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
                                ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
                                ->simplePaginate(10000);
        
        $inicioocontinuidad = Tramite::where('clienteitaid', $cliente->id)
                                    ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
                                    ->exists();
        $tramiteinicio = Tramite::where('clienteitaid', $cliente->id)
                                    ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
                                    ->where('tramite', 'MASA HEREDITARIA')
                                    ->exists();
        $tramitecontinuidad = Tramite::where('clienteitaid', $cliente->id)
                                    ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
                                    ->where('tramite', 'MASA HEREDITARIA')
                                    ->exists();

        return view('admin.tramites.procmasahereditaria', compact('inicioocontinuidad','tramiteinicio','tramitecontinuidad','procedimientotramites','id','cliente','nombrecompleto', 'personal'));
    }
    public function procapelacion(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
        
        return view('admin.tramites.procapelacion', compact('id','cliente','nombrecompleto', 'personal'));
    }
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

    // TRAMITE INVALIDEZ
    public function procinvalidez(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci')
            ->where('categoria','PROVEEDOR INTERNO')
        ->get();

        $modelocartasreclamos = Modelocartareclamo::where('estado', 'ACTIVO')
        ->pluck('tipocarta', 'id');

        $inicioocontinuidad = Tramite::where('clienteid', $cliente->id)
            ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
        ->exists();

        $tramiteinicio = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
            ->where('tramite', 'INVALIDEZ')
        ->exists();

        $tramitecontinuidad = Tramite::where('clienteid', $cliente->id)
            ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
            ->where('tramite', 'INVALIDEZ')
        ->exists();

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

        $apoderados = InstructivasPoder::where('clienteid', $cliente->id) 
            ->where('tramite', 'INVALIDEZ')
            ->first([
                'apoderado1', 'apoderado2', 'apoderado3', 'apoderado4', 'apoderado5',
                'apoderado6', 'apoderado7', 'apoderado8', 'apoderado9', 'apoderado10'
        ]);

        $apoderadosList = collect($apoderados)->filter()->values();

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

        return view('admin.tramites.procinvalidez', compact('listasolicitudes','matriculacliente','imagenCliente','aseguradoras','estlab','afpgestora','estadolaboral','registrosGuardadosProgramacioCS','registrosGuardadosProgramacionSIC','registrosGuardadosProgramacion','todasareas','registrosAgrupados','empresas','permisoContinuidad','numeropoder','apoderadosList','proveedoresmedicos','aseguradora','apoderadoAsignado','programaciones','puedeEditarArchivo','puedeEditarFecha','proveedores','idTramite','modelocartasreclamos','tramiteinicio','tramitecontinuidad','inicioocontinuidad','cartasreclamos','procedimientotramites','id','cliente','nombrecompleto', 'personal'));
    }
    public function actualizardatoscliente(UpdateClienteitaRequest $request, Cliente $cliente)
    {
        $clienteData = $request->validated();

        foreach ($clienteData as $campo => $nuevoValor) {
            $valorAnterior = $cliente->$campo;
            if ($valorAnterior != $nuevoValor) {
                ModificacionesDatos::create([
                    'tabla' => 'clientes',
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
    public function guardariniciotramiteclienteita(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nivelprocedimiento' => 'required|in:INICIO DE TRÁMITE,CONTINUIDAD DE TRÁMITE',
            'clienteid' => 'required',
            'usuarioid' => 'required',
            'idtramite' => 'required',
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
    
        $mensaje = "REGISTRO DE {$request->nivelprocedimiento} DE {$request->tramite} EXITOSO";
        return redirect(session()->previousUrl())->with('success', $mensaje);
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

        // MODIFICACION DE ARCHIVOS
        if ($request->input('accion') === 'reemplazarArchivo') {

            // ✅ 2. Validar campos del reemplazo
            $request->validate([
                'archivo_reemplazo' => 'required',
                'tramite_reemplazo_id' => 'required',
            ]);

            // ✅ 3. Reemplazar archivo
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
        /* $archivo2s = $request->file('archivo2'); */
        $tramites = $request->input('tramite', []);
        $niveles = $request->input('nivelprocedimiento', []);

        if (is_array($archivos) && count($archivos) > 0) {
            foreach ($archivos as $key => $archivo) {
                /* if (!$archivo) {
                    continue;
                }

                foreach ($request->file('archivo') as $key => $archivo) {
                    $archivo_name = null;
                    if ($archivo) {
                        $nombreTramite = isset($tramites[$key]) ? $tramites[$key] : 'SIN_TRAMITE';
                        $nivel = isset($niveles[$key]) ? $niveles[$key] : 'SIN_NIVEL';
                        $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$nombreTramite}/{$nivel}");
                        if (!file_exists($carpetaCliente)) {
                            mkdir($carpetaCliente, 0755, true);
                        }
                        $archivo_name = time() . '_' . $archivo->getClientOriginalName();
                        $archivo->move($carpetaCliente, $archivo_name);
                    } */

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
                        $viaja = $request->input('viaja', []);
                        $departamentoviaja = $request->input('departamentoviaja', []);
                        $fechagestoradictamen = $request->input('fechagestoradictamen', []);
                        $fechasinestro = $request->input('fechasinestro', []);
                        $fechacobrocontrato = $request->input('fechacobrocontrato', []);
                        $montocontrato = $request->input('montocontrato', []);
                        $motivorechazo = $request->input('motivorechazo', []);
                        $notaseguimiento = $request->input('notaseguimiento', []);
                        $riesgodictamen = $request->input('riesgodictamen', []);
                        $tiporiesgodictamen = $request->input('tiporiesgodictamen', []);
                    //

                    $conteo = Tramite::where('clienteid', $request->clienteid)
                        ->where('nivelprocedimiento', $nivelprocedimiento[$key] ?? null)
                        ->where('subprocedimiento', $subprocedimiento[$key] ?? null)
                        ->where('tramite', $tramite[$key] ?? null)
                        ->count();

                    $nro = $conteo + 1;

                    Tramite::create([
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

                    if ($nivelprocedimiento[$key] === 'CONTRATO' && $subprocedimiento[$key] === 'FIRMA DE CONTRATO') {
                        Tramitesubcliente::where([
                            ['clienteid', $request->clienteid],
                            ['tramite', 'MASA HEREDITARIA'],
                            ['estado', 'PENDIENTE']
                        ])->update(['estado' => 'FINALIZADO']);
                    }

                    if ($nivelprocedimiento[$key] === 'CONTRATO' && $subprocedimiento[$key] === 'NOTA DE RECHAZO DE TRÁMITEs') {
                        Tramitesubcliente::where([
                            ['clienteid', $request->clienteid],
                            ['tramite', 'MASA HEREDITARIA'],
                            ['estado', 'PENDIENTE']
                        ])->update(['estado' => 'FINALIZADO']);
                    }

                    if ($nivelprocedimiento[$key] === 'DICTAMEN' && $subprocedimiento[$key] === 'INICIO PROCESO DE APELACIÓN') {
                        Tramitesubcliente::where([
                            ['clienteid', $request->clienteid],
                            ['tramite', 'INVALIDEZ'],
                            ['estado', 'PENDIENTE']
                        ])->update(['estado' => 'FINALIZADO']);
                    }
                /* } */
            }
        }

        $razonsocialempleador = $request->input('razonsocialempleador', []);
        $periodos = $request->input('periodo', []);
        $observacion = $request->input('observacion', []);
        $usuarioAutenticadoid = Auth::user()->id;
        $usuarioAutenticadonombre = Auth::user()->name;
        $idtramitecliente = $request->input('idtramite');

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
                    'tramite' => 'INVALIDEZ',
                    'idtramite' => $idtramitecliente,
                    'tipo' => 'OBSERVACIONES FIRMA EAP',
                    'usuarioregistroid' => $usuarioAutenticadoid,
                    'usuarioregistronombre' => $usuarioAutenticadonombre,
                ]);
            }
        }

        $estudioespecialidad = $request->input('estudioespecialidad', $request->input('estudioespecialidad2', $request->input('estudioespecialidad3', [])));
        $fechaprogramacion = $request->input('fechaprogramacion', $request->input('fechaprogramacion2', $request->input('fechaprogramacion3', [])));
        $horaprogramacion = $request->input('horaprogramacion', $request->input('horaprogramacion2', $request->input('horaprogramacion3', [])));
        $subtramite_ids = $request->input('subtramite_id', $request->input('subtramite_id2', $request->input('subtramite_id3', [])));
        $nombremedicoprog = $request->input('nombremedicoprog', $request->input('nombremedicoprog2', $request->input('nombremedicoprog3', [])));
        $asistencias = $request->input('asistenciaprogramacion', $request->input('asistenciaprogramacion2', $request->input('asistenciaprogramacion3', [])));
        $opcionatencion = $request->input('opcionatencion', $request->input('opcionatencion2', $request->input('opcionatencion3', [])));
        $ordenes = $request->file('ordenprogramacion', $request->file('ordenprogramacion2', $request->file('ordenprogramacion3', [])));
        $nombreTramite = $request->input('tramitenombreprog');
        $fechareprogramacion = $request->input('fechareprogramacion', $request->input('fechareprogramacion2', $request->input('fechareprogramacion3', [])));
        $horareprogramacion = $request->input('horareprogramacion', $request->input('horareprogramacion2', $request->input('horareprogramacion3', [])));
        $motivoreprogramacion = $request->input('motivoreprogramacion', $request->input('motivoreprogramacion2', $request->input('motivoreprogramacion3', [])));

        if ($request->has('estudioespecialidad3')) {
            $tipo = 'PROGRAMACIONES COMPRA DE SERVICIOS';
        } elseif ($request->has('estudioespecialidad2')) {
            $tipo = 'PROGRAMACIONES SIC ENTE GESTOR DE SALUD';
        } else {
            $tipo = 'PROGRAMACIONES SITM ENTE GESTOR DE SALUD';
        }

        for ($i = 0; $i < count($estudioespecialidad); $i++) {
            $id = $subtramite_ids[$i] ?? null;
            $asistio = in_array($id, $asistencias) ? 1 : 0;
            $archivo_name = null;

            if (isset($ordenes[$i]) && $ordenes[$i]) {
                $archivo = $ordenes[$i];
                $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$nombreTramite}/ORDENES");

                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }

                $archivo_name = time() . '_' . $archivo->getClientOriginalName();
                $archivo->move($carpetaCliente, $archivo_name);
            }

            if ($id && $registro = SubTramite::find($id)) {
                $registro->update([
                    'fechaprogramacion' => $fechaprogramacion[$i] ?? $registro->fechaprogramacion,
                    'horaprogramacion' => $horaprogramacion[$i] ?? $registro->horaprogramacion,
                    'nombremedico' => $nombremedicoprog[$i] ?? $registro->nombremedico,
                    'asistenciaprogramacion' => $asistio,
                    'ordenprogramacion' => $archivo_name ?? $registro->ordenprogramacion,
                    'opcionatencion' => $opcionatencion[$i] ?? $registro->opcionatencion,
                    'fechareprogramacion' => array_key_exists($id, $fechareprogramacion) && $fechareprogramacion[$id] !== null
                        ? $fechareprogramacion[$id]
                        : $registro->fechareprogramacion,
                    'horareprogramacion' => array_key_exists($id, $horareprogramacion) && $horareprogramacion[$id] !== null
                        ? $horareprogramacion[$id]
                        : $registro->horareprogramacion,
                    'motivoreprogramacion' => array_key_exists($id, $motivoreprogramacion) && $motivoreprogramacion[$id] !== null
                        ? $motivoreprogramacion[$id]
                        : $registro->motivoreprogramacion,
                ]);

            } else {
                SubTramite::create([
                    'clienteid' => $cliente->id,
                    'clientenombre' => $cliente->nombrecompleto,
                    'tramite' => 'INVALIDEZ',
                    'idtramite' => $idtramitecliente,
                    'tipo' => $tipo,
                    'estudioespecialidad' => $estudioespecialidad[$i] ?? null,
                    'fechaprogramacion' => $fechaprogramacion[$i] ?? null,
                    'horaprogramacion' => $horaprogramacion[$i] ?? null,
                    'nombremedico' => $nombremedicoprog[$i] ?? null,
                    /* 'opcionatencion' => $opcionatencion[$i] ?? null, */
                    'opcionatencion' => $tipo === 'PROGRAMACIONES COMPRA DE SERVICIOS' ? 'COMPRA DE SERVICIOS' : ($opcionatencion[$i] ?? null),
                    'asistenciaprogramacion' => $asistio,
                    'ordenprogramacion' => $archivo_name,
                    'usuarioregistroid' => $usuarioAutenticadoid,
                    'usuarioregistronombre' => $usuarioAutenticadonombre,
                    'fechareprogramacion' => $fechareprogramacion[$i] ?? null,
                    'horareprogramacion' => $horareprogramacion[$i] ?? null,
                    'motivoreprogramacion' => $motivoreprogramacion[$i] ?? null,
                ]);
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
        }elseif (Str::contains($previousUrl, 'procpensionpormuerte')) {
            return redirect()->route('admin.tramites.procpensionpormuerte', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'procretiroaportesparcial')) {
            return redirect()->route('admin.tramites.procretiroaportesparcial', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'procretiroaportestotal')) {
            return redirect()->route('admin.tramites.procretiroaportestotal', $cliente)->with('info', 'El registro se guardó con éxito');
        }elseif (Str::contains($previousUrl, 'procsegundasolicitud')) {
            return redirect()->route('admin.tramites.procsegundasolicitud', $cliente)->with('info', 'El registro se guardó con éxito');
        }else {
            return redirect()->route('admin.tramites.index')->with('info', 'El registro se guardó con éxito');
        }
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
        $tramitenombre = $request->input('tramitenombre');
        $archivo_name = null;

        if ($archivo) {
            $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$tramitenombre}/COMUNICACIONES");
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
        $personal = Proveedoresservicios::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
        
        $nombreclienteita = $cliente->nombrecompleto;
        $procedimientotramites = Tramite::where('clienteitanombre', $nombreclienteita)
                                ->where('tramite', 'JUBILACIÓN')
                                ->where('nivelprocedimiento', '!=', 'SEGUIMIENTO')
                                ->where('nivelprocedimiento', '!=', 'ADJUNTOS Y RESPUESTAS')
                                ->where('nivelprocedimiento', '!=', 'CARTAS / RECLAMOS')
                                ->where('nivelprocedimiento', '!=', 'INICIO DE TRAMITE')
                                ->where('nivelprocedimiento', '!=', 'CONTINUIDAD DE TRAMITE')
                                ->simplePaginate(10000);

        $inicioocontinuidad = Tramite::where('clienteitaid', $cliente->id)
                                ->whereIn('nivelprocedimiento', ['INICIO DE TRAMITE', 'CONTINUIDAD DE TRAMITE'])
                                ->exists();
        $tramiteinicio = Tramite::where('clienteitaid', $cliente->id)
                                ->where('nivelprocedimiento', 'INICIO DE TRAMITE')
                                ->where('tramite', 'JUBILACIÓN')
                                ->exists();
        $tramitecontinuidad = Tramite::where('clienteitaid', $cliente->id)
                                ->where('nivelprocedimiento', 'CONTINUIDAD DE TRAMITE')
                                ->where('tramite', 'JUBILACIÓN')
                                ->exists();

        return view('admin.tramites.procjubilacion', compact('inicioocontinuidad','tramiteinicio','tramitecontinuidad','procedimientotramites','id','cliente','nombrecompleto', 'personal'));
    }
    public function procpensionpormuerte(Request $request, Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        $personal = Proveedoresservicios::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
        
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
        $personal = Proveedoresservicios::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
        
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
        $personal = Proveedoresservicios::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
        
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
        $personal = Proveedoresservicios::select('id', 'nombrecompleto', 'ci', 'ciexp')->get();
        
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
        $personal = Proveedoresservicios::findOrFail($apoderadoId);
        $tipocartareclamo = $request->input('tipocartareclamo');
        $notaseguimiento = $request->input('notaseguimiento');
        $folio = $request->input('folio');
        

        // Obtener el primer registro de Requisitosubcliente para el cliente especificado
        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $clienteid)->where('servicio', $tipoPdf)->first();
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
        $apoderado = $request->input('apoderado');
        $idtramite = $request->input('idtramite');
        $fechaactual = Carbon::parse($request->input('fechaactual'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $apoderadoId = $request->input('apoderado');
        $personal = Proveedoresservicios::where('razonsocial', $apoderadoId)->first();

        $tipocartareclamo = $request->input('tipocartareclamo');
        $folio = $request->input('folio');

        $notatecnicomedico = $request->input('notatecnicomedico');
        $fechanotatecnicomedico = Carbon::parse($request->input('fechanotatecnicomedico'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        
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

        // Obtener el primer registro de Requisitosubcliente para el cliente especificado
        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $clienteid)->where('servicio',$request->tramite)->first();
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
            case 'ADJUNTO Y RESPUESTA AL ACTA TMC':
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
            'idtramite' => $idtramite,
            'apoderado' => $apoderado,
            'nivelprocedimiento' => 'ADJUNTOS Y RESPUESTAS',
            'subprocedimiento' => $tipoPdf,
            'clienteitaid' => $clienteid,
            'clienteitanombre' => $cliente->nombrecompleto,
            'document' => $pdfName
        ]);

        // Descargar el PDF generado
        return response()->download($pdfPath);
    }
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
        $fechainformeestudio = Carbon::parse($request->input('fechainformeestudio'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $notatecnicomedico = $request->input('notatecnicomedico');
        $fechanotatecnicomedico = Carbon::parse($request->input('fechanotatecnicomedico'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        
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
        $numeropodercliente = Requisitosubcliente::where('clienteitaid', $clienteid)->first();
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
            case 'INFORME AL EMPLEADOR':
                $pdfView = 'admin.tramites.solicitudes.informeempleador';
                $subProcedimiento = 'INFORME AL EMPLEADOR';
                break;
            default:
                return response()->json(['error' => 'Tipo de PDF no válido'], 400);
        }

        $pdf = PDF::loadView($pdfView, compact('cliente', 'fechaactual', 'fechaingresotramite', 'fechafirmaeap', 'tipocartareclamo', 'numeropoder', 'fechaeap30', 
            'folio', 'cambioactualizacion', 'notatecnicomedico', 'fechanotatecnicomedico', 'adjuntos', 'matricula', 
            'fechainformeestudio', 'especialistas', 'adjuntos2','nombremedico','cargomedico','aseguradora','afpgestora','tramite',
        'nombretramite'));

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
            'tipo' => 'SOLICITUD',
            'nro' => $nro,
            'clienteid' => $clienteid,
            'clientenombre' => $cliente->nombrecompleto,
            'document' => $pdfName
        ]);

        return response()->download($pdfPath);
    }

    public function guardarrespuesta(Request $request, Cliente $cliente)
{
    $request->validate([
        'document2' => 'nullable|file|mimes:pdf',
        'observaciones' => 'nullable|string',
    ]);

    $tramite = Tramite::findOrFail($request->tramite_id);

    if ($request->hasFile('document2')) {
        $archivo = $request->file('document2');
        $archivoNombre = time() . '_' . $archivo->getClientOriginalName();

        $carpetaCliente = public_path("/tramitesclientesita/{$cliente->id}/{$request->nombretramite}/SOLICITUDES");
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }

        $archivo->move($carpetaCliente, $archivoNombre);
        $tramite->document2 = $archivoNombre;
    }

    $tramite->observaciones = $request->observaciones;
    $tramite->save();

    return back()->with('info', 'Archivo y observación guardados correctamente.');
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
