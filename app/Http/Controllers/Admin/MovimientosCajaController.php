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
use Illuminate\Support\Facades\File;

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
    public function guardarAperturaCaja(Request $request)
    {
        $validated = $request->validate([
            'documentoapertura' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Guardar el archivo en la carpeta correspondiente
        $usuarioId = auth()->user()->id;
        $path = $request->file('documentoapertura')->storeAs('public/aperturacaja/' . $usuarioId, $request->file('documentoapertura')->getClientOriginalName());

        // Crear el registro de apertura de caja
        Aperturacaja::create([
            'usuarioaperturaid' => $usuarioId,
            'usuarioaperturanombre' => auth()->user()->name,
            'documentoapertura' => $path,
        ]);

        return back()->with('success', 'Documento de apertura guardado correctamente.');
    }
    /* INGRESOS */
    public function index()
    {
        $sucursal = auth()->user()->sucursal;
        $consolidados = Consolidadocaja::all();
        
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
            'aperturascajas'=> $aperturascajas
        ]);
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

            // Guardar la apertura de caja en la base de datos
            Aperturacaja::create([
                'usuarioaperturaid' => $usuarioId,
                'usuarioaperturanombre' => $usuarioNombre,
                'documentoapertura' => $archivo_name,
            ]);
        }

        return back()->with('info', 'Apertura de caja registrada exitosamente.');
    }
    public function verificarCodigo(Request $request)
    {
        // Obtiene el código que el usuario ha ingresado
        $codigoIngresado = $request->input('codigo');
        
        // Obtiene el nombre del usuario autenticado
        $usuarioAutenticado = auth()->user()->name;
        
        // Obtiene la fecha actual
        $fechaActual = now()->toDateString();
        
        // Realiza la consulta para encontrar un registro en CodigoAprobaciones que cumpla con las condiciones
        $codigoAprobacion = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $fechaActual)
            ->where('permisoSolicitado', 'admin.ingreso.index')
            ->where('codigo', $codigoIngresado)
            ->first();
        
        // Verifica si se encontró el registro y si el estado no es "expirado"
        if ($codigoAprobacion && $codigoAprobacion->estado != 'expirado') {
            // Actualiza el estado a "expirado"
            $codigoAprobacion->horaActivacion = now(); 
            $codigoAprobacion->estado = 'expirado';
            $codigoAprobacion->save();
            
            // Lógica para permitir el acceso, redirigir o mostrar el siguiente paso
            return redirect()->route('admin.caja.ingreso.index')->with('info', 'CÓDIGO VÁLIDO, AHORA SI PUEDES CONTINUAR');
        } elseif ($codigoAprobacion && $codigoAprobacion->estado == 'expirado') {
            // Si el registro ya tiene estado "expirado", muestra un mensaje de error
            return back()->with('infoerror', 'EL CÓDIGO YA HA SIDO USADO, EL ACCESO ESTA BLOQUEADO');
        } else {
            // Si no se encontró el código o no coinciden las condiciones, muestra un mensaje de error
            return back()->with('infoerror', 'CÓDIGO INVALIDO O NO AUTORIZADO');
        }
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
        if (!in_array($tipoCliente, ['clienteitaid', 'clienteauditoriaid', 'clientecomunid', 'clientebancoid'])) {
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
            case 'clientebancoid':
                $cliente = ClienteBanco::where('ci', $entrada)->orWhere('id', $entrada)->first(['id', 'nombrecompleto', 'ci']);
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
    
        // Unir ambos conjuntos de registros
        $registros = $registrosProgramacion->merge($registrosInformesFinales);
    
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

        $programacionIds = explode(',', $request->programacionIds);
        $descuentos = explode(',', $request->descuentos);
        $pagos = explode(',', $request->pagos);

        $tipocliente = match ($request->tipocliente) {
            'clienteitaid' => 'ITA',
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
        } elseif (in_array($tipotransaccion, ['EFECTIVO', 'CHEQUE', 'ATC'])) {
        }

        $tipotransaccion2 = $request->tipotransaccion2;
        if ($tipotransaccion2 == 'DEPOSITO_BANCARIO') {
            $tipotransaccion2 = 'DEPOSITO BANCARIO';
        } elseif ($tipotransaccion2 == 'TRANSFERENCIA_BANCARIA') {
            $tipotransaccion2 = 'TRANSFERENCIA BANCARIA';
        } elseif (in_array($tipotransaccion2, ['EFECTIVO', 'CHEQUE', 'ATC'])) {
        }

        $recibo = Recibo::create([
            'ciudadregistro' => $request->ciudadregistro,
            'usuarioregistroid' => $usuarioAutenticadoid,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
            'tipocliente' => $tipocliente,
            'clienteid' => $request->clienteid,
            'clientenombre' => $request->clientenombre,
            'tipomovimiento' => 'INGRESO',
            'subtotal' => $request->subtotal,
            'descuentototal' => $request->descuento,
            'montototal' => $request->montototal,
            'estado' => $estado,
            'saldototal' => $saldototal,
        ]);

        Cajacentral::create([
            'tipocliente' => $tipocliente,
            'clienteid' => $request->clienteid,
            'clientenombre' => $request->clientenombre,
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
        ]);

        foreach ($programacionIds as $index => $programacionId) {
            $programacion = ProgramacionSubCliente::find($programacionId);
            $proveedor = ProveedorInformeFinal::find($programacionId);
        
            // Verificar si ya existe un registro en Detallerecibo con el mismo programacionid
            $ultimoDetalleRecibo = Detallerecibo::where('programacionid', $programacionId)
                ->orwhere('provinfofinalid', $programacionId)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($ultimoDetalleRecibo) {
                $subtotalDetalle = $ultimoDetalleRecibo->saldo;
                $descuentoDetalle = $descuentos[$index];
                $pagoDetalle = $pagos[$index];
            } else {
                $subtotalDetalle = $programacion ? $programacion->precio : $proveedor->precio;
                $descuentoDetalle = $descuentos[$index];
                $pagoDetalle = $pagos[$index];
            }

            $subtotalDetalle = is_numeric($subtotalDetalle) ? floatval($subtotalDetalle) : 0;
            $saldoDetalle = $subtotalDetalle - $descuentoDetalle - $pagoDetalle;
            $saldoDetalle = number_format($saldoDetalle, 2, '.', '');
            $estadoDetalle = ($saldoDetalle == 0) ? 'PAGO PROCESADO' : 'SALDO PENDIENTE';

            if ($estadoDetalle == 'PAGO PROCESADO') {
                Detallerecibo::where('programacionid', $programacionId)
                    ->where('estado', '!=', 'PAGO PROCESADO')
                    ->update(['estado' => 'PAGO PROCESADO']);
            }
        
            Detallerecibo::create([
                'reciboid' => $recibo->id,
                'clienteid' => $recibo->clienteid,
                'clientenombre' => $recibo->clientenombre,
                'programacionid' => $programacion ? $programacionId : null,
                'bateriaid' => $programacion ? $programacion->bateriaid : null,
                'provinfofinalid' => $proveedor ? $programacionId : null,
                'usuarioregistroid' => $usuarioAutenticadoid,
                'usuarioregistronombre' => $usuarioAutenticadonombre,
                'area' => $programacion ? 'MEDICA' : 'INFORME FINAL',
                'detalle' => $programacion ? $programacion->accionnombre : $proveedor->accionnombre,
                'fechabateria' => $programacion ? $programacion->fechabateria : $proveedor->fechabateria,
                'fechaatencion' => $programacion ? $programacion->fechaasignada : $proveedor->fechaasignada,
                'servicio' => $programacion ? $programacion->servicio : $proveedor->servicio,
                'proveedoratencion' => $programacion ? $programacion->proveedornombre : $proveedor->proveedorasignado,
                'subtotal' => $subtotalDetalle,
                'descuento' => $descuentoDetalle,
                'montototal' => $pagoDetalle,
                'saldo' => $saldoDetalle,
                'estado' => $estadoDetalle,
                'tipomovimiento' => 'INGRESO',
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

    /* DOCUMENTACION REPALDO INGRESOS */
    /* public function respaldodocumentacioningreso() 
    {
        $userId = auth()->id();
        $rolUsuario = auth()->user()->getRoleNames()->first();
        $registros = CajaCentral::where('usuarioRegistroID', $userId)
                                ->where('tipomovimiento', 'INGRESO')
                                ->whereDate('updated_at', today())
                                ->get();

        return view('admin.caja.ingreso.documentacion', compact('registros','rolUsuario'));
    } */
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

        /* foreach ($registros as $registro) {
            if ($registro->tipotransaccion === 'ATC') {
                if (empty($registro->nrobancarizacionatc)) {
                    $registro->nrobancarizacionatc = $request->nrobancarizacion[$registro->id] ?? null;
                }
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
            $registro->estadorevisioncierre = 'RESPALDADO';

            $registro->documentorespaldo = $archivo_name ?? $registro->documentorespaldo;
            $registro->docfactura = $archivo_name3 ?? $registro->docfactura;
            $registro->doccomprobante = $archivo_name2 ?? $registro->doccomprobante;
            $registro->save();
        } */
        foreach ($registros as $registro) {
            if ($registro->tipotransaccion === 'ATC') {
                $nuevoNroBancarizacion = $request->nrobancarizacion[$registro->id] ?? null;
                
                if (empty($registro->nrobancarizacionatc) && $nuevoNroBancarizacion) {
                    $registro->nrobancarizacionatc = $nuevoNroBancarizacion;
                    
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
            
            $registro->estadorevisioncierre = 'RESPALDADO';
            $registro->documentorespaldo = $archivo_name ?? $registro->documentorespaldo;
            $registro->docfactura = $archivo_name3 ?? $registro->docfactura;
            $registro->doccomprobante = $archivo_name2 ?? $registro->doccomprobante;
            $registro->save();
        }

        return redirect()->back()->with('info', 'Registro respaldado exitosamente.');
    }

    /* CIERRE DE CAJA INGRESOS */
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

    /* CUENTAS POR COBRAR */
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
            ->where('servicio', '<>', 'EXTERNO')
            ->orderBy('clienteitanombre');
        
        $query2 = Bateriasubcliente::with(['estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria', 'programacionsubclienteauditoria','informesfinalesauditoria'])
            ->whereNotNull('clienteauditoriaid')
            /* ->whereNotNull('proveedorasignado') */
            /* ->where('preciocompra', '!=', NULL)
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)  */
            ->where('servicio', '<>', 'AJENO') 
            ->where('servicio', '<>', 'EXTERNO')
            ->orderBy('clienteauditorianombre');

        $query3 = Bateriasubcliente::with(['estadoprogramacionsubclientecomun', 'documentacionsubclientecomun', 'programacionsubclientecomun','informesfinalescomun'])
            ->whereNotNull('clientecomunnombre')
            /* ->whereNotNull('proveedorasignado') */
            /* ->where('preciocompra', '!=', NULL)
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)  */
            ->where('servicio', '<>', 'AJENO') 
            ->where('servicio', '<>', 'EXTERNO')
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

                $resultadopago = $item->programacionsubcliente
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

                $resultadopago = $item->programacionsubcliente
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

        return view('admin.caja.cuentascobrar.listacuentascobrar', compact('year', 'month', 'records', 'usuarioAutenticado','result', 'cliente', 'fechas','result2', 'clienteauditoria','result3', 'clientecomun'));
    }

    public function cobrarhoy(Request $request)
    {
        $fechaActual = now()->toDateString();

        /* PAGOS PENDIENTES INTERNOS */
        $pagosprogramacionesita = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clienteitaid')
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
        ->get();

        $pagosprogramacionescomun = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clientecomunid')
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
        ->get();

        $pagosprogramacionesauditoria = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
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
        ->get();


        /* PAGOS PENDIENTES EXTERNOS */
        $pagosexternosprogramacionesita = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clienteitaid')
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
        ->get();

        $pagosexternosprogramacionescomun = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clientecomunid')
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
        ->get();

        $pagosexternosprogramacionesauditoria = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
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
            ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clientescomunes.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                'bateriaproveedores.servicio',
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
            'pagosprogramacionesauditoria','fechaActual','pagosinformefinalita','pagosinformefinalauditoria','pagosprocesadosinformefinalita','pagosprocesadosinformefinalauditoria'
        ));
    }
    public function ccporcredito(Request $request)
    {
        $registros = collect();
        $gerentes = Proveedoresservicios::whereIn('cargo', ['GERENTE GENERAL', 'GERENTE FINANCIERO'])->get();

        if ($request->filled('search') || $request->filled('tipo_cliente')) {
            $search = $request->search;
            $tipoCliente = $request->tipo_cliente;
        
            // Si se selecciona un tipo de cliente, filtramos por ello
            $subClienteRegistros = Bateriasubcliente::where(function($query) use ($search, $tipoCliente) {
                // Si hay un tipo de cliente seleccionado, filtrar por ese tipo
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
            ->where('accionnombre', '!=', 'INFORME FINAL');
        
            // Buscar en ProveedorInformefinal con el mismo filtro por tipo de cliente
            $proveedorRegistros = ProveedorInformefinal::where(function($query) use ($search, $tipoCliente) {
                if ($tipoCliente) {
                    if ($tipoCliente == 'CLIENTE ITA') {
                        $query->where('clienteitaid', 'like', "%$search%")
                            ->orWhere('clienteitanombre', 'like', "%$search%");
                    } elseif ($tipoCliente == 'CLIENTE BANCO') {
                        $query->where('clientebancoid', 'like', "%$search%")
                            ->orWhere('clientebanconombre', 'like', "%$search%");
                    } elseif ($tipoCliente == 'CLIENTE AUDITORIA') {
                        $query->where('clienteauditoriaid', 'like', "%$search%")
                            ->orWhere('clienteauditorianombre', 'like', "%$search%");
                    } 
                } else {
                   
                }
            });
        
            // Concatenar ambos resultados
            $registros = $subClienteRegistros->get()->concat($proveedorRegistros->get());
        
            // Obtener la relación 'tramite' para cada registro
            foreach ($registros as $registro) {
                $registro->tramite = TramiteSubCliente::where(function($query) use ($registro) {
                    $query->where('clienteitaid', $registro->clienteitaid)
                        ->orWhere('clienteauditoriaid', $registro->clienteauditoriaid);
                })->first();
            }
        }
        

        return view('admin.caja.cuentascobrar.ccporcredito', compact('registros', 'gerentes'));
    }
    public function actualizarRegistros(Request $request)
    {
        $seleccionados = $request->input('seleccionados', []);
        $campoFecha = $request->input('campo_fecha', []);
        $gerenteId = $request->input('gerente'); // ID del gerente seleccionado
        $documento = $request->file('documento'); // Archivo subido
        $documentolcambio = $request->file('documentolcambio'); // Archivo subido
        $usuarioId = auth()->id(); // ID del usuario autenticado

        // Validar los datos
        $request->validate([
            'gerente' => 'required', // Verifica que el gerente sea válido
            'documento' => 'required', // Verifica que el archivo sea válido
            'documentolcambio' => 'required', // Verifica que el archivo sea válido
        ]);

        // Obtener el nombre del gerente
        $gerente = Proveedoresservicios::find($gerenteId);
        if (!$gerente) {
            return redirect()->back()->with('error', 'Gerente no encontrado.');
        }
        $nombreGerente = $gerente->nombrecompleto; // Cambia 'nombrecompleto' según el campo correcto en tu tabla


        $archivoName = null;
        if ($request->hasFile('documento')) {
            $carpetaUsuario = public_path("creditos/");
            if (!file_exists($carpetaUsuario)) {
                mkdir($carpetaUsuario, 0755, true); // Crear la carpeta si no existe
            }
            $archivoName = time() . '_' . $documento->getClientOriginalName(); // Generar nombre único
            $documento->move($carpetaUsuario, $archivoName); // Guardar el archivo en la carpeta
        }
        $archivoName2 = null;
        if ($request->hasFile('documentolcambio')) {
            $carpetaUsuario = public_path("creditos/");
            if (!file_exists($carpetaUsuario)) {
                mkdir($carpetaUsuario, 0755, true); // Crear la carpeta si no existe
            }
            $archivoName2 = time() . '_' . $documentolcambio->getClientOriginalName(); // Generar nombre único
            $documentolcambio->move($carpetaUsuario, $archivoName2); // Guardar el archivo en la carpeta
        }

        foreach ($seleccionados as $id) {
            // Actualizar ProveedorInformefinal
            $informeFinal = ProveedorInformefinal::find($id);
            if ($informeFinal) {
                if ($informeFinal->accionnombre === 'INFORME FINAL') {
                    $informeFinal->update([
                        'fechacredito' => $campoFecha[$id] ?? null,
                        'usuarioautorizador' => $nombreGerente,
                        'documentocredito' => $archivoName,
                        'documentolcambio' => $archivoName2,
                    ]);
                }
            } else {
                // Actualizar ProgramacionSubCliente
                $registro = Bateriasubcliente::find($id);
                if ($registro) {
                    $registro->update([
                        'fechacredito' => $campoFecha[$id] ?? null,
                        'usuarioautorizador' => $nombreGerente,
                        'documentocredito' => $archivoName,
                        'documentolcambio' => $archivoName2,
                    ]);
                }
            }
        }

        return redirect()->back()->with('info', 'Registros actualizados correctamente.');
    }
    
    public function buscarlistacuentascobrar(Cliente $cliente, ClienteAuditoria $clienteauditoria, ClienteComun $clientecomun, Request $request)
    {
        return $this->listacuentascobrar($cliente, $clienteauditoria,$clientecomun, $request);
    }

    /* EGRESOS */
    public function cajaegresos()
    {
        $sucursal = auth()->user()->sucursal;
        $consolidados = Consolidadocaja::all();
        $bancos = Banco::all();
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

        return view('admin.caja.egreso.cajaegresos', [
            'mostrarVista' => $mostrarVista,
            'sucursal' => $sucursal,
            'consolidados' => $consolidados,
            'bancos' => $bancos,
            'siguienteId' => $siguienteId,
            'rolusuario' => $rolusuario,
            'proveedores' => $proveedores
        ]);
    }
    public function buscarPorProveedoregreso(Request $request)   
    {
        $entrada = $request->input('proveedorid');
        $nrofactura = $request->input('nrofactura');
        $nrofactura2 = $request->input('nrofactura2');
        $nrofactura3 = $request->input('nrofactura3');
        $tipoproveedor = $request->input('tipocliente');

        if (!in_array($tipoproveedor, ['medico'/* , 'personal' */])) {
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
            /* case 'personal':
                $proveedor = Proveedoresservicios::where('ci', $entrada)
                    ->orWhere('proveedor', 'LIKE', "%{$entrada}%")
                    ->first(['id', 'proveedor', 'ci']);
                break; */
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
            
                // Obtener trámite según tipo de cliente
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

        $programacionIds = explode(',', $request->programacionIds);
        $descuentos = explode(',', $request->descuentos);
        $pagos = explode(',', $request->pagos);

        $tipocliente = match ($request->tipocliente) {
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
        } elseif (in_array($tipotransaccion, ['EFECTIVO', 'CHEQUE', 'ATC'])) {
        }

        $tipotransaccion2 = $request->tipotransaccion2;
        if ($tipotransaccion2 == 'DEPOSITO_BANCARIO') {
            $tipotransaccion2 = 'DEPOSITO BANCARIO';
        } elseif ($tipotransaccion2 == 'TRANSFERENCIA_BANCARIA') {
            $tipotransaccion2 = 'TRANSFERENCIA BANCARIA';
        } elseif (in_array($tipotransaccion2, ['EFECTIVO', 'CHEQUE', 'ATC'])) {
        }

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
            'estado' => $estado,
            'area' =>  $request->area,
            'detalle' => $request->detalle,
            'nrofactura' => $request->nrofactura,
            'nrofactura2' => $request->nrofactura2,
            'nrofactura3' => $request->nrofactura3,
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

        ]);

        foreach ($programacionIds as $index => $programacionId) {
            $programacion = ProgramacionSubCliente::find($programacionId);
            $proveedor = ProveedorInformeFinal::find($programacionId);
            $cuentapagar = CuentasPagar::find($programacionId);
        
            $ultimoDetalleRecibo = Detallerecibo::where('programacionid', $programacionId)
                ->orwhere('provinfofinalid', $programacionId)
                ->orwhere('cuentapagarid', $programacionId)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($ultimoDetalleRecibo) {
                $subtotalDetalle = $ultimoDetalleRecibo->saldo;
                $descuentoDetalle = $descuentos[$index];
                $pagoDetalle = $pagos[$index];
            } else {
                /* $subtotalDetalle = $programacion ? $programacion->preciocompra : $proveedor->preciocompra; */
                if ($programacion) {
                    $subtotalDetalle = $programacion->preciocompra;
                } elseif ($proveedor) {
                    $subtotalDetalle = $proveedor->preciocompra;
                } elseif ($cuentapagar) {
                    $subtotalDetalle = $cuentapagar->preciocompra;
                } else {
                    $subtotalDetalle = 0; // Valor por defecto en caso de que no se encuentren los datos
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
                'tipomovimiento' => 'EGRESO',
            ]);
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
    
    /* DOCUMENTACION RESPALDO EGRESOS */
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

        // Actualizar registros seleccionados
        CajaCentral::whereIn('id', $request->registro_ids)->update([
            'estadorevisioncierre' => 'RESPALDADO',
            'docfactura' => $archivo_name2,
            'doccomprobante' => $archivo_name3,
        ]);

        return redirect()->back()->with('info', 'Registro respaldado exitosamente.');
    }

    /* CUENTAS POR PAGAR */
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
        'estadoprogramacionsubclientecomun', 'documentacionsubclientecomun', 'programacionsubclientecomun','informesfinalescomun','provinfofinalcomun'])
            ->whereNotNull('proveedorasignado')
            ->where('preciocompra', '!=', NULL)
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)
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

        return view('admin.caja.cuentaspagar.listacuentaspagar', compact('year', 'month', 'records', 'usuarioAutenticado','result', 'fechas', 'proveedor'));
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
                    $pagoservicioinformefinal = $resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null;
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
                    'accion' => $item->accionnombre,
                ];
            }

        // Generar el PDF con los datos reconstruidos
        $pdf = Pdf::loadView('admin.caja.cuentaspagar.reporte', compact('result'));

        return $pdf->download('Reporte_Cuentas_Pagar.pdf');
    }
    public function buscarlistacuentaspagar(Proveedor $proveedor,  Request $request)
    {
        return $this->listacuentaspagar($proveedor, $request);
    }
    public function actualizarFactura(Request $request)
    {
        $nroFactura = $request->input('nroFactura');
        $seleccionados = $request->input('seleccionados');
    
        foreach ($seleccionados as $id) {
            // Verifica si es un idprogramacion o provinfofinalid
            $registro = Programacionsubcliente::find($id);
            if ($registro) {
                $registro->nroFactura = $nroFactura;
                $registro->save();
            } else {
                $registro = ProveedorInformefinal::find($id);
                if ($registro) {
                    $registro->nroFactura = $nroFactura;
                    $registro->save();
                }
            }
        }
    
        return redirect()->back()->with('info', 'Facturas actualizadas correctamente.');
    }
    
