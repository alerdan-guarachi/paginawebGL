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
use App\Models\CuentasPagar;
use App\Models\ProveedorInformefinal;
use App\Models\Detallerecibo;
use App\Models\Recibo;
use App\Models\Proveedor;
use App\Models\Proveedoresservicios;
use App\Models\Cajacentral;
use App\Models\DetalleOrdenes;
use App\Models\Banco;
use App\Models\PermisoCodigo;
use App\Models\Bateriaproveedor;
use App\Models\Bateriasubcliente;
use App\Models\Cierrecaja;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Aprobacioninformefinal;
use App\Models\Informefinal;
use App\Models\Documentacionsubcliente;
use App\Models\Requisitosubcliente;
use App\Models\Requisitosclientesauditoria;
use Barryvdh\DomPDF\Facade\PDF;
use App\Models\Aperturacaja;
use App\Models\DepositosBancarios;
use App\Models\Credito;
use App\Models\CuentasBancos;
use App\Models\CuentasCobrar;
use App\Models\PlanillasPagosGeneradas;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Notifications\ComprobanteNotification;
use App\Models\User;
use App\Models\CCyCPdetalles;

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

// APERTURA DE CAJA Y ID DE RECIBO
    public function guardarAperturaCaja(Request $request)
    {
        $validated = $request->validate([
            'documentoapertura' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        $usuarioId = auth()->user()->id;
        $path = $request->file('documentoapertura')->storeAs('public/aperturacaja/' . $usuarioId, $request->file('documentoapertura')->getClientOriginalName());

        Aperturacaja::create([
            'usuarioaperturaid' => $usuarioId,
            'usuarioaperturanombre' => auth()->user()->name,
            'documentoapertura' => $path,
        ]);

        return back()->with('success', 'Documento de apertura guardado correctamente.');
    }
    public function storeAperturaCaja(Request $request)
    {
        $usuarioAutenticado = auth()->user();
        $usuarioId = $usuarioAutenticado->id;
        $usuarioNombre = $usuarioAutenticado->name;

        $archivo_name = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $carpetaCliente = public_path("/aperturacaja/$usuarioId");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
            Aperturacaja::create([
                'usuarioaperturaid' => $usuarioId,
                'usuarioaperturanombre' => $usuarioNombre,
                'documentoapertura' => $archivo_name,
            ]);
        }

        return back()->with('info', 'Apertura de caja registrada exitosamente.');
    }
    public function obtenerSiguienteId()
    {
        $ultimoId = Recibo::max('id');
        $siguienteId = $ultimoId ? $ultimoId + 1 : 1;

        return response()->json(['siguienteId' => $siguienteId]);
    }
//

// DESBLOQUEO DE CAJA
    public function verificarCodigo(Request $request)
    {
        $codigoIngresado = $request->input('codigo');
        $usuarioAutenticado = auth()->user()->name;
        $fechaActual = now()->toDateString();

        $codigoAprobacion = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $fechaActual)
            ->where('permisoSolicitado', 'admin.ingreso.index')
            ->where('codigo', $codigoIngresado)
            ->first();

        if ($codigoAprobacion && $codigoAprobacion->estado != 'expirado') {
            $codigoAprobacion->horaActivacion = now(); 
            $codigoAprobacion->estado = 'expirado';
            $codigoAprobacion->save();
            return redirect()->route('admin.caja.ingreso.index')->with('info', 'CÓDIGO VÁLIDO, AHORA SI PUEDES CONTINUAR');

        } elseif ($codigoAprobacion && $codigoAprobacion->estado == 'expirado') {

            return back()->with('infoerror', 'EL CÓDIGO YA HA SIDO USADO, EL ACCESO ESTA BLOQUEADO');
        } else {

            return back()->with('infoerror', 'CÓDIGO INVALIDO O NO AUTORIZADO');
        }
    }
//

// CAJA INGRESOS
    public function index(Request $request)
    {
        $sucursal = auth()->user()->sucursal;
        $consolidados = Consolidadocaja::all();
        $cuentas = CuentasBancos::where('estado', 'ACTIVO')->get();
        $bancos = Banco::all();
        $ultimoId = Recibo::max('id');
        $siguienteId = $ultimoId ? $ultimoId + 1 : 1;

        $rolusuario = auth()->user()->getRoleNames()->first();
        $user = auth()->user();

        $usuarioAutenticado = auth()->user()->name;

        $consolidadosusuario = Consolidadocaja::where('usuarioconsolidadonombre', $usuarioAutenticado)->get();

        $hoy = now();
        if ($hoy->dayOfWeek == 1) {
            $fechaAyer = $hoy->copy()->subDays(2)->toDateString();
        } else {
            $fechaAyer = $hoy->copy()->subDay()->toDateString();
        }

        /* $registroCierreCajaAyer = DB::table('cierrecaja')
            ->where('usuariocierrenombre', $usuarioAutenticado)
            ->whereDate('updated_at', $fechaAyer)
            ->orderBy('updated_at', 'desc')
            ->first();

        $codigoAprobacion = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.ingreso.index')
            ->where('estado', 'expirado')
            ->exists();

        $mostrarVista = $registroCierreCajaAyer || $codigoAprobacion; */

        // BLOQUEO CAJA
            $idUsuario = auth()->user()->id;
            $usuarioAutenticado = auth()->user()->name;
            $hoy = Carbon::today();
            $ayer = Carbon::yesterday();
            $horaLimite = Carbon::today()->setTime(10, 00);
            $ahora = Carbon::now();
            $mostrarVista = true;

            $ultimoRegistro = DB::table('cajacentral')
                ->where('usuarioregistroid', $idUsuario)
                ->orderBy('created_at', 'desc')
                ->first();

            $registroCierreCaja = true;

            if ($ultimoRegistro) {
                $fechaUltimoRegistro = Carbon::parse($ultimoRegistro->created_at)->toDateString();

                if ($fechaUltimoRegistro !== $hoy->toDateString()) {
                    $registroCierreCaja = DB::table('cierrecaja')
                        ->where('usuariocierreid', $idUsuario)
                        ->whereDate('created_at', $fechaUltimoRegistro)
                        ->exists();
                }
            }

            $codigoAprobacion = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
                ->whereDate('fechaSolicitada', $hoy->toDateString())
                ->where('permisoSolicitado', 'admin.ingreso.index')
                ->where('estado', 'expirado')
                ->exists();

            /*  $mostrarVista = $registroCierreCaja || $codigoAprobacion; */

            $tuvoEfectivoAyer = DB::table('cajacentral')
                ->where('usuarioregistroid', $idUsuario)
                ->whereDate('created_at', $ayer->toDateString())
                ->where('tipotransaccion', 'EFECTIVO')
                ->exists();

            $registroDepositoHoyAntesDe10am = true;

            if ($ahora->greaterThan($horaLimite)) {
                $registroDepositoHoyAntesDe10am = DB::table('depositosbancarios')
                    ->where('usuarioregistroid', $idUsuario)
                    ->whereDate('created_at', $hoy->toDateString())
                    ->whereTime('created_at', '<=', $horaLimite->toTimeString())
                    ->exists();
            }

            $restriccionDeposito = $tuvoEfectivoAyer && !$registroDepositoHoyAntesDe10am;

            $mostrarVista = ($registroCierreCaja && !$restriccionDeposito) || $codigoAprobacion;
        //

        $arqueos = Arqueocaja::where('usuarioarqueonombre', $usuarioAutenticado)
            ->get();

        $usuarioArqueoId = auth()->user()->id; // Asumimos que tienes la autenticación configurada

        // Obtener el registro del arqueo para el usuario autenticado
        $arqueo = ArqueoCaja::where('usuarioarqueoid', $usuarioArqueoId)->first();
        $hoy = \Carbon\Carbon::today(); // Esto obtendrá la fecha actual sin la hora.

        $registroapertura = Aperturacaja::where('usuarioaperturanombre', $usuarioAutenticado)
            ->whereDate('created_at', $hoy) // Compara solo la fecha
            ->first();

        $aperturascajas = Aperturacaja::orderBy('created_at', 'desc')->get();
        
        $entrada = $request->input('clienteid');
        $tieneCredito = Credito::where('clienteid', $entrada)->get();

        $proveedores = ProveedoresServicios::select('id', 'razonsocial')->orderBy('razonsocial')->get();
        $clientesIta = Cliente::select('id', 'nombrecompleto')->orderBy('nombrecompleto')->get();
        $clientesAuditoria = ClienteAuditoria::select('id', 'nombrecompleto')->orderBy('nombrecompleto')->get();
        $clientesComunes = ClienteComun::select('id', 'nombrecompleto')->orderBy('nombrecompleto')->get();
        
        return view('admin.caja.ingreso.index', [
            'mostrarVista' => $mostrarVista,
            'sucursal' => $sucursal,
            'consolidados' => $consolidados,
            'bancos' => $bancos,
            'siguienteId' => $siguienteId,
            'rolusuario' => $rolusuario,
            'arqueos' => $arqueos,
            'arqueo' => $arqueo,
            'consolidadosusuario'=> $consolidadosusuario,
            'registroapertura'=> $registroapertura,
            'aperturascajas'=> $aperturascajas,
            'tieneCredito' => $tieneCredito, 
            'cuentas' => $cuentas,
            'proveedores' => $proveedores,
            'clientesIta' => $clientesIta,
            'clientesAuditoria' => $clientesAuditoria,
            'clientesComunes' => $clientesComunes
        ]);
    }
    public function expirar(Request $request)
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

    public function buscarPorCliente(Request $request)  
    {
        $entrada = $request->input('clienteid');
        $tipoCliente = $request->input('tipoCliente');
        $buscarHoy = $request->input('buscarHoy', false);
        $buscarPorFechas = $request->input('buscarPorFechas', false);
        $fechaInicio = $request->input('fechaInicio');
        $fechaFinal = $request->input('fechaFinal');

        if ($fechaInicio) {
            $fechaInicio = \Carbon\Carbon::parse($fechaInicio)->format('Y-m-d');
        }
        if ($fechaFinal) {
            $fechaFinal = \Carbon\Carbon::parse($fechaFinal)->format('Y-m-d');
        }
        if (!in_array($tipoCliente, ['clienteitaid', 'clienteauditoriaid', 'clientecomunid', 'clientebancoid', 'clienteproveedor'])) {
            return response()->json(['error' => 'Tipo de cliente no válido'], 400);
        }
        $clienteId = null;
        switch ($tipoCliente) {
            case 'clienteitaid':
                $cliente = Cliente::where('ci', $entrada)->orWhere('id', $entrada)->first(['id', 'nombrecompleto', 'ci']);
                break;
            case 'clienteauditoriaid':
                $cliente = ClienteAuditoria::where('ci', $entrada)->orWhere('id', $entrada)->first(['id', 'nombrecompleto', 'ci']);
                break;
            case 'clientecomunid':
                $cliente = ClienteComun::where('ci', $entrada)->orWhere('id', $entrada)->first(['id', 'nombrecompleto', 'ci']);
                break;
            case 'clienteproveedor':
                $cliente = ClienteBanco::where('ci', $entrada)->orWhere('id', $entrada)->first(['id', 'nombrecompleto', 'ci']);
                break;
            case 'clientebancoid':
                $cliente = Proveedoresservicios::where('ci', $entrada)->orWhere('id', $entrada)->first(['id', 'razonsocial', 'ci']);
                break;
            default:
                $cliente = null;
        }
        if (!$cliente) {
            return response()->json(['error' => 'CLIENTE NO ENCONTRADO'], 404);
        }
        $clienteId = $cliente->id;
        $hoy = now()->toDateString();
        $registrosProgramacion = ProgramacionSubCliente::where($tipoCliente, $clienteId)
            ->whereNull('deleted_at')
            ->where('pagoservicio', 'INTERNO')
            ->when($buscarHoy, function ($query) use ($hoy) {
                return $query->where('fechaasignada', $hoy); 
            })
            ->when($buscarPorFechas, function ($query) use ($fechaInicio, $fechaFinal) {
                if ($fechaInicio && $fechaFinal) {
                    return $query->whereBetween('fechaasignada', [$fechaInicio, $fechaFinal]);
                }
                return $query;
            })
            ->where(function ($query) {
                $query->where('preciocompra', '>', 0)
                      ->orWhere(function ($subQuery) {
                          $subQuery->whereNotNull('preciocompra')
                                   ->where('preciocompra', '!=', '0.00')
                                   ->where('preciocompra', '!=', '0,00')
                                   ->where('preciocompra', '!=', '0');
                      });
            })
            ->get()
            /* ->map(function ($registro) {
                $detallerecibo = Detallerecibo::where('programacionid', $registro->id) 
                    ->latest('created_at')
                    ->first();

                if ($detallerecibo) {
                    if ($detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'INGRESO') {
                        return null;
                    }

                    if ($detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'INGRESO') {
                        $registro->precio = $detallerecibo->saldo;
                    }
                } */
                ->reject(function ($registro) {
                    $existeEnBateria = BateriaSubCliente::where('fechabateria', $registro->fechabateria)
                        ->where('accionnombre', $registro->accionnombre)
                        ->where('proveedorasignado', $registro->proveedornombre)
                        ->whereRaw("
                            CASE 
                                WHEN ? IS NOT NULL THEN clienteitaid = ? 
                                WHEN ? IS NOT NULL THEN clienteauditoriaid = ? 
                                WHEN ? IS NOT NULL THEN clientecomunid = ? 
                            END
                        ", [
                            $registro->clienteitaid, $registro->clienteitaid,
                            $registro->clienteauditoriaid, $registro->clienteauditoriaid,
                            $registro->clientecomunid, $registro->clientecomunid
                        ])
                        ->where('pagoatencion', 'PAGO PROCESADO')
                        ->exists();
            
                    if ($existeEnBateria) {
                        return true;
                    }
                    $detallerecibo = Detallerecibo::where('programacionid', $registro->id)
                        ->where('tipomovimiento', 'INGRESO')
                        ->latest('created_at')
                        ->first();
            
                    if ($detallerecibo && $detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'INGRESO') {
                        return true;
                    }
                    return false;
                })
                ->map(function ($registro) {
                    $detallerecibo = Detallerecibo::where('programacionid', $registro->id)
                        ->latest('created_at')
                        ->first();
            
                    if ($detallerecibo && $detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'INGRESO') {
                        $registro->precio = $detallerecibo->saldo;
                    }

                switch (true) {
                    case isset($registro->clienteitaid):
                        $registro->tramite = TramitesubCliente::where('clienteitaid', $registro->clienteitaid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;

                    case isset($registro->clienteid):
                        $registro->tramite = TramitesubCliente::where('clienteid', $registro->clienteid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;

                    case isset($registro->clientecomunid):
                        $registro->tramite = TramitesubCliente::where('clientecomunid', $registro->clientecomunid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;

                    case isset($registro->clienteauditoriaid):
                        $registro->tramite = TramitesubCliente::where('clienteauditoriaid', $registro->clienteauditoriaid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;
                }
                return $registro;
            })
            ->filter()
        ->values();
        // Obtener registros de ProveedorInformefinal
        $registrosInformesFinales = ProveedorInformefinal::where($tipoCliente, $clienteId)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($registro) use ($clienteId) {
                $detallerecibo = Detallerecibo::where('provinfofinalid', $registro->id)
                    ->latest('created_at')
                    ->first();
    
                if ($detallerecibo) {
                    if ($detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'INGRESO') {
                        return null;
                    }
    
                    if ($detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'INGRESO') {
                        $registro->precio = $detallerecibo->saldo;
                    }
                }
    
                switch (true) {
                    case isset($registro->clienteitaid):
                        $registro->tramite = TramitesubCliente::where('clienteitaid', $registro->clienteitaid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->latest('id')
                            ->value('tramite');
                        break;

                    case isset($registro->clienteid):
                        $registro->tramite = TramitesubCliente::where('clienteid', $registro->clienteid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->latest('id')
                            ->value('tramite');
                        break;

                    case isset($registro->clientecomunid):
                        $registro->tramite = TramitesubCliente::where('clientecomunid', $registro->clientecomunid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->latest('id')
                            ->value('tramite');
                        break;

                    case isset($registro->clienteauditoriaid):
                        $registro->tramite = TramitesubCliente::where('clienteauditoriaid', $registro->clienteauditoriaid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->latest('id')
                            ->value('tramite');
                        break;
                }
                return $registro;
            })
            ->filter()
        ->values();
    
        /* $registrosCuentasporPagar = CuentasCobrar::where('proveedornombre', $cliente->razonsocial)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($registro) {

                $detallerecibo = Detallerecibo::where('cuentacobrarid', $registro->id) 
                    ->latest('created_at')
                    ->first();

                if ($detallerecibo) {
                    if ($detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'INGRESO') {
                        return null;
                    }

                    if ($detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'INGRESO') {
                        $registro->precio = $detallerecibo->saldo;
                    }
                }
                return $registro;
            })
            ->filter()
        ->values(); */
        $registrosCuentasporPagar = CuentasCobrar::where(function ($query) use ($clienteId, $cliente) {
            $query->where('proveedorid', $clienteId)
                  ->orWhere('proveedornombre', $cliente->razonsocial);
        })
        ->whereNull('deleted_at')
        ->get()
        ->map(function ($registro) {
            $detallerecibo = Detallerecibo::where('cuentacobrarid', $registro->id) 
                ->latest('created_at')
                ->first();
    
            if ($detallerecibo) {
                if ($detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'INGRESO') {
                    return null;
                }
    
                if ($detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'INGRESO') {
                    $registro->precio = $detallerecibo->saldo;
                }
            }
            return $registro;
        })
        ->filter()
        ->values();

        $registros = $registrosProgramacion->merge($registrosInformesFinales)->merge($registrosCuentasporPagar);
        $tieneCredito = Credito::where('clienteid', $clienteId)->exists();
        $creditos = collect();

        if ($tieneCredito) {
            $creditos = Credito::where('clienteid', $clienteId)->whereNotNull('cartacredito')->whereNull('estado')->get();
        }

        $usuarioNombre = Auth::user()->name;
        /* $permisoExiste = PermisoCodigo::where('clienteid', $entrada)
            ->whereDate('fechaSolicitada', Carbon::today())
            ->where('usuarioSolicitante', $usuarioNombre)
            ->where('permisoSolicitado', 'admin.caja.ingresos.concederdescuentosingresos')
            ->where('estado', 'expirado')
        ->exists(); */
        $permisoExiste = PermisoCodigo::where('clienteid', $entrada)
            ->whereDate('fechaSolicitada', Carbon::today())
            ->where('usuarioSolicitante', $usuarioNombre)
            ->where('permisoSolicitado', 'admin.caja.ingresos.concederdescuentosingresos')
            ->where('estado', 'expirado')
            /* ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('cajacentral as c')
                    ->whereRaw('c.created_at > permisos_codigo.updated_at');
            }) */
        ->exists();

        $permisoExistefecha = PermisoCodigo::where('clienteid', $entrada)
            ->whereDate('fechaSolicitada', Carbon::today())
            ->where('usuarioSolicitante', $usuarioNombre)
            ->where('permisoSolicitado', 'admin.caja.ingresos.cambiarfecharegistro')
            ->where('estado', 'expirado')
            /* ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('cajacentral as c')
                    ->whereRaw('c.fecharegistroreal > permisos_codigo.updated_at');
            }) */
        ->exists();

        return response()->json([
            'cliente' => $cliente,
            'registros' => $registros,'tieneCredito' => $tieneCredito,'creditos' => $creditos,'permitirDescuento' => $permisoExiste,'permisoExistefecha' => $permisoExistefecha
        ]);
    }
    public function obtenerCreditos(Request $request) 
    {
        $clienteId = $request->input('clienteid');
    
        $creditos = Credito::where('clienteid', $clienteId)->get();
    
        return response()->json([
            'creditos' => $creditos
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
            'montoreal' => '',
            'montototal' => '',
            'ciudadregistro' => '',
            'programacionIds' => '',
            'area' => '',
            'detalle' => '',
            'nrofactura' => '',
            'nrobancarizaciondeposito' => '',
            'nrobancarizaciontransferencia' => '',
            'nrocheque' => '',
            'nrotarjeta' => '',
            'nroap' => '',
            'nroref' => '',
            'nrocuentadestinodeposito' => '',
            'nrocuentaorigendeposito' => '',
            'tipobancodeposito' => '',
            'nrocuentadestinotransferencia' => '',
            'nrocuentaorigentransferencia' => '',
            'tipobancotransferencia' => '',
            'tipobancocheque' => '',
            'nrocuentadestinocheque' => '',
            'tipobanco' => '',
            'tipocambio' => '',
            'tipomovimiento' => '',
            'tipotransaccion' => '',
            'tipotransaccion2' => '',
            'ciudadregistro' => '',
            'nombrebanco' => '',
            'numerobanco' => '',
            'usuarioregistroid' => '',
            'usuarioregistronombre' => '',
            'diferenciacontra' => '',
            'diferenciafavor' => '',
            'montoPagado' => '',
            'cambio' => '',
            'descuentoatc' => '',
            'montorealatc' => '',
            'billetecorte200' => '',
            'billetecorte100' => '',
            'billetecorte50' => '',
            'billetecorte20' => '',
            'billetecorte10' => '',
            'monedacorte5' => '',
            'monedacorte2' => '',
            'monedacorte1' => '',
            'monedacorte050' => '',
            'monedacorte020' => '',
            'monedacorte010' => '',
            'montototal' => '',
            'created_at' => '',
            'updated_at' => '',

            'cambiobilletecorte200' => '',
            'cambiobilletecorte100' => '',
            'cambiobilletecorte50' => '',
            'cambiobilletecorte20' => '',
            'cambiobilletecorte10' => '',
            'cambiomoneda5' => '',
            'cambiomoneda2' => '',
            'cambiomoneda1' => '',
            'cambiomoneda050' => '',
            'cambiomoneda020' => '',
            'cambiomoneda010' => '',
            
        ]);

        if (empty($request->programacionIds)) {
            // Redirigir con el mensaje en la sesión
            return redirect()->back()->with('infoerror', 'Debes seleccionar al menos un registro');
        }

        $programacionIds = explode(',', $request->programacionIds);
        $descuentos = explode(',', $request->descuentos);
        $pagos = explode(',', $request->pagos);

        $tipocliente = match ($request->tipocliente) {
            'clienteitaid' => 'ITA',
            'clienteauditoriaid' => 'AUDITORIA',
            'clientecomunid' => 'COMUN',
            'clientebancoid' => 'PROVEEDOR DE SERVICIO',
            default => $request->tipocliente,
        };

        $saldototal = $request->montoreal - ($request->montototal + $request->descuento);
        $saldototal = number_format($saldototal, 2, '.', ''); 

        $estado = ($saldototal == 0) ? 'PAGO PROCESADO' : 'SALDO PENDIENTE';

        $tipotransaccion = $request->tipotransaccion;
        if ($tipotransaccion == 'DEPOSITO_BANCARIO') {
            $tipotransaccion = 'DEPOSITO BANCARIO';
        } elseif ($tipotransaccion == 'TRANSFERENCIA_BANCARIA') {
            $tipotransaccion = 'TRANSFERENCIA BANCARIA';
        } elseif (in_array($tipotransaccion, ['EFECTIVO', 'CHEQUE', 'ATC'])) {
        }

        $tipotransaccion2 = $request->tipotransaccion2;
        if ($tipotransaccion2 == 'DEPOSITO_BANCARIO') {
            $tipotransaccion2 = 'DEPOSITO BANCARIO';
        } elseif ($tipotransaccion2 == 'TRANSFERENCIA_BANCARIA') {
            $tipotransaccion2 = 'TRANSFERENCIA BANCARIA';
        } elseif (in_array($tipotransaccion2, ['EFECTIVO', 'CHEQUE', 'ATC'])) {
        }

        $fechaCreacion = $request->created_at
        ? Carbon::createFromFormat('Y-m-d\TH:i', $request->created_at)
        : now();

        $fechaActualizacion = $request->updated_at
        ? Carbon::createFromFormat('Y-m-d\TH:i', $request->updated_at)
        : now();

        $recibo = Recibo::create([
            'ciudadregistro' => $request->ciudadregistro,
            'usuarioregistroid' => $usuarioAutenticadoid,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
            'tipocliente' => $tipocliente,
            /* 'clienteid' => $request->clienteid,
            'clientenombre' => $request->clientenombre, */
            'clienteid' => ($request->area === 'CUENTA POR COBRAR') ? null : $request->clienteid,
            'clientenombre' => ($request->area === 'CUENTA POR COBRAR') ? null : $request->clientenombre,
            'proveedorid' => ($request->area === 'CUENTA POR COBRAR') ? $request->clienteid : null,
            'proveedornombre' => ($request->area === 'CUENTA POR COBRAR') ? $request->clientenombre : null,
            'tipomovimiento' => 'INGRESO',
            'subtotal' => $request->subtotal,
            'descuentototal' => $request->descuento,
            'montototal' => $request->montototal,
            'estado' => $estado,
            'saldototal' => $saldototal,
            'created_at' => $fechaCreacion,
            'updated_at' => $fechaActualizacion,
        ]);

        Cajacentral::create([
            'tipocliente' => $tipocliente,
            /* 'clienteid' => $request->clienteid,
            'clientenombre' => $request->clientenombre,
            'proveedorid' => $request->clienteid,
            'proveedornombre' => $request->clientenombre, */
            'clienteid' => ($request->area === 'CUENTA POR COBRAR') ? null : $request->clienteid,
            'clientenombre' => ($request->area === 'CUENTA POR COBRAR') ? null : $request->clientenombre,
            'proveedorid' => ($request->area === 'CUENTA POR COBRAR') ? $request->clienteid : null,
            'proveedornombre' => ($request->area === 'CUENTA POR COBRAR') ? $request->clientenombre : null,
            'subtotal' => $request->subtotal,
            'descuento' => $request->descuento,
            'montototal' => $request->montototal,
            'nrorecibo' => $recibo->id,
            'saldo' => $saldototal,
            'estado' => $estado,
            'area' =>  $request->area,
            'detalle' => $request->detalle,
            'nrofactura' => $request->nrofactura,
            'nrobancarizaciontransferencia' => $request->nrobancarizaciontransferencia,
            'nrobancarizaciondeposito' => $request->nrobancarizaciondeposito,
            'nrocheque' => $request->nrocheque,
            'nrotarjeta' => $request->nrotarjeta,
            'nroap' => $request->nroap,
            'nroref' => $request->nroref,
            'nrocuentadestinodeposito' => $request->nrocuentadestinodeposito,
            'nrocuentaorigendeposito' => $request->nrocuentaorigendeposito,
            'tipobancodeposito' => $request->tipobancodeposito,
            'nrocuentadestinotransferencia' => $request->nrocuentadestinotransferencia,
            'nrocuentaorigentransferencia' => $request->nrocuentaorigentransferencia,
            'tipobancotransferencia' => $request->tipobancotransferencia,
            'tipobancocheque' => $request->tipobancocheque,
            'tipobanco' => $request->tipobanco,
            'tipocambio' => $request->tipocambio,
            'tipomovimiento' => 'INGRESO',
            'tipotransaccion' => $tipotransaccion,
            'tipotransaccion2' => $tipotransaccion2,
            'ciudadregistro' => $request->ciudadregistro,
            'nombrebanco' => $request->nombrebanco,
            'numerobanco' => $request->numerobanco,
            'usuarioregistroid' => $usuarioAutenticadoid,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
            'estadorevisioncierre' => 'DOCUMENTACION PENDIENTE',
            'diferenciafavor' => $request->diferenciafavor,
            'diferenciacontra' => $request->diferenciacontra,
            'montopago' => $request->montoPagado,
            'montodevuelto' => $request->cambio,
            'descuentoatc' => ($tipotransaccion === 'ATC') ? $request->montototal * 0.02 : 0,
            'nrocuentadestinoatc' => ($tipotransaccion === 'ATC') ? 3000189269 : null,
            'nrocuentadestinocheque' => ($tipotransaccion === 'CHEQUE') ? 3000189269 : null,
            'created_at' => $fechaCreacion,
            'updated_at' => $fechaActualizacion,
            'fecharegistroreal' => now(),
        ]);

        foreach ($programacionIds as $index => $programacionId) {
            /* $programacion = ProgramacionSubCliente::find($programacionId);
            $proveedor = ProveedorInformeFinal::find($programacionId);
            $cuentapagar = CuentasCobrar::where('id', $programacionId)->first(); */

            $programacion = null;
            $proveedor = null;
            $cuentapagar = null;
            if (str_ends_with($programacionId, 'CC')) {
                $cuentapagar = CuentasCobrar::find($programacionId);
            }
            if (!$cuentapagar) {
                $programacion = ProgramacionSubCliente::find($programacionId);
                if (!$programacion) {
                    $proveedor = ProveedorInformeFinal::find($programacionId);
                }
            }

            $ultimoDetalleRecibo = Detallerecibo::where(function ($query) use ($programacionId) {
                    $query->where('programacionid', $programacionId)
                          ->orWhere('provinfofinalid', $programacionId)
                          ->orWhere('cuentacobrarid', $programacionId);
                })
                ->where('tipomovimiento', 'INGRESO')
                ->orderBy('created_at', 'desc') 
                ->first();
            

            if ($ultimoDetalleRecibo) {
                $subtotalDetalle = $ultimoDetalleRecibo->saldo;
                $descuentoDetalle = $descuentos[$index];
                $pagoDetalle = $pagos[$index];
            } else {
                /* $subtotalDetalle = $programacion ? $programacion->precio : $proveedor->precio;
                $descuentoDetalle = $descuentos[$index];
                $pagoDetalle = $pagos[$index]; */

                if ($programacion) {
                    $subtotalDetalle = $programacion->precio;
                } elseif ($proveedor) {
                    $subtotalDetalle = $proveedor->precio;
                } elseif ($cuentapagar) {
                    $subtotalDetalle = $cuentapagar->precio;
                } else {
                    $subtotalDetalle = 0;
                }
                $descuentoDetalle = $descuentos[$index];
                $pagoDetalle = $pagos[$index];
            }

            $subtotalDetalle = is_numeric($subtotalDetalle) ? floatval($subtotalDetalle) : 0;
            $saldoDetalle = $subtotalDetalle - $descuentoDetalle - $pagoDetalle;
            $saldoDetalle = number_format($saldoDetalle, 2, '.', '');
            $estadoDetalle = ($saldoDetalle == 0) ? 'PAGO PROCESADO' : 'SALDO PENDIENTE';

            if ($estadoDetalle == 'PAGO PROCESADO') {
                Detallerecibo::where('programacionid', $programacionId)
                    ->orwhere('provinfofinalid', $programacionId)
                    ->orwhere('cuentacobrarid', $programacionId)
                    ->where('estado', '!=', 'PAGO PROCESADO')
                    ->update(['estado' => 'PAGO PROCESADO']);
            }

            if ($programacion) {
                $area = 'MEDICA';
            } elseif ($proveedor) {
                $area = 'INFORME FINAL';
            } elseif ($cuentapagar) {
                $area = 'CUENTA POR COBRAR';
            }

        
            $detalleRecibo = Detallerecibo::create([
                'reciboid' => $recibo->id,
                'cuentacobrarid' => $cuentapagar ? $programacionId : null,
                'clienteid' => $cuentapagar ? null : (
                    $programacion ? 
                        ($programacion->clienteitaid ?? $programacion->clienteauditoriaid ?? $programacion->clientecomunid) : 
                        ($proveedor ? 
                            ($proveedor->clienteitaid ?? $proveedor->clienteauditoriaid ?? $proveedor->clientecomunid) : 
                        null)
                ),

                'clientenombre' => $cuentapagar ? null : (
                        $programacion ? 
                            ($programacion->clienteitanombre ?? $programacion->clienteauditorianombre ?? $programacion->clientecomunnombre) : 
                            ($proveedor ? 
                                ($proveedor->clienteitanombre ?? $proveedor->clienteauditorianombre ?? $proveedor->clientecomunnombre) : 
                            null)
                    ),

                'programacionid' => $programacion ? $programacionId : null,
                'bateriaid' => $programacion ? $programacion->bateriaid : null,
                'provinfofinalid' => $proveedor ? $programacionId : null,
                'usuarioregistroid' => $usuarioAutenticadoid,
                'usuarioregistronombre' => $usuarioAutenticadonombre,
                'area' => $area,
                'detalle' => $cuentapagar
                ? ($cuentapagar->cantidad > 0 
                    ? $cuentapagar->cantidad . ' ' . $cuentapagar->detalleproducto 
                    : $cuentapagar->detalleproducto)
                : ($programacion->accionnombre ?? $proveedor->accionnombre ?? null),

                'fechabateria' => $cuentapagar ? $cuentapagar->fechaasignada : ($programacion ? $programacion->fechabateria : $proveedor->fechabateria),
                'fechaatencion' => $cuentapagar ? $cuentapagar->fechaasignada : ($programacion ? $programacion->fechaasignada : $proveedor->fechaasignada),
                'servicio' => $cuentapagar ? null : ($programacion ? $programacion->servicio : $proveedor->servicio),
                'proveedoratencion' => $cuentapagar ? $cuentapagar->proveedornombre : ($programacion ? $programacion->proveedornombre : $proveedor->proveedorasignado),
                'subtotal' => $subtotalDetalle,
                'descuento' => $descuentoDetalle,
                'montototal' => $pagoDetalle,
                'saldo' => $saldoDetalle,
                'estado' => $estadoDetalle,
                'tipomovimiento' => 'INGRESO',
                'tipotransaccion' => $tipotransaccion,
                'descuentoatc' => ($tipotransaccion === 'ATC') ? $pagoDetalle * 0.02 : 0,
                'created_at' => $fechaCreacion,
                'updated_at' => $fechaActualizacion,
                
            ]);
                // Buscar el primer crédito que coincida y que NO tenga estado "PROCESADO"
            $credito = Credito::where('clienteid', $detalleRecibo->clienteid)
                ->where('clientenombre', $detalleRecibo->clientenombre)
                /* ->where('detalle', $detalleRecibo->detalle) */
                /* ->where('proveedor', $detalleRecibo->proveedoratencion) */
                ->where('montocuota', $detalleRecibo->montototal)
                ->whereDate('fechacredito', $detalleRecibo->created_at->toDateString())

                ->whereNull('estado')
                ->orderBy('id', 'asc') // Asegura que se toma el más antiguo disponible
                ->first();

            // Si se encuentra un crédito sin procesar, actualizar su estado a "PROCESADO"
            if ($credito) {
            $credito->update(['estado' => 'PROCESADO']);
            }

            if ($detalleRecibo->cuentacobrarid) {
                CuentasCobrar::whereIn('id', explode(',', $detalleRecibo->cuentacobrarid))
                    ->update(['estado' => $detalleRecibo->estado]);
            }

        }

        $usuarioAutenticadoid = Auth::id();

        $arqueo = ArqueoCaja::where('usuarioarqueoid', $usuarioAutenticadoid)->first();

        if ($arqueo) {
            $arqueo->update([
                'billetecorte200' => $arqueo->billetecorte200 + $request->billetecorte200 - $request->cambiobilletecorte200,
                'billetecorte100' => $arqueo->billetecorte100 + $request->billetecorte100 - $request->cambiobilletecorte100,
                'billetecorte50' => $arqueo->billetecorte50 + $request->billetecorte50 - $request->cambiobilletecorte500,
                'billetecorte20' => $arqueo->billetecorte20 + $request->billetecorte20 - $request->cambiobilletecorte20,
                'billetecorte10' => $arqueo->billetecorte10 + $request->billetecorte10 - $request->cambiobilletecorte10,
                'monedacorte5' => $arqueo->monedacorte5 + $request->monedacorte5 - $request->cambiomoneda5,
                'monedacorte2' => $arqueo->monedacorte2 + $request->monedacorte2 - $request->cambiomoneda2,
                'monedacorte1' => $arqueo->monedacorte1 + $request->monedacorte1 - $request->cambiomoneda1,
                'monedacorte050' => $arqueo->monedacorte050 + $request->monedacorte050 - $request->cambiomoneda050,
                'monedacorte020' => $arqueo->monedacorte020 + $request->monedacorte020 - $request->cambiomoneda020,
                'monedacorte010' => $arqueo->monedacorte010 + $request->monedacorte010 - $request->cambiomoneda010,
            ]);

             // Calcular el consolidado efectivo sumando billetes y monedas
                $consolidadoEfectivo = 
                ($request->billetecorte200 * 200) +
                ($request->billetecorte100 * 100) +
                ($request->billetecorte50 * 50) +
                ($request->billetecorte20 * 20) +
                ($request->billetecorte10 * 10) +
                ($request->monedacorte5 * 5) +
                ($request->monedacorte2 * 2) +
                ($request->monedacorte1 * 1) +
                ($request->monedacorte050 * 0.50) +
                ($request->monedacorte020 * 0.20) +
                ($request->monedacorte010 * 0.10);

            // Obtener el consolidado de caja
            $consolidado = Consolidadocaja::where('usuarioconsolidadoid', $usuarioAutenticadoid)->first();

            if ($consolidado) {
                // Actualizar el consolidado efectivo sumando el total calculado
                $consolidado->update([
                    'consolidadoefectivo' => $consolidado->consolidadoefectivo + $consolidadoEfectivo,
                ]);
            }
        }

        // Determinamos qué columna debemos actualizar según el tipo de transacción
        switch ($tipotransaccion) {
            case 'DEPOSITO BANCARIO':
                $columna = 'consolidadodeposito';
                break;
            case 'TRANSFERENCIA BANCARIA':
                $columna = 'consolidadotransferencia';
                break;
            case 'CHEQUE':
                $columna = 'consolidadocheque';
                break;
            case 'ATC':
                return redirect()->route('admin.caja.ingreso.index')
                ->with('info', 'Registro guardado correctamente')
                ->with('montototal', $request->montototal)
                ->with('tipotransaccion', $request->tipotransaccion)
                ->with('tipotransaccion2', $request->tipotransaccion2);
            case 'EFECTIVO':
                return redirect()->route('admin.caja.ingreso.index')
                ->with('info', 'Registro guardado correctamente')
                ->with('montototal', $request->montototal)
                ->with('tipotransaccion', $request->tipotransaccion)
                ->with('tipotransaccion2', $request->tipotransaccion2);
            default:
                return response()->json(['error' => 'Tipo de transacción no válido.'], 400);
        }

        // Actualizamos el monto en la columna correspondiente de la tabla Consolidados
        $consolidado = Consolidadocaja::where('usuarioconsolidadoid', $usuarioAutenticadoid)->first();

        if ($consolidado) {
            // Si ya existe un registro, sumamos el monto total a la columna correspondiente
            $consolidado->$columna += $request->montototal;
            $consolidado->save();
        } else {
            // Si no existe un registro, lo creamos con el monto total en la columna correspondiente
            $consolidado = new Consolidadocaja();
            $consolidado->usuarioconsolidadoid = $usuarioAutenticadoid;
            $consolidado->$columna = $request->montototal;
            $consolidado->save();
        }
    
        return redirect()->route('admin.caja.ingreso.index')
            ->with('info', 'Registro guardado correctamente')
            ->with('montototal', $request->montototal)
            ->with('tipotransaccion', $request->tipotransaccion)
            ->with('tipotransaccion2', $request->tipotransaccion2);
    }
    public function guardarArqueo(Request $request)
    {
        $request->validate([
            'billetecorte200' => 'required|integer|min:0',
            'billetecorte100' => 'required|integer|min:0',
            'billetecorte50' => 'required|integer|min:0',
            'billetecorte20' => 'required|integer|min:0',
            'billetecorte10' => 'required|integer|min:0',
            'monedacorte5' => 'required|integer|min:0',
            'monedacorte2' => 'required|integer|min:0',
            'monedacorte1' => 'required|integer|min:0',
            'monedacorte050' => 'required|integer|min:0',
            'monedacorte020' => 'required|integer|min:0',
            'monedacorte010' => 'required|integer|min:0',
            'montototal' => 'required|numeric|min:0',
        ]);

        $usuarioAutenticadoid = Auth::id();

        $arqueo = ArqueoCaja::where('usuarioarqueoid', $usuarioAutenticadoid)->first();

        if ($arqueo) {
            $arqueo->update([
                'billetecorte200' => $arqueo->billetecorte200 + $request->billetecorte200,
                'billetecorte100' => $arqueo->billetecorte100 + $request->billetecorte100,
                'billetecorte50' => $arqueo->billetecorte50 + $request->billetecorte50,
                'billetecorte20' => $arqueo->billetecorte20 + $request->billetecorte20,
                'billetecorte10' => $arqueo->billetecorte10 + $request->billetecorte10,
                'monedacorte5' => $arqueo->monedacorte5 + $request->monedacorte5,
                'monedacorte2' => $arqueo->monedacorte2 + $request->monedacorte2,
                'monedacorte1' => $arqueo->monedacorte1 + $request->monedacorte1,
                'monedacorte050' => $arqueo->monedacorte050 + $request->monedacorte050,
                'monedacorte020' => $arqueo->monedacorte020 + $request->monedacorte020,
                'monedacorte010' => $arqueo->monedacorte010 + $request->monedacorte010,
            ]);

            $consolidado = Consolidadocaja::where('usuarioconsolidadoid', $usuarioAutenticadoid)->first();

            if ($consolidado) {
                $consolidado->update([
                    'consolidadoefectivo' => $consolidado->consolidadoefectivo + number_format($request->montototal, 2, '.', ''),
                ]);
            }

            return redirect()->route('admin.caja.ingreso.index')->with('info', 'Registro guardado correctamente');
        } else {

            return redirect()->route('admin.caja.ingreso.index')->with('infoerror', 'ERROR AL GUARDAR EL ARQUEO');
        }
    }
    public function creditosupdatefecha(Request $request)
    {
        $fechas = $request->input('fechacredito'); // Recibe todas las fechas modificadas
    
        foreach ($fechas as $id => $nuevaFecha) {
            $credito = Credito::find($id);
            if ($credito) {
                $credito->fechacredito = $nuevaFecha;
                $credito->save();
            }
        }
    
        return redirect()->back()->with('info', 'Fechas de crédito actualizadas correctamente');
    }
//

// DOCUMENTACION REPALDO INGRESOS
    public function respaldodocumentacioningreso(Request $request)  
    {
        $userId = auth()->id();
        $rolUsuario = auth()->user()->getRoleNames()->first();
        $fecha = $request->input('fecha', today()->toDateString());
        $usuarios = Consolidadocaja::select('usuarioconsolidadoID', 'usuarioconsolidadoNombre')
                                    ->distinct()
                                    ->get();
        $usuarioSeleccionado = $request->input('usuario', $userId);
        $registros = CajaCentral::where('tipomovimiento', 'INGRESO')
                                ->whereDate('created_at', $fecha)
                                ->when($usuarioSeleccionado, function ($query, $usuarioSeleccionado) {
                                    return $query->where('usuarioRegistroID', $usuarioSeleccionado);
                                })
                                ->get();

        return view('admin.caja.ingreso.documentacion', compact('registros', 'rolUsuario', 'usuarios', 'fecha', 'usuarioSeleccionado'));
    }
    public function actualizarEstado(Request $request)
    {
        // Obtener los IDs seleccionados
        $ids = $request->input('registro_ids');

        // Verificar si hay IDs seleccionados
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No se seleccionaron registros para actualizar.');
        }

        // Actualiza el estado en la tabla cajacentral
        Cajacentral::whereIn('id', $ids)->update([
            'estadorevisioncierre' => 'DOCUMENTACION PENDIENTE',
            'documentorespaldo' => null,
            'docfactura' => null,
        ]);

        return redirect()->back()->with('info', 'Estado actualizado exitosamente.');
    }
    public function guardarRespaldo(Request $request)
    {
        $request->validate([
            'archivo' => '',
            'archivo2' => '',
            'archivo3' => '',
            'registro_ids' => 'required|array|min:1',
            'nrobancarizacion' => 'nullable|array',
        ]);

        $userId = auth()->id();

        $archivo_name = null;
            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $carpetaCliente = public_path("/documentacioncaja/ingresos/$userId");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }
                $archivo_name = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name);
            }
        
        $archivo_name2 = null;
            if ($request->hasFile('archivo2')) {
                $file = $request->file('archivo2');
                $carpetaCliente = public_path("/documentacioncaja/ingresos/$userId");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }
                $archivo_name2 = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name2);
            }

        $archivo_name3 = null;
            if ($request->hasFile('archivo3')) {
                $file = $request->file('archivo3');
                $carpetaCliente = public_path("/documentacioncaja/ingresos/$userId");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }
                $archivo_name3 = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name3);
            }

        $registros = CajaCentral::whereIn('id', $request->registro_ids)->get();

        foreach ($registros as $registro) {
            if ($registro->tipotransaccion === 'ATC') {
                $nuevoNroBancarizacion = $request->nrobancarizacion[$registro->id] ?? null;
                
                if (empty($registro->nrobancarizacionatc) && $nuevoNroBancarizacion) {
                    $registro->nrobancarizacionatc = $nuevoNroBancarizacion;
                    $registro->fechabancarizacionatc = now();
                    
                    $consolidado = Consolidadocaja::where('usuarioconsolidadoid', $userId)->first();
    
                    if ($consolidado) {
                        $consolidado->consolidadoatc += ($registro->montototal - $registro->descuentoatc);
                        $consolidado->save();
                    } else {
                        $consolidado = new Consolidadocaja();
                        $consolidado->usuarioconsolidadoid = $userId;
                        $consolidado->consolidadoatc = ($registro->montototal - $registro->descuentoatc);
                        $consolidado->save();
                    }
                }
            }
            
            /* $registro->estadorevisioncierre = 'RESPALDADO'; */
            if ($registro->estadorevisioncierre !== 'FINALIZADO') {
                $registro->estadorevisioncierre = 'RESPALDADO';
            }
            $registro->documentorespaldo = $archivo_name ?? $registro->documentorespaldo;
            $registro->docfactura = $archivo_name3 ?? $registro->docfactura;
            $registro->doccomprobante = $archivo_name2 ?? $registro->doccomprobante;
            $registro->save();
        }

        return redirect()->back()->with('info', 'Registro respaldado exitosamente.');
    }
