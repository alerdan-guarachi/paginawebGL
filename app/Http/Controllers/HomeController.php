<?php

namespace App\Http\Controllers;

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
use App\Http\Requests\StoreAsociadoRequest;
use App\Http\Requests\StoreBateriaproveedorRequest;
use App\Http\Requests\StoreProgramacionsubclienteRequest;
use App\Http\Requests\StoreEstadoprogramacionsubclienteRequest;
use App\Http\Requests\StoreDocumentacionsubclienteRequest;
use App\Http\Requests\StoreBateriasubclienteRequest;
use App\Http\Requests\StoreBateriaclientecomunRequest;
use App\Http\Requests\StoreClienteAuditoriaRequest;
use App\Http\Requests\StoreClienteComunRequest;
use App\Http\Requests\StoreClienteBancoRequest;
use App\Http\Requests\StoreClienteRequest;
use App\Services\WhatsAppService;
use App\Models\Mensaje;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(ClienteAuditoria $clienteauditoria, ClienteComun $clientecomun, Cliente $cliente, ClienteBanco $clientebanco)
    {
        $clientesComunesCount = DB::table('clientescomunes')->count();
        $clientesBancosCount = DB::table('clientebancos')->count();
        $clientesITACount = DB::table('clientes')->count();
        $clientesAuditoriasCount = DB::table('clienteauditorias')->count();


        $fechaActual = now()->toDateString();
        // FECHAS ACTUALES DE CLIENTES AUDITORIA
        $progauditoria = Programacionsubcliente::whereNotNull('clienteauditorianombre')
            ->where('clienteauditorianombre', '!=', '')
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->orderBy('horadesde', 'asc')
            ->get();

        // FECHAS ACTUALES DE CLIENTES COMUNES
        $progcomun = Programacionsubcliente::whereNotNull('clientecomunnombre')
            ->where('clientecomunnombre', '!=', '')
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->orderBy('horadesde', 'asc')
            ->get();

        // FECHAS ACTUALES DE CLIENTES ITA
        $progita = Programacionsubcliente::whereNotNull('clienteitanombre')
            ->where('clienteitanombre', '!=', '')
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->orderBy('horadesde', 'asc')
            ->get();


        //FECHAS PROXIMAS DE CLIENTES AUDITORIA 
        $programacionclienteauditorias = Programacionsubcliente::whereNotNull('clienteauditorianombre')
                                                    ->where('clienteauditorianombre', '!=', '')->whereDate('fechaasignada', '=', now()->addDay()->toDateString())
                                                    ->get();
            $nombreClienteAuditoria = $clienteauditoria->nombrecompleto;
            $accionesClienteAuditoria = BateriaSubCliente::where('clienteauditorianombre', $nombreClienteAuditoria)->pluck('accionnombre')->toArray();
            $idAuditoria = $clienteauditoria->nombrecompleto ? ClienteAuditoria::where('nombrecompleto', $clienteauditoria->nombrecompleto)->value('id') : null;
            $accionesPorAreaAuditoria = Programacionsubcliente::where('clienteauditorianombre', $nombreClienteAuditoria)
            ->whereDate('fechaasignada', '=', now()->addDay()->toDateString())
            ->get(['accionnombre', 'proveedornombre', 'fechaasignada', 'horaasignada']);    
            $estadoRegistradosAuditoria = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesClienteAuditoria)
                ->where('clienteauditorianombre', $nombreClienteAuditoria)
                ->pluck('accionnombre')->toArray();
            $accionesDisponiblesAuditoria = $accionesPorAreaAuditoria;
        $accionesPorAreasAuditoria = Programacionsubcliente::where('clienteauditorianombre', $nombreClienteAuditoria)->pluck('accionnombre', 'accionnombre');


        //FECHAS PROXIMAS DE CLIENTES COMUNES
        $programacionclientecomunes = Programacionsubcliente::whereNotNull('clientecomunnombre')
                                                    ->where('clientecomunnombre', '!=', '')->whereDate('fechaasignada', '=', now()->addDay()->toDateString())
                                                    ->get();
            $nombreClienteComun = $clientecomun->nombrecompleto;
            $accionesClienteComun = BateriaSubCliente::where('clientecomunnombre', $nombreClienteComun)->pluck('accionnombre')->toArray();
            $idComun = $clientecomun->nombrecompleto ? ClienteComun::where('nombrecompleto', $clientecomun->nombrecompleto)->value('id') : null;
            $accionesPorAreaComun = Programacionsubcliente::where('clientecomunnombre', $nombreClienteComun)
                ->whereDate('fechaasignada', '=', now()->addDay()->toDateString())
                ->get(['accionnombre', 'proveedornombre', 'fechaasignada', 'horaasignada']);
            $estadoRegistradosComun = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesClienteComun)
                ->where('clientecomunnombre', $nombreClienteComun)
                ->pluck('accionnombre')->toArray();
            $accionesDisponiblesComun = $accionesPorAreaComun;
        $accionesPorAreasComun = Programacionsubcliente::where('clientecomunnombre', $nombreClienteComun)->pluck('accionnombre', 'accionnombre');


        //FECHAS PROXIMAS DE CLIENTES ITA
        $programacionclienteitas = Programacionsubcliente::whereNotNull('clienteitanombre')
                                                    ->where('clienteitanombre', '!=', '')->whereDate('fechaasignada', '=', now()->addDay()->toDateString())
                                                    ->get();
            $nombreClienteIta = $cliente->nombrecompleto;
            $accionesClienteIta = BateriaSubCliente::where('clienteitanombre', $nombreClienteIta)->pluck('accionnombre')->toArray();
            $idIta = $cliente->nombrecompleto ? Cliente::where('nombrecompleto', $cliente->nombrecompleto)->value('id') : null;
            $accionesPorAreaIta = Programacionsubcliente::where('clienteitanombre', $nombreClienteIta)
                ->whereDate('fechaasignada', '=', now()->addDay()->toDateString())
                ->get(['accionnombre', 'proveedornombre', 'fechaasignada', 'horaasignada']);
            $estadoRegistradosIta = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesClienteIta)
                ->where('clienteitanombre', $nombreClienteIta)
                ->pluck('accionnombre')->toArray();
            $accionesDisponiblesIta = $accionesPorAreaIta;
        $accionesPorAreasIta = Programacionsubcliente::where('clienteitanombre', $nombreClienteIta)->pluck('accionnombre', 'accionnombre');


        //FECHAS PROXIMAS DE CLIENTES BANCOS
        $programacionclientebancos = Programacionsubcliente::whereNotNull('clientenombre')
                                                    ->where('clientenombre', '!=', '')->whereDate('fechaasignada', '=', now()->addDay()->toDateString())
                                                    ->get();
            $nombreClienteBanco = $clientebanco->nombrecompleto;
            $accionesClienteBanco = BateriaSubCliente::where('clientenombre', $nombreClienteBanco)->pluck('accionnombre')->toArray();
            $idBanco = $clientebanco->nombrecompleto ? ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('id') : null;
            $accionesPorAreaBanco = Programacionsubcliente::where('clientenombre', $nombreClienteBanco)
                ->whereDate('fechaasignada', '=', now()->addDay()->toDateString())
                ->get(['accionnombre', 'proveedornombre', 'fechaasignada', 'horaasignada']);
            $estadoRegistradosBanco = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesClienteBanco)
                ->where('clientebanconombre', $nombreClienteBanco)
                ->pluck('accionnombre')->toArray();
            $accionesDisponiblesBanco = $accionesPorAreaBanco;
        $accionesPorAreasBanco = Programacionsubcliente::where('clientenombre', $nombreClienteBanco)->pluck('accionnombre', 'accionnombre');

        $usuarioAutenticado = Auth::user()->name;

        $hoy = Carbon::now()->startOfDay();

        $mensajes = Mensaje::where('created_at', '>=', $hoy)
        ->orderBy('created_at', 'asc')
        ->get()
        ->groupBy('titulo');

        $mensajesPrincipales = $mensajes->map(function ($group) {
        $primerMensaje = $group->first();
        $ultimoMensaje = $group->last();

        return [
            'mensaje' => $primerMensaje,
            'esUltimoMensajeParaUsuario' => $ultimoMensaje->usuarioregistro != Auth::user()->name,
            'mensajes' => $group
        ];
        });

        $userRole = auth()->user()->getRoleNames()->first(); 

        $licencias = DB::table('licenciaspagos')
        ->where('plazo', '!=', 'PERMANENTE')
        ->whereNull('deleted_at')
        ->whereBetween(DB::raw('DATEDIFF(proximopago, CURDATE())'), [0, 7])
        ->get();

        $santaCruzCount = User::where('estado', 'ACTIVO')
            ->whereNotNull('clienteid')
            ->where('sucursal', 'SANTA CRUZ')
            ->count();

        $cochabambaCount = User::where('estado', 'ACTIVO')
            ->whereNotNull('clienteid')
            ->where('sucursal', 'COCHABAMBA')
            ->count();

        $totalUsers = $santaCruzCount + $cochabambaCount;

        return view('home', compact('userRole','mensajesPrincipales','mensajes','programacionclientebancos','programacionclienteitas','programacionclientecomunes', 'programacionclienteauditorias','clientesComunesCount', 'clientesBancosCount', 'clientesITACount', 'clientesAuditoriasCount', 
        'accionesPorAreasAuditoria', 'accionesPorAreaAuditoria', 'accionesDisponiblesAuditoria', 'clienteauditoria', 'idAuditoria', 'accionesClienteAuditoria', 'estadoRegistradosAuditoria',
        'accionesPorAreasComun', 'accionesPorAreaComun', 'accionesDisponiblesComun', 'clientecomun', 'idComun', 'accionesClienteComun', 'estadoRegistradosComun',
        'accionesPorAreasIta', 'accionesPorAreaIta', 'accionesDisponiblesIta', 'cliente', 'idIta', 'accionesClienteIta', 'estadoRegistradosIta',
        'accionesPorAreasBanco', 'accionesPorAreaBanco', 'accionesDisponiblesBanco', 'clientebanco', 'idBanco', 'accionesClienteBanco', 'estadoRegistradosBanco',
        'progauditoria','progcomun','progita','licencias','santaCruzCount','cochabambaCount','totalUsers'));
    }

    public function marcarPagado($id)
    {
        $licencia = DB::table('licenciaspagos')->where('id', $id)->first();

        $fechaBase = Carbon::parse($licencia->proximopago);

        if ($licencia->tipoplazo == 'AÑO') {
            $nuevoProximoPago = $fechaBase->addYears($licencia->plazo);
        } elseif ($licencia->tipoplazo == 'MES') {
            $nuevoProximoPago = $fechaBase->addMonths($licencia->plazo);
        } elseif ($licencia->tipoplazo == 'DIA') {
            $nuevoProximoPago = $fechaBase->addDays($licencia->plazo);
        }

        DB::table('licenciaspagos')
            ->where('id', $id)
            ->update([
                'updated_at' => Carbon::now(),
                'proximopago' => $nuevoProximoPago
            ]);

        return back()->with('info', 'Licencia marcada como pagada correctamente');
    }
    public function store(Request $request)
{
    // Validar los datos del formulario
    $request->validate([
        'asunto' => 'required|string',
        'usuariodestino' => 'required|string',
        'usuarioregistro' => 'required|string',
        'usuarioid' => 'required|string',
        'respuesta' => 'required|string',
    ]);

    // Crear un nuevo mensaje con la respuesta
    Mensaje::create([
        'titulo' => $request->input('asunto'),
        'usuariodestino' => $request->input('usuariodestino'),
        'usuarioregistro' => $request->input('usuarioregistro'),
        'usuarioid' => $request->input('usuarioid'),
        'mensaje' => $request->input('respuesta'),
    ]);

    return redirect()->back()->with('info', 'Respuesta enviada con éxito');
}

}
