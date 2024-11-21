<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
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
use App\Models\Empresa;
use App\Models\Pais;
use App\Models\Ciudad;
use App\Models\Cliente;
use App\Models\Aseguradora;
use App\Models\Afp;
use App\Models\Bateriasubcliente;
use App\Models\Estadoprogramacionsubcliente;
use App\Models\Programacionsubcliente;
use App\Models\Documentacionsubcliente;
use App\Http\Requests\UpdateProgramacionsubclienteRequest;
use App\Services\WhatsAppService;

class AdministrarProgramacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /* public function __construct() { 
        $this->middleware('can:admin.users.index')->only('index');
    } */

    public function index(Request $request, ClienteAuditoria $clienteauditoria, ClienteComun $clientecomun, Cliente $cliente, ClienteBanco $clientebanco)
    {
        $busqueda = $request->get('buscarpor');
        $fechaActual = $busqueda ?? now()->toDateString();

        $clientesComunesCount = DB::table('clientescomunes')->count();
        $clientesBancosCount = DB::table('clientebancos')->count();
        $clientesITACount = DB::table('clientes')->count();
        $clientesAuditoriasCount = DB::table('clienteauditorias')->count();

        // FECHAS PROXIMAS DE CLIENTES AUDITORIA
        $programacionclienteauditorias = Programacionsubcliente::whereNotNull('clienteauditorianombre')
            ->where('clienteauditorianombre', '!=', '')
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->orderBy('horadesde', 'asc')
            ->get();

        $nombreClienteAuditoria = $clienteauditoria->nombrecompleto;
        $accionesClienteAuditoria = BateriaSubCliente::where('clienteauditorianombre', $nombreClienteAuditoria)->pluck('accionnombre')->toArray();
        $idAuditoria = $clienteauditoria->nombrecompleto ? ClienteAuditoria::where('nombrecompleto', $clienteauditoria->nombrecompleto)->value('id') : null;
        $accionesPorAreaAuditoria = Programacionsubcliente::where('clienteauditorianombre', $nombreClienteAuditoria)
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->get(['accionnombre', 'proveedornombre', 'fechaasignada', 'horaasignada']);
        $estadoRegistradosAuditoria = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesClienteAuditoria)
            ->where('clienteauditorianombre', $nombreClienteAuditoria)
            ->pluck('accionnombre')->toArray();
        $accionesDisponiblesAuditoria = $accionesPorAreaAuditoria;
        $accionesPorAreasAuditoria = Programacionsubcliente::where('clienteauditorianombre', $nombreClienteAuditoria)->pluck('accionnombre', 'accionnombre');

        // FECHAS PROXIMAS DE CLIENTES COMUNES
        $programacionclientecomunes = Programacionsubcliente::whereNotNull('clientecomunnombre')
            ->where('clientecomunnombre', '!=', '')
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->orderBy('horadesde', 'asc')
            ->get();

        $nombreClienteComun = $clientecomun->nombrecompleto;
        $accionesClienteComun = BateriaSubCliente::where('clientecomunnombre', $nombreClienteComun)->pluck('accionnombre')->toArray();
        $idComun = $clientecomun->nombrecompleto ? ClienteComun::where('nombrecompleto', $clientecomun->nombrecompleto)->value('id') : null;
        $accionesPorAreaComun = Programacionsubcliente::where('clientecomunnombre', $nombreClienteComun)
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->get(['accionnombre', 'proveedornombre', 'fechaasignada', 'horaasignada']);
        $estadoRegistradosComun = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesClienteComun)
            ->where('clientecomunnombre', $nombreClienteComun)
            ->pluck('accionnombre')->toArray();
        $accionesDisponiblesComun = $accionesPorAreaComun;
        $accionesPorAreasComun = Programacionsubcliente::where('clientecomunnombre', $nombreClienteComun)->pluck('accionnombre', 'accionnombre');

        // FECHAS PROXIMAS DE CLIENTES ITA
        $programacionclienteitas = Programacionsubcliente::whereNotNull('clienteitanombre')
            ->where('clienteitanombre', '!=', '')
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->orderBy('horadesde', 'asc')
            ->get();

        $nombreClienteIta = $cliente->nombrecompleto;
        $accionesClienteIta = BateriaSubCliente::where('clienteitanombre', $nombreClienteIta)->pluck('accionnombre')->toArray();
        $idIta = $cliente->nombrecompleto ? Cliente::where('nombrecompleto', $cliente->nombrecompleto)->value('id') : null;
        $accionesPorAreaIta = Programacionsubcliente::where('clienteitanombre', $nombreClienteIta)
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->get(['accionnombre', 'proveedornombre', 'fechaasignada', 'horaasignada']);
        $estadoRegistradosIta = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesClienteIta)
            ->where('clienteitanombre', $nombreClienteIta)
            ->pluck('accionnombre')->toArray();
        $accionesDisponiblesIta = $accionesPorAreaIta;
        $accionesPorAreasIta = Programacionsubcliente::where('clienteitanombre', $nombreClienteIta)->pluck('accionnombre', 'accionnombre');

        // FECHAS PROXIMAS DE CLIENTES BANCOS
        $programacionclientebancos = Programacionsubcliente::whereNotNull('clientenombre')
            ->where('clientenombre', '!=', '')
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->orderBy('horadesde', 'asc')
            ->get();

        $nombreClienteBanco = $clientebanco->nombrecompleto;
        $accionesClienteBanco = BateriaSubCliente::where('clientenombre', $nombreClienteBanco)->pluck('accionnombre')->toArray();
        $idBanco = $clientebanco->nombrecompleto ? ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('id') : null;
        $accionesPorAreaBanco = Programacionsubcliente::where('clientenombre', $nombreClienteBanco)
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->get(['accionnombre', 'proveedornombre', 'fechaasignada', 'horaasignada']);
        $estadoRegistradosBanco = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesClienteBanco)
            ->where('clientebanconombre', $nombreClienteBanco)
            ->pluck('accionnombre')->toArray();
        $accionesDisponiblesBanco = $accionesPorAreaBanco;
        $accionesPorAreasBanco = Programacionsubcliente::where('clientenombre', $nombreClienteBanco)->pluck('accionnombre', 'accionnombre');

        return view('admin.admprogramaciones.index', compact(
            'programacionclientebancos',
            'programacionclienteitas',
            'programacionclientecomunes',
            'programacionclienteauditorias',
            'clientesComunesCount',
            'clientesBancosCount',
            'clientesITACount',
            'clientesAuditoriasCount',
            'accionesPorAreasAuditoria',
            'accionesPorAreaAuditoria',
            'accionesDisponiblesAuditoria',
            'clienteauditoria',
            'idAuditoria',
            'accionesClienteAuditoria',
            'estadoRegistradosAuditoria',
            'accionesPorAreasComun',
            'accionesPorAreaComun',
            'accionesDisponiblesComun',
            'clientecomun',
            'idComun',
            'accionesClienteComun',
            'estadoRegistradosComun',
            'accionesPorAreasIta',
            'accionesPorAreaIta',
            'accionesDisponiblesIta',
            'cliente',
            'idIta',
            'accionesClienteIta',
            'estadoRegistradosIta',
            'accionesPorAreasBanco',
            'accionesPorAreaBanco',
            'accionesDisponiblesBanco',
            'clientebanco',
            'idBanco',
            'accionesClienteBanco',
            'estadoRegistradosBanco',
            'fechaActual'
        ));
    }


    public function documentacionpendiente(Request $request, Asociado $asociado, Cliente $cliente)
    {
        /* $proveedor = $request->get('buscarpor');

        $clientes = Programacionsubcliente::where('proveedornombre', 'LIKE', "%$proveedor%")
            ->whereIn('accionnombre', function ($query) use ($proveedor) {
                $query->select('accionnombre')
                    ->from('estadoprogramacionsubclientes')
                    ->where('proveedornombre', 'LIKE', "%$proveedor%");
            })
            ->whereNotNull('clienteitaid')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('documentacionsubclientes')
                    ->whereRaw('documentacionsubclientes.clienteitaid = programacionsubclientes.clienteitaid')
                    ->whereRaw('documentacionsubclientes.accion = programacionsubclientes.accionnombre')
                    ->whereRaw('documentacionsubclientes.fechabateria = programacionsubclientes.fechabateria');
            })
            ->orderBy('proveedornombre')
            ->simplePaginate(10000);
        $clientes2 = Programacionsubcliente::where('proveedornombre', 'LIKE', "%$proveedor%")
            ->whereIn('accionnombre', function ($query) use ($proveedor) {
                $query->select('accionnombre')
                    ->from('estadoprogramacionsubclientes')
                    ->where('proveedornombre', 'LIKE', "%$proveedor%");
            })
            ->whereNotNull('clientecomunid')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('documentacionsubclientes')
                    ->whereRaw('documentacionsubclientes.clientecomunid = programacionsubclientes.clientecomunid')
                    ->whereRaw('documentacionsubclientes.accion = programacionsubclientes.accionnombre')
                    ->whereRaw('documentacionsubclientes.fechabateria = programacionsubclientes.fechabateria');
            })
            ->orderBy('proveedornombre')
            ->simplePaginate(10000);

        return view('admin.admprogramaciones.documentacionpendiente', compact('cliente', 'asociado', 'clientes', 'clientes2')); */
        $buscar = $request->get('buscarpor');

        // Documentación Pendiente
        $clientes = Programacionsubcliente::where('proveedornombre', 'LIKE', "%$buscar%")
            ->whereNotNull('clienteitaid')
            ->simplePaginate(10000);

        $clientes2 = Programacionsubcliente::where('proveedornombre', 'LIKE', "%$buscar%")
            ->whereNotNull('clientecomunid')
            ->simplePaginate(10000);

        $clientes3 = Programacionsubcliente::where('proveedornombre', 'LIKE', "%$buscar%")
            ->whereNotNull('clienteauditoriaid')
            ->simplePaginate(10000);

        // Documentación Activa
        $documentacion = Documentacionsubcliente::with('estadoprogramacionsubcliente')
            ->where('clienteitaid', 'LIKE', "%$buscar%")
            ->orWhere('clienteitanombre', 'LIKE', "%$buscar%")
            ->simplePaginate(10000);

        // Documentación Activa
        $documentacionauditoria = Documentacionsubcliente::with('estadoprogramacionsubcliente')
            ->where('clienteauditoriaid', 'LIKE', "%$buscar%")
            ->orWhere('clienteauditorianombre', 'LIKE', "%$buscar%")
            ->simplePaginate(10000);

        // Documentación Activa
        $documentacioncomun = Documentacionsubcliente::with('estadoprogramacionsubcliente')
            ->where('clientecomunid', 'LIKE', "%$buscar%")
            ->orWhere('clientecomunnombre', 'LIKE', "%$buscar%")
            ->simplePaginate(10000);

        return view('admin.admprogramaciones.documentacionpendiente', compact(
            'asociado',
            'clientes',
            'clientes2','clientes3',
            'documentacion','documentacionauditoria','documentacioncomun'
        ));
    }
    public function documentacionactiva(Request $request, Asociado $asociado, Cliente $cliente)
    {
        $clienteitanombre = $request->get('buscarpor');
        
        $clientes = Documentacionsubcliente::with('estadoprogramacionsubcliente')
                    ->where('clienteitanombre', 'LIKE', "%$clienteitanombre%")
                    ->whereNotNull('clienteitaid')
                    ->orderBy('clienteitanombre')
                    ->simplePaginate(10000);
        $clientes2 = Documentacionsubcliente::with('estadoprogramacionsubclientecomun')
                    ->where('clientecomunnombre', 'LIKE', "%$clienteitanombre%")
                    ->whereNotNull('clientecomunid')
                    ->orderBy('clientecomunnombre')
                    ->simplePaginate(10000);

        return view('admin.admprogramaciones.documentacionactiva', compact('cliente', 'asociado', 'clientes', 'clientes2'));
    }

    /* public function clientescreadoshoy(Request $request)
    {
        $fechaActual = now()->toDateString();

        $clientes = Cliente::whereDate('created_at', $fechaActual)->get();
        $clientes2 = ClienteAuditoria::whereDate('created_at', $fechaActual)->get();
        $clientes3 = ClienteComun::whereDate('created_at', $fechaActual)->get();

        return view('admin.admprogramaciones.clientescreadoshoy', compact('clientes', 'clientes2', 'clientes3', 'fechaActual'));
    } */
    public function clientescreadoshoy(Request $request)
    {
        $fechaActual = $request->input('buscarpor', now()->toDateString());

        // Clientes creados hoy
        $clientes = Cliente::whereDate('created_at', $fechaActual)->simplePaginate(10000);
        $clientes2 = ClienteAuditoria::whereDate('created_at', $fechaActual)->simplePaginate(10000);
        $clientes3 = ClienteComun::whereDate('created_at', $fechaActual)->simplePaginate(10000);

        // Baterías creadas hoy
        $bateriashoyita = Bateriasubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->simplePaginate(10000);

        $bateriashoycomun = Bateriasubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clientecomunid')
            ->simplePaginate(10000);

        $bateriashoyauditoria = Bateriasubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->simplePaginate(10000);

        // Programaciones creadas hoy
        $programacioneshoyita = Programacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->simplePaginate(10000);

        $programacioneshoycomun = Programacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clientecomunid')
            ->simplePaginate(10000);

        $programacioneshoyauditoria = Programacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->simplePaginate(10000);

        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
            
        $todosClientes = $clientes->merge($clientes2)->merge($clientes3)->toArray();
        $contadorclientes = array_reduce($todosClientes, function ($count, $item) {
                return $count + 1;
            }, 0);
        
        $todosbaterias = $bateriashoyita->merge($bateriashoycomun)->merge($bateriashoyauditoria)->toArray();
        $contadorbaterias = array_reduce($todosbaterias, function ($count, $item) {
                return $count + 1;
            }, 0);

        $todosprogramaciones = $programacioneshoyita->merge($programacioneshoycomun)->merge($programacioneshoyauditoria)->toArray();
        $contadorprogramaciones = array_reduce($todosprogramaciones, function ($count, $item) {
                return $count + 1;
            }, 0);

        return view('admin.admprogramaciones.clientescreadoshoy', compact(
            'clientes',
            'clientes2',
            'clientes3',
            'bateriashoyita',
            'bateriashoycomun',
            'bateriashoyauditoria',
            'programacioneshoyita',
            'programacioneshoycomun',
            'programacioneshoyauditoria',
            'fechaActual','contadorclientes','contadorbaterias','contadorprogramaciones'
        ));
    }


    public function buscarclientesporfecha(Request $request)
    {
        $busqueda = $request->get('buscarpor');
        $fechaActual = $busqueda ?: now()->toDateString();

        // Clientes
        $clientes = Cliente::whereDate('created_at', $fechaActual)->simplePaginate(10);
        $clientes2 = ClienteAuditoria::whereDate('created_at', $fechaActual)->simplePaginate(10);
        $clientes3 = ClienteComun::whereDate('created_at', $fechaActual)->simplePaginate(10);

        // Baterías
        $bateriashoyita = Bateriasubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->simplePaginate(100);

        $bateriashoycomun = Bateriasubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clientecomunid')
            ->simplePaginate(100);

        $bateriashoyauditoria = Bateriasubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->simplePaginate(100);

        // Programaciones
        $programacioneshoyita = Programacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->simplePaginate(100);

        $programacioneshoycomun = Programacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clientecomunid')
            ->simplePaginate(100);

        $programacioneshoyauditoria = Programacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->simplePaginate(100);

        $todosClientes = $clientes->merge($clientes2)->merge($clientes3)->toArray();
        $contadorclientes = array_reduce($todosClientes, function ($count, $item) {
                return $count + 1;
            }, 0);
        
        $todosbaterias = $bateriashoyita->merge($bateriashoycomun)->merge($bateriashoyauditoria)->toArray();
        $contadorbaterias = array_reduce($todosbaterias, function ($count, $item) {
                return $count + 1;
            }, 0);

        $todosprogramaciones = $programacioneshoyita->merge($programacioneshoycomun)->merge($programacioneshoyauditoria)->toArray();
        $contadorprogramaciones = array_reduce($todosprogramaciones, function ($count, $item) {
                return $count + 1;
            }, 0);

        return view('admin.admprogramaciones.clientescreadoshoy', compact(
            'clientes',
            'clientes2',
            'clientes3',
            'bateriashoyita',
            'bateriashoycomun',
            'bateriashoyauditoria',
            'programacioneshoyita',
            'programacioneshoycomun',
            'programacioneshoyauditoria',
            'fechaActual', 'contadorclientes','contadorbaterias','contadorprogramaciones'
        ));
    }

    public function pagosprogramaciones(Request $request)
    {
        $fechaActual = now()->toDateString();

        $pagosprogramacionesita = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where(function ($query) {
                $query->whereNull('pagoatencion')
                    ->orWhere('pagoatencion', '');
            })
            ->whereNotNull('clienteitaid')
            ->where(function ($query) {
                $query->whereNotNull('precio')
                    ->where('precio', '!=', 0);
            })
            ->get();


        $pagosprogramacionescomun = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where(function ($query) {
                $query->whereNull('pagoatencion')
                    ->orWhere('pagoatencion', '');
            })
            ->whereNotNull('clientecomunid')
            ->where(function ($query) {
                $query->whereNotNull('precio')
                    ->where('precio', '!=', 0);
            })
            ->get();

        $pagosprogramacionesauditoria = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where(function ($query) {
                $query->whereNull('pagoatencion')
                    ->orWhere('pagoatencion', '');
            })
            ->whereNotNull('clienteauditoriaid')
            ->where(function ($query) {
                $query->whereNotNull('precio')
                    ->where('precio', '!=', 0);
            })
            ->get();

        $pagadosprogramacionesita = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where('pagoatencion', 'PAGO PROCESADO')
            ->whereNotNull('clienteitaid')
            ->get();

        $pagadosprogramacionescomun = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where('pagoatencion', 'PAGO PROCESADO')
            ->whereNotNull('clientecomunid')
            ->get();

        $pagadosprogramacionesauditoria = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where('pagoatencion', 'PAGO PROCESADO')
            ->whereNotNull('clienteauditoriaid')
            ->get();


        $pagosexternosprogramacionesita = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where(function ($query) {
                $query->whereNull('pagoatencion')
                    ->orWhere('pagoatencion', '');
            })
            ->whereNotNull('clienteitaid')
            ->where(function ($query) {
                $query->whereNotNull('precio')
                    ->where('precio', '==', 0);
            })
            ->get();


        $pagosexternosprogramacionescomun = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where(function ($query) {
                $query->whereNull('pagoatencion')
                    ->orWhere('pagoatencion', '');
            })
            ->whereNotNull('clientecomunid')
            ->where(function ($query) {
                $query->whereNotNull('precio')
                    ->where('precio', '==', 0);
            })
            ->get();

        $pagosexternosprogramacionesauditoria = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where(function ($query) {
                $query->whereNull('pagoatencion')
                    ->orWhere('pagoatencion', '');
            })
            ->whereNotNull('clienteauditoriaid')
            ->where(function ($query) {
                $query->whereNotNull('precio')
                    ->where('precio', '==', 0);
            })
            ->get();
        return view('admin.admprogramaciones.pagosprogramaciones', compact('pagosexternosprogramacionesauditoria','pagosexternosprogramacionescomun','pagosexternosprogramacionesita','pagadosprogramacionesita','pagadosprogramacionescomun','pagadosprogramacionesauditoria','pagosprogramacionesita','pagosprogramacionescomun','pagosprogramacionesauditoria', 'fechaActual'));
    }
    public function confirmarPagos(Request $request)
    {
        $programacionesIds = $request->input('programaciones', []);

        if (!empty($programacionesIds)) {
            Programacionsubcliente::whereIn('id', $programacionesIds)
                ->update(['pagoatencion' => 'PAGO PROCESADO']);
        }

        return redirect()->back()->with('info', 'Pagos confirmados correctamente.');
    }

    public function buscarprogramacionesporfecha(Request $request)
{
    $fechaActual = $request->get('fecha') ?: now()->toDateString(); 

    $criterio = $request->get('criterio'); // ID, nombre o CI
    $fecha = $request->get('fecha'); // Fecha seleccionada

    // Función común para filtrar programaciones
    $filtrarProgramaciones = function ($query) use ($criterio, $fecha) {
        if ($criterio) {
            $query->where(function ($subQuery) use ($criterio) {
                $subQuery->where('clienteitaid', 'like', "%$criterio%")
                         ->orWhere('clienteitanombre', 'like', "%$criterio%")
                         ->orWhere('clienteauditoriaid', 'like', "%$criterio%")
                         ->orWhere('clienteauditorianombre', 'like', "%$criterio%")
                         ->orWhere('clientecomunid', 'like', "%$criterio%")
                         ->orWhere('clientecomunnombre', 'like', "%$criterio%");
            });
        }

        if ($fecha) {
            $query->whereDate('fechaasignada', $fecha);
        }

        $query->where(function ($subQuery) {
            $subQuery->whereNull('pagoatencion')
                     ->orWhere('pagoatencion', '');
        });
        
    };

    // Consultas
    $pagosprogramacionesita = Programacionsubcliente::where($filtrarProgramaciones)
        ->whereNotNull('clienteitaid')
        ->where(function ($query) {
            $query->whereNotNull('precio')
                ->where('precio', '!=', 0);
        })
        ->simplePaginate(1000);

    $pagosprogramacionescomun = Programacionsubcliente::where($filtrarProgramaciones)
        ->whereNotNull('clientecomunid')
        ->where(function ($query) {
            $query->whereNotNull('precio')
                ->where('precio', '!=', 0);
        })
        ->simplePaginate(1000);

    $pagosprogramacionesauditoria = Programacionsubcliente::where($filtrarProgramaciones)
        ->whereNotNull('clienteauditoriaid')
        ->where(function ($query) {
            $query->whereNotNull('precio')
                ->where('precio', '!=', 0);
        })
        ->simplePaginate(1000);

    // Para programaciones pagadas
    $filtrarPagados = function ($query) use ($criterio, $fecha) {
        if ($criterio) {
            $query->where(function ($subQuery) use ($criterio) {
                $subQuery->where('clienteitaid', 'like', "%$criterio%")
                         ->orWhere('clienteitanombre', 'like', "%$criterio%");
            });
        }

        if ($fecha) {
            $query->whereDate('fechaasignada', $fecha);
        }

        $query->where('pagoatencion', 'PAGO PROCESADO');
    };

    $pagadosprogramacionesita = Programacionsubcliente::where($filtrarPagados)
        ->whereNotNull('clienteitaid')
        ->simplePaginate(1000);

    $pagadosprogramacionescomun = Programacionsubcliente::where($filtrarPagados)
        ->whereNotNull('clientecomunid')
        ->simplePaginate(1000);

    $pagadosprogramacionesauditoria = Programacionsubcliente::where($filtrarPagados)
        ->whereNotNull('clienteauditoriaid')
        ->simplePaginate(1000);



        // Función común para filtrar programaciones
    $filtrarProgramacionesexternos = function ($query) use ($criterio, $fecha) {
        if ($criterio) {
            $query->where(function ($subQuery) use ($criterio) {
                $subQuery->where('clienteitaid', 'like', "%$criterio%")
                         ->orWhere('clienteitanombre', 'like', "%$criterio%")
                         ->orWhere('clienteauditoriaid', 'like', "%$criterio%")
                         ->orWhere('clienteauditorianombre', 'like', "%$criterio%")
                         ->orWhere('clientecomunid', 'like', "%$criterio%")
                         ->orWhere('clientecomunnombre', 'like', "%$criterio%");
            });
        }

        if ($fecha) {
            $query->whereDate('fechaasignada', $fecha);
        }

        $query->where(function ($subQuery) {
            $subQuery->whereNull('pagoatencion')
                     ->orWhere('pagoatencion', '');
        });
        
    };

    // Consultas
    $pagosexternosprogramacionesita = Programacionsubcliente::where($filtrarProgramacionesexternos)
        ->whereNotNull('clienteitaid')
        ->where(function ($query) {
            $query->whereNotNull('precio')
                ->where('precio', 0);
        })
        ->simplePaginate(1000);

    $pagosexternosprogramacionescomun = Programacionsubcliente::where($filtrarProgramacionesexternos)
        ->whereNotNull('clientecomunid')
        ->where(function ($query) {
            $query->whereNotNull('precio')
                ->where('precio', 0);
        })
        ->simplePaginate(1000);

    $pagosexternosprogramacionesauditoria = Programacionsubcliente::where($filtrarProgramacionesexternos)
        ->whereNotNull('clienteauditoriaid')
        ->where(function ($query) {
            $query->whereNotNull('precio')
                ->where('precio',  0);
        })
        ->simplePaginate(1000);


    return view('admin.admprogramaciones.pagosprogramaciones', compact('pagosexternosprogramacionesauditoria','pagosexternosprogramacionescomun','pagosexternosprogramacionesita',
        'pagadosprogramacionesita',
        'pagadosprogramacionescomun',
        'pagadosprogramacionesauditoria',
        'pagosprogramacionesita',
        'pagosprogramacionescomun',
        'pagosprogramacionesauditoria','fechaActual'
    ));
}


    public function controlregistros(Request $request)
    {
        return view('admin.admprogramaciones.controlregistros');
    }
   

    public function bateriascreadoshoy(Request $request)
    {
        $fechaActual = now()->toDateString();

        $bateriashoyita = Bateriasubcliente::whereDate('created_at', $fechaActual)
        ->whereNotNull('clienteitaid')
        ->get();

        $bateriashoycomun = Bateriasubcliente::whereDate('created_at', $fechaActual)
        ->whereNotNull('clientecomunid')
        ->get();

        $bateriashoyauditoria = Bateriasubcliente::whereDate('created_at', $fechaActual)
        ->whereNotNull('clienteauditoriaid')
        ->get();

        return view('admin.admprogramaciones.bateriascreadashoy', compact('bateriashoyita', 'bateriashoycomun', 'bateriashoyauditoria', 'fechaActual'));
    }
    public function buscarbateriasporfecha(Request $request)
    {
        $busqueda = $request->get('buscarpor');
        if (!$busqueda) {
            $fechaActual = now()->toDateString();
        } else {
            $fechaActual = $busqueda;
        }
        $bateriashoyita = Bateriasubcliente::where(function ($query) use ($busqueda) {
                    $query->where('created_at', 'like', "%$busqueda%")
                    ->whereNotNull('clienteitaid');
                        })->simplePaginate(1000);
        $bateriashoycomun = Bateriasubcliente::where(function ($query) use ($busqueda) {
                    $query->where('created_at', 'like', "%$busqueda%")
                    ->whereNotNull('clientecomunid');
                        })->simplePaginate(1000);
        $bateriashoyauditoria = Bateriasubcliente::where(function ($query) use ($busqueda) {
                    $query->where('created_at', 'like', "%$busqueda%")
                    ->whereNotNull('clienteauditoriaid');
                        })->simplePaginate(1000);
        return view('admin.admprogramaciones.bateriascreadashoy', compact('bateriashoyita', 'bateriashoycomun', 'bateriashoyauditoria', 'fechaActual'));
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