//INGRESOS EXTERNOS
    public function ingresosexternos()
    {
        $sucursal = auth()->user()->sucursal;
        $consolidados = Consolidadocaja::all();
        $bancos = Banco::all();
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
            'arqueo' => $arqueo
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
                    $existeEnBateria = BateriaSubCliente::where('fechabateria', $registro->fechabateria)
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


        return response()->json([
            'proveedor' => $proveedor,
            'registros' => $registros
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
        ]);

        foreach ($programacionIds as $index => $programacionId) {
            $programacion = ProgramacionSubCliente::find($programacionId);
            $proveedor = ProveedorInformeFinal::find($programacionId);
            $cuentapagar = CuentasPagar::find($programacionId);
        
            // Verificar si ya existe un registro en Detallerecibo con el mismo programacionid
            $ultimoDetalleRecibo = Detallerecibo::where('programacionid', $programacionId)
                ->orwhere('provinfofinalid', $programacionId)
                ->orwhere('cuentapagarid', $programacionId)
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


    /* ANULACIONES DE CAJA */
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
                    'deleted_at' => $fechaActual,
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
    /* DEPOSITOS BANCARIOS */
    public function depositosbancarios(Request $request)
    {
        $userId = auth()->id();
        $rolUsuario = auth()->user()->getRoleNames()->first();
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

        return view('admin.caja.ingreso.depositosbancarios', compact('registros', 'rolUsuario', 'usuarios', 'fecha', 'usuarioSeleccionado'));
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

    /* EGRESOS */
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