//

// CIERRE DE CAJA INGRESO Y EGRESO
    public function cierrecajaingresos(Request $request)
    {
        $usuarioAutenticado = auth()->user();
        $userId = auth()->id();
        $rolusuario = auth()->user()->getRoleNames()->first();
        $todosFinalizados = CajaCentral::where('usuarioregistroid', $userId)
        ->where('estadorevisioncierre', 'FINALIZADO')
        ->whereDate('updated_at', today());

        $usuariosConsolidados = Consolidadocaja::select('usuarioconsolidadonombre')
            ->groupBy('usuarioconsolidadonombre')
            ->get();

        $usuarioBusqueda = $request->input('usuario_busqueda', null);

        /* $query = DB::table('cajacentral')
            ->select(
            'id', 
            'clientenombre', 
            'area', 
            'tipomovimiento', 
            'tipotransaccion', 
            'tipotransaccion2', 
            'subtotal', 
            'descuento', 
            'montototal', 
            'saldo', 
            'nrorecibo', 
            'usuarioregistronombre', 
            'documentorespaldo', 
            'estadorevisioncierre', 
            'descuentoatc', 
            'fechacierre', 
            'ciudadregistro', 
            'usuarioregistroid', 
            'proveedornombre', 
            'docfactura', 
            'doccomprobante', 
            'usuarioanulacion')
            ->where('tipomovimiento', 'INGRESO')
            ->where('usuarioregistroid', $userId)
            ->whereDate('created_at', today());

        if ($usuarioBusqueda) {
            $query->where('usuarioregistronombre', $usuarioBusqueda);
        }

        $registros = $query->get(); */
        $query = DB::table('cajacentral')
            ->leftJoin('detallerecibos', 'cajacentral.nrorecibo', '=', 'detallerecibos.reciboid')
            ->select(
                'cajacentral.id',
                'cajacentral.clientenombre',
                'cajacentral.area',
                'cajacentral.tipomovimiento',
                'cajacentral.tipotransaccion',
                'cajacentral.tipotransaccion2',
                'cajacentral.subtotal',
                'cajacentral.descuento',
                'cajacentral.montototal',
                'cajacentral.saldo',
                'cajacentral.nrorecibo',
                'cajacentral.usuarioregistronombre',
                'cajacentral.documentorespaldo',
                'cajacentral.estadorevisioncierre',
                'cajacentral.descuentoatc',
                'cajacentral.fechacierre',
                'cajacentral.ciudadregistro',
                'cajacentral.usuarioregistroid',
                'cajacentral.proveedornombre',
                'cajacentral.docfactura',
                'cajacentral.doccomprobante',
                'cajacentral.usuarioanulacion',
                DB::raw('GROUP_CONCAT(detallerecibos.detalle SEPARATOR ", ") as detalle')
            )
            ->where('cajacentral.tipomovimiento', 'INGRESO')
            ->where('cajacentral.usuarioregistroid', $userId)
            ->whereDate('cajacentral.created_at', today())
            ->groupBy(
                'cajacentral.id',
                'cajacentral.clientenombre',
                'cajacentral.area',
                'cajacentral.tipomovimiento',
                'cajacentral.tipotransaccion',
                'cajacentral.tipotransaccion2',
                'cajacentral.subtotal',
                'cajacentral.descuento',
                'cajacentral.montototal',
                'cajacentral.saldo',
                'cajacentral.nrorecibo',
                'cajacentral.usuarioregistronombre',
                'cajacentral.documentorespaldo',
                'cajacentral.estadorevisioncierre',
                'cajacentral.descuentoatc',
                'cajacentral.fechacierre',
                'cajacentral.ciudadregistro',
                'cajacentral.usuarioregistroid',
                'cajacentral.proveedornombre',
                'cajacentral.docfactura',
                'cajacentral.doccomprobante',
                'cajacentral.usuarioanulacion'
            );

        if ($usuarioBusqueda) {
            $query->where('cajacentral.usuarioregistronombre', $usuarioBusqueda);
        }

        $registros = $query->get();


        /* $query2 = DB::table('cajacentral')
            ->select('id', 
            'proveedornombre', 
            'area', 
            'tipomovimiento', 
            'tipotransaccion', 
            'tipotransaccion2', 
            'subtotal', 
            'descuento', 
            'montototal', 
            'saldo', 
            'nrorecibo', 
            'usuarioregistronombre', 
            'docrespaldoegreso', 
            'estadorevisioncierre', 
            'descuentoatc', 
            'fechacierre', 
            'ciudadregistro', 
            'usuarioregistroid', 
            'proveedornombre', 
            'docfactura', 
            'doccomprobante', 
            'usuarioanulacion')
            ->where('tipomovimiento', 'EGRESO')
            ->where('usuarioregistroid', $userId)
            ->whereDate('created_at', today());

        if ($usuarioBusqueda) {
            $query2->where('usuarioregistronombre', $usuarioBusqueda);
        }

        $registrosegreso = $query2->get(); */

        $query2 = DB::table('cajacentral')
            ->leftJoin('detallerecibos', 'cajacentral.nrorecibo', '=', 'detallerecibos.reciboid')
            ->select(
                'cajacentral.id',
                'cajacentral.proveedornombre',
                'cajacentral.area',
                'cajacentral.tipomovimiento',
                'cajacentral.tipotransaccion',
                'cajacentral.tipotransaccion2',
                'cajacentral.subtotal',
                'cajacentral.descuento',
                'cajacentral.montototal',
                'cajacentral.saldo',
                'cajacentral.nrorecibo',
                'cajacentral.usuarioregistronombre',
                'cajacentral.docrespaldoegreso',
                'cajacentral.estadorevisioncierre',
                'cajacentral.descuentoatc',
                'cajacentral.fechacierre',
                'cajacentral.ciudadregistro',
                'cajacentral.usuarioregistroid',
                'cajacentral.proveedornombre',
                'cajacentral.docfactura',
                'cajacentral.doccomprobante',
                'cajacentral.usuarioanulacion',
                DB::raw('GROUP_CONCAT(DISTINCT detallerecibos.detalle SEPARATOR ", ") as detalle')
            )
            ->where('cajacentral.tipomovimiento', 'EGRESO')
            ->where('cajacentral.usuarioregistroid', $userId)
            ->whereDate('cajacentral.created_at', today())
            ->groupBy(
                'cajacentral.id',
                'cajacentral.proveedornombre',
                'cajacentral.area',
                'cajacentral.tipomovimiento',
                'cajacentral.tipotransaccion',
                'cajacentral.tipotransaccion2',
                'cajacentral.subtotal',
                'cajacentral.descuento',
                'cajacentral.montototal',
                'cajacentral.saldo',
                'cajacentral.nrorecibo',
                'cajacentral.usuarioregistronombre',
                'cajacentral.docrespaldoegreso',
                'cajacentral.estadorevisioncierre',
                'cajacentral.descuentoatc',
                'cajacentral.fechacierre',
                'cajacentral.ciudadregistro',
                'cajacentral.usuarioregistroid',
                'cajacentral.proveedornombre',
                'cajacentral.docfactura',
                'cajacentral.doccomprobante',
                'cajacentral.usuarioanulacion'
            );

        if ($usuarioBusqueda) {
            $query2->where('cajacentral.usuarioregistronombre', $usuarioBusqueda);
        }

        $registrosegreso = $query2->get();


        $consolidados = Consolidadocaja::where('usuarioconsolidadonombre', $usuarioAutenticado->name)
            ->whereDate('updated_at', today())
            ->first();

        $tiposTransaccion = ['Efectivo', 'Cheque', 'ATC', 'Deposito Bancario', 'Transferencia Bancaria'];
        $montosCajaCentral = [];

        foreach ($tiposTransaccion as $tipo) {
            $montosCajaCentral[$tipo] = DB::table('cajacentral')
                ->where('usuarioregistronombre', $usuarioAutenticado->name)
                ->where('tipotransaccion', $tipo)
                ->whereDate('created_at', today())
                ->sum('montototal');
        }

        $cierrecajas = Cierrecaja::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();


        return view('admin.caja.ingreso.cierre', [
            'registros' => $registros,
            'registrosegreso' => $registrosegreso,
            'consolidados' => $consolidados,
            'usuarioBusqueda' => $usuarioBusqueda,
            'montosCajaCentral' => $montosCajaCentral,
            'tiposTransaccion' => $tiposTransaccion,
            'usuariosConsolidados' => $usuariosConsolidados,
            'todosFinalizados' => $todosFinalizados,
            'cierrecajas' => $cierrecajas,
            'rolusuario' => $rolusuario
        ]);
    }
    public function manejarCierreCaja(Request $request)
    {
        $rolusuario = auth()->user()->getRoleNames()->first();
        $usuarioAutenticado = auth()->user();
        $usuariosConsolidados = Consolidadocaja::select('usuarioconsolidadonombre')
            ->groupBy('usuarioconsolidadonombre')
            ->get();
        $usuarioBusqueda = $request->input('usuario_busqueda', $usuarioAutenticado->name);
        $tipotransaccion = $request->input('tipotransaccion');
        $estadorevisioncierre = $request->input('estadorevisioncierre');
        $fechacierre = $request->input('fechacierre');

        /* BUSQUEDA */
        /* $registros = DB::table('cajacentral')
            ->select('id', 'clientenombre', 'area', 'tipomovimiento', 'tipotransaccion', 'tipotransaccion2', 'subtotal', 'descuento', 'montototal', 'saldo', 'nrorecibo', 'usuarioregistronombre', 'documentorespaldo', 'estadorevisioncierre', 'fechacierre', 'descuentoatc', 'ciudadregistro', 'usuarioregistroid', 'proveedornombre', 'docfactura', 'doccomprobante', 'usuarioanulacion')
            ->where('tipomovimiento', 'INGRESO')
            ->where('usuarioregistronombre', $usuarioBusqueda)
            ->when($tipotransaccion, function ($query) use ($tipotransaccion) {
                return $query->where('tipotransaccion', 'like', '%' . $tipotransaccion . '%');
            })
            ->when($estadorevisioncierre, function ($query) use ($estadorevisioncierre) {
                return $query->where('estadorevisioncierre', 'like', '%' . $estadorevisioncierre . '%');
            })
            ->when($fechacierre, function ($query) use ($fechacierre) {
                return $query->whereDate('created_at', '=', $fechacierre);
            })
        ->get(); */
        $registros = DB::table('cajacentral')
            ->leftJoin('detallerecibos', 'cajacentral.nrorecibo', '=', 'detallerecibos.reciboid')
            ->select(
                'cajacentral.id',
                'cajacentral.clientenombre',
                'cajacentral.area',
                'cajacentral.tipomovimiento',
                'cajacentral.tipotransaccion',
                'cajacentral.tipotransaccion2',
                'cajacentral.subtotal',
                'cajacentral.descuento',
                'cajacentral.montototal',
                'cajacentral.saldo',
                'cajacentral.nrorecibo',
                'cajacentral.usuarioregistronombre',
                'cajacentral.documentorespaldo',
                'cajacentral.estadorevisioncierre',
                'cajacentral.fechacierre',
                'cajacentral.descuentoatc',
                'cajacentral.ciudadregistro',
                'cajacentral.usuarioregistroid',
                'cajacentral.proveedornombre',
                'cajacentral.docfactura',
                'cajacentral.doccomprobante',
                'cajacentral.usuarioanulacion',
                DB::raw('GROUP_CONCAT(detallerecibos.detalle SEPARATOR ", ") as detalle') // Aquí añadimos el detalle
            )
            ->where('cajacentral.tipomovimiento', 'INGRESO')
            ->where('cajacentral.usuarioregistronombre', $usuarioBusqueda)
            ->when($tipotransaccion, function ($query) use ($tipotransaccion) {
                return $query->where('cajacentral.tipotransaccion', 'like', '%' . $tipotransaccion . '%');
            })
            ->when($estadorevisioncierre, function ($query) use ($estadorevisioncierre) {
                return $query->where('cajacentral.estadorevisioncierre', 'like', '%' . $estadorevisioncierre . '%');
            })
            ->when($fechacierre, function ($query) use ($fechacierre) {
                return $query->whereDate('cajacentral.created_at', '=', $fechacierre);
            })
            ->groupBy(
                'cajacentral.id',
                'cajacentral.clientenombre',
                'cajacentral.area',
                'cajacentral.tipomovimiento',
                'cajacentral.tipotransaccion',
                'cajacentral.tipotransaccion2',
                'cajacentral.subtotal',
                'cajacentral.descuento',
                'cajacentral.montototal',
                'cajacentral.saldo',
                'cajacentral.nrorecibo',
                'cajacentral.usuarioregistronombre',
                'cajacentral.documentorespaldo',
                'cajacentral.estadorevisioncierre',
                'cajacentral.fechacierre',
                'cajacentral.descuentoatc',
                'cajacentral.ciudadregistro',
                'cajacentral.usuarioregistroid',
                'cajacentral.proveedornombre',
                'cajacentral.docfactura',
                'cajacentral.doccomprobante',
                'cajacentral.usuarioanulacion'
            )
            ->get();

        
        /* $registrosegreso = DB::table('cajacentral')
            ->select('id', 'proveedornombre', 'area', 'tipomovimiento', 'tipotransaccion', 'tipotransaccion2', 'subtotal', 'descuento', 'montototal', 'saldo', 'nrorecibo', 'usuarioregistronombre', 'docrespaldoegreso', 'estadorevisioncierre', 'fechacierre', 'descuentoatc', 'ciudadregistro', 'usuarioregistroid', 'proveedornombre', 'docfactura', 'doccomprobante', 'usuarioanulacion')
            ->where('tipomovimiento', 'EGRESO')
            ->where('usuarioregistronombre', $usuarioBusqueda)
            ->when($tipotransaccion, function ($query) use ($tipotransaccion) {
                return $query->where('tipotransaccion', 'like', '%' . $tipotransaccion . '%');
            })
            ->when($estadorevisioncierre, function ($query) use ($estadorevisioncierre) {
                return $query->where('estadorevisioncierre', 'like', '%' . $estadorevisioncierre . '%');
            })
            ->when($fechacierre, function ($query) use ($fechacierre) {
                return $query->whereDate('created_at', '=', $fechacierre);
            })
        ->get(); */
        $registrosegreso = DB::table('cajacentral')
        ->leftJoin('detallerecibos', 'cajacentral.nrorecibo', '=', 'detallerecibos.reciboid')
        ->select(
            'cajacentral.id',
            'cajacentral.clientenombre',
            'cajacentral.area',
            'cajacentral.tipomovimiento',
            'cajacentral.tipotransaccion',
            'cajacentral.tipotransaccion2',
            'cajacentral.subtotal',
            'cajacentral.descuento',
            'cajacentral.montototal',
            'cajacentral.saldo',
            'cajacentral.nrorecibo',
            'cajacentral.usuarioregistronombre',
            'cajacentral.docrespaldoegreso',
            'cajacentral.estadorevisioncierre',
            'cajacentral.fechacierre',
            'cajacentral.descuentoatc',
            'cajacentral.ciudadregistro',
            'cajacentral.usuarioregistroid',
            'cajacentral.proveedornombre',
            'cajacentral.docfactura',
            'cajacentral.doccomprobante',
            'cajacentral.usuarioanulacion',
            DB::raw('GROUP_CONCAT(detallerecibos.detalle SEPARATOR ", ") as detalle') // Aquí añadimos el detalle
        )
        ->where('cajacentral.tipomovimiento', 'EGRESO')
        ->where('cajacentral.usuarioregistronombre', $usuarioBusqueda)
        ->when($tipotransaccion, function ($query) use ($tipotransaccion) {
            return $query->where('cajacentral.tipotransaccion', 'like', '%' . $tipotransaccion . '%');
        })
        ->when($estadorevisioncierre, function ($query) use ($estadorevisioncierre) {
            return $query->where('cajacentral.estadorevisioncierre', 'like', '%' . $estadorevisioncierre . '%');
        })
        ->when($fechacierre, function ($query) use ($fechacierre) {
            return $query->whereDate('cajacentral.created_at', '=', $fechacierre);
        })
        ->groupBy(
            'cajacentral.id',
            'cajacentral.clientenombre',
            'cajacentral.area',
            'cajacentral.tipomovimiento',
            'cajacentral.tipotransaccion',
            'cajacentral.tipotransaccion2',
            'cajacentral.subtotal',
            'cajacentral.descuento',
            'cajacentral.montototal',
            'cajacentral.saldo',
            'cajacentral.nrorecibo',
            'cajacentral.usuarioregistronombre',
            'cajacentral.docrespaldoegreso',
            'cajacentral.estadorevisioncierre',
            'cajacentral.fechacierre',
            'cajacentral.descuentoatc',
            'cajacentral.ciudadregistro',
            'cajacentral.usuarioregistroid',
            'cajacentral.proveedornombre',
            'cajacentral.docfactura',
            'cajacentral.doccomprobante',
            'cajacentral.usuarioanulacion'
        )
        ->get();



        // Datos consolidados para el usuario autenticado
        $consolidados = Consolidadocaja::where('usuarioconsolidadonombre', $usuarioAutenticado->name)
            ->whereDate('updated_at', today())
            ->first();

        // Tipos de transacción para caja central
        $tiposTransaccion = ['Efectivo', 'Cheque', 'ATC', 'Deposito Bancario', 'Transferencia Bancaria'];
        $montosCajaCentral = [];

        // Sumamos los montos por cada tipo de transacción
        foreach ($tiposTransaccion as $tipo) {
            $montosCajaCentral[$tipo] = DB::table('cajacentral')
                ->where('usuarioregistronombre', $usuarioAutenticado->name)
                ->where('tipotransaccion', $tipo)
                ->whereDate('updated_at', today())
                ->sum('montototal');
        }

        // Si se presiona "Aprobar Cierre" o "Cerrar Caja"
        if ($request->isMethod('post') && $request->has('accion')) {
            $request->validate([
                'registro_ids' => 'required|array',
            ]);

            $username = auth()->user()->name;

            if ($request->accion == 'aprobar') {
                DB::table('cajacentral')
                    ->whereIn('id', $request->registro_ids)
                    ->update([
                        'estadorevisioncierre' => 'CIERRE APROBADO',
                        'usuariorevisioncierre' => $username
                    ]);

                return back()->with('info', 'Cierre aprobado exitosamente.');
            }
            elseif ($request->accion == 'cerrar') {
                $fechaCierre = now();
                $username = auth()->user()->name;
                $userId = auth()->id();

                DB::table('cajacentral')
                    ->whereIn('id', $request->registro_ids)
                    ->update([
                        'estadorevisioncierre' => 'FINALIZADO',
                        'fechacierre' => $fechaCierre,
                        'usuariocierrecaja' => $username
                    ]);

                $consolidado = Consolidadocaja::where('usuarioconsolidadonombre', $username)->first();
            
                if ($consolidado) {
                    DB::table('cierrecaja')->insert([
                        'usuariocierrenombre' => $username, 
                        'usuariocierreid' => $userId,
                        'usuariocierrenombre' => $username, 
                        'cierreefectivo' => $consolidado->consolidadoefectivo,
                        'cierredeposito' => $consolidado->consolidadodeposito,
                        'cierretransferencia' => $consolidado->consolidadotransferencia,
                        'cierrecheque' => $consolidado->consolidadocheque,
                        'cierreatc' => $consolidado->consolidadoatc,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
                return back()->with('info', 'Caja cerrada y nuevo registro en Cierrecaja creado exitosamente. Los datos consolidados se han restablecido a 0.00.');
            }
        }

        $cierrecajas = Cierrecaja::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();


        return view('admin.caja.ingreso.cierre', [
            'registros' => $registros,
            'registrosegreso' => $registrosegreso,
            'consolidados' => $consolidados,
            'montosCajaCentral' => $montosCajaCentral,
            'tiposTransaccion' => $tiposTransaccion,
            'usuariosConsolidados' => $usuariosConsolidados,
            'usuarioBusqueda' => $usuarioBusqueda,
            'cierrecajas' => $cierrecajas,
            'rolusuario' => $rolusuario
        ]);
    }
//

// CUENTAS POR COBRAR
    public function listacuentascobrar(Cliente $cliente, ClienteAuditoria $clienteauditoria, ClienteComun $clientecomun, Request $request)
    {
        $fechas = Bateriasubcliente::pluck('fechabateria')->unique()->sort()->toArray();

        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;

        $userRole = auth()->user()->getRoleNames()->first(); 
        
        $query = Bateriasubcliente::with(['estadoprogramacionsubcliente', 'documentacionsubcliente', 'programacionsubcliente','informesfinales','pagoservicio','pagoservicioinformefinal'])
            ->whereNotNull('clienteitaid')
            /* ->whereNotNull('proveedorasignado') */
            /* ->where('preciocompra', '!=', NULL)
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)  */
            ->where('servicio', '<>', 'AJENO') 
            /* ->where('pagoservicio', '=', 'INTERNO') */
            ->orderBy('clienteitanombre');
        
        $query2 = Bateriasubcliente::with(['estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria', 'programacionsubclienteauditoria','informesfinalesauditoria','pagoservicio','pagoservicioinformefinal'])
            ->whereNotNull('clienteauditoriaid')
            /* ->whereNotNull('proveedorasignado') */
            /* ->where('preciocompra', '!=', NULL)
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)  */
            ->where('servicio', '<>', 'AJENO') 
            /* ->where('servicio', '<>', 'EXTERNO') */
            /* ->where('pagoservicio', '=', 'INTERNO') */
            ->orderBy('clienteauditorianombre');

        $query3 = Bateriasubcliente::with(['estadoprogramacionsubclientecomun', 'documentacionsubclientecomun', 'programacionsubclientecomun','informesfinalescomun'])
            ->whereNotNull('clientecomunnombre')
            /* ->whereNotNull('proveedorasignado') */
            /* ->where('preciocompra', '!=', NULL)
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)  */
            ->where('servicio', '<>', 'AJENO') 
            /* ->where('servicio', '<>', 'EXTERNO') */
            /* ->where('pagoservicio', '=', 'INTERNO') */
            ->orderBy('clientecomunnombre');

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('clienteita', function ($q) use ($request) {
                $q->where('clienteitanombre', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query2->whereHas('clienteauditoria', function ($q) use ($request) {
                $q->where('clienteauditorianombre', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query3->whereHas('clientecomun', function ($q) use ($request) {
                $q->where('clientecomunnombre', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        $bateriaclientesita = $query->get();
        $grouped = $bateriaclientesita->groupBy(function($item) {
            return $item->clienteitanombre . '|' . $item->fechabateria;
        });

        $result = [];
        foreach ($grouped as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $fechabateria = explode('|', $key)[1];

            $clienteitaid = $items->first()->clienteitaid;

            $tramites = TramiteSubCliente::where('clienteitaid', $clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->get();
            $tramiteNombre = $tramites->isEmpty() ? ['SIN SERVICIO'] : $tramites->pluck('tramite')->toArray();

            $usuarioRegistro = Cliente::where('id', $items->first()->clienteitaid)
                ->first();

            $usuarioregistro = $usuarioRegistro ? $usuarioRegistro->sucursal : null;
            $clienteitaid = $items->first()->clienteitaid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {
                $estadoProgramacion = $item->estadoprogramacionsubcliente
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                ->first();
                
                $programacionasignada = $item->programacionsubcliente
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                ->first();
                
                $informesubido = $item->documentacionsubcliente
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accion', $item->accionnombre)
                ->first();
                
                $informefinalsubido = $item->informesfinales
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accion', 'INFORME FINAL')
                ->first();

                $resultadopagoinformefinal = $item->pagoservicioinformefinal
                    ->where('provinfofinalid', $item->provinfofinalid)
                    ->where('tipomovimiento', 'INGRESO')
                ->first();

                    $resultadopago = $item->programacionsubcliente
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();
                    
                    if ($resultadopago) {
                        $programacionId = $resultadopago->id;
                        $programacionfechabateria = $resultadopago->fechabateria;
                        $programacioncliente = $resultadopago->clienteitaid;
                        $programacionaccion = $resultadopago->accionnombre;

                        $detallerecibo = Detallerecibo::where('programacionid', $programacionId)
                                                      ->where('tipomovimiento', 'INGRESO')
                                                      ->first();

                        $resultadopagobateria = Bateriasubcliente::where('fechabateria', $programacionfechabateria)
                                                      ->where('accionnombre', $programacionaccion)
                                                      ->where('clienteitaid', $programacioncliente)
                                                      ->first();
                    
                        if ($detallerecibo) {
                            $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                        } else {
                            $pagoservicioinforme = $resultadopagobateria->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : null;
                        }
                    } else {
                        $pagoservicioinforme = null;
                    }

                $fechaAtencion = $estadoProgramacion ? $estadoProgramacion->fechaatencionprogramacion : null;
                $fechaprogramacion = $programacionasignada ? $programacionasignada->fechaasignada : null;
                $informedocumentacion = $informesubido ? $informesubido->created_at->toDateString() : null;
                $informedocumentacionfinal = $informefinalsubido ? $informefinalsubido->created_at->toDateString() : null;
                $pagoservicioinformefinal = $resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null;

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'cantidadcuotas' => $item->cantidadcuotas,
                    'precio' => $item->precio,
                    'pagoservicio' => $item->pagoservicio,
                    'preciocompra' => $item->preciocompra,
                    'proveedorasignado' => $item->proveedorasignado,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechaprogramacion' => $fechaprogramacion,
                    'informedocumentacion' => $informedocumentacion,
                    'informedocumentacionfinal' => $informedocumentacionfinal,
                    'clienteitanombre' => $item->clienteitanombre,
                    'fechabateria' => $item->fechabateria,
                    'pagoservicioinforme' => $pagoservicioinforme,
                    'pagoservicioinformefinal' => $pagoservicioinformefinal,
                ];
            }
            $result[] = [
                'clienteitaid' => $clienteitaid,
                'clienteitanombre' => $clienteNombre,
                'fechabateria' => $fechabateria,
                'tramite' => $tramiteNombre,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'usuarioregistro' => $usuarioregistro,
                'pagoservicioinforme' => $pagoservicioinforme,
            ];
        }

        
        $bateriaclientesauditoria = $query2->get();
        $grouped2 = $bateriaclientesauditoria->groupBy(function($item) {
            return $item->clienteauditorianombre . '|' . $item->fechabateria;
        });
        $result2 = [];
        foreach ($grouped2 as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $fechabateria = explode('|', $key)[1];

            $clienteauditoriaid = $items->first()->clienteauditoriaid;

            $tramites = TramiteSubCliente::where('clienteauditoriaid', $clienteauditoriaid)
                ->where('fechabateria', $fechabateria)
                ->get();
            $tramiteNombre = $tramites->isEmpty() ? ['SIN SERVICIO'] : $tramites->pluck('tramite')->toArray();

            $usuarioRegistro = ClienteAuditoria::where('id', $items->first()->clienteauditoriaid)
                ->first();

            $usuarioregistro = $usuarioRegistro ? $usuarioRegistro->sucursal : null;
            $clienteauditoriaid = $items->first()->clienteauditoriaid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {
                $estadoProgramacion = $item->estadoprogramacionsubclienteauditoria
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();
                
                $programacionasignada = $item->programacionsubclienteauditoria
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();
                
                $informesubido = $item->documentacionsubclienteauditoria
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accion', $item->accionnombre)
                    ->first();
                
                $informefinalsubido = $item->informesfinalesauditoria
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accion', 'INFORME FINAL')
                    ->first();

                $resultadopagoinformefinal = $item->pagoservicioinformefinal
                    ->where('provinfofinalid', $item->provinfofinalid)
                    ->where('tipomovimiento', 'INGRESO')
                ->first();

                $resultadopago = $item->programacionsubclienteauditoria
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();
                    
                    if ($resultadopago) {
                        $programacionId = $resultadopago->id;
                        $programacionfechabateria = $resultadopago->fechabateria;
                        $programacioncliente = $resultadopago->clienteauditoriaid;
                        $programacionaccion = $resultadopago->accionnombre;

                        $detallerecibo = Detallerecibo::where('programacionid', $programacionId)
                                                      ->where('tipomovimiento', 'INGRESO')
                                                      ->first();

                        $resultadopagobateria = Bateriasubcliente::where('fechabateria', $programacionfechabateria)
                                                      ->where('accionnombre', $programacionaccion)
                                                      ->where('clienteauditoriaid', $programacioncliente)
                                                      ->first();
                    
                        if ($detallerecibo) {
                            $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                        } else {
                            $pagoservicioinforme = $resultadopagobateria->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : null;
                        }
                    } else {
                        $pagoservicioinforme = null;
                    }

                $fechaAtencion = $estadoProgramacion ? $estadoProgramacion->fechaatencionprogramacion : null;
                $fechaprogramacion = $programacionasignada ? $programacionasignada->fechaasignada : null;
                $informedocumentacion = $informesubido ? $informesubido->created_at->toDateString() : null;
                $informedocumentacionfinal = $informefinalsubido ? $informefinalsubido->created_at->toDateString() : null;
                $pagoservicioinformefinal = $resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null;

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'cantidadcuotas' => $item->cantidadcuotas,
                    'pagoservicio' => $item->pagoservicio,
                    'preciocompra' => $item->preciocompra,
                    'proveedorasignado' => $item->proveedorasignado,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechaprogramacion' => $fechaprogramacion,
                    'informedocumentacion' => $informedocumentacion,
                    'informedocumentacionfinal' => $informedocumentacionfinal,
                    'clienteauditorianombre' => $item->clienteauditorianombre,
                    'fechabateria' => $item->fechabateria,
                    'pagoservicioinforme' => $pagoservicioinforme,
                    'pagoservicioinformefinal' => $pagoservicioinformefinal,
                ];
            }
            $result2[] = [
                'clienteauditoriaid' => $clienteauditoriaid,
                'clienteauditorianombre' => $clienteNombre,
                'fechabateria' => $fechabateria,
                'tramite' => $tramiteNombre,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'usuarioregistro' => $usuarioregistro,
                'pagoservicioinforme' => $pagoservicioinforme,
            ];
        }

        
        $bateriaclientescomun = $query3->get();
        $grouped3 = $bateriaclientescomun->groupBy(function($item) {
            return $item->clientecomunnombre . '|' . $item->fechabateria;
        });
        $result3 = [];
        foreach ($grouped3 as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $fechabateria = explode('|', $key)[1];

            $clientecomunid = $items->first()->clientecomunid;

            $tramites = TramiteSubCliente::where('clientecomunid', $clientecomunid)
                ->where('fechabateria', $fechabateria)
                ->get();
            $tramiteNombre = $tramites->isEmpty() ? ['SIN SERVICIO'] : $tramites->pluck('tramite')->toArray();

            $usuarioRegistro = ClienteComun::where('id', $items->first()->clientecomunid)
                ->first();

            $usuarioregistro = $usuarioRegistro ? $usuarioRegistro->sucursal : null;
            $clientecomunid = $items->first()->clientecomunid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {
                $estadoProgramacion = $item->estadoprogramacionsubclientecomun
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();
                
                $programacionasignada = $item->programacionsubclientecomun
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();
                
                $informesubido = $item->documentacionsubclientecomun
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accion', $item->accionnombre)
                    ->first();

                    $resultadopagoinformefinal = $item->pagoservicioinformefinal
                    ->where('provinfofinalid', $item->provinfofinalid)
                    ->where('tipomovimiento', 'INGRESO')
                ->first();

                $resultadopago = $item->programacionsubclientecomun
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();
                    
                    if ($resultadopago) {
                        $programacionId = $resultadopago->id;
                        $programacionfechabateria = $resultadopago->fechabateria;
                        $programacioncliente = $resultadopago->clientecomunid;
                        $programacionaccion = $resultadopago->accionnombre;

                        $detallerecibo = Detallerecibo::where('programacionid', $programacionId)
                                                      ->where('tipomovimiento', 'INGRESO')
                                                      ->first();

                        $resultadopagobateria = Bateriasubcliente::where('fechabateria', $programacionfechabateria)
                                                      ->where('accionnombre', $programacionaccion)
                                                      ->where('clientecomunid', $programacioncliente)
                                                      ->first();
                    
                        if ($detallerecibo) {
                            $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                        } else {
                            //$pagoservicioinforme = $resultadopagobateria->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : null;
                            $pagoservicioinforme = $resultadopagobateria?->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : null;
                        }
                    } else {
                        $pagoservicioinforme = null;
                    }

                $pagoservicioinformefinal = $resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null;

                $fechaAtencion = $estadoProgramacion ? $estadoProgramacion->fechaatencionprogramacion : null;
                $fechaprogramacion = $programacionasignada ? $programacionasignada->fechaasignada : null;
                $informedocumentacion = $informesubido ? $informesubido->created_at->toDateString() : null;
                $informedocumentacionfinal = $informefinalsubido ? $informefinalsubido->created_at->toDateString() : null;
                $pagoingreso = $informefinalsubido ? $informefinalsubido->estado->toDateString() : null;

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'pagoservicio' => $item->pagoservicio,
                    'cantidadcuotas' => $item->cantidadcuotas,
                    'preciocompra' => $item->preciocompra,
                    'proveedorasignado' => $item->proveedorasignado,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechaprogramacion' => $fechaprogramacion,
                    'informedocumentacion' => $informedocumentacion,
                    'informedocumentacionfinal' => $informedocumentacionfinal,
                    'pagoingreso' => $pagoingreso,
                    'clientecomunnombre' => $item->clientecomunnombre,
                    'fechabateria' => $item->fechabateria,
                    'pagoservicioinforme' => $pagoservicioinforme,
                    'pagoservicioinformefinal' => $pagoservicioinformefinal,
                ];
            }
            $result3[] = [
                'clientecomunid' => $clientecomunid,
                'clientecomunnombre' => $clienteNombre,
                'fechabateria' => $fechabateria,
                'tramite' => $tramiteNombre,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'usuarioregistro' => $usuarioregistro,
                'pagoservicioinforme' => $pagoservicioinforme,
            ];
        }

        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');
        $user = auth()->user()->name;

        $records = DB::table('bateriasubclientes')
            ->selectRaw("COALESCE(fechacredito, fechabateria) as fechabateria,  
                        SUM(CASE WHEN precio IS NULL THEN 0 ELSE precio END) as total_ingresos")
            ->where('pagoservicio', 'INTERNO')
            ->whereNotNull('preciocompra')
            ->whereYear(DB::raw('COALESCE(fechacredito, fechabateria)'), $year)
            ->whereMonth(DB::raw('COALESCE(fechacredito, fechabateria)'), $month)
            ->whereNull('deleted_at')
            ->whereNotIn('id', function($query) {
                $query->select('bateriaid')
                    ->from('programacionsubclientes')
                    ->whereNotNull('bateriaid');
            })
            ->groupBy(DB::raw('COALESCE(fechacredito, fechabateria)'))
            ->get();

        if ($request->ajax()) {
            return response()->json($records);
        }

        $query4 = Bateriasubcliente::with(['estadoprogramacionsubcliente', 'documentacionsubcliente', 'programacionsubcliente','informesfinales','pagoservicio','pagoservicioinformefinal','provinfofinal',
        'estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria', 'programacionsubclienteauditoria','informesfinalesauditoria','provinfofinalauditoria',
        'estadoprogramacionsubclientecomun', 'documentacionsubclientecomun', 'programacionsubclientecomun','informesfinalescomun','provinfofinalcomun'])
            ->whereNotNull('proveedorasignado')
            ->where('preciocompra', '!=', NULL)
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)
            ->where('pagoservicio', '=', 'EXTERNO')
            ->where('proveedorasignado', '<>', 'PROVEEDOR AJENO') 
            ->orderBy('proveedorasignado');

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query4->whereHas('proveedorasignado', function ($q) use ($request) {
                $q->where('proveedorasignado', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        $bateriaproveedores = $query4->get();
        $grouped = $bateriaproveedores->groupBy(function($item) {
            return $item->proveedorasignado;
        });

        $result4 = [];
        foreach ($grouped as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {

                    $estadoProgramacion = collect([
                            $item->estadoprogramacionsubcliente, 
                            $item->estadoprogramacionsubclienteauditoria, 
                            $item->estadoprogramacionsubclientecomun
                        ])->filter();
                        
                        $resultadoestado = $estadoProgramacion
                            ->flatMap(function ($estadoprogramacion) { 
                                return $estadoprogramacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();  

                    $programaciones = collect([
                            $item->programacionsubcliente, 
                            $item->programacionsubclienteauditoria, 
                            $item->programacionsubclientecomun
                        ])->filter();
                    
                        $resultadoprog = $programaciones
                            ->flatMap(function ($programacion) { 
                                return $programacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();                    

                    $informesubido = collect([
                            $item->documentacionsubcliente, 
                            $item->documentacionsubclienteauditoria, 
                            $item->documentacionsubclientecomun
                        ])->filter();
                        
                        $resultadoinforme = $informesubido
                            ->flatMap(function ($informe) { 
                                return $informe;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accion', $item->accionnombre)
                    ->first();           

                    $informefinalsubido = collect([
                            $item->informesfinales, 
                            $item->informesfinalesauditoria, 
                            $item->informesfinalescomun
                        ])->filter();
                        
                        $resultadoinformefinal = $informefinalsubido
                            ->flatMap(function ($informefinal) { 
                                return $informefinal;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $provinformes = collect([
                            $item->provinfofinal, 
                            $item->provinfofinalauditoria, 
                            $item->provinfofinalcomun
                        ])->filter();
                        
                        $resultadoprovinformes = $provinformes
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $resultadopagoinformefinal = $item->pagoservicioinformefinal()
                        ->where('provinfofinalid', $item->provinfofinalid)
                        ->where('tipomovimiento', 'INGRESO')
                    ->first();

                    $pagobateria = collect([
                        $item->programacionsubcliente,
                        $item->programacionsubclienteauditoria,
                        $item->programacionsubclientecomun
                    ])->filter();
                    
                    $resultadopago = $pagobateria
                        ->flatMap(fn($pago) => $pago)
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();
                    
                    if ($resultadopago) {
                        $programacionId = $resultadopago->id;
                        $detallerecibo = Detallerecibo::where('programacionid', $programacionId)
                                                      ->where('tipomovimiento', 'INGRESO')
                                                      ->first();
                    
                        if ($detallerecibo) {
                            $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                        } else {
                            $pagoservicioinforme = $resultadopago->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : null;
                        }
                    } else {
                        $pagoservicioinforme = null;
                    }

                $fechaAtencion = $resultadoestado ? $resultadoestado->fechaatencionprogramacion : null;
                $fechaprogramacion = $resultadoprog ? $resultadoprog->fechaasignada : null;
                $idprogramacion = $resultadoprog ? $resultadoprog->id : null;
                $nrofacturaprog = $resultadoprog ? $resultadoprog->nrofactura : null;
                $informedocumentacion = $resultadoinforme ? $resultadoinforme->created_at->toDateString() : null;
                $informedocumentacionfinal = $resultadoinformefinal ? $resultadoinformefinal->created_at->toDateString() : null;
                $pagoservicioinformefinal = in_array($item->id, [3173, 3178, 3187, 3043]) 
                ? 'PROCESADO' 
                : ($resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null);
                $nrofacturainformefinal = $resultadoprovinformes ? $resultadoprovinformes->nrofactura : null;

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'pagoservicio' => $item->pagoservicio,
                    'preciocompra' => $item->preciocompra,
                    'clienteitaid' => $item->clienteitaid,
                    'clienteitanombre' => $item->clienteitanombre,
                    'clienteauditoriaid' => $item->clienteauditoriaid,
                    'clienteauditorianombre' => $item->clienteauditorianombre,
                    'clientecomunid' => $item->clientecomunid,
                    'clientecomunnombre' => $item->clientecomunnombre,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechaprogramacion' => $fechaprogramacion,
                    'informedocumentacion' => $informedocumentacion,
                    'informedocumentacionfinal' => $informedocumentacionfinal,
                    'pagoservicioinforme' => $pagoservicioinforme,
                    'pagoservicioinformefinal' => $pagoservicioinformefinal,
                    'idprogramacion' => $idprogramacion,
                    'fechabateria' => $item->fechabateria,
                    'provinfofinalid' => $item->provinfofinalid,
                    'nrofacturaprog' => $nrofacturaprog,
                    'nrofacturainformefinal' => $nrofacturainformefinal,
                ];
            }
            $result4[] = [
                'proveedorasignado' => $item->proveedorasignado,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'fechabateria' => $item->fechabateria,
            ];
        }

        $cuentaspagar = CuentasCobrar::all();  

        return view('admin.caja.cuentascobrar.listacuentascobrar', compact('cuentaspagar','year', 'month', 'records', 'usuarioAutenticado','result', 'cliente', 'fechas','result2', 'clienteauditoria','result3', 'clientecomun','result4'));
    }
    public function buscarlistacuentascobrar(Cliente $cliente, ClienteAuditoria $clienteauditoria, ClienteComun $clientecomun, Request $request)
    {
        return $this->listacuentascobrar($cliente, $clienteauditoria,$clientecomun, $request);
    }
    public function cobrarhoy(Request $request)
    {
        $fechaActual = now()->toDateString();

        /* PAGOS PENDIENTES INTERNOS */
        $pagosprogramacionesita = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->where('servicio', 'INTERNO')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('pagoatencion')
                        ->orWhere('pagoatencion', '');
                });
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.programacionid', 'programacionsubclientes.id');
            })
            ->join('clientes', 'programacionsubclientes.clienteitaid', '=', 'clientes.id')
            /* ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clientes.sucursal', '=', 'bateriaproveedores.sucursal');
            }) */
            ->leftJoin('creditos', function ($join) {
                $join->on('programacionsubclientes.bateriaid', '=', 'creditos.bateriaid');
            })
            /* ->where('bateriaproveedores.servicio', 'INTERNO') */
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                /* 'bateriaproveedores.servicio', */
                'clientes.sucursal as cliente_sucursal',
                DB::raw('CASE WHEN creditos.bateriaid IS NOT NULL THEN "SI" ELSE "NO" END AS tiene_credito')
            )
        ->get();

        $pagosprogramacionescomun = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clientecomunid')
            ->where('servicio', 'INTERNO')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('pagoatencion')
                        ->orWhere('pagoatencion', '');
                });
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.programacionid', 'programacionsubclientes.id');
            })
            ->join('clientescomunes', 'programacionsubclientes.clientecomunid', '=', 'clientescomunes.id')
            /* ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clientescomunes.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->where('bateriaproveedores.servicio', 'INTERNO') */
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                /* 'bateriaproveedores.servicio', */
                'clientescomunes.sucursal as cliente_sucursal'
            )
        ->get();

        $pagosprogramacionesauditoria = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->where('servicio', 'INTERNO')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('pagoatencion')
                        ->orWhere('pagoatencion', '');
                });
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.programacionid', 'programacionsubclientes.id');
            })
            ->join('clienteauditorias', 'programacionsubclientes.clienteauditoriaid', '=', 'clienteauditorias.id')
            /* ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clienteauditorias.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->where('bateriaproveedores.servicio', 'INTERNO') */
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                /* 'bateriaproveedores.servicio', */
                'clienteauditorias.sucursal as cliente_sucursal'
            )
        ->get();


        /* PAGOS PENDIENTES EXTERNOS */
        $pagosexternosprogramacionesita = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->where('servicio', 'EXTERNO')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('pagoatencion')
                        ->orWhere('pagoatencion', '');
                });
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.programacionid', 'programacionsubclientes.id');
            })
            ->join('clientes', 'programacionsubclientes.clienteitaid', '=', 'clientes.id')
            /* ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clientes.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->where('bateriaproveedores.servicio', 'EXTERNO') */
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                /* 'bateriaproveedores.servicio', */
                'clientes.sucursal as cliente_sucursal'
            )
        ->get();

        $pagosexternosprogramacionescomun = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clientecomunid')
            ->where('servicio', 'EXTERNO')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('pagoatencion')
                        ->orWhere('pagoatencion', '');
                });
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.programacionid', 'programacionsubclientes.id');
            })
            ->join('clientescomunes', 'programacionsubclientes.clientecomunid', '=', 'clientescomunes.id')
            /* ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clientescomunes.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->where('bateriaproveedores.servicio', 'EXTERNO') */
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                /* 'bateriaproveedores.servicio', */
                'clientescomunes.sucursal as cliente_sucursal'
            )
        ->get();

        $pagosexternosprogramacionesauditoria = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->where('servicio', 'EXTERNO')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('pagoatencion')
                        ->orWhere('pagoatencion', '');
                });
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.programacionid', 'programacionsubclientes.id');
            })
            ->join('clienteauditorias', 'programacionsubclientes.clienteauditoriaid', '=', 'clienteauditorias.id')
            /* ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clienteauditorias.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->where('bateriaproveedores.servicio', 'EXTERNO') */
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                /* 'bateriaproveedores.servicio', */
                'clienteauditorias.sucursal as cliente_sucursal'
            )
        ->get();


        /* PAGOS PROCESADOS */
        $pagadosprogramacionesita = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clienteitaid')
            /* ->where(function ($query) {
                $query->where(function ($query) {
                    $query->Where('pagoatencion', 'PAGO PROCESADO');
                });
            }) */
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.programacionid', 'programacionsubclientes.id')
                    ->where('detallerecibos.estado', 'PAGO PROCESADO');
            })

            ->join('clientes', 'programacionsubclientes.clienteitaid', '=', 'clientes.id')
            /* ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clientes.sucursal', '=', 'bateriaproveedores.sucursal');
            }) */
            ->select(
                'programacionsubclientes.*', 
                /* 'bateriaproveedores.servicio', */
                'clientes.sucursal as cliente_sucursal'
            )
        ->get();
        
        $pagadosprogramacionescomun = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clientecomunid')
            /* ->where(function ($query) {
                $query->where(function ($query) {
                    $query->Where('pagoatencion', 'PAGO PROCESADO');
                });
            }) */
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.programacionid', 'programacionsubclientes.id')
                    ->where('detallerecibos.estado', 'PAGO PROCESADO');
            })
            ->join('clientescomunes', 'programacionsubclientes.clientecomunid', '=', 'clientescomunes.id')
            /* ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clientescomunes.sucursal', '=', 'bateriaproveedores.sucursal');
            }) */
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                /* 'bateriaproveedores.servicio', */
                'clientescomunes.sucursal as cliente_sucursal'
            )
        ->get();

        $pagadosprogramacionesauditoria = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            /* ->where(function ($query) {
                $query->where(function ($query) {
                    $query->Where('pagoatencion', 'PAGO PROCESADO');
                });
            }) */
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.programacionid', 'programacionsubclientes.id')
                    ->where('detallerecibos.estado', 'PAGO PROCESADO');
            })
            ->join('clienteauditorias', 'programacionsubclientes.clienteauditoriaid', '=', 'clienteauditorias.id')
            /* ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clienteauditorias.sucursal', '=', 'bateriaproveedores.sucursal');
            }) */
            ->select(
                'programacionsubclientes.*', 
                /* 'bateriaproveedores.servicio', */
                'clienteauditorias.sucursal as cliente_sucursal'
            )
        ->get();


        /* PAGOS PENDIENTES INFORMES FINALES */
        $pagosinformefinalita = ProveedorInformefinal::where(function ($query) {
                /* $query->whereNull('pagoinforme')
                    ->orWhere('pagoinforme', ''); */
            })
            ->whereNotNull('clienteitaid')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.provinfofinalid', 'proveedorinformesfinales.id');
            })
            ->join('clientes', 'proveedorinformesfinales.clienteitaid', '=', 'clientes.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clientes.sucursal as cliente_sucursal'
            )
        ->get();

        $pagosinformefinalauditoria = ProveedorInformefinal::where(function ($query) {
                /* $query->whereNull('pagoinforme')
                    ->orWhere('pagoinforme', ''); */
            })
            ->whereNotNull('clienteauditoriaid')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.provinfofinalid', 'proveedorinformesfinales.id');
            })
            ->join('clienteauditorias', 'proveedorinformesfinales.clienteauditoriaid', '=', 'clienteauditorias.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clienteauditorias.sucursal as cliente_sucursal'
            )
        ->get();


        /* PAGOS PROCESADOS INFORMES FINALES */
        $pagosprocesadosinformefinalita = ProveedorInformefinal::/* where('pagoinforme', 'PAGO PROCESADO')
            -> */whereNotNull('clienteitaid')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.provinfofinalid', 'proveedorinformesfinales.id')
                    ->where('detallerecibos.estado', 'PAGO PROCESADO');
            })
            ->join('clientes', 'proveedorinformesfinales.clienteitaid', '=', 'clientes.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clientes.sucursal as cliente_sucursal'
            )
        ->get();

        $pagosprocesadosinformefinalauditoria = ProveedorInformefinal::/* where('pagoinforme', 'PAGO PROCESADO')
            -> */whereNotNull('clienteauditoriaid')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.provinfofinalid', 'proveedorinformesfinales.id')
                    ->where('detallerecibos.estado', 'PAGO PROCESADO');
            })
            ->join('clienteauditorias', 'proveedorinformesfinales.clienteauditoriaid', '=', 'clienteauditorias.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clienteauditorias.sucursal as cliente_sucursal'
            )
        ->get();

        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');

        $records = DB::table('programacionsubclientes')
            ->selectRaw("
                COALESCE(fechacredito, fechaasignada) as fechaasignada,  -- Usar fechacredito si existe, sino usar fechaasignada
                SUM(CASE 
                    WHEN precio IS NULL THEN 0
                    ELSE precio 
                END) as total_ingresos,
                SUM(CASE 
                    WHEN preciocompra IS NULL THEN 0
                    ELSE preciocompra 
                END) as total_egresos
            ")
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)
            ->whereNotNull('preciocompra')
            ->whereYear(DB::raw('COALESCE(fechacredito, fechaasignada)'), $year)
            ->whereMonth(DB::raw('COALESCE(fechacredito, fechaasignada)'), $month)
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('COALESCE(fechacredito, fechaasignada)'))
        ->get();

        if ($request->ajax()) {
            return response()->json($records);
        }

        return view('admin.caja.cuentascobrar.cobrarhoy', compact('year', 'month', 'records','pagosprocesadosinformefinalauditoria','pagosprocesadosinformefinalita','pagosinformefinalauditoria','pagosinformefinalita','pagosexternosprogramacionesauditoria','pagosexternosprogramacionescomun','pagosexternosprogramacionesita','pagadosprogramacionesita','pagadosprogramacionescomun','pagadosprogramacionesauditoria','pagosprogramacionesita','pagosprogramacionescomun','pagosprogramacionesauditoria', 'fechaActual'));
    }
    public function buscarccporfecha(Request $request)
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
            
            $query->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.programacionid', 'programacionsubclientes.id');
            });
            
        };

        /* PAGOS PENDIENTES INTERNOS */
        $pagosprogramacionesita = Programacionsubcliente::where($filtrarProgramaciones)
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'programacionsubclientes.clienteitaid', '=', 'clientes.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clientes.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->where('bateriaproveedores.servicio', 'INTERNO')
                ->select(
                    'programacionsubclientes.id as programacionsubcliente_id',
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clientes.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);

        $pagosprogramacionescomun = Programacionsubcliente::where($filtrarProgramaciones)
            ->whereNotNull('clientecomunid')
            ->join('clientescomunes', 'programacionsubclientes.clientecomunid', '=', 'clientescomunes.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clientescomunes.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->where('bateriaproveedores.servicio', 'INTERNO')
                ->select(
                    'programacionsubclientes.id as programacionsubcliente_id',
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clientescomunes.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);

        $pagosprogramacionesauditoria = Programacionsubcliente::where($filtrarProgramaciones)
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'programacionsubclientes.clienteauditoriaid', '=', 'clienteauditorias.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clienteauditorias.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->where('bateriaproveedores.servicio', 'INTERNO')
                ->select(
                    'programacionsubclientes.id as programacionsubcliente_id',
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clienteauditorias.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);


        /* PAGOS PENDIENTES EXTERNOS */
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
            $query->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.programacionid', 'programacionsubclientes.id');
            });
            
        };

        $pagosexternosprogramacionesita = Programacionsubcliente::where($filtrarProgramacionesexternos)
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'programacionsubclientes.clienteitaid', '=', 'clientes.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clientes.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->where('bateriaproveedores.servicio', 'EXTERNO')
                ->select(
                    'programacionsubclientes.id as programacionsubcliente_id',
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clientes.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);

        $pagosexternosprogramacionescomun = Programacionsubcliente::where($filtrarProgramacionesexternos)
            ->whereNotNull('clientecomunid')
            ->join('clientescomunes', 'programacionsubclientes.clientecomunid', '=', 'clientescomunes.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clientescomunes.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->where('bateriaproveedores.servicio', 'EXTERNO')
                ->select(
                    'programacionsubclientes.id as programacionsubcliente_id',
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clientescomunes.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);

        $pagosexternosprogramacionesauditoria = Programacionsubcliente::where($filtrarProgramacionesexternos)
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'programacionsubclientes.clienteauditoriaid', '=', 'clienteauditorias.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clienteauditorias.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->where('bateriaproveedores.servicio', 'EXTERNO')
                ->select(
                    'programacionsubclientes.id as programacionsubcliente_id',
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clienteauditorias.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);


        // PAGOS PROCESADOS
        $filtrarPagados = function ($query) use ($criterio, $fecha) {
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

            /* $query->where('pagoatencion', 'PAGO PROCESADO'); */

            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.programacionid', 'programacionsubclientes.id')
                    ->where('detallerecibos.estado', 'PAGO PROCESADO');
            });
        };

        $pagadosprogramacionesita = Programacionsubcliente::where($filtrarPagados)
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'programacionsubclientes.clienteitaid', '=', 'clientes.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clientes.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->select(
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clientes.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);

        $pagadosprogramacionescomun = Programacionsubcliente::where($filtrarPagados)
            ->whereNotNull('clientecomunid')
            ->join('clientescomunes', 'programacionsubclientes.clientecomunid', '=', 'clientescomunes.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clientescomunes.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->select(
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clientescomunes.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);

        $pagadosprogramacionesauditoria = Programacionsubcliente::where($filtrarPagados)
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'programacionsubclientes.clienteauditoriaid', '=', 'clienteauditorias.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clienteauditorias.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->select(
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clienteauditorias.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);


        /* PAGOS PENDIENTES INFORMES FINALES */
        $filtrarpagospendientesinformefinalita = function ($query) use ($criterio) {
            if ($criterio) {
                $query->where(function ($subQuery) use ($criterio) {
                    $subQuery->where('clienteitaid', 'like', "%$criterio%")
                            ->orWhere('clienteitanombre', 'like', "%$criterio%")
                            ->orWhere('clienteauditoriaid', 'like', "%$criterio%")
                            ->orWhere('clienteauditorianombre', 'like', "%$criterio%");
                });
            }

            /* $query->where(function ($subQuery) {
                $subQuery->whereNull('pagoinforme')
                        ->orWhere('pagoinforme', '');
            }); */

            $query->whereNotExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.provinfofinalid', 'proveedorinformesfinales.id')
                    ->where('detallerecibos.estado', 'PAGO PROCESADO');
            });
            
        };

        $pagosinformefinalita = ProveedorInformefinal::where($filtrarpagospendientesinformefinalita)
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'proveedorinformesfinales.clienteitaid', '=', 'clientes.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clientes.sucursal as cliente_sucursal'
            )
        ->simplePaginate(1000);

        $pagosinformefinalauditoria = ProveedorInformefinal::where($filtrarpagospendientesinformefinalita)
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'proveedorinformesfinales.clienteauditoriaid', '=', 'clienteauditorias.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clienteauditorias.sucursal as cliente_sucursal'
            )
        ->simplePaginate(1000);

        // PAGOS PROCESADOS INFORMES FINALES
        $filtrarPagadosinformesfinales = function ($query) use ($criterio) {
            if ($criterio) {
                $query->where(function ($subQuery) use ($criterio) {
                    $subQuery->where('clienteitaid', 'like', "%$criterio%")
                            ->orWhere('clienteitanombre', 'like', "%$criterio%")
                            ->orWhere('clienteauditoriaid', 'like', "%$criterio%")
                            ->orWhere('clienteauditorianombre', 'like', "%$criterio%");
                });
            }

            /* $query->where('pagoinforme', 'PAGO PROCESADO'); */

            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.provinfofinalid', 'proveedorinformesfinales.id')
                    ->where('detallerecibos.estado', 'PAGO PROCESADO');
            });
        };

        $pagosprocesadosinformefinalita = ProveedorInformefinal::where($filtrarPagadosinformesfinales)
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'proveedorinformesfinales.clienteitaid', '=', 'clientes.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clientes.sucursal as cliente_sucursal'
            )
        ->simplePaginate(1000);

        $pagosprocesadosinformefinalauditoria = ProveedorInformefinal::where($filtrarPagadosinformesfinales)
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'proveedorinformesfinales.clienteauditoriaid', '=', 'clienteauditorias.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clienteauditorias.sucursal as cliente_sucursal'
            )
        ->simplePaginate(1000);

        // Usar el año y mes actuales si no se pasan
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');

        // Obtener los registros del mes y año especificados
        $records = DB::table('programacionsubclientes')
            ->selectRaw("
                fechaasignada, 
                SUM(CASE WHEN pagoatencion = 'PAGO PROCESADO' THEN 1 ELSE 0 END) as procesados,
                SUM(CASE WHEN pagoatencion IS NULL THEN 1 ELSE 0 END) as sin_pago
            ")
            ->whereYear('fechaasignada', $year)
            ->whereMonth('fechaasignada', $month)
            ->whereNull('deleted_at')
            ->groupBy('fechaasignada')
            ->get();

        // Si es una solicitud AJAX, retornar los registros
        if ($request->ajax()) {
            return response()->json($records);
        }

        return view('admin.caja.cuentascobrar.cobrarhoy', compact('year', 'month', 'records','pagosexternosprogramacionesauditoria','pagosexternosprogramacionescomun','pagosexternosprogramacionesita',
            'pagadosprogramacionesita',
            'pagadosprogramacionescomun',
            'pagadosprogramacionesauditoria',
            'pagosprogramacionesita',
            'pagosprogramacionescomun',
            'pagosprogramacionesauditoria','fechaActual','pagosinformefinalita','pagosinformefinalauditoria','pagosprocesadosinformefinalita','pagosprocesadosinformefinalauditoria'));
    }
    public function nuevacuentacobrar(Request $request)  
    {
        $nombreproducto = $request->get('buscarpor');
        $sucursal = auth()->user()->sucursal;

        $proveedores = ProveedoresServicios::select('id', 'razonsocial', 'tipotransaccion', 'ciudad', 'ciudad2', 'categoria', 'bancoorigen')->orderBy('razonsocial')->get();
        $clientesIta = Cliente::select('id', 'nombrecompleto', 'sucursal')->orderBy('nombrecompleto')->get();
        $clientesAuditoria = ClienteAuditoria::select('id', 'nombrecompleto', 'sucursal')->orderBy('nombrecompleto')->get();
        $clientesComunes = ClienteComun::select('id', 'nombrecompleto', 'sucursal')->orderBy('nombrecompleto')->get();

        $detallescxc = CCyCPdetalles::where('tipocuenta', 'CUENTA POR COBRAR')->select('id', 'detalle', 'precio')->get();

        return view('admin.caja.cuentascobrar.nuevacuentacobrar', compact('clientesIta', 'clientesAuditoria', 'clientesComunes','proveedores', 'sucursal', 'detallescxc'));
    }
    public function guardarcuentacobrar(Request $request)
    {
        // Obtener los datos generales del formulario
        $formaPago = $request->formapago2;
        $fechaCobro = $request->fechapagar2;
        $bancoorigen = $request->bancoorigen;
        $proveedorId = $request->proveedorId2;
        $proveedorNombre = $request->proveedorNombre2;
        $sucursal = $request->sucursalgasto2;
        $sucursal2 = $request->sucursal2;
        $observacion = $request->observacion;
        $usuarioAutenticado = auth()->user()->id;
        $usuarioAutenticadonombre = auth()->user()->name;

        $ordenes = json_decode($request->ordenes_venta, true);

        if (!$ordenes || !is_array($ordenes)) {
            return back()->with('error', 'No se encontraron datos para guardar.');
        }

        // Determinar el tipo de proveedor o cliente
        $tipoProveedorServicio = null;

        $proveedor = Proveedoresservicios::find($proveedorId);
        if ($proveedor) {
            $tipoProveedorServicio = $proveedor->categoria;
        } elseif ($clienteITA = Cliente::find($proveedorId)) {
            $tipoProveedorServicio = 'CLIENTE ITA';
        } elseif ($clienteAuditoria = ClienteAuditoria::find($proveedorId)) {
            $tipoProveedorServicio = 'CLIENTE AUDITORIA';
        } elseif ($clienteComun = ClienteComun::find($proveedorId)) {
            $tipoProveedorServicio = 'CLIENTE COMUN';
        } else {
            return redirect()->back()->with('error', 'Proveedor o cliente no encontrado');
        }

        $ultimacp = CuentasCobrar::withTrashed() // Incluye eliminados lógicamente
            ->orderByRaw("LENGTH(id) DESC, id DESC")
        ->first();
        $nuevoIdcp = $ultimacp ? ((int) filter_var($ultimacp->id, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;

        foreach ($ordenes as $orden) {
            $idUnico = $nuevoIdcp . 'CC';
        
            DB::table('cuentasporcobrar')->insert([
                'id' => $idUnico,
                'proveedorid' => $proveedorId,
                'proveedornombre' => $proveedorNombre,
                'ciudad' => $sucursal,
                'sucursalcobro' => $sucursal2,
                'formacobro' => $formaPago,
                'fechaasignada' => $fechaCobro,
                'detalleproducto' => $orden['detalle'],
                'cantidad' => $orden['cantidad'],
                'subtotal' => $orden['subtotal'] + $orden['descuento'],
                'descuento' => $orden['descuento'],
                'montototal' => $orden['subtotal'],
                'precio' => $orden['subtotal'],
                'tipoorden' => 'CUENTA POR COBRAR',
                'tipoproveedorservicio' => $tipoProveedorServicio,
                'nrobancodestino' => $bancoorigen,
                'estado' => 'PENDIENTE',
                'observaciones' => $observacion,
                'created_at' => now(),
                'updated_at' => now(),
                'usuarioregistroid' => $usuarioAutenticado,
                'usuarioregistronombre' => $usuarioAutenticadonombre,
            ]);
        
            $nuevoIdcp++;
        }
        

        return redirect()->route('admin.caja.cuentascobrar.nuevacuentacobrar')
            ->with('info', 'Cuenta por cobrar generada con éxito.');
    }
    public function guardardetallecxc(Request $request)  
    {
        CCyCPdetalles::create([
            'detalle' => $request->detalle2,
            'precio' => $request->precio ?? 0.00,
            'tipocuenta' => 'CUENTA POR COBRAR',
            'usuarioregistroid' =>  $request->usuarioregistroid,
            'usuarioregistronombre' =>  $request->usuarioregistronombre,
        ]);

        return redirect()->route('admin.caja.cuentascobrar.nuevacuentacobrar')->with('info', 'El detalle se creó con éxito');
    }
//

// ASIGNAR CREDITOS
    public function actualizarCantidadCuotas(Request $request)
    {
        $request->validate([
            'seleccionados' => 'required|array',
            'seleccionados.*' => 'required|string',
            'cantidadcuotas' => 'required|integer|min:2|max:5',
        ]);

        $primerRegistro = null;

        foreach ($request->seleccionados as $id) {
            $registro = BateriaSubCliente::find($id);
            if ($registro) {
                $registro->cantidadcuotas = $request->cantidadcuotas;
                $registro->save();

                if (!$primerRegistro) {
                    $primerRegistro = $registro;
                }
            }
        }

        if ($primerRegistro) {
            // Determinar tipo de cliente y sucursal
            if ($primerRegistro->clienteitaid) {
                $tipoproveedorservicio = 'CLIENTE ITA';
                $proveedorid = $primerRegistro->clienteitaid;
                $proveedornombre = $primerRegistro->clienteitanombre;
                $cliente = DB::table('clientes')->where('id', $proveedorid)->first();
            } elseif ($primerRegistro->clienteauditoriaid) {
                $tipoproveedorservicio = 'CLIENTE AUDITORIA';
                $proveedorid = $primerRegistro->clienteauditoriaid;
                $proveedornombre = $primerRegistro->clienteauditorianombre;
                $cliente = DB::table('clienteauditorias')->where('id', $proveedorid)->first();
            } else {
                $tipoproveedorservicio = 'CLIENTE COMUN';
                $proveedorid = $primerRegistro->clientecomunid;
                $proveedornombre = $primerRegistro->clientecomunnombre;
                $cliente = DB::table('clientescomunes')->where('id', $proveedorid)->first();
            }

            $SucursalCliente = $cliente->sucursal ?? '';

            // Generar ID único
            $ultimacp = CuentasCobrar::orderByRaw("LENGTH(id) DESC, id DESC")->first();
            $nuevoIdcp = $ultimacp ? ((int) filter_var($ultimacp->id, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
            $idUnico = $nuevoIdcp . 'CC';

            // Crear solo una cuenta por cobrar
            CuentasCobrar::create([
                'id' => $idUnico,
                'proveedorid' => $proveedorid,
                'proveedornombre' => $proveedornombre,
                'tipoproveedorservicio' => $tipoproveedorservicio,
                'detalleproducto' => 'LETRA DE CAMBIO',
                'fechaasignada' => now(),
                'cantidad' => '0',
                'subtotal' => '60.00',
                'descuento' => '0.00',
                'montototal' => '60.00',
                'precio' => '60.00',
                'tipoorden' => 'CUENTA POR COBRAR',
                'estado' => 'PENDIENTE',
                'usuarioregistroid' => auth()->user()->id,
                'usuarioregistronombre' => auth()->user()->name,
                'ciudad' => auth()->user()->sucursal ?? '',
                'sucursalcobro' => $SucursalCliente,
                'formacobro' => 'CONTADO',
                'observaciones' => 'NINGUNO',
                'nrobancodestino' => '3000189269',
            ]);
        }

        return redirect()->back()->with('info', 'Nro. de cuotas asignadas exitosamente.');
    }
    public function ccporcredito(Request $request)
    {
        $registros = collect();
        $creditos = collect();

        if ($request->filled('search') || $request->filled('tipo_cliente')) {
            $search = $request->search;
            $tipoCliente = $request->tipo_cliente;
        
            $subClienteRegistros = Bateriasubcliente::where(function($query) use ($search, $tipoCliente) {
                if ($tipoCliente) {
                    if ($tipoCliente == 'CLIENTE ITA') {
                        $query->where('clienteitaid', 'like', "%$search%")
                            ->orWhere('clienteitanombre', 'like', "%$search%");
                    } elseif ($tipoCliente == 'CLIENTE BANCO') {
                        $query->where('clienteid', 'like', "%$search%")
                            ->orWhere('clientenombre', 'like', "%$search%");
                    } elseif ($tipoCliente == 'CLIENTE AUDITORIA') {
                        $query->where('clienteauditoriaid', 'like', "%$search%")
                            ->orWhere('clienteauditorianombre', 'like', "%$search%");
                    } elseif ($tipoCliente == 'CLIENTE COMUN') {
                        $query->where('clientecomunid', 'like', "%$search%")
                            ->orWhere('clientecomunnombre', 'like', "%$search%");
                    }
                } else {
                    
                }
            })
            ->where('cantidadcuotas', '!=', '')
            ->whereNotNull('cantidadcuotas')
            ->where(function ($query) {
                $query->whereNull('estadocredito')
                      ->orWhere('estadocredito', '');
            });

            $registros = $subClienteRegistros->get();
        
            foreach ($registros as $registro) {
                $registro->tramite = TramiteSubCliente::where(function($query) use ($registro) {
                    $query->where('clienteitaid', $registro->clienteitaid)
                        ->orWhere('clienteauditoriaid', $registro->clienteauditoriaid);
                })->first();
            }

            $creditosregistros = Credito::where(function($query) use ($search, $tipoCliente) {
                if ($tipoCliente) {
                    if ($tipoCliente == 'CLIENTE ITA') {
                        $query->where('clienteid', 'like', "%$search%")
                            ->orWhere('clientenombre', 'like', "%$search%");
                    } elseif ($tipoCliente == 'CLIENTE BANCO') {
                        $query->where('clienteid', 'like', "%$search%")
                            ->orWhere('clientenombre', 'like', "%$search%");
                    } elseif ($tipoCliente == 'CLIENTE AUDITORIA') {
                        $query->where('clienteid', 'like', "%$search%")
                            ->orWhere('clientenombre', 'like', "%$search%");
                    } elseif ($tipoCliente == 'CLIENTE COMUN') {
                        $query->where('clienteid', 'like', "%$search%")
                            ->orWhere('clientenombre', 'like', "%$search%");
                    }
                } else {
                    
                }
            });

            $creditos = $creditosregistros->get();

        }

        return view('admin.caja.cuentascobrar.ccporcredito', compact('registros', 'creditos'));
    }
    public function creditosaprobados(Request $request)
    {
        $creditos = Credito::orderBy('fechacredito', 'asc')->get();


        return view('admin.caja.cuentascobrar.creditosaprobados', compact('creditos'));
    }
    public function actualizarRegistros(Request $request)
    {
        $seleccionados = $request->input('seleccionados', []);
        $campoFecha = $request->input('campo_fecha', []);
        $campoMonto = $request->input('campo_monto', []);
        $nombreGerente = $request->input('gerente');
        $idcliente = $request->input('idcliente');
        $documento = $request->file('documento');
        $documentolcambio = $request->file('documentolcambio');
        $usuarioId = auth()->id();
        $usuarioNombre = auth()->user()->name;

        $request->validate([
            'gerente' => 'required',
            'documento' => 'required',
            'documentolcambio' => 'required',
        ]);

        $archivoName = null;
        if ($request->hasFile('documento')) {
            $carpetaUsuario = public_path("creditos/{$idcliente}/");
            if (!file_exists($carpetaUsuario)) {
                mkdir($carpetaUsuario, 0755, true);
            }
            $archivoName = time() . '_' . $documento->getClientOriginalName();
            $documento->move($carpetaUsuario, $archivoName);
        }

        $archivoName2 = null;
        if ($request->hasFile('documentolcambio')) {
            $carpetaUsuario = public_path("creditos/{$idcliente}/");
            if (!file_exists($carpetaUsuario)) {
                mkdir($carpetaUsuario, 0755, true);
            }
            $archivoName2 = time() . '_' . $documentolcambio->getClientOriginalName();
            $documentolcambio->move($carpetaUsuario, $archivoName2);
        }

        Carbon::setLocale('es');

        $totalCuotas = 0;
        $detallesArray = [];
        $tramitesArray = [];
        $creditosData = [];
        $detallesSumados = [];
        $montocuotaTotal = 0;
        $ultimoNroCredito = DB::table('creditos')->orderBy('nrocredito', 'desc')->first();

        if ($ultimoNroCredito) {
            preg_match('/(\d+)([A-Za-z]+)/', $ultimoNroCredito->nrocredito, $matches);
            $nuevoNumero = $matches[1] + 1;
            $nuevoNroCredito = $nuevoNumero . 'CR';
        } else {
            $nuevoNroCredito = '1CR';
        }
            foreach ($seleccionados as $id) {
                $registro = Bateriasubcliente::find($id);
                if ($registro) {
                    $cantidadCuotas = $registro->cantidadcuotas;
                    for ($i = 0; $i < $cantidadCuotas; $i++) {
                        if ($registro->clienteitaid) {
                            $cliente = DB::table('clientes')->where('id', $registro->clienteitaid)->first();
                            $clienteNombre = $registro->clienteitanombre;
                            $SucursalCliente = $cliente->sucursal ?? 'N/A';
                            $clienteCI = $cliente->ci ?? 'N/A';
                        } elseif ($registro->clientecomunid) {
                            $cliente = DB::table('clientescomunes')->where('id', $registro->clientecomunid)->first();
                            $clienteNombre = $registro->clientecomunnombre;
                            $SucursalCliente = $cliente->sucursal ?? 'N/A';
                            $clienteCI = $cliente->ci ?? 'N/A';
                        } elseif ($registro->clienteauditoriaid) {
                            $cliente = DB::table('clientesauditorias')->where('id', $registro->clienteauditoriaid)->first();
                            $clienteNombre = $registro->clienteauditorianombre;
                            $SucursalCliente = $cliente->sucursal ?? 'N/A';
                            $clienteCI = $cliente->ci ?? 'N/A';
                        } else {
                            $clienteNombre = 'Desconocido';
                            $clienteCI = 'N/A';
                            $SucursalCliente = 'N/A';
                        }
                        $montocuota = $campoMonto[$id][$i] ?? 0;
                        $fechacredito = $campoFecha[$id][$i] ?? null;
                        if ($fechacredito) {
                            if (!isset($detallesSumados[$fechacredito][$montocuota])) {
                                $detallesSumados[$fechacredito][$montocuota] = true;
                                $montocuotaTotal += $montocuota;
                                $totalCuotas += $montocuota;
                                $detalle = $registro->accionnombre;
                                if (!in_array($detalle, $detallesArray)) {
                                    $detallesArray[] = $detalle;
                                }
                                $tramite = DB::table('tramitessubclientes')
                                    ->where(function ($query) use ($registro) {
                                        $query->where('clienteitaid', $registro->clienteitaid)
                                            ->orWhere('clienteauditoriaid', $registro->clienteauditoriaid);
                                    })
                                    ->where('fechabateria', $registro->fechabateria)
                                    ->value('tramite');
                                if ($tramite && !in_array($tramite, $tramitesArray)) {
                                    $tramitesArray[] = $tramite;
                                }
                                $exists = DB::table('creditos')
                                    ->where('bateriaid', $registro->id)
                                    ->where('fechacredito', $fechacredito)
                                    ->where('montocuota', $montocuota)
                                    ->exists();
                                if (!$exists) {
                                    $data = [
                                        'bateriaid' => $registro->id,
                                        'detalle' => $registro->accionnombre,
                                        'clienteid' => $registro->clienteitaid ?? $registro->clienteauditoriaid ?? $registro->clientecomunid,
                                        'clientenombre' => $registro->clienteitanombre ?? $registro->clienteauditorianombre ?? $registro->clientecomunnombre,
                                        'proveedor' => $registro->proveedorasignado,
                                        'precioreal' => $registro->precio,
                                        'fechacredito' => $fechacredito,
                                        'montocuota' => $montocuota,
                                        'usuarioautorizador' => $nombreGerente,
                                        'docrespaldo' => $archivoName,
                                        'letracambio' => $archivoName2,
                                        'usuarioregistroid' => $usuarioId,
                                        'usuarioregistronombre' => auth()->user()->name,
                                        'tramite' => $tramite,
                                        'nrocredito' => $nuevoNroCredito,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ];
                                    $creditoId = DB::table('creditos')->insertGetId($data);
                                    $creditosData[] = [
                                        'id' => $creditoId,
                                        'fechacredito' => $fechacredito,
                                        'montocuota' => $montocuota,
                                        'clienteNombre' => $clienteNombre,
                                        'clienteCI' => $clienteCI,
                                        'SucursalCliente' => $SucursalCliente,
                                        'nrocredito' => $nuevoNroCredito,
                                    ];
                                }
                            }
                        }
                    }
                    $registro->update(['estadocredito' => 'PROCESADO']);

                }
            }

            $detalles = implode(', ', $detallesArray);
            $tramites = implode(', ', $tramitesArray);
            $fechaactual = Carbon::now()->translatedFormat('d \d\e F \d\e Y');

            $pdf = PDF::loadView('admin.caja.cuentascobrar.pdfcreditos', [
                    'creditos' => $creditosData, 
                    'totalCuotas' => $totalCuotas, 
                    'montocuotaTotal' => $montocuotaTotal,
                    'detalles' => $detalles,
                    'tramites' => $tramites,
                    'fechaactual' => $fechaactual,
                    'nrocredito' => $nuevoNroCredito,]);

            return $pdf->download('CARTA_DE_CREDITO_' . $clienteNombre . '.pdf');

    }
    public function agregarcartacredito(Request $request)  
    {
        // Obtener los créditos seleccionados
        $idcliente = $request->input('idcliente');
        $creditosSeleccionados = $request->input('creditos');
        $cartacredito = $request->file('cartacredito');

        // Validación
        $request->validate([
            'cartacredito' => 'required|file',
        ]);

        // Subir documento solo una vez
        $archivoName = null;
        if ($request->hasFile('cartacredito')) {
            $archivoName = time() . '_' . $cartacredito->getClientOriginalName();
            $carpetaUsuario = public_path("creditos/{$idcliente}/"); // Usar el primer cliente para la carpeta

            if (!file_exists($carpetaUsuario)) {
                mkdir($carpetaUsuario, 0755, true);
            }

            // Mover el archivo una sola vez
            $cartacredito->move($carpetaUsuario, $archivoName);
        }

        // Actualizar base de datos para cada crédito seleccionado
        foreach ($creditosSeleccionados as $creditoId) {
            DB::table('creditos')
                ->where('id', $creditoId)
                ->update(['cartacredito' => $archivoName]);
        }

        return redirect()->back()->with('info', 'Registro de carta de crédito exitoso.');
    }
//

// EGRESOS
    public function cajaegresos()
    {
        $sucursal = auth()->user()->sucursal;
        $consolidados = Consolidadocaja::all();
        $bancos = Banco::all();
        $cuentas = CuentasBancos::where('estado', 'ACTIVO')->get();
        $ultimoId = Recibo::max('id');
        $siguienteId = $ultimoId ? $ultimoId + 1 : 1;
        $rolusuario = auth()->user()->getRoleNames()->first();
        $user = auth()->user();
        $usuarioAutenticado = auth()->user()->name;
        $proveedores = Proveedor::orderBy('proveedor')->get();
        $proveedoresservicios = Proveedoresservicios::orderBy('razonsocial')->get();
        $hoy = now();
        if ($hoy->dayOfWeek == 1) {
            $fechaAyer = $hoy->copy()->subDays(2)->toDateString();
        } else {
            $fechaAyer = $hoy->copy()->subDay()->toDateString();
        }

        /* $registroCierreCajaAyer = DB::table('cierrecaja')
            ->where('usuariocierrenombre', $usuarioAutenticado)
            ->whereDate('updated_at', $fechaAyer)
            ->orderBy('updated_at', 'desc')
            ->first();

        $codigoAprobacion = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.ingreso.index')
            ->where('estado', 'expirado')
            ->exists();

        $mostrarVista = $registroCierreCajaAyer || $codigoAprobacion; */

        //BLOQUEO CAJA
            $idUsuario = auth()->user()->id;
            $usuarioAutenticado = auth()->user()->name;
            $hoy = Carbon::today();
            $ayer = Carbon::yesterday();
            $horaLimite = Carbon::today()->setTime(10, 00);
            $ahora = Carbon::now();

            $mostrarVista = true;

            $ultimoRegistro = DB::table('cajacentral')
                ->where('usuarioregistroid', $idUsuario)
                ->orderBy('created_at', 'desc')
                ->first();

            $registroCierreCaja = true;

            if ($ultimoRegistro) {
                $fechaUltimoRegistro = Carbon::parse($ultimoRegistro->created_at)->toDateString();

                if ($fechaUltimoRegistro !== $hoy->toDateString()) {
                    $registroCierreCaja = DB::table('cierrecaja')
                        ->where('usuariocierreid', $idUsuario)
                        ->whereDate('created_at', $fechaUltimoRegistro)
                        ->exists();
                }
            }

            $codigoAprobacion = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
                ->whereDate('fechaSolicitada', $hoy->toDateString())
                ->where('permisoSolicitado', 'admin.ingreso.index')
                ->where('estado', 'expirado')
                ->exists();

            /*  $mostrarVista = $registroCierreCaja || $codigoAprobacion; */

            $tuvoEfectivoAyer = DB::table('cajacentral')
                ->where('usuarioregistroid', $idUsuario)
                ->whereDate('created_at', $ayer->toDateString())
                ->where('tipotransaccion', 'EFECTIVO')
                ->exists();

            $registroDepositoHoyAntesDe10am = true;

            if ($ahora->greaterThan($horaLimite)) {
                $registroDepositoHoyAntesDe10am = DB::table('depositosbancarios')
                    ->where('usuarioregistroid', $idUsuario)
                    ->whereDate('created_at', $hoy->toDateString())
                    ->whereTime('created_at', '<=', $horaLimite->toTimeString())
                    ->exists();
            }

            $restriccionDeposito = $tuvoEfectivoAyer && !$registroDepositoHoyAntesDe10am;

            $mostrarVista = ($registroCierreCaja && !$restriccionDeposito) || $codigoAprobacion;
        //

        return view('admin.caja.egreso.cajaegresos', [
            'mostrarVista' => $mostrarVista,
            'sucursal' => $sucursal,
            'consolidados' => $consolidados,
            'bancos' => $bancos,
            'siguienteId' => $siguienteId,
            'rolusuario' => $rolusuario,
            'proveedores' => $proveedores,
            'proveedoresservicios' => $proveedoresservicios,
            'cuentas' => $cuentas
        ]);
    }
    public function buscarPorProveedoregreso(Request $request)   
    {
        $entrada = $request->input('proveedorid');
        $nrofactura = $request->input('nrofactura');
        $nrofactura2 = $request->input('nrofactura2');
        $nrofactura3 = $request->input('nrofactura3');
        $tipoproveedor = $request->input('tipocliente');

        if (!in_array($tipoproveedor, ['medico', 'proveedor'])) {
            return response()->json(['error' => 'Tipo de cliente no válido'], 400);
        }

        $proveedorNit = null;
        $proveedorCi = null;
        switch ($tipoproveedor) {
            case 'medico':
                $proveedor = Proveedor::where('proveedor', $entrada)
                    ->orWhere('proveedor', 'LIKE', "%{$entrada}%")
                    ->first(['id', 'proveedor', 'proveedor']);
                break;
            case 'proveedor':
                $proveedor = Proveedoresservicios::where('razonsocial', $entrada)
                    ->orWhere('razonsocial', 'LIKE', "%{$entrada}%")
                    ->first(['id', 'razonsocial']);
                break;
            default:
                $proveedor = null;
        }

        if (!$proveedor) {
            return response()->json(['error' => 'Proveedor no encontrado'], 404);
        }

        $proveedorNit = $proveedor->nit;
        $proveedorCi = $proveedor->ci;

        // Obtener registros de la tabla ProgramacionSubCliente
        $registrosProgramacion = ProgramacionSubCliente::where('proveedorNombre', $proveedor->proveedor)
            ->whereNull('deleted_at')
            ->where(function ($query) use ($nrofactura, $nrofactura2, $nrofactura3) {
                // Filtramos los números de factura solo si no están vacíos
                if ($nrofactura) {
                    $query->orWhere('nrofactura', $nrofactura);
                }
                if ($nrofactura2) {
                    $query->orWhere('nrofactura', $nrofactura2);
                }
                if ($nrofactura3) {
                    $query->orWhere('nrofactura', $nrofactura3);
                }
            })
            ->where('pagoatencion', null)
            ->where(function ($query) {
                $query->where('preciocompra', '>', 0)
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNotNull('preciocompra')
                                ->where('preciocompra', '!=', '0.00')
                                ->where('preciocompra', '!=', '0,00')
                                ->where('preciocompra', '!=', '0');
                    });
            })
            ->get()
            ->map(function ($registro) {

                $detallerecibo = Detallerecibo::where('programacionid', $registro->id) 
                    ->where('tipomovimiento', 'EGRESO')
                    ->latest('created_at')
                    ->first();

                if ($detallerecibo) {
                    if ($detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'EGRESO') {
                        return null;
                    }

                    if ($detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'EGRESO') {
                        $registro->preciocompra = $detallerecibo->saldo;
                    }
                }

                /* $existeDocumentacion = DocumentacionSubCliente::where(function ($query) use ($registro) {
                        if (!is_null($registro->clienteitaid)) {
                            $query->where('clienteitaid', $registro->clienteitaid);
                        } elseif (!is_null($registro->clienteauditoriaid)) {
                            $query->where('clienteauditoriaid', $registro->clienteauditoriaid);
                        } elseif (!is_null($registro->clientebancoid)) {
                            $query->where('clientebancoid', $registro->clientebancoid);
                        } elseif (!is_null($registro->clientecomunid)) {
                            $query->where('clientecomunid', $registro->clientecomunid);
                        }
                    })
                    ->where('accion', $registro->accionnombre)
                    ->where('fechabateria', $registro->fechabateria)
                ->exists();
                
                if (!$existeDocumentacion) {
                    return null;
                } */
                $existeDocumentacion = DocumentacionSubCliente::where(function ($query) use ($registro) {
                    if (!is_null($registro->clienteitaid)) {
                        $query->where('clienteitaid', $registro->clienteitaid);
                    } elseif (!is_null($registro->clienteauditoriaid)) {
                        $query->where('clienteauditoriaid', $registro->clienteauditoriaid);
                    } elseif (!is_null($registro->clientebancoid)) {
                        $query->where('clientebancoid', $registro->clientebancoid);
                    }
                })
                ->where('accion', $registro->accionnombre)
                ->where('fechabateria', $registro->fechabateria)
                ->exists();
                
                // Aplicar la restricción **solo si NO es clientecomunid**
                if (!$existeDocumentacion && is_null($registro->clientecomunid)) {
                    return null;
                }
                
            
                switch (true) {
                    case isset($registro->clienteitaid):
                        $registro->tramite = TramitesubCliente::where('clienteitaid', $registro->clienteitaid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;

                    case isset($registro->clienteid):
                        $registro->tramite = TramitesubCliente::where('clienteid', $registro->clienteid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;

                    case isset($registro->clientecomunid):
                        $registro->tramite = TramitesubCliente::where('clientecomunid', $registro->clientecomunid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;

                    case isset($registro->clienteauditoriaid):
                        $registro->tramite = TramitesubCliente::where('clienteauditoriaid', $registro->clienteauditoriaid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;
                }
                return $registro;
            })
            ->filter()
        ->values();

        // Obtener registros de la tabla ProveedorInformesFinales
        $registrosProveedorInformesFinales = ProveedorInformefinal::where('proveedorasignado', $proveedor->proveedor)
            ->whereNull('deleted_at')
            ->where(function ($query) use ($nrofactura, $nrofactura2, $nrofactura3) {
                // Filtramos los números de factura solo si no están vacíos
                if ($nrofactura) {
                    $query->orWhere('nrofactura', $nrofactura);
                }
                if ($nrofactura2) {
                    $query->orWhere('nrofactura', $nrofactura2);
                }
                if ($nrofactura3) {
                    $query->orWhere('nrofactura', $nrofactura3);
                }
            })
            ->where(function ($query) {
                $query->where('preciocompra', '>', 0)
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNotNull('preciocompra')
                                ->where('preciocompra', '!=', '0.00')
                                ->where('preciocompra', '!=', '0,00')
                                ->where('preciocompra', '!=', '0');
                    });
            })
            ->get()
            ->map(function ($registro) {
                $detallerecibo = Detallerecibo::where('provinfofinalid', $registro->id) 
                    ->latest('created_at')
                    ->first();

                if ($detallerecibo) {
                    if ($detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'EGRESO') {
                        return null;
                    }

                    if ($detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'EGRESO') {
                        $registro->preciocompra = $detallerecibo->saldo;
                    }
                }

                $existeDocumentacion = Informefinal::where(function ($query) use ($registro) {
                        if (!is_null($registro->clienteitaid)) {
                            $query->where('clienteitaid', $registro->clienteitaid);
                        } elseif (!is_null($registro->clienteauditoriaid)) {
                            $query->where('clienteauditoriaid', $registro->clienteauditoriaid);
                        } elseif (!is_null($registro->clientebancoid)) {
                            $query->where('clientebancoid', $registro->clientebancoid);
                        }
                    })
                    ->where('fechabateria', $registro->fechabateria)
                    ->exists();
                
                if (!$existeDocumentacion) {
                    return null;
                }
            
                // Obtener todos los trámites agrupados por 'fechabateria'
                $tramitesPorFecha = TramitesubCliente::where('fechabateria', $registro->fechabateria)
                ->orderBy('id') // Ordena para una asignación consistente
                ->get()
                ->groupBy('fechabateria');

                // Lista de trámites disponibles para la `fechabateria`
                $tramites = $tramitesPorFecha[$registro->fechabateria] ?? collect();

                // Contador de registros por fecha para alternar los trámites
                static $contador = [];

                // Asegurar índice para asignar un trámite diferente en cada iteración
                $contador[$registro->fechabateria] = ($contador[$registro->fechabateria] ?? 0) % max(1, $tramites->count());

                switch (true) {
                case isset($registro->clienteitaid):
                    $registro->tramite = optional($tramites->where('clienteitaid', $registro->clienteitaid)->values()->get($contador[$registro->fechabateria]))->tramite;
                    break;

                case isset($registro->clienteid):
                    $registro->tramite = optional($tramites->where('clienteid', $registro->clienteid)->values()->get($contador[$registro->fechabateria]))->tramite;
                    break;

                case isset($registro->clientecomunid):
                    $registro->tramite = optional($tramites->where('clientecomunid', $registro->clientecomunid)->values()->get($contador[$registro->fechabateria]))->tramite;
                    break;

                case isset($registro->clienteauditoriaid):
                    $registro->tramite = optional($tramites->where('clienteauditoriaid', $registro->clienteauditoriaid)->values()->get($contador[$registro->fechabateria]))->tramite;
                    break;
                }

                // Incrementamos el contador para el siguiente registro de la misma `fechabateria`
                $contador[$registro->fechabateria]++;

                return $registro;

            })
            ->filter()
        ->values();

        $registrosCuentasporPagar = CuentasPagar::where('proveedorNombre', $proveedor->razonsocial)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($registro) {

                $detallerecibo = Detallerecibo::where('cuentapagarid', $registro->id) 
                    ->latest('created_at')
                    ->first();

                if ($detallerecibo) {
                    if ($detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'EGRESO') {
                        return null;
                    }

                    if ($detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'EGRESO') {
                        $registro->preciocompra = $detallerecibo->saldo;
                    }
                }
                return $registro;
            })
            ->filter()
        ->values();


        $registros = $registrosProgramacion
            ->merge($registrosProveedorInformesFinales)
            ->merge($registrosCuentasporPagar);


        return response()->json([
            'proveedor' => $proveedor,
            'registros' => $registros
        ]);
    }
    public function guardarCajaCentralegreso(Request $request)
    {
        $usuarioAutenticadoid = Auth::user()->id;
        $usuarioAutenticadonombre = Auth::user()->name;
    
        $request->validate([
            'tipocliente' => '',
            'clienteid' => '',
            'clientenombre' => '',
            'proveedorid' => '',
            'proveedornombre' => '',
            'subtotal' => '',
            'descuento' => '',
            'montoreal' => '',
            'montototal' => '',
            'ciudadregistro' => '',
            'programacionIds' => '',
            'area' => '',
            'detalle' => '',
            'nrofactura' => '',
            'nrofactura2' => '',
            'nrofactura3' => '',
            'nrobancarizaciondeposito' => '',
            'nrobancarizaciontransferencia' => '',
            'nrobancarizacioncheque' => '',
            'nrocheque' => '',
            'nrotarjeta' => '',
            'nroap' => '',
            'nroref' => '',
            'nrocuentadestinodeposito' => '',
            'nrocuentaorigendeposito' => '',
            'tipobancodeposito' => '',
            'nrocuentadestinotransferencia' => '',
            'nrocuentaorigentransferencia' => '',
            'tipobancotransferencia' => '',
            'tipobancocheque' => '',
            'nrocuentadestinocheque' => '',
            'tipobanco' => '',
            'tipocambio' => '',
            'tipomovimiento' => '',
            'tipotransaccion' => '',
            'tipotransaccion2' => '',
            'ciudadregistro' => '',
            'nombrebanco' => '',
            'numerobanco' => '',
            'usuarioregistroid' => '',
            'usuarioregistronombre' => '',
        ]);

        if (empty($request->programacionIds)) {
            // Redirigir con el mensaje en la sesión
            return redirect()->back()->with('infoerror', 'Debes seleccionar al menos un registro');
        }

        $programacionIds = explode(',', $request->programacionIds);
        $descuentos = explode(',', $request->descuentos);
        $pagos = explode(',', $request->pagos);

        $tipocliente = match ($request->tipocliente) {
            'proveedor' => 'PROVEEDOR DE SERVICIO',
            'medico' => 'MEDICO',
            'clienteauditoriaid' => 'AUDITORIA',
            'clientecomunid' => 'COMUN',
            'clientebancoid' => 'BANCO',
            default => $request->tipocliente,
        };

        $saldototal = $request->montoreal - ($request->montototal + $request->descuento);
        $saldototal = number_format($saldototal, 2, '.', ''); 

        $estado = ($saldototal == 0) ? 'PAGO PROCESADO' : 'SALDO PENDIENTE';

        $tipotransaccion = $request->tipotransaccion;
        if ($tipotransaccion == 'DEPOSITO_BANCARIO') {
            $tipotransaccion = 'DEPOSITO BANCARIO';
        } elseif ($tipotransaccion == 'TRANSFERENCIA_BANCARIA') {
            $tipotransaccion = 'TRANSFERENCIA BANCARIA';
        } elseif ($tipotransaccion == 'RETIRO_BANCARIO') {
            $tipotransaccion = 'RETIRO BANCARIO';
        } elseif (in_array($tipotransaccion, ['EFECTIVO', 'CHEQUE', 'ATC'])) {
        }

        $tipotransaccion2 = $request->tipotransaccion2;
        if ($tipotransaccion2 == 'DEPOSITO_BANCARIO') {
            $tipotransaccion2 = 'DEPOSITO BANCARIO';
        } elseif ($tipotransaccion2 == 'TRANSFERENCIA_BANCARIA') {
            $tipotransaccion2 = 'TRANSFERENCIA BANCARIA';
        } elseif (in_array($tipotransaccion2, ['EFECTIVO', 'CHEQUE', 'ATC'])) {
        }

        $tipomovimiento = 'EGRESO';
        $recibo = Recibo::create([
            'ciudadregistro' => $request->ciudadregistro,
            'usuarioregistroid' => $usuarioAutenticadoid,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
            'tipocliente' => $tipocliente,
            'proveedorid' => $request->proveedorid,
            'proveedornombre' => $request->proveedornombre,
            'tipomovimiento' => 'EGRESO',
            'subtotal' => $request->subtotal,
            'descuentototal' => $request->descuento,
            'montototal' => $request->montototal,
            /* 'estado' => ($tipocliente === 'PROVEEDOR DE SERVICIO' && $tipomovimiento === 'EGRESO') ? 'PAGO PROCESADO' : $estado, */
            'estado' => $estado,
            'saldototal' => $saldototal,
        ]);


        // Obtener el HTML del recibo desde la solicitud
        $html = $request->input('html_recibo');
        
        if (!$html) {
            return redirect()->back()->with('error', 'No se recibió HTML para generar el recibo');
        }

        // Obtener el ID del usuario autenticado
        $usuarioAutenticadoid = Auth::id();  // O como estés obteniendo el ID del usuario

        // Nombre del archivo con timestamp
        $nombreArchivo = 'recibo_' . time() . '.html';

        // Ruta del archivo donde lo queremos guardar
        $rutaDirectorio = public_path('documentacioncaja/egresos/' . $usuarioAutenticadoid);

        // Verificar si la ruta existe y si no, crearla
        if (!File::exists($rutaDirectorio)) {
            File::makeDirectory($rutaDirectorio, 0777, true);  // Crear directorios de forma recursiva
        }

        // Ruta completa para guardar el archivo
        $rutaArchivo = $rutaDirectorio . '/' . $nombreArchivo;

        // Guardar el HTML en un archivo
        File::put($rutaArchivo, $html);

        Cajacentral::create([
            'tipocliente' => $tipocliente,
            'proveedorid' => $request->proveedorid,
            'proveedornombre' => $request->proveedornombre,
            'subtotal' => $request->subtotal,
            'descuento' => $request->descuento,
            'montototal' => $request->montototal,
            'nrorecibo' => $recibo->id,
            'saldo' => $saldototal,
            'area' =>  $request->area,
            /* 'estado' => $request->area === 'CUENTA POR PAGAR' ? 'PAGO PROCESADO' : $estado, */
            'estado' => $estado,
            'detalle' => $request->detalle,
            'nrofactura' => $request->nrofactura,
            'nrofactura2' => $request->nrofactura2,
            'nrofactura3' => $request->nrofactura3,
            'nrobancarizaciontransferencia' => $request->nrobancarizaciontransferencia,
            'nrobancarizacioncheque' => $request->nrobancarizacioncheque,
            'nrobancarizaciondeposito' => $request->nrobancarizaciondeposito,
            'nrocheque' => $request->nrocheque,
            'nrotarjeta' => $request->nrotarjeta,
            'nroap' => $request->nroap,
            'nroref' => $request->nroref,
            'nrocuentadestinodeposito' => $request->nrocuentadestinodeposito,
            'nrocuentaorigendeposito' => $request->nrocuentaorigendeposito,
            'tipobancodeposito' => $request->tipobancodeposito,
            'nrocuentadestinotransferencia' => $request->nrocuentadestinotransferencia,
            'nrocuentaorigentransferencia' => $request->nrocuentaorigentransferencia,
            'tipobancotransferencia' => $request->tipobancotransferencia,
            'tipobancocheque' => $request->tipobancocheque,
            'tipobanco' => $request->tipobanco,
            'tipocambio' => $request->tipocambio,
            'tipomovimiento' => 'EGRESO',
            'tipotransaccion' => $tipotransaccion,
            'tipotransaccion2' => $tipotransaccion2,
            'ciudadregistro' => $request->ciudadregistro,
            'nombrebanco' => $request->nombrebanco,
            'numerobanco' => $request->numerobanco,
            'usuarioregistroid' => $usuarioAutenticadoid,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
            'estadorevisioncierre' => 'DOCUMENTACION PENDIENTE',
            'docrespaldoegreso' => $nombreArchivo,
            'nrocuentadestinocheque' => ($tipotransaccion === 'CHEQUE') ? 3000189269 : null,
        ]);

        foreach ($programacionIds as $index => $programacionId) {
            /* $programacion = ProgramacionSubCliente::find($programacionId);
            $proveedor = ProveedorInformeFinal::find($programacionId);
            $cuentapagar = CuentasPagar::where('id', $programacionId)->first(); */
            $programacion = null;
            $proveedor = null;
            $cuentapagar = null;
            if (str_ends_with($programacionId, 'CP')) {
                $cuentapagar = CuentasPagar::find($programacionId);
            }
            if (!$cuentapagar) {
                $programacion = ProgramacionSubCliente::find($programacionId);
                if (!$programacion) {
                    $proveedor = ProveedorInformeFinal::find($programacionId);
                }
            }
        
            $ultimoDetalleRecibo = Detallerecibo::where(function ($query) use ($programacionId) {
                $query->where('programacionid', $programacionId)
                      ->orWhere('provinfofinalid', $programacionId)
                      ->orWhere('cuentapagarid', $programacionId);
            })
            ->where('tipomovimiento', 'EGRESO')
            ->orderBy('created_at', 'desc')
            ->first();
        

            if ($ultimoDetalleRecibo) {
                $subtotalDetalle = $ultimoDetalleRecibo->saldo;
                $descuentoDetalle = $descuentos[$index];
                $pagoDetalle = $pagos[$index];
            } else {
                if ($programacion) {
                    $subtotalDetalle = $programacion->preciocompra;
                } elseif ($proveedor) {
                    $subtotalDetalle = $proveedor->preciocompra;
                } elseif ($cuentapagar) {
                    /* $subtotalDetalle = $cuentapagar->preciocompra; */
                     $subtotalDetalle = (!empty($cuentapagar->preciocompra) && (float)$cuentapagar->preciocompra > 0) 
                            ? $cuentapagar->preciocompra 
                            : $cuentapagar->subtotal;
                } else {
                    $subtotalDetalle = 0;
                }
                $descuentoDetalle = $descuentos[$index];
                $pagoDetalle = $pagos[$index];
            }

            $subtotalDetalle = is_numeric($subtotalDetalle) ? floatval($subtotalDetalle) : 0;

            $saldoDetalle = $subtotalDetalle - $descuentoDetalle - $pagoDetalle;
            $saldoDetalle = number_format($saldoDetalle, 2, '.', '');

            $estadoDetalle = ($saldoDetalle == 0) ? 'PAGO PROCESADO' : 'SALDO PENDIENTE';

            if ($estadoDetalle == 'PAGO PROCESADO') {
                Detallerecibo::where('programacionid', $programacionId)
                ->orwhere('provinfofinalid', $programacionId)
                ->orwhere('cuentapagarid', $programacionId)
                    ->where('estado', '!=', 'PAGO PROCESADO')
                    ->update(['estado' => 'PAGO PROCESADO']);
            }

            if ($programacion) {
                $area = 'MEDICA';
            } elseif ($proveedor) {
                $area = 'INFORME FINAL';
            } elseif ($cuentapagar) {
                $area = 'CUENTA POR PAGAR';
            }

            $sucursalGasto = null;

            if ($area === 'MEDICA' || $area === 'INFORME FINAL') {
                $clienteId = $cuentapagar ? null : (
                    $programacion ? 
                        ($programacion->clienteitaid ?? $programacion->clienteauditoriaid ?? $programacion->clientecomunid) : 
                        ($proveedor ? 
                            ($proveedor->clienteitaid ?? $proveedor->clienteauditoriaid ?? $proveedor->clientecomunid) : 
                        null)
                );

                if ($clienteId) {
                    $cliente = Cliente::find($clienteId);
                    if (!$cliente) {
                        $cliente = ClienteAuditoria::find($clienteId);
                    }
                    if (!$cliente) {
                        $cliente = ClienteComun::find($clienteId);
                    }

                    if ($cliente) {
                        $sucursalGasto = $cliente->sucursal ?? null;
                    }
                }
            }


            $detalleRecibo = Detallerecibo::create([
                'reciboid' => $recibo->id,
                'programacionid' => $programacion ? $programacionId : null,
                'provinfofinalid' => $proveedor ? $programacionId : null,
                'cuentapagarid' => $cuentapagar ? $programacionId : null,
                'usuarioregistroid' => $usuarioAutenticadoid,
                'usuarioregistronombre' => $usuarioAutenticadonombre,
                'clienteid' => $cuentapagar ? null : (
                    $programacion ? 
                        ($programacion->clienteitaid ?? $programacion->clienteauditoriaid ?? $programacion->clientecomunid) : 
                        ($proveedor ? 
                            ($proveedor->clienteitaid ?? $proveedor->clienteauditoriaid ?? $proveedor->clientecomunid) : 
                        null)
                ),

                'clientenombre' => $cuentapagar ? null : (
                        $programacion ? 
                            ($programacion->clienteitanombre ?? $programacion->clienteauditorianombre ?? $programacion->clientecomunnombre) : 
                            ($proveedor ? 
                                ($proveedor->clienteitanombre ?? $proveedor->clienteauditorianombre ?? $proveedor->clientecomunnombre) : 
                            null)
                    ),

                'area' => $area,
                'detalle' => $cuentapagar
                ? ($cuentapagar->cantidad > 0 
                    ? $cuentapagar->cantidad . ' ' . $cuentapagar->detalleproducto 
                    : $cuentapagar->detalleproducto)
                : ($programacion->accionnombre ?? $proveedor->accionnombre ?? null),

                'fechabateria' => $cuentapagar ? $cuentapagar->fechaasignada : ($programacion ? $programacion->fechabateria : $proveedor->fechabateria),
                'fechaatencion' => $cuentapagar ? $cuentapagar->fechaasignada : ($programacion ? $programacion->fechaasignada : $proveedor->fechaasignada),
                'servicio' => $cuentapagar ? null : ($programacion ? $programacion->servicio : $proveedor->servicio),
                'proveedoratencion' => $cuentapagar ? $cuentapagar->proveedornombre : ($programacion ? $programacion->proveedornombre : $proveedor->proveedorasignado),
                'subtotal' => $subtotalDetalle,
                'descuento' => $descuentoDetalle,
                'bateriaid' => $programacion ? $programacion->bateriaid : null,
                'montototal' => $pagoDetalle,
                'saldo' => $saldoDetalle,
                'ordenid' => $cuentapagar ? $cuentapagar->ordenid : null,
                'estado' => ($cuentapagar && $cuentapagar->ordenid) ? 'PAGO PROCESADO' : $estadoDetalle,
                'tipomovimiento' => 'EGRESO',
                'tipotransaccion' => $tipotransaccion,
                /* 'sucursalgasto' => ($area === 'CUENTA POR PAGAR') ? $cuentapagar->sucursalgasto : null, */
                'sucursalgasto' => ($area === 'CUENTA POR PAGAR') ? $cuentapagar->sucursalgasto : $sucursalGasto,
                'comprobante' => $cuentapagar ? $cuentapagar->comprobante : ($programacion ? $programacion->comprobante : $proveedor->comprobante),
                'factura' => $cuentapagar ? $cuentapagar->factura : ($programacion ? $programacion->factura : $proveedor->factura),
            ]);

            if ($detalleRecibo->cuentapagarid) {
                CuentasPagar::whereIn('id', explode(',', $detalleRecibo->cuentapagarid))
                    ->update(['estado' => $detalleRecibo->estado]);
            }

            CajaCentral::where('nrorecibo', $detalleRecibo->reciboid)
            ->update([
                'doccomprobante' => $detalleRecibo->comprobante,
                'docfactura'     => $detalleRecibo->factura
            ]);
            if ($detalleRecibo->programacionid) {
                $programacionIds = explode(',', $detalleRecibo->programacionid);

                // Obtener los bateriaid relacionados desde programacionsubclientes
                $bateriaIds = Programacionsubcliente::whereIn('id', $programacionIds)
                    ->pluck('bateriaid')
                    ->unique()
                    ->toArray();

                // Actualizar bateriasubclientes
                Bateriasubcliente::whereIn('id', $bateriaIds)
                    ->update(['prioridad' => $detalleRecibo->estado]);
            }
            if ($detalleRecibo->provinfofinalid) {
                Bateriasubcliente::where('provinfofinalid', $detalleRecibo->provinfofinalid)
                    ->update(['prioridad' => $detalleRecibo->estado]);
            }



        }

        switch ($tipotransaccion) {
            case 'DEPOSITO BANCARIO':
                $columna = 'consolidadodeposito';
                break;
            case 'TRANSFERENCIA BANCARIA':
                $columna = 'consolidadotransferencia';
                break;
            case 'CHEQUE':
                $columna = 'consolidadocheque';
                break;
            case 'ATC':
                $columna = 'consolidadoatc';
                break;
            case 'RETIRO BANCARIO':
                $columna = 'consolidadotransferencia';
                break;
            case 'EFECTIVO':
                return redirect()->route('admin.caja.egreso.cajaegresos')
            ->with('info', 'Registro guardado correctamente')
            ->with('montototal', $request->montototal)
            ->with('tipotransaccion', $request->tipotransaccion)
            ->with('tipotransaccion2', $request->tipotransaccion2);
            default:
                return response()->json(['error' => 'Tipo de transacción no válido.'], 400);
        }

        $consolidado = Consolidadocaja::where('usuarioconsolidadoid', $usuarioAutenticadoid)->first();

        if ($consolidado) {
            $consolidado->$columna -= $request->montototal;
            $consolidado->save();
        } else {
            $consolidado = new Consolidadocaja();
            $consolidado->usuarioconsolidadoid = $usuarioAutenticadoid;
            $consolidado->$columna = $request->montototal;
            $consolidado->save();
        }

        return redirect()->route('admin.caja.egreso.cajaegresos')
            ->with('info', 'Registro guardado correctamente')
            ->with('montototal', $request->montototal)
            ->with('tipotransaccion', $request->tipotransaccion)
            ->with('tipotransaccion2', $request->tipotransaccion2);
    }

    public function cajaegresoscomprobantes()
    {
        $sucursal = auth()->user()->sucursal;
        $consolidados = Consolidadocaja::all();
        $bancos = Banco::all();
        $cuentas = CuentasBancos::where('estado', 'ACTIVO')->get();
        $ultimoId = Recibo::max('id');
        $siguienteId = $ultimoId ? $ultimoId + 1 : 1;
        $rolusuario = auth()->user()->getRoleNames()->first();
        $user = auth()->user();
        $usuarioAutenticado = auth()->user()->name;
        $proveedores = Proveedor::orderBy('proveedor')->get();
        $proveedoresservicios = Proveedoresservicios::orderBy('razonsocial')->get();
        $hoy = now();
        if ($hoy->dayOfWeek == 1) {
            $fechaAyer = $hoy->copy()->subDays(2)->toDateString();
        } else {
            $fechaAyer = $hoy->copy()->subDay()->toDateString();
        }

        $idUsuario = auth()->user()->id;
        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $mostrarVista = true;

        $ultimoRegistro = DB::table('cajacentral')
            ->where('usuarioregistroid', $idUsuario)
            ->orderBy('created_at', 'desc')
            ->first();

        $registroCierreCaja = true;

        if ($ultimoRegistro) {
            $fechaUltimoRegistro = Carbon::parse($ultimoRegistro->created_at)->toDateString();

            if ($fechaUltimoRegistro !== $hoy->toDateString()) {
                $registroCierreCaja = DB::table('cierrecaja')
                    ->where('usuariocierreid', $idUsuario)
                    ->whereDate('created_at', $fechaUltimoRegistro)
                    ->exists();
            }
        }

        $codigoAprobacion = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.ingreso.index')
            ->where('estado', 'expirado')
            ->exists();

        $mostrarVista = $registroCierreCaja || $codigoAprobacion;


        return view('admin.caja.egreso.cajaegresoscomprobantes', [
            'mostrarVista' => $mostrarVista,
            'sucursal' => $sucursal,
            'consolidados' => $consolidados,
            'bancos' => $bancos,
            'siguienteId' => $siguienteId,
            'rolusuario' => $rolusuario,
            'proveedores' => $proveedores,
            'proveedoresservicios' => $proveedoresservicios,
            'cuentas' => $cuentas
        ]);
    }
    public function buscarPorProveedoregresocomprobantes(Request $request)   
    {
        $entrada = $request->input('proveedorid');
        $nrofactura = $request->input('nrofactura');
        $nrofactura2 = $request->input('nrofactura2');
        $nrofactura3 = $request->input('nrofactura3');
        $tipoproveedor = $request->input('tipocliente');

        if (!in_array($tipoproveedor, ['medico', 'proveedor'])) {
            return response()->json(['error' => 'Tipo de cliente no válido'], 400);
        }

        $proveedorNit = null;
        $proveedorCi = null;
        switch ($tipoproveedor) {
            case 'medico':
                $proveedor = Proveedor::where('proveedor', $entrada)
                    ->orWhere('proveedor', 'LIKE', "%{$entrada}%")
                    ->first(['id', 'proveedor', 'proveedor']);
                break;
            case 'proveedor':
                $proveedor = Proveedoresservicios::where('razonsocial', $entrada)
                    ->orWhere('razonsocial', 'LIKE', "%{$entrada}%")
                    ->first(['id', 'razonsocial']);
                break;
            default:
                $proveedor = null;
        }

        if (!$proveedor) {
            return response()->json(['error' => 'Proveedor no encontrado'], 404);
        }

        $proveedorNit = $proveedor->nit;
        $proveedorCi = $proveedor->ci;

        $registrosProgramacion = ProgramacionSubCliente::where('proveedorNombre', $proveedor->proveedor)
            ->whereNull('deleted_at')
            ->whereNotNull('comprobante')
            ->where(function ($query) use ($nrofactura, $nrofactura2, $nrofactura3) {
                if ($nrofactura) {
                    $query->orWhere('nrofactura', $nrofactura);
                }
                if ($nrofactura2) {
                    $query->orWhere('nrofactura', $nrofactura2);
                }
                if ($nrofactura3) {
                    $query->orWhere('nrofactura', $nrofactura3);
                }
            })
            ->where('pagoatencion', null)
            ->where(function ($query) {
                $query->where('preciocompra', '>', 0)
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNotNull('preciocompra')
                                ->where('preciocompra', '!=', '0.00')
                                ->where('preciocompra', '!=', '0,00')
                                ->where('preciocompra', '!=', '0');
                    });
            })
            ->get()
            ->map(function ($registro) {

                $detallerecibo = Detallerecibo::where('programacionid', $registro->id) 
                    ->where('tipomovimiento', 'EGRESO')
                    ->latest('created_at')
                    ->first();

                if ($detallerecibo) {
                    if ($detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'EGRESO') {
                        return null;
                    }

                    if ($detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'EGRESO') {
                        $registro->preciocompra = $detallerecibo->saldo;
                    }
                }

                $existeDocumentacion = DocumentacionSubCliente::where(function ($query) use ($registro) {
                    if (!is_null($registro->clienteitaid)) {
                        $query->where('clienteitaid', $registro->clienteitaid);
                    } elseif (!is_null($registro->clienteauditoriaid)) {
                        $query->where('clienteauditoriaid', $registro->clienteauditoriaid);
                    } elseif (!is_null($registro->clientebancoid)) {
                        $query->where('clientebancoid', $registro->clientebancoid);
                    }
                })
                ->where('accion', $registro->accionnombre)
                ->where('fechabateria', $registro->fechabateria)
                ->exists();
                
                // Aplicar la restricción **solo si NO es clientecomunid**
                if (!$existeDocumentacion && is_null($registro->clientecomunid)) {
                    return null;
                }
                
            
                switch (true) {
                    case isset($registro->clienteitaid):
                        $registro->tramite = TramitesubCliente::where('clienteitaid', $registro->clienteitaid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;

                    case isset($registro->clienteid):
                        $registro->tramite = TramitesubCliente::where('clienteid', $registro->clienteid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;

                    case isset($registro->clientecomunid):
                        $registro->tramite = TramitesubCliente::where('clientecomunid', $registro->clientecomunid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;

                    case isset($registro->clienteauditoriaid):
                        $registro->tramite = TramitesubCliente::where('clienteauditoriaid', $registro->clienteauditoriaid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;
                }
                return $registro;
            })
            ->filter()
        ->values();

        // Obtener registros de la tabla ProveedorInformesFinales
        $registrosProveedorInformesFinales = ProveedorInformefinal::where('proveedorasignado', $proveedor->proveedor)
            ->whereNull('deleted_at')
            ->whereNotNull('comprobante')
            ->where(function ($query) use ($nrofactura, $nrofactura2, $nrofactura3) {
                // Filtramos los números de factura solo si no están vacíos
                if ($nrofactura) {
                    $query->orWhere('nrofactura', $nrofactura);
                }
                if ($nrofactura2) {
                    $query->orWhere('nrofactura', $nrofactura2);
                }
                if ($nrofactura3) {
                    $query->orWhere('nrofactura', $nrofactura3);
                }
            })
            ->where(function ($query) {
                $query->where('preciocompra', '>', 0)
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNotNull('preciocompra')
                                ->where('preciocompra', '!=', '0.00')
                                ->where('preciocompra', '!=', '0,00')
                                ->where('preciocompra', '!=', '0');
                    });
            })
            ->get()
            ->map(function ($registro) {
                $detallerecibo = Detallerecibo::where('provinfofinalid', $registro->id) 
                    ->latest('created_at')
                    ->first();

                if ($detallerecibo) {
                    if ($detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'EGRESO') {
                        return null;
                    }

                    if ($detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'EGRESO') {
                        $registro->preciocompra = $detallerecibo->saldo;
                    }
                }

                $existeDocumentacion = Informefinal::where(function ($query) use ($registro) {
                        if (!is_null($registro->clienteitaid)) {
                            $query->where('clienteitaid', $registro->clienteitaid);
                        } elseif (!is_null($registro->clienteauditoriaid)) {
                            $query->where('clienteauditoriaid', $registro->clienteauditoriaid);
                        } elseif (!is_null($registro->clientebancoid)) {
                            $query->where('clientebancoid', $registro->clientebancoid);
                        }
                    })
                    ->where('fechabateria', $registro->fechabateria)
                    ->exists();
                
                if (!$existeDocumentacion) {
                    return null;
                }
            
                // Obtener todos los trámites agrupados por 'fechabateria'
                $tramitesPorFecha = TramitesubCliente::where('fechabateria', $registro->fechabateria)
                ->orderBy('id') // Ordena para una asignación consistente
                ->get()
                ->groupBy('fechabateria');

                // Lista de trámites disponibles para la `fechabateria`
                $tramites = $tramitesPorFecha[$registro->fechabateria] ?? collect();

                // Contador de registros por fecha para alternar los trámites
                static $contador = [];

                // Asegurar índice para asignar un trámite diferente en cada iteración
                $contador[$registro->fechabateria] = ($contador[$registro->fechabateria] ?? 0) % max(1, $tramites->count());

                switch (true) {
                case isset($registro->clienteitaid):
                    $registro->tramite = optional($tramites->where('clienteitaid', $registro->clienteitaid)->values()->get($contador[$registro->fechabateria]))->tramite;
                    break;

                case isset($registro->clienteid):
                    $registro->tramite = optional($tramites->where('clienteid', $registro->clienteid)->values()->get($contador[$registro->fechabateria]))->tramite;
                    break;

                case isset($registro->clientecomunid):
                    $registro->tramite = optional($tramites->where('clientecomunid', $registro->clientecomunid)->values()->get($contador[$registro->fechabateria]))->tramite;
                    break;

                case isset($registro->clienteauditoriaid):
                    $registro->tramite = optional($tramites->where('clienteauditoriaid', $registro->clienteauditoriaid)->values()->get($contador[$registro->fechabateria]))->tramite;
                    break;
                }

                // Incrementamos el contador para el siguiente registro de la misma `fechabateria`
                $contador[$registro->fechabateria]++;

                return $registro;

            })
            ->filter()
        ->values();

        $registrosCuentasporPagar = CuentasPagar::where('proveedorNombre', $proveedor->razonsocial)
            ->whereNull('deleted_at')
            ->whereNotNull('comprobante')
            ->get()
            ->map(function ($registro) {

                $detallerecibo = Detallerecibo::where('cuentapagarid', $registro->id) 
                    ->latest('created_at')
                    ->first();

                if ($detallerecibo) {
                    if ($detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'EGRESO') {
                        return null;
                    }

                    if ($detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'EGRESO') {
                        $registro->preciocompra = $detallerecibo->saldo;
                    }
                }
                return $registro;
            })
            ->filter()
        ->values();


        $registros = $registrosProgramacion
            ->merge($registrosProveedorInformesFinales)
            ->merge($registrosCuentasporPagar);


        return response()->json([
            'proveedor' => $proveedor,
            'registros' => $registros
        ]);
    }
    public function guardarCajaCentralegresocomprobantes(Request $request)
    {
        $usuarioAutenticadoid = Auth::user()->id;
        $usuarioAutenticadonombre = Auth::user()->name;
    
        $request->validate([
            'tipocliente' => '',
            'clienteid' => '',
            'clientenombre' => '',
            'proveedorid' => '',
            'proveedornombre' => '',
            'subtotal' => '',
            'descuento' => '',
            'montoreal' => '',
            'montototal' => '',
            'ciudadregistro' => '',
            'programacionIds' => '',
            'area' => '',
            'detalle' => '',
            'nrofactura' => '',
            'nrofactura2' => '',
            'nrofactura3' => '',
            'nrobancarizaciondeposito' => '',
            'nrobancarizaciontransferencia' => '',
            'nrobancarizacioncheque' => '',
            'nrocheque' => '',
            'nrotarjeta' => '',
            'nroap' => '',
            'nroref' => '',
            'nrocuentadestinodeposito' => '',
            'nrocuentaorigendeposito' => '',
            'tipobancodeposito' => '',
            'nrocuentadestinotransferencia' => '',
            'nrocuentaorigentransferencia' => '',
            'tipobancotransferencia' => '',
            'tipobancocheque' => '',
            'nrocuentadestinocheque' => '',
            'tipobanco' => '',
            'tipocambio' => '',
            'tipomovimiento' => '',
            'tipotransaccion' => '',
            'tipotransaccion2' => '',
            'ciudadregistro' => '',
            'nombrebanco' => '',
            'numerobanco' => '',
            'usuarioregistroid' => '',
            'usuarioregistronombre' => '',
        ]);

        if (empty($request->programacionIds)) {
            // Redirigir con el mensaje en la sesión
            return redirect()->back()->with('infoerror', 'Debes seleccionar al menos un registro');
        }

        $programacionIds = explode(',', $request->programacionIds);
        $descuentos = explode(',', $request->descuentos);
        $pagos = explode(',', $request->pagos);

        $tipocliente = match ($request->tipocliente) {
            'proveedor' => 'PROVEEDOR DE SERVICIO',
            'medico' => 'MEDICO',
            'clienteauditoriaid' => 'AUDITORIA',
            'clientecomunid' => 'COMUN',
            'clientebancoid' => 'BANCO',
            default => $request->tipocliente,
        };

        $saldototal = $request->montoreal - ($request->montototal + $request->descuento);
        $saldototal = number_format($saldototal, 2, '.', ''); 

        $estado = ($saldototal == 0) ? 'PAGO PROCESADO' : 'SALDO PENDIENTE';

        $tipotransaccion = $request->tipotransaccion;
        if ($tipotransaccion == 'DEPOSITO_BANCARIO') {
            $tipotransaccion = 'DEPOSITO BANCARIO';
        } elseif ($tipotransaccion == 'TRANSFERENCIA_BANCARIA') {
            $tipotransaccion = 'TRANSFERENCIA BANCARIA';
        } elseif ($tipotransaccion == 'RETIRO_BANCARIO') {
            $tipotransaccion = 'RETIRO BANCARIO';
        } elseif (in_array($tipotransaccion, ['EFECTIVO', 'CHEQUE', 'ATC'])) {
        }

        $tipotransaccion2 = $request->tipotransaccion2;
        if ($tipotransaccion2 == 'DEPOSITO_BANCARIO') {
            $tipotransaccion2 = 'DEPOSITO BANCARIO';
        } elseif ($tipotransaccion2 == 'TRANSFERENCIA_BANCARIA') {
            $tipotransaccion2 = 'TRANSFERENCIA BANCARIA';
        } elseif (in_array($tipotransaccion2, ['EFECTIVO', 'CHEQUE', 'ATC'])) {
        }

        $tipomovimiento = 'EGRESO';
        $recibo = Recibo::create([
            'ciudadregistro' => $request->ciudadregistro,
            'usuarioregistroid' => $usuarioAutenticadoid,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
            'tipocliente' => $tipocliente,
            'proveedorid' => $request->proveedorid,
            'proveedornombre' => $request->proveedornombre,
            'tipomovimiento' => 'EGRESO',
            'subtotal' => $request->subtotal,
            'descuentototal' => $request->descuento,
            'montototal' => $request->montototal,
            /* 'estado' => ($tipocliente === 'PROVEEDOR DE SERVICIO' && $tipomovimiento === 'EGRESO') ? 'PAGO PROCESADO' : $estado, */
            'estado' => $estado,
            'saldototal' => $saldototal,
        ]);


        // Obtener el HTML del recibo desde la solicitud
        $html = $request->input('html_recibo');
        
        if (!$html) {
            return redirect()->back()->with('error', 'No se recibió HTML para generar el recibo');
        }

        // Obtener el ID del usuario autenticado
        $usuarioAutenticadoid = Auth::id();  // O como estés obteniendo el ID del usuario

        // Nombre del archivo con timestamp
        $nombreArchivo = 'recibo_' . time() . '.html';

        // Ruta del archivo donde lo queremos guardar
        $rutaDirectorio = public_path('documentacioncaja/egresos/' . $usuarioAutenticadoid);

        // Verificar si la ruta existe y si no, crearla
        if (!File::exists($rutaDirectorio)) {
            File::makeDirectory($rutaDirectorio, 0777, true);  // Crear directorios de forma recursiva
        }

        // Ruta completa para guardar el archivo
        $rutaArchivo = $rutaDirectorio . '/' . $nombreArchivo;

        // Guardar el HTML en un archivo
        File::put($rutaArchivo, $html);

        Cajacentral::create([
            'tipocliente' => $tipocliente,
            'proveedorid' => $request->proveedorid,
            'proveedornombre' => $request->proveedornombre,
            'subtotal' => $request->subtotal,
            'descuento' => $request->descuento,
            'montototal' => $request->montototal,
            'nrorecibo' => $recibo->id,
            'saldo' => $saldototal,
            'area' =>  $request->area,
            /* 'estado' => $request->area === 'CUENTA POR PAGAR' ? 'PAGO PROCESADO' : $estado, */
            'estado' => $estado,
            'detalle' => $request->detalle,
            'nrofactura' => $request->nrofactura,
            'nrofactura2' => $request->nrofactura2,
            'nrofactura3' => $request->nrofactura3,
            'nrobancarizaciontransferencia' => $request->nrobancarizaciontransferencia,
            'nrobancarizacioncheque' => $request->nrobancarizacioncheque,
            'nrobancarizaciondeposito' => $request->nrobancarizaciondeposito,
            'nrocheque' => $request->nrocheque,
            'nrotarjeta' => $request->nrotarjeta,
            'nroap' => $request->nroap,
            'nroref' => $request->nroref,
            'nrocuentadestinodeposito' => $request->nrocuentadestinodeposito,
            'nrocuentaorigendeposito' => $request->nrocuentaorigendeposito,
            'tipobancodeposito' => $request->tipobancodeposito,
            'nrocuentadestinotransferencia' => $request->nrocuentadestinotransferencia,
            'nrocuentaorigentransferencia' => $request->nrocuentaorigentransferencia,
            'tipobancotransferencia' => $request->tipobancotransferencia,
            'tipobancocheque' => $request->tipobancocheque,
            'tipobanco' => $request->tipobanco,
            'tipocambio' => $request->tipocambio,
            'tipomovimiento' => 'EGRESO',
            'tipotransaccion' => $tipotransaccion,
            'tipotransaccion2' => $tipotransaccion2,
            'ciudadregistro' => $request->ciudadregistro,
            'nombrebanco' => $request->nombrebanco,
            'numerobanco' => $request->numerobanco,
            'usuarioregistroid' => $usuarioAutenticadoid,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
            'estadorevisioncierre' => 'DOCUMENTACION PENDIENTE',
            'docrespaldoegreso' => $nombreArchivo,
            'nrocuentadestinocheque' => ($tipotransaccion === 'CHEQUE') ? 3000189269 : null,
        ]);

        foreach ($programacionIds as $index => $programacionId) {
            /* $programacion = ProgramacionSubCliente::find($programacionId);
            $proveedor = ProveedorInformeFinal::find($programacionId);
            $cuentapagar = CuentasPagar::where('id', $programacionId)->first(); */
            $programacion = null;
            $proveedor = null;
            $cuentapagar = null;
            if (str_ends_with($programacionId, 'CP')) {
                $cuentapagar = CuentasPagar::find($programacionId);
            }
            if (!$cuentapagar) {
                $programacion = ProgramacionSubCliente::find($programacionId);
                if (!$programacion) {
                    $proveedor = ProveedorInformeFinal::find($programacionId);
                }
            }
        
            $ultimoDetalleRecibo = Detallerecibo::where(function ($query) use ($programacionId) {
                $query->where('programacionid', $programacionId)
                      ->orWhere('provinfofinalid', $programacionId)
                      ->orWhere('cuentapagarid', $programacionId);
            })
            ->where('tipomovimiento', 'EGRESO')
            ->orderBy('created_at', 'desc')
            ->first();
        

            if ($ultimoDetalleRecibo) {
                $subtotalDetalle = $ultimoDetalleRecibo->saldo;
                $descuentoDetalle = $descuentos[$index];
                $pagoDetalle = $pagos[$index];
            } else {
                if ($programacion) {
                    $subtotalDetalle = $programacion->preciocompra;
                } elseif ($proveedor) {
                    $subtotalDetalle = $proveedor->preciocompra;
                } elseif ($cuentapagar) {
                    /* $subtotalDetalle = $cuentapagar->preciocompra; */
                     $subtotalDetalle = (!empty($cuentapagar->preciocompra) && (float)$cuentapagar->preciocompra > 0) 
                            ? $cuentapagar->preciocompra 
                            : $cuentapagar->subtotal;
                } else {
                    $subtotalDetalle = 0;
                }
                $descuentoDetalle = $descuentos[$index];
                $pagoDetalle = $pagos[$index];
            }

            $subtotalDetalle = is_numeric($subtotalDetalle) ? floatval($subtotalDetalle) : 0;

            $saldoDetalle = $subtotalDetalle - $descuentoDetalle - $pagoDetalle;
            $saldoDetalle = number_format($saldoDetalle, 2, '.', '');

            $estadoDetalle = ($saldoDetalle == 0) ? 'PAGO PROCESADO' : 'SALDO PENDIENTE';

            if ($estadoDetalle == 'PAGO PROCESADO') {
                Detallerecibo::where('programacionid', $programacionId)
                ->orwhere('provinfofinalid', $programacionId)
                ->orwhere('cuentapagarid', $programacionId)
                    ->where('estado', '!=', 'PAGO PROCESADO')
                    ->update(['estado' => 'PAGO PROCESADO']);
            }

            if ($programacion) {
                $area = 'MEDICA';
            } elseif ($proveedor) {
                $area = 'INFORME FINAL';
            } elseif ($cuentapagar) {
                $area = 'CUENTA POR PAGAR';
            }

            $detalleRecibo = Detallerecibo::create([
                'reciboid' => $recibo->id,
                'programacionid' => $programacion ? $programacionId : null,
                'provinfofinalid' => $proveedor ? $programacionId : null,
                'cuentapagarid' => $cuentapagar ? $programacionId : null,
                'usuarioregistroid' => $usuarioAutenticadoid,
                'usuarioregistronombre' => $usuarioAutenticadonombre,
                'clienteid' => $cuentapagar ? null : (
                    $programacion ? 
                        ($programacion->clienteitaid ?? $programacion->clienteauditoriaid ?? $programacion->clientecomunid) : 
                        ($proveedor ? 
                            ($proveedor->clienteitaid ?? $proveedor->clienteauditoriaid ?? $proveedor->clientecomunid) : 
                        null)
                ),

                'clientenombre' => $cuentapagar ? null : (
                        $programacion ? 
                            ($programacion->clienteitanombre ?? $programacion->clienteauditorianombre ?? $programacion->clientecomunnombre) : 
                            ($proveedor ? 
                                ($proveedor->clienteitanombre ?? $proveedor->clienteauditorianombre ?? $proveedor->clientecomunnombre) : 
                            null)
                    ),

                'area' => $area,
                'detalle' => $cuentapagar
                ? ($cuentapagar->cantidad > 0 
                    ? $cuentapagar->cantidad . ' ' . $cuentapagar->detalleproducto 
                    : $cuentapagar->detalleproducto)
                : ($programacion->accionnombre ?? $proveedor->accionnombre ?? null),

                'fechabateria' => $cuentapagar ? $cuentapagar->fechaasignada : ($programacion ? $programacion->fechabateria : $proveedor->fechabateria),
                'fechaatencion' => $cuentapagar ? $cuentapagar->fechaasignada : ($programacion ? $programacion->fechaasignada : $proveedor->fechaasignada),
                'servicio' => $cuentapagar ? null : ($programacion ? $programacion->servicio : $proveedor->servicio),
                'proveedoratencion' => $cuentapagar ? $cuentapagar->proveedornombre : ($programacion ? $programacion->proveedornombre : $proveedor->proveedorasignado),
                'subtotal' => $subtotalDetalle,
                'descuento' => $descuentoDetalle,
                'bateriaid' => $programacion ? $programacion->bateriaid : null,
                'montototal' => $pagoDetalle,
                'saldo' => $saldoDetalle,
                'ordenid' => $cuentapagar ? $cuentapagar->ordenid : null,
                'estado' => ($cuentapagar && $cuentapagar->ordenid) ? 'PAGO PROCESADO' : $estadoDetalle,
                'tipomovimiento' => 'EGRESO',
                'tipotransaccion' => $tipotransaccion,
                'sucursalgasto' => ($area === 'CUENTA POR PAGAR') ? $cuentapagar->sucursalgasto : null,
                'comprobante' => $cuentapagar ? $cuentapagar->comprobante : ($programacion ? $programacion->comprobante : $proveedor->comprobante),
                'factura' => $cuentapagar ? $cuentapagar->factura : ($programacion ? $programacion->factura : $proveedor->factura),
            ]);

            if ($detalleRecibo->cuentapagarid) {
                CuentasPagar::whereIn('id', explode(',', $detalleRecibo->cuentapagarid))
                    ->update(['estado' => $detalleRecibo->estado]);
            }

                CajaCentral::where('nrorecibo', $detalleRecibo->reciboid)
                ->update([
                    'doccomprobante' => $detalleRecibo->comprobante,
                    'docfactura'     => $detalleRecibo->factura
                ]);
                if ($detalleRecibo->programacionid) {
                $programacionIds = explode(',', $detalleRecibo->programacionid);

                // Obtener los bateriaid relacionados desde programacionsubclientes
                $bateriaIds = Programacionsubcliente::whereIn('id', $programacionIds)
                    ->pluck('bateriaid')
                    ->unique()
                    ->toArray();

                // Actualizar bateriasubclientes
                Bateriasubcliente::whereIn('id', $bateriaIds)
                    ->update(['prioridad' => $detalleRecibo->estado]);
            }
            if ($detalleRecibo->provinfofinalid) {
                Bateriasubcliente::where('provinfofinalid', $detalleRecibo->provinfofinalid)
                    ->update(['prioridad' => $detalleRecibo->estado]);
            }


        }

        switch ($tipotransaccion) {
            case 'DEPOSITO BANCARIO':
                $columna = 'consolidadodeposito';
                break;
            case 'TRANSFERENCIA BANCARIA':
                $columna = 'consolidadotransferencia';
                break;
            case 'CHEQUE':
                $columna = 'consolidadocheque';
                break;
            case 'ATC':
                $columna = 'consolidadoatc';
                break;
            case 'RETIRO BANCARIO':
                $columna = 'consolidadotransferencia';
                break;
            case 'EFECTIVO':
                return redirect()->route('admin.caja.egreso.cajaegresos')
            ->with('info', 'Registro guardado correctamente')
            ->with('montototal', $request->montototal)
            ->with('tipotransaccion', $request->tipotransaccion)
            ->with('tipotransaccion2', $request->tipotransaccion2);
            default:
                return response()->json(['error' => 'Tipo de transacción no válido.'], 400);
        }

        $consolidado = Consolidadocaja::where('usuarioconsolidadoid', $usuarioAutenticadoid)->first();

        if ($consolidado) {
            $consolidado->$columna -= $request->montototal;
            $consolidado->save();
        } else {
            $consolidado = new Consolidadocaja();
            $consolidado->usuarioconsolidadoid = $usuarioAutenticadoid;
            $consolidado->$columna = $request->montototal;
            $consolidado->save();
        }

        return redirect()->route('admin.caja.egreso.cajaegresoscomprobantes')
            ->with('info', 'Registro guardado correctamente')
            ->with('montototal', $request->montototal)
            ->with('tipotransaccion', $request->tipotransaccion)
            ->with('tipotransaccion2', $request->tipotransaccion2);
    }


    public function guardarArqueoegreso(Request $request)
    {
        $request->validate([
            'billetecorte200' => 'required|integer|min:0',
            'billetecorte100' => 'required|integer|min:0',
            'billetecorte50' => 'required|integer|min:0',
            'billetecorte20' => 'required|integer|min:0',
            'billetecorte10' => 'required|integer|min:0',
            'monedacorte5' => 'required|integer|min:0',
            'monedacorte2' => 'required|integer|min:0',
            'monedacorte1' => 'required|integer|min:0',
            'monedacorte050' => 'required|integer|min:0',
            'monedacorte020' => 'required|integer|min:0',
            'monedacorte010' => 'required|integer|min:0',
            'montototal' => 'required|numeric|min:0',
        ]);

        $usuarioAutenticadoid = Auth::id();

        $arqueo = ArqueoCaja::where('usuarioarqueoid', $usuarioAutenticadoid)->first();

        if ($arqueo) {
            $arqueo->update([
                'billetecorte200' => $arqueo->billetecorte200 - $request->billetecorte200,
                'billetecorte100' => $arqueo->billetecorte100 - $request->billetecorte100,
                'billetecorte50' => $arqueo->billetecorte50 - $request->billetecorte50,
                'billetecorte20' => $arqueo->billetecorte20 - $request->billetecorte20,
                'billetecorte10' => $arqueo->billetecorte10 - $request->billetecorte10,
                'monedacorte5' => $arqueo->monedacorte5 - $request->monedacorte5,
                'monedacorte2' => $arqueo->monedacorte2 - $request->monedacorte2,
                'monedacorte1' => $arqueo->monedacorte1 - $request->monedacorte1,
                'monedacorte050' => $arqueo->monedacorte050 - $request->monedacorte050,
                'monedacorte020' => $arqueo->monedacorte020 - $request->monedacorte020,
                'monedacorte010' => $arqueo->monedacorte010 - $request->monedacorte010,
            ]);

            $consolidado = Consolidadocaja::where('usuarioconsolidadoid', $usuarioAutenticadoid)->first();

            if ($consolidado) {
                $consolidado->update([
                    'consolidadoefectivo' => $consolidado->consolidadoefectivo - number_format($request->montototal, 2, '.', ''),
                ]);
            }

            return redirect()->route('admin.caja.egreso.cajaegresos')->with('info', 'Registro guardado correctamente');
        } else {

            return redirect()->route('admin.caja.egreso.cajaegresos')->with('infoerror', 'ERROR AL GUARDAR EL ARQUEO');
        }
    }
//

// DOCUMENTACION RESPALDO EGRESOS
    public function respaldodocumentacionegreso(Request $request) 
    {
        $userId = auth()->id();  // Obtener el ID del usuario autenticado
        $rolUsuario = auth()->user()->getRoleNames()->first();
        $fecha = $request->input('fecha', today()->toDateString());
        $usuarios = Consolidadocaja::select('usuarioconsolidadoID', 'usuarioconsolidadoNombre')
                                    ->distinct()
                                    ->get();
        $usuarioSeleccionado = $request->input('usuario', $userId);

        $registros = CajaCentral::where('tipomovimiento', 'EGRESO')
                                ->whereDate('created_at', $fecha)
                                ->when($usuarioSeleccionado, function ($query, $usuarioSeleccionado) {
                                    return $query->where('usuarioRegistroID', $usuarioSeleccionado);
                                })
                                ->get();

        return view('admin.caja.egreso.documentacionegreso', compact('registros', 'rolUsuario', 'usuarios', 'fecha', 'usuarioSeleccionado'));  // Pasar los registros a la vista
    }
    public function actualizarEstadoegreso(Request $request)
    {
        // Obtener los IDs seleccionados
        $ids = $request->input('registro_ids');

        // Verificar si hay IDs seleccionados
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No se seleccionaron registros para actualizar.');
        }

        // Actualiza el estado en la tabla cajacentral
        Cajacentral::whereIn('id', $ids)->update([
            'estadorevisioncierre' => 'DOCUMENTACION PENDIENTE',
            'docrespaldoegreso' => null,
        ]);

        return redirect()->back()->with('info', 'Estado actualizado exitosamente.');
    }
    public function guardarRespaldoegreso(Request $request)
    {
        $request->validate([
            'registro_ids' => 'required|array|min:1',
            'archivo2' => '',
            'archivo3' => '',
        ]);

        $userId = auth()->id();
        
        $archivo_name2 = null;
            if ($request->hasFile('archivo2')) {
                $file = $request->file('archivo2');
                $carpetaCliente = public_path("/documentacioncaja/egresos/$userId");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }
                $archivo_name2 = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name2);
            }

        $archivo_name3 = null;
            if ($request->hasFile('archivo3')) {
                $file = $request->file('archivo3');
                $carpetaCliente = public_path("/documentacioncaja/egresos/$userId");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }
                $archivo_name3 = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name3);
            }

        foreach ($request->registro_ids as $id) {
            $registro = CajaCentral::find($id);
            if ($registro) {
                if ($registro->estadorevisioncierre !== 'FINALIZADO') {
                    $registro->estadorevisioncierre = 'RESPALDADO';
                }
                $registro->docfactura = $archivo_name2 ?? $registro->docfactura;
                $registro->doccomprobante = $archivo_name3 ?? $registro->doccomprobante;
                $registro->save();
            }
        }


        return redirect()->back()->with('info', 'Registro respaldado exitosamente.');
    }
