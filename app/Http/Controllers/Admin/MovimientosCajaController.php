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
use PDF;
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
use App\Models\PreOrdenes;
/* NUEVO 011225 */
use App\Models\HistorialArqueocaja;

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
    public function verificarCodigo2(Request $request)
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
            return redirect()->route('admin.caja.egreso.cajaegresos')->with('info', 'CÓDIGO VÁLIDO, AHORA SI PUEDES CONTINUAR');

        } elseif ($codigoAprobacion && $codigoAprobacion->estado == 'expirado') {

            return back()->with('infoerror', 'EL CÓDIGO YA HA SIDO USADO, EL ACCESO ESTA BLOQUEADO');
        } else {

            return back()->with('infoerror', 'CÓDIGO INVALIDO O NO AUTORIZADO');
        }
    }
    public function verificarCodigo3(Request $request)
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
            return redirect()->route('admin.caja.ingreso.cierre')->with('info', 'CÓDIGO VÁLIDO, AHORA SI PUEDES CONTINUAR');

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
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->first();

            $registroCierreCaja = true;

            // ✅ Si existe un registro en cajacentral, debemos validar su cierre
            if ($ultimoRegistro) {
                $fechaUltimoRegistro = Carbon::parse($ultimoRegistro->created_at)->toDateString();

                // Si la fecha del último registro NO es hoy, exigimos cierre para esa fecha
                if ($fechaUltimoRegistro !== $hoy->toDateString()) {
                    $registroCierreCaja = DB::table('cierrecaja')
                        ->where('usuariocierreid', $idUsuario)
                        ->whereDate('fechacierre', $fechaUltimoRegistro)
                        ->exists();
                }
            }

            $codigoAprobacion = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
                ->whereDate('fechaSolicitada', $hoy->toDateString())
                ->where('permisoSolicitado', 'admin.ingreso.index')
                ->where('estado', 'expirado')
                ->exists();

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

            $motivoBloqueo = null;
            if ($ultimoRegistro && !$registroCierreCaja) {
                $motivoBloqueo = 'NO CERRASTE TU CAJA DEL DÍA ' . Carbon::parse($ultimoRegistro->created_at)->format('d/m/Y');
            } elseif ($restriccionDeposito) {
                $motivoBloqueo = 'NO REGISTRASTE EL DEPÓSITO DEL EFECTIVO DE AYER ANTES DE LAS 10:00 AM.';
            }

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

        $aperturascajas = Aperturacaja::orderBy('created_at', 'desc')
        ->take(10)
        ->get();
        
        $entrada = $request->input('clienteid');
        $tieneCredito = Credito::where('clienteid', $entrada)->get();

        $proveedores = ProveedoresServicios::select('id', 'razonsocial')->orderBy('razonsocial')->get();
        $clientesIta = Cliente::select('id', 'nombrecompleto')->orderBy('nombrecompleto')->get();
        $clientesAuditoria = ClienteAuditoria::select('id', 'nombrecompleto')->orderBy('nombrecompleto')->get();
        $clientesComunes = ClienteComun::select('id', 'nombrecompleto')->orderBy('nombrecompleto')->get();
        $medicos = Proveedor::select('id', 'proveedor')->orderBy('proveedor')->get();
        
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
            'clientesComunes' => $clientesComunes,
            'motivoBloqueo' => $motivoBloqueo,
            'medicos' => $medicos
        ]);
    }

    public function historialcierrescaja(Request $request) 
    {
        $programaciones = DB::table('programacionsubclientes')
            ->select('id', 'fechaasignada', 'precio', 'accionnombre', 'proveedornombre', 'clienteitaid', 'clienteitanombre', 'clienteauditoriaid', 'clienteauditorianombre', 'clientecomunid', 'clientecomunnombre', 'usuarioregistro', 
                DB::raw("SUM(precio) as total_programado"),
                DB::raw("(SELECT DATE(created_at) FROM detallerecibos WHERE programacionid = programacionsubclientes.id ORDER BY created_at DESC LIMIT 1) as fechapago"))
            ->groupBy('id', 'fechaasignada', 'precio', 'accionnombre', 'proveedornombre', 'clienteitaid', 'clienteitanombre', 'clienteauditoriaid', 'clienteauditorianombre', 'clientecomunid', 'clientecomunnombre','usuarioregistro')
            ->where('proveedornombre', '!=', 'PROVEEDOR AJENO')
            ->where('pagoservicio', '=', 'INTERNO')
            ->orderBy('fechaasignada', 'desc')
        ->get();

        $recibos = DB::table('detallerecibos')
        ->join('cajacentral', 'detallerecibos.reciboid', '=', 'cajacentral.nrorecibo')
        ->leftJoin('informesfinales', function ($join) {
            $join->on('informesfinales.fechabateria', '=', 'detallerecibos.fechabateria')
                ->on('informesfinales.servicio', '=', 'detallerecibos.servicio')
                ->whereNull('informesfinales.deleted_at')
                ->where(function ($query) {
                    $query->on('informesfinales.clienteitaid', '=', 'detallerecibos.clienteid')
                        ->orOn('informesfinales.clienteauditoriaid', '=', 'detallerecibos.clienteid');
                });
        })
        ->select(
            'detallerecibos.id',
            'detallerecibos.programacionid',
            'detallerecibos.fechaatencion',
            'detallerecibos.fechabateria',
            'detallerecibos.clienteid',
            'detallerecibos.clientenombre',
            'detallerecibos.detalle',
            'detallerecibos.proveedoratencion',
            'detallerecibos.montototal',
            'detallerecibos.descuentoatc',
            'detallerecibos.usuarioregistronombre',
            'detallerecibos.provinfofinalid',
            'detallerecibos.reciboid',
            'detallerecibos.subtotal',
            'detallerecibos.descuento',
            'detallerecibos.area',
            'detallerecibos.sucursalgasto',
            DB::raw("DATE(detallerecibos.created_at) as fecha"),
            DB::raw("SUM(detallerecibos.montototal + detallerecibos.descuento) as total_recibido"),
            'cajacentral.tipotransaccion',
            DB::raw("DATE(informesfinales.created_at) as fecha_informe_final")
        )
        ->whereIn('detallerecibos.area', ['MEDICA', 'INFORME FINAL'])
        ->where('detallerecibos.tipomovimiento', 'INGRESO')
        ->where('detallerecibos.estado', '!=', 'ANULADO')
        ->groupBy(
            'detallerecibos.id',
            'detallerecibos.programacionid',
            'detallerecibos.fechaatencion',
            'detallerecibos.fechabateria',
            'detallerecibos.clienteid',
            'detallerecibos.clientenombre',
            'detallerecibos.detalle',
            'detallerecibos.proveedoratencion',
            'detallerecibos.montototal',
            'detallerecibos.descuentoatc',
            'detallerecibos.usuarioregistronombre',
            'detallerecibos.provinfofinalid',
            'detallerecibos.reciboid',
            'detallerecibos.subtotal',
            'detallerecibos.descuento',
            'detallerecibos.area',
            'detallerecibos.sucursalgasto',
            DB::raw("DATE(detallerecibos.created_at)"),
            'cajacentral.tipotransaccion',
            DB::raw("DATE(informesfinales.created_at)")
        )
        ->orderBy(DB::raw("DATE(detallerecibos.created_at)"), 'desc')
        ->get();

        $consolidacion = [];
        foreach ($programaciones as $prog) {
            $consolidacion[] = (object)[
                'id'                     => $prog->id,
                'fechaasignada'          => $prog->fechaasignada,
                'total_programado'       => number_format($prog->total_programado, 2, '.', ''), 
            ];
        }

        $proveedores = DB::table('detallerecibos')
                    ->select('proveedoratencion')
                    ->distinct()
                    ->get();
        $query = DB::table('detallerecibos')
        ->select(
            'detallerecibos.*', 
            'depositosbancarios.created_at as depositosbancarios_created_at',
            'cajacentral.updated_at as cajacentral_updated_at',
            'cajacentral.fechabancarizacionatc as cajacentral_fechabancarizacionatc',
            'cajacentral.nrofactura as cajacentral_nrofactura',
            'cajacentral.ciudadregistro as cajacentral_ciudadregistro',
            'cajacentral.nrocuentadestinotransferencia as cajacentral_nrocuentadestinotransferencia',
            'cajacentral.nrobancarizaciontransferencia as cajacentral_nrobancarizaciontransferencia',
            'cajacentral.nrobancodestinoefectivo as cajacentral_nrobancodestinoefectivo',
            'cajacentral.nrobancarizacionefectivo as cajacentral_nrobancarizacionefectivo',
            'cajacentral.nrocuentadestinodeposito as cajacentral_nrocuentadestinodeposito',
            'cajacentral.nrobancarizaciondeposito as cajacentral_nrobancarizaciondeposito',
            'cajacentral.nrocuentadestinoatc as cajacentral_nrocuentadestinoatc',
            'cajacentral.nrobancarizacionatc as cajacentral_nrobancarizacionatc',
            'cajacentral.nrocuentadestinocheque as cajacentral_nrocuentadestinocheque',
            'cajacentral.nrocheque as cajacentral_nrocheque',
            'cajacentral.nrobancarizacioncheque as cajacentral_nrobancarizacioncheque',
            'cajacentral.estadorevisioncierre as cajacentral_estadorevisioncierre',
        )
        /* ->leftJoin('depositosbancarios', function($join) {
            $join->on(DB::raw('DATE(detallerecibos.created_at)'), '=', 'depositosbancarios.fecha')
                ->on('detallerecibos.usuarioregistronombre', '=', 'depositosbancarios.usuarioregistronombre');
        }) */
        ->leftJoin(DB::raw('(
            SELECT 
                fecha,
                usuarioregistronombre,
                MAX(created_at) as created_at
            FROM depositosbancarios
            GROUP BY fecha, usuarioregistronombre
        ) as depositosbancarios'), function ($join) {
            $join->on(DB::raw('DATE(detallerecibos.created_at)'), '=', 'depositosbancarios.fecha')
                ->on('detallerecibos.usuarioregistronombre', '=', 'depositosbancarios.usuarioregistronombre');
        })

        ->leftJoin('cajacentral', 'detallerecibos.reciboid', '=', 'cajacentral.nrorecibo')
        ->where('detallerecibos.estado', '<>', 'ANULADO');

        if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
            $fechaDesde = \Carbon\Carbon::parse($request->input('fecha_desde'))->startOfDay();
            $fechaHasta = \Carbon\Carbon::parse($request->input('fecha_hasta'))->endOfDay();
            
            $query->whereBetween('detallerecibos.created_at', [$fechaDesde, $fechaHasta]);
        }
        
        if ($request->filled('proveedoratencion')) {
            $query->where('detallerecibos.proveedoratencion', 'LIKE', '%' . $request->input('proveedoratencion') . '%');
        }
        if ($request->filled('clientenombre')) {
            $query->where('detallerecibos.clientenombre', 'LIKE', '%' . $request->input('clientenombre') . '%');
        }
        if ($request->filled('tipotransaccion')) {
            $query->where('detallerecibos.tipotransaccion', $request->input('tipotransaccion'));
        }
        if ($request->filled('tipomovimiento')) {
            $query->where('detallerecibos.tipomovimiento', $request->input('tipomovimiento'));
        }
        if ($request->filled('estado')) {
            $query->where('detallerecibos.estado', $request->input('estado'));
        }
        if ($request->filled('ciudad')) {
            $query->where('cajacentral.ciudadregistro', $request->input('ciudad'));
        }
        if ($request->filled('cuenta')) {
            $cuenta = $request->input('cuenta');
            $query->where(function ($q) use ($cuenta) {
                $q->where('cajacentral.nrocuentadestinotransferencia', $cuenta)
                ->orWhere('cajacentral.nrobancodestinoefectivo', $cuenta)
                ->orWhere('cajacentral.nrocuentadestinodeposito', $cuenta)
                ->orWhere('cajacentral.nrocuentadestinoatc', $cuenta)
                ->orWhere('cajacentral.nrocuentadestinocheque', $cuenta);
            });
        }
        
        $query->where('cajacentral.estado', '<>', 'ANULADO');

        $detalles = $query->get();
        $totalMontototal = $detalles->sum(function ($detalle) {
            return $detalle->montototal - ($detalle->descuentoatc ?? 0);
        });
        $cuentas = CuentasBancos::where('estado', 'ACTIVO')->get();

        $usuarioAutenticado = auth()->user();
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
            ->limit(15)
            ->get();

        return view('admin.caja.ingreso.historialcierrescaja', compact('consolidados','tiposTransaccion','cierrecajas','cuentas','consolidacion','programaciones', 'recibos','detalles', 'totalMontototal', 'proveedores'));
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
        if (!in_array($tipoCliente, ['clienteitaid', 'clienteauditoriaid', 'clientecomunid', 'proveedorid', 'medicoid'])) {
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
            case 'proveedorid':
                $cliente = Proveedoresservicios::where('ci', $entrada)->orWhere('id', $entrada)->first(['id', 'razonsocial', 'ci']);
                break;
            case 'medicoid':
                $cliente = Proveedor::where('nit', $entrada)->orWhere('id', $entrada)->first(['id', 'proveedor', 'nit']);
                break;
            default:
                $cliente = null;
        }
        if (!$cliente) {
            return response()->json(['error' => 'CLIENTE NO ENCONTRADO'], 404);
        }
        $clienteId = $cliente->id;
        $hoy = now()->toDateString();

        $registrosProgramacion = collect();
        if ($tipoCliente !== 'medicoid') {
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
        }

        $registrosInformesFinales = collect();
        if ($tipoCliente !== 'proveedorid' && $tipoCliente !== 'medicoid') {
            $registrosInformesFinales = ProveedorInformefinal::where($tipoCliente, $clienteId)
                ->where('pagoservicio', '!=', 'PAGO PROCESADO')
                ->whereNull('deleted_at')
                ->get()
                ->map(function ($registro) use ($clienteId) {
                    $detallerecibo = Detallerecibo::where('provinfofinalid', $registro->id)
                        ->where('tipomovimiento', 'INGRESO')
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
        }

        $registrosCuentasporCobrar = CuentasCobrar::where(function ($query) use ($clienteId, $cliente) {
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

        $registros = $registrosProgramacion->merge($registrosInformesFinales)->merge($registrosCuentasporCobrar);

        
        $tieneCredito = Credito::where('clienteid', $clienteId)->exists();
        $creditos = collect();

        if ($tieneCredito) {
            $creditos = Credito::where('clienteid', $clienteId)->whereNull('estado')->get();
        }

        $usuarioNombre = Auth::user()->name;

        $permisoExiste = PermisoCodigo::where('clienteid', $entrada)
            ->whereDate('fechaSolicitada', Carbon::today())
            ->where('usuarioSolicitante', $usuarioNombre)
            ->where('permisoSolicitado', 'admin.caja.ingresos.concederdescuentosingresos')
            ->where('estado', 'expirado')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('cajacentral as c')
                    ->whereRaw('c.created_at > permisos_codigo.updated_at');
            })
        ->exists();

        $permisoExistefecha = PermisoCodigo::where('clienteid', $entrada)
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

        return response()->json([
            'cliente' => $cliente,
            'registros' => $registros,
            'tieneCredito' => $tieneCredito,
            'creditos' => $creditos,
            'permitirDescuento' => $permisoExiste,
            'permisoExistefecha' => $permisoExistefecha
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
            'codautorizacion' => '',
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
            'proveedorid' => 'PROVEEDOR DE SERVICIO',
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
            'codautorizacion' => $request->codautorizacion,
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

            // AGREGAR REGISTRO A FACTURASEGRESO
                // Usuario autenticado
                $usuarioId = Auth::id();
                $usuarioNombre = Auth::user()->name;

                // Obtener NITCI y complemento
                $nitci = '';
                $complemento = '';

                // ID del cliente o proveedor
                $idEntidad = $request->clienteid ?? $request->proveedorid;

                // Consultar en las 3 tablas
                if ($tipocliente === 'ITA') {
                    $cliente = DB::table('clientes')->where('id', $idEntidad)->first();
                } elseif ($tipocliente === 'AUDITORIA') {
                    $cliente = DB::table('clienteauditorias')->where('id', $idEntidad)->first();
                } elseif ($tipocliente === 'COMUN') {
                    $cliente = DB::table('clientescomunes')->where('id', $idEntidad)->first();
                } else {
                    $cliente = null;
                }

                // Si encontró
                if ($cliente) {
                    $nitci = (!empty($cliente->nitci) && $cliente->nitci != '0') ? $cliente->nitci : ($cliente->ci ?? '');
                    $complemento = $cliente->cicomplemento ?? '0';
                    $sucursalcliente = $cliente->sucursal ?? '0';
                }

                // Insertar en facturasegreso
                if (!empty($request->nrofactura) && $request->nrofactura != '0') {
                    DB::table('facturasegreso')->insert([
                        'especificacion' => '2',
                        'fechafacturaduidim' => now(),
                        'nrofactura' => $request->nrofactura,
                        'codigoautorizacion' => $request->codautorizacion,
                        'nitci' => $nitci,
                        'complemento' => $complemento,
                        'razonsocial' => $request->clientenombre,
                        'total' => $request->montototal,
                        'ice' => '0.00',
                        'iehd' => '0.00',
                        'ipj' => '0.00',
                        'tasas' => '0.00',
                        'otronosujcredfiscaloiva' => '0.00',
                        'importeyexporteexterno' => '0.00',
                        'tasacero' => '0.00',
                        'subtotal' => $request->subtotal,
                        'descuento' => $request->descuento,
                        'giftcard' => '0.00',
                        'importebasecfdf' => $request->montototal,
                        'creditodebitofiscal' => $request->montototal * 0.13,
                        'estado' => 'VALIDO',
                        'codigocontrol' => '0.00',
                        'tipo' => '2',
                        'ciudad' => $sucursalcliente,
                        'usuarioregistroid' => $usuarioId,
                        'usuarioregistronombre' => $usuarioNombre,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            //

        foreach ($programacionIds as $index => $programacionId) {
            $programacion = null;
            $proveedor = null;
            $cuentapagar = null;
            if (str_ends_with($programacionId, 'CC')) {
                $cuentapagar = CuentasCobrar::find($programacionId);
            }
            if (!$cuentapagar) {
                $programacion = ProgramacionSubCliente::find($programacionId);
                $proveedor = ProveedorInformeFinal::find($programacionId);

                // 🔥 DECISIÓN REAL
                if ($proveedor && $proveedor->accionnombre === 'INFORME FINAL') {
                    // usar proveedor
                    $programacion = null;

                } else {
                    // usar programación (si existe)
                    $proveedor = null;
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

            $subtotalDetalle = is_numeric(str_replace(',', '.', $subtotalDetalle)) 
                ? (float) str_replace(',', '.', $subtotalDetalle) : 0;

            $descuentoDetalle = is_numeric(str_replace(',', '.', $descuentoDetalle)) 
                ? (float) str_replace(',', '.', $descuentoDetalle) : 0;

            $pagoDetalle = is_numeric(str_replace(',', '.', $pagoDetalle)) 
                ? (float) str_replace(',', '.', $pagoDetalle) : 0;

            $saldoDetalle = $subtotalDetalle - $descuentoDetalle - $pagoDetalle;
            $saldoDetalle = number_format($saldoDetalle, 2, '.', '');
            $estadoDetalle = ($saldoDetalle == 0) ? 'PAGO PROCESADO' : 'SALDO PENDIENTE';

            if ($estadoDetalle == 'PAGO PROCESADO') {
                if ($programacion) {
                    Detallerecibo::where('programacionid', $programacionId)
                        ->where('estado', '!=', 'PAGO PROCESADO')
                        ->update(['estado' => 'PAGO PROCESADO']);
                } elseif ($proveedor) {
                    Detallerecibo::where('provinfofinalid', $programacionId)
                        ->where('estado', '!=', 'PAGO PROCESADO')
                        ->update(['estado' => 'PAGO PROCESADO']);
                } elseif ($cuentapagar) {
                    Detallerecibo::where('cuentacobrarid', $programacionId)
                        ->where('estado', '!=', 'PAGO PROCESADO')
                        ->update(['estado' => 'PAGO PROCESADO']);
                }
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
                'codautorizacion' => $request->codautorizacion,
                
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
                'cajacentral.docfactura2',
                'cajacentral.docfactura3',
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
                'cajacentral.docfactura2',
                'cajacentral.docfactura3',
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
                ->whereNull('deleted_at')
                ->first();

            $registroCierreCaja = true;

            // ✅ Si existe un registro en cajacentral, debemos validar su cierre
            if ($ultimoRegistro) {
                $fechaUltimoRegistro = Carbon::parse($ultimoRegistro->created_at)->toDateString();

                // Si la fecha del último registro NO es hoy, exigimos cierre para esa fecha
                if ($fechaUltimoRegistro !== $hoy->toDateString()) {
                    $registroCierreCaja = DB::table('cierrecaja')
                        ->where('usuariocierreid', $idUsuario)
                        ->whereDate('fechacierre', $fechaUltimoRegistro)
                        ->exists();
                }
            }

            $codigoAprobacion = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
                ->whereDate('fechaSolicitada', $hoy->toDateString())
                ->where('permisoSolicitado', 'admin.ingreso.index')
                ->where('estado', 'expirado')
                ->exists();

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

            $motivoBloqueo = null;
            if ($ultimoRegistro && !$registroCierreCaja) {
                $motivoBloqueo = 'NO CERRASTE TU CAJA DEL DÍA ' . Carbon::parse($ultimoRegistro->created_at)->format('d/m/Y');
            } elseif ($restriccionDeposito) {
                $motivoBloqueo = 'NO REGISTRASTE EL DEPÓSITO DEL EFECTIVO DE AYER ANTES DE LAS 10:00 AM.';
            }
        //

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
            'rolusuario' => $rolusuario,
            'mostrarVista' => $mostrarVista,
            'motivoBloqueo' => $motivoBloqueo
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
                DB::raw('GROUP_CONCAT(detallerecibos.detalle SEPARATOR ", ") as detalle')
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
                'cajacentral.docfactura2',
                'cajacentral.docfactura3',
                'cajacentral.doccomprobante',
                'cajacentral.usuarioanulacion',
                DB::raw('GROUP_CONCAT(detallerecibos.detalle SEPARATOR ", ") as detalle')
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
                'cajacentral.docfactura2',
                'cajacentral.docfactura3',
                'cajacentral.doccomprobante',
                'cajacentral.usuarioanulacion'
            )
        ->get();

        $consolidados = Consolidadocaja::where('usuarioconsolidadonombre', $usuarioAutenticado->name)
            ->whereDate('updated_at', today())
            ->first();

        $tiposTransaccion = ['Efectivo', 'Cheque', 'ATC', 'Deposito Bancario', 'Transferencia Bancaria'];
        $montosCajaCentral = [];

        foreach ($tiposTransaccion as $tipo) {
            $montosCajaCentral[$tipo] = DB::table('cajacentral')
                ->where('usuarioregistronombre', $usuarioAutenticado->name)
                ->where('tipotransaccion', $tipo)
                ->whereDate('updated_at', today())
                ->sum('montototal');
        }

        /* if ($request->isMethod('post') && $request->has('accion')) {
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
        } */
        /* if ($request->isMethod('post') && $request->has('accion')) {
            $request->validate([
                'registro_ids' => 'required|array',
            ]);

            $authUsername = auth()->user()->name;
            $authUserId = auth()->id();

            if ($request->accion == 'aprobar') {
                DB::table('cajacentral')
                    ->whereIn('id', $request->registro_ids)
                    ->update([
                        'estadorevisioncierre' => 'CIERRE APROBADO',
                        'usuariorevisioncierre' => $authUsername
                    ]);

                return back()->with('info', 'Cierre aprobado exitosamente.');
            } elseif ($request->accion == 'cerrar') {
                $fechaCierre = now();

                // Si no hay registros seleccionados, insertar directamente el cierre en cero
                if (!$request->filled('registro_ids') || empty($request->registro_ids)) {
                    DB::table('cierrecaja')->insert([
                        'usuariocierrenombre' => $authUsername,
                        'usuariocierreid' => $authUserId,
                        'cierreefectivo' => 0.00,
                        'cierredeposito' => 0.00,
                        'cierretransferencia' => 0.00,
                        'cierrecheque' => 0.00,
                        'cierreatc' => 0.00,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'fechacierre' => $fechaCierre->startOfDay(),
                    ]);

                    return back()->with('info', 'Caja cerrada sin movimientos. Cierre registrado con montos en cero.');
                }

                // Actualiza registros seleccionados
                DB::table('cajacentral')
                    ->whereIn('id', $request->registro_ids)
                    ->update([
                        'estadorevisioncierre' => 'FINALIZADO',
                        'fechacierre' => $fechaCierre,
                        'usuariocierrecaja' => $authUsername
                    ]);

                // Traer registros seleccionados
                $registros = DB::table('cajacentral')
                    ->whereIn('id', $request->registro_ids)
                    ->get();

                // Agrupar por usuarioregistronombre y fecha (YYYY-MM-DD)
                $agrupados = $registros->groupBy(function ($item) {
                    return $item->usuarioregistronombre . '|' . Carbon::parse($item->created_at)->toDateString();
                });

                foreach ($agrupados as $clave => $items) {
                    [$usuarioRegistro, $fecha] = explode('|', $clave);

                    $efectivo = 0;
                    $deposito = 0;
                    $transferencia = 0;
                    $cheque = 0;
                    $atc = 0;

                    foreach ($items as $item) {
                        $signo = strtoupper($item->tipomovimiento) === 'INGRESO' ? 1 : -1;
                        $monto = floatval($item->montototal);
                        $descuentoAtc = floatval($item->descuentoatc ?? 0);

                        switch (strtoupper($item->tipotransaccion)) {
                            case 'EFECTIVO':
                                $efectivo += $signo * $monto;
                                break;
                            case 'DEPOSITO BANCARIO':
                                $deposito += $signo * $monto;
                                break;
                            case 'TRANSFERENCIA BANCARIA':
                                $transferencia += $signo * $monto;
                                break;
                            case 'CHEQUE':
                                $cheque += $signo * $monto;
                                break;
                            case 'ATC':
                                $atc += $signo * ($monto - $descuentoAtc);
                                break;
                        }
                    }

                    // Buscar cierre existente
                    $cierrecaja = DB::table('cierrecaja')
                        ->whereDate('fechacierre', $fecha)
                        ->where('usuariocierrenombre', $usuarioRegistro)
                        ->first();

                    if ($cierrecaja) {
                        DB::table('cierrecaja')
                            ->where('id', $cierrecaja->id)
                            ->update([
                                'cierreefectivo' => $cierrecaja->cierreefectivo + $efectivo,
                                'cierredeposito' => $cierrecaja->cierredeposito + $deposito,
                                'cierretransferencia' => $cierrecaja->cierretransferencia + $transferencia,
                                'cierrecheque' => $cierrecaja->cierrecheque + $cheque,
                                'cierreatc' => $cierrecaja->cierreatc + $atc,
                                'updated_at' => now(),
                            ]);
                    } else {
                        DB::table('cierrecaja')->insert([
                            'usuariocierrenombre' => $usuarioRegistro,
                            'usuariocierreid' => $authUserId,
                            'cierreefectivo' => $efectivo,
                            'cierredeposito' => $deposito,
                            'cierretransferencia' => $transferencia,
                            'cierrecheque' => $cheque,
                            'cierreatc' => $atc,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'fechacierre' => Carbon::parse($fecha)->startOfDay(), // ← CORREGIDO
                        ]);
                    }
                }
                return back()->with('info', 'Caja cerrada. Cierres actualizados o creados correctamente.');
            }
        } */
        if ($request->isMethod('post') && $request->has('accion')) {
            $authUsername = auth()->user()->name;
            $authUserId = auth()->id();

            if ($request->accion == 'aprobar') {
                $request->validate([
                    'registro_ids' => 'required|array',
                ]);

                DB::table('cajacentral')
                    ->whereIn('id', $request->registro_ids)
                    ->update([
                        'estadorevisioncierre' => 'CIERRE APROBADO',
                        'usuariorevisioncierre' => $authUsername
                    ]);

                return back()->with('info', 'Cierre aprobado exitosamente.');

            } elseif ($request->accion == 'cerrar') {
                $fechaCierre = now();

                // Si no hay registros seleccionados, insertar directamente el cierre en cero
                if (!$request->filled('registro_ids') || empty($request->registro_ids)) {
                    // Verificar si ya existe un cierre para hoy y este usuario
                    $existe = DB::table('cierrecaja')
                        ->whereDate('fechacierre', $fechaCierre->toDateString())
                        ->where('usuariocierrenombre', $authUsername)
                        ->exists();

                    if (!$existe) {
                        DB::table('cierrecaja')->insert([
                            'usuariocierrenombre' => $authUsername,
                            'usuariocierreid' => $authUserId,
                            'cierreefectivo' => 0.00,
                            'cierredeposito' => 0.00,
                            'cierretransferencia' => 0.00,
                            'cierrecheque' => 0.00,
                            'cierreatc' => 0.00,
                            'egresotransferencia' => 0.00,
                            'egresocheque' => 0.00,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'fechacierre' => $fechaCierre->startOfDay(),
                        ]);

                        return back()->with('info', 'Caja cerrada sin movimientos. Cierre registrado con montos en cero.');
                    } else {
                        return back()->with('info', 'Ya existe un registro de cierre para hoy.');
                    }
                }

                // REGISTROS SELECCIONADOS
                DB::table('cajacentral')
                    ->whereIn('id', $request->registro_ids)
                    ->update([
                        'estadorevisioncierre' => 'FINALIZADO',
                        'fechacierre' => $fechaCierre,
                        'usuariocierrecaja' => $authUsername
                    ]);

                $registros = DB::table('cajacentral')
                    ->whereIn('id', $request->registro_ids)
                    ->get();

                $agrupados = $registros->groupBy(function ($item) {
                    return $item->usuarioregistronombre . '|' . Carbon::parse($item->created_at)->toDateString();
                });

                foreach ($agrupados as $clave => $items) {
                    [$usuarioRegistro, $fecha] = explode('|', $clave);

                    $efectivo = 0;
                    $deposito = 0;
                    $transferencia = 0;
                    $cheque = 0;
                    $atc = 0;
                    $egresoTransferencia = 0;
                    $egresoCheque = 0;

                    foreach ($items as $item) {
                        $mov = strtoupper($item->tipomovimiento);
                        $trans = strtoupper($item->tipotransaccion);
                        $monto = floatval($item->montototal);
                        $descuentoAtc = floatval($item->descuentoatc ?? 0);

                        if ($mov === 'INGRESO') {
                            switch ($trans) {
                                case 'EFECTIVO':
                                    $efectivo += $monto;
                                    break;
                                case 'DEPOSITO BANCARIO':
                                    $deposito += $monto;
                                    break;
                                case 'TRANSFERENCIA BANCARIA':
                                    $transferencia += $monto;
                                    break;
                                case 'CHEQUE':
                                    $cheque += $monto;
                                    break;
                                case 'ATC':
                                    $atc += ($monto - $descuentoAtc);
                                    break;
                            }
                        } elseif ($mov === 'EGRESO') {
                            switch ($trans) {
                                case 'TRANSFERENCIA BANCARIA':
                                    $egresoTransferencia += $monto;
                                    break;
                                case 'CHEQUE':
                                    $egresoCheque += $monto;
                                    break;
                            }
                        }
                    }

                    // Buscar si ya existe cierre para ese usuario y fecha
                    $cierrecaja = DB::table('cierrecaja')
                        ->whereDate('fechacierre', $fecha)
                        ->where('usuariocierrenombre', $usuarioRegistro)
                        ->first();

                    if ($cierrecaja) {
                        DB::table('cierrecaja')
                            ->where('id', $cierrecaja->id)
                            ->update([
                                'cierreefectivo' => $cierrecaja->cierreefectivo + $efectivo,
                                'cierredeposito' => $cierrecaja->cierredeposito + $deposito,
                                'cierretransferencia' => $cierrecaja->cierretransferencia + $transferencia,
                                'cierrecheque' => $cierrecaja->cierrecheque + $cheque,
                                'cierreatc' => $cierrecaja->cierreatc + $atc,
                                'egresotransferencia' => ($cierrecaja->egresotransferencia ?? 0) + $egresoTransferencia,
                                'egresocheque' => ($cierrecaja->egresocheque ?? 0) + $egresoCheque,
                                'updated_at' => now(),
                            ]);
                    } else {
                        DB::table('cierrecaja')->insert([
                            'usuariocierrenombre' => $usuarioRegistro,
                            'usuariocierreid' => $authUserId,
                            'cierreefectivo' => $efectivo,
                            'cierredeposito' => $deposito,
                            'cierretransferencia' => $transferencia,
                            'cierrecheque' => $cheque,
                            'cierreatc' => $atc,
                            'egresotransferencia' => $egresoTransferencia,
                            'egresocheque' => $egresoCheque,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'fechacierre' => Carbon::parse($fecha)->startOfDay(),
                        ]);
                    }
                }

                /* NUEVO 011225 */
                $registrosarqueocaja = DB::table('arqueocaja')
                    ->where('usuarioarqueoid', auth()->user()->id)
                    ->get();

                foreach ($registrosarqueocaja as $registro) {
                    $fechaArqueo = now()->format('Y-m-d');

                    $consolidado = DB::table('consolidadoscaja')
                        ->where('usuarioconsolidadoid', $registro->usuarioarqueoid)
                        ->first();
                    $totalMonto = $consolidado ? $consolidado->consolidadoefectivo : 0;

                    $hayDinero = (
                        $registro->billetecorte200 != 0 ||
                        $registro->billetecorte100 != 0 ||
                        $registro->billetecorte50 != 0 ||
                        $registro->billetecorte20 != 0 ||
                        $registro->billetecorte10 != 0 ||
                        $registro->monedacorte5 != 0 ||
                        $registro->monedacorte2 != 0 ||
                        $registro->monedacorte1 != 0 ||
                        $registro->monedacorte050 != 0 ||
                        $registro->monedacorte020 != 0 ||
                        $registro->monedacorte010 != 0
                    ) && $totalMonto != 0.00;

                    if ($hayDinero) {
                        DB::table('historialarqueocaja')->updateOrInsert(
                            [
                                'usuarioarqueoid' => $registro->usuarioarqueoid,
                                'fechaarqueo' => $fechaArqueo,
                            ],
                            [
                                'usuarioarqueonombre' => $registro->usuarioarqueonombre,
                                'billetecorte200' => $registro->billetecorte200,
                                'billetecorte100' => $registro->billetecorte100,
                                'billetecorte50' => $registro->billetecorte50,
                                'billetecorte20' => $registro->billetecorte20,
                                'billetecorte10' => $registro->billetecorte10,
                                'monedacorte5' => $registro->monedacorte5,
                                'monedacorte2' => $registro->monedacorte2,
                                'monedacorte1' => $registro->monedacorte1,
                                'monedacorte050' => $registro->monedacorte050,
                                'monedacorte020' => $registro->monedacorte020,
                                'monedacorte010' => $registro->monedacorte010,
                                'totalmonto' => $totalMonto,
                                'updated_at' => now(),
                                'created_at' => now(),
                            ]
                        );
                    }
                }

                return back()->with('info', 'Caja cerrada. Cierres actualizados o creados correctamente.');
            }
        }


        $cierrecajas = Cierrecaja::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

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

        

        return view('admin.caja.ingreso.cierre', [
            'registros' => $registros,
            'registrosegreso' => $registrosegreso,
            'consolidados' => $consolidados,
            'montosCajaCentral' => $montosCajaCentral,
            'tiposTransaccion' => $tiposTransaccion,
            'usuariosConsolidados' => $usuariosConsolidados,
            'usuarioBusqueda' => $usuarioBusqueda,
            'cierrecajas' => $cierrecajas,
            'rolusuario' => $rolusuario,
            'mostrarVista' => $mostrarVista
        ]);
    }
//

// CUENTAS POR COBRAR
    public function listacuentascobrar(Cliente $cliente, ClienteAuditoria $clienteauditoria, ClienteComun $clientecomun, Request $request)
    {
        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
        $userRole = auth()->user()->getRoleNames()->first(); 
        $cuentaspagar = CuentasCobrar::all();  
   
        $result = [];
        $clientesITA = Cliente::select('id', 'sucursal')->get()->keyBy('id');
        $clientesAUD = ClienteAuditoria::select('id', 'sucursal')->get()->keyBy('id');
        $clientesCOM = ClienteComun::select('id', 'sucursal')->get()->keyBy('id');
        $tramites = TramiteSubCliente::select('clienteitaid', 'fechabateria', 'tramite')
            ->get()
            ->groupBy(fn ($t) => $t->clienteitaid . '|' . $t->fechabateria);
        $detallesRecibo = Detallerecibo::where('tipomovimiento', 'INGRESO')
            ->get()
            ->keyBy('programacionid');

        $query = Bateriasubcliente::with([
                'estadoprogclientes',
                'infomedicosclientes',
                'progclientes',
                'infofinalesclientes',
                'pagoservicioclientes',
                'pagoservicioinfofinalclientes'
            ])
            ->where('servicio', '<>', 'AJENO')
            ->orderBy('clientenombre');

        if ($request->filled('buscarporcliente')) {
            $query->where('clientenombre', 'LIKE', '%' . $request->buscarporcliente . '%');
        }

        $query->chunk(300, function ($baterias) use (
            &$result,
            $clientesITA,
            $clientesAUD,
            $clientesCOM,
            $tramites,
            $detallesRecibo,
        ) {

            $grouped = $baterias->groupBy(fn ($item) =>
                $item->clienteid . '|' . $item->fechabateria
            );

            foreach ($grouped as $key => $items) {

                [$clienteid, $fechabateria] = explode('|', $key);

                $first = $items->first();
                $tipocliente = $first->tipocliente;
                $nombrecliente = $first->clientenombre;

                $usuarioregistro = match ($tipocliente) {
                    'ITA' => $clientesITA[$clienteid]->sucursal ?? null,
                    'AUDITORIA' => $clientesAUD[$clienteid]->sucursal ?? null,
                    'COMUN' => $clientesCOM[$clienteid]->sucursal ?? null,
                    default => null,
                };

                $tramiteNombre = ($tramites[$clienteid . '|' . $fechabateria] ?? collect())
                    ->pluck('tramite')
                    ->toArray();

                $accionesConEstado = [];

                foreach ($items as $item) {

                    $estadoProgramacion = $item->estadoprogclientes
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();

                    $programacion = $item->progclientes
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();

                    $informesubido = $item->infomedicosclientes
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accion', $item->accionnombre)
                        ->first();

                    $informefinal = $item->infofinalesclientes
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accion', 'INFORME FINAL')
                        ->first();

                    $pagoservicioinforme = null;

                    if ($programacion) {
                        $detallerecibo = $detallesRecibo[$programacion->id] ?? null;

                        if ($detallerecibo) {
                            $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                        } else {
                            $pagoservicioinforme = $item->pagoatencion === 'PAGO PROCESADO'
                                ? 'PROCESADO'
                                : null;
                        }
                    }

                    $pagoservicioinfofinal = in_array($item->id, [
                        5505, 5506, 5507, 5508, 5509, 5510, 5511, 5512,
                        5513, 5514, 5515, 5516, 5517, 5518, 5519, 5520,
                        5521, 5522, 5523, 5524, 5525, 5526, 5527, 5528,
                        5529, 5530, 5531, 5532
                    ])
                        ? 'PROCESADO'
                        : optional(
                            $item->pagoservicioinfofinalclientes
                                ->where('provinfofinalid', $item->provinfofinalid)
                                ->where('tipomovimiento', 'INGRESO')
                                ->first()
                        )?->created_at?->toDateString();

                    $accionesConEstado[] = [
                        'id' => $item->id,
                        'accion' => $item->accionnombre,
                        'servicio' => $item->servicio,
                        'cantidadcuotas' => $item->cantidadcuotas,
                        'precio' => $item->precio,
                        'preciocompra' => $item->preciocompra,
                        'proveedorasignado' => $item->proveedorasignado,
                        'fechaasignada' => $item->fechaasignada,
                        'created_at' => $item->created_at,
                        'fechaatencionprogramacion' => $estadoProgramacion?->fechaatencionprogramacion,
                        'fechaprogramacion' => $programacion?->fechaasignada,
                        'informedocumentacion' => $informesubido?->created_at?->toDateString(),
                        'informedocumentacionfinal' => $informefinal?->created_at?->toDateString(),
                        'pagoservicioinforme' => $pagoservicioinforme,
                        'pagoservicioinformefinal' => $pagoservicioinfofinal,
                        'clientenombre' => $nombrecliente,
                        'fechabateria' => $item->fechabateria,
                    ];
                }

                $result[] = [
                    'clienteid' => $clienteid,
                    'clientenombre' => $nombrecliente,
                    'fechabateria' => $fechabateria,
                    'tramite' => $tramiteNombre,
                    'estado' => 'COMPLETO',
                    'acciones' => $accionesConEstado,
                    'usuarioregistro' => $usuarioregistro,
                    'tipocliente' => $tipocliente,
                ];
            }
        });


        /* $query4 = Bateriasubcliente::with([
            'estadoprogclientes',
            'infomedicosclientes',
            'progclientes',
            'infofinalesclientes',
            'pagoservicioclientes',
            'pagoservicioinfofinalclientes',
            'provinfofinalclientes'
            ])
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
                            $item->estadoprogclientes
                        ])->filter();
                        
                        $resultadoestado = $estadoProgramacion
                            ->flatMap(function ($estadoprogramacion) { 
                                return $estadoprogramacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();  

                    $programaciones = collect([
                            $item->progclientes
                        ])->filter();
                    
                        $resultadoprog = $programaciones
                            ->flatMap(function ($programacion) { 
                                return $programacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();                    

                    $informesubido = collect([
                            $item->infomedicosclientes
                        ])->filter();
                        
                        $resultadoinforme = $informesubido
                            ->flatMap(function ($informe) { 
                                return $informe;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accion', $item->accionnombre)
                    ->first();           

                    $informefinalsubido = collect([
                            $item->infofinalesclientes
                        ])->filter();
                        
                        $resultadoinformefinal = $informefinalsubido
                            ->flatMap(function ($informefinal) { 
                                return $informefinal;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $provinformes = collect([
                            $item->provinfofinalclientes
                        ])->filter();
                        
                        $resultadoprovinformes = $provinformes
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $resultadopagoinformefinal = $item->pagoservicioinfofinalclientes()
                        ->where('provinfofinalid', $item->provinfofinalid)
                        ->where('tipomovimiento', 'INGRESO')
                    ->first();

                    $pagobateria = collect([
                        $item->progclientes
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
                            $pagoservicioclientes = $detallerecibo->created_at->toDateString();
                        } else {
                            $pagoservicioclientes = $resultadopago->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : null;
                        }
                    } else {
                        $pagoservicioclientes = null;
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
                    'clienteid' => $item->clienteid,
                    'clientenombre' => $item->clientenombre,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechaprogramacion' => $fechaprogramacion,
                    'informedocumentacion' => $informedocumentacion,
                    'informedocumentacionfinal' => $informedocumentacionfinal,
                    'pagoservicioinforme' => $pagoservicioclientes,
                    'pagoservicioinformefinal' => $pagoservicioinformefinal,
                    'idprogramacion' => $idprogramacion,
                    'fechabateria' => $item->fechabateria,
                    'provinfofinalid' => $item->provinfofinalid,
                    'nrofacturaprog' => $nrofacturaprog,
                    'nrofacturainformefinal' => $nrofacturainformefinal,
                    'tipocliente' => $item->tipocliente,
                ];
            }
            $result4[] = [
                'proveedorasignado' => $item->proveedorasignado,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'fechabateria' => $item->fechabateria,
            ];
        } */

        $result4 = [];

        $detallesRecibo = Detallerecibo::where('tipomovimiento', 'INGRESO')
            ->get()
            ->keyBy('programacionid');

        $query4 = Bateriasubcliente::with([
                'estadoprogclientes',
                'infomedicosclientes',
                'progclientes',
                'infofinalesclientes',
                'pagoservicioclientes',
                'pagoservicioinfofinalclientes',
                'provinfofinalclientes'
            ])
            ->whereNotNull('proveedorasignado')
            ->whereNotNull('preciocompra')
            ->where('preciocompra', '>', 0)
            ->where('pagoservicio', 'EXTERNO')
            ->where('proveedorasignado', '<>', 'PROVEEDOR AJENO')
            ->orderBy('proveedorasignado');

        if ($request->filled('buscarporcliente')) {
            $query4->where('proveedorasignado', 'LIKE', '%' . $request->buscarporcliente . '%');
        }

        $query4->chunk(300, function ($baterias) use (&$result4, $detallesRecibo) {
            $grouped = $baterias->groupBy('proveedorasignado');
            foreach ($grouped as $proveedor => $items) {
                $accionesConEstado = [];
                foreach ($items as $item) {

                    $estadoProgramacion = $item->estadoprogclientes
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();

                    $programacion = $item->progclientes
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();

                    $informe = $item->infomedicosclientes
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accion', $item->accionnombre)
                        ->first();

                    $informefinal = $item->infofinalesclientes
                        ->where('fechabateria', $item->fechabateria)
                        ->first();

                    $provInforme = $item->provinfofinalclientes
                        ->where('fechabateria', $item->fechabateria)
                        ->first();

                    $pagoInformeFinal = $item->pagoservicioinfofinalclientes
                        ->where('provinfofinalid', $item->provinfofinalid)
                        ->where('tipomovimiento', 'INGRESO')
                        ->first();

                    $pagoservicio = null;
                    if ($programacion) {
                        $detallerecibo = $detallesRecibo[$programacion->id] ?? null;

                        if ($detallerecibo) {
                            $pagoservicio = $detallerecibo->created_at->toDateString();
                        } else {
                            $pagoservicio = $programacion->pagoatencion === 'PAGO PROCESADO'
                                ? 'PROCESADO'
                                : null;
                        }
                    }

                    $accionesConEstado[] = [
                        'id' => $item->id,
                        'accion' => $item->accionnombre,
                        'servicio' => $item->servicio,
                        'precio' => $item->precio,
                        'pagoservicio' => $item->pagoservicio,
                        'preciocompra' => $item->preciocompra,
                        'clienteid' => $item->clienteid,
                        'clientenombre' => $item->clientenombre,
                        'fechaasignada' => $item->fechaasignada,
                        'created_at' => $item->created_at,
                        'fechaatencionprogramacion' => $estadoProgramacion?->fechaatencionprogramacion,
                        'fechaprogramacion' => $programacion?->fechaasignada,
                        'idprogramacion' => $programacion?->id,
                        'nrofacturaprog' => $programacion?->nrofactura,
                        'informedocumentacion' => $informe?->created_at?->toDateString(),
                        'informedocumentacionfinal' => $informefinal?->created_at?->toDateString(),
                        'pagoservicioinforme' => $pagoservicio,
                        'pagoservicioinformefinal' => in_array($item->id, [3173, 3178, 3187, 3043])
                            ? 'PROCESADO'
                            : $pagoInformeFinal?->created_at?->toDateString(),
                        'nrofacturainformefinal' => $provInforme?->nrofactura,
                        'fechabateria' => $item->fechabateria,
                        'provinfofinalid' => $item->provinfofinalid,
                        'tipocliente' => $item->tipocliente,
                    ];
                }

                if (!isset($result4[$proveedor])) {
                    $result4[$proveedor] = [
                        'proveedorasignado' => $proveedor,
                        'estado' => 'COMPLETO',
                        'acciones' => [],
                        'fechabateria' => $item->fechabateria,
                    ];
                }
                
                $result4[$proveedor]['acciones'] = array_merge(
                    $result4[$proveedor]['acciones'],
                    $accionesConEstado
                );
            }
        });

        return view('admin.caja.cuentascobrar.listacuentascobrar', compact('cuentaspagar','usuarioAutenticado',
            'result','cliente','clienteauditoria','clientecomun','result4'));
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
            ->where('pagoservicio', 'INTERNO')
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
            ->where('pagoservicio', 'INTERNO')
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
            ->where('pagoservicio', 'INTERNO')
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
            ->where('pagoservicio', 'EXTERNO')
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
            ->where('pagoservicio', 'EXTERNO')
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
            ->where('pagoservicio', 'EXTERNO')
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
            ->where('pagoservicio', '=', 'INTERNO')
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
            ->where('pagoservicio', '=', 'INTERNO')
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
        $pagosprocesadosinformefinalita = ProveedorInformefinal::whereNotNull('clienteitaid')
            ->where(function ($query) {
                $query->where('pagoservicio', 'PAGO PROCESADO')
                    ->orWhereExists(function ($subquery) {
                        $subquery->select(DB::raw(1))
                            ->from('detallerecibos')
                            ->whereColumn('detallerecibos.provinfofinalid', 'proveedorinformesfinales.id')
                            ->where('detallerecibos.estado', 'PAGO PROCESADO');
                    });
            })
            ->join('clientes', 'proveedorinformesfinales.clienteitaid', '=', 'clientes.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clientes.sucursal as cliente_sucursal'
            )
        ->get();

        $pagosprocesadosinformefinalauditoria = ProveedorInformefinal::whereNotNull('clienteauditoriaid')
            ->where(function ($query) {
                $query->where('pagoservicio', 'PAGO PROCESADO')
                    ->orWhereExists(function ($subquery) {
                        $subquery->select(DB::raw(1))
                            ->from('detallerecibos')
                            ->whereColumn('detallerecibos.provinfofinalid', 'proveedorinformesfinales.id')
                            ->where('detallerecibos.estado', 'PAGO PROCESADO');
                    });
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
            ->where('pagoservicio', '=', 'INTERNO')
            ->join('clientes', 'proveedorinformesfinales.clienteitaid', '=', 'clientes.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clientes.sucursal as cliente_sucursal'
            )
        ->simplePaginate(1000);

        $pagosinformefinalauditoria = ProveedorInformefinal::where($filtrarpagospendientesinformefinalita)
            ->whereNotNull('clienteauditoriaid')
            ->where('pagoservicio', '=', 'INTERNO')
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

            $query->where(function ($q) {
                $q->where('pagoservicio', 'PAGO PROCESADO')
                ->orWhereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('detallerecibos')
                        ->whereColumn('detallerecibos.provinfofinalid', 'proveedorinformesfinales.id')
                        ->where('detallerecibos.estado', 'PAGO PROCESADO');
                });
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
        $cuentas = CuentasBancos::where('estado', 'ACTIVO')->get();
        $proveedores = ProveedoresServicios::select('id', 'razonsocial', 'tipotransaccion', 'ciudad', 'ciudad2', 'categoria', 'bancoorigen')->orderBy('razonsocial')->get();
        $proveedormedico = Proveedor::select('id', 'proveedor', 'mododepago', 'ciudad', 'ciudad2', 'bancoorigen')->where('id', 61)->orderBy('proveedor')->get();
        $clientesIta = Cliente::select('id', 'nombrecompleto', 'sucursal')->orderBy('nombrecompleto')->get();
        $clientesAuditoria = ClienteAuditoria::select('id', 'nombrecompleto', 'sucursal')->orderBy('nombrecompleto')->get();
        $clientesComunes = ClienteComun::select('id', 'nombrecompleto', 'sucursal')->orderBy('nombrecompleto')->get();

        $detallescxc = CCyCPdetalles::where('tipocuenta', 'CUENTA POR COBRAR')->select('id', 'detalle', 'precio')->get();

        return view('admin.caja.cuentascobrar.nuevacuentacobrar', compact('cuentas','clientesIta', 'clientesAuditoria', 'clientesComunes','proveedores', 'sucursal', 'detallescxc','proveedormedico'));
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
        
        $ultimoNroCredito = DB::table('creditos')
            ->where('nrocredito', 'like', '%CR')
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimoNroCredito && preg_match('/(\d+)([A-Za-z]+)/', $ultimoNroCredito->nrocredito, $matches)) {
            $numero = isset($matches[1]) ? (int)$matches[1] : 0;
            $sufijo = isset($matches[2]) ? $matches[2] : 'CR';
            $nuevoNroCredito = ($numero + 1) . $sufijo;
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

            /* $mostrarVista = true;

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

            $motivoBloqueo = null;
            if (!$registroCierreCaja) {
                $motivoBloqueo = 'NO CERRASTE TU CAJA EL DIA DE AYER';
            } elseif ($restriccionDeposito) {
                $motivoBloqueo = 'NO REGISTRASTE EL DEPÓSITO DEL EFECTIVO DE AYER ANTES DE LAS 10:00 AM.';
            } */
            $mostrarVista = true;

            $ultimoRegistro = DB::table('cajacentral')
                ->where('usuarioregistroid', $idUsuario)
                ->orderBy('created_at', 'desc')
                ->whereNull('deleted_at')
                ->first();

            $registroCierreCaja = true;

            // ✅ Si existe un registro en cajacentral, debemos validar su cierre
            if ($ultimoRegistro) {
                $fechaUltimoRegistro = Carbon::parse($ultimoRegistro->created_at)->toDateString();

                // Si la fecha del último registro NO es hoy, exigimos cierre para esa fecha
                if ($fechaUltimoRegistro !== $hoy->toDateString()) {
                    $registroCierreCaja = DB::table('cierrecaja')
                        ->where('usuariocierreid', $idUsuario)
                        ->whereDate('fechacierre', $fechaUltimoRegistro)
                        ->exists();
                }
            }

            $codigoAprobacion = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
                ->whereDate('fechaSolicitada', $hoy->toDateString())
                ->where('permisoSolicitado', 'admin.ingreso.index')
                ->where('estado', 'expirado')
                ->exists();

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

            $motivoBloqueo = null;
            if ($ultimoRegistro && !$registroCierreCaja) {
                $motivoBloqueo = 'NO CERRASTE TU CAJA DEL DÍA ' . Carbon::parse($ultimoRegistro->created_at)->format('d/m/Y');
            } elseif ($restriccionDeposito) {
                $motivoBloqueo = 'NO REGISTRASTE EL DEPÓSITO DEL EFECTIVO DE AYER ANTES DE LAS 10:00 AM.';
            }
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
            'cuentas' => $cuentas,
            'motivoBloqueo' => $motivoBloqueo
        ]);
    }
    public function codigocajaegresos(Request $request)
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
            ->orderBy('nrofactura')
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

                    /* case isset($registro->clienteid):
                        $registro->tramite = TramitesubCliente::where('clienteid', $registro->clienteid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break; */

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
            ->orderBy('nrofactura')
            ->get()
            ->map(function ($registro) {
                $detallerecibo = Detallerecibo::where('provinfofinalid', $registro->id) 
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

        $usuarioNombre = Auth::user()->name;
        $permisoExistefecha = PermisoCodigo::where(function($query) use ($entrada) {
            $query->whereIn('clienteid', function($q) use ($entrada) {
                $q->select('id')->from('proveedores')->where('proveedor', $entrada);
            })
            ->orWhereIn('clienteid', function($q) use ($entrada) {
                $q->select('id')->from('proveedoresservicios')->where('razonsocial', $entrada);
            });
        })
        ->whereDate('fechaSolicitada', Carbon::today())
        ->where('usuarioSolicitante', $usuarioNombre)
        ->where('permisoSolicitado', 'admin.caja.egresos.cambiarfecharegistro')
        ->where('estado', 'expirado')
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('cajacentral as c')
                ->whereRaw('c.fecharegistroreal > permisos_codigo.updated_at');
        })
        ->exists();


        return response()->json([
            'proveedor' => $proveedor,
            'registros' => $registros,'permisoExistefecha' => $permisoExistefecha
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
            'created_at' => '',
            'updated_at' => '',
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

        $fechaCreacion = $request->created_at
        ? Carbon::createFromFormat('Y-m-d\TH:i', $request->created_at)
        : now();

        $fechaActualizacion = $request->updated_at
        ? Carbon::createFromFormat('Y-m-d\TH:i', $request->updated_at)
        : now();

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
            'created_at' => $fechaCreacion,
            'updated_at' => $fechaActualizacion
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
            'created_at' => $fechaCreacion,
            'updated_at' => $fechaActualizacion,
            'fecharegistroreal' => now(),
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
                $proveedor = ProveedorInformeFinal::find($programacionId);

                // 🔥 DECISIÓN REAL
                if ($proveedor && $proveedor->accionnombre === 'INFORME FINAL') {
                    // usar proveedor
                    $programacion = null;

                } else {
                    // usar programación (si existe)
                    $proveedor = null;
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
                     $subtotalDetalle = (!empty($cuentapagar->preciocompra) && (float)$cuentapagar->preciocompra > 0) 
                            ? $cuentapagar->preciocompra 
                            : $cuentapagar->subtotal;
                } else {
                    $subtotalDetalle = 0;
                }
                $descuentoDetalle = $descuentos[$index];
                $pagoDetalle = $pagos[$index];
            }

            /* $subtotalDetalle = is_numeric($subtotalDetalle) ? floatval($subtotalDetalle) : 0; */
            $subtotalDetalle = is_numeric(str_replace(',', '.', $subtotalDetalle)) 
                ? (float) str_replace(',', '.', $subtotalDetalle) : 0;

            $descuentoDetalle = is_numeric(str_replace(',', '.', $descuentoDetalle)) 
                ? (float) str_replace(',', '.', $descuentoDetalle) : 0;

            $pagoDetalle = is_numeric(str_replace(',', '.', $pagoDetalle)) 
                ? (float) str_replace(',', '.', $pagoDetalle) : 0;

            $saldoDetalle = $subtotalDetalle - $descuentoDetalle - $pagoDetalle;
            $saldoDetalle = number_format($saldoDetalle, 2, '.', '');
            $estadoDetalle = ($saldoDetalle == 0) ? 'PAGO PROCESADO' : 'SALDO PENDIENTE';

            /* if ($estadoDetalle == 'PAGO PROCESADO') {
                Detallerecibo::where('programacionid', $programacionId)
                ->orwhere('provinfofinalid', $programacionId)
                ->orwhere('cuentapagarid', $programacionId)
                    ->where('estado', '!=', 'PAGO PROCESADO')
                    ->update(['estado' => 'PAGO PROCESADO']);
            } */

            if ($estadoDetalle == 'PAGO PROCESADO') {
                if ($programacion) {
                    Detallerecibo::where('programacionid', $programacionId)
                        ->where('estado', '!=', 'PAGO PROCESADO')
                        ->update(['estado' => 'PAGO PROCESADO']);
                } elseif ($proveedor) {
                    Detallerecibo::where('provinfofinalid', $programacionId)
                        ->where('estado', '!=', 'PAGO PROCESADO')
                        ->update(['estado' => 'PAGO PROCESADO']);
                } elseif ($cuentapagar) {
                    Detallerecibo::where('cuentapagarid', $programacionId)
                        ->where('estado', '!=', 'PAGO PROCESADO')
                        ->update(['estado' => 'PAGO PROCESADO']);
                }
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
                'created_at' => $fechaCreacion,
                'updated_at' => $fechaActualizacion
            ]);

            if ($detalleRecibo->cuentapagarid) {
                CuentasPagar::whereIn('id', explode(',', $detalleRecibo->cuentapagarid))
                    ->update(['estado' => $detalleRecibo->estado]);
            }

            /* CajaCentral::where('nrorecibo', $detalleRecibo->reciboid)
            ->update([
                'doccomprobante' => $detalleRecibo->comprobante,
                'docfactura'     => $detalleRecibo->factura
            ]); */

            $facturas = Detallerecibo::where('reciboid', $detalleRecibo->reciboid)
                ->pluck('factura')
                ->filter() // elimina null o vacíos
                ->unique()
                ->values()
                ->take(3); // máximo 3 diferentes

            // 2. Asignar cada una a una variable
            $factura1 = $facturas[0] ?? null;
            $factura2 = $facturas[1] ?? null;
            $factura3 = $facturas[2] ?? null;

            // 3. Actualizar CajaCentral
            CajaCentral::where('nrorecibo', $detalleRecibo->reciboid)
                ->update([
                    'doccomprobante' => $detalleRecibo->comprobante,
                    'docfactura'     => $factura1,
                    'docfactura2'    => $factura2,
                    'docfactura3'    => $factura3
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
            ->whereNull('deleted_at')
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
            ->orderBy('nrofactura')
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

                    /* case isset($registro->clienteid):
                        $registro->tramite = TramitesubCliente::where('clienteid', $registro->clienteid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break; */

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
            ->orderBy('nrofactura')
            ->get()
            ->map(function ($registro) {
                $detallerecibo = Detallerecibo::where('provinfofinalid', $registro->id) 
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


        $usuarioNombre = Auth::user()->name;
        $permisoExistefecha = PermisoCodigo::where(function($query) use ($entrada) {
            $query->whereIn('clienteid', function($q) use ($entrada) {
                $q->select('id')->from('proveedores')->where('proveedor', $entrada);
            })
            ->orWhereIn('clienteid', function($q) use ($entrada) {
                $q->select('id')->from('proveedoresservicios')->where('razonsocial', $entrada);
            });
        })
        ->whereDate('fechaSolicitada', Carbon::today())
        ->where('usuarioSolicitante', $usuarioNombre)
        ->where('permisoSolicitado', 'admin.caja.egresos.cambiarfecharegistro')
        ->where('estado', 'expirado')
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('cajacentral as c')
                ->whereRaw('c.fecharegistroreal > permisos_codigo.updated_at');
        })
        ->exists();

        return response()->json([
            'proveedor' => $proveedor,
            'registros' => $registros, 'permisoExistefecha' => $permisoExistefecha
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
            'created_at' => '',
            'updated_at' => '',
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

        $fechaCreacion = $request->created_at
        ? Carbon::createFromFormat('Y-m-d\TH:i', $request->created_at)
        : now();

        $fechaActualizacion = $request->updated_at
        ? Carbon::createFromFormat('Y-m-d\TH:i', $request->updated_at)
        : now();

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
            'created_at' => $fechaCreacion,
            'updated_at' => $fechaActualizacion,
            'fecharegistroreal' => now(),
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
                $proveedor = ProveedorInformeFinal::find($programacionId);

                // 🔥 DECISIÓN REAL
                if ($proveedor && $proveedor->accionnombre === 'INFORME FINAL') {
                    // usar proveedor
                    $programacion = null;

                } else {
                    // usar programación (si existe)
                    $proveedor = null;
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

            /* $subtotalDetalle = is_numeric($subtotalDetalle) ? floatval($subtotalDetalle) : 0; */
            $subtotalDetalle = is_numeric(str_replace(',', '.', $subtotalDetalle)) 
                ? (float) str_replace(',', '.', $subtotalDetalle) : 0;

            $descuentoDetalle = is_numeric(str_replace(',', '.', $descuentoDetalle)) 
                ? (float) str_replace(',', '.', $descuentoDetalle) : 0;

            $pagoDetalle = is_numeric(str_replace(',', '.', $pagoDetalle)) 
                ? (float) str_replace(',', '.', $pagoDetalle) : 0;
                
            $saldoDetalle = $subtotalDetalle - $descuentoDetalle - $pagoDetalle;
            $saldoDetalle = number_format($saldoDetalle, 2, '.', '');
            $estadoDetalle = ($saldoDetalle == 0) ? 'PAGO PROCESADO' : 'SALDO PENDIENTE';

            /* if ($estadoDetalle == 'PAGO PROCESADO') {
                Detallerecibo::where('programacionid', $programacionId)
                ->orwhere('provinfofinalid', $programacionId)
                ->orwhere('cuentapagarid', $programacionId)
                    ->where('estado', '!=', 'PAGO PROCESADO')
                    ->update(['estado' => 'PAGO PROCESADO']);
            } */
            if ($estadoDetalle == 'PAGO PROCESADO') {
                if ($programacion) {
                    Detallerecibo::where('programacionid', $programacionId)
                        ->where('estado', '!=', 'PAGO PROCESADO')
                        ->update(['estado' => 'PAGO PROCESADO']);
                } elseif ($proveedor) {
                    Detallerecibo::where('provinfofinalid', $programacionId)
                        ->where('estado', '!=', 'PAGO PROCESADO')
                        ->update(['estado' => 'PAGO PROCESADO']);
                } elseif ($cuentapagar) {
                    Detallerecibo::where('cuentapagarid', $programacionId)
                        ->where('estado', '!=', 'PAGO PROCESADO')
                        ->update(['estado' => 'PAGO PROCESADO']);
                }
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
                'created_at' => $fechaCreacion,
                'updated_at' => $fechaActualizacion,
            ]);

            if ($detalleRecibo->cuentapagarid) {
                CuentasPagar::whereIn('id', explode(',', $detalleRecibo->cuentapagarid))
                    ->update(['estado' => $detalleRecibo->estado]);
            }

                /* CajaCentral::where('nrorecibo', $detalleRecibo->reciboid)
                ->update([
                    'doccomprobante' => $detalleRecibo->comprobante,
                    'docfactura'     => $detalleRecibo->factura
                ]); */

                $facturas = Detallerecibo::where('reciboid', $detalleRecibo->reciboid)
                    ->pluck('factura')
                    ->filter() // elimina null o vacíos
                    ->unique()
                    ->values()
                    ->take(3); // máximo 3 diferentes

                // 2. Asignar cada una a una variable
                $factura1 = $facturas[0] ?? null;
                $factura2 = $facturas[1] ?? null;
                $factura3 = $facturas[2] ?? null;

                // 3. Actualizar CajaCentral
                CajaCentral::where('nrorecibo', $detalleRecibo->reciboid)
                    ->update([
                        'doccomprobante' => $detalleRecibo->comprobante,
                        'docfactura'     => $factura1,
                        'docfactura2'    => $factura2,
                        'docfactura3'    => $factura3
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
        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;

        $userRole = auth()->user()->getRoleNames()->first(); 

        /* $query = Bateriasubcliente::with([
            'estadoprogclientes',
            'infomedicosclientes',
            'progclientes',
            'infofinalesclientes',
            'pagoservicioclientes',
            'pagoservicioinfofinalclientes',
            'provinfofinalclientes',
            'tramiteclientes'
            ])
            ->whereNotNull('proveedorasignado')
            ->whereNotNull('preciocompra')
            ->where('preciocompra', '>', 0)
            ->where('pagoservicio', '<>', 'EXTERNO')
            ->whereNotIn('proveedorasignado', [
                'DIAGNOSTICO MEDICO POR IMAGEN DMI',
                'PROVEEDOR AJENO'
            ])
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
                            $item->estadoprogclientes
                        ])->filter();
                        
                        $resultadoestado = $estadoProgramacion
                            ->flatMap(function ($estadoprogramacion) { 
                                return $estadoprogramacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();  

                    $programaciones = collect([
                            $item->progclientes
                        ])->filter();
                    
                        $resultadoprog = $programaciones
                            ->flatMap(function ($programacion) { 
                                return $programacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();                    

                    $informesubido = collect([
                            $item->infomedicosclientes
                        ])->filter();
                        
                        $resultadoinforme = $informesubido
                            ->flatMap(function ($informe) { 
                                return $informe;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accion', $item->accionnombre)
                    ->first();           

                    $informefinalsubido = collect([
                            $item->infofinalesclientes
                        ])->filter();
                        
                        $resultadoinformefinal = $informefinalsubido
                            ->flatMap(function ($informefinal) { 
                                return $informefinal;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $provinformes = collect([
                            $item->provinfofinalclientes
                        ])->filter();
                        
                        $resultadoprovinformes = $provinformes
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first(); 
                    
                    $provinformes2 = collect([
                            $item->provinfofinalclientes
                        ])->filter();
                        
                        $resultadoprovinformes2 = $provinformes2
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('id', $item->provinfofinalid)
                    ->first();

                    $tramitesubcliente = collect([
                            $item->tramiteclientes
                        ])->filter();
                        
                        $resultadotramitesubcliente = $tramitesubcliente
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();

                    $resultadopagoinformefinal = $item->pagoservicioinfofinalclientes()
                        ->where('provinfofinalid', $item->provinfofinalid)
                        ->where('tipomovimiento', 'EGRESO')
                    ->first();

                    $pagobateria = collect([
                        $item->progclientes
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
                $codautorizacion = $resultadoprog ? $resultadoprog->codautorizacion : null;
                $informedocumentacion = $resultadoinforme ? $resultadoinforme->created_at->toDateString() : null;
                $informedocumentacionfinal = $resultadoinformefinal ? $resultadoinformefinal->created_at->toDateString() : null;
                $pagoservicioinfofinalclientes = in_array($item->id, [3173, 3178, 3187, 3043]) 
                ? 'PROCESADO' 
                : ($resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null);
                $nrofacturainformefinal = $resultadoprovinformes2 ? $resultadoprovinformes2->nrofactura : null;
                $codautorizacioninfofinal = $resultadoprovinformes2 ? $resultadoprovinformes2->codautorizacion : null;
                $facturainformefinal = $resultadoprovinformes2 ? $resultadoprovinformes2->factura : null;
                $tramiteinformefinal = $resultadoprovinformes2 ? $resultadoprovinformes2->servicio : null;
                $tramitecliente = $resultadotramitesubcliente ? $resultadotramitesubcliente->tramite : null;

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'pagoservicio' => $item->pagoservicioclientes,
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
                    'pagoservicioinformefinal' => $pagoservicioinfofinalclientes,
                    'idprogramacion' => $idprogramacion,
                    'fechabateria' => $item->fechabateria,
                    'provinfofinalid' => $item->provinfofinalid,
                    'nrofacturaprog' => $nrofacturaprog,
                    'documentofactura' => $documentofactura,
                    'codautorizacion' => $codautorizacion,
                    'nrofacturainformefinal' => $nrofacturainformefinal,
                    'codautorizacioninfofinal' => $codautorizacioninfofinal,
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
        } */

        $query = Bateriasubcliente::with([
            'estadoprogclientes',
            'infomedicosclientes',
            'progclientes',
            'infofinalesclientes',
            'pagoservicioclientes',
            'pagoservicioinfofinalclientes',
            'provinfofinalclientes',
            'tramiteclientes'
        ])
        ->whereNotNull('proveedorasignado')
        ->whereNotNull('preciocompra')
        ->where('preciocompra', '>', 0)
        ->where('pagoservicio', '<>', 'EXTERNO')
        ->whereNotIn('proveedorasignado', [
            'DIAGNOSTICO MEDICO POR IMAGEN DMI',
            'PROVEEDOR AJENO'
        ])
        ->orderBy('proveedorasignado');

        if ($request->filled('buscarporcliente')) {
            $query->where('proveedorasignado', 'LIKE', '%' . $request->buscarporcliente . '%');
        }

        $bateriaproveedores = $query->get();
        $grouped = $bateriaproveedores->groupBy('proveedorasignado');
        $result = [];

        foreach ($grouped as $proveedor => $items) {

            $accionesConEstado = [];

            foreach ($items as $item) {

                $estadoProgramacion = $item->estadoprogclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();

                $programacion = $item->progclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();

                $informe = $item->infomedicosclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accion', $item->accionnombre)
                    ->first();

                $informeFinal = $item->infofinalesclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->first();

                $provInfoFinal = $item->provinfofinalclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->where('id', $item->provinfofinalid)
                    ->first();

                $tramiteCliente = $item->tramiteclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->first();

                $pagoInfoFinal = $item->pagoservicioinfofinalclientes
                    ->where('provinfofinalid', $item->provinfofinalid)
                    ->where('tipomovimiento', 'EGRESO')
                    ->first();

                // ============================
                // PAGO ATENCIÓN + PRECIO FINAL
                // ============================
                $preciocompra = $item->preciocompra;
                $pagoservicioinforme = 'PENDIENTE';

                if ($programacion) {
                    $detallerecibo = Detallerecibo::where('programacionid', $programacion->id)
                        ->where('tipomovimiento', 'EGRESO')
                        ->latest('id')
                        ->first();

                    if ($detallerecibo) {
                        if ($detallerecibo->estado === 'PAGO PROCESADO') {
                            $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                        } elseif ($detallerecibo->estado === 'SALDO PENDIENTE') {
                            $pagoservicioinforme = 'SALDO PENDIENTE';
                            $preciocompra = $detallerecibo->saldo ?? $preciocompra;
                        }
                    } else {
                        $pagoservicioinforme = $programacion->pagoatencion === 'PAGO PROCESADO'
                            ? 'PROCESADO'
                            : 'PENDIENTE';
                    }
                }

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'preciocompra' => $preciocompra,

                    'clienteitaid' => $item->clienteitaid,
                    'clienteitanombre' => $item->clienteitanombre,
                    'clienteauditoriaid' => $item->clienteauditoriaid,
                    'clienteauditorianombre' => $item->clienteauditorianombre,
                    'clientecomunid' => $item->clientecomunid,
                    'clientecomunnombre' => $item->clientecomunnombre,
                    'clienteid' => $item->clienteid,
                    'clientenombre' => $item->clientenombre,

                    'fechabateria' => $item->fechabateria,
                    'fechaatencionprogramacion' => optional($estadoProgramacion)->fechaatencionprogramacion,
                    'fechaprogramacion' => optional($programacion)->fechaasignada,
                    'idprogramacion' => optional($programacion)->id,
                    'nrofacturaprog' => optional($programacion)->nrofactura,
                    'documentofactura' => optional($programacion)->factura,
                    'codautorizacion' => optional($programacion)->codautorizacion,
                    'provinfofinalid' => $item->provinfofinalid,
                    'informedocumentacion' => optional($informe)->created_at?->toDateString(),
                    'informedocumentacionfinal' => optional($informeFinal)->created_at?->toDateString(),

                    'pagoservicioinforme' => $pagoservicioinforme,
                    'pagoservicioinformefinal' => in_array($item->id, [3173, 3178, 3187, 3043])
                        ? 'PROCESADO'
                        : optional($pagoInfoFinal)->created_at?->toDateString(),

                    'nrofacturainformefinal' => optional($provInfoFinal)->nrofactura,
                    'codautorizacioninfofinal' => optional($provInfoFinal)->codautorizacion,
                    'facturainformefinal' => optional($provInfoFinal)->factura,
                    'tramiteinformefinal' => optional($provInfoFinal)->servicio,
                    'tramitecliente' => optional($tramiteCliente)->tramite,

                    'prioridad' => $item->prioridad,
                    'estadoaprobacion' => $item->estadoaprobacion,
                ];
            }

            $result[] = [
                'proveedorasignado' => $proveedor,
                'estado' => 'COMPLETO',
                'fechabateria' => $items->first()->fechabateria,
                'acciones' => $accionesConEstado,
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
                ->whereColumn('proveedorinformesfinales.servicio', 'informesfinales.servicio')
                ->whereNull('informesfinales.motivoanulacion');
            })
            ->select('bateriasubclientes.*', 'informesfinales.created_at as informe_created_at')
            ->distinct()
            ->with([
                'programacion' => function ($query) {
                    $query->select('id', 'bateriaid', 'fechaasignada', 'nrofactura', 'factura');
                },
                'programacion.documentacion' => function ($query) {
                    $query->select('id', 'programacionid', 'created_at');
                },
                'proveedorinformefinal' => function ($query) {
                    $query->select('id', 'nrofactura', 'factura');
                },
                'clienteita2:id,sucursal',
                'clienteauditoria2:id,sucursal',
                'clientecomun2:id,sucursal',
            ])
        ->get();
        
        foreach ($registrosbateria as $registro) {
            $programacion = Programacionsubcliente::where('bateriaid', $registro->id)
                ->orderBy('id', 'desc')
                ->first();

            if ($programacion) {
                $detallerecibo = Detallerecibo::where('programacionid', $programacion->id)
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
        $proveedoresServicioscuenta = Proveedor::pluck('cuenta', 'proveedor');
        $documentosPorFecha = PlanillasPagosGeneradas::select('tipo', 'documento', 'fechapago', 'proveedor')
            ->get()
        ->groupBy('fechapago');

        $cuentasbancos = CuentasBancos::where('estado', 'ACTIVO')->get();


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
                                ->whereNotNull('nrobancarizacioncheque');
                    });
            })
            ->where('tipomovimiento', 'INGRESO')
            ->where('estado', '!=', 'ANULADO')
            ->sum(DB::raw("CASE 
                            WHEN tipotransaccion = 'ATC' AND nrobancarizacionatc IS NOT NULL 
                            THEN montototal - descuentoATC 
                            WHEN tipotransaccion = 'EFECTIVO' 
                            THEN montototal + diferenciafavor
                            ELSE montototal 
        END"));

        $totalCuenta1Egreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '3000189269')
                ->orWhere(function ($subquery) {
                    $subquery->where('tipotransaccion', 'CHEQUE')
                                ->where('nrocuentadestinocheque', '3000189269')
                                ->whereNotNull('nrobancarizacioncheque');
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
                                ->whereNotNull('nrobancarizacioncheque');
                    });
                })
                ->where('tipomovimiento', 'INGRESO')
                ->where('estado', '!=', 'ANULADO')
                ->sum(DB::raw("CASE 
                    WHEN tipotransaccion = 'ATC' AND nrobancarizacionatc IS NOT NULL 
                    THEN montototal - descuentoATC 
                    WHEN tipotransaccion = 'EFECTIVO' 
                    THEN montototal + diferenciafavor
                    ELSE montototal 
        END"));
        
        $totalCuenta2Egreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '2505314878')
                ->orWhere(function ($subquery) {
                    $subquery->where('tipotransaccion', 'CHEQUE')
                            ->where('nrocuentadestinocheque', '2505314878')
                            ->whereNotNull('nrobancarizacioncheque');
                });
            })
            ->where('tipomovimiento', 'EGRESO')
            ->where('estado', '!=', 'ANULADO')
        ->sum('montototal');

        /* TOTAL DE CAJA INGRESOS Y EGRESOS DE 1031266712 */
        $totalCuenta4Ingreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '1031266712')
                    ->orWhere('nrocuentadestinodeposito', '1031266712')
                    ->orWhere(function ($subquery) {
                        $subquery->where('nrobancodestinoefectivo', '1031266712')
                                ->whereNotNull('nrobancarizacionefectivo');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('tipotransaccion', 'ATC')
                                ->where('nrocuentadestinoatc', '1031266712')
                                ->whereNotNull('nrobancarizacionatc');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('tipotransaccion', 'CHEQUE')
                                ->where('nrocuentadestinocheque', '1031266712')
                                ->whereNotNull('nrobancarizacioncheque');
                    });
            })
            ->where('tipomovimiento', 'INGRESO')
            ->where('estado', '!=', 'ANULADO')
            ->sum(DB::raw("CASE 
                            WHEN tipotransaccion = 'ATC' AND nrobancarizacionatc IS NOT NULL 
                            THEN montototal - descuentoATC 
                            WHEN tipotransaccion = 'EFECTIVO' 
                            THEN montototal + diferenciafavor
                            ELSE montototal 
        END"));

        $totalCuenta4Egreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '1031266712')
                ->orWhere(function ($subquery) {
                    $subquery->where('tipotransaccion', 'CHEQUE')
                                ->where('nrocuentadestinocheque', '1031266712')
                                ->whereNotNull('nrobancarizacioncheque');
                });
            })
            ->where('tipomovimiento', 'EGRESO')
            ->where('estado', '!=', 'ANULADO')
        ->sum('montototal');

        $saldoanteriorcuenta1 = '-174910.55';
        $saldoanteriorcuenta2 = '34508.22';
        $saldoanteriorcuenta4 = '0.00';

        $cuentasConSaldo = [
            '3000189269' => $saldoanteriorcuenta1 + $totalCuenta1Ingreso - $totalCuenta1Egreso,
            '2505314878' => $saldoanteriorcuenta2 + $totalCuenta2Ingreso - $totalCuenta2Egreso,
            '1031266712' => $saldoanteriorcuenta4 + $totalCuenta4Ingreso - $totalCuenta4Egreso,
        ];

        return view('admin.caja.cuentaspagar.listacuentaspagar', compact('registrosbateria','cuentaspagar','year','month',
        'records','usuarioAutenticado','result','proveedor','totalCuenta1Ingreso','totalCuenta1Egreso','totalCuenta2Ingreso',
        'totalCuenta2Egreso','saldoanteriorcuenta1','saldoanteriorcuenta2','documentosPorFecha','cuentasbancos',
        'proveedoresServicios','proveedoresServicioscuenta','saldoanteriorcuenta4','totalCuenta4Ingreso','totalCuenta4Egreso'));
    }
    public function listacuentaspagarenmora(Proveedor $proveedor, Request $request)
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
                $codautorizacion = $resultadoprog ? $resultadoprog->codautorizacion : null;
                $informedocumentacion = $resultadoinforme ? $resultadoinforme->created_at->toDateString() : null;
                $informedocumentacionfinal = $resultadoinformefinal ? $resultadoinformefinal->created_at->toDateString() : null;
                $pagoservicioinformefinal = in_array($item->id, [3173, 3178, 3187, 3043]) 
                ? 'PROCESADO' 
                : ($resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null);
                $nrofacturainformefinal = $resultadoprovinformes2 ? $resultadoprovinformes2->nrofactura : null;
                $codautorizacioninfofinal = $resultadoprovinformes2 ? $resultadoprovinformes2->codautorizacion : null;
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
                    'codautorizacion' => $codautorizacion,
                    'nrofacturainformefinal' => $nrofacturainformefinal,
                    'codautorizacioninfofinal' => $codautorizacioninfofinal,
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
                ->whereColumn('proveedorinformesfinales.servicio', 'informesfinales.servicio')
                ->whereNull('informesfinales.motivoanulacion');
            })
            ->select('bateriasubclientes.*', 'informesfinales.created_at as informe_created_at')
            ->distinct()
            ->with([
                'programacion' => function ($query) {
                    $query->select('id', 'bateriaid', 'fechaasignada', 'nrofactura', 'factura');
                },
                'programacion.documentacion' => function ($query) {
                    $query->select('id', 'programacionid', 'created_at');
                },
                'proveedorinformefinal' => function ($query) {
                    $query->select('id', 'nrofactura', 'factura');
                },
                'clienteita2:id,sucursal',
                'clienteauditoria2:id,sucursal',
                'clientecomun2:id,sucursal',
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
        $proveedoresServicioscuenta = Proveedor::pluck('cuenta', 'proveedor');


        $documentosPorFecha = PlanillasPagosGeneradas::select('tipo', 'documento', 'fechapago', 'proveedor')
        ->get()
        ->groupBy('fechapago');

        $cuentasbancos = CuentasBancos::where('estado', 'ACTIVO')->get();

        return view('admin.caja.cuentaspagar.listacuentaspagarenmora', compact('registrosbateria','cuentaspagar','year',
        'month','records','usuarioAutenticado','result','fechas','proveedor','documentosPorFecha','cuentasbancos',
        'proveedoresServicios','proveedoresServicioscuenta'));
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
        $codautorizacion = $request->input('codautorizacion');
        $fechapago = $request->input('fechapago');
        $fechaPagoProv = $request->input('fechaPagoProv');
        $seleccionados = $request->input('seleccionados', []);
        $nrocuentabanco = $request->input('nrocuentabanco');

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

            // ========= REGISTROS PROGRAMACION (EXCLUYE INFORME FINAL) =========
            $registros = Programacionsubcliente::whereIn('id', $seleccionados)
                ->whereRaw('UPPER(accionnombre) != "INFORME FINAL"')
            ->get();

            // ========= REGISTROS INFORME FINAL =========
            $registrosFinales = ProveedorInformefinal::whereIn('id', $seleccionados)->get();

            // ========= OBTENER PROVEEDOR ASIGNADO SEGÚN ORIGEN =========
            $proveedorAsignado = null;

            if ($registros->isNotEmpty()) {
                $proveedorAsignado = $registros->first()->proveedornombre;
            } elseif ($registrosFinales->isNotEmpty()) {
                $proveedorAsignado = $registrosFinales->first()->proveedorasignado;
            }
            $ordenExistente = BateriaSubCliente::where('fechapago', $fechaPagoProv)
                ->where('proveedorasignado', $proveedorAsignado)
                ->whereHas('programaciones', function ($query) use ($nroFactura) {
                    $query->where('nrofactura', $nroFactura);
                })
                ->orderBy('id', 'desc')
                ->first();

            if ($ordenExistente) {
                $nuevoOrdenId = $ordenExistente->ordenid;
            } else {
                $ultimoOrden = BateriaSubCliente::whereNotNull('ordenid')
                    ->where('ordenid', 'LIKE', '%M')
                    ->orderByRaw("CAST(SUBSTRING_INDEX(ordenid, 'M', 1) AS UNSIGNED) DESC")
                    ->first();

                $nuevoNumero = 1;
                if ($ultimoOrden && preg_match('/^(\d+)M$/', $ultimoOrden->ordenid, $matches)) {
                    $nuevoNumero = intval($matches[1]) + 1;
                }
                $nuevoOrdenId = $nuevoNumero . 'M';
            }

            // ========= ACTUALIZAR TODOS LOS REGISTROS =========
            foreach ($registros as $registro) {
                $registro->nroFactura = $nroFactura;
                $registro->codautorizacion = $codautorizacion;
                $registro->factura = $nombreArchivo;
                $registro->save();

                $bateria = Bateriasubcliente::find($registro->bateriaid);
                if ($bateria) {
                    $proveedor = Proveedor::where('proveedor', $bateria->proveedorasignado)->first();

                    if ($proveedor) {
                        $bateria->fechapago = $fechaPagoProv;
                        $bateria->prioridad = 'CUENTA POR PAGAR';
                        $bateria->nrobancoorigen = $nrocuentabanco;
                        $bateria->ordenid = $nuevoOrdenId;
                        $bateria->save();
                    }
                }
            }

            foreach ($registrosFinales as $registroFinal) {
                $registroFinal->nroFactura = $nroFactura;
                $registroFinal->codautorizacion = $codautorizacion;
                $registroFinal->factura = $nombreArchivo;
                $registroFinal->save();

                $bateria = Bateriasubcliente::where('provinfofinalid', $registroFinal->id)->first();
                if ($bateria) {
                    $proveedor = Proveedor::where('proveedor', $bateria->proveedorasignado)->first();

                    if ($proveedor) {
                        $bateria->fechapago = $fechaPagoProv;
                        $bateria->prioridad = 'CUENTA POR PAGAR';
                        $bateria->nrobancoorigen = $nrocuentabanco;
                        $bateria->ordenid = $nuevoOrdenId;
                        $bateria->save();
                    }
                }
            }

            // ========= REGISTRO FACTURASEGRESO PARA PROGRAMACION =========
            if ($registros->count() > 0) {
                $proveedores = $registros->pluck('proveedornombre')->unique();
                if ($proveedores->count() > 1) {
                    return back()->with('error', 'Todos los registros seleccionados deben ser del mismo proveedor.');
                }

                $razonsocial = $proveedores->first();
                $subtotal = $registros->sum('preciocompra');
                $total = $subtotal;
                $importenosujetocfdf = 0;
                $importeBaseCFDF = $total - $importenosujetocfdf;
                $creditoDebitoFiscal = $importeBaseCFDF * 0.13;

                $proveedor = Proveedor::where('proveedor', $razonsocial)->first();
                $nit = ($proveedor && !empty($proveedor->nit) && $proveedor->nit != '0') ? $proveedor->nit : ($proveedor->ci ?? '');

                /* $ciudad = $proveedor->ciudad ?? 'NO DEFINIDO'; */

                if ($proveedor && !empty($proveedor->ciudad2)) {
                    $registroBase = $registros->first();
                    $ciudad = null;
                    if ($registroBase->clienteitaid) {
                        $ciudad = DB::table('clientes')
                            ->where('id', $registroBase->clienteitaid)
                            ->value('sucursal');

                    } elseif ($registroBase->clienteauditoriaid) {
                        $ciudad = DB::table('clienteauditorias')
                            ->where('id', $registroBase->clienteauditoriaid)
                            ->value('sucursal');

                    } elseif ($registroBase->clientecomunid) {
                        $ciudad = DB::table('clientescomunes')
                            ->where('id', $registroBase->clientecomunid)
                            ->value('sucursal');
                    }
                    $ciudad = $ciudad ?? 'NO DEFINIDO';
                } else {
                    $ciudad = $proveedor->ciudad ?? 'NO DEFINIDO';
                }


                $usuarioId = Auth::id();
                $usuarioNombre = Auth::user()->name;

                $fechaFactura2 = now()->toDateString();
                $registroExistente2 = DB::table('facturasegreso')
                    ->whereDate('fechafacturaduidim', $fechaFactura2)
                    ->where('nrofactura', $nroFactura)
                    ->where('razonsocial', $razonsocial)
                    ->first();
                if ($registroExistente2) {
                    DB::table('facturasegreso')
                        ->where('id', $registroExistente2->id)
                        ->update([
                            'subtotal' => $registroExistente2->subtotal + $subtotal,
                            'total' => $registroExistente2->total + $total,
                            'importebasecfdf' => $registroExistente2->importebasecfdf + $importeBaseCFDF,
                            'creditodebitofiscal' => $registroExistente2->creditodebitofiscal + $creditoDebitoFiscal,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('facturasegreso')->insert([
                        'especificacion' => '1',
                        'nitci' => $nit,
                        'razonsocial' => $razonsocial,
                        'codigoautorizacion' => $codautorizacion,
                        'nrofactura' => $nroFactura,
                        'fechafacturaduidim' => now(),
                        'subtotal' => $subtotal,
                        'descuento' => '0.00',
                        'total' => $total,
                        'importebasecfdf' => $importeBaseCFDF,
                        'creditodebitofiscal' => $creditoDebitoFiscal,
                        'tipo' => '1',
                        'ciudad' => $ciudad,
                        'estado' => 'VALIDO',
                        'importenosujetocfdf' => $importenosujetocfdf,
                        'usuarioregistroid' => $usuarioId,
                        'usuarioregistronombre' => $usuarioNombre,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'nroduidim' => '0.00',
                        'ice' => '0.00',
                        'iehd' => '0.00',
                        'ipj' => '0.00',
                        'tasas' => '0.00',
                        'importeyexporteexterno' => '0.00',
                        'tasacero' => '0.00',
                        'giftcard' => '0.00',
                        'codigocontrol' => '0.00',
                        'otronosujcredfiscaloiva' => '0.00',
                        'complemento' => '0',
                    ]);
                }
            }

            // ========= REGISTRO FACTURASEGRESO PARA INFORME FINAL =========
            if ($registrosFinales->count() > 0) {
                $proveedoresFinal = $registrosFinales->pluck('proveedorasignado')->unique();
                if ($proveedoresFinal->count() > 1) {
                    return back()->with('error', 'Todos los informes finales deben ser del mismo proveedor.');
                }

                $razonsocial = $proveedoresFinal->first();
                $subtotal = $registrosFinales->sum('preciocompra');
                $total = $subtotal;
                $importenosujetocfdf = 0;
                $importeBaseCFDF = $total - $importenosujetocfdf;
                $creditoDebitoFiscal = $importeBaseCFDF * 0.13;

                $proveedor = Proveedor::where('proveedor', $razonsocial)->first();
                $nit = ($proveedor && !empty($proveedor->nit) && $proveedor->nit != '0') ? $proveedor->nit : ($proveedor->ci ?? '');

                /* $ciudad = $proveedor->ciudad ?? 'NO DEFINIDO'; */

                if ($proveedor && !empty($proveedor->ciudad2)) {
                    $registroBase = $registros->first();
                    $ciudad = null;
                    if ($registroBase->clienteitaid) {
                        $ciudad = DB::table('clientes')
                            ->where('id', $registroBase->clienteitaid)
                            ->value('sucursal');

                    } elseif ($registroBase->clienteauditoriaid) {
                        $ciudad = DB::table('clienteauditorias')
                            ->where('id', $registroBase->clienteauditoriaid)
                            ->value('sucursal');

                    } elseif ($registroBase->clientecomunid) {
                        $ciudad = DB::table('clientescomunes')
                            ->where('id', $registroBase->clientecomunid)
                            ->value('sucursal');
                    }
                    $ciudad = $ciudad ?? 'NO DEFINIDO';
                } else {
                    $ciudad = $proveedor->ciudad ?? 'NO DEFINIDO';
                }


                $usuarioId = Auth::id();
                $usuarioNombre = Auth::user()->name;

                $fechaFactura = now()->toDateString();
                $registroExistente = DB::table('facturasegreso')
                    ->whereDate('fechafacturaduidim', $fechaFactura)
                    ->where('nrofactura', $nroFactura)
                    ->where('razonsocial', $razonsocial)
                    ->first();
                if ($registroExistente) {
                    DB::table('facturasegreso')
                        ->where('id', $registroExistente->id)
                        ->update([
                            'subtotal' => $registroExistente->subtotal + $subtotal,
                            'total' => $registroExistente->total + $total,
                            'importebasecfdf' => $registroExistente->importebasecfdf + $importeBaseCFDF,
                            'creditodebitofiscal' => $registroExistente->creditodebitofiscal + $creditoDebitoFiscal,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('facturasegreso')->insert([
                        'especificacion' => '1',
                        'nitci' => $nit,
                        'razonsocial' => $razonsocial,
                        'codigoautorizacion' => $codautorizacion,
                        'nrofactura' => $nroFactura,
                        'fechafacturaduidim' => now(),
                        'subtotal' => $subtotal,
                        'descuento' => '0.00',
                        'total' => $total,
                        'importebasecfdf' => $importeBaseCFDF,
                        'creditodebitofiscal' => $creditoDebitoFiscal,
                        'tipo' => '1',
                        'ciudad' => $ciudad,
                        'estado' => 'VALIDO',
                        'importenosujetocfdf' => $importenosujetocfdf,
                        'usuarioregistroid' => $usuarioId,
                        'usuarioregistronombre' => $usuarioNombre,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'nroduidim' => '0.00',
                        'ice' => '0.00',
                        'iehd' => '0.00',
                        'ipj' => '0.00',
                        'tasas' => '0.00',
                        'importeyexporteexterno' => '0.00',
                        'tasacero' => '0.00',
                        'giftcard' => '0.00',
                        'codigocontrol' => '0.00',
                        'otronosujcredfiscaloiva' => '0.00',
                        'complemento' => '0',
                    ]);
                }
            }
            return redirect()->back()->with('info', 'Facturas actualizadas correctamente.');
        } elseif ($accion === 'anular') {
            foreach ($seleccionados as $id) {
                $registro = Programacionsubcliente::find($id);
                if ($registro && strtoupper($registro->accionnombre) !== 'INFORME FINAL') {
                    $registro->nroFactura = null;
                    $registro->codautorizacion = null;
                    $registro->factura = null;
                    $registro->save();
                } else {
                    $registroFinal = ProveedorInformefinal::find($id);
                    if ($registroFinal) {
                        $registroFinal->nroFactura = null;
                        $registroFinal->codautorizacion = null;
                        $registroFinal->factura = null;
                        $registroFinal->save();
                    }
                    
                }
            }

            return redirect()->back()->with('info', 'Facturas anuladas correctamente.');
        } elseif ($accion === 'guardarsinfactura') {
            // ========= REGISTROS PROGRAMACION (EXCLUYE INFORME FINAL) =========
            $registros = Programacionsubcliente::whereIn('id', $seleccionados)
                ->whereRaw('UPPER(accionnombre) != "INFORME FINAL"')
            ->get();

            // ========= REGISTROS INFORME FINAL =========
            $registrosFinales = ProveedorInformefinal::whereIn('id', $seleccionados)->get();

            // ========= OBTENER PROVEEDOR ASIGNADO SEGÚN ORIGEN =========
            $proveedorAsignado = null;

            if ($registros->isNotEmpty()) {
                $proveedorAsignado = $registros->first()->proveedornombre;
            } elseif ($registrosFinales->isNotEmpty()) {
                $proveedorAsignado = $registrosFinales->first()->proveedorasignado;
            }
            $ordenExistente = BateriaSubCliente::where('fechapago', $fechaPagoProv)
                ->where('proveedorasignado', $proveedorAsignado)
                ->orderBy('id', 'desc')
                ->first();

            if ($ordenExistente) {
                $nuevoOrdenId = $ordenExistente->ordenid;
            } else {
                $ultimoOrden = BateriaSubCliente::whereNotNull('ordenid')
                    ->where('ordenid', 'LIKE', '%M')
                    ->orderByRaw("CAST(SUBSTRING_INDEX(ordenid, 'M', 1) AS UNSIGNED) DESC")
                    ->first();

                $nuevoNumero = 1;
                if ($ultimoOrden && preg_match('/^(\d+)M$/', $ultimoOrden->ordenid, $matches)) {
                    $nuevoNumero = intval($matches[1]) + 1;
                }
                $nuevoOrdenId = $nuevoNumero . 'M';
            }

            // ========= ACTUALIZAR TODOS LOS REGISTROS =========
            foreach ($registros as $registro) {
                $registro->save();

                $bateria = Bateriasubcliente::find($registro->bateriaid);
                if ($bateria) {
                    $proveedor = Proveedor::where('proveedor', $bateria->proveedorasignado)->first();
                    if ($proveedor) {
                        $bateria->fechapago = $fechaPagoProv;
                        $bateria->prioridad = 'CUENTA POR PAGAR';
                        /* if ($proveedor->bancoorigen === 'CUENTA FACTURADA') {
                            $bateria->nrobancoorigen = '3000189269';
                        } elseif ($proveedor->bancoorigen === 'CUENTA NO FACTURADA') {
                            $bateria->nrobancoorigen = '2505314878';
                        } */
                        $bateria->nrobancoorigen = $nrocuentabanco;
                        $bateria->ordenid = $nuevoOrdenId;
                        $bateria->save();
                    }
                }
            }

            foreach ($registrosFinales as $registroFinal) {
                $registroFinal->save();

                $bateria = Bateriasubcliente::where('provinfofinalid', $registroFinal->id)->first();
                if ($bateria) {
                    $proveedor = Proveedor::where('proveedor', $bateria->proveedorasignado)->first();
                    if ($proveedor) {
                        $bateria->fechapago = $fechaPagoProv;
                        $bateria->prioridad = 'CUENTA POR PAGAR';
                        /* if ($proveedor->bancoorigen === 'CUENTA FACTURADA') {
                            $bateria->nrobancoorigen = '3000189269';
                        } elseif ($proveedor->bancoorigen === 'CUENTA NO FACTURADA') {
                            $bateria->nrobancoorigen = '2505314878';
                        } */
                        $bateria->nrobancoorigen = $nrocuentabanco;
                        $bateria->ordenid = $nuevoOrdenId;
                        $bateria->save();
                    }
                }
            }

            return redirect()->back()->with('info', 'Registro actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'Acción no reconocida.');
    }
    public function subirFacturasOtrosProv(Request $request)
    {
        $action = $request->input('action');
        $ids = explode(',', $request->input('ids_seleccionados7'));

        if ($action === 'guardar') {
            $request->validate([
                'nro_factura' => 'required|string',
                'codigo_autorizacion' => 'required|string',
                'archivo_comprobante' => 'required|mimes:pdf|max:10240',
            ]);

            $archivo = $request->file('archivo_comprobante');
            $nombreArchivo = Str::random(10) . '_' . $archivo->getClientOriginalName();
            $archivo->move(public_path('comprobantescuentaspagar'), $nombreArchivo);

            // AGREGAR REGISTRO A FACTURASEGRESO
                // Obtener registros seleccionados
                $registros = DB::table('cuentasporpagar')
                    ->whereIn('id', $ids)
                    ->get();

                // Validar que todos tengan el mismo proveedor
                $proveedores = $registros->pluck('proveedornombre')->unique();
                if ($proveedores->count() > 1) {
                    return back()->with('error', 'Todos los registros seleccionados deben ser del mismo proveedor.');
                }

                $razonsocial = $proveedores->first();
                $subtotal = $registros->sum('subtotal');
                $descuento = $registros->sum('descuento');
                $total = $subtotal - $descuento;
                $importenosujetocfdf = 0;
                $importeBaseCFDF = $total - $descuento - $importenosujetocfdf;
                $creditoDebitoFiscal = $importeBaseCFDF * 0.13;

                $proveedor = Proveedoresservicios::where('razonsocial', $razonsocial)->first();
                $nit = ($proveedor && !empty($proveedor->nit) && $proveedor->nit != '0') ? $proveedor->nit : ($proveedor->ci ?? '');
                $ciudad = $registros->first()->ciudad ?? 'NO DEFINIDO';
                $usuarioId = Auth::id();
                $usuarioNombre = Auth::user()->name;

                //EMPRESAS EXCLUIDAS POR LA RAZON SOCIAL 
                $razonesExcluidas = [
                    'COOPERATIVA DE SERVICIOS PUBLICOS SANTA CRUZ R.L.',
                    'COOPERATIVA RURAL DE ELECTRIFICACIÓN R.L.',
                    'LAURA GONZALES MONTENEGRO',
                ];

                if (!in_array(strtoupper($razonsocial), $razonesExcluidas)) {
                    DB::table('facturasegreso')->insert([
                        'especificacion' => '1',
                        'nitci' => $nit,
                        'razonsocial' => $razonsocial,
                        'codigoautorizacion' => $request->codigo_autorizacion,
                        'nrofactura' => $request->nro_factura,
                        'fechafacturaduidim' => now(),
                        'subtotal' => $subtotal,
                        'descuento' => $descuento,
                        'total' => $total,
                        'importebasecfdf' => $importeBaseCFDF,
                        'creditodebitofiscal' => $creditoDebitoFiscal,
                        'tipo' => '1',
                        'ciudad' => $ciudad,
                        'estado' => 'VALIDO',
                        'importenosujetocfdf' => $importenosujetocfdf,
                        'usuarioregistroid' => $usuarioId,
                        'usuarioregistronombre' => $usuarioNombre,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'nroduidim' => '0.00',
                        'ice' => '0.00',
                        'iehd' => '0.00',
                        'ipj' => '0.00',
                        'tasas' => '0.00',
                        'importeyexporteexterno' => '0.00',
                        'tasacero' => '0.00',
                        'giftcard' => '0.00',
                        'codigocontrol' => '0.00',
                        'otronosujcredfiscaloiva' => '0.00',
                        'complemento' => '0',
                    ]);
                }
            //

            foreach ($ids as $id) {
                DB::table('cuentasporpagar')
                    ->where('id', $id)
                    ->update([
                        'nrofactura' => $request->nro_factura,
                        'codautorizacion' => $request->codigo_autorizacion,
                        'factura' => $nombreArchivo,
                    ]);
            }

            return back()->with('info', 'Registros actualizados correctamente.');
        }

        if ($action === 'anular') {
            foreach ($ids as $id) {
                DB::table('cuentasporpagar')
                    ->where('id', $id)
                    ->update([
                        'nrofactura' => null,
                        'codautorizacion' => null,
                        'factura' => null,
                    ]);
            }
            return back()->with('info', 'Facturas anuladas correctamente.');
        }
        
        return back()->with('error', 'Acción no reconocida.');
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
            $fechasAsignadas = DB::table('cuentasporpagar')
                ->whereIn('id', $cuentas)
                ->pluck('fechaasignada')
                ->unique();

            // Aprobar seleccionados
            DB::table('cuentasporpagar')
                ->whereIn('id', $cuentas)
                ->update(['estadoaprobacion' => 'APROBADO']);

            DB::table('cuentasporpagar')
                ->whereIn('fechaasignada', $fechasAsignadas)
                ->whereNotIn('id', $cuentas)
                ->whereNotIn('estadoaprobacion', ['APROBADO', 'CARGADO', 'SUBIDO'])
                ->update(['estadoaprobacion' => 'RECHAZADO']);

            $cuentasData = DB::table('cuentasporpagar')
                ->whereIn('id', $cuentas)
                ->where('nrobancoorigen', '3000189269')
                ->get();

            foreach ($cuentasData as $cuenta) {
                // Obtener proveedor
                $proveedorNombre = $cuenta->proveedornombre;
                $proveedorID = $cuenta->proveedorid;
                $proveedor = DB::table('proveedores')
                    ->where('proveedor', $proveedorNombre)
                    ->first()
                    ?: DB::table('proveedoresservicios')
                        ->where('razonsocial', $proveedorNombre)
                        ->where('id', $proveedorID)
                        ->first();

                if (!$proveedor) continue;
                if (!in_array($proveedor->tipoplanilla, ['PAGO A TERCERO','PAGO INTERBANCARIO'])) continue;

                // Datos comunes
                $cuentaProv = $proveedor->cuenta ?? $proveedor->numcuenta;
                $monto      = round($cuenta->montototal * 100);

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
                $key = $cuentaProv . '|' . $cuenta->ordenid;
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
            $fechapago = DB::table('bateriasubclientes')
                ->whereIn('id', $programaciones)
                ->pluck('fechapago')
                ->unique();

            // Aprobar seleccionados
            DB::table('bateriasubclientes')
                ->whereIn('id', $programaciones)
                ->update(['estadoaprobacion' => 'APROBADO']);

            // Rechazar los que NO fueron seleccionados, pero tienen la misma fechaasignada
            DB::table('bateriasubclientes')
                ->whereIn('fechapago', $fechapago)
                ->whereNotIn('id', $programaciones)
                ->whereNotIn('estadoaprobacion', ['APROBADO', 'CARGADO', 'SUBIDO'])
                ->update(['estadoaprobacion' => 'RECHAZADO']);

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
                $monto      = round($prog->preciocompra * 100);

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

                $key = $cuentaProv . '|' . $prog->ordenid;

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
            foreach ($agrupadosTercero as $key => $info) {
                list($cuentaProv, $ordenId) = explode('|', $key);
                $m = $info['monto'];
                $lines[] = "<d13>{$ordenId}<d12>{$ordenId}<d6>0<d7>{$m}<d9>{$cuentaProv}<d0>2";
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
            foreach ($agrupadosInter as $key => $info) {
                list($cuentaProv, $ordenId) = explode('|', $key);
                $d = $info['detalle'];
                $m = $info['monto'];
                $r = $info['razonsocial'];
                $lines[] = "<d8>{$d['codBanco']}<d13>{$ordenId}<d12>{$ordenId}<d6>0<d7>{$m}<d3>{$r}<d9>{$cuentaProv}<d2>{$d['docId']}<d1>{$d['codId']}<d51>{$d['codProd']}<d0>22";
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

    public function actualizarMonto(Request $request, $id)
    {
        $nuevoSubtotal = floatval($request->input('nuevo_subtotal'));
        $nuevoTotal = floatval($request->input('nuevo_total'));

        $registro = DB::table('cuentasporpagar')->where('id', $id)->first();

        if (!$registro) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        // Subtotal original y nuevo
        $subtotalOriginal = floatval($registro->subtotal);
        $diferencia = $subtotalOriginal - $nuevoSubtotal;

        if ($diferencia < 0) {
            return response()->json(['message' => 'El nuevo subtotal no puede ser mayor al original'], 400);
        }

        // 1. Actualizar el original
        DB::table('cuentasporpagar')->where('id', $id)->update([
            'subtotal' => $nuevoSubtotal,
            'montototal' => $nuevoTotal
        ]);

        if ($diferencia > 0) {
            // 2. Generar nuevo ID tipo nCP
            $ultimoId = DB::table('cuentasporpagar')
                ->where('id', 'like', '%CP')
                ->orderByDesc(DB::raw("CAST(SUBSTRING_INDEX(id, 'CP', 1) AS UNSIGNED)"))
                ->value('id');

            $nuevoIdNum = $ultimoId ? intval(str_replace('CP', '', $ultimoId)) + 1 : 1;
            $nuevoId = $nuevoIdNum . 'CP';

            // 3. Crear nuevo registro con el restante
            DB::table('cuentasporpagar')->insert([
                'id' => $nuevoId,
                'proveedorid' => $registro->proveedorid,
                'proveedornombre' => $registro->proveedornombre,
                'detalleproducto' => $registro->detalleproducto,
                'fechaasignada' => $registro->fechaasignada,
                'fechacomprar' => $registro->fechacomprar,
                'nrobancoorigen' => $registro->nrobancoorigen,
                'subtotal' => $diferencia,
                'descuento' => 0,
                'montototal' => $diferencia,
                'preciocompra' => '0.00',
                'estadoaprobacion' => 'RECHAZADO',
                'sucursalgasto' => $registro->sucursalgasto,
                'ciudad' => $registro->ciudad,
                'tipoorden' => $registro->tipoorden,
                'tipoproveedorservicio' => $registro->tipoproveedorservicio,
                'ordenid' => $registro->ordenid,
                'cantidad' => $registro->cantidad,
                'estado' => $registro->estado,
                'usuarioregistroid' => $registro->usuarioregistroid,
                'usuarioregistronombre' => $registro->usuarioregistronombre,
                'detalleordenid' => $registro->detalleordenid,
            ]);
        }

        return response()->json(['info' => 'Registro actualizado correctamente.']);
    }



    /* public function rechazarSeleccionados(Request $request)
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
    } */
   public function rechazarSeleccionados(Request $request)
{
    $cuentas = $request->input('cuentas', []);
    $programaciones = $request->input('programaciones', []);

    // Procesar cuentasporpagar
    if (!empty($cuentas)) {
        $registrosCuentas = DB::table('cuentasporpagar')
            ->whereIn('id', $cuentas)
            ->get();

        foreach ($registrosCuentas as $cuenta) {
            if ($cuenta->estadoaprobacion === 'SUGERIDO') {
                DB::table('cuentasporpagar')
                    ->where('id', $cuenta->id)
                    ->update([
                        'estadoaprobacion' => 'RECHAZADO',
                        'fechaasignada' => $cuenta->fechamora,
                        'fechamora' => null,
                    ]);
            } else {
                DB::table('cuentasporpagar')
                    ->where('id', $cuenta->id)
                    ->update([
                        'estadoaprobacion' => 'RECHAZADO',
                    ]);
            }
        }
    }

    // Procesar bateriasubclientes
    if (!empty($programaciones)) {
        $registrosProgramaciones = DB::table('bateriasubclientes')
            ->whereIn('id', $programaciones)
            ->get();

        foreach ($registrosProgramaciones as $prog) {
            if ($prog->estadoaprobacion === 'SUGERIDO') {
                DB::table('bateriasubclientes')
                    ->where('id', $prog->id)
                    ->update([
                        'estadoaprobacion' => 'RECHAZADO',
                        'fechapago' => $prog->fechamora,
                        'fechamora' => null,
                    ]);
            } else {
                DB::table('bateriasubclientes')
                    ->where('id', $prog->id)
                    ->update([
                        'estadoaprobacion' => 'RECHAZADO',
                    ]);
            }
        }
    }

    return back()->with('info', 'Registros rechazados correctamente.');
}

    public function cambiarfechaSeleccionados(Request $request)
    {
        $cuentas = $request->input('cuentas', []);
        $programaciones = $request->input('programaciones', []);
        $fechapago = $request->input('fechapagocambio');

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
    public function sugerirpagosSeleccionados(Request $request)
    {
        $cuentas = $request->input('cuentas', []);
        $programaciones = $request->input('programaciones', []);
        $fechapago = $request->input('fechapagocambio');

        if (!empty($cuentas)) {
            $cuentasPorPagar = CuentasPagar::whereIn('id', $cuentas)->get();

            foreach ($cuentasPorPagar as $cuenta) {
                if (is_null($cuenta->fechamora)) {
                    $cuenta->fechamora = $cuenta->fechaasignada;
                }
                $cuenta->fechaasignada = $fechapago;
                $cuenta->estadoaprobacion = 'SUGERIDO';
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
                $bateria->estadoaprobacion = 'SUGERIDO';
                $bateria->save();
            }
        }

        return back()->with('info', 'Cuentas por Pagar sugeridas correctamente.');
    }
    public function sugerirpagosSeleccionadosnomora(Request $request)
    {
        $cuentas = $request->input('cuentas', []);
        $programaciones = $request->input('programaciones', []);

        if (!empty($cuentas)) {
            $cuentasPorPagar = CuentasPagar::whereIn('id', $cuentas)->get();

            foreach ($cuentasPorPagar as $cuenta) {
                $cuenta->estadoaprobacion = 'SUGERIDO';
                $cuenta->save();
            }
        }

        if (!empty($programaciones)) {
            $baterias = BateriaSubCliente::whereIn('id', $programaciones)->get();

            foreach ($baterias as $bateria) {
                $bateria->estadoaprobacion = 'SUGERIDO';
                $bateria->save();
            }
        }

        return back()->with('info', 'Cuentas por Pagar sugeridas correctamente.');
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
    

        /* $query = Bateriasubcliente::with([
                'estadoprogclientes',
                'infomedicosclientes',
                'progclientes',
                'infofinalesclientes',
                'pagoservicioclientes',
                'pagoservicioinfofinalclientes',
                'provinfofinalclientes'])
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
                            $item->estadoprogclientes
                        ])->filter();
                        
                        $resultadoestado = $estadoProgramacion
                            ->flatMap(function ($estadoprogramacion) { 
                                return $estadoprogramacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();  

                    $programaciones = collect([
                            $item->progclientes
                        ])->filter();
                    
                        $resultadoprog = $programaciones
                            ->flatMap(function ($programacion) { 
                                return $programacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();                    

                    $informesubido = collect([
                            $item->infomedicosclientes
                        ])->filter();
                        
                        $resultadoinforme = $informesubido
                            ->flatMap(function ($informe) { 
                                return $informe;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accion', $item->accionnombre)
                    ->first();           

                    $informefinalsubido = collect([
                            $item->infofinalesclientes
                        ])->filter();
                        
                        $resultadoinformefinal = $informefinalsubido
                            ->flatMap(function ($informefinal) { 
                                return $informefinal;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $provinformes = collect([
                            $item->provinfofinalclientes
                        ])->filter();
                        
                        $resultadoprovinformes = $provinformes
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $resultadopagoinformefinal = $item->pagoservicioinfofinalclientes()
                        ->where('provinfofinalid', $item->provinfofinalid)
                        ->where('tipomovimiento', 'EGRESO')
                    ->first();

                    $pagobateria = collect([
                        $item->progclientes
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
                            $pagoservicioclientes = $detallerecibo->created_at->toDateString();
                        } else {
                            $pagoservicioclientes = $resultadopago->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : null;
                        }
                    } else {
                        $pagoservicioclientes = null;
                    }

                $fechaAtencion = $resultadoestado ? $resultadoestado->fechaatencionprogramacion : null;
                $fechaprogramacion = $resultadoprog ? $resultadoprog->fechaasignada : null;
                $idprogramacion = $resultadoprog ? $resultadoprog->id : null;
                $nrofacturaprog = $resultadoprog ? $resultadoprog->nrofactura : null;
                $documentofactura = $resultadoprog ? $resultadoprog->factura : null;
                $informedocumentacion = $resultadoinforme ? $resultadoinforme->created_at->toDateString() : null;
                $informedocumentacionfinal = $resultadoinformefinal ? $resultadoinformefinal->created_at->toDateString() : null;
                $pagoservicioinfofinalclientes = in_array($item->id, [3173, 3178, 3187, 3043]) 
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
                    'pagoservicioinforme' => $pagoservicioclientes,
                    'pagoservicioinformefinal' => $pagoservicioinfofinalclientes,
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
        } */

        $query = Bateriasubcliente::with([
            'estadoprogclientes',
            'infomedicosclientes',
            'progclientes',
            'infofinalesclientes',
            'pagoservicioclientes',
            'pagoservicioinfofinalclientes',
            'provinfofinalclientes'
            ])
            ->whereNotNull('proveedorasignado')
            ->whereNotNull('preciocompra')
            ->where('preciocompra', '>', 0)
            ->where('pagoservicio', '<>', 'EXTERNO')
            ->whereNotIn('proveedorasignado', [
                'DIAGNOSTICO MEDICO POR IMAGEN DMI',
                'PROVEEDOR AJENO'
            ])
        ->orderBy('proveedorasignado');

        if ($request->filled('buscarporcliente')) {
            $query->where('proveedorasignado', 'LIKE', '%' . $request->buscarporcliente . '%');
        }

        $bateriaproveedores = $query->get();

        $grouped = $bateriaproveedores->groupBy('proveedorasignado');
        $result = [];

        foreach ($grouped as $proveedor => $items) {

            $accionesConEstado = [];

            foreach ($items as $item) {

                $estadoProgramacion = $item->estadoprogclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();

                $programacion = $item->progclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();

                $informe = $item->infomedicosclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accion', $item->accionnombre)
                    ->first();

                $informeFinal = $item->infofinalesclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->first();

                $provInformeFinal = $item->provinfofinalclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->first();

                $pagoInformeFinal = $item->pagoservicioinfofinalclientes
                    ->where('provinfofinalid', $item->provinfofinalid)
                    ->where('tipomovimiento', 'EGRESO')
                    ->first();

                $pagoServicio = null;

                if ($programacion) {
                    $detallerecibo = Detallerecibo::where('programacionid', $programacion->id)
                        ->where('tipomovimiento', 'EGRESO')
                        ->first();

                    $pagoServicio = $detallerecibo
                        ? $detallerecibo->created_at->toDateString()
                        : ($programacion->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : null);
                }

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'preciocompra' => $item->preciocompra,
                    'pagoservicio' => $item->pagoservicio,
                    'clienteid' => $item->clienteid,
                    'clientenombre' => $item->clientenombre,
                    'fechabateria' => $item->fechabateria,
                    'fechaatencionprogramacion' => optional($estadoProgramacion)->fechaatencionprogramacion,
                    'fechaprogramacion' => optional($programacion)->fechaasignada,
                    'idprogramacion' => optional($programacion)->id,
                    'nrofacturaprog' => optional($programacion)->nrofactura,
                    'documentofactura' => optional($programacion)->factura,
                    'informedocumentacion' => optional($informe)->created_at?->toDateString(),
                    'informedocumentacionfinal' => optional($informeFinal)->created_at?->toDateString(),
                    'pagoservicioinforme' => $pagoServicio,
                    'pagoservicioinformefinal' => in_array($item->id, [3173, 3178, 3187, 3043])
                        ? 'PROCESADO'
                        : optional($pagoInformeFinal)->created_at?->toDateString(),
                    'nrofacturainformefinal' => optional($provInformeFinal)->nrofactura,
                    'prioridad' => $item->prioridad,
                ];
            }

            $result[] = [
                'proveedorasignado' => $proveedor,
                'estado' => 'COMPLETO',
                'fechabateria' => $items->first()->fechabateria,
                'acciones' => $accionesConEstado,
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
        if ($nroCuenta == '2505314878' || $nroCuenta == '1031266712') {
            DB::table('cuentasporpagar')
                ->where('fechaasignada', $fecha)
                ->where('estadoaprobacion', '=', 'APROBADO')
                ->update(['estadoaprobacion' => 'SUBIDO']);
            DB::table('bateriasubclientes')
                ->where('fechapago', $fecha)
                ->where('estadoaprobacion', '=', 'APROBADO')
                ->update(['estadoaprobacion' => 'SUBIDO']);

        } elseif ($nroCuenta == '3000189269') {
            $proveedoresValidos = DB::table('proveedores')
                ->whereIn('tipoplanilla', ['PAGO INTERBANCARIO', 'PAGO A TERCERO', 'PAGO CHEQUE'])
                ->pluck('proveedor')
                ->toArray();
            $serviciosValidos = DB::table('proveedoresservicios')
                ->whereIn('tipoplanilla', ['PAGO INTERBANCARIO', 'PAGO A TERCERO', 'PAGO CHEQUE'])
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
        
        /* $query = Bateriasubcliente::with([
            'estadoprogclientes',
            'infomedicosclientes',
            'progclientes',
            'infofinalesclientes',
            'pagoservicioclientes',
            'pagoservicioinfofinalclientes',
            'provinfofinalclientes'
            ])
            ->whereNotNull('proveedorasignado')
            ->whereNotNull('preciocompra')
            ->where('preciocompra', '>', 0)
            ->where('pagoservicio', '<>', 'EXTERNO')
            ->whereNotIn('proveedorasignado', [
                'DIAGNOSTICO MEDICO POR IMAGEN DMI',
                'PROVEEDOR AJENO'
            ])
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
                            $item->estadoprogclientes
                        ])->filter();
                        
                        $resultadoestado = $estadoProgramacion
                            ->flatMap(function ($estadoprogramacion) { 
                                return $estadoprogramacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();  

                    $programaciones = collect([
                            $item->progclientes
                        ])->filter();
                    
                        $resultadoprog = $programaciones
                            ->flatMap(function ($programacion) { 
                                return $programacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();                    

                    $informesubido = collect([
                            $item->infomedicosclientes
                        ])->filter();
                        
                        $resultadoinforme = $informesubido
                            ->flatMap(function ($informe) { 
                                return $informe;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accion', $item->accionnombre)
                    ->first();           

                    $informefinalsubido = collect([
                            $item->infofinalesclientes
                        ])->filter();
                        
                        $resultadoinformefinal = $informefinalsubido
                            ->flatMap(function ($informefinal) { 
                                return $informefinal;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $provinformes = collect([
                            $item->provinfofinalclientes
                        ])->filter();
                        
                        $resultadoprovinformes = $provinformes
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $resultadopagoinformefinal = $item->pagoservicioinfofinalclientes()
                        ->where('provinfofinalid', $item->provinfofinalid)
                        ->where('tipomovimiento', 'EGRESO')
                    ->first();

                    $pagobateria = collect([
                        $item->progclientes
                    ])->filter();
                    
                    $resultadopago = $pagobateria
                        ->flatMap(fn($pago) => $pago)
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                    ->first();

                    $preciocompra = $item->preciocompra;
                    $pagoservicioclientes = null;

                    if ($resultadopago) {
                        $programacionId = $resultadopago->id;

                        $detallerecibo = Detallerecibo::where('programacionid', $programacionId)
                            ->where('tipomovimiento', 'EGRESO')
                            ->orderByDesc('id')
                            ->first();

                        if ($detallerecibo) {
                            if ($detallerecibo->estado === 'PAGO PROCESADO') {
                                $pagoservicioclientes = $detallerecibo->created_at->toDateString();
                            } elseif ($detallerecibo->estado === 'SALDO PENDIENTE') {
                                $pagoservicioclientes = 'SALDO PENDIENTE';
                                $preciocompra = $detallerecibo->saldo ?? $item->preciocompra;
                            }
                        } else {
                            $pagoservicioclientes = $resultadopago->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : 'PENDIENTE';
                        }
                    } else {
                        $pagoservicioclientes = 'PENDIENTE';
                    }

                $fechaAtencion = $resultadoestado ? $resultadoestado->fechaatencionprogramacion : null;
                $fechaprogramacion = $resultadoprog ? $resultadoprog->fechaasignada : null;
                $idprogramacion = $resultadoprog ? $resultadoprog->id : null;
                $nrofacturaprog = $resultadoprog ? $resultadoprog->nrofactura : null;
                $informedocumentacion = $resultadoinforme ? $resultadoinforme->created_at->toDateString() : null;
                $informedocumentacionfinal = $resultadoinformefinal ? $resultadoinformefinal->created_at->toDateString() : null;
                $pagoservicioinfofinalclientes = in_array($item->id, [3173, 3178, 3187, 3043]) 
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
                    'pagoservicioinforme' => $pagoservicioclientes,
                    'pagoservicioinformefinal' => $pagoservicioinfofinalclientes,
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
        } */

        $query = Bateriasubcliente::with([
            'estadoprogclientes',
            'infomedicosclientes',
            'progclientes',
            'infofinalesclientes',
            'pagoservicioclientes',
            'pagoservicioinfofinalclientes',
            'provinfofinalclientes'
        ])
        ->whereNotNull('proveedorasignado')
        ->whereNotNull('preciocompra')
        ->where('preciocompra', '>', 0)
        ->where('pagoservicio', '<>', 'EXTERNO')
        ->whereNotIn('proveedorasignado', [
            'DIAGNOSTICO MEDICO POR IMAGEN DMI',
            'PROVEEDOR AJENO'
        ])
        ->orderBy('proveedorasignado');

        if ($request->filled('buscarporcliente')) {
            $query->where('proveedorasignado', 'LIKE', '%' . $request->buscarporcliente . '%');
        }

        $bateriaproveedores = $query->get();
        $grouped = $bateriaproveedores->groupBy('proveedorasignado');
        $result = [];

        foreach ($grouped as $proveedor => $items) {

            $accionesConEstado = [];

            foreach ($items as $item) {

                $estadoProgramacion = $item->estadoprogclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();

                $programacion = $item->progclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();

                $informe = $item->infomedicosclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accion', $item->accionnombre)
                    ->first();

                $informeFinal = $item->infofinalesclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->first();

                $provInformeFinal = $item->provinfofinalclientes
                    ->where('fechabateria', $item->fechabateria)
                    ->first();

                $pagoInformeFinal = $item->pagoservicioinfofinalclientes
                    ->where('provinfofinalid', $item->provinfofinalid)
                    ->where('tipomovimiento', 'EGRESO')
                    ->first();

                $preciocompra = $item->preciocompra;
                $pagoservicio = 'PENDIENTE';

                if ($programacion) {

                    $detallerecibo = Detallerecibo::where('programacionid', $programacion->id)
                        ->where('tipomovimiento', 'EGRESO')
                        ->latest('id')
                        ->first();

                    if ($detallerecibo) {
                        if ($detallerecibo->estado === 'PAGO PROCESADO') {
                            $pagoservicio = $detallerecibo->created_at->toDateString();
                        } elseif ($detallerecibo->estado === 'SALDO PENDIENTE') {
                            $pagoservicio = 'SALDO PENDIENTE';
                            $preciocompra = $detallerecibo->saldo ?? $preciocompra;
                        }
                    } else {
                        $pagoservicio = $programacion->pagoatencion === 'PAGO PROCESADO'
                            ? 'PROCESADO'
                            : 'PENDIENTE';
                    }
                }

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'preciocompra' => $preciocompra,
                    'pagoservicio' => $pagoservicio,
                    'clienteid' => $item->clienteid,
                    'clientenombre' => $item->clientenombre,
                    'fechabateria' => $item->fechabateria,
                    'fechaatencionprogramacion' => optional($estadoProgramacion)->fechaatencionprogramacion,
                    'fechaprogramacion' => optional($programacion)->fechaasignada,
                    'idprogramacion' => optional($programacion)->id,
                    'nrofacturaprog' => optional($programacion)->nrofactura,
                    'informedocumentacion' => optional($informe)->created_at?->toDateString(),
                    'informedocumentacionfinal' => optional($informeFinal)->created_at?->toDateString(),
                    'pagoservicioinformefinal' => in_array($item->id, [3173, 3178, 3187, 3043])
                        ? 'PROCESADO'
                        : optional($pagoInformeFinal)->created_at?->toDateString(),
                    'nrofacturainformefinal' => optional($provInformeFinal)->nrofactura,
                    'prioridad' => $item->prioridad,
                ];
            }

            $result[] = [
                'proveedorasignado' => $proveedor,
                'estado' => 'COMPLETO',
                'fechabateria' => $items->first()->fechabateria,
                'acciones' => $accionesConEstado,
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
                ->whereColumn('proveedorinformesfinales.servicio', 'informesfinales.servicio')
                ->whereNull('informesfinales.motivoanulacion');
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

        $documentosPorFecha = PlanillasPagosGeneradas::select('tipo', 'documento', 'fechapago', 'proveedor')
        ->get()
        ->groupBy('fechapago');

        $usuarioscomprobantes = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['CONTABLE', 'ADMINISTRADOR']);
        })->orderBy('name', 'asc')->get();

        return view('admin.caja.cuentaspagar.cppcomprobantes', compact('usuarioscomprobantes','registrosbateria','cuentaspagar', 'usuarioAutenticado','result', 'fechas', 'proveedor', 'documentosPorFecha', 'proveedoresServicios'));
    }
    public function actualizarComprobante(Request $request)
    {
        if (!$request->hasFile('archivo')) {
            return response()->json(['message' => 'No se subió ningún archivo.'], 400);
        }
        $archivo = $request->file('archivo');
        $nombreArchivo = Str::random(10) . '_' . $archivo->getClientOriginalName();
        $archivo->move(public_path('comprobantescuentaspagar'), $nombreArchivo);

        $nombreArchivo2 = null;

        if ($request->hasFile('archivo2')) {
            $archivo2 = $request->file('archivo2');
            $nombreArchivo2 = Str::random(10) . '_' . $archivo2->getClientOriginalName();
            $archivo2->move(public_path('comprobantescuentaspagar'), $nombreArchivo2);
        }

        $usuarioNotificadoNombre = $request->usuarioNotificado;
        $usuarioNotificado = User::where('name', $usuarioNotificadoNombre)->first();
        $usuarioAuth = auth()->user();

        // CUENTAS POR PAGAR
        if ($request->has('cuentas')) {
            $cuentas = CuentasPagar::whereIn('id', $request->cuentas)->get();

            if ($usuarioNotificado) {
                $cuentasPorOrden = $cuentas->groupBy('ordenid');

                foreach ($cuentasPorOrden as $ordenid => $grupoCuentas) {
                    $cuenta = $grupoCuentas->first();
                    $usuarioNotificado->notify(new ComprobanteNotification($cuenta));
                }
            }

            CuentasPagar::whereIn('id', $request->cuentas)
                ->update([
                    'comprobante' => $nombreArchivo,
                    'cheque' => $nombreArchivo2,
                    'usuariocomprobante' => $usuarioAuth->name,
                ]);

                // SI EXISTE CHEQUE, ACTUALIZAR TAMBIÉN EN DETALLERECIBO
                if ($nombreArchivo2) {
                    foreach ($request->cuentas as $cuentaId) {
                        DB::table('detallerecibos')
                            ->where('cuentapagarid', $cuentaId)
                            ->update([
                                'cheque' => $nombreArchivo2,
                                'comprobante' => $nombreArchivo
                            ]);
                    }
                }
        }

        //PROGRAMACIONES MEDICAS
        if ($request->has('bateria')) {
            $bateriaIDs = $request->bateria;

            Bateriasubcliente::whereIn('id', $bateriaIDs)
                ->update([
                    'comprobante' => $nombreArchivo,
                    'cheque' => $nombreArchivo2,
                    'usuariocomprobante' => $usuarioAuth->name,
                ]);
            $baterias = Bateriasubcliente::whereIn('id', $bateriaIDs)->get();

            foreach ($baterias as $bateria) {
                $actualizados = ProgramacionSubCliente::where('bateriaid', $bateria->id)->update([
                    'comprobante' => $nombreArchivo,
                    'cheque' => $nombreArchivo2,
                    'usuariocomprobante' => $usuarioAuth->name,
                ]);

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
                        'cheque' => $nombreArchivo2,
                        'usuariocomprobante' => $usuarioAuth->name,
                    ]);
                }

                if (strtoupper($bateria->accionnombre) === 'INFORME FINAL' && $bateria->provinfofinalid) {
                    ProveedorInformeFinal::where('id', $bateria->provinfofinalid)
                        ->update([
                            'comprobante' => $nombreArchivo,
                            'cheque' => $nombreArchivo2,
                            'usuariocomprobante' => $usuarioAuth->name,
                        ]);
                }
            }

            if ($nombreArchivo2) { // solo si hay cheque
                foreach ($baterias as $bateria) {
                    // 1. Buscar registros en detallerecibo donde programacionid coincida con los registros actualizados en ProgramacionSubCliente
                    $programaciones = ProgramacionSubCliente::where('bateriaid', $bateria->id)->pluck('id');

                    foreach ($programaciones as $programacionId) {
                        DB::table('detallerecibos')
                            ->where('programacionid', $programacionId)
                            ->update([
                                'cheque' => $nombreArchivo2,
                                'comprobante' => $nombreArchivo,
                            ]);
                    }

                    // 2. Si hay informe final, buscar también en detallerecibo por provinfofinalid
                    if (strtoupper($bateria->accionnombre) === 'INFORME FINAL' && $bateria->provinfofinalid) {
                        DB::table('detallerecibos')
                            ->where('provinfofinalid', $bateria->provinfofinalid)
                            ->update([
                                'cheque' => $nombreArchivo2,
                                'comprobante' => $nombreArchivo,
                            ]);
                    }
                }
            }


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
                ->whereNull('deleted_at')
                ->first();

            $registroCierreCaja = true;

            // ✅ Si existe un registro en cajacentral, debemos validar su cierre
            if ($ultimoRegistro) {
                $fechaUltimoRegistro = Carbon::parse($ultimoRegistro->created_at)->toDateString();

                // Si la fecha del último registro NO es hoy, exigimos cierre para esa fecha
                if ($fechaUltimoRegistro !== $hoy->toDateString()) {
                    $registroCierreCaja = DB::table('cierrecaja')
                        ->where('usuariocierreid', $idUsuario)
                        ->whereDate('fechacierre', $fechaUltimoRegistro)
                        ->exists();
                }
            }

            $codigoAprobacion = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
                ->whereDate('fechaSolicitada', $hoy->toDateString())
                ->where('permisoSolicitado', 'admin.ingreso.index')
                ->where('estado', 'expirado')
                ->exists();

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

            $motivoBloqueo = null;
            if ($ultimoRegistro && !$registroCierreCaja) {
                $motivoBloqueo = 'NO CERRASTE TU CAJA DEL DÍA ' . Carbon::parse($ultimoRegistro->created_at)->format('d/m/Y');
            } elseif ($restriccionDeposito) {
                $motivoBloqueo = 'NO REGISTRASTE EL DEPÓSITO DEL EFECTIVO DE AYER ANTES DE LAS 10:00 AM.';
            }

        //

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

                    /* case isset($registro->clienteid):
                        $registro->tramite = TramitesubCliente::where('clienteid', $registro->clienteid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->value('tramite');
                        break; */

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
                ->where('tipomovimiento', 'INGRESO')
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

                    /* case isset($registro->clienteid):
                        $registro->tramite = TramitesubCliente::where('clienteid', $registro->clienteid)
                            ->where('fechabateria', $registro->fechabateria)
                            ->latest('id')
                            ->value('tramite');
                        break; */

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
            'codautorizacion' => '',
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
            'codautorizacion' => $request->codautorizacion,
        ]);

        // AGREGAR REGISTRO A FACTURASEGRESO
            // Usuario autenticado
            $usuarioId = Auth::id();
            $usuarioNombre = Auth::user()->name;

            // Obtener NITCI y complemento
            $nitci = '';
            $complemento = '';

            // ID del cliente o proveedor
            $idEntidad = $request->proveedorid;

            // Consultar en las 3 tablas
            $proveedor = DB::table('proveedores')->where('id', $idEntidad)->first()
                /* ?? DB::table('clienteauditorias')->where('id', $idEntidad)->first()
                ?? DB::table('clientescomunes')->where('id', $idEntidad)->first() */;

            // Si encontró
            if ($proveedor) {
                $nitci = (!empty($proveedor->nit) && $proveedor->nit != '0') ? $proveedor->nit : ($proveedor->ci ?? '0');
                $complemento = $proveedor->cicomplemento ?? '0';
                $sucursalproveedor = $proveedor->ciudad ?? '0';
            }

            // Insertar en facturasegreso
            if (!empty($request->nrofactura) && $request->nrofactura != '0') {
                DB::table('facturasegreso')->insert([
                    'especificacion' => '2',
                    'fechafacturaduidim' => now(),
                    'nrofactura' => $request->nrofactura,
                    'codigoautorizacion' => $request->codautorizacion,
                    'nitci' => $nitci,
                    'complemento' => '0',
                    'razonsocial' => $request->proveedornombre,
                    'total' => $request->montototal,
                    'ice' => '0.00',
                    'iehd' => '0.00',
                    'ipj' => '0.00',
                    'tasas' => '0.00',
                    'otronosujcredfiscaloiva' => '0.00',
                    'importeyexporteexterno' => '0.00',
                    'tasacero' => '0.00',
                    'subtotal' => $request->subtotal,
                    'descuento' => $request->descuento,
                    'giftcard' => '0.00',
                    'importebasecfdf' => $request->montototal,
                    'creditodebitofiscal' => $request->montototal * 0.13,
                    'estado' => 'VALIDO',
                    'codigocontrol' => '0',
                    'tipo' => '2',
                    'ciudad' => $sucursalproveedor,
                    'usuarioregistroid' => $usuarioId,
                    'usuarioregistronombre' => $usuarioNombre,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        //

        foreach ($programacionIds as $index => $programacionId) {
            /* $programacion = ProgramacionSubCliente::find($programacionId);
            $proveedor = ProveedorInformeFinal::find($programacionId);
            $cuentapagar = CuentasPagar::find($programacionId); */

            $programacion = null;
            $proveedor = null;
            $cuentapagar = null;
            if (str_ends_with($programacionId, 'CC')) {
                $cuentapagar = CuentasCobrar::find($programacionId);
            }
            if (!$cuentapagar) {
                $programacion = ProgramacionSubCliente::find($programacionId);
                $proveedor = ProveedorInformeFinal::find($programacionId);

                // 🔥 DECISIÓN REAL
                if ($proveedor && $proveedor->accionnombre === 'INFORME FINAL') {
                    // usar proveedor
                    $programacion = null;

                } else {
                    // usar programación (si existe)
                    $proveedor = null;
                }
            }
        
            $ultimoDetalleRecibo = Detallerecibo::where(function ($query) use ($programacionId) {
                    $query->where('programacionid', $programacionId)
                          ->orWhere('provinfofinalid', $programacionId);
                })
                ->where('tipomovimiento', 'INGRESO')
                ->orderBy('created_at', 'desc') 
                ->first();

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

            /* if ($estadoDetalle == 'PAGO PROCESADO') {
                Detallerecibo::where('programacionid', $programacionId)
                ->orwhere('provinfofinalid', $programacionId)
                    ->where('estado', '!=', 'PAGO PROCESADO')
                    ->update(['estado' => 'PAGO PROCESADO']);
            } */
            if ($estadoDetalle == 'PAGO PROCESADO') {
                if ($programacion) {
                    Detallerecibo::where('programacionid', $programacionId)
                        ->where('estado', '!=', 'PAGO PROCESADO')
                        ->update(['estado' => 'PAGO PROCESADO']);
                } elseif ($proveedor) {
                    Detallerecibo::where('provinfofinalid', $programacionId)
                        ->where('estado', '!=', 'PAGO PROCESADO')
                        ->update(['estado' => 'PAGO PROCESADO']);
                }
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
                'codautorizacion' => $request->codautorizacion,
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

                    $detalles = DB::table('detallerecibos')
                        ->where('reciboid', $registro->nrorecibo)
                        ->whereNotNull('cuentapagarid')
                        ->pluck('cuentapagarid');

                    if ($detalles->isNotEmpty()) {
                        DB::table('cuentasporpagar')
                            ->whereIn('id', $detalles)
                            ->update(['estado' => 'PENDIENTE']);
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
                ->whereNull('deleted_at')
                ->first();

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

            $mostrarVista = !$restriccionDeposito || $codigoAprobacion;

            $motivoBloqueo = null;
            if ($restriccionDeposito) {
                $motivoBloqueo = 'NO REGISTRASTE EL DEPÓSITO DEL EFECTIVO DE AYER ANTES DE LAS 10:00 AM.';
            }
        //

        /* $registros = CajaCentral::where('tipomovimiento', 'INGRESO')
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
        } */

        // Caja Central agrupada por fecha
        $registros = CajaCentral::where('tipomovimiento', 'INGRESO')
            ->where('tipotransaccion', 'EFECTIVO')
            ->when($usuarioSeleccionado, fn($q) => $q->where('usuarioregistroid', $usuarioSeleccionado))
            ->selectRaw('DATE(created_at) as fecha, usuarioregistroid, usuarioRegistroNombre,
                        SUM(montoTotal - diferenciaContra + diferenciaFavor) as total')
            ->groupBy('fecha', 'usuarioregistroid', 'usuarioRegistroNombre')
            ->orderByDesc('fecha')
            ->get();

        // Depósitos Bancarios relacionados por fecha y usuario
        $depositos = DepositosBancarios::whereIn('fecha', $registros->pluck('fecha'))
            ->whereIn('usuarioregistroid', $registros->pluck('usuarioregistroid'))
            ->orderByDesc('fecha')
            ->get();

        // Agrupar depósitos por fecha + usuario
        $depositosAgrupados = $depositos->groupBy(fn($d) => $d->fecha . '_' . $d->usuarioregistroid);

        // Vincular depósitos a los registros de caja
        foreach ($registros as $registro) {
            $clave = $registro->fecha . '_' . $registro->usuarioregistroid;
            $registro->depositos = $depositosAgrupados[$clave] ?? collect();
            $registro->montoDepositos = $registro->depositos->sum('monto');
        }

        return view('admin.caja.ingreso.depositosbancarios', compact('registros', 'rolUsuario', 'usuarios', 'fecha', 'usuarioSeleccionado', 'cuentas', 'mostrarVista', 'motivoBloqueo'));
    }
    public function verificarCodigo4(Request $request)
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
            return redirect()->route('admin.caja.ingreso.depositosbancarios')->with('info', 'CÓDIGO VÁLIDO, AHORA SI PUEDES CONTINUAR');

        } elseif ($codigoAprobacion && $codigoAprobacion->estado == 'expirado') {

            return back()->with('infoerror', 'EL CÓDIGO YA HA SIDO USADO, EL ACCESO ESTA BLOQUEADO');
        } else {

            return back()->with('infoerror', 'CÓDIGO INVALIDO O NO AUTORIZADO');
        }
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

        $usuarioId = auth()->user()->id;
        $historial = DB::table('historialarqueocaja')
            ->where('usuarioarqueoid', $usuarioId)
            ->orderBy('fechaarqueo', 'desc')
        ->first();

        if ($historial) {
            DB::table('arqueocaja')
                ->where('usuarioarqueoid', $usuarioId)
                ->update([
                    'billetecorte200' => DB::raw("GREATEST(billetecorte200 - {$historial->billetecorte200}, 0)"),
                    'billetecorte100' => DB::raw("GREATEST(billetecorte100 - {$historial->billetecorte100}, 0)"),
                    'billetecorte50' => DB::raw("GREATEST(billetecorte50 - {$historial->billetecorte50}, 0)"),
                    'billetecorte20' => DB::raw("GREATEST(billetecorte20 - {$historial->billetecorte20}, 0)"),
                    'billetecorte10' => DB::raw("GREATEST(billetecorte10 - {$historial->billetecorte10}, 0)"),
                    'monedacorte5' => DB::raw("GREATEST(monedacorte5 - {$historial->monedacorte5}, 0)"),
                    'monedacorte2' => DB::raw("GREATEST(monedacorte2 - {$historial->monedacorte2}, 0)"),
                    'monedacorte1' => DB::raw("GREATEST(monedacorte1 - {$historial->monedacorte1}, 0)"),
                    'monedacorte050' => DB::raw("GREATEST(monedacorte050 - {$historial->monedacorte050}, 0)"),
                    'monedacorte020' => DB::raw("GREATEST(monedacorte020 - {$historial->monedacorte020}, 0)"),
                    'monedacorte010' => DB::raw("GREATEST(monedacorte010 - {$historial->monedacorte010}, 0)"),
                    'updated_at' => now(),
                ]);
            DB::table('consolidadoscaja')
                ->where('usuarioconsolidadoid', $usuarioId)
                ->update([
                    'consolidadoefectivo' => DB::raw("GREATEST(consolidadoefectivo - {$historial->totalmonto}, 0)"),
                    'updated_at' => now()
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