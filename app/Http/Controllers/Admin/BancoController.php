<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Detallerecibo;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;
use App\Models\CuentasBancos;

class BancoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() { 
        $this->middleware ('can:admin.empresas.index')->only('index');
    }

/* public function index() 
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
    ->select(
        'detallerecibos.id',
        'detallerecibos.programacionid',
        'detallerecibos.fechaatencion',
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
        DB::raw("DATE(detallerecibos.created_at) as fecha"),
        DB::raw("SUM(detallerecibos.montototal + detallerecibos.descuento) as total_recibido"),
        'cajacentral.tipotransaccion'
    )
    ->whereIn('detallerecibos.area', ['MEDICA', 'INFORME FINAL'])
    ->where('detallerecibos.tipomovimiento', 'INGRESO')
    ->where('detallerecibos.estado', '!=', 'ANULADO')
    ->groupBy(
        'detallerecibos.id',
        'detallerecibos.programacionid',
        'detallerecibos.fechaatencion',
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
        DB::raw("DATE(detallerecibos.created_at)"),
        'cajacentral.tipotransaccion'
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


    // Consulta para resumen agrupado (ya lo tienes)
    $programacionesMes = DB::table('programacionsubclientes')
        ->select(
            DB::raw("DATE_FORMAT(fechaasignada, '%Y-%m') as mes"),
            DB::raw("SUM(precio) as total_programado")
        )
        ->where('proveedornombre', '!=', 'PROVEEDOR AJENO')
        ->where('pagoservicio', '=', 'INTERNO')
        ->groupBy(DB::raw("DATE_FORMAT(fechaasignada, '%Y-%m')"))
        ->orderBy(DB::raw("DATE_FORMAT(fechaasignada, '%Y-%m')"), 'desc')
        ->get();

    $ingresosMes = DB::table('detallerecibos')
        ->join('cajacentral', 'detallerecibos.reciboid', '=', 'cajacentral.nrorecibo')
        ->select(
            DB::raw("DATE_FORMAT(detallerecibos.created_at, '%Y-%m') as mes"),
            DB::raw("SUM(detallerecibos.montototal + detallerecibos.descuento) as total_ingresado")
        )
        ->whereIn('detallerecibos.area', ['MEDICA', 'INFORME FINAL'])
        ->where('detallerecibos.tipomovimiento', 'INGRESO')
        ->where('detallerecibos.estado', '!=', 'ANULADO')
        ->groupBy(DB::raw("DATE_FORMAT(detallerecibos.created_at, '%Y-%m')"))
        ->orderBy(DB::raw("DATE_FORMAT(detallerecibos.created_at, '%Y-%m')"), 'desc')
        ->get();

        // Consulta de ingresos de "INFORME FINAL" agrupados por mes
        $ingresosInformeFinalMes = DB::table('detallerecibos')
        ->select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"),
            DB::raw("SUM(montototal) as total_informe_final")
        )
        ->where('area', 'INFORME FINAL')
        ->where('tipomovimiento', 'INGRESO')
        ->where('estado', '!=', 'ANULADO')
        ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
        ->orderBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), 'desc')
        ->get();


    $consolidadoMes = [];
    foreach ($programacionesMes as $prog) {
        $consolidadoMes[$prog->mes] = [
            'total_programado' => (float) $prog->total_programado,
            'total_ingresado'  => 0,
            'total_informe_final' => 0
        ];
    }
    foreach ($ingresosMes as $ing) {
        if (isset($consolidadoMes[$ing->mes])) {
            $consolidadoMes[$ing->mes]['total_ingresado'] = (float) $ing->total_ingresado;
        } else {
            $consolidadoMes[$ing->mes] = [
                'total_programado' => 0,
                'total_ingresado'  => (float) $ing->total_ingresado
            ];
        }
    }
    foreach ($ingresosInformeFinalMes as $ingInforme) {
        if (isset($consolidadoMes[$ingInforme->mes])) {
            $consolidadoMes[$ingInforme->mes]['total_informe_final'] = (float) $ingInforme->total_informe_final;
        } else {
            $consolidadoMes[$ingInforme->mes] = [
                'total_programado' => 0,
                'total_ingresado' => 0,
                'total_informe_final' => (float) $ingInforme->total_informe_final
            ];
        }
    }
    
    ksort($consolidadoMes);
    $meses = array_reverse(array_keys($consolidadoMes));

    // Consultas para obtener los detalles sin agrupar
    $detallesProgramaciones = DB::table('programacionsubclientes')
        ->select('id', 'fechaasignada', 'precio', 'accionnombre', 'proveedornombre', 
                 'clienteitaid', 'clienteitanombre', 'clienteauditoriaid', 'clienteauditorianombre', 
                 'clientecomunid', 'clientecomunnombre', 'usuarioregistro',
                 DB::raw("(SELECT DATE(created_at) FROM detallerecibos 
                           WHERE programacionid = programacionsubclientes.id 
                           ORDER BY created_at DESC LIMIT 1) as fechapago")
        )
        ->where('proveedornombre', '!=', 'PROVEEDOR AJENO')
        ->where('pagoservicio', '=', 'INTERNO')
        ->get();

    $detallesIngresos = DB::table('detallerecibos')
        ->join('cajacentral', 'detallerecibos.reciboid', '=', 'cajacentral.nrorecibo')
        ->select(
            'detallerecibos.id',
            'detallerecibos.programacionid',
            'detallerecibos.fechaatencion',
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
            DB::raw("DATE(detallerecibos.created_at) as fecha"),
            'cajacentral.tipotransaccion'
        )
        ->whereIn('detallerecibos.area', ['MEDICA', 'INFORME FINAL'])
        ->where('detallerecibos.tipomovimiento', 'INGRESO')
        ->where('detallerecibos.estado', '!=', 'ANULADO')
        ->get();

    // Se agrupan los detalles por mes
    $detallesProgPorMes = [];
    foreach ($detallesProgramaciones as $prog) {
        $mesProg = date('Y-m', strtotime($prog->fechaasignada));
        $detallesProgPorMes[$mesProg][] = $prog;
    }

    $detallesIngPorMes = [];
    foreach ($detallesIngresos as $ing) {
        $mesIng = date('Y-m', strtotime($ing->fecha));
        $detallesIngPorMes[$mesIng][] = $ing;
    }


    return view('admin.banco.index', compact('consolidacion','programaciones', 'recibos','consolidadoMes', 'programacionesMes', 'ingresosMes', 'meses',
    'detallesProgPorMes',
    'detallesIngPorMes'));
} */

public function index(Request $request) 
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
        'cajacentral.diferenciafavor as cajacentral_diferenciafavor',
        'cajacentral.diferenciacontra as cajacentral_diferenciacontra',

        //NUEVO 260326
        DB::raw("
            CASE 
                WHEN cajacentral.tipocliente = 'ITA' THEN clientes.sucursal
                WHEN cajacentral.tipocliente = 'AUDITORIA' THEN clienteauditorias.sucursal
                WHEN cajacentral.tipocliente = 'COMUN' THEN clientescomunes.sucursal
                WHEN cajacentral.tipocliente = 'MEDICO' THEN proveedores.ciudad
                WHEN cajacentral.tipocliente = 'PROVEEDOR DE SERVICIO' THEN proveedoresservicios.ciudad
                ELSE NULL
            END as sucursal_origen
        "),
    )
    ->leftJoin('depositosbancarios', function($join) {
        $join->on(DB::raw('DATE(detallerecibos.created_at)'), '=', 'depositosbancarios.fecha')
             ->on('detallerecibos.usuarioregistronombre', '=', 'depositosbancarios.usuarioregistronombre');
    })
    ->leftJoin('cajacentral', 'detallerecibos.reciboid', '=', 'cajacentral.nrorecibo')
    
    //NUEVO 260326
    ->leftJoin('clientes', function($join) {
        $join->on('cajacentral.clienteid', '=', 'clientes.id')
            ->where('cajacentral.tipocliente', '=', 'ITA');
    })
    ->leftJoin('clienteauditorias', function($join) {
        $join->on('cajacentral.clienteid', '=', 'clienteauditorias.id')
            ->where('cajacentral.tipocliente', '=', 'AUDITORIA');
    })
    ->leftJoin('clientescomunes', function($join) {
        $join->on('cajacentral.clienteid', '=', 'clientescomunes.id')
            ->where('cajacentral.tipocliente', '=', 'COMUN');
    })
    ->leftJoin('proveedores', function($join) {
        $join->on('cajacentral.proveedorid', '=', 'proveedores.id')
            ->where('cajacentral.tipocliente', '=', 'MEDICO');
    })
    ->leftJoin('proveedoresservicios', function($join) {
        $join->on('cajacentral.proveedorid', '=', 'proveedoresservicios.id')
            ->where('cajacentral.tipocliente', '=', 'PROVEEDOR DE SERVICIO');
    })
    ->addSelect('proveedores.ciudad2 as proveedores_ciudad2')
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
    $query->orderBy('detallerecibos.id', 'asc');
    $detalles = $query->simplePaginate(500);
    /* $totalMontototal = $detalles->sum('montototal'); */
    $totalMontototal = $detalles->sum(function ($detalle) {
        return $detalle->montototal - ($detalle->descuentoatc ?? 0);
    });
    $cuentas = CuentasBancos::where('estado', 'ACTIVO')->get();

    return view('admin.banco.index', compact('cuentas','consolidacion','programaciones', 'recibos','detalles', 'totalMontototal', 'proveedores'));
}