//

// CUENTAS POR PAGAR
    public function cppregistradas(Request $request)
    {
        $nombreproveedor = $request->get('buscarpor');

        $cuentaspagar = CuentasPagar::where('proveedornombre', 'LIKE', "%$nombreproveedor%")
                          ->orderBy('proveedornombre')
                          ->simplePaginate(1000);

        return view('admin.caja.cuentaspagar.cppregistradas', compact('cuentaspagar'));
    }
    public function registrarcpp(Request $request)
    {
        return view('admin.caja.cuentaspagar.registrarcpp');
    }
    public function obtenerProveedores(Request $request)
    {
        $tipoProveedor = $request->tipoProveedor;

        if ($tipoProveedor === 'MEDICO') {
            $proveedores = DB::table('proveedores')
                ->select('id as proveedor_id', 'proveedor as proveedor_nombre')
                ->get();
        } else {
            $proveedores = DB::table('proveedoresservicios')
                ->where('tipoPersonal', $tipoProveedor)
                ->select('id as proveedor_id', 'nombreCompleto as proveedor_nombre')
                ->get();
        }

        return response()->json($proveedores);
    }
    public function guardarCuentaPagar(Request $request) 
    {
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'tipoproveedor' => 'required',
            'proveedorid' => 'required',
            'proveedornombre' => 'required',
            'proveedornombre_text' => '',
            'detalle' => 'required',
            'fechaasignada' => 'required',
            'subtotal' => 'required',
            'descuentosancion' => '',
            'descuentoafp' => '',
            'montototal' => 'required',
            'usuarioregistroid' => '',
            'usuarioregistronombre' => '',
        ]);

        // Guardar los datos en la tabla
        CuentasPagar::create([
            'tipoproveedor' => $validatedData['tipoproveedor'],
            'proveedorid' => $validatedData['proveedorid'],
            'proveedornombre' => $validatedData['proveedornombre_text'],
            'detalle' => $validatedData['detalle'],
            'fechaasignada' => $validatedData['fechaasignada'],
            'subtotal' => $validatedData['subtotal'],
            'descuentosancion' => $validatedData['descuentosancion'] ?? 0,
            'descuentoafp' => $validatedData['descuentoafp'] ?? 0,
            'montototal' => $validatedData['montototal'],
            'usuarioregistroid' => $validatedData['usuarioregistroid'],
            'usuarioregistronombre' => $validatedData['usuarioregistronombre'],
            'estado' => 'PENDIENTE',
            'preciocompra' => $validatedData['montototal'],
        ]);

        // Redireccionar a la vista de cuentas registradas
        return redirect()->route('admin.caja.cuentaspagar.cppregistradas')
            ->with('info', 'Cuenta por pagar registrada correctamente.');
    }
    public function cierrecajaegresos(Request $request)
    {
        $usuarioAutenticado = auth()->user();
        $userId = auth()->id();

        $todosFinalizados = CajaCentral::where('usuarioregistroid', $userId)
        ->where('estadorevisioncierre', 'FINALIZADO')
        ->whereDate('updated_at', today());

        $usuariosConsolidados = Consolidadocaja::select('usuarioconsolidadonombre')
            ->groupBy('usuarioconsolidadonombre')
            ->get();

        $usuarioBusqueda = $request->input('usuario_busqueda', null);

        $query = DB::table('cajacentral')
            ->select('id', 'clientenombre', 'area', 'tipotransaccion', 'tipotransaccion2', 'subtotal', 'descuento', 'montototal', 'saldo', 'nrorecibo', 'usuarioregistronombre', 'documentorespaldo', 'estadorevisioncierre')
            ->whereDate('updated_at', today());

        if ($usuarioBusqueda) {
            $query->where('usuarioregistronombre', $usuarioBusqueda);
        }

        $registros = $query->get();

        $consolidados = Consolidadocaja::where('usuarioconsolidadonombre', $usuarioAutenticado->name)
            ->whereDate('updated_at', today())
            ->first();

        $tiposTransaccion = ['Efectivo', 'Cheque', 'ATC', 'Deposito', 'Transferencia'];
        $montosCajaCentral = [];

        foreach ($tiposTransaccion as $tipo) {
            $montosCajaCentral[$tipo] = DB::table('cajacentral')
                ->where('usuarioregistronombre', $usuarioAutenticado->name)
                ->where('tipotransaccion', $tipo)
                ->whereDate('updated_at', today())
                ->sum('montototal');
        }


        $cierrecajas = Cierrecaja::orderBy('created_at', 'desc')
            ->get();


        return view('admin.caja.egreso.cierrecajaegreso', [
            'registros' => $registros,
            'consolidados' => $consolidados,
            'usuarioBusqueda' => $usuarioBusqueda,
            'montosCajaCentral' => $montosCajaCentral,
            'tiposTransaccion' => $tiposTransaccion,
            'usuariosConsolidados' => $usuariosConsolidados,
            'todosFinalizados' => $todosFinalizados,
            'cierrecajas' => $cierrecajas,
        ]);
    }
    public function listacuentaspagar(Proveedor $proveedor, Request $request)
    {
        $fechas = Bateriasubcliente::pluck('fechabateria')->unique()->sort()->toArray();

        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;

        $userRole = auth()->user()->getRoleNames()->first(); 
        
        $query = Bateriasubcliente::with(['estadoprogramacionsubcliente', 'documentacionsubcliente', 'programacionsubcliente','informesfinales','pagoservicio','pagoservicioinformefinal','provinfofinal',
            'estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria', 'programacionsubclienteauditoria','informesfinalesauditoria','provinfofinalauditoria',
            'estadoprogramacionsubclientecomun', 'documentacionsubclientecomun', 'programacionsubclientecomun','informesfinalescomun','provinfofinalcomun','tramitesubclienteita','tramitesubclienteauditoria','tramitesubclientecomun'])
            ->whereNotNull('proveedorasignado')
            ->where('preciocompra', '!=', NULL)
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)
            ->where('pagoservicio','<>', 'EXTERNO')
            ->where('proveedorasignado', '<>', 'DIAGNOSTICO MEDICO POR IMAGEN DMI') 
            ->where('proveedorasignado', '<>', 'PROVEEDOR AJENO') 
        ->orderBy('proveedorasignado');

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('proveedorasignado', function ($q) use ($request) {
                $q->where('proveedorasignado', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        $bateriaproveedores = $query->get();
        $grouped = $bateriaproveedores->groupBy(function($item) {
            return $item->proveedorasignado;
        });

        $result = [];
        foreach ($grouped as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {

                    $estadoProgramacion = collect([
                            $item->estadoprogramacionsubcliente, 
                            $item->estadoprogramacionsubclienteauditoria, 
                            $item->estadoprogramacionsubclientecomun
                        ])->filter();
                        
                        $resultadoestado = $estadoProgramacion
                            ->flatMap(function ($estadoprogramacion) { 
                                return $estadoprogramacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();  

                    $programaciones = collect([
                            $item->programacionsubcliente, 
                            $item->programacionsubclienteauditoria, 
                            $item->programacionsubclientecomun
                        ])->filter();
                    
                        $resultadoprog = $programaciones
                            ->flatMap(function ($programacion) { 
                                return $programacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();                    

                    $informesubido = collect([
                            $item->documentacionsubcliente, 
                            $item->documentacionsubclienteauditoria, 
                            $item->documentacionsubclientecomun
                        ])->filter();
                        
                        $resultadoinforme = $informesubido
                            ->flatMap(function ($informe) { 
                                return $informe;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accion', $item->accionnombre)
                    ->first();           

                    $informefinalsubido = collect([
                            $item->informesfinales, 
                            $item->informesfinalesauditoria, 
                            $item->informesfinalescomun
                        ])->filter();
                        
                        $resultadoinformefinal = $informefinalsubido
                            ->flatMap(function ($informefinal) { 
                                return $informefinal;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $provinformes = collect([
                            $item->provinfofinal, 
                            $item->provinfofinalauditoria, 
                            $item->provinfofinalcomun
                        ])->filter();
                        
                        $resultadoprovinformes = $provinformes
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first(); 
                    
                    $provinformes2 = collect([
                            $item->provinfofinal, 
                            $item->provinfofinalauditoria, 
                            $item->provinfofinalcomun
                        ])->filter();
                        
                        $resultadoprovinformes2 = $provinformes2
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('id', $item->provinfofinalid)
                    ->first();

                    $tramitesubcliente = collect([
                            $item->tramitesubclienteita, 
                            $item->tramitesubclienteauditoria, 
                            $item->tramitesubclientecomun
                        ])->filter();
                        
                        $resultadotramitesubcliente = $tramitesubcliente
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();

                    $resultadopagoinformefinal = $item->pagoservicioinformefinal()
                        ->where('provinfofinalid', $item->provinfofinalid)
                        ->where('tipomovimiento', 'EGRESO')
                    ->first();

                    $pagobateria = collect([
                        $item->programacionsubcliente,
                        $item->programacionsubclienteauditoria,
                        $item->programacionsubclientecomun
                    ])->filter();
                    
                    $resultadopago = $pagobateria
                        ->flatMap(fn($pago) => $pago)
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                    ->first();

                    $preciocompra = $item->preciocompra;
                    $pagoservicioinforme = null;

                    if ($resultadopago) {
                        $programacionId = $resultadopago->id;

                        $detallerecibo = Detallerecibo::where('programacionid', $programacionId)
                            ->where('tipomovimiento', 'EGRESO')
                            ->orderByDesc('id')
                            ->first();

                        if ($detallerecibo) {
                            if ($detallerecibo->estado === 'PAGO PROCESADO') {
                                $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                            } elseif ($detallerecibo->estado === 'SALDO PENDIENTE') {
                                $pagoservicioinforme = 'SALDO PENDIENTE';
                                $preciocompra = $detallerecibo->saldo ?? $item->preciocompra;
                            }
                        } else {
                            $pagoservicioinforme = $resultadopago->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : 'PENDIENTE';
                        }
                    } else {
                        $pagoservicioinforme = 'PENDIENTE';
                    }

                $fechaAtencion = $resultadoestado ? $resultadoestado->fechaatencionprogramacion : null;
                $fechaprogramacion = $resultadoprog ? $resultadoprog->fechaasignada : null;
                $idprogramacion = $resultadoprog ? $resultadoprog->id : null;
                $nrofacturaprog = $resultadoprog ? $resultadoprog->nrofactura : null;
                $documentofactura = $resultadoprog ? $resultadoprog->factura : null;
                $informedocumentacion = $resultadoinforme ? $resultadoinforme->created_at->toDateString() : null;
                $informedocumentacionfinal = $resultadoinformefinal ? $resultadoinformefinal->created_at->toDateString() : null;
                $pagoservicioinformefinal = in_array($item->id, [3173, 3178, 3187, 3043]) 
                ? 'PROCESADO' 
                : ($resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null);
                $nrofacturainformefinal = $resultadoprovinformes2 ? $resultadoprovinformes2->nrofactura : null;
                $facturainformefinal = $resultadoprovinformes2 ? $resultadoprovinformes2->factura : null;
                $tramiteinformefinal = $resultadoprovinformes2 ? $resultadoprovinformes2->servicio : null;
                $tramitecliente = $resultadotramitesubcliente ? $resultadotramitesubcliente->tramite : null;

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'pagoservicio' => $item->pagoservicio,
                    'preciocompra' => $preciocompra,
                    'clienteitaid' => $item->clienteitaid,
                    'clienteitanombre' => $item->clienteitanombre,
                    'clienteauditoriaid' => $item->clienteauditoriaid,
                    'clienteauditorianombre' => $item->clienteauditorianombre,
                    'clientecomunid' => $item->clientecomunid,
                    'clientecomunnombre' => $item->clientecomunnombre,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechaprogramacion' => $fechaprogramacion,
                    'informedocumentacion' => $informedocumentacion,
                    'informedocumentacionfinal' => $informedocumentacionfinal,
                    'pagoservicioinforme' => $pagoservicioinforme,
                    'pagoservicioinformefinal' => $pagoservicioinformefinal,
                    'idprogramacion' => $idprogramacion,
                    'fechabateria' => $item->fechabateria,
                    'provinfofinalid' => $item->provinfofinalid,
                    'nrofacturaprog' => $nrofacturaprog,
                    'documentofactura' => $documentofactura,
                    'nrofacturainformefinal' => $nrofacturainformefinal,
                    'facturainformefinal' => $facturainformefinal,
                    'tramiteinformefinal' => $tramiteinformefinal,
                    'tramitecliente' => $tramitecliente,
                    'prioridad' => $item->prioridad,
                    'estadoaprobacion' => $item->estadoaprobacion,
                ];
            }
            $result[] = [
                'proveedorasignado' => $item->proveedorasignado,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'fechabateria' => $item->fechabateria,
            ];
        }

        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');
        $user = auth()->user()->name;

        $records = DB::table('bateriasubclientes')
            ->selectRaw("
                COALESCE(fechacredito, fechabateria) as fechabateria,  
                SUM(CASE 
                    WHEN precio IS NULL THEN 0
                    ELSE precio 
                END) as total_ingresos,
                SUM(CASE 
                    WHEN preciocompra IS NULL THEN 0
                    ELSE preciocompra 
                END) as total_egresos
            ")
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)
            ->whereNotNull('preciocompra')
            ->whereYear(DB::raw('COALESCE(fechacredito, fechabateria)'), $year)
            ->whereMonth(DB::raw('COALESCE(fechacredito, fechabateria)'), $month)
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('COALESCE(fechacredito, fechabateria)'))
            ->get();

            if ($request->ajax()) {
                return response()->json($records);
            }

        /* $cuentaspagar = CuentasPagar::all();   */
        $cuentaspagar = CuentasPagar::with('proveedorServicio')->get();

        $registrosbateria = Bateriasubcliente::where('prioridad', 'CUENTA POR PAGAR')
            ->leftJoin('proveedorinformesfinales', 'bateriasubclientes.provinfofinalid', '=', 'proveedorinformesfinales.id')
            ->leftJoin('informesfinales', function ($join) {
                $join->on(function ($q) {
                    $q->whereColumn('bateriasubclientes.clienteitaid', 'informesfinales.clienteitaid')
                    ->orWhereColumn('bateriasubclientes.clienteauditoriaid', 'informesfinales.clienteauditoriaid')
                    ->orWhereColumn('bateriasubclientes.clientecomunid', 'informesfinales.clientecomunid');
                })
                ->whereColumn('bateriasubclientes.fechabateria', 'informesfinales.fechabateria')
                ->whereColumn('proveedorinformesfinales.servicio', 'informesfinales.servicio');
            })
            ->select('bateriasubclientes.*', 'informesfinales.created_at as informe_created_at')
            ->with([
                'programacion' => function ($query) {
                    $query->select('id', 'bateriaid', 'fechaasignada');
                },
                'programacion.documentacion' => function ($query) {
                    $query->select('id', 'programacionid', 'created_at');
                }
            ])
        ->get();
        foreach ($registrosbateria as $registro) {
            $programacion = \App\Models\Programacionsubcliente::where('bateriaid', $registro->id)
                ->orderBy('id', 'desc')
                ->first();

            if ($programacion) {
                $detallerecibo = \App\Models\Detallerecibo::where('programacionid', $programacion->id)
                    ->where('tipomovimiento', 'EGRESO')
                    ->where('estado', 'SALDO PENDIENTE')
                    ->orderByDesc('created_at')
                    ->first();

                if ($detallerecibo && $detallerecibo->saldo > 0) {
                    $registro->preciocompra = $detallerecibo->saldo;
                }
            }
        }

        $proveedoresServicios = Proveedor::pluck('tipoplanilla', 'proveedor');

        /* TOTAL DE CAJA INGRESOS Y EGRESOS DE 3000189269 */
        $totalCuenta1Ingreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '3000189269')
                    ->orWhere('nrocuentadestinodeposito', '3000189269')
                    ->orWhere(function ($subquery) {
                        $subquery->where('nrobancodestinoefectivo', '3000189269')
                                ->whereNotNull('nrobancarizacionefectivo');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('tipotransaccion', 'ATC')
                                ->where('nrocuentadestinoatc', '3000189269')
                                ->whereNotNull('nrobancarizacionatc');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('tipotransaccion', 'CHEQUE')
                                ->where('nrocuentadestinocheque', '3000189269')
                                ->whereNotNull('doccomprobante');
                    });
            })
            ->where('tipomovimiento', 'INGRESO')
            ->where('estado', '!=', 'ANULADO')
            ->sum(DB::raw("CASE 
                            WHEN tipotransaccion = 'ATC' AND nrobancarizacionatc IS NOT NULL 
                            THEN montototal - descuentoATC 
                            ELSE montototal 
        END"));

        $totalCuenta1Egreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '3000189269')
                ->orWhere(function ($subquery) {
                    $subquery->where('tipotransaccion', 'CHEQUE')
                                ->where('nrocuentadestinocheque', '3000189269')
                                ->whereNotNull('doccomprobante');
                });
            })
            ->where('tipomovimiento', 'EGRESO')
            ->where('estado', '!=', 'ANULADO')
        ->sum('montototal');

        /* TOTAL DE CAJA INGRESOS Y EGRESOS DE 2505314878 */
        $totalCuenta2Ingreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '2505314878')
                    ->orWhere('nrocuentadestinodeposito', '2505314878')
                    ->orWhere(function ($subquery) {
                        $subquery->where('nrobancodestinoefectivo', '2505314878')
                                ->whereNotNull('nrobancarizacionefectivo');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('tipotransaccion', 'ATC')
                                ->where('nrocuentadestinoatc', '2505314878')
                                ->whereNotNull('nrobancarizacionatc');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('tipotransaccion', 'CHEQUE')
                                ->where('nrocuentadestinocheque', '2505314878')
                                ->whereNotNull('doccomprobante');
                    });
                })
                ->where('tipomovimiento', 'INGRESO')
                ->where('estado', '!=', 'ANULADO')
                ->sum(DB::raw("CASE 
                    WHEN tipotransaccion = 'ATC' AND nrobancarizacionatc IS NOT NULL 
                    THEN montototal - descuentoATC 
                    ELSE montototal 
        END"));
        
        $totalCuenta2Egreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '2505314878')
                ->orWhere(function ($subquery) {
                    $subquery->where('tipotransaccion', 'CHEQUE')
                            ->where('nrocuentadestinocheque', '2505314878')
                            ->whereNotNull('doccomprobante');
                });
            })
            ->where('tipomovimiento', 'EGRESO')
            ->where('estado', '!=', 'ANULADO')
        ->sum('montototal');

        /* TOTAL DE CAJA INGRESOS Y EGRESOS DE 4011113557 */
        $totalCuenta3Ingreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '4011113557')
                    ->orWhere('nrocuentadestinodeposito', '4011113557')
                    ->orWhere(function ($subquery) {
                        $subquery->where('nrobancodestinoefectivo', '4011113557')
                                ->whereNotNull('nrobancarizacionefectivo');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('tipotransaccion', 'ATC')
                                ->where('nrocuentadestinoatc', '4011113557')
                                ->whereNotNull('nrobancarizacionatc');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('tipotransaccion', 'CHEQUE')
                                ->where('nrocuentadestinocheque', '4011113557')
                                ->whereNotNull('doccomprobante');
                    });
            })
            ->where('tipomovimiento', 'INGRESO')
            ->where('estado', '!=', 'ANULADO')
            ->sum(DB::raw("CASE 
                            WHEN tipotransaccion = 'ATC' AND nrobancarizacionatc IS NOT NULL 
                            THEN montototal - descuentoATC 
                            ELSE montototal 
        END"));

        $totalCuenta3Egreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '4011113557')
                ->orWhere(function ($subquery) {
                    $subquery->where('tipotransaccion', 'CHEQUE')
                                ->where('nrocuentadestinocheque', '4011113557')
                                ->whereNotNull('doccomprobante');
                });
            })
            ->where('tipomovimiento', 'EGRESO')
            ->where('estado', '!=', 'ANULADO')
        ->sum('montototal');

        $saldoanteriorcuenta1 = '-174910.55';
        $saldoanteriorcuenta2 = '34508.22';
        $saldoanteriorcuenta3 = '3500.00';

        $cuentasConSaldo = [
            '3000189269' => $saldoanteriorcuenta1 + $totalCuenta1Ingreso - $totalCuenta1Egreso,
            '2505314878' => $saldoanteriorcuenta2 + $totalCuenta2Ingreso - $totalCuenta2Egreso,
            /* '4011113557' => $saldoanteriorcuenta3 + $totalCuenta3Ingreso - $totalCuenta3Egreso, */
        ];

        $documentosPorFecha = PlanillasPagosGeneradas::select('tipo', 'documento', 'fechapago', 'proveedor')
        ->get()
        ->groupBy('fechapago');

        return view('admin.caja.cuentaspagar.listacuentaspagar', compact('registrosbateria','cuentaspagar','year', 'month', 'records', 'usuarioAutenticado','result', 'fechas', 'proveedor'
                , 'totalCuenta1Ingreso', 'totalCuenta1Egreso', 'totalCuenta2Ingreso', 'totalCuenta2Egreso', 'totalCuenta3Ingreso', 'totalCuenta3Egreso'
                , 'saldoanteriorcuenta1', 'saldoanteriorcuenta2', 'saldoanteriorcuenta3', 'documentosPorFecha', 'proveedoresServicios'));
    }
    public function actualizarPrioridadProgramacion(Request $request)
    {
        foreach ($request->preordenes as $id) {
            Bateriasubcliente::where('id', $id)->update([
                'prioridad' => 'PRIORITARIO'
            ]);
        }
        return response()->json(['message' => 'Prioridades actualizadas correctamente.']);
    }
    public function buscarlistacuentaspagar(Proveedor $proveedor,  Request $request)
    {
        return $this->listacuentaspagar($proveedor, $request);
    }
    /* public function actualizarFactura(Request $request)
    {
        $nroFactura = $request->input('nroFactura');
        $seleccionados = $request->input('seleccionados');

        $archivo = $request->file('documentofactura');
        $nombreArchivo = Str::random(10) . '_' . $archivo->getClientOriginalName();
        $archivo->move(public_path('comprobantescuentaspagar'), $nombreArchivo);

        foreach ($seleccionados as $id) {
            $registro = Programacionsubcliente::find($id);
            if ($registro && strtoupper($registro->accionnombre) !== 'INFORME FINAL') {
                $registro->nroFactura = $nroFactura;
                $registro->factura = $nombreArchivo;
                $registro->save();
            } else {
                $registroFinal = ProveedorInformefinal::find($id);
                if ($registroFinal) {
                    $registroFinal->nroFactura = $nroFactura;
                    $registroFinal->factura = $nombreArchivo;
                    $registroFinal->save();
                }
            }
        }

        return redirect()->back()->with('info', 'Facturas actualizadas correctamente.');
    } */
    public function actualizarFactura(Request $request)
    {
        $accion = $request->input('action_type');
        $nroFactura = $request->input('nroFactura');
        $seleccionados = $request->input('seleccionados', []);

        if (empty($seleccionados)) {
            return redirect()->back()->with('error', 'No hay registros seleccionados.');
        }

        if ($accion === 'guardar') {
            $archivo = $request->file('documentofactura');
            if (!$archivo || !$nroFactura) {
                return redirect()->back()->with('error', 'Debe adjuntar el archivo PDF y escribir el número de factura.');
            }

            $nombreArchivo = Str::random(10) . '_' . $archivo->getClientOriginalName();
            $archivo->move(public_path('comprobantescuentaspagar'), $nombreArchivo);

            foreach ($seleccionados as $id) {
                $registro = Programacionsubcliente::find($id);
                if ($registro && strtoupper($registro->accionnombre) !== 'INFORME FINAL') {
                    $registro->nroFactura = $nroFactura;
                    $registro->factura = $nombreArchivo;
                    $registro->save();
                } else {
                    $registroFinal = ProveedorInformefinal::find($id);
                    if ($registroFinal) {
                        $registroFinal->nroFactura = $nroFactura;
                        $registroFinal->factura = $nombreArchivo;
                        $registroFinal->save();
                    }
                }
            }

            return redirect()->back()->with('info', 'Facturas actualizadas correctamente.');

        } elseif ($accion === 'anular') {
            foreach ($seleccionados as $id) {
                $registro = Programacionsubcliente::find($id);
                if ($registro && strtoupper($registro->accionnombre) !== 'INFORME FINAL') {
                    $registro->nroFactura = null;
                    $registro->factura = null;
                    $registro->save();
                } else {
                    $registroFinal = ProveedorInformefinal::find($id);
                    if ($registroFinal) {
                        $registroFinal->nroFactura = null;
                        $registroFinal->factura = null;
                        $registroFinal->save();
                    }
                }
            }

            return redirect()->back()->with('info', 'Facturas anuladas correctamente.');
        }

        return redirect()->back()->with('error', 'Acción no reconocida.');
    }
