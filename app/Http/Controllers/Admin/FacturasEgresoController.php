<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Empresa;
use App\Models\FacturasEgreso;

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

        return view('admin.facturasegreso.index', compact('ventas', 'compras', 'fechaDesde', 'fechaHasta', 'totales', 'ventasscz', 'comprasscz', 'ventascbba', 'comprascbba'));
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
