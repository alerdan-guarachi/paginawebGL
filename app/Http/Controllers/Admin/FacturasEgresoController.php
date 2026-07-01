<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Empresa;
use App\Models\FacturasEgreso;
use App\Models\SaldoCreditoFiscal;
use App\Models\FormularioImpuestos;
use App\Models\Proveedoresservicios;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PermisoCodigo;

class FacturasEgresoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() { 
        $this->middleware ('can:admin.facturasegreso.index')->only('index');
    }

    public function index(Request $request)
    {
        $fechaDesde = $request->input('fechaDesde');
        $fechaHasta = $request->input('fechaHasta');

        $ventas = collect();
        $compras = collect();

        $totales = [
            'totalVentas' => 0,
            'ivaDebito' => 0,
            'itVentas' => 0,
            'totalCompras' => 0,
            'ivaCredito' => 0,
            'diferencia' => 0
        ];

        if ($fechaDesde && $fechaHasta) {
            $ventas = FacturasEgreso::where('tipo', 2)
                ->whereBetween('fechafacturaduidim', [$fechaDesde, $fechaHasta])
                ->where('estado', 'VALIDO')
                ->get()
                ->groupBy('ciudad');

            $compras = FacturasEgreso::where('tipo', 1)
                ->whereBetween('fechafacturaduidim', [$fechaDesde, $fechaHasta])
                ->where('estado', 'VALIDO')
                ->get()
                ->groupBy('ciudad');

            // Calcular totales
            foreach ($ventas as $registros) {
                $total = $registros->sum('importebasecfdf');
                $cdfiscal = $registros->sum('creditodebitofiscal');
                $totales['totalVentas'] += $total;
                $totales['ivaDebito'] += /* $total *  */$cdfiscal;
                $totales['itVentas'] += $total * 0.03;
            }

            foreach ($compras as $registros) {
                $total = $registros->sum('importebasecfdf');
                $cdfiscal = $registros->sum('creditodebitofiscal');
                $totales['totalCompras'] += $total;
                $totales['ivaCredito'] += /* $total *  */$cdfiscal;
            }

            // Diferencia final
            $totales['diferencia'] = $totales['ivaDebito'] + $totales['itVentas'] - $totales['ivaCredito'];
        }

        $ventasscz = collect();
        $comprasscz = collect();
        $ventascbba = collect();
        $comprascbba = collect();

        //NUEVO 190126
        if ($fechaDesde && $fechaHasta) {

            $order = 'COALESCE(CAST(idfactura AS UNSIGNED), id)';

            $ventasscz = FacturasEgreso::where('tipo', 2)
                ->whereBetween('fechafacturaduidim', [$fechaDesde, $fechaHasta])
                ->where('estado', 'VALIDO')
                ->where('ciudad', 'SANTA CRUZ')
                ->orderByRaw($order)
                ->get();

            $comprasscz = FacturasEgreso::where('tipo', 1)
                ->whereBetween('fechafacturaduidim', [$fechaDesde, $fechaHasta])
                ->where('estado', 'VALIDO')
                ->where('ciudad', 'SANTA CRUZ')
                ->orderByRaw($order)
                ->get();

            $ventascbba = FacturasEgreso::where('tipo', 2)
                ->whereBetween('fechafacturaduidim', [$fechaDesde, $fechaHasta])
                ->where('estado', 'VALIDO')
                ->where('ciudad', 'COCHABAMBA')
                ->orderByRaw($order)
                ->get();

            $comprascbba = FacturasEgreso::where('tipo', 1)
                ->whereBetween('fechafacturaduidim', [$fechaDesde, $fechaHasta])
                ->where('estado', 'VALIDO')
                ->where('ciudad', 'COCHABAMBA')
                ->orderByRaw($order)
                ->get();
        }

        $registrosImpuestos = FormularioImpuestos::latest()->get();
        $periodo = now()->subMonth()->format('Y-m');
        $nombresExistentes = FormularioImpuestos::where('periodo', $periodo)->pluck('nombre')->toArray();

        /* $usuariosEntrega = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR EXTERNO', 'PROVEEDOR INTERNO'])
        ->orderBy('razonsocial')
        ->get(); */
        $usuariosEntrega = Proveedoresservicios::where(function ($query) {
            $query->whereIn('categoria', ['PROVEEDOR EXTERNO', 'PROVEEDOR INTERNO'])
                ->orWhere('id', '1PS');
        })
        ->orderBy('razonsocial')
        ->get();

        $razonesSociales1 = Proveedor::pluck('proveedor')->toArray();
        $razonesSociales2 = ProveedoresServicios::pluck('razonsocial')->toArray();

        $razonesSociales = array_unique(array_merge($razonesSociales1, $razonesSociales2));
        sort($razonesSociales, SORT_NATURAL | SORT_FLAG_CASE);


        $otrasventasscz = FacturasEgreso::where('tipo', 2)
            ->whereIn('estado', ['ANULADO', 'PENDIENTE', 'RECHAZADO'])
            ->where('ciudad', 'SANTA CRUZ')
            ->get();

        $otrascomprasscz = FacturasEgreso::where('tipo', 1)
            ->whereIn('estado', ['ANULADO', 'PENDIENTE', 'RECHAZADO'])
            ->where('ciudad', 'SANTA CRUZ')
            ->get();

        $otrasventascbba = FacturasEgreso::where('tipo', 2)
            ->whereIn('estado', ['ANULADO', 'PENDIENTE', 'RECHAZADO'])
            ->where('ciudad', 'COCHABAMBA')
            ->get();

        $otrascomprascbba = FacturasEgreso::where('tipo', 1)
            ->whereIn('estado', ['ANULADO', 'PENDIENTE', 'RECHAZADO'])
            ->where('ciudad', 'COCHABAMBA')
            ->get();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $codigosPermitidos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
            ->whereDate('fechaSolicitada', $hoy->toDateString())
            ->where('permisoSolicitado', 'admin.facturasegreso.cambiarrazonsocial')
            ->where('estado', 'expirado')
            ->pluck('clienteid')
        ->toArray();

        return view('admin.facturasegreso.index', compact('codigosPermitidos','razonesSociales','ventas', 'compras', 'fechaDesde', 
        'fechaHasta', 'totales', 'ventasscz', 'comprasscz', 'ventascbba', 'comprascbba', 'registrosImpuestos', 
        'nombresExistentes', 'usuariosEntrega','otrasventasscz','otrascomprasscz','otrasventascbba','otrascomprascbba'));
    }

    //NUEVO 190126
    public function cerrarMes(Request $request)
    {
        [$anio, $mes] = explode('-', $request->mes_anio);


        $fechaSeleccionada = Carbon::createFromDate($anio, $mes, 1);
        $mesAnterior = $fechaSeleccionada->copy()->subMonth();

        $facturasPendientesMesAnterior = DB::table('facturasegreso')
            ->whereYear('fechafacturaduidim', $mesAnterior->year)
            ->whereMonth('fechafacturaduidim', $mesAnterior->month)
            ->whereNull('idfactura')
            ->where('estado', 'VALIDO')
            ->whereNull('deleted_at')
            ->exists();

        if ($facturasPendientesMesAnterior) {

            Carbon::setLocale('es');

            $nombreMes = ucfirst($mesAnterior->translatedFormat('F'));

            return back()->with(
                'error',
                "No puede cerrar este mes porque falta realizar el cierre de {$nombreMes}."
            );
        }


        DB::transaction(function () use ($mes, $anio) {

            $ultimoIdFactura = DB::table('facturasegreso')
                ->whereNotNull('idfactura')
                ->whereNull('deleted_at')
                ->max('idfactura') ?? 0;

            $facturas = DB::table('facturasegreso')
                ->whereYear('fechafacturaduidim', $anio)
                ->whereMonth('fechafacturaduidim', $mes)
                ->whereNull('idfactura')
                ->where('estado', 'VALIDO')
                ->whereNull('deleted_at')
                ->orderBy('fechafacturaduidim')
                ->orderBy('id')
                ->get();

            $contador = $ultimoIdFactura + 1;

            foreach ($facturas as $factura) {
                DB::table('facturasegreso')
                    ->where('id', $factura->id)
                    ->update(['idfactura' => $contador]);

                $contador++;
            }
        });

        return back()->with('info', 'Cierre de mes realizado correctamente.');
    }
    public function codigocambiofacturacambiorsocial(Request $request)
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
    public function actualizarfacturaimpuestos(Request $request, $id)
    {
        $request->validate([
            'nitci' => '',
            'complemento' => '',
            'razonsocial' => '',
        ]);

        $producto = DB::table('facturasegreso')->where('id', $id)->first();

        $datosActualizados = [
            'nitci' => $request->nitci,
            'complemento' => $request->complemento,
            'razonsocial' => $request->razonsocial,
            'updated_at' => now(),
        ];

        DB::table('facturasegreso')
            ->where('id', $id)
            ->update($datosActualizados);

        return redirect()->back()->with('info', 'Stock actualizado correctamente.');
    }


    public function actualizarEstado(Request $request)
    {
        $ids = $request->input('seleccionados', []);
        $accion = $request->input('accion'); // 'pendiente' o 'rechazado'
        $motivo = $request->input('motivo');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un registro.');
        }

        foreach ($ids as $id) {
            DB::table('facturasegreso') // o usa el modelo FacturaEgreso::where(...)
                ->where('id', $id)
                ->update([
                    'estado' => $accion === 'pendiente' ? 'PENDIENTE' : 'RECHAZADO',
                    'motivo' => $motivo,
                ]);
        }

        return redirect()->back()->with('info', 'Registros actualizados correctamente.');
    }
    public function facturascontadorexterno(Request $request)
    {
        $fechaDesde = $request->input('fechaDesde');
        $fechaHasta = $request->input('fechaHasta');

        $ventas = collect();
        $compras = collect();

        $totales = [
            'totalVentas' => 0,
            'ivaDebito' => 0,
            'itVentas' => 0,
            'totalCompras' => 0,
            'ivaCredito' => 0,
            'diferencia' => 0
        ];

        if ($fechaDesde && $fechaHasta) {
            $ventas = FacturasEgreso::where('tipo', 2)
                ->whereBetween('fechafacturaduidim', [$fechaDesde, $fechaHasta])
                ->get()
                ->groupBy('ciudad');

            $compras = FacturasEgreso::where('tipo', 1)
                ->whereBetween('fechafacturaduidim', [$fechaDesde, $fechaHasta])
                ->get()
                ->groupBy('ciudad');

            // Calcular totales
            foreach ($ventas as $registros) {
                $total = $registros->sum('total');
                $totales['totalVentas'] += $total;
                $totales['ivaDebito'] += $total * 0.13;
                $totales['itVentas'] += $total * 0.03;
            }

            foreach ($compras as $registros) {
                $total = $registros->sum('total');
                $totales['totalCompras'] += $total;
                $totales['ivaCredito'] += $total * 0.13;
            }

            // Diferencia final
            $totales['diferencia'] = $totales['ivaDebito'] + $totales['itVentas'] - $totales['ivaCredito'];
        }

        $ventasscz = collect();
        $comprasscz = collect();
        $ventascbba = collect();
        $comprascbba = collect();

        if ($fechaDesde && $fechaHasta) {
            $ventasscz = FacturasEgreso::where('tipo', 2)
                ->whereBetween('fechafacturaduidim', [$fechaDesde, $fechaHasta])
                ->where('ciudad', 'SANTA CRUZ')
                ->get();

            $comprasscz = FacturasEgreso::where('tipo', 1)
                ->whereBetween('fechafacturaduidim', [$fechaDesde, $fechaHasta])
                ->where('ciudad', 'SANTA CRUZ')
                ->get();

            $ventascbba = FacturasEgreso::where('tipo', 2)
                ->whereBetween('fechafacturaduidim', [$fechaDesde, $fechaHasta])
                ->where('ciudad', 'COCHABAMBA')
                ->get();

            $comprascbba = FacturasEgreso::where('tipo', 1)
                ->whereBetween('fechafacturaduidim', [$fechaDesde, $fechaHasta])
                ->where('ciudad', 'COCHABAMBA')
                ->get();
        }

        $registrosImpuestos = FormularioImpuestos::latest()->get();
        $periodo = now()->subMonth()->format('Y-m');
        $nombresExistentes = FormularioImpuestos::where('periodo', $periodo)->pluck('nombre')->toArray();

        $usuariosEntrega = Proveedoresservicios::whereIn('categoria', ['PROVEEDOR EXTERNO', 'PROVEEDOR INTERNO'])
        ->orderBy('razonsocial')
        ->get();

        $nombresExistentes = FormularioImpuestos::where('periodo', $periodo)->pluck('nombre')->toArray();

        $formulariosCompletos = in_array('IMPUESTO AL VALOR AGREGADO', $nombresExistentes) &&
                                in_array('IMPUESTO A LAS TRANSACCIONES', $nombresExistentes);

        return view('admin.facturasegreso.facturascontadorexterno', compact('ventas', 'compras', 'fechaDesde', 'fechaHasta', 'totales', 'ventasscz', 'comprasscz', 'ventascbba', 'comprascbba', 'registrosImpuestos', 'nombresExistentes', 'usuariosEntrega','formulariosCompletos'));
    }
    public function guardarfacturacontaext(Request $request, FacturasEgreso $empresa)
    {
        $request->validate([
            'archivo_iva' => 'required|file|mimes:pdf|max:2048',
            'archivo_it' => 'required|file|mimes:pdf|max:2048',
        ]);

        $periodo = now()->subMonth()->format('Y-m');

        // Guardar o actualizar IVA
        $archivoIVA = $request->file('archivo_iva');
        $nombreArchivoIVA = time() . '_' . $archivoIVA->getClientOriginalName();
        $archivoIVA->move(public_path('formulariosimpuestos'), $nombreArchivoIVA);

        FormularioImpuestos::updateOrCreate(
            ['periodo' => $periodo, 'nombre' => 'IMPUESTO AL VALOR AGREGADO'],
            [
                'montocontador' => $request->monto_iva,
                'archivo' => $nombreArchivoIVA,
                'registrocontadorid' => auth()->id(),
                'registrocontadornombre' => auth()->user()->name,
                'fecharegistrocontador' => now(),
            ]
        );

        // Guardar o actualizar IT
        $archivoIT = $request->file('archivo_it');
        $nombreArchivoIT = time() . '_' . $archivoIT->getClientOriginalName();
        $archivoIT->move(public_path('formulariosimpuestos'), $nombreArchivoIT);

        FormularioImpuestos::updateOrCreate(
            ['periodo' => $periodo, 'nombre' => 'IMPUESTO A LAS TRANSACCIONES'],
            [
                'montocontador' => $request->monto_it,
                'archivo' => $nombreArchivoIT,
                'registrocontadorid' => auth()->id(),
                'registrocontadornombre' => auth()->user()->name,
                'fecharegistrocontador' => now(),
            ]
        );

        return redirect()->back()->with('info', 'Formularios guardados exitosamente.');
    }
    public function guardarsaldocreditofiscal(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric'
        ]);

        SaldoCreditofiscal::create([
            'saldo' => $request->monto,
            'usuarioregistroid' => Auth::id(),
            'usuarioregistronombre' => Auth::user()->name,
        ]);

        return response()->json(['success' => true]);
    }

    public function guardarAmbosFormularios(Request $request)
    {

        $periodo = now()->subMonth()->format('Y-m');

        // Verificar si ya existen ambos
        $existen = FormularioImpuestos::where('periodo', $periodo)->pluck('nombre')->toArray();
        if (in_array('IMPUESTO AL VALOR AGREGADO', $existen) && in_array('IMPUESTO A LAS TRANSACCIONES', $existen)) {
            return redirect()->back()->with('error', 'Ambos montos ya fueron registrados para el mes anterior.');
        }

        // Verificar si la fecha actual es mayor al 16
        /* if (now()->day > 16) {
            return redirect()->back()->with('error', 'El plazo para registrar estos motnos ha vencido (hasta el día 16).');
        } */

        // Guardar IVA
        if (!in_array('IMPUESTO AL VALOR AGREGADO', $existen)) {
            FormularioImpuestos::create([
                'nombre' => 'IMPUESTO AL VALOR AGREGADO',
                'periodo' => $periodo,
                'monto' => $request->monto_iva,
                'usuarioregistroid' => auth()->id(),
                'usuarioregistronombre' => auth()->user()->name,
            ]);
        }

        // Guardar IT
        if (!in_array('IMPUESTO A LAS TRANSACCIONES', $existen)) {
            FormularioImpuestos::create([
                'nombre' => 'IMPUESTO A LAS TRANSACCIONES',
                'periodo' => $periodo,
                'monto' => $request->monto_it,
                'usuarioregistroid' => auth()->id(),
                'usuarioregistronombre' => auth()->user()->name,
            ]);
        }

        return redirect()->back()->with('info', 'Montos guardados exitosamente.');
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
    public function store(Request $request, FacturasEgreso $empresa)
    {
        $request->merge([
            'otronosujcredfiscaloiva' => $request->otronosujcredfiscaloiva ?? 0.00,
            'tasas' => $request->tasas ?? 0.00
        ]);
        $factura = FacturasEgreso::create($request->all());

        return redirect()->route('admin.facturasegreso.index', $factura)->with('info', 'La factura se registro con exito');
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