public function uploadexcel(Request $request) 
{
    // Validar archivo
    $request->validate([
        'archivo' => 'required|file|mimes:csv,txt'
    ]);

    // Guardar archivo
    $archivo = $request->file('archivo');
    $ruta = $archivo->storeAs('uploads', time().'_'.$archivo->getClientOriginalName());
    $rutaCompleta = storage_path('app/' . $ruta);

    // Definir qué columnas deseas extraer (basado en índices de un CSV)
    $indicesDeseados = [0,1,2,3,4,5,6,7,8,9,10];

    // Variables para almacenar datos
    $datos = [];
    $total = 0;
    $encabezados = [];

    if (($gestor = fopen($rutaCompleta, "r")) !== false) {
        // Leer encabezados
        $encabezados = fgetcsv($gestor, 1000, ";"); 

        // Leer filas del CSV
        while (($fila = fgetcsv($gestor, 1000, ";")) !== false) {
            $filaFiltrada = [];
            foreach ($indicesDeseados as $indice) {
                $filaFiltrada[] = isset($fila[$indice]) ? $fila[$indice] : null;
            }

            // Sumar valores de la columna 8 si la columna 4 es "Abono Cta por ACH"
            if (isset($fila[3]) && trim($fila[3]) === "Abono Cta por ACH") {
                $total += isset($fila[8]) ? floatval(str_replace(',', '', $fila[8])) : 0;

            }

            $datos[] = $filaFiltrada;
        }
        fclose($gestor);
    }



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
    ->select(
        'detallerecibos.id',
        'detallerecibos.programacionid',
        'detallerecibos.fechaatencion',
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
        DB::raw("DATE(detallerecibos.created_at) as fecha"),
        DB::raw("SUM(detallerecibos.montototal + detallerecibos.descuento) as total_recibido"),
        'cajacentral.tipotransaccion'
    )
    ->whereIn('detallerecibos.area', ['MEDICA', 'INFORME FINAL'])
    ->where('detallerecibos.tipomovimiento', 'INGRESO')
    ->where('detallerecibos.estado', '!=', 'ANULADO')
    ->groupBy(
        'detallerecibos.id',
        'detallerecibos.programacionid',
        'detallerecibos.fechaatencion',
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
        DB::raw("DATE(detallerecibos.created_at)"),
        'cajacentral.tipotransaccion'
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
    )
    ->leftJoin('depositosbancarios', function($join) {
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
    /* $totalMontototal = $detalles->sum('montototal'); */
    $totalMontototal = $detalles->sum(function ($detalle) {
        return $detalle->montototal - ($detalle->descuentoatc ?? 0);
    });

    // Retornar vista con los datos y el total calculado
    return view('admin.banco.index', compact('datos', 'encabezados', 'total','consolidacion','programaciones', 'recibos','detalles', 'totalMontototal', 'proveedores'));
}

/* public function consolidadoegresos() 
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
        ->select('id', 'programacionid', 'fechaatencion', 'clienteid', 'clientenombre', 'detalle', 'proveedoratencion', 'montototal', 'usuarioregistronombre', 'provinfofinalid', 'reciboid', 'subtotal', 'descuento', 'area', DB::raw("DATE(created_at) as fecha"), DB::raw("SUM(montototal + descuento) as total_recibido"))
        ->whereIn('area', ['MEDICA', 'INFORME FINAL']) 
        ->where('tipomovimiento', 'EGRESO')
        ->where('estado','!=' ,'ANULADO')
        ->groupBy('id', 'programacionid', 'fechaatencion', 'clienteid', 'clientenombre', 'detalle', 'proveedoratencion', 'montototal', 'usuarioregistronombre', 'provinfofinalid', 'reciboid', 'subtotal', 'descuento', 'area', DB::raw("DATE(created_at)"))
        ->orderBy(DB::raw("DATE(created_at)"), 'desc')
    ->get();

    
    $consolidacion = [];
    foreach ($programaciones as $prog) {
       

        $consolidacion[] = (object)[
            'id'                     => $prog->id,
            'fechaasignada'          => $prog->fechaasignada,
            'total_programado'       => number_format($prog->total_programado, 2, '.', ''),
            
        ];
    }

    return view('admin.banco.consolidadoegresos', compact('consolidacion','programaciones', 'recibos'));
} */
public function consolidadoegresos() 
{
    $programaciones = DB::table('cuentasporpagar')
        ->select('id',
        'proveedorid',
        'proveedornombre',
        'tipoproveedorservicio',
        'detalleproducto',
        'fechaasignada',
        'subtotal',
        'descuento',
        'montototal',
        'preciocompra',
        'tipoorden',
        'ordenid',
        'estado',
        'usuarioregistroid',
        'usuarioregistronombre',
        'fechacomprar',
        'cantidad',
        'detalleordenid',
            DB::raw("SUM(montototal) as total_programado"),
            DB::raw("(SELECT DATE(created_at) FROM detallerecibos WHERE cuentapagarid = cuentasporpagar.id ORDER BY created_at DESC LIMIT 1) as fechapago"))
        ->groupBy('id',
        'proveedorid',
        'proveedornombre',
        'tipoproveedorservicio',
        'detalleproducto',
        'fechaasignada',
        'subtotal',
        'descuento',
        'montototal',
        'preciocompra',
        'tipoorden',
        'ordenid',
        'estado',
        'usuarioregistroid',
        'usuarioregistronombre',
        'fechacomprar',
        'cantidad',
        'detalleordenid',)
        ->orderBy('fechaasignada', 'desc')
    ->get();

    $recibos = DB::table('detallerecibos')
        ->select('id',
        'reciboid',
        'area',
        'detalle',
        'subtotal',
        'descuento',
        'montototal',
        'saldo',
        'precioprogramado',
        'usuarioregistroid',
        'usuarioregistronombre',
        'programacionid',
        'fechabateria',
        'servicio',
        'proveedoratencion',
        'fechaatencion',
        'estado',
        'provinfofinalid',
        'tipomovimiento',
        'cuentapagarid',
        'bateriaid',
        'clienteid',
        'clientenombre',
        'ordenid', DB::raw("DATE(created_at) as fecha"), DB::raw("SUM(montototal + descuento) as total_recibido"))
        ->where('area', 'CUENTAS POR PAGAR') 
        ->where('tipomovimiento', 'EGRESO')
        ->where('estado','!=' ,'ANULADO')
        ->groupBy('id',
        'reciboid',
        'area',
        'detalle',
        'subtotal',
        'descuento',
        'montototal',
        'saldo',
        'precioprogramado',
        'usuarioregistroid',
        'usuarioregistronombre',
        'programacionid',
        'fechabateria',
        'servicio',
        'proveedoratencion',
        'fechaatencion',
        'estado',
        'provinfofinalid',
        'tipomovimiento',
        'cuentapagarid',
        'bateriaid',
        'clienteid',
        'clientenombre',
        'ordenid', DB::raw("DATE(created_at)"))
        ->orderBy(DB::raw("DATE(created_at)"), 'desc')
    ->get();

    
    $consolidacion = [];
    foreach ($programaciones as $prog) {
       

        $consolidacion[] = (object)[
            'id'                     => $prog->id,
            'fechaasignada'          => $prog->fechaasignada,
            'total_programado'       => number_format($prog->total_programado, 2, '.', ''),
            
        ];
    }

    return view('admin.banco.consolidadoegresos', compact('consolidacion','programaciones', 'recibos'));
}

    public function montototalbancos() 
    {
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

        $totalCuenta3Egreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '4011113557')
                ->orWhere(function ($subquery) {
                    $subquery->where('tipotransaccion', 'CHEQUE')
                                ->where('nrocuentadestinocheque', '4011113557')
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



        /* REGISTROS DE DETALLE RECIBO DE 3000189269 */
        $ingresoscuenta1 = DB::table('detallerecibos')
            ->join('cajacentral', 'detallerecibos.reciboid', '=', 'cajacentral.nrorecibo')
            ->leftJoin('depositosbancarios', function($join) {
                $join->on(DB::raw('DATE(detallerecibos.created_at)'), '=', 'depositosbancarios.fecha')
                    ->on('detallerecibos.usuarioregistronombre', '=', 'depositosbancarios.usuarioregistronombre');
            })
            ->selectRaw("
                detallerecibos.*, 
                cajacentral.tipotransaccion, 
                cajacentral.updated_at as cajacentral_updated_at, 
                cajacentral.fechabancarizacionatc as cajacentral_fechabancarizacionatc, 
                depositosbancarios.created_at AS depositosbancarios_created_at, 
                CASE 
                    WHEN cajacentral.tipotransaccion = 'ATC' AND cajacentral.nrobancarizacionatc IS NOT NULL 
                    THEN cajacentral.montototal - cajacentral.descuentoATC 
                    ELSE cajacentral.montototal 
                END as montototal_calculado
            ")
            ->where(function ($query) {
                $query->where('cajacentral.nrocuentadestinotransferencia', '3000189269')
                    ->orWhere('cajacentral.nrocuentadestinodeposito', '3000189269')
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.nrobancodestinoefectivo', '3000189269')
                                ->whereNotNull('cajacentral.nrobancarizacionefectivo');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.tipotransaccion', 'ATC')
                                ->where('cajacentral.nrocuentadestinoatc', '3000189269')
                                ->whereNotNull('cajacentral.nrobancarizacionatc');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.tipotransaccion', 'CHEQUE')
                                ->where('cajacentral.nrocuentadestinocheque', '3000189269')
                                ->whereNotNull('cajacentral.nrobancarizacioncheque');
                    });
            })
            ->where('detallerecibos.tipomovimiento', 'INGRESO')
            ->where('detallerecibos.estado', '!=', 'ANULADO')
            ->orderBy('detallerecibos.id', 'asc')
        ->get();

        $egresoscuenta1 = DB::table('detallerecibos')
            ->join('cajacentral', 'detallerecibos.reciboid', '=', 'cajacentral.nrorecibo')
            ->select('detallerecibos.*', 'cajacentral.tipotransaccion')
            ->where(function ($query) {
                $query->where('cajacentral.nrocuentadestinotransferencia', '3000189269')
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.tipotransaccion', 'CHEQUE')
                                ->where('cajacentral.nrocuentadestinocheque', '3000189269')
                                ->whereNotNull('nrobancarizacioncheque');
                    });
            })
            ->where('detallerecibos.tipomovimiento', 'EGRESO')
            ->where('detallerecibos.estado', '!=', 'ANULADO')
            ->orderBy('detallerecibos.id', 'asc')
        ->get();

        /* REGISTROS DE DETALLE RECIBO DE 2505314878 */
        $ingresoscuenta2 = DB::table('detallerecibos')
            ->join('cajacentral', 'detallerecibos.reciboid', '=', 'cajacentral.nrorecibo')
            ->leftJoin('depositosbancarios', function($join) {
                $join->on(DB::raw('DATE(detallerecibos.created_at)'), '=', 'depositosbancarios.fecha')
                    ->on('detallerecibos.usuarioregistronombre', '=', 'depositosbancarios.usuarioregistronombre');
            })
            ->selectRaw("
                detallerecibos.*, 
                cajacentral.tipotransaccion, 
                cajacentral.updated_at as cajacentral_updated_at, 
                cajacentral.fechabancarizacionatc as cajacentral_fechabancarizacionatc, 
                depositosbancarios.created_at AS depositosbancarios_created_at, 
                CASE 
                    WHEN cajacentral.tipotransaccion = 'ATC' AND cajacentral.nrobancarizacionatc IS NOT NULL 
                    THEN cajacentral.montototal - cajacentral.descuentoATC 
                    ELSE cajacentral.montototal 
                END as montototal_calculado
            ")
            ->where(function ($query) {
                $query->where('cajacentral.nrocuentadestinotransferencia', '2505314878')
                    ->orWhere('cajacentral.nrocuentadestinodeposito', '2505314878')
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.nrobancodestinoefectivo', '2505314878')
                                ->whereNotNull('cajacentral.nrobancarizacionefectivo');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.tipotransaccion', 'ATC')
                                ->where('cajacentral.nrocuentadestinoatc', '2505314878')
                                ->whereNotNull('cajacentral.nrobancarizacionatc');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.tipotransaccion', 'CHEQUE')
                                ->where('cajacentral.nrocuentadestinocheque', '2505314878')
                                ->whereNotNull('cajacentral.nrobancarizacioncheque');
                    });
            })
            ->where('detallerecibos.tipomovimiento', 'INGRESO')
            ->where('detallerecibos.estado', '!=', 'ANULADO')
            ->orderBy('detallerecibos.id', 'asc')
        ->get();

        $egresoscuenta2 = DB::table('detallerecibos')
            ->join('cajacentral', 'detallerecibos.reciboid', '=', 'cajacentral.nrorecibo')
            ->select('detallerecibos.*', 'cajacentral.tipotransaccion')
            ->where(function ($query) {
                $query->where('cajacentral.nrocuentadestinotransferencia', '2505314878')
                ->orWhere(function ($subquery) {
                    $subquery->where('cajacentral.tipotransaccion', 'CHEQUE')
                            ->where('cajacentral.nrocuentadestinocheque', '2505314878')
                            ->whereNotNull('nrobancarizacioncheque');
                });
            })
            ->where('detallerecibos.tipomovimiento', 'EGRESO')
            ->where('detallerecibos.estado', '!=', 'ANULADO')
            ->orderBy('detallerecibos.id', 'asc')
        ->get();

        /* REGISTROS DE DETALLE RECIBO DE 4011113557 */
        $ingresoscuenta3 = DB::table('detallerecibos')
            ->join('cajacentral', 'detallerecibos.reciboid', '=', 'cajacentral.nrorecibo')
            ->leftJoin('depositosbancarios', function($join) {
                $join->on(DB::raw('DATE(detallerecibos.created_at)'), '=', 'depositosbancarios.fecha')
                    ->on('detallerecibos.usuarioregistronombre', '=', 'depositosbancarios.usuarioregistronombre');
            })
            ->selectRaw("
                detallerecibos.*, 
                cajacentral.tipotransaccion, 
                cajacentral.updated_at as cajacentral_updated_at, 
                cajacentral.fechabancarizacionatc as cajacentral_fechabancarizacionatc, 
                depositosbancarios.created_at AS depositosbancarios_created_at, 
                CASE 
                    WHEN cajacentral.tipotransaccion = 'ATC' AND cajacentral.nrobancarizacionatc IS NOT NULL 
                    THEN cajacentral.montototal - cajacentral.descuentoATC 
                    ELSE cajacentral.montototal 
                END as montototal_calculado
            ")
            ->where(function ($query) {
                $query->where('cajacentral.nrocuentadestinotransferencia', '4011113557')
                    ->orWhere('cajacentral.nrocuentadestinodeposito', '4011113557')
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.nrobancodestinoefectivo', '4011113557')
                                ->whereNotNull('cajacentral.nrobancarizacionefectivo');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.tipotransaccion', 'ATC')
                                ->where('cajacentral.nrocuentadestinoatc', '4011113557')
                                ->whereNotNull('cajacentral.nrobancarizacionatc');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.tipotransaccion', 'CHEQUE')
                                ->where('cajacentral.nrocuentadestinocheque', '4011113557')
                                ->whereNotNull('cajacentral.nrobancarizacioncheque');
                    });
            })
            ->where('detallerecibos.tipomovimiento', 'INGRESO')
            ->where('detallerecibos.estado', '!=', 'ANULADO')
            ->orderBy('detallerecibos.id', 'asc')
        ->get();

        $egresoscuenta3 = DB::table('detallerecibos')
            ->join('cajacentral', 'detallerecibos.reciboid', '=', 'cajacentral.nrorecibo')
            ->select('detallerecibos.*', 'cajacentral.tipotransaccion')
            ->where(function ($query) {
                $query->where('cajacentral.nrocuentadestinotransferencia', '4011113557')
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.tipotransaccion', 'CHEQUE')
                                ->where('cajacentral.nrocuentadestinocheque', '4011113557')
                                ->whereNotNull('nrobancarizacioncheque');
                    });
            })
            ->where('detallerecibos.tipomovimiento', 'EGRESO')
            ->where('detallerecibos.estado', '!=', 'ANULADO')
            ->orderBy('detallerecibos.id', 'asc')
        ->get();

        /* REGISTROS DE DETALLE RECIBO DE 1031266712 */
        $ingresoscuenta4 = DB::table('detallerecibos')
            ->join('cajacentral', 'detallerecibos.reciboid', '=', 'cajacentral.nrorecibo')
            ->leftJoin('depositosbancarios', function($join) {
                $join->on(DB::raw('DATE(detallerecibos.created_at)'), '=', 'depositosbancarios.fecha')
                    ->on('detallerecibos.usuarioregistronombre', '=', 'depositosbancarios.usuarioregistronombre');
            })
            ->selectRaw("
                detallerecibos.*, 
                cajacentral.tipotransaccion, 
                cajacentral.updated_at as cajacentral_updated_at, 
                cajacentral.fechabancarizacionatc as cajacentral_fechabancarizacionatc, 
                depositosbancarios.created_at AS depositosbancarios_created_at, 
                CASE 
                    WHEN cajacentral.tipotransaccion = 'ATC' AND cajacentral.nrobancarizacionatc IS NOT NULL 
                    THEN cajacentral.montototal - cajacentral.descuentoATC 
                    ELSE cajacentral.montototal 
                END as montototal_calculado
            ")
            ->where(function ($query) {
                $query->where('cajacentral.nrocuentadestinotransferencia', '1031266712')
                    ->orWhere('cajacentral.nrocuentadestinodeposito', '1031266712')
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.nrobancodestinoefectivo', '1031266712')
                                ->whereNotNull('cajacentral.nrobancarizacionefectivo');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.tipotransaccion', 'ATC')
                                ->where('cajacentral.nrocuentadestinoatc', '1031266712')
                                ->whereNotNull('cajacentral.nrobancarizacionatc');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.tipotransaccion', 'CHEQUE')
                                ->where('cajacentral.nrocuentadestinocheque', '1031266712')
                                ->whereNotNull('cajacentral.nrobancarizacioncheque');
                    });
            })
            ->where('detallerecibos.tipomovimiento', 'INGRESO')
            ->where('detallerecibos.estado', '!=', 'ANULADO')
            ->orderBy('detallerecibos.id', 'asc')
        ->get();

        $egresoscuenta4 = DB::table('detallerecibos')
            ->join('cajacentral', 'detallerecibos.reciboid', '=', 'cajacentral.nrorecibo')
            ->select('detallerecibos.*', 'cajacentral.tipotransaccion')
            ->where(function ($query) {
                $query->where('cajacentral.nrocuentadestinotransferencia', '1031266712')
                    ->orWhere(function ($subquery) {
                        $subquery->where('cajacentral.tipotransaccion', 'CHEQUE')
                                ->where('cajacentral.nrocuentadestinocheque', '1031266712')
                                ->whereNotNull('nrobancarizacioncheque');
                    });
            })
            ->where('detallerecibos.tipomovimiento', 'EGRESO')
            ->where('detallerecibos.estado', '!=', 'ANULADO')
            ->orderBy('detallerecibos.id', 'asc')
        ->get();

        $saldoanteriorcuenta1 = '-174910.55';
        $saldoanteriorcuenta2 = '34508.22';
        $saldoanteriorcuenta3 = '3500.00';
        $saldoanteriorcuenta4 = '0.00';

        return view('admin.banco.montototalbancos', compact('saldoanteriorcuenta4','saldoanteriorcuenta1',
        'saldoanteriorcuenta2','saldoanteriorcuenta3','totalCuenta3Ingreso','totalCuenta3Egreso','ingresoscuenta3',
        'egresoscuenta3','ingresoscuenta1','egresoscuenta1','ingresoscuenta2','egresoscuenta2','totalCuenta1Ingreso',
        'totalCuenta2Ingreso','totalCuenta1Egreso','totalCuenta2Egreso','totalCuenta4Ingreso','totalCuenta4Egreso',
        'ingresoscuenta4','egresoscuenta4'));
    }


