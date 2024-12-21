<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Programacionsubcliente;
use App\Models\Cliente;
use App\Models\ClienteAuditoria;
use App\Models\ClienteBanco;
use App\Models\ClienteComun;
use App\Models\Tramitesubcliente;
use App\Models\Arqueocaja;
use App\Models\Consolidadocaja;
use App\Models\ProveedorInformefinal;
use App\Models\Detallerecibo;
use App\Models\Recibo;
use App\Models\Cajacentral;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MovimientosCajaController extends Controller
{
    /**
     * Constructor para aplicar middleware de permisos.
     */
    public function __construct()
    {
        $this->middleware('can:admin.ingreso.index');
        $this->middleware('can:admin.egreso.index');
    }

    /**
     * Muestra la vista principal de Ingreso.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $sucursal = auth()->user()->sucursal;
        return view('admin.caja.ingreso.index', compact('sucursal'));
    }
    /* public function buscarPorCliente(Request $request)
    {
        $clienteId = $request->input('clienteid');
        $tipoCliente = $request->input('tipoCliente');

        if (!in_array($tipoCliente, ['clienteitaid', 'clienteauditoriaid', 'clientecomunid', 'clientebancoid'])) {
            return response()->json(['error' => 'Tipo de cliente no válido'], 400);
        }

        $registros = ProgramacionSubCliente::where($tipoCliente, $clienteId)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($registro) {
                $tramite = TramitesubCliente::where('clienteitaid', $registro->clienteitaid)
                    ->where('fechabateria', $registro->fechabateria)
                    ->value('tramite');
                $registro->tramite = $tramite;
                return $registro;
            });

        $cliente = null;
        switch ($tipoCliente) {
            case 'clienteitaid':
                $cliente = Cliente::find($clienteId, ['nombrecompleto', 'ci']);
                break;
            case 'clienteauditoriaid':
                $cliente = ClienteAuditoria::find($clienteId, ['nombrecompleto', 'ci']);
                break;
            case 'clientecomunid':
                $cliente = ClienteComun::find($clienteId, ['nombrecompleto', 'ci']);
                break;
            case 'clientebancoid':
                $cliente = ClienteBanco::find($clienteId, ['nombrecompleto', 'ci']);
                break;
        }
        return response()->json([
            'cliente' => $cliente,
            'registros' => $registros
        ]);
    } */

    public function buscarPorCliente(Request $request) 
    {
        $clienteId = $request->input('clienteid');
        $tipoCliente = $request->input('tipoCliente');
    
        if (!in_array($tipoCliente, ['clienteitaid', 'clienteauditoriaid', 'clientecomunid', 'clientebancoid'])) {
            return response()->json(['error' => 'Tipo de cliente no válido'], 400);
        }
    
        // Buscar registros en la tabla ProgramacionSubCliente
        $registrosProgramacion = ProgramacionSubCliente::where($tipoCliente, $clienteId)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($registro) {
                $tramite = TramitesubCliente::where('clienteitaid', $registro->clienteitaid)
                    ->where('fechabateria', $registro->fechabateria)
                    ->value('tramite');
                $registro->tramite = $tramite;
                return $registro;
            });
    
        // Buscar registros en la tabla ProveedorInformesFinales
        $registrosInformesFinales = ProveedorInformefinal::where($tipoCliente, $clienteId)
        ->whereNull('deleted_at')
        ->get()
        ->map(function ($registro) {
            $tramite = TramitesubCliente::where('clienteitaid', $registro->clienteitaid)
                ->where('fechabateria', $registro->fechabateria)
                ->value('tramite');
            $registro->tramite = $tramite;
            return $registro;
        });
    
        // Unir los dos conjuntos de registros
        $registros = $registrosProgramacion->merge($registrosInformesFinales);
    
        // Obtener los datos del cliente según el tipo
        $cliente = null;
        switch ($tipoCliente) {
            case 'clienteitaid':
                $cliente = Cliente::find($clienteId, ['nombrecompleto', 'ci']);
                break;
            case 'clienteauditoriaid':
                $cliente = ClienteAuditoria::find($clienteId, ['nombrecompleto', 'ci']);
                break;
            case 'clientecomunid':
                $cliente = ClienteComun::find($clienteId, ['nombrecompleto', 'ci']);
                break;
            case 'clientebancoid':
                $cliente = ClienteBanco::find($clienteId, ['nombrecompleto', 'ci']);
                break;
        }
    
        // Retornar los registros y el cliente en formato JSON
        return response()->json([
            'cliente' => $cliente,
            'registros' => $registros
        ]);
    }

    public function guardarCajaCentral(Request $request) 
    {
        $usuarioAutenticadoid = Auth::user()->id;
        $usuarioAutenticadonombre = Auth::user()->name;

        $request->validate([
            'tipocliente' => '',
            'clienteid' => '',
            'clientenombre' => '',
            'subtotal' => '',
            'descuento' => '',
            'montototal' => '',
            'ciudadregistro' => '',
        ]);

        $tipocliente = match ($request->tipocliente) {
            'clienteitaid' => 'ITA',
            'clienteauditoriaid' => 'AUDITORIA',
            'clientecomunid' => 'COMUN',
            'clientebancoid' => 'BANCO',
            default => $request->tipocliente,
        };

        $recibo = Recibo::create([
            'ciudadregistro' => $request->ciudadregistro,
            'usuarioregistroid' => $usuarioAutenticadoid,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
            'tipocliente' => $tipocliente,
            'clienteid' => $request->clienteid,
            'clientenombre' => $request->clientenombre,
            'tipomovimiento' => 'INGRESO',
            'subtotal' => $request->subtotal,
            'descuento' => $request->descuento,
            'montototal' => $request->montototal,
            'estado' => '',
        ]);

        Cajacentral::create([
            'tipocliente' => $tipocliente,
            'clienteid' => $request->clienteid,
            'clientenombre' => $request->clientenombre,
            'subtotal' => $request->subtotal,
            'descuento' => $request->descuento,
            'montototal' => $request->montototal,
            'nrorecibo' => $recibo->id,
        ]);

        Detallerecibo::create([
            'reciboid' => $recibo->id,
            'area' => '',
            'detalle' => '',
            'subtotal' => '',
            'descuento' => '',
            'montototal' => '',
            'saldo' => '',
            'usuarioregistroid' => '',
            'usuarioregistronombre' => '',
            'programacionid' => '',
            'fechabateria' => '',
            'servicio' => '',
            'proveedoratencion' => '',
            'fechaatencion' => '',
        ]);


        return redirect()->route('admin.caja.ingreso.index')->with('info', 'Registro guardado correctamente');
    }



    // Guardar en la tabla Cajacentral
    /* Cajacentral::create([
        'tipocliente' => $tipocliente,
        'clienteid' => $request->clienteid,
        'clientenombre' => $request->clientenombre,
        'subtotal' => $request->subtotal,
        'descuento' => $request->descuento,
        'montototal' => $request->montototal,
    ]);

    Detallerecibo::create([
        'reciboid' => $tipocliente,
        'area' => $request->clienteid,
        'detalle' => $request->clientenombre,
        'subtotal' => $request->subtotal,
        'descuento' => $request->descuento,
        'montototal' => $request->montototal,
        'saldo' => $request->montototal,
    ]); */
    // Acción para guardar el arqueo
    public function guardarArqueo(Request $request)
    {
        try {
            $reciboId = $request->input('recibo_id');
            $montoArqueo = $request->input('monto_arqueo');
            $usuarioId = auth()->id();
    
            // Registrar en ArqueoCaja
            ArqueoCaja::create([
                'recibo_id' => $reciboId,
                'usuario_id' => $usuarioId,
                'monto_arqueo' => $montoArqueo,
                'fecha' => now(),
                // Otros campos necesarios
            ]);
    
            // Actualizar ConsolidadoCaja
            $consolidado = ConsolidadoCaja::firstOrNew(['usuario_id' => $usuarioId]);
            $consolidado->total_arqueo += $montoArqueo;
            $consolidado->save();
    
            return response()->json(['success' => true, 'message' => 'Arqueo registrado con éxito.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al guardar el arqueo: ' . $e->getMessage()], 500);
        }
    }
    
    public function create()
    {
    }
    public function store()
    {
    }
    public function edit()
    {
    }
    public function show()
    {
    }
    public function listarIngresos()
    {
        // Aquí puedes obtener los datos necesarios para la vista
        // Por ejemplo:
        // $entradas = Entrada::all();
        return view('admin.caja.ingreso.index'); // Asegúrate de crear esta vista
    }
    public function cierreCaja_Ingresos()
    {
        // Aquí puedes obtener los datos necesarios para la vista
        // Por ejemplo:
        // $entradas = Entrada::all();
        return view('admin.caja.ingreso.cierre'); // Asegúrate de crear esta vista
    }

    public function listarEgresos()
    {
        // Aquí puedes obtener los datos necesarios para la vista
        // Por ejemplo:
        // $entradas = Entrada::all();
        return view('admin.caja.egreso.index'); // Asegúrate de crear esta vista
    }
    public function cierreCaja_Egresos()
    {
        // Aquí puedes obtener los datos necesarios para la vista
        // Por ejemplo:
        // $entradas = Entrada::all();
        return view('admin.caja.egreso.cierre'); // Asegúrate de crear esta vista
    }
    
}