//

// APROBACION DE CUENTAS POR PAGAR
    /* public function aprobarSeleccionados(Request $request)
    {
        $cuentas = $request->input('cuentas', []);
        $programaciones = $request->input('programaciones', []);
        $lineasPagoTercero = [];
        $lineasPagoInterbancario = [];
        $totalRegistrosTercero = 0;
        $totalRegistrosInterbancario = 0;
        $totalMontosTercero = 0;
        $totalMontosInterbancario = 0;

        if (!empty($cuentas)) {
            DB::table('cuentasporpagar')
                ->whereIn('id', $cuentas)
                ->update(['estadoaprobacion' => 'CUENTA POR PAGAR']);

            $cuentasData = DB::table('cuentasporpagar')
                ->whereIn('id', $cuentas)
                ->where('nrobancoorigen', '3000189269')
                ->get();
            foreach ($cuentasData as $cuenta) {
                $proveedorNombre = $cuenta->proveedornombre;
                $proveedor = DB::table('proveedores')
                    ->where('proveedor', $proveedorNombre)
                    ->first();
                if (!$proveedor) {
                    $proveedor = DB::table('proveedoresservicios')
                        ->where('razonsocial', $proveedorNombre)
                        ->first();
                }
                if ($proveedor && in_array($proveedor->tipoplanilla, ['PAGO A TERCERO', 'PAGO INTERBANCARIO'])) {
                    $cuentaProveedor = $proveedor->cuenta ?? $proveedor->numcuenta;
                    $monto = intval($cuenta->montototal * 100);
                    $documentoIdentidad = null;
                    $detalleIdentidad = null;
                    if (strtoupper(trim($proveedor->tipocuenta)) === 'CUENTA CORRIENTE') {
                        $documentoIdentidad = $proveedor->nit;
                        $detalleIdentidad = 'NUMERO DE IDENTIFICACION TRIBUTARIA';
                    } else {
                        $documentoIdentidad = $proveedor->ci;
                        $detalleIdentidad = 'CARNET DE IDENTIDAD';
                    }
                    $codigoTipoIdentificacion = DB::table('planillatercerinter')
                        ->where('seccion', 'TIPO DE DOCUMENTO DE IDENTIFICACION')
                        ->where('detalle', $detalleIdentidad)
                        ->value('codigo');
                    $codigoTipoCuenta = DB::table('planillatercerinter')
                        ->where('seccion', 'TIPO DE PRODUCTO')
                        ->where('detalle', $proveedor->tipocuenta)
                        ->value('codigo');
                    $codigoBanco = DB::table('planillatercerinter')
                        ->where('seccion', 'CODIGO ENTIDAD FINANCIERA PARA EL ABONO')
                        ->where('detalle', $proveedor->banco)
                        ->value('codigo');
                    $linea = "<d13>{$cuenta->ordenid}<d12>{$cuenta->ordenid}<d6>0<d7>{$monto}<d9>{$cuentaProveedor}<d0>2";
                    $linea2 = "<d8>$codigoBanco<d13>{$cuenta->ordenid}<d12>{$cuenta->ordenid}<d6>0<d7>{$monto}<d3>{$proveedor->razonsocial}<d9>{$cuentaProveedor}<d2>{$documentoIdentidad}<d1>{$codigoTipoIdentificacion}<d51>{$codigoTipoCuenta}<d0>22";
                    if ($proveedor->tipoplanilla === 'PAGO A TERCERO') {
                        $lineasPagoTercero[] = $linea;
                        $totalRegistrosTercero++;
                        $totalMontosTercero += $monto;
                    } else {
                        $lineasPagoInterbancario[] = $linea2;
                        $totalRegistrosInterbancario++;
                        $totalMontosInterbancario += $monto;
                    }
                }
            }
        }

        if (!empty($programaciones)) {
            DB::table('bateriasubclientes')
                ->whereIn('id', $programaciones)
                ->update(['estadoaprobacion' => 'CUENTA POR PAGAR']);
            $programacionesData = DB::table('bateriasubclientes')
                ->whereIn('id', $programaciones)
                ->where('nrobancoorigen', '3000189269')
                ->get();
            foreach ($programacionesData as $prog) {
                $proveedorNombre = $prog->proveedorasignado;
                $proveedor = DB::table('proveedores')
                    ->where('proveedor', $proveedorNombre)
                    ->first();
                if (!$proveedor) {
                    $proveedor = DB::table('proveedoresservicios')
                        ->where('razonsocial', $proveedorNombre)
                        ->first();
                }
                if ($proveedor && in_array($proveedor->tipoplanilla, ['PAGO A TERCERO', 'PAGO INTERBANCARIO'])) {
                    $cuentaProveedor = $proveedor->cuenta ?? $proveedor->numcuenta;
                    $monto = intval($prog->preciocompra * 100);
                    $documentoIdentidad = null;
                    $detalleIdentidad = null;
                    if (strtoupper(trim($proveedor->tipocuenta)) === 'CUENTA CORRIENTE') {
                        $documentoIdentidad = $proveedor->nit;
                        $detalleIdentidad = 'NUMERO DE IDENTIFICACION TRIBUTARIA';
                    } else {
                        $documentoIdentidad = $proveedor->ci;
                        $detalleIdentidad = 'CARNET DE IDENTIDAD';
                    }
                    $codigoTipoIdentificacion = DB::table('planillatercerinter')
                        ->where('seccion', 'TIPO DE DOCUMENTO DE IDENTIFICACION')
                        ->where('detalle', $detalleIdentidad)
                        ->value('codigo');
                    $codigoTipoCuenta = DB::table('planillatercerinter')
                        ->where('seccion', 'TIPO DE PRODUCTO')
                        ->where('detalle', $proveedor->tipocuenta)
                        ->value('codigo');
                    $codigoBanco = DB::table('planillatercerinter')
                        ->where('seccion', 'CODIGO ENTIDAD FINANCIERA PARA EL ABONO')
                        ->where('detalle', $proveedor->banco)
                        ->value('codigo');
                    $linea = "<d13>{$prog->ordenid}<d12>{$prog->ordenid}<d6>0<d7>{$monto}<d9>{$cuentaProveedor}<d0>2";
                    $linea2 = "<d8>$codigoBanco<d13>{$prog->ordenid}<d12>{$prog->ordenid}<d6>0<d7>{$monto}<d3>{$proveedor->proveedor}<d9>{$cuentaProveedor}<d2>{$documentoIdentidad}<d1>{$codigoTipoIdentificacion}<d51>{$codigoTipoCuenta}<d0>22";
                    if ($proveedor->tipoplanilla === 'PAGO A TERCERO') {
                        $lineasPagoTercero[] = $linea;
                        $totalRegistrosTercero++;
                        $totalMontosTercero += $monto;
                    } else {
                        $lineasPagoInterbancario[] = $linea2;
                        $totalRegistrosInterbancario++;
                        $totalMontosInterbancario += $monto;
                    }
                }
            }
        }
        $fechaActual = now()->format('Ymd');
        $fechaPago = null;
        if (!empty($cuentasData)) {
            $fechaPago = $cuentasData[0]->fechaasignada ?? now();
        } elseif (!empty($programacionesData)) {
            $fechaPago = $programacionesData[0]->fechapago ?? now();
        } else {
            $fechaPago = now();
        }
        $fechaCarpeta = \Carbon\Carbon::parse($fechaPago)->format('Ymd');
        $carpetaDestino = public_path("planillaspagosgeneradas/{$fechaCarpeta}");

        if (!File::exists($carpetaDestino)) {
            File::makeDirectory($carpetaDestino, 0777, true, true);
        }
        $seGuardoAlMenosUnArchivo = false;
        $fechaPago = null;
        if (!empty($cuentasData)) {
            $fechaPago = $cuentasData[0]->fechaasignada ?? now();
        } elseif (!empty($programacionesData)) {
            $fechaPago = $programacionesData[0]->fechapago ?? now();
        } else {
            $fechaPago = now();
        }
        if (!empty($lineasPagoTercero)) {
            $cabecera = "<c1>3000189269<c2>2<c3>{$totalRegistrosTercero}<c4>{$totalMontosTercero}<c5>0<c6>0<c7>PAGO PROVEEDORES<c8>PAGO PROVEEDORES<c9>{$fechaActual}";
            array_unshift($lineasPagoTercero, $cabecera);
            $fechaArchivo = \Carbon\Carbon::parse($fechaPago)->format('Ymd');
            $baseFilename = "Pago_Tercero_{$fechaArchivo}";
            $extension = ".txt";
            $filenameTercero = "{$baseFilename}{$extension}";
            $pathTercero = "{$carpetaDestino}/{$filenameTercero}";
            $counter = 1;
            while (File::exists($pathTercero)) {
                $filenameTercero = "{$baseFilename}({$counter}){$extension}";
                $pathTercero = "{$carpetaDestino}/{$filenameTercero}";
                $counter++;
            }
            file_put_contents($pathTercero, implode("\n", $lineasPagoTercero));
            DB::table('planillaspagosgeneradas')->insert([
                'tipo' => 'PAGO A TERCERO',
                'fechapago' => $fechaPago,
                'documento' => $filenameTercero,
                'created_at' => now(),
                'updated_at' => now(),
                'usuarioregistroid' => Auth::id(),
                'usuarioregistronombre' => Auth::user()->name
            ]);
            $seGuardoAlMenosUnArchivo = true;
        }
        if (!empty($lineasPagoInterbancario)) {
            $cabecera = "<c1>3000189269<c2>22<c3>{$totalRegistrosInterbancario}<c4>{$totalMontosInterbancario}<c5>0<c6>0<c7>PAGO PROVEEDORES<c8>PAGO PROVEEDORES<c9>{$fechaActual}";
            array_unshift($lineasPagoInterbancario, $cabecera);

            $fechaArchivo = \Carbon\Carbon::parse($fechaPago)->format('Ymd');
            $baseFilename = "Pago_Interbancario_{$fechaArchivo}";
            $extension = ".txt";
            $filenameInterbancario = "{$baseFilename}{$extension}";
            $pathInterbancario = "{$carpetaDestino}/{$filenameInterbancario}";
            $counter = 1;
            while (File::exists($pathInterbancario)) {
                $filenameInterbancario = "{$baseFilename}({$counter}){$extension}";
                $pathInterbancario = "{$carpetaDestino}/{$filenameInterbancario}";
                $counter++;
            }
            file_put_contents($pathInterbancario, implode("\n", $lineasPagoInterbancario));
            DB::table('planillaspagosgeneradas')->insert([
                'tipo' => 'PAGO INTERBANCARIO',
                'fechapago' => $fechaPago,
                'documento' => $filenameInterbancario,
                'created_at' => now(),
                'updated_at' => now(),
                'usuarioregistroid' => Auth::id(),
                'usuarioregistronombre' => Auth::user()->name
            ]);
            $seGuardoAlMenosUnArchivo = true;
        }
                if ($seGuardoAlMenosUnArchivo) {
            return back()->with('info', 'Los archivos de planilla fueron generados y guardados correctamente.');
        } else {
            return back()->with('error', 'No se encontraron registros válidos con tipo de planilla requerido.');
        }
    } */
    public function aprobarSeleccionados(Request $request)
    {
        $cuentas           = $request->input('cuentas', []);
        $programaciones    = $request->input('programaciones', []);
        $fechaActual       = now()->format('Ymd');

        // Arrays asociativos para agrupar por cuentaProveedor
        $agrupadosTercero = [];
        $agrupadosInter   = [];

        // --- Procesar cuentas por pagar ---
        if (!empty($cuentas)) {
            DB::table('cuentasporpagar')
                ->whereIn('id', $cuentas)
                ->update(['estadoaprobacion' => 'APROBADO']);

            $cuentasData = DB::table('cuentasporpagar')
                ->whereIn('id', $cuentas)
                ->where('nrobancoorigen', '3000189269')
                ->get();

            foreach ($cuentasData as $cuenta) {
                // Obtener proveedor
                $proveedorNombre = $cuenta->proveedornombre;
                $proveedor = DB::table('proveedores')
                    ->where('proveedor', $proveedorNombre)
                    ->first()
                    ?: DB::table('proveedoresservicios')
                        ->where('razonsocial', $proveedorNombre)
                        ->first();

                if (!$proveedor) continue;
                if (!in_array($proveedor->tipoplanilla, ['PAGO A TERCERO','PAGO INTERBANCARIO'])) continue;

                // Datos comunes
                $cuentaProv = $proveedor->cuenta ?? $proveedor->numcuenta;
                $monto      = intval($cuenta->montototal * 100);

                // Identificación
                if (strtoupper(trim($proveedor->tipocuenta)) === 'CUENTA CORRIENTE') {
                    $docId   = $proveedor->nit;
                    $detId   = 'NUMERO DE IDENTIFICACION TRIBUTARIA';
                } else {
                    $docId   = $proveedor->ci;
                    $detId   = 'CARNET DE IDENTIDAD';
                }

                // Códigos planilla
                $codId     = DB::table('planillatercerinter')
                    ->where('seccion','TIPO DE DOCUMENTO DE IDENTIFICACION')
                    ->where('detalle',$detId)
                    ->value('codigo');
                $codProd   = DB::table('planillatercerinter')
                    ->where('seccion','TIPO DE PRODUCTO')
                    ->where('detalle',$proveedor->tipocuenta)
                    ->value('codigo');
                $codBanco  = DB::table('planillatercerinter')
                    ->where('seccion','CODIGO ENTIDAD FINANCIERA PARA EL ABONO')
                    ->where('detalle',$proveedor->banco)
                    ->value('codigo');

                // Agrupar
                $key = $cuentaProv;
                $target = ($proveedor->tipoplanilla === 'PAGO A TERCERO')
                    ? 'agrupadosTercero' : 'agrupadosInter';

                if (!isset($$target[$key])) {
                    $$target[$key] = [
                        'ordenid'     => $cuenta->ordenid,
                        'monto'       => 0,
                        'registros'   => 0,
                        'detalle'     => compact('docId','codId','codProd','codBanco'),
                        'razonsocial' => $proveedor->razonsocial,
                    ];
                }
                $$target[$key]['monto']     += $monto;
                $$target[$key]['registros'] += 1;
            }
        }

        // --- Procesar bateriasubclientes ---
        if (!empty($programaciones)) {
            DB::table('bateriasubclientes')
                ->whereIn('id', $programaciones)
                ->update(['estadoaprobacion' => 'APROBADO']);

            $progData = DB::table('bateriasubclientes')
                ->whereIn('id', $programaciones)
                ->where('nrobancoorigen','3000189269')
                ->get();

            foreach ($progData as $prog) {
                $provNombre = $prog->proveedorasignado;
                $proveedor = DB::table('proveedores')
                    ->where('proveedor',$provNombre)
                    ->first()
                    ?: DB::table('proveedoresservicios')
                        ->where('razonsocial',$provNombre)
                        ->first();

                if (!$proveedor) continue;
                if (!in_array($proveedor->tipoplanilla,['PAGO A TERCERO','PAGO INTERBANCARIO'])) continue;

                $cuentaProv = $proveedor->cuenta ?? $proveedor->numcuenta;
                $monto      = intval($prog->preciocompra * 100);

                if (strtoupper(trim($proveedor->tipocuenta)) === 'CUENTA CORRIENTE') {
                    $docId = $proveedor->nit;
                    $detId = 'NUMERO DE IDENTIFICACION TRIBUTARIA';
                } else {
                    $docId = $proveedor->ci;
                    $detId = 'CARNET DE IDENTIDAD';
                }

                $codId    = DB::table('planillatercerinter')
                    ->where('seccion','TIPO DE DOCUMENTO DE IDENTIFICACION')
                    ->where('detalle',$detId)
                    ->value('codigo');
                $codProd  = DB::table('planillatercerinter')
                    ->where('seccion','TIPO DE PRODUCTO')
                    ->where('detalle',$proveedor->tipocuenta)
                    ->value('codigo');
                $codBanco = DB::table('planillatercerinter')
                    ->where('seccion','CODIGO ENTIDAD FINANCIERA PARA EL ABONO')
                    ->where('detalle',$proveedor->banco)
                    ->value('codigo');

                $key = $cuentaProv;
                $target = ($proveedor->tipoplanilla === 'PAGO A TERCERO')
                    ? 'agrupadosTercero' : 'agrupadosInter';

                if (!isset($$target[$key])) {
                    $$target[$key] = [
                        'ordenid'     => $prog->ordenid,
                        'monto'       => 0,
                        'registros'   => 0,
                        'detalle'     => compact('docId','codId','codProd','codBanco'),
                        'razonsocial' => $proveedor->proveedor,
                    ];
                }
                $$target[$key]['monto']     += $monto;
                $$target[$key]['registros'] += 1;
            }
        }

        // Calcular totales y generar archivos agrupados
        $totalRegT  = count($agrupadosTercero);
        $totalRegI  = count($agrupadosInter);
        $totalMonT  = array_sum(array_column($agrupadosTercero,'monto'));
        $totalMonI  = array_sum(array_column($agrupadosInter,'monto'));

        $fechaPago  = now();
        $fechaCarp  = \Carbon\Carbon::parse($fechaPago)->format('Ymd');
        $destino    = public_path("planillaspagosgeneradas/{$fechaCarp}");
        if (!File::exists($destino)) {
            File::makeDirectory($destino, 0777, true, true);
        }

        $guardado = false;

        // Pago a Tercero
        if ($totalRegT > 0) {
            $hdr = "<c1>3000189269<c2>2<c3>{$totalRegT}<c4>{$totalMonT}<c5>0<c6>0<c7>PAGO PROVEEDORES<c8>PAGO PROVEEDORES<c9>{$fechaActual}";
            $lines = [$hdr];
            foreach ($agrupadosTercero as $cuentaProv => $info) {
                $o = $info['ordenid'];
                $m = $info['monto'];
                $lines[] = "<d13>{$o}<d12>{$o}<d6>0<d7>{$m}<d9>{$cuentaProv}<d0>2";
            }
            $fileName = "Pago_Tercero_{$fechaCarp}.txt";
            $path = "{$destino}/{$fileName}";
            $i=1; while(File::exists($path)) {
                $fileName = "Pago_Tercero_{$fechaCarp}({$i}).txt"; $path = "{$destino}/{$fileName}"; $i++;
            }
            file_put_contents($path, implode("\n",$lines));
            DB::table('planillaspagosgeneradas')->insert([
                'tipo'=>'PAGO A TERCERO','fechapago'=>$fechaPago,'documento'=>$fileName,
                'created_at'=>now(),'updated_at'=>now(),
                'usuarioregistroid'=>Auth::id(),'usuarioregistronombre'=>Auth::user()->name
            ]);
            $guardado = true;
        }

        // Pago Interbancario
        if ($totalRegI > 0) {
            $hdr = "<c1>3000189269<c2>22<c3>{$totalRegI}<c4>{$totalMonI}<c5>0<c6>0<c7>PAGO PROVEEDORES<c8>PAGO PROVEEDORES<c9>{$fechaActual}";
            $lines = [$hdr];
            foreach ($agrupadosInter as $cuentaProv => $info) {
                $d = $info['detalle'];
                $o = $info['ordenid'];
                $m = $info['monto'];
                $r = $info['razonsocial'];
                $lines[] = "<d8>{$d['codBanco']}<d13>{$o}<d12>{$o}<d6>0<d7>{$m}<d3>{$r}<d9>{$cuentaProv}<d2>{$d['docId']}<d1>{$d['codId']}<d51>{$d['codProd']}<d0>22";
            }
            $fileName = "Pago_Interbancario_{$fechaCarp}.txt";
            $path = "{$destino}/{$fileName}";
            $i=1; while(File::exists($path)) {
                $fileName = "Pago_Interbancario_{$fechaCarp}({$i}).txt"; $path = "{$destino}/{$fileName}"; $i++;
            }
            file_put_contents($path, implode("\n",$lines));
            DB::table('planillaspagosgeneradas')->insert([
                'tipo'=>'PAGO INTERBANCARIO','fechapago'=>$fechaPago,'documento'=>$fileName,
                'created_at'=>now(),'updated_at'=>now(),
                'usuarioregistroid'=>Auth::id(),'usuarioregistronombre'=>Auth::user()->name
            ]);
            $guardado = true;
        }

        // Respuesta final
        if ($guardado) {
            return back()->with('info','Los archivos de planilla fueron generados y guardados correctamente.');
        }
        return back()->with('error','No se encontraron registros válidos con tipo de planilla requerido.');
    }
    public function rechazarSeleccionados(Request $request)
    {
        $cuentas = $request->input('cuentas', []);
        $programaciones = $request->input('programaciones', []);

        if (!empty($cuentas)) {
            DB::table('cuentasporpagar')
                ->whereIn('id', $cuentas)
                ->update(['estadoaprobacion' => 'RECHAZADO']);
        }

        if (!empty($programaciones)) {
            DB::table('bateriasubclientes')
                ->whereIn('id', $programaciones)
                ->update(['estadoaprobacion' => 'RECHAZADO']);
        }

        return back()->with('info', 'Registros rechazados correctamente.');
    }
    public function cambiarfechaSeleccionados(Request $request)
    {
        $cuentas = $request->input('cuentas', []);
        $programaciones = $request->input('programaciones', []);
        $fechapago = $request->input('fechapago');

        if (!empty($cuentas)) {
            $cuentasPorPagar = CuentasPagar::whereIn('id', $cuentas)->get();

            foreach ($cuentasPorPagar as $cuenta) {
                if (is_null($cuenta->fechamora)) {
                    $cuenta->fechamora = $cuenta->fechaasignada;
                }
                $cuenta->fechaasignada = $fechapago;
                $cuenta->estadoaprobacion = 'PENDIENTE';
                $cuenta->save();
            }
        }

        if (!empty($programaciones)) {
            $baterias = BateriaSubCliente::whereIn('id', $programaciones)->get();

            foreach ($baterias as $bateria) {
                if (is_null($bateria->fechamora)) {
                    $bateria->fechamora = $bateria->fechapago;
                }
                $bateria->fechapago = $fechapago;
                $bateria->estadoaprobacion = null;
                $bateria->save();
            }
        }

        return back()->with('info', 'Fechas actualizadas correctamente.');
    }
    public function guardarQR(Request $request)
    {
        $request->validate([
            'imagen_qr' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'proveedor' => 'required|string',
            'fechapago' => 'required|date',
        ]);

        // Obtener el archivo subido
        $archivo = $request->file('imagen_qr');
        
        // Generar un nombre único para el archivo
        $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
        
        // Crear la carpeta de destino sin guiones en la fecha
        $fechapagoSinGuiones = str_replace('-', '', $request->fechapago); // Eliminar los guiones
        $carpetaDestino = public_path("planillaspagosgeneradas/{$fechapagoSinGuiones}");
        
        // Verificar si la carpeta no existe y crearla
        if (!file_exists($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }

        // Mover el archivo a la carpeta destino
        $ruta = $archivo->move($carpetaDestino, $nombreArchivo);

        // Guardar los detalles en la base de datos
        PlanillasPagosGeneradas::create([
            'proveedor' => $request->proveedor,
            'fechapago' => $request->fechapago,
            'tipo' => 'PAGO QR',
            'documento' => $nombreArchivo,
            'usuarioregistroid' => Auth::id(),
            'usuarioregistronombre' => Auth::user()->name
        ]);

        return back()->with('info', 'QR subido y guardado exitosamente');
    }
    public function cpppendientes(Proveedor $proveedor, Request $request)
    {
        $fechas = Bateriasubcliente::pluck('fechabateria')->unique()->sort()->toArray();

        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;

        $userRole = auth()->user()->getRoleNames()->first(); 
        
        $query = Bateriasubcliente::with(['estadoprogramacionsubcliente', 'documentacionsubcliente', 'programacionsubcliente','informesfinales','pagoservicio','pagoservicioinformefinal','provinfofinal',
            'estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria', 'programacionsubclienteauditoria','informesfinalesauditoria','provinfofinalauditoria',
            'estadoprogramacionsubclientecomun', 'documentacionsubclientecomun', 'programacionsubclientecomun','informesfinalescomun','provinfofinalcomun'])
            ->whereNotNull('proveedorasignado')
            ->where('preciocompra', '!=', NULL)
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)
            ->where('pagoservicio','<>', 'EXTERNO')
            ->where('proveedorasignado', '<>', 'DIAGNOSTICO MEDICO POR IMAGEN DMI') 
            ->where('proveedorasignado', '<>', 'PROVEEDOR AJENO') 
        ->orderBy('proveedorasignado');

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('proveedorasignado', function ($q) use ($request) {
                $q->where('proveedorasignado', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        $bateriaproveedores = $query->get();
        $grouped = $bateriaproveedores->groupBy(function($item) {
            return $item->proveedorasignado;
        });

        $result = [];
        foreach ($grouped as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {

                    $estadoProgramacion = collect([
                            $item->estadoprogramacionsubcliente, 
                            $item->estadoprogramacionsubclienteauditoria, 
                            $item->estadoprogramacionsubclientecomun
                        ])->filter();
                        
                        $resultadoestado = $estadoProgramacion
                            ->flatMap(function ($estadoprogramacion) { 
                                return $estadoprogramacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();  

                    $programaciones = collect([
                            $item->programacionsubcliente, 
                            $item->programacionsubclienteauditoria, 
                            $item->programacionsubclientecomun
                        ])->filter();
                    
                        $resultadoprog = $programaciones
                            ->flatMap(function ($programacion) { 
                                return $programacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();                    

                    $informesubido = collect([
                            $item->documentacionsubcliente, 
                            $item->documentacionsubclienteauditoria, 
                            $item->documentacionsubclientecomun
                        ])->filter();
                        
                        $resultadoinforme = $informesubido
                            ->flatMap(function ($informe) { 
                                return $informe;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accion', $item->accionnombre)
                    ->first();           

                    $informefinalsubido = collect([
                            $item->informesfinales, 
                            $item->informesfinalesauditoria, 
                            $item->informesfinalescomun
                        ])->filter();
                        
                        $resultadoinformefinal = $informefinalsubido
                            ->flatMap(function ($informefinal) { 
                                return $informefinal;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $provinformes = collect([
                            $item->provinfofinal, 
                            $item->provinfofinalauditoria, 
                            $item->provinfofinalcomun
                        ])->filter();
                        
                        $resultadoprovinformes = $provinformes
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $resultadopagoinformefinal = $item->pagoservicioinformefinal()
                        ->where('provinfofinalid', $item->provinfofinalid)
                        ->where('tipomovimiento', 'EGRESO')
                    ->first();

                    $pagobateria = collect([
                        $item->programacionsubcliente,
                        $item->programacionsubclienteauditoria,
                        $item->programacionsubclientecomun
                    ])->filter();
                    
                    $resultadopago = $pagobateria
                        ->flatMap(fn($pago) => $pago)
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();
                    
                    if ($resultadopago) {
                        $programacionId = $resultadopago->id;
                        $detallerecibo = Detallerecibo::where('programacionid', $programacionId)
                                                      ->where('tipomovimiento', 'EGRESO')
                                                      ->first();
                    
                        if ($detallerecibo) {
                            $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                        } else {
                            $pagoservicioinforme = $resultadopago->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : null;
                        }
                    } else {
                        $pagoservicioinforme = null;
                    }

                $fechaAtencion = $resultadoestado ? $resultadoestado->fechaatencionprogramacion : null;
                $fechaprogramacion = $resultadoprog ? $resultadoprog->fechaasignada : null;
                $idprogramacion = $resultadoprog ? $resultadoprog->id : null;
                $nrofacturaprog = $resultadoprog ? $resultadoprog->nrofactura : null;
                $documentofactura = $resultadoprog ? $resultadoprog->factura : null;
                $informedocumentacion = $resultadoinforme ? $resultadoinforme->created_at->toDateString() : null;
                $informedocumentacionfinal = $resultadoinformefinal ? $resultadoinformefinal->created_at->toDateString() : null;
                $pagoservicioinformefinal = in_array($item->id, [3173, 3178, 3187, 3043]) 
                ? 'PROCESADO' 
                : ($resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null);
                $nrofacturainformefinal = $resultadoprovinformes ? $resultadoprovinformes->nrofactura : null;

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'pagoservicio' => $item->pagoservicio,
                    'preciocompra' => $item->preciocompra,
                    'clienteitaid' => $item->clienteitaid,
                    'clienteitanombre' => $item->clienteitanombre,
                    'clienteauditoriaid' => $item->clienteauditoriaid,
                    'clienteauditorianombre' => $item->clienteauditorianombre,
                    'clientecomunid' => $item->clientecomunid,
                    'clientecomunnombre' => $item->clientecomunnombre,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechaprogramacion' => $fechaprogramacion,
                    'informedocumentacion' => $informedocumentacion,
                    'informedocumentacionfinal' => $informedocumentacionfinal,
                    'pagoservicioinforme' => $pagoservicioinforme,
                    'pagoservicioinformefinal' => $pagoservicioinformefinal,
                    'idprogramacion' => $idprogramacion,
                    'fechabateria' => $item->fechabateria,
                    'provinfofinalid' => $item->provinfofinalid,
                    'nrofacturaprog' => $nrofacturaprog,
                    'documentofactura' => $documentofactura,
                    'nrofacturainformefinal' => $nrofacturainformefinal,
                    'prioridad' => $item->prioridad,
                ];
            }
            $result[] = [
                'proveedorasignado' => $item->proveedorasignado,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'fechabateria' => $item->fechabateria,
            ];
        }

        
        $cuentaspagar = CuentasPagar::with('proveedorServicio')->get();


        $registrosbateria = Bateriasubcliente::where('prioridad', 'CUENTA POR PAGAR')->orwhere('prioridad', 'PAGO PROCESADO')
            ->leftJoin('proveedorinformesfinales', 'bateriasubclientes.provinfofinalid', '=', 'proveedorinformesfinales.id')
            ->leftJoin('informesfinales', function ($join) {
                $join->on(function ($q) {
                    $q->whereColumn('bateriasubclientes.clienteitaid', 'informesfinales.clienteitaid')
                    ->orWhereColumn('bateriasubclientes.clienteauditoriaid', 'informesfinales.clienteauditoriaid')
                    ->orWhereColumn('bateriasubclientes.clientecomunid', 'informesfinales.clientecomunid');
                })
                ->whereColumn('bateriasubclientes.fechabateria', 'informesfinales.fechabateria')
                ->whereColumn('proveedorinformesfinales.servicio', 'informesfinales.servicio');
            })
            ->select('bateriasubclientes.*', 'informesfinales.created_at as informe_created_at')
            ->with([
                'programacion' => function ($query) {
                    $query->select('id', 'bateriaid', 'fechaasignada');
                },
                'programacion.documentacion' => function ($query) {
                    $query->select('id', 'programacionid', 'created_at');
                }
            ])
        ->get();

        $proveedoresServicios = Proveedor::pluck('tipoplanilla', 'proveedor');

        $documentosPorFecha = PlanillasPagosGeneradas::select('tipo', 'documento', 'fechapago', 'proveedor')
        ->get()
        ->groupBy('fechapago');

        return view('admin.caja.cuentaspagar.cpppendientes', compact('registrosbateria','cuentaspagar', 'usuarioAutenticado','result', 'fechas', 'proveedor', 'documentosPorFecha', 'proveedoresServicios'));
    }
    public function actualizarEstadoCargado(Request $request)
    {
        if ($request->has('cuentas')) {
            CuentasPagar::whereIn('id', $request->cuentas)->update(['estadoaprobacion' => 'CARGADO']);
        }

        if ($request->has('bateria')) {
            Bateriasubcliente::whereIn('id', $request->bateria)->update(['estadoaprobacion' => 'CARGADO']);
        }

        $nombreUsuario = auth()->user()->name;

        $telegramToken = '7918449232:AAF3tX7GsdCRyNgc7f7L3riK0kg5NYV3Alw';
        $chatId = '-4764405401';
        $mensaje = "De: {$nombreUsuario}\n\nEstimados FABRICIO PRADO y MAUREN LOPEZ,\n\nSe le informa que ya fueron cargadas las planillas al banco, se le solicita autorizarlo a la brevedad posible.\n\nQuedo atent@ a su confirmación.";

        Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $mensaje,
        ]);

        return response()->json(['message' => 'Registros actualizados correctamente']);
    }
    /* public function marcarComoCargado(Request $request)
    {
        $fecha = $request->input('fecha');
        $nroCuenta = $request->input('nrobancoorigen');

        if ($nroCuenta == '2505314878') {
            // CASO 1: cuenta tradicional
            DB::table('cuentasporpagar')
                ->where('fechaasignada', $fecha)
                ->where('nrobancoorigen', $nroCuenta)
                ->update(['estadoaprobacion' => 'SUBIDO']);

            DB::table('bateriasubclientes')
                ->where('fechapago', $fecha)
                ->where('nrobancoorigen', $nroCuenta)
                ->update(['estadoaprobacion' => 'SUBIDO']);

        } 
       elseif ($nroCuenta == '3000189269') {
        // CASO 2: verificar tipoplanilla en las tablas relacionadas

        // Obtener proveedores válidos por tipoplanilla desde ambas tablas
        $proveedoresValidos = DB::table('proveedores')
            ->whereIn('tipoplanilla', ['PAGO INTERBANCARIO', 'PAGO A TERCERO'])
            ->pluck('proveedor') // Este es texto
            ->toArray();

        $serviciosValidos = DB::table('proveedoresservicios')
            ->whereIn('tipoplanilla', ['PAGO INTERBANCARIO', 'PAGO A TERCERO'])
            ->pluck('id') // Este es numérico
            ->toArray();

        // Bateriasubclientes (usa proveedorasignado que coincide con proveedores.proveedor)
        DB::table('bateriasubclientes')
            ->where('fechapago', $fecha)
            ->where('nrobancoorigen', $nroCuenta)
            ->whereIn('proveedorasignado', $proveedoresValidos)
            ->update(['estadoaprobacion' => 'SUBIDO']);

        // Cuentasporpagar (usa proveedorid que coincide con proveedoresservicios.id)
        DB::table('cuentasporpagar')
            ->where('fechaasignada', $fecha)
            ->where('nrobancoorigen', $nroCuenta)
            ->whereIn('proveedorid', $serviciosValidos)
            ->update(['estadoaprobacion' => 'SUBIDO']);
    }

        return back()->with('success', 'Registros marcados como SUBIDO correctamente.');
    } */
    public function marcarComoCargado(Request $request)
    {
        $fecha = $request->input('fecha');

        // Buscar el nroCuenta desde las tablas
        $nroCuenta = DB::table('cuentasporpagar')
            ->where('fechaasignada', $fecha)
            ->value('nrobancoorigen');

        if (!$nroCuenta) {
            $nroCuenta = DB::table('bateriasubclientes')
                ->where('fechapago', $fecha)
                ->value('nrobancoorigen');
        }

        if (!$nroCuenta) {
            return back()->with('error', 'No se encontró número de cuenta para la fecha especificada.');
        }

        if ($nroCuenta == '2505314878') {
            // CASO 1: cuenta tradicional
            DB::table('cuentasporpagar')
                ->where('fechaasignada', $fecha)
                ->where('estadoaprobacion', '=', 'APROBADO')
                ->update(['estadoaprobacion' => 'SUBIDO']);

            DB::table('bateriasubclientes')
                ->where('fechapago', $fecha)
                ->where('estadoaprobacion', '=', 'APROBADO')
                ->update(['estadoaprobacion' => 'SUBIDO']);

        } elseif ($nroCuenta == '3000189269') {
            // CASO 2: validar proveedores con tipoplanilla

            $proveedoresValidos = DB::table('proveedores')
                ->whereIn('tipoplanilla', ['PAGO INTERBANCARIO', 'PAGO A TERCERO'])
                ->pluck('proveedor')
                ->toArray();

            $serviciosValidos = DB::table('proveedoresservicios')
                ->whereIn('tipoplanilla', ['PAGO INTERBANCARIO', 'PAGO A TERCERO'])
                ->pluck('id')
                ->toArray();

            DB::table('bateriasubclientes')
                ->where('fechapago', $fecha)
                ->where('estadoaprobacion', '=', 'APROBADO')
                ->whereIn('proveedorasignado', $proveedoresValidos)
                ->update(['estadoaprobacion' => 'SUBIDO']);

            DB::table('cuentasporpagar')
                ->where('fechaasignada', $fecha)
                ->where('estadoaprobacion', '=', 'APROBADO')
                ->whereIn('proveedorid', $serviciosValidos)
                ->update(['estadoaprobacion' => 'SUBIDO']);
        }

        return back()->with('info', 'Registros marcados como SUBIDO correctamente.');
    }
    public function informarSubida(Request $request)
    {
        $nombreUsuario = auth()->user()->name;

        $telegramToken = '7918449232:AAF3tX7GsdCRyNgc7f7L3riK0kg5NYV3Alw';
        $chatId = '-4764405401';
        $mensaje = "De: {$nombreUsuario}\n\nEstimados FABRICIO PRADO y MAUREN LOPEZ,\n\nSe le informa que ya fueron cargadas las planillas al banco, se le solicita autorizarlo a la brevedad posible.\n\nQuedo atent@ a su confirmación.";

        Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $mensaje,
        ]);

        return response()->json(['message' => 'Notificacion enviada correctamente']);
    }
    public function informarSubidaCxPlistas(Request $request)
    {
        $nombreUsuario = auth()->user()->name;

        $telegramToken = '7918449232:AAF3tX7GsdCRyNgc7f7L3riK0kg5NYV3Alw';
        $chatId = '-4764405401';
        $mensaje = "De: {$nombreUsuario}\n\nEstimada MAUREN LOPEZ, se le informa que la planilla de Cuentas por Pagar ya esta cargada para su respectiva aprobación.\n\nQuedo atent@ a su confirmación.";

        Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $mensaje,
        ]);

        return response()->json(['message' => 'Notificacion enviada correctamente']);
    }
    public function cppcomprobantes(Proveedor $proveedor, Request $request)
    {
        $fechas = Bateriasubcliente::pluck('fechabateria')->unique()->sort()->toArray();

        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;

        $userRole = auth()->user()->getRoleNames()->first(); 
        
        $query = Bateriasubcliente::with(['estadoprogramacionsubcliente', 'documentacionsubcliente', 'programacionsubcliente','informesfinales','pagoservicio','pagoservicioinformefinal','provinfofinal',
            'estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria', 'programacionsubclienteauditoria','informesfinalesauditoria','provinfofinalauditoria',
            'estadoprogramacionsubclientecomun', 'documentacionsubclientecomun', 'programacionsubclientecomun','informesfinalescomun','provinfofinalcomun'])
            ->whereNotNull('proveedorasignado')
            ->where('preciocompra', '!=', NULL)
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)
            ->where('pagoservicio','<>', 'EXTERNO')
            ->where('proveedorasignado', '<>', 'DIAGNOSTICO MEDICO POR IMAGEN DMI') 
            ->where('proveedorasignado', '<>', 'PROVEEDOR AJENO') 
        ->orderBy('proveedorasignado');

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('proveedorasignado', function ($q) use ($request) {
                $q->where('proveedorasignado', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        $bateriaproveedores = $query->get();
        $grouped = $bateriaproveedores->groupBy(function($item) {
            return $item->proveedorasignado;
        });

        $result = [];
        foreach ($grouped as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {

                    $estadoProgramacion = collect([
                            $item->estadoprogramacionsubcliente, 
                            $item->estadoprogramacionsubclienteauditoria, 
                            $item->estadoprogramacionsubclientecomun
                        ])->filter();
                        
                        $resultadoestado = $estadoProgramacion
                            ->flatMap(function ($estadoprogramacion) { 
                                return $estadoprogramacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();  

                    $programaciones = collect([
                            $item->programacionsubcliente, 
                            $item->programacionsubclienteauditoria, 
                            $item->programacionsubclientecomun
                        ])->filter();
                    
                        $resultadoprog = $programaciones
                            ->flatMap(function ($programacion) { 
                                return $programacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();                    

                    $informesubido = collect([
                            $item->documentacionsubcliente, 
                            $item->documentacionsubclienteauditoria, 
                            $item->documentacionsubclientecomun
                        ])->filter();
                        
                        $resultadoinforme = $informesubido
                            ->flatMap(function ($informe) { 
                                return $informe;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accion', $item->accionnombre)
                    ->first();           

                    $informefinalsubido = collect([
                            $item->informesfinales, 
                            $item->informesfinalesauditoria, 
                            $item->informesfinalescomun
                        ])->filter();
                        
                        $resultadoinformefinal = $informefinalsubido
                            ->flatMap(function ($informefinal) { 
                                return $informefinal;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $provinformes = collect([
                            $item->provinfofinal, 
                            $item->provinfofinalauditoria, 
                            $item->provinfofinalcomun
                        ])->filter();
                        
                        $resultadoprovinformes = $provinformes
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $resultadopagoinformefinal = $item->pagoservicioinformefinal()
                        ->where('provinfofinalid', $item->provinfofinalid)
                        ->where('tipomovimiento', 'EGRESO')
                    ->first();

                    $pagobateria = collect([
                        $item->programacionsubcliente,
                        $item->programacionsubclienteauditoria,
                        $item->programacionsubclientecomun
                    ])->filter();
                    
                    $resultadopago = $pagobateria
                        ->flatMap(fn($pago) => $pago)
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();

                    $preciocompra = $item->preciocompra;
                    $pagoservicioinforme = null;

                    if ($resultadopago) {
                        $programacionId = $resultadopago->id;

                        $detallerecibo = Detallerecibo::where('programacionid', $programacionId)
                            ->where('tipomovimiento', 'EGRESO')
                            ->orderByDesc('id')
                            ->first();

                        if ($detallerecibo) {
                            if ($detallerecibo->estado === 'PAGO PROCESADO') {
                                $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                            } elseif ($detallerecibo->estado === 'SALDO PENDIENTE') {
                                $pagoservicioinforme = 'SALDO PENDIENTE';
                                $preciocompra = $detallerecibo->saldo ?? $item->preciocompra;
                            }
                        } else {
                            $pagoservicioinforme = $resultadopago->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : 'PENDIENTE';
                        }
                    } else {
                        $pagoservicioinforme = 'PENDIENTE';
                    }

                $fechaAtencion = $resultadoestado ? $resultadoestado->fechaatencionprogramacion : null;
                $fechaprogramacion = $resultadoprog ? $resultadoprog->fechaasignada : null;
                $idprogramacion = $resultadoprog ? $resultadoprog->id : null;
                $nrofacturaprog = $resultadoprog ? $resultadoprog->nrofactura : null;
                $informedocumentacion = $resultadoinforme ? $resultadoinforme->created_at->toDateString() : null;
                $informedocumentacionfinal = $resultadoinformefinal ? $resultadoinformefinal->created_at->toDateString() : null;
                $pagoservicioinformefinal = in_array($item->id, [3173, 3178, 3187, 3043]) 
                ? 'PROCESADO' 
                : ($resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null);
                $nrofacturainformefinal = $resultadoprovinformes ? $resultadoprovinformes->nrofactura : null;

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'pagoservicio' => $item->pagoservicio,
                    'preciocompra' => $preciocompra,
                    'clienteitaid' => $item->clienteitaid,
                    'clienteitanombre' => $item->clienteitanombre,
                    'clienteauditoriaid' => $item->clienteauditoriaid,
                    'clienteauditorianombre' => $item->clienteauditorianombre,
                    'clientecomunid' => $item->clientecomunid,
                    'clientecomunnombre' => $item->clientecomunnombre,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechaprogramacion' => $fechaprogramacion,
                    'informedocumentacion' => $informedocumentacion,
                    'informedocumentacionfinal' => $informedocumentacionfinal,
                    'pagoservicioinforme' => $pagoservicioinforme,
                    'pagoservicioinformefinal' => $pagoservicioinformefinal,
                    'idprogramacion' => $idprogramacion,
                    'fechabateria' => $item->fechabateria,
                    'provinfofinalid' => $item->provinfofinalid,
                    'nrofacturaprog' => $nrofacturaprog,
                    'nrofacturainformefinal' => $nrofacturainformefinal,
                    'prioridad' => $item->prioridad,
                ];
            }
            $result[] = [
                'proveedorasignado' => $item->proveedorasignado,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'fechabateria' => $item->fechabateria,
            ];
        }

        
        $cuentaspagar = CuentasPagar::with('proveedorServicio')->get();


        $registrosbateria = Bateriasubcliente::where('prioridad', 'CUENTA POR PAGAR')->orwhere('prioridad', 'PAGO PROCESADO')
            ->leftJoin('proveedorinformesfinales', 'bateriasubclientes.provinfofinalid', '=', 'proveedorinformesfinales.id')
            ->leftJoin('informesfinales', function ($join) {
                $join->on(function ($q) {
                    $q->whereColumn('bateriasubclientes.clienteitaid', 'informesfinales.clienteitaid')
                    ->orWhereColumn('bateriasubclientes.clienteauditoriaid', 'informesfinales.clienteauditoriaid')
                    ->orWhereColumn('bateriasubclientes.clientecomunid', 'informesfinales.clientecomunid');
                })
                ->whereColumn('bateriasubclientes.fechabateria', 'informesfinales.fechabateria')
                ->whereColumn('proveedorinformesfinales.servicio', 'informesfinales.servicio');
            })
            ->select('bateriasubclientes.*', 'informesfinales.created_at as informe_created_at')
            ->with([
                'programacion' => function ($query) {
                    $query->select('id', 'bateriaid', 'fechaasignada');
                },
                'programacion.documentacion' => function ($query) {
                    $query->select('id', 'programacionid', 'created_at');
                },
            ])
        ->get();

        foreach ($registrosbateria as $registro) {
            $programacion = \App\Models\Programacionsubcliente::where('bateriaid', $registro->id)
                ->orderBy('id', 'desc') // tomar el más reciente si hay varios
                ->first();

            if ($programacion) {
                $detallerecibo = \App\Models\Detallerecibo::where('programacionid', $programacion->id)
                    ->where('tipomovimiento', 'EGRESO')
                    ->where('estado', 'SALDO PENDIENTE')
                    ->orderByDesc('created_at')
                    ->first();

                if ($detallerecibo && $detallerecibo->saldo > 0) {
                    $registro->preciocompra = $detallerecibo->saldo;
                }
            }
        }


        $proveedoresServicios = Proveedor::pluck('tipoplanilla', 'proveedor');



        $documentosPorFecha = PlanillasPagosGeneradas::select('tipo', 'documento', 'fechapago', 'proveedor')
        ->get()
        ->groupBy('fechapago');

        return view('admin.caja.cuentaspagar.cppcomprobantes', compact('registrosbateria','cuentaspagar', 'usuarioAutenticado','result', 'fechas', 'proveedor', 'documentosPorFecha', 'proveedoresServicios'));
    }
    public function actualizarComprobante(Request $request)
    {
        if (!$request->hasFile('archivo')) {
            return response()->json(['message' => 'No se subió ningún archivo.'], 400);
        }

        $archivo = $request->file('archivo');
        $nombreArchivo = Str::random(10) . '_' . $archivo->getClientOriginalName();
        $archivo->move(public_path('comprobantescuentaspagar'), $nombreArchivo);

        // Obtener el nombre del usuario a notificar
        $usuarioNotificadoNombre = $request->usuarioNotificado;
        $usuarioNotificado = User::where('name', $usuarioNotificadoNombre)->first();


        $usuarioAuth = auth()->user();

        // CUENTAS POR PAGAR
        if ($request->has('cuentas')) {
            $cuentas = CuentasPagar::whereIn('id', $request->cuentas)->get();

            if ($usuarioNotificado) {
                // Agrupar por ordenid y notificar una sola vez por ordenid
                $cuentasPorOrden = $cuentas->groupBy('ordenid');

                foreach ($cuentasPorOrden as $ordenid => $grupoCuentas) {
                    $cuenta = $grupoCuentas->first(); // solo un registro representativo
                    $usuarioNotificado->notify(new ComprobanteNotification($cuenta));
                }
            }

            // Actualizar los campos de las cuentas
            CuentasPagar::whereIn('id', $request->cuentas)
                ->update([
                    'comprobante' => $nombreArchivo,
                    'usuariocomprobante' => $usuarioAuth->name,
                ]);
        }


        // BATERÍAS
        /* if ($request->has('bateria')) {
            $bateriaIDs = $request->bateria;

            // Actualiza comprobantes en múltiples tablas
            Bateriasubcliente::whereIn('id', $bateriaIDs)
                ->update([
                    'comprobante' => $nombreArchivo,
                    'usuariocomprobante' => $usuarioAuth->name,
                ]);

            ProgramacionSubCliente::whereIn('bateriaid', $bateriaIDs)
                ->update([
                    'comprobante' => $nombreArchivo,
                    'usuariocomprobante' => $usuarioAuth->name,
                ]);

            $baterias = Bateriasubcliente::whereIn('id', $bateriaIDs)->get();

            // Actualiza comprobantes en ProveedorInformeFinal si corresponde
            foreach ($baterias as $bateria) {
                if (strtoupper($bateria->accionnombre) === 'INFORME FINAL' && $bateria->provinfofinalid) {
                    ProveedorInformeFinal::where('id', $bateria->provinfofinalid)
                        ->update([
                            'comprobante' => $nombreArchivo,
                            'usuariocomprobante' => $usuarioAuth->name,
                        ]);
                }
            }

            // Notifica una vez por cada ordenid
            if ($usuarioNotificado) {
                $bateriasPorOrden = $baterias->groupBy('ordenid');

                foreach ($bateriasPorOrden as $ordenid => $grupoBaterias) {
                    $bateria = $grupoBaterias->first(); // Solo una instancia representativa
                    $usuarioNotificado->notify(new ComprobanteNotification($bateria));
                }
            }
        } */
        if ($request->has('bateria')) {
            $bateriaIDs = $request->bateria;

            // Actualiza comprobantes en Bateriasubcliente
            Bateriasubcliente::whereIn('id', $bateriaIDs)
                ->update([
                    'comprobante' => $nombreArchivo,
                    'usuariocomprobante' => $usuarioAuth->name,
                ]);

            $baterias = Bateriasubcliente::whereIn('id', $bateriaIDs)->get();

            foreach ($baterias as $bateria) {
                // Primero intenta actualizar por bateriaid directamente
                $actualizados = ProgramacionSubCliente::where('bateriaid', $bateria->id)->update([
                    'comprobante' => $nombreArchivo,
                    'usuariocomprobante' => $usuarioAuth->name,
                ]);

                // Si no encontró por bateriaid, intenta por coincidencias de cliente + fecha + proveedor + acción
                if ($actualizados === 0) {
                    ProgramacionSubCliente::where(function ($query) use ($bateria) {
                        $query->where('clienteitaid', $bateria->clienteitaid)
                            ->orWhere('clienteauditoriaid', $bateria->clienteauditoriaid)
                            ->orWhere('clientecomunid', $bateria->clientecomunid);
                    })
                    ->whereDate('fechabateria', $bateria->fechabateria)
                    ->where('proveedornombre', $bateria->proveedorasignado)
                    ->where('accionnombre', $bateria->accionnombre)
                    ->update([
                        'comprobante' => $nombreArchivo,
                        'usuariocomprobante' => $usuarioAuth->name,
                    ]);
                }

                // Actualiza en ProveedorInformeFinal si corresponde
                if (strtoupper($bateria->accionnombre) === 'INFORME FINAL' && $bateria->provinfofinalid) {
                    ProveedorInformeFinal::where('id', $bateria->provinfofinalid)
                        ->update([
                            'comprobante' => $nombreArchivo,
                            'usuariocomprobante' => $usuarioAuth->name,
                        ]);
                }
            }

            // Notificación por orden
            if ($usuarioNotificado) {
                $bateriasPorOrden = $baterias->groupBy('ordenid');

                foreach ($bateriasPorOrden as $ordenid => $grupoBaterias) {
                    $bateria = $grupoBaterias->first();
                    $usuarioNotificado->notify(new ComprobanteNotification($bateria));
                }
            }
        }



        return response()->json(['message' => 'Registros actualizados correctamente']);
    }
//

// GENERACION DE PDF
    public function generarPDFprovservicios($fecha)
    {
        /* $cuentas = CuentasPagar::whereIn('estado', ['PENDIENTE', 'SALDO PENDIENTE'])
                        ->where('fechaasignada', $fecha)
                        ->get();

        $pdf = Pdf::loadView('admin.caja.cuentaspagar.reportecxpservicios', compact('cuentas', 'fecha'));
        return $pdf->download('CXP_PENDIENTES_'.$fecha.'.pdf'); */
        $cuentas = CuentasPagar::whereIn('estado', ['PENDIENTE', 'SALDO PENDIENTE'])
                            ->where('fechaasignada', $fecha)
                            ->get();

        $nombreArchivo = 'CXP_PENDIENTES_' . $fecha . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$nombreArchivo\"");

        $salida = fopen('php://output', 'w');

        // Usamos punto y coma como delimitador
        fputcsv($salida, ['ID Reg.', 'Proveedor', 'Tipo Orden', 'Orden ID', 'Detalle', 'Fecha Pago', 'N.Cuenta Origen', 'Cantidad', 'Subtotal', 'Descuento', 'Total', 'Estado'], ';');

        foreach ($cuentas as $pendiente) {
            fputcsv($salida, [
                $pendiente->id,
                $pendiente->proveedornombre,
                $pendiente->tipoorden,
                $pendiente->ordenid ?? 0,
                $pendiente->detalleproducto,
                $pendiente->fechaasignada,
                $pendiente->nrobancoorigen ?? 0,
                $pendiente->cantidad ?? 0,
                $pendiente->subtotal,
                $pendiente->descuento,
                $pendiente->montototal,
                $pendiente->estado,
            ], ';');
        }

        fclose($salida);
        exit;
    }
    public function generarPDF(Request $request)
    {
        $fechas = Bateriasubcliente::pluck('fechabateria')->unique()->sort()->toArray();

        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;

        $userRole = auth()->user()->getRoleNames()->first(); 
        
        $query = Bateriasubcliente::with(['estadoprogramacionsubcliente', 'documentacionsubcliente', 'programacionsubcliente','informesfinales','pagoservicio','pagoservicioinformefinal','provinfofinal',
        'estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria', 'programacionsubclienteauditoria','informesfinalesauditoria','provinfofinalauditoria',
        'estadoprogramacionsubclientecomun', 'documentacionsubclientecomun', 'programacionsubclientecomun','informesfinalescomun','provinfofinalcomun'])
            ->whereNotNull('proveedorasignado')
            ->where('preciocompra', '!=', NULL)
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)
            ->where('pagoservicio','<>', 'EXTERNO')
            ->where('proveedorasignado', '<>', 'DIAGNOSTICO MEDICO POR IMAGEN DMI') 
            ->where('proveedorasignado', '<>', 'PROVEEDOR AJENO') 
            ->orderBy('proveedorasignado');

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('proveedorasignado', function ($q) use ($request) {
                $q->where('proveedorasignado', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        $bateriaproveedores = $query->get();
        $grouped = $bateriaproveedores->groupBy(function($item) {
            return $item->proveedorasignado;
        });

        $result = [];
        foreach ($grouped as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {

                    $estadoProgramacion = collect([
                            $item->estadoprogramacionsubcliente, 
                            $item->estadoprogramacionsubclienteauditoria, 
                            $item->estadoprogramacionsubclientecomun
                        ])->filter();
                        
                        $resultadoestado = $estadoProgramacion
                            ->flatMap(function ($estadoprogramacion) { 
                                return $estadoprogramacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();  

                    $programaciones = collect([
                            $item->programacionsubcliente, 
                            $item->programacionsubclienteauditoria, 
                            $item->programacionsubclientecomun
                        ])->filter();
                    
                        $resultadoprog = $programaciones
                            ->flatMap(function ($programacion) { 
                                return $programacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();                    

                    $informesubido = collect([
                            $item->documentacionsubcliente, 
                            $item->documentacionsubclienteauditoria, 
                            $item->documentacionsubclientecomun
                        ])->filter();
                        
                        $resultadoinforme = $informesubido
                            ->flatMap(function ($informe) { 
                                return $informe;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accion', $item->accionnombre)
                    ->first();           

                    $informefinalsubido = collect([
                            $item->informesfinales, 
                            $item->informesfinalesauditoria, 
                            $item->informesfinalescomun
                        ])->filter();
                        
                        $resultadoinformefinal = $informefinalsubido
                            ->flatMap(function ($informefinal) { 
                                return $informefinal;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $provinformes = collect([
                            $item->provinfofinal, 
                            $item->provinfofinalauditoria, 
                            $item->provinfofinalcomun
                        ])->filter();
                        
                        $resultadoprovinformes = $provinformes
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $resultadopagoinformefinal = $item->pagoservicioinformefinal()
                        ->where('provinfofinalid', $item->provinfofinalid)
                        ->where('tipomovimiento', 'EGRESO')
                    ->first();

                    $pagobateria = collect([
                        $item->programacionsubcliente,
                        $item->programacionsubclienteauditoria,
                        $item->programacionsubclientecomun
                    ])->filter();
                    
                    $resultadopago = $pagobateria
                        ->flatMap(fn($pago) => $pago)
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();
                    
                    if ($resultadopago) {
                        $programacionId = $resultadopago->id;
                        $detallerecibo = Detallerecibo::where('programacionid', $programacionId)
                                                      ->where('tipomovimiento', 'EGRESO')
                                                      ->first();
                    
                        if ($detallerecibo) {
                            $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                        } else {
                            $pagoservicioinforme = $resultadopago->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : null;
                        }
                    } else {
                        $pagoservicioinforme = null;
                    }

                $fechaAtencion = $resultadoestado ? $resultadoestado->fechaatencionprogramacion : null;
                $fechaprogramacion = $resultadoprog ? $resultadoprog->fechaasignada : null;
                $idprogramacion = $resultadoprog ? $resultadoprog->id : null;
                $nrofacturaprog = $resultadoprog ? $resultadoprog->nrofactura : null;
                $informedocumentacion = $resultadoinforme ? $resultadoinforme->created_at->toDateString() : null;
                $informedocumentacionfinal = $resultadoinformefinal ? $resultadoinformefinal->created_at->toDateString() : null;
                $pagoservicioinformefinal = in_array($item->id, [3173, 3178, 3187, 3043]) 
                ? 'PROCESADO' 
                : ($resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null);
                $nrofacturainformefinal = $resultadoprovinformes ? $resultadoprovinformes->nrofactura : null;

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'pagoservicio' => $item->pagoservicio,
                    'preciocompra' => $item->preciocompra,
                    'clienteitaid' => $item->clienteitaid,
                    'clienteitanombre' => $item->clienteitanombre,
                    'clienteauditoriaid' => $item->clienteauditoriaid,
                    'clienteauditorianombre' => $item->clienteauditorianombre,
                    'clientecomunid' => $item->clientecomunid,
                    'clientecomunnombre' => $item->clientecomunnombre,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechaprogramacion' => $fechaprogramacion,
                    'informedocumentacion' => $informedocumentacion,
                    'informedocumentacionfinal' => $informedocumentacionfinal,
                    'pagoservicioinforme' => $pagoservicioinforme,
                    'pagoservicioinformefinal' => $pagoservicioinformefinal,
                    'idprogramacion' => $idprogramacion,
                    'fechabateria' => $item->fechabateria,
                    'provinfofinalid' => $item->provinfofinalid,
                    'nrofacturaprog' => $nrofacturaprog,
                    'nrofacturainformefinal' => $nrofacturainformefinal,
                ];
            }
            $result[] = [
                'proveedorasignado' => $item->proveedorasignado,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'fechabateria' => $item->fechabateria,
            ];
        }

        // Generar el PDF con los datos reconstruidos
        /* $pdf = Pdf::loadView('admin.caja.cuentaspagar.reporte', compact('result'));

        return $pdf->download('Reporte_Cuentas_Pagar.pdf'); */
        /* $pdf = Pdf::loadView('admin.caja.cuentaspagar.reporte', compact('result'));

        return $pdf->stream('Reporte_Cuentas_Pagar.pdf'); */
        $filename = 'REPORTE_CUENTAS_PAGAR.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($result) {
            $file = fopen('php://output', 'w');

            // Escribe la cabecera
            fputcsv($file, ['Proveedor', 'Cliente', 'Estudio/Especialidad', 'Fecha_Prog', 'Pago', 'Informe', 'Factura'], ';');

            foreach ($result as $item) {
                foreach ($item['acciones'] as $accion) {
                    if (
                        !is_null($accion['fechaprogramacion']) &&
                        is_null($accion['pagoservicioinforme']) &&
                        !is_null($accion['fechaatencionprogramacion']) &&
                        $accion['accion'] !== 'INFORME FINAL'
                    ) {
                        fputcsv($file, [
                            $item['proveedorasignado'],
                            $accion['clienteitanombre'] . $accion['clienteauditorianombre'] . $accion['clientecomunnombre'],
                            $accion['accion'],
                            $accion['fechaprogramacion'],
                            $accion['preciocompra'],
                            $accion['informedocumentacion'] ?? 'PENDIENTE',
                            $accion['nrofacturaprog'] ?? 'PENDIENTE'
                        ], ';');
                    }

                    if (is_null($accion['pagoservicioinformefinal']) && $accion['accion'] === 'INFORME FINAL') {
                        fputcsv($file, [
                            $item['proveedorasignado'],
                            $accion['clienteitanombre'] . $accion['clienteauditorianombre'] . $accion['clientecomunnombre'],
                            $accion['accion'],
                            $accion['informedocumentacionfinal'] ?? 'PENDIENTE',
                            $accion['preciocompra'],
                            $accion['informedocumentacionfinal'] ?? 'PENDIENTE',
                            $accion['nrofacturaprog'] ?? 'PENDIENTE'
                        ], ';');
                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
//

//INGRESOS EXTERNOS
    public function ingresosexternos()
    {
        $sucursal = auth()->user()->sucursal;
        $consolidados = Consolidadocaja::all();
        $bancos = Banco::all();
        $cuentas = CuentasBancos::where('estado', 'ACTIVO')->get();
        $ultimoId = Recibo::max('id');
        $siguienteId = $ultimoId ? $ultimoId + 1 : 1;
        $rolusuario = auth()->user()->getRoleNames()->first();
        $user = auth()->user();
        $usuarioAutenticado = auth()->user()->name;
        $proveedores = Proveedor::orderBy('proveedor')->get();
        $hoy = now();
        if ($hoy->dayOfWeek == 1) {
            $fechaAyer = $hoy->copy()->subDays(2)->toDateString();
        } else {
            $fechaAyer = $hoy->copy()->subDay()->toDateString();
        }

        $registroCierreCajaAyer = DB::table('cierrecaja')
            ->where('usuariocierrenombre', $usuarioAutenticado)
            ->whereDate('updated_at', $fechaAyer)
            ->orderBy('updated_at', 'desc')
            ->first();

        $codigoAprobacion = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.ingreso.index')
            ->where('estado', 'expirado')
            ->exists();

        $mostrarVista = $registroCierreCajaAyer || $codigoAprobacion;

        $usuarioArqueoId = auth()->user()->id; 
        $arqueo = ArqueoCaja::where('usuarioarqueoid', $usuarioArqueoId)->first();

        return view('admin.caja.ingreso.ingresosexternos', [
            'mostrarVista' => $mostrarVista,
            'sucursal' => $sucursal,
            'consolidados' => $consolidados,
            'bancos' => $bancos,
            'siguienteId' => $siguienteId,
            'rolusuario' => $rolusuario,
            'proveedores' => $proveedores,
            'arqueo' => $arqueo,
            'cuentas' => $cuentas
        ]);
    }
    public function buscarPorProveedoringresoexterno(Request $request)   
    {
        $entrada = $request->input('proveedorid');
        $tipoproveedor = $request->input('tipocliente');

        if (!in_array($tipoproveedor, ['medico'])) {
            return response()->json(['error' => 'Tipo de cliente no válido'], 400);
        }
        $proveedorNit = null;
        $proveedorCi = null;
        switch ($tipoproveedor) {
            case 'medico':
                $proveedor = Proveedor::where('proveedor', $entrada)
                    ->orWhere('proveedor', 'LIKE', "%{$entrada}%")
                    ->first(['id', 'proveedor', 'proveedor']);
                break;
            default:
                $proveedor = null;
        }

        if (!$proveedor) {
            return response()->json(['error' => 'Proveedor no encontrado'], 404);
        }

        $proveedorNit = $proveedor->nit;
        $proveedorCi = $proveedor->ci;

        $registrosProgramacion = ProgramacionSubCliente::where('proveedorNombre', $proveedor->proveedor)
            ->whereNull('deleted_at')
            ->where('pagoservicio','EXTERNO')
            ->get()
            /* ->map(function ($registro) {

                $detallerecibo = Detallerecibo::where('programacionid', $registro->id) 
                    ->latest('created_at')
                    ->first();

                if ($detallerecibo) {
                    if ($detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'INGRESO') {
                        return null;
                    }

                    if ($detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'INGRESO') {
                        $registro->preciocompra = $detallerecibo->saldo;
                    }
                } */

                ->reject(function ($registro) {
                    /* $existeEnBateria = BateriaSubCliente::where('fechabateria', $registro->fechabateria)
                        ->where('accionnombre', $registro->accionnombre)
                        ->where('proveedorasignado', $registro->proveedornombre)
                        ->where(function ($query) use ($registro) {
                            $query->where('clienteitaid', $registro->clienteitaid)
                                  ->orWhere('clienteauditoriaid', $registro->clienteauditoriaid)
                                  ->orWhere('clientecomunid', $registro->clientecomunid);
                        })
                        ->where('pagoatencion', 'PAGO PROCESADO')
                        ->exists();
            
                    if ($existeEnBateria) {
                        return true;
                    } */

                    $existeEnBateria = BateriaSubCliente::where('fechabateria', $registro->fechabateria)
                        ->where('accionnombre', $registro->accionnombre)
                        ->where('proveedorasignado', $registro->proveedornombre)
                        ->whereRaw("
                            CASE 
                                WHEN ? IS NOT NULL THEN clienteitaid = ? 
                                WHEN ? IS NOT NULL THEN clienteauditoriaid = ? 
                                WHEN ? IS NOT NULL THEN clientecomunid = ? 
                            END
                        ", [
                            $registro->clienteitaid, $registro->clienteitaid,
                            $registro->clienteauditoriaid, $registro->clienteauditoriaid,
                            $registro->clientecomunid, $registro->clientecomunid
                        ])
                        ->where('pagoatencion', 'PAGO PROCESADO')
                        ->where('pagoservicio','INTERNO')
                        ->exists();
            
                    if ($existeEnBateria) {
                        return true;
                    }

                    $detallerecibo = Detallerecibo::where('programacionid', $registro->id)
                        ->where('tipomovimiento', 'INGRESO')
                        ->latest('created_at')
                        ->first();
            
                    if ($detallerecibo && $detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'INGRESO') {
                        return true;
                    }
                    return false;
                })
                ->map(function ($registro) {
                    $detallerecibo = Detallerecibo::where('programacionid', $registro->id)
                        ->latest('created_at')
                        ->first();
            
                    if ($detallerecibo && $detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'INGRESO') {
                        $registro->precio = $detallerecibo->saldo;
                    }

                switch (true) {
                    case isset($registro->clienteitaid):
                        $registro->tramite = TramitesubCliente::where('clienteitaid', $registro->clienteitaid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;

                    case isset($registro->clienteid):
                        $registro->tramite = TramitesubCliente::where('clienteid', $registro->clienteid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;

                    case isset($registro->clientecomunid):
                        $registro->tramite = TramitesubCliente::where('clientecomunid', $registro->clientecomunid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;

                    case isset($registro->clienteauditoriaid):
                        $registro->tramite = TramitesubCliente::where('clienteauditoriaid', $registro->clienteauditoriaid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break;
                }
                return $registro;
            })
            ->filter()
        ->values();

        // Obtener registros de la tabla ProveedorInformesFinales
        $registrosProveedorInformesFinales = ProveedorInformefinal::where('proveedorasignado', $proveedor->proveedor)
            ->whereNull('deleted_at')
            ->where('pagoservicio','EXTERNO')
            ->get()
            ->map(function ($registro) {
                $detallerecibo = Detallerecibo::where('provinfofinalid', $registro->id) 
                    ->latest('created_at')
                    ->first();

                if ($detallerecibo) {
                    if ($detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'INGRESO') {
                        return null;
                    }

                    if ($detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'INGRESO') {
                        $registro->preciocompra = $detallerecibo->saldo;
                    }
                }

                $existeDocumentacion = Informefinal::where(function ($query) use ($registro) {
                        if (!is_null($registro->clienteitaid)) {
                            $query->where('clienteitaid', $registro->clienteitaid);
                        } elseif (!is_null($registro->clienteauditoriaid)) {
                            $query->where('clienteauditoriaid', $registro->clienteauditoriaid);
                        } elseif (!is_null($registro->clientebancoid)) {
                            $query->where('clientebancoid', $registro->clientebancoid);
                        }
                    })
                    ->where('fechabateria', $registro->fechabateria)
                    ->exists();
                
                if (!$existeDocumentacion) {
                    return null;
                }
            
                // Obtener trámite según tipo de cliente
                switch (true) {
                    case isset($registro->clienteitaid):
                        $registro->tramite = TramitesubCliente::where('clienteitaid', $registro->clienteitaid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->latest('id')
                            ->value('tramite');
                        break;

                    case isset($registro->clienteid):
                        $registro->tramite = TramitesubCliente::where('clienteid', $registro->clienteid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->latest('id')
                            ->value('tramite');
                        break;

                    case isset($registro->clientecomunid):
                        $registro->tramite = TramitesubCliente::where('clientecomunid', $registro->clientecomunid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->latest('id')
                            ->value('tramite');
                        break;

                    case isset($registro->clienteauditoriaid):
                        $registro->tramite = TramitesubCliente::where('clienteauditoriaid', $registro->clienteauditoriaid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->latest('id')
                            ->value('tramite');
                        break;
                }
                return $registro;
            })
            ->filter()
        ->values();

        $registrosCuentasporPagar = CuentasPagar::where('proveedorNombre', $proveedor->proveedor)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($registro) {

                $detallerecibo = Detallerecibo::where('cuentapagarid', $registro->id) 
                    ->latest('created_at')
                    ->first();

                if ($detallerecibo) {
                    if ($detallerecibo->estado == 'PAGO PROCESADO' && $detallerecibo->tipomovimiento == 'EGRESO') {
                        return null;
                    }

                    if ($detallerecibo->estado == 'SALDO PENDIENTE' && $detallerecibo->tipomovimiento == 'EGRESO') {
                        $registro->preciocompra = $detallerecibo->saldo;
                    }
                }
                return $registro;
            })
            ->filter()
        ->values();

        $registros = $registrosProgramacion
            ->merge($registrosProveedorInformesFinales)
            ->merge($registrosCuentasporPagar);

        $usuarioNombre = Auth::user()->name;
        // Buscar proveedor por nombre
        $proveedor2 = Proveedor::where('proveedor', $entrada)->first(); // ajusta el campo si es otro (ej. 'nombrecompleto')

        $permisoExistefecha = false;

        if ($proveedor2) {
            $permisoExistefecha = PermisoCodigo::where('clienteid', $proveedor2->id)
                ->whereDate('fechaSolicitada', Carbon::today())
                ->where('usuarioSolicitante', $usuarioNombre)
                ->where('permisoSolicitado', 'admin.caja.ingresos.cambiarfecharegistro')
                ->where('estado', 'expirado')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('cajacentral as c')
                        ->whereRaw('c.fecharegistroreal > permisos_codigo.updated_at');
                })
                ->exists();
        }

        return response()->json([
            'proveedor' => $proveedor,
            'registros' => $registros,
            'permisoExistefecha' => $permisoExistefecha
        ]);
    }
    public function guardarCajaCentralingresoexterno(Request $request)
    {
        $usuarioAutenticadoid = Auth::user()->id;
        $usuarioAutenticadonombre = Auth::user()->name;
    
        $request->validate([
            'tipocliente' => '',
            'proveedorid' => '',
            'proveedornombre' => '',
            'subtotal' => '',
            'descuento' => '',
            'montoreal' => '',
            'montototal' => '',
            'ciudadregistro' => '',
            'programacionIds' => '',
            'area' => '',
            'detalle' => '',
            'nrofactura' => '',
            'nrobancarizaciondeposito' => '',
            'nrobancarizaciontransferencia' => '',
            'nrocheque' => '',
            'nrotarjeta' => '',
            'nroap' => '',
            'nroref' => '',
            'nrocuentadestinodeposito' => '',
            'nrocuentaorigendeposito' => '',
            'tipobancodeposito' => '',
            'nrocuentadestinotransferencia' => '',
            'nrocuentaorigentransferencia' => '',
            'tipobancotransferencia' => '',
            'tipobancocheque' => '',
            'nrocuentadestinocheque' => '',
            'tipobanco' => '',
            'tipocambio' => '',
            'tipomovimiento' => '',
            'tipotransaccion' => '',
            'tipotransaccion2' => '',
            'ciudadregistro' => '',
            'nombrebanco' => '',
            'numerobanco' => '',
            'usuarioregistroid' => '',
            'usuarioregistronombre' => '',
            'diferenciacontra' => '',
            'diferenciafavor' => '',
            'montoPagado' => '',
            'cambio' => '',
            'descuentoatc' => '',
            'montorealatc' => '',
            'billetecorte200' => '',
            'billetecorte100' => '',
            'billetecorte50' => '',
            'billetecorte20' => '',
            'billetecorte10' => '',
            'monedacorte5' => '',
            'monedacorte2' => '',
            'monedacorte1' => '',
            'monedacorte050' => '',
            'monedacorte020' => '',
            'monedacorte010' => '',
            'montototal' => '',
            'created_at' => '',
            'updated_at' => '',

            'cambiobilletecorte200' => '',
            'cambiobilletecorte100' => '',
            'cambiobilletecorte50' => '',
            'cambiobilletecorte20' => '',
            'cambiobilletecorte10' => '',
            'cambiomoneda5' => '',
            'cambiomoneda2' => '',
            'cambiomoneda1' => '',
            'cambiomoneda050' => '',
            'cambiomoneda020' => '',
            'cambiomoneda010' => '',
            
        ]);

        if (empty($request->programacionIds)) {
            // Redirigir con el mensaje en la sesión
            return redirect()->back()->with('infoerror', 'Debes seleccionar al menos un registro');
        }
        
        $programacionIds = explode(',', $request->programacionIds);
        $descuentos = explode(',', $request->descuentos);
        $pagos = explode(',', $request->pagos);

        $tipocliente = match ($request->tipocliente) {
            'medico' => 'MEDICO',
            default => $request->tipocliente,
        };

        $saldototal = $request->montoreal - ($request->montototal + $request->descuento);
        $saldototal = number_format($saldototal, 2, '.', ''); 

        $estado = ($saldototal == 0) ? 'PAGO PROCESADO' : 'SALDO PENDIENTE';

        $tipotransaccion = $request->tipotransaccion;
        if ($tipotransaccion == 'DEPOSITO_BANCARIO') {
            $tipotransaccion = 'DEPOSITO BANCARIO';
        } elseif ($tipotransaccion == 'TRANSFERENCIA_BANCARIA') {
            $tipotransaccion = 'TRANSFERENCIA BANCARIA';
        } elseif (in_array($tipotransaccion, ['EFECTIVO', 'CHEQUE', 'ATC'])) {
        }

        $tipotransaccion2 = $request->tipotransaccion2;
        if ($tipotransaccion2 == 'DEPOSITO_BANCARIO') {
            $tipotransaccion2 = 'DEPOSITO BANCARIO';
        } elseif ($tipotransaccion2 == 'TRANSFERENCIA_BANCARIA') {
            $tipotransaccion2 = 'TRANSFERENCIA BANCARIA';
        } elseif (in_array($tipotransaccion2, ['EFECTIVO', 'CHEQUE', 'ATC'])) {
        }

        $fechaCreacion = $request->created_at
        ? Carbon::createFromFormat('Y-m-d\TH:i', $request->created_at)
        : now();

        $fechaActualizacion = $request->updated_at
        ? Carbon::createFromFormat('Y-m-d\TH:i', $request->updated_at)
        : now();

        $recibo = Recibo::create([
            'ciudadregistro' => $request->ciudadregistro,
            'usuarioregistroid' => $usuarioAutenticadoid,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
            'tipocliente' => $tipocliente,
            'proveedorid' => $request->proveedorid,
            'proveedornombre' => $request->proveedornombre,
            'tipomovimiento' => 'INGRESO',
            'subtotal' => $request->subtotal,
            'descuentototal' => $request->descuento,
            'montototal' => $request->montototal,
            'estado' => $estado,
            'saldototal' => $saldototal,
            'created_at' => $fechaCreacion,
            'updated_at' => $fechaActualizacion,
        ]);

        // Obtener el HTML del recibo desde la solicitud
        $html = $request->input('html_recibo');
        
        if (!$html) {
            return redirect()->back()->with('error', 'No se recibió HTML para generar el recibo');
        }

        // Obtener el ID del usuario autenticado
        $usuarioAutenticadoid = Auth::id();  // O como estés obteniendo el ID del usuario

        // Nombre del archivo con timestamp
        $nombreArchivo = 'recibo_' . time() . '.html';

        // Ruta del archivo donde lo queremos guardar
        $rutaDirectorio = public_path('documentacioncaja/ingresos/' . $usuarioAutenticadoid);

        // Verificar si la ruta existe y si no, crearla
        if (!File::exists($rutaDirectorio)) {
            File::makeDirectory($rutaDirectorio, 0777, true);  // Crear directorios de forma recursiva
        }

        // Ruta completa para guardar el archivo
        $rutaArchivo = $rutaDirectorio . '/' . $nombreArchivo;

        // Guardar el HTML en un archivo
        File::put($rutaArchivo, $html);

        Cajacentral::create([
            'tipocliente' => $tipocliente,
            'proveedorid' => $request->proveedorid,
            'proveedornombre' => $request->proveedornombre,
            'subtotal' => $request->subtotal,
            'descuento' => $request->descuento,
            'montototal' => $request->montototal,
            'nrorecibo' => $recibo->id,
            'saldo' => $saldototal,
            'estado' => $estado,
            'area' =>  $request->area,
            'detalle' => $request->detalle,
            'nrofactura' => $request->nrofactura,
            'nrobancarizaciontransferencia' => $request->nrobancarizaciontransferencia,
            'nrobancarizaciondeposito' => $request->nrobancarizaciondeposito,
            'nrocheque' => $request->nrocheque,
            'nrotarjeta' => $request->nrotarjeta,
            'nroap' => $request->nroap,
            'nroref' => $request->nroref,
            'nrocuentadestinodeposito' => $request->nrocuentadestinodeposito,
            'nrocuentaorigendeposito' => $request->nrocuentaorigendeposito,
            'tipobancodeposito' => $request->tipobancodeposito,
            'nrocuentadestinotransferencia' => $request->nrocuentadestinotransferencia,
            'nrocuentaorigentransferencia' => $request->nrocuentaorigentransferencia,
            'tipobancotransferencia' => $request->tipobancotransferencia,
            'tipobancocheque' => $request->tipobancocheque,
            'tipobanco' => $request->tipobanco,
            'tipocambio' => $request->tipocambio,
            'tipomovimiento' => 'INGRESO',
            'tipotransaccion' => $tipotransaccion,
            'tipotransaccion2' => $tipotransaccion2,
            'ciudadregistro' => $request->ciudadregistro,
            'nombrebanco' => $request->nombrebanco,
            'numerobanco' => $request->numerobanco,
            'usuarioregistroid' => $usuarioAutenticadoid,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
            'estadorevisioncierre' => 'DOCUMENTACION PENDIENTE',
            'diferenciafavor' => $request->diferenciafavor,
            'diferenciacontra' => $request->diferenciacontra,
            'montopago' => $request->montoPagado,
            'montodevuelto' => $request->cambio,
            'descuentoatc' => ($tipotransaccion === 'ATC') ? $request->montototal * 0.02 : 0,
            'documentorespaldo' => $nombreArchivo,
            'nrocuentadestinoatc' => ($tipotransaccion === 'ATC') ? 3000189269 : null,
            'nrocuentadestinocheque' => ($tipotransaccion === 'CHEQUE') ? 3000189269 : null,
            'created_at' => $fechaCreacion,
            'updated_at' => $fechaActualizacion,
            'fecharegistroreal' => now(),
        ]);

        foreach ($programacionIds as $index => $programacionId) {
            $programacion = ProgramacionSubCliente::find($programacionId);
            $proveedor = ProveedorInformeFinal::find($programacionId);
            $cuentapagar = CuentasPagar::find($programacionId);
        
            // Verificar si ya existe un registro en Detallerecibo con el mismo programacionid
            /* $ultimoDetalleRecibo = Detallerecibo::where('programacionid', $programacionId)
                ->orwhere('provinfofinalid', $programacionId)
                ->orwhere('cuentapagarid', $programacionId)
                ->orderBy('created_at', 'desc')
                ->first(); */

            $ultimoDetalleRecibo = Detallerecibo::where(function ($query) use ($programacionId) {
                    $query->where('programacionid', $programacionId)
                          ->orWhere('provinfofinalid', $programacionId);
                })
                ->where('tipomovimiento', 'INGRESO')
                ->orderBy('created_at', 'desc') 
                ->first();

            /* if ($ultimoDetalleRecibo) {
                $subtotalDetalle = $ultimoDetalleRecibo->saldo;
                $descuentoDetalle = $descuentos[$index];
                $pagoDetalle = $pagos[$index];
            } else {
                if ($programacion) {
                    $subtotalDetalle = $programacion->precio - $programacion->preciocompra;
                } elseif ($proveedor) {
                    $subtotalDetalle = $proveedor->preciocompra;
                } elseif ($cuentapagar) {
                    $subtotalDetalle = $cuentapagar->preciocompra;
                } else {
                    $subtotalDetalle = 0;
                }
                $descuentoDetalle = $descuentos[$index];
                $pagoDetalle = $pagos[$index];
            } */

            if ($ultimoDetalleRecibo) {
                $subtotalDetalle = is_numeric(str_replace(',', '.', $ultimoDetalleRecibo->saldo)) 
                    ? (float) str_replace(',', '.', $ultimoDetalleRecibo->saldo) 
                    : 0;
                $descuentoDetalle = is_numeric(str_replace(',', '.', $descuentos[$index])) 
                    ? (float) str_replace(',', '.', $descuentos[$index]) 
                    : 0;
                $pagoDetalle = is_numeric(str_replace(',', '.', $pagos[$index])) 
                    ? (float) str_replace(',', '.', $pagos[$index]) 
                    : 0;
            } else {
                if ($programacion) {
                    $subtotalDetalle = (is_numeric(str_replace(',', '.', $programacion->precio)) 
                        && is_numeric(str_replace(',', '.', $programacion->preciocompra))) 
                        ? (float) (str_replace(',', '.', $programacion->precio) - str_replace(',', '.', $programacion->preciocompra)) 
                        : 0;
                } elseif ($proveedor && is_numeric(str_replace(',', '.', $proveedor->preciocompra))) {
                    $subtotalDetalle = (float) str_replace(',', '.', $proveedor->preciocompra);
                } elseif ($cuentapagar && is_numeric(str_replace(',', '.', $cuentapagar->preciocompra))) {
                    $subtotalDetalle = (float) str_replace(',', '.', $cuentapagar->preciocompra);
                } else {
                    $subtotalDetalle = 0;
                }
            
                $descuentoDetalle = is_numeric(str_replace(',', '.', $descuentos[$index])) 
                    ? (float) str_replace(',', '.', $descuentos[$index]) 
                    : 0;
                $pagoDetalle = is_numeric(str_replace(',', '.', $pagos[$index])) 
                    ? (float) str_replace(',', '.', $pagos[$index]) 
                    : 0;
            }
            

            $subtotalDetalle = is_numeric($subtotalDetalle) ? floatval($subtotalDetalle) : 0;

            $saldoDetalle = $subtotalDetalle - $descuentoDetalle - $pagoDetalle;
            $saldoDetalle = number_format($saldoDetalle, 2, '.', '');

            $estadoDetalle = ($saldoDetalle == 0) ? 'PAGO PROCESADO' : 'SALDO PENDIENTE';

            if ($estadoDetalle == 'PAGO PROCESADO') {
                Detallerecibo::where('programacionid', $programacionId)
                ->orwhere('provinfofinalid', $programacionId)
                    ->where('estado', '!=', 'PAGO PROCESADO')
                    ->update(['estado' => 'PAGO PROCESADO']);
            }

            if ($programacion) {
                $area = 'MEDICA';
            } elseif ($proveedor) {
                $area = 'INFORME FINAL';
            } elseif ($cuentapagar) {
                $area = 'CUENTAS POR PAGAR';
            }

            Detallerecibo::create([
                'reciboid' => $recibo->id,
                'programacionid' => $programacion ? $programacionId : null,
                'provinfofinalid' => $proveedor ? $programacionId : null,
                'cuentapagarid' => $cuentapagar ? $programacionId : null,
                'usuarioregistroid' => $usuarioAutenticadoid,
                'usuarioregistronombre' => $usuarioAutenticadonombre,
                'clienteid' => $cuentapagar ? null : (
                    $programacion ? 
                        ($programacion->clienteitaid ?? $programacion->clienteauditoriaid ?? $programacion->clientecomunid) : 
                        ($proveedor ? 
                            ($proveedor->clienteitaid ?? $proveedor->clienteauditoriaid ?? $proveedor->clientecomunid) : 
                        null)
                ),

                'clientenombre' => $cuentapagar ? null : (
                        $programacion ? 
                            ($programacion->clienteitanombre ?? $programacion->clienteauditorianombre ?? $programacion->clientecomunnombre) : 
                            ($proveedor ? 
                                ($proveedor->clienteitanombre ?? $proveedor->clienteauditorianombre ?? $proveedor->clientecomunnombre) : 
                            null)
                    ),

                'area' => $area,
                /* 'detalle' => $programacion ? $programacion->accionnombre : $proveedor->accionnombre, */
                'detalle' => $cuentapagar ? $cuentapagar->detalle : ($programacion ? $programacion->accionnombre : $proveedor->accionnombre),
                /* 'fechabateria' => $programacion ? $programacion->fechabateria : $proveedor->fechabateria,
                'fechaatencion' => $programacion ? $programacion->fechaasignada : $proveedor->fechaasignada,
                'servicio' => $programacion ? $programacion->servicio : $proveedor->servicio,
                'proveedoratencion' => $programacion ? $programacion->proveedornombre : $proveedor->proveedornombre, */
                'fechabateria' => $cuentapagar ? $cuentapagar->fechaasignada : ($programacion ? $programacion->fechabateria : $proveedor->fechabateria),
                'fechaatencion' => $cuentapagar ? $cuentapagar->fechaasignada : ($programacion ? $programacion->fechaasignada : $proveedor->fechaasignada),
                'servicio' => $cuentapagar ? $cuentapagar->detalle : ($programacion ? $programacion->servicio : $proveedor->servicio),
                'proveedoratencion' => $cuentapagar ? null : ($programacion ? $programacion->proveedornombre : $proveedor->proveedorasignado),
                'subtotal' => $subtotalDetalle,
                'descuento' => $descuentoDetalle,
                'montototal' => $pagoDetalle,
                'saldo' => $saldoDetalle,
                'estado' => $estadoDetalle,
                'tipomovimiento' => 'INGRESO',
                'tipotransaccion' => $tipotransaccion,
                'descuentoatc' => ($tipotransaccion === 'ATC') ? $pagoDetalle * 0.02 : 0,
                'created_at' => $fechaCreacion,
                'updated_at' => $fechaActualizacion,
            ]);
        }

        $usuarioAutenticadoid = Auth::id();

        $arqueo = ArqueoCaja::where('usuarioarqueoid', $usuarioAutenticadoid)->first();

        if ($arqueo) {
            $arqueo->update([
                'billetecorte200' => $arqueo->billetecorte200 + $request->billetecorte200 - $request->cambiobilletecorte200,
                'billetecorte100' => $arqueo->billetecorte100 + $request->billetecorte100 - $request->cambiobilletecorte100,
                'billetecorte50' => $arqueo->billetecorte50 + $request->billetecorte50 - $request->cambiobilletecorte500,
                'billetecorte20' => $arqueo->billetecorte20 + $request->billetecorte20 - $request->cambiobilletecorte20,
                'billetecorte10' => $arqueo->billetecorte10 + $request->billetecorte10 - $request->cambiobilletecorte10,
                'monedacorte5' => $arqueo->monedacorte5 + $request->monedacorte5 - $request->cambiomoneda5,
                'monedacorte2' => $arqueo->monedacorte2 + $request->monedacorte2 - $request->cambiomoneda2,
                'monedacorte1' => $arqueo->monedacorte1 + $request->monedacorte1 - $request->cambiomoneda1,
                'monedacorte050' => $arqueo->monedacorte050 + $request->monedacorte050 - $request->cambiomoneda050,
                'monedacorte020' => $arqueo->monedacorte020 + $request->monedacorte020 - $request->cambiomoneda020,
                'monedacorte010' => $arqueo->monedacorte010 + $request->monedacorte010 - $request->cambiomoneda010,
            ]);

             // Calcular el consolidado efectivo sumando billetes y monedas
                $consolidadoEfectivo = 
                ($request->billetecorte200 * 200) +
                ($request->billetecorte100 * 100) +
                ($request->billetecorte50 * 50) +
                ($request->billetecorte20 * 20) +
                ($request->billetecorte10 * 10) +
                ($request->monedacorte5 * 5) +
                ($request->monedacorte2 * 2) +
                ($request->monedacorte1 * 1) +
                ($request->monedacorte050 * 0.50) +
                ($request->monedacorte020 * 0.20) +
                ($request->monedacorte010 * 0.10);

            // Obtener el consolidado de caja
            $consolidado = Consolidadocaja::where('usuarioconsolidadoid', $usuarioAutenticadoid)->first();

            if ($consolidado) {
                // Actualizar el consolidado efectivo sumando el total calculado
                $consolidado->update([
                    'consolidadoefectivo' => $consolidado->consolidadoefectivo + $consolidadoEfectivo,
                ]);
            }
        }

        // Determinamos qué columna debemos actualizar según el tipo de transacción
        switch ($tipotransaccion) {
            case 'DEPOSITO BANCARIO':
                $columna = 'consolidadodeposito';
                break;
            case 'TRANSFERENCIA BANCARIA':
                $columna = 'consolidadotransferencia';
                break;
            case 'CHEQUE':
                $columna = 'consolidadocheque';
                break;
            case 'ATC':
                return redirect()->route('admin.caja.ingreso.ingresosexternos')
                ->with('info', 'Registro guardado correctamente')
                ->with('montototal', $request->montototal)
                ->with('tipotransaccion', $request->tipotransaccion)
                ->with('tipotransaccion2', $request->tipotransaccion2);
            case 'EFECTIVO':
                return redirect()->route('admin.caja.ingreso.ingresosexternos')
                ->with('info', 'Registro guardado correctamente')
                ->with('montototal', $request->montototal)
                ->with('tipotransaccion', $request->tipotransaccion)
                ->with('tipotransaccion2', $request->tipotransaccion2);
            default:
                return response()->json(['error' => 'Tipo de transacción no válido.'], 400);
        }

        // Actualizamos el monto en la columna correspondiente de la tabla Consolidados
        $consolidado = Consolidadocaja::where('usuarioconsolidadoid', $usuarioAutenticadoid)->first();

        if ($consolidado) {
            // Si ya existe un registro, sumamos el monto total a la columna correspondiente
            $consolidado->$columna += $request->montototal;
            $consolidado->save();
        } else {
            // Si no existe un registro, lo creamos con el monto total en la columna correspondiente
            $consolidado = new Consolidadocaja();
            $consolidado->usuarioconsolidadoid = $usuarioAutenticadoid;
            $consolidado->$columna = $request->montototal;
            $consolidado->save();
        }
    
        return redirect()->route('admin.caja.ingreso.ingresosexternos')
            ->with('info', 'Registro guardado correctamente')
            ->with('montototal', $request->montototal)
            ->with('tipotransaccion', $request->tipotransaccion)
            ->with('tipotransaccion2', $request->tipotransaccion2);
    }
//

// RESUMEN FINANCIERO
    public function resumenfinanciero(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');
        $user = auth()->user()->name; // Asumiendo que el nombre del usuario autenticado es este.

        // Datos actuales
        $records = DB::table('programacionsubclientes')
            ->selectRaw("
                COALESCE(fechacredito, fechaasignada) as fechaasignada,  
                SUM(CASE 
                    WHEN precio IS NULL THEN 0
                    ELSE precio 
                END) as total_ingresos,
                SUM(CASE 
                    WHEN preciocompra IS NULL THEN 0
                    ELSE preciocompra 
                END) as total_egresos
            ")
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)
            ->whereNotNull('preciocompra')
            ->whereYear(DB::raw('COALESCE(fechacredito, fechaasignada)'), $year)
            ->whereMonth(DB::raw('COALESCE(fechacredito, fechaasignada)'), $month)
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('COALESCE(fechacredito, fechaasignada)'))
            ->get();

            // Datos para la gráfica
            $graphData = DB::table('cajacentral')
                ->selectRaw('DATE(created_at) as fecha, usuarioregistronombre, SUM(montoTotal) as total')
                ->whereBetween('created_at', [now()->subDays(7)->startOfDay(), now()->endOfDay()])
                ->where('tipoMovimiento', 'INGRESO')  // Filtra por "INGRESO"
                ->whereNull('deleted_at')  // Filtra por registros donde deleted_at es NULL
                ->groupBy(DB::raw('DATE(created_at)'), 'usuarioregistronombre')
                ->orderBy('fecha')
                ->orderBy('usuarioregistronombre')
                ->get();

            $graphDataegresos = DB::table('cajacentral')
                ->selectRaw('DATE(created_at) as fecha, usuarioregistronombre, SUM(montoTotal) as total')
                ->whereBetween('created_at', [now()->subDays(7)->startOfDay(), now()->endOfDay()])
                ->where('tipoMovimiento', 'EGRESO')  // Filtra por "INGRESO"
                ->whereNull('deleted_at')  // Filtra por registros donde deleted_at es NULL
                ->groupBy(DB::raw('DATE(created_at)'), 'usuarioregistronombre')
                ->orderBy('fecha')
                ->orderBy('usuarioregistronombre')
                ->get();

            if ($request->ajax()) {
                return response()->json($records);
            }
        return view('admin.caja.panel.resumenfinanciero', compact('year', 'month', 'records', 'graphData', 'graphDataegresos'));
    }
//

// ANULACIONES DE CAJA
    public function anularcaja(Request $request)   
    {
        $registros = collect();

        if ($request->filled('search')) {
            $search = $request->search;
            $registros = Cajacentral::where('id', 'like', "%$search%")->get();
        }

        $anulaciones = CajaCentral::withTrashed() // Incluye los registros eliminados
            ->where('estado', 'ANULADO')
            ->whereNotNull('deleted_at') // Asegura que deleted_at tenga un valor
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('admin.caja.anulaciones.anularcaja', compact('registros','anulaciones'));
    }
    public function anularregitrocaja(Request $request)
    {
        $request->validate([
            'seleccionados' => 'required|array',
            'motivoAnulacion' => 'required|string|max:255',
        ]);

        $idsSeleccionados = $request->input('seleccionados');
        $motivo = $request->input('motivoAnulacion');
        $fechaActual = now();
        $usuarioAutenticado = auth()->user()->name;

        try {
            $registros = DB::table('cajacentral')
                ->whereIn('id', $idsSeleccionados)
                ->get();

                foreach ($registros as $registro) {
                    $campo = '';
                    switch ($registro->tipotransaccion) {
                        case 'EFECTIVO':
                            $campo = 'consolidadoefectivo';
                            break;
                        case 'TRANSFERENCIA BANCARIA':
                            $campo = 'consolidadotransferencia';
                            break;
                        case 'ATC':
                            $campo = 'consolidadoatc';
                            break;
                        case 'DEPOSITO BANCARIO':
                            $campo = 'consolidadodeposito';
                            break;
                        case 'CHEQUE':
                            $campo = 'consolidadocheque';
                            break;
                    }
                    if ($campo) {
                        DB::table('consolidadoscaja')
                            ->where('usuarioconsolidadoid', $registro->usuarioregistroid)
                            ->decrement($campo, $registro->montototal);
                    }
                }

            DB::table('cajacentral')
                ->whereIn('id', $idsSeleccionados)
                ->update([
                    'estado' => 'ANULADO',
                    'deleted_at' => $fechaActual,
                    'motivoanulacion' => $motivo,
                    'usuarioanulacion' => $usuarioAutenticado,
                ]);

            $nrosRecibo = DB::table('cajacentral')
                ->whereIn('id', $idsSeleccionados)
                ->pluck('nrorecibo');

            DB::table('recibos')
                ->whereIn('id', $nrosRecibo)
                ->update([
                    'estado' => 'ANULADO',
                    
                ]);

            DB::table('detallerecibos')
                ->whereIn('reciboid', $nrosRecibo)
                ->update([
                    'estado' => 'ANULADO',
                    'deleted_at' => $fechaActual,
                ]);

            return redirect()->back()->with('info', 'Registros anulados correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al anular los registros. Inténtalo de nuevo.']);
        }
    }
//

// DEPOSITOS BANCARIOS DE EFECTIVO
    public function depositosbancarios(Request $request)
    {
        $userId = auth()->id();
        $rolUsuario = auth()->user()->getRoleNames()->first();
        $cuentas = CuentasBancos::where('estado', 'ACTIVO')->get();
        $fecha = $request->input('fecha', today()->toDateString());
        $usuarios = Consolidadocaja::select('usuarioconsolidadoID', 'usuarioconsolidadoNombre')->distinct()->get();
        $usuarioSeleccionado = $request->input('usuario', $userId);

        $registros = CajaCentral::where('tipomovimiento', 'INGRESO')
            ->where('tipotransaccion', 'EFECTIVO')
            ->when($usuarioSeleccionado, function ($query, $usuarioSeleccionado) {
                return $query->where('usuarioregistroid', $usuarioSeleccionado);
            })
            ->selectRaw('DATE(created_at) as fecha, usuarioregistroid, usuarioRegistroNombre, 
                        SUM(montoTotal) - SUM(diferenciaContra) + SUM(diferenciaFavor) as total')
            ->groupBy('fecha', 'usuarioregistroid', 'usuarioRegistroNombre')
            ->orderByDesc('fecha')
            ->get();

        $depositos = DepositosBancarios::whereIn('fecha', $registros->pluck('fecha'))
            ->whereIn('usuarioregistroid', $registros->pluck('usuarioregistroid'))
            ->get()
            ->keyBy(function ($item) {
                return $item->fecha . '_' . $item->usuarioregistroid . '_' . $item->monto;
            });

        foreach ($registros as $registro) {
            $clave = $registro->fecha . '_' . $registro->usuarioregistroid . '_' . $registro->total;
            if (isset($depositos[$clave])) {
                $deposito = $depositos[$clave];
                $registro->documentorespaldo = $deposito->documentorespaldo;
                $registro->documentofactura = $deposito->documentofactura;
                $registro->bancarizacion = $deposito->bancarizacion;
                $registro->bancodestino = $deposito->bancodestino;
            } else {
                $registro->documentorespaldo = null;
                $registro->documentofactura = null;
                $registro->bancarizacion = null;
                $registro->bancodestino = null;
            }
        }

        return view('admin.caja.ingreso.depositosbancarios', compact('registros', 'rolUsuario', 'usuarios', 'fecha', 'usuarioSeleccionado', 'cuentas'));
    }
    public function guardardepositobancario(Request $request)
    {
        $request->validate([
            'archivo' => '',
            'archivo3' => '',
            'registro_ids' => '',
            'bancarizacion' => '',
            'bancodestino' => '',
            'fecha' => '',
            'usuarioregistro' => '',
            'monto' => '',
        ]);
        $userId = auth()->id();
        $usuarioAutenticadoid = Auth::user()->id;
        $usuarioAutenticadonombre = Auth::user()->name;

        $archivo_name = null;
            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $carpetaCliente = public_path("/documentacioncaja/depositosbancarios/$userId");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }
                $archivo_name = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name);
            }
        $archivo_name3 = null;
            if ($request->hasFile('archivo3')) {
                $file = $request->file('archivo3');
                $carpetaCliente = public_path("/documentacioncaja/depositosbancarios/$userId");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }
                $archivo_name3 = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name3);
            }
            DepositosBancarios::create([
                'usuarioregistroid' => $usuarioAutenticadoid,
                'usuarioregistronombre' => $usuarioAutenticadonombre,
                'detalle' => 'DEPOSITO BANCARIO',
                'monto' => $request->monto,
                'fecha' => $request->fecha,
                'estado' => 'FINALIZADO',
                'salida' => 'CAJA',
                'destino' => 'BANCO',
                'tipotransaccion' => 'DEPOSITO BANCARIO',
                'bancarizacion' => $request->bancarizacion,
                'bancodestino' => $request->bancodestino,
                'documentorespaldo' => $archivo_name,
                'documentofactura' => $archivo_name3,
            ]);

            CajaCentral::where('tipomovimiento', 'INGRESO')
                ->where('tipotransaccion', 'EFECTIVO')
                ->where('usuarioregistronombre', $request->usuarioregistro)
                ->whereDate('created_at', $request->fecha)
                ->update([
                    'nrobancarizacionefectivo' => $request->bancarizacion,
                    'nrobancodestinoefectivo' => $request->bancodestino,
            ]);

        // Actualización de la tabla ArqueoCaja
        $arqueo = ArqueoCaja::where('usuarioarqueoid', $usuarioAutenticadoid)->first();
        if ($arqueo) {
            $arqueo->update([
                'billetecorte200' => 0,
                'billetecorte100' => 0,
                'billetecorte50' => 0,
                'billetecorte20' => 0,
                'billetecorte10' => 0,
                'monedacorte5' => 0,
                'monedacorte2' => 0,
                'monedacorte1' => 0,
                'monedacorte050' => 0,
                'monedacorte020' => 0,
                'monedacorte010' => 0,
            ]);
        }

        // Actualización de la tabla Consolidadocaja
        $consolidado = Consolidadocaja::where('usuarioconsolidadoid', $usuarioAutenticadoid)->first();
        if ($consolidado) {
            $consolidado->update([
                'consolidadoefectivo' => 0.00,
                'actualizaciondeposito' => today(),
            ]);
        }

        return redirect()->back()->with('info', 'Registro respaldado exitosamente.');
    }
//

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
        return view('admin.caja.ingreso.index');
    }
    public function listarEgresos()
    {
        return view('admin.caja.egreso.index');
    }
    public function cierreCaja_Egresos()
    {
        return view('admin.caja.egreso.cierre');
    }
    
}