public function detallemovimientos(Request $request)
{
    // Obtener los filtros desde la solicitud
    $filtroTransaccion = $request->input('tipotransaccion');
    $filtroProveedor    = $request->input('proveedoratencion');

    // Lista de valores únicos para llenar los selects
    $listaTransacciones = DB::table('detallerecibos')
        ->select('tipotransaccion')
        ->distinct()
        ->where('estado', '!=', 'ANULADO') // Excluir los registros con estado 'Anulado'
        ->pluck('tipotransaccion');
        
    $listaProveedores = DB::table('detallerecibos')
        ->select('proveedoratencion')
        ->distinct()
        ->where('estado', '!=', 'ANULADO') // Excluir los registros con estado 'Anulado'
        ->pluck('proveedoratencion');

    // Agrupación por tipotransaccion
    $queryTransacciones = DB::table('detallerecibos')
        ->select(
            'tipotransaccion',
            DB::raw("SUM(CASE WHEN tipomovimiento = 'INGRESO' THEN montototal ELSE 0 END) as total_ingreso"),
            DB::raw("SUM(CASE WHEN tipomovimiento = 'EGRESO' THEN montototal ELSE 0 END) as total_egreso")
        )
        ->where('estado', '!=', 'ANULADO'); // Excluir los registros con estado 'Anulado'

    // Si se filtra por tipo de transacción, se aplica el where
    if (!empty($filtroTransaccion)) {
        $queryTransacciones->where('tipotransaccion', $filtroTransaccion);
    }
    $transacciones = $queryTransacciones->groupBy('tipotransaccion')->get();

    // Agrupación por proveedornombre
    $queryProveedores = DB::table('detallerecibos')
        ->select(
            'proveedoratencion',
            DB::raw("SUM(CASE WHEN tipomovimiento = 'INGRESO' THEN montototal ELSE 0 END) as total_ingreso"),
            DB::raw("SUM(CASE WHEN tipomovimiento = 'EGRESO' THEN montototal ELSE 0 END) as total_egreso")
        )
        ->where('estado', '!=', 'ANULADO'); // Excluir los registros con estado 'Anulado'

    // Si se filtra por proveedor, se aplica el where
    if (!empty($filtroProveedor)) {
        $queryProveedores->where('proveedoratencion', $filtroProveedor);
    }
    $proveedores = $queryProveedores->groupBy('proveedoratencion')->get();
        
    return view('admin.banco.detallemovimientos', compact(
        'listaTransacciones', 
        'listaProveedores', 
        'transacciones', 
        'proveedores',
        'filtroTransaccion',
        'filtroProveedor'
    ));
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.banco.create');
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

        return redirect()->route('admin.banco.index', $empresa)->with('info', 'La empresa se creó con exito');
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
        return view('admin.banco.edit', compact('empresa'));
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

        return redirect()->route('admin.banco.index', $empresa)->with('info', 'La empresa se actualizó con éxito');
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

        return redirect()->route('admin.banco.index', $empresa)->with('eliminar', 'ok');
    }
    
    
}
