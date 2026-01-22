<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Proveedor;
use App\Models\Areaaccion;
use App\Models\Departamento;
use App\Http\Requests\StoreProveedorRequest;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /* public function __construct() { 
        $this->middleware('can:admin.users.index')->only('index');
    } */

    public function index(Request $request)
    {
        $nombreproveedor = $request->get('buscarpor');

        $proveedores = Proveedor::where('proveedor','Like',"%$nombreproveedor%")->simplePaginate(1000);
       

        return view('admin.proveedores.index', compact('proveedores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areas = AreaAccion::select('area')->distinct()->pluck('area');
        $estadoproveedor = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
        return view('admin.proveedores.create', compact('departamentos', 'estadoproveedor', 'areas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProveedorRequest $request)
{
    $idCiudad = $request->input('ciudad');
    $ciudad = Departamento::findOrFail($idCiudad);
    $ciudadNombre = $ciudad->departamento;

    $areasSeleccionadas = $request->input('area');
    $estadoSeleccionado = $request->input('estadoproveedor');



    foreach ($areasSeleccionadas as $areaNombre) { // Iterar sobre los nombres de las áreas seleccionadas
        $proveedorData = $request->except(['area', '_token']);
        $proveedorData['ciudad'] = $ciudadNombre;
        $proveedorData['area'] = $areaNombre;
        $proveedorData['estadoproveedor'] = $estadoSeleccionado;



        Proveedor::create($proveedorData);
    }

    return redirect()->route('admin.proveedores.index')->with('info', 'Los proveedores se crearon con éxito');
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
    public function edit(Proveedor $proveedore)
{
    // Obtener las áreas asociadas al proveedor
    $areas_proveedor = explode(',', $proveedore->area);
    
    // Obtener las áreas de acciones asociadas al área del proveedor
    $areaacciones_proveedor = AreaAccion::whereIn('area', $areas_proveedor)
                                        ->select('areaaccion')
                                        ->distinct()
                                        ->pluck('areaaccion')
                                        ->toArray();
    
    // Obtener todas las áreas de acciones únicas
    $areaacciones = AreaAccion::select('areaaccion')
                              ->distinct()
                              ->pluck('areaaccion')
                              ->toArray();
    
    // Pasar las áreas de acciones del proveedor a la vista
    return view('admin.proveedores.edit', compact('proveedore', 'areas_proveedor', 'areaacciones', 'areaacciones_proveedor'));
}



    


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proveedor $proveedore)
    {

        $proveedore->update($request->all());

        return redirect()->route('admin.proveedores.index', $proveedore)->with('info', 'El proveedor se actualizó con éxito');
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
    
    public function listacuentaspagar(Proveedor $proveedor, Request $request)
    {
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
        'records','usuarioAutenticado','result','proveedor','totalCuenta1Ingreso','totalCuenta1Egreso',
        'totalCuenta2Ingreso','totalCuenta2Egreso','saldoanteriorcuenta1',
        'saldoanteriorcuenta2','saldoanteriorcuenta3','documentosPorFecha','cuentasbancos','proveedoresServicios',
        'proveedoresServicioscuenta','saldoanteriorcuenta4','totalCuenta4Ingreso','totalCuenta4Egreso'));
    }
}
