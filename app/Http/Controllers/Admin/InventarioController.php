<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CuentasPagar;
use App\Models\DetalleOrdenes;
use App\Models\Ordenes;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Inventario;
use App\Models\SeccionesInventario;
use App\Models\Proveedoresservicios;
use App\Models\PortafolioProveedores;
use App\Models\Detallerecibo;
use App\Models\PreOrdenes;
use App\Models\EntradaSalidaInventario;
use App\Models\SolicitudInventario;
use Illuminate\Support\Facades\Auth;
use App\Notifications\StockBajoNotification;
use App\Notifications\SolicitudProductoNotification;
use Illuminate\Support\Str;
use App\Notifications\ActSolicitudNotification;
use App\Notifications\AceptaSolicitudNotification;
use App\Notifications\AceptoRechazosolicitudNotification;
use App\Notifications\SolicitudInvEsperaNotification;
use Dompdf\Dompdf;
use PDF;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\PlanesServiciosProv;
use App\Models\CuentasBancos;
use App\Models\ClienteAuditoria;
use App\Models\ClienteComun;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Bateriasubcliente;
use App\Models\OpcionesInventario;
use App\Models\PermisoCodigo;
use Carbon\Carbon;

class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() { 
        $this->middleware ('can:admin.inventario.index')->only('index');
    }

    /* INVENTARIO */
    public function index(Request $request) 
    {
        $nombreproducto = $request->get('buscarpor');

        // Buscar y paginar los bienes del ALMACÉN
        $bienesalmacen = Inventario::where('nombreproducto', 'LIKE', "%$nombreproducto%")
            ->where('tipoinventario', 'ALMACEN')
            ->where('inventario', '!=', 'ANULADO')
            ->simplePaginate(20);

        // Contar todos los que coinciden (sin paginar)
        $almacenCount = Inventario::where('nombreproducto', 'LIKE', "%$nombreproducto%")
            ->where('tipoinventario', 'ALMACEN')
            ->where('inventario', '!=', 'ANULADO')
            ->count();


        // Buscar y paginar los bienes de ACTIVOS FIJOS
        $bienesactivosfijos = Inventario::where('nombreproducto', 'LIKE', "%$nombreproducto%")
            ->where('tipoinventario', 'ACTIVO FIJO')
            ->where('inventario', '!=', 'ANULADO')
            ->simplePaginate(20);

        // Contar todos los que coinciden (sin paginar)
        $activosfijosCount = Inventario::where('nombreproducto', 'LIKE', "%$nombreproducto%")
            ->where('tipoinventario', 'ACTIVO FIJO')
            ->where('inventario', '!=', 'ANULADO')
            ->count();


        // Paginar resultados con stock bajo
        $bienesalmacenstockbajo = Inventario::where('nombreproducto', 'LIKE', "%$nombreproducto%")
            ->where('tipoinventario', 'ALMACEN')
            ->whereColumn('stockactual', '<', 'minimocantidad')
            ->where('inventario', '!=', 'ANULADO')
            ->simplePaginate(20);

        // Contar total de registros con stock bajo (sin paginación)
        $stockbajoCount = Inventario::where('nombreproducto', 'LIKE', "%$nombreproducto%")
            ->where('tipoinventario', 'ALMACEN')
            ->whereColumn('stockactual', '<', 'minimocantidad')
            ->where('inventario', '!=', 'ANULADO')
            ->count();

        $usuarios = User::has('roles')
                        ->orderBy('name', 'asc')
                        ->get();

        $historiales = EntradaSalidaInventario::where('tipo','ENTRADA')->orderBy('created_at', 'desc')->get();
        $historialessalidas = EntradaSalidaInventario::where('tipo','SALIDA')->orderBy('created_at', 'desc')->get();
        
        $detalleOrdenes = DB::table('detalleordenes') 
            ->whereNull('estado')
            ->where('tipoorden', 'ORDEN DE COMPRA')
            ->whereIn('id', function ($query) {
                $query->select('detalleordenid')
                ->from('cuentasporpagar');
            })
            ->whereIn('id', function ($query) {
                $query->select('detalleordenid')
                    ->from('cuentasporpagar')
                    ->whereIn('id', function ($subquery) {
                        $subquery->select('cuentapagarid')
                            ->from('detallerecibos')
                            ->where('estado', 'PAGO PROCESADO');
                    });
            })
            ->get();

        $detalleOrdenesCount = $detalleOrdenes->count();
            
        // Traer todos los códigos como strings
        $codigosInventario = Inventario::pluck('codigo')->map(fn($c) => (string) $c)->toArray();
        $codigosPreordenes = PreOrdenes::pluck('codigo')->map(fn($c) => (string) $c)->toArray();

        // Unir y filtrar los existentes
        $codigosExistentes = array_unique(array_merge($codigosInventario, $codigosPreordenes));

        // Ahora convertir también los ID de portafolio a string antes de comparar
        $detalleingresodirecto = PortafolioProveedores::all()->filter(function($item) use ($codigosExistentes) {
            return !in_array((string) $item->id, $codigosExistentes);
        });

        $detalleingresodirectoCount = $detalleingresodirecto->count();

        $usuarioAutenticado = auth()->user()->name;
        $hoy = Carbon::today();
        $codigosPermitidos = PermisoCodigo::where('usuarioSolicitante', $usuarioAutenticado)
        ->whereDate('fechaSolicitada', $hoy->toDateString())
        ->where('permisoSolicitado', 'admin.inventario.cambiarstockinventario')
        ->where('estado', 'expirado')
        ->pluck('clienteid')
        ->toArray();

        return view('admin.inventario.index', compact('codigosPermitidos','detalleingresodirectoCount','detalleOrdenesCount','stockbajoCount','almacenCount','activosfijosCount','detalleingresodirecto','bienesalmacenstockbajo','detalleOrdenes', 'bienesalmacen', 'bienesactivosfijos', 'usuarios', 'historiales', 'historialessalidas'));
    }

    public function anularproductoinventario(Request $request)
    {
        $codigos = $request->input('codigos', []);

        if (!empty($codigos)) {
            Inventario::whereIn('codigo', $codigos)
                ->update(['inventario' => 'ANULADO']);
            
            return back()->with('info', 'Los productos han sido de baja correctamente.');
        }

        return back()->with('error', 'No seleccionó ningún producto.');
    }

    public function permisoscodigocambiarstock(Request $request)
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
    public function actualizarStockcodigo(Request $request, $codigo)
    {
        $request->validate([
            'stockactual' => 'required|numeric|min:0',
        ]);

        $producto = DB::table('inventario')->where('codigo', $codigo)->first();

        $datosActualizados = [
            'stockactual' => $request->stockactual,
            'updated_at' => now(),
        ];

        // Solo si el valor actual de 'inventario' es 'AGOTADO', se actualiza a 'PRINCIPAL'
        if (!is_null($producto) && strtoupper($producto->inventario) === 'AGOTADO') {
            $datosActualizados['inventario'] = 'PRINCIPAL';
        }

        DB::table('inventario')
            ->where('codigo', $codigo)
            ->update($datosActualizados);

        return redirect()->back()->with('info', 'Stock actualizado correctamente.');
    }

    public function getByCodigo($codigo)
    {
        $registro = PortafolioProveedores::find($codigo);
        if ($registro) {
            return response()->json($registro);
        }
        return response()->json(['error' => 'Registro no encontrado'], 404);
    }
    public function actualizarStockinventario(Request $request) 
{
    $codigoProducto = $request->codigo_producto;
    $cantidad = $request->cantidad;

    $inventario = Inventario::where('codigo', $codigoProducto)->first();

    if ($inventario) {
        $inventario->stockactual += $cantidad;
        $inventario->inventario = 'PRINCIPAL';
        $inventario->save();

        $detalleOrdenes = DB::table('detalleordenes')
            ->where('codigo', $codigoProducto)
            ->get();

        foreach ($detalleOrdenes as $detalle) {
            $cuentaPagar = CuentasPagar::where('detalleordenid', $detalle->id)->first();

            if ($cuentaPagar) {
                $cuentaPagar->estado = 'FINALIZADO';
                $cuentaPagar->save();

                DB::table('detalleordenes')
                    ->where('id', $detalle->id)
                    ->update(['estado' => 'FINALIZADO']);
            }
        }

        // Mensaje en la sesión
        session()->flash('info', 'STOCK ACTUALIZADO EN INVENTARIO.');

        return response()->json(['success' => true]);
    } else {
        return response()->json(['success' => false, 'message' => 'Producto no encontrado en el inventario.']);
    }
}

    public function registrarProducto(Request $request)
    {
        $validated = $request->validate([
            'codigo' => '',
            'nombreproducto' => '',
            'stockactual' => '',
            'tipoinventario' => '',
            'materiaprima' => '',
            'especificacionmedida' => '',
            'color' => '',
            'marca' => '',
            'unidadmedida' => '',
            'inventario' => '',
            'seccion' => '',
            'stockinicial' => '',
            'precio' => '',
            'deposito' => '',
            'ciudad' => '',
            'modelo' => '',
            'serie' => '',
            'presentacion' => '',
            'unidades' => '',
            'cantidad' => '',
            'minimacantidad' => '',
            'fechavencimiento' => '',
            'garantia' => '',
            'proveedorid' => '',
            'proveedornombre' => '',
            'preciounitario' => '',
        ]);

        $usuario = auth()->user();

        $nuevoProducto = new Inventario();
        $nuevoProducto->codigo = $request->codigo;
        $nuevoProducto->nombreproducto = $request->nombreproducto;
        $nuevoProducto->stockactual = $request->cantidad;
        $nuevoProducto->tipoinventario = $request->tipoinventario;
        $nuevoProducto->materiaprima = $request->materiaprima;
        $nuevoProducto->especificacionmedida = $request->especificacionmedida;
        $nuevoProducto->color = $request->color;
        $nuevoProducto->marca = $request->marca;
        $nuevoProducto->unidadmedida = $request->unidadmedida;
        $nuevoProducto->inventario = $request->inventario;
        $nuevoProducto->seccion = $request->seccion;
        $nuevoProducto->stockinicial = $request->cantidad;
        $nuevoProducto->precio = $request->precio;
        $nuevoProducto->deposito = $request->deposito;
        $nuevoProducto->ciudad = $usuario->sucursal;
        $nuevoProducto->modelo = $request->modelo;
        $nuevoProducto->serie = $request->serie;
        $nuevoProducto->presentacion = $request->presentacion;
        $nuevoProducto->unidades = $request->unidades;
        $nuevoProducto->cantidad = $request->cantidad;
        $nuevoProducto->minimocantidad = $request->minimacantidad;
        $nuevoProducto->usuarioregistroid = $usuario->id;
        $nuevoProducto->usuarioregistronombre = $usuario->name;
        $nuevoProducto->proveedorid = $request->proveedorid;
        $nuevoProducto->proveedornombre = $request->proveedornombre;
        $nuevoProducto->preciounitario = $request->preciounitario;

        $nuevoProducto->save();

        $detalleOrdenId = $request->detalleordenid;
        if ($detalleOrdenId) {
            DB::table('detalleordenes')
                ->where('id', $detalleOrdenId)
                ->update(['estado' => 'FINALIZADO']);
        }

        $entrada = new EntradaSalidaInventario();
        $entrada->tipo = 'ENTRADA';
        $entrada->codigoproducto = $request->codigo;
        $entrada->usuarioregistroid = $usuario->id;
        $entrada->usuarioregistronombre = $usuario->name;
        $entrada->nrofactura = $request->nrofactura;
        $entrada->nrorecibo = $request->nrorecibo;
        $entrada->fechamovimiento = $request->fecha;
        $entrada->precio = $request->precio;
        $entrada->cantidad = $request->cantidad;
        $entrada->fechacompra = $request->fechacompra;
        $entrada->fechavencimiento = $request->fechavencimiento;
        $entrada->garantia = $request->garantia;
        $entrada->save();

        return redirect()->route('admin.inventario.index')->with('info', 'Producto registrado y estado de la orden actualizado a FINALIZADO.');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /* CREAR PRODUCTO ALMACEN */
    public function create(Request $request)
    {
        $proveedores = ProveedoresServicios::where(function($query) {
            $query->where('tipoorden1', 'ORDEN DE COMPRA')
                  ->orWhere('tipoorden2', 'ORDEN DE COMPRA')
                  ->orWhere('tipoorden3', 'ORDEN DE COMPRA');
        })
        ->select('id', 'razonsocial')
        ->orderBy('razonsocial')
        ->get();

        $user = auth()->user();
        $sucursal = $user->sucursal; 

        $opcionesInventario = DB::table('opcionesinventario')->get();

        return view('admin.inventario.create', compact('proveedores', 'sucursal', 'opcionesInventario'));
    }

    /* CREAR ACTIVO FIJO */
    public function crearactivofijo(Request $request)
    {
        $proveedores = ProveedoresServicios::where(function($query) {
            $query->where('tipoorden1', 'ORDEN DE COMPRA')
                  ->orWhere('tipoorden2', 'ORDEN DE COMPRA')
                  ->orWhere('tipoorden3', 'ORDEN DE COMPRA');
        })
        ->select('id', 'razonsocial')
        ->orderBy('razonsocial')
        ->get();

        $user = auth()->user();
        $sucursal = $user->sucursal; 

        $opcionesInventario = DB::table('opcionesinventario')->get();
    
        return view('admin.inventario.crearactivofijo', compact('proveedores','sucursal', 'opcionesInventario'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $nuevoID = null;

        if ($request->tipo_inventario == 'ALMACEN') {
            $prefijo = null;
            if ($request->seccion == 'ESCRITORIO') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'COCINA') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'ALIMENTOS Y BEBIDAS') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'CONTRUCCION Y FERRETERIA') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'LIMPIEZA') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'PLASTICO') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'PROMOCIONAL') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'USO MEDICO') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'INSUMOS DECORATIVOS') {
                $prefijo = 'OTIN-';
            } else {
                $prefijo = 'OTIN-';
            }
            
        } elseif ($request->tipo_inventario == 'ACTIVO FIJO') {
            $prefijo = null;
            if ($request->seccion == 'ALMACEN') {
                $prefijo = 'OTIN-';
            } elseif (in_array($request->seccion, ['BAÑO 1', 'BAÑO 2', 'BAÑO GERENCIAL', 'BAÑO PLANTA ALTA'])) {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'COCINA') {
                $prefijo = 'OTIN-';
            } elseif (in_array($request->seccion, ['CONSULTORIO 1', 'CONSULTORIO 2', 'CONSULTORIO 3'])) {
                $prefijo = 'OTIN-';
            } elseif (in_array($request->seccion, ['GERENCIA COMERCIAL Y FINANCIERA', 'GERENCIA GENERAL', 'GERENCIA FINANCIERA'])) {
                $prefijo = 'OTIN-';
            } elseif (in_array($request->seccion, ['LAVANDERIA', 'GRADAS','PASILLO PLANTA ALTA','PASILLO PLANTA BAJA','DEPOSITO SECUNDARIO','DEPOSITO PRINCIPAL'])) {
                $prefijo = 'OTIN-';
            } elseif (in_array($request->seccion, ['OFICINA 1', 'OFICINA 2', 'ADMINISTRACION', 'AUDIOMETRIA', 'CAJA', 'ELECTROCARDIOGRAMA', 'ERGOMETRIA', 'ESPIROMETRIA', 'FISIOTERAPIA-MEDICINA LABORAL', 'LABORATORIO', 'OFICINA ADMINISTRATIVA', 'OFTALMOLOGIA', 'PRESTACIONES 1', 'PRESTACIONES 2', 'PROGRAMACION', 'PSICOLOGIA', 'SISTEMAS','OFICINA 1 PLANTA ALTA','OFICINA 2 PLANTA BAJA','OFICINA 3 PLANTA BAJA','CONSULTORIO 1 PLANTA ALTA','CONSULTORIO 2 PLANTA ALTA','CONSULTORIO 3 PLANTA ALTA','CONSULTORIO 4 PLANTA ALTA','CONSULTORIO 5 PLANTA ALTA','CONSULTORIO 6 PLANTA BAJA','CONSULTORIO 7 PLANTA BAJA','CONSULTORIO 8 PLANTA BAJA','CONSULTORIO 9 PLANTA BAJA'])) {
                $prefijo = 'OTIN-';
            } elseif (in_array($request->seccion, ['SALA DE ESPERA PLANTA BAJA', 'SALA 1', 'SALA DE ESPERA', 'SALA DE REUNIONES', 'ZONA DE MONITOREO','VISTA FRONTAL','ENTRADA PRINCIPAL','SALA DE ATENCION AL CLIENTE'])) {
                $prefijo = 'OTIN-';
            } else {
                $prefijo = 'OTIN-';
            }
            
        }
        if ($prefijo) {
            $ultimoRegistro = Inventario::where('codigo', 'LIKE', $prefijo . '%')
                                        ->orderByRaw("CAST(SUBSTRING(codigo, " . (strlen($prefijo) + 1) . ", LENGTH(codigo)) AS UNSIGNED) DESC")
                                        ->first();
            if ($ultimoRegistro) {
                $ultimoNumero = (int) substr($ultimoRegistro->codigo, strlen($prefijo));
                $nuevoNumero = $ultimoNumero + 1;
            } else {
                $nuevoNumero = 1;
            }
            $nuevoID = $prefijo . $nuevoNumero;
            $request->merge(['id' => $nuevoID]);
        }

        $usuarioAutenticado = Auth::user();

        Inventario::create([
            'tipoinventario' => $request->tipo_inventario,
            'codigo' => $nuevoID,
            'nombreproducto' => $request->nombreproducto,
            'materiaprima' => $request->materia_prima,
            'especificacionmedida' => $request->especificacionmedida,
            'color' => $request->color,
            'marca' => $request->marca,
            'unidadmedida' => $request->unidad_medida,
            'presentacion' => $request->presentacion,
            'unidades' => $request->unidades,
            'minimocantidad' => $request->minimocantidad,
            'inventario' => $request->inventario,
            'seccion' => $request->seccion,
            'stockinicial' => $request->stockinicial,
            'stockactual' => $request->stockactual,
            'precio' => $request->precio,
            'preciounitario' => $request->preciounitario,
            'cantidad' => $request->cantidad,
            'deposito' => $request->deposito,
            'ciudad' => $request->ciudad,
            /* 'modelo' => $request->modelo,
            'serie' => $request->serie, */
            'usuarioregistroid' => $usuarioAutenticado->id,
            'usuarioregistronombre' => $usuarioAutenticado->name,
            'proveedorid' => $request->proveedorid,
            'proveedornombre' => $request->proveedornombre,
        ]);

        return redirect()->route('admin.inventario.index')->with('info', 'Inventario registrados con éxito.');
    }
    public function adquisicioninventario(Request $request)  
    {
        $nombreproducto = $request->get('buscarpor');

        /* $bienesalmacen = PortafolioProveedores::leftJoin('inventario', 'portafolioproveedores.id', '=', 'inventario.codigo')
            ->select('portafolioproveedores.*',
                    'inventario.stockactual as cantidadalmacen'); */

        
        $bienesalmacen = PortafolioProveedores::leftJoin('inventario', 'portafolioproveedores.id', '=', 'inventario.codigo')
            ->leftJoin('proveedoresservicios', 'portafolioproveedores.proveedorid', '=', 'proveedoresservicios.id')
            ->select(
                'portafolioproveedores.*',
                'inventario.stockactual as cantidadalmacen',
                'proveedoresservicios.bancoorigen'
            );

        if ($nombreproducto) {
            $bienesalmacen = $bienesalmacen->where(function ($query) use ($nombreproducto) {
                    $query->where('portafolioproveedores.nombreproducto', 'LIKE', "%$nombreproducto%")
                        ->orWhere('portafolioproveedores.id', 'LIKE', "%$nombreproducto%");
                });
            }
            $bienesalmacen = $bienesalmacen->paginate(20);

        if ($request->ajax()) {
            return view('admin.inventario.partials.tabla', compact('bienesalmacen'))->render();
        }

        return view('admin.inventario.adquisicioninventario', compact('bienesalmacen'));
    }
    
//PRE ORDENES
    /* public function generarpreordencompra(Request $request)  
    {
        $ordenesCompra = json_decode($request->input('ordenes_compra'), true);
        $tipotransaccion = $request->input('tipotransaccion');
        $formapago = $request->input('formapago');
        $sucursal = $request->input('sucursal');
        $sucursalgasto = $request->input('sucursalgasto');
        $fechacomprar = $request->input('fechacomprar');
        $fechapagar = $request->input('fechapagar');
        $proveedorid = $request->input('proveedorId');
        $observacion = $request->input('observacion');
        $usuarioAutenticado = auth()->user()->id;
        $usuarioAutenticadonombre = auth()->user()->name;

        $ultimoNumero = PreOrdenes::select(DB::raw('MAX(CAST(SUBSTRING(preordenid, 1, LENGTH(preordenid) - 2) AS UNSIGNED)) as max_num'))
        ->where('preordenid', 'LIKE', '%PO')
        ->value('max_num');

        $nuevoPreOrdenId = ($ultimoNumero ? ($ultimoNumero + 1) : 1) . 'PO';

        $proveedor = Proveedoresservicios::find($proveedorid);
        if (!$proveedor) {
            return redirect()->back()->with('error', 'Proveedor no encontrado');
        }

        foreach ($ordenesCompra as $detalle) {
            PreOrdenes::create([
                'detalle' => $detalle['detalle'],
                'cantidad' => $detalle['cantidad'],
                'tipotransaccion' => $tipotransaccion,
                'preciounitario' => $detalle['subtotal'],
                'codigo' => $detalle['id'],
                'descuentounitario' => $detalle['descuento'],
                'totalunitario' => $detalle['subtotal'] - $detalle['descuento'],
                'proveedorid' => $proveedorid,
                'proveedornombre' => $proveedor->razonsocial,
                'fechacomprar' => $fechacomprar,
                'fechapagar' => $fechapagar,
                'tipoorden' => 'ORDEN DE COMPRA',
                'estado' => 'PENDIENTE',
                'formapago' => $formapago,
                'sucursal' => $sucursal,
                'sucursalgasto' => $sucursalgasto,
                'observacion' => $observacion,
                'preordenid' => $nuevoPreOrdenId,
                'usuarioregistroid' => $usuarioAutenticado,
                'usuarioregistronombre' => $usuarioAutenticadonombre,
            ]);
        }
        
        return redirect()->route('admin.inventario.crearordenes')->with('info', 'Pre - Orden de compra registrada con éxito.');
    }
    public function generarOrdenCompra(Request $request)
    {
        $tipotransaccion = $request->input('tipotransaccion');
        $formapago = $request->input('formapago');
        $fechacomprar = $request->input('fechacomprar');
        $sucursalgasto = $request->input('sucursalgasto');
        $fechapagar = $request->input('fechapagar');
        $proveedorid = $request->input('proveedorid');
        $proveedornombre = $request->input('proveedornombre');
        $observacion = $request->input('observacion');
        $montototal = $request->input('montototal');
        $subtotal = $request->input('subtotal');
        $descuento = $request->input('descuento');
        $usuariopreorden = $request->input('usuariopreorden');
        $usuarioAutenticado = auth()->user()->id;
        $usuarioAutenticadonombre = auth()->user()->name;
        $sucursal = auth()->user()->sucursal;
        $bancosDestino = $request->input('bancodestino');
        $totalunitariosEditados = $request->input('totalunitario');

        $proveedor = Proveedoresservicios::find($proveedorid);
        if (!$proveedor) {
            return redirect()->back()->with('error', 'Proveedor no encontrado');
        }

        $ultimoNumero = Ordenes::where('tipoorden', 'ORDEN DE COMPRA')
            ->where('id', 'LIKE', '%C')
            ->select(DB::raw('MAX(CAST(SUBSTRING(id, 1, LENGTH(id) - 1) AS UNSIGNED)) as max_id'))
            ->value('max_id');

        $nuevoIdConSufijo = (($ultimoNumero ?? 0) + 1) . 'C';

        $ordenData = [
            'id' => $nuevoIdConSufijo,
            'observacion' => $observacion,
            'tipotransaccion' => $tipotransaccion,
            'formapago' => $formapago,
            'sucursalgasto' => $sucursalgasto,
            'sucursal' => $sucursal,
            'fechacomprar' => $fechacomprar,
            'fechapagar' => $fechapagar,
            'proveedorid' => $proveedorid,
            'proveedornombre' => $proveedornombre,
            'montototal' => $montototal,
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'observacion' => $observacion,
            'usuarioregistroid' => $usuarioAutenticado,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
            'tipoorden' => 'ORDEN DE COMPRA',
            'estado' => 'APROBADO',
            'usuariopreorden' => $usuariopreorden,
        ];

        $orden = Ordenes::create($ordenData);
        $ordenesCompra = $request->input('ordenes');
        $preOrdenesSeleccionadas = PreOrdenes::whereIn('id', $ordenesCompra)->get();
        $agrupadosPorPreordenId = $preOrdenesSeleccionadas->groupBy('preordenid');
        foreach ($agrupadosPorPreordenId as $preordenid => $preOrdenes) {
            $codigosSeleccionados = $preOrdenes->pluck('id')->toArray();

            PreOrdenes::where('preordenid', $preordenid)->update([
                'estado' => 'RECHAZADO' 
            ]);

            PreOrdenes::whereIn('id', $codigosSeleccionados)->update([
                'estado' => 'APROBADO'
            ]);
        }
            if ($ordenesCompra) {
                $detallesPreOrdenes = [];
                foreach ($ordenesCompra as $id) {
                    $detalleOrden = PreOrdenes::where('id', $id)->first();
                    $totalUnitarioFinal = $totalunitariosEditados[$id] ?? $detalleOrden->totalunitario;
                    $totalUnitarioFinal = str_replace(',', '', $totalUnitarioFinal);
                    $totalUnitarioFinal = floatval($totalUnitarioFinal);

                    if ($detalleOrden) {
                        $detallesPreOrdenes[] = $detalleOrden;
                        $cuentaSeleccionada = $bancosDestino[$id] ?? null;
                
                        DetalleOrdenes::create([
                            'detalle' => $detalleOrden->detalle,
                            'cantidad' => $detalleOrden->cantidad,
                            'preciounitario' => $detalleOrden->preciounitario,
                            'codigo' => $detalleOrden->codigo,
                            'descuentounitario' => $detalleOrden->descuentounitario,
                            'totalunitario' => $totalUnitarioFinal,
                            'tipoorden' => 'ORDEN DE COMPRA',
                            'ordenid' => $orden->id,
                            'observacion' => $observacion,
                            'nrobancoorigen' => $cuentaSeleccionada,
                            'tipotransaccion' => $tipotransaccion,
                            'formapago' => $formapago,
                            'fechacomprar' => $fechacomprar,
                            'fechapagar' => $fechapagar,
                            'proveedorid' => $proveedorid,
                            'proveedornombre' => $proveedornombre,
                            'usuarioregistroid' => $usuarioAutenticado,
                            'usuarioregistronombre' => $usuarioAutenticadonombre,
                            'usuariopreorden' => $usuariopreorden,
                        ]);

                    }
                }
            }
            $detalleOrdenes = DetalleOrdenes::where('ordenid', $orden->id)->get(); 

            $ultimacp = CuentasPagar::orderByRaw("LENGTH(id) DESC, id DESC")
                ->first();
            $nuevoIdcp = $ultimacp ? ((int) filter_var($ultimacp->id, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;

            foreach ($detalleOrdenes as $detalle) {
                $idUnico = $nuevoIdcp . 'CP';

                $preordenConSaldo = PreOrdenes::where('detalle', 'LIKE', '%' . $detalle->ordenid . ')')
                    ->where('codigo', $detalle->codigo)
                    ->where('preciounitario', '>', 0)
                    ->first();

                $subtotalFinal = $detalle->preciounitario;
                if ($preordenConSaldo) {
                    $subtotalFinal -= $preordenConSaldo->preciounitario;
                }

                $cuentasPagar = new CuentasPagar();
                $cuentasPagar->id = $idUnico;
                $cuentasPagar->proveedorid = $orden->proveedorid;
                $cuentasPagar->proveedornombre = $orden->proveedornombre;
                $cuentasPagar->detalleproducto = $detalle->detalle;
                $cuentasPagar->fechaasignada = $orden->fechapagar;
                $cuentasPagar->fechacomprar = $orden->fechacomprar;
                $cuentasPagar->nrobancoorigen = $detalle->nrobancoorigen;
                $cuentasPagar->subtotal = $subtotalFinal;
                $cuentasPagar->descuento = $detalle->descuentounitario;
                $cuentasPagar->montototal = $detalle->totalunitario;
                $cuentasPagar->preciocompra = '0.00';
                $cuentasPagar->sucursalgasto = $orden->sucursalgasto;
                $cuentasPagar->ciudad = $sucursal;
                $cuentasPagar->tipoorden = $orden->tipoorden;
                $cuentasPagar->tipoproveedorservicio = $proveedor->categoria;
                $cuentasPagar->ordenid = $orden->id;
                $cuentasPagar->cantidad = $detalle->cantidad;
                $cuentasPagar->estado = 'PENDIENTE';
                $cuentasPagar->usuarioregistroid = $orden->usuarioregistroid;
                $cuentasPagar->usuarioregistronombre = $orden->usuarioregistronombre;
                $cuentasPagar->detalleordenid = $detalle->id;
                $cuentasPagar->save();
                $nuevoIdcp++;
            }

            $pdfData = [
                'ordenesCompra' => $detalleOrdenes,
                'tipotransaccion' => $tipotransaccion,
                'formapago' => $formapago,
                'fechacomprar' => $fechacomprar,
                'fechapagar' => $fechapagar,
                'proveedor' => $proveedornombre,
                'observacion' => $observacion,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'montototal' => $montototal,
                'saldo' => $subtotal - ($descuento + $montototal),
                'usuarioAutenticado' => $usuarioAutenticado,
                'usuarioAutenticadonombre' => $usuarioAutenticadonombre,
                'ordenId' => $orden->id,
                'proveedor' => $proveedor,
                'usuarioregistro' => $usuariopreorden,
            ];

            $pdf = PDF::loadView('admin.inventario.pdfgenerarordencompra', $pdfData);
            $nombreArchivo = 'ORDEN_COMPRA_' . $orden->id . '.pdf';
            
            $directorio = public_path('ordenesaprobadas/' . $usuarioAutenticado);
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }
            
            $rutaArchivo = $directorio . '/' . $nombreArchivo;
            $pdf->save($rutaArchivo);

            $orden->update([
                'documentoorden' => $nombreArchivo,
            ]);

        return redirect()->route('admin.inventario.listaordenes')->with('info', 'Orden de compra generada con éxito.');
    } */

        /* NUEVAS ORDENES */
    public function crearordenes(Request $request)  
    {
        $nombreproducto = $request->get('buscarpor');
        $sucursal = auth()->user()->sucursal;
        $bienesalmacen = PortafolioProveedores::leftJoin('inventario', 'portafolioproveedores.id', '=', 'inventario.codigo')
            ->leftJoin('proveedoresservicios', 'portafolioproveedores.proveedorid', '=', 'proveedoresservicios.id')
            ->select(
                'portafolioproveedores.*',
                'inventario.stockactual as cantidadalmacen',
                'proveedoresservicios.bancoorigen'
            );
        if ($nombreproducto) {
        $bienesalmacen = $bienesalmacen->where(function ($query) use ($nombreproducto) {
                $query->where('portafolioproveedores.nombreproducto', 'LIKE', "%$nombreproducto%")
                    ->orWhere('portafolioproveedores.id', 'LIKE', "%$nombreproducto%");
            });
        }
        $bienesalmacen = $bienesalmacen->paginate(20);

        if ($request->ajax()) {
            return view('admin.inventario.partials.tabla', compact('bienesalmacen'))->render();
        }

        $proveedores = ProveedoresServicios::where(function($query) {
            $query->where('tipoorden1', 'ORDEN DE SERVICIO')
                  ->orWhere('tipoorden2', 'ORDEN DE SERVICIO')
                  ->orWhere('tipoorden3', 'ORDEN DE SERVICIO');
        })
        ->select('id', 'razonsocial', 'tipotransaccion', 'ciudad', 'ciudad2', 'bancoorigen')
        ->orderBy('razonsocial')
        ->get();


        /* $proveedoresServicios = ProveedoresServicios::where(function($query) {
                $query->where('tipoorden1', 'ORDEN DE SERVICIO')
                    ->orWhere('tipoorden2', 'ORDEN DE SERVICIO')
                    ->orWhere('tipoorden3', 'ORDEN DE SERVICIO');
            })
            ->select(
                'id',
                'razonsocial',
                'tipotransaccion',
                'ciudad',
                'ciudad2',
                'bancoorigen'
            )
            ->get();

        $proveedorExtra = Proveedor::where('id', 13)
            ->selectRaw("
                id,
                proveedor as razonsocial,
                mododepago as tipotransaccion,
                ciudad,
                ciudad2,
                bancoorigen
            ")
            ->get();

        $proveedores = $proveedoresServicios->merge($proveedorExtra)
                                    ->sortBy('razonsocial')
                                    ->values(); */

    
        $proveedorespersonal = ProveedoresServicios::whereIn('categoria', ['PROVEEDOR EXTERNO', 'PROVEEDOR INTERNO'])->select('id', 'razonsocial', 'tipotransaccion', 'categoria', 'ciudad', 'ciudad2', 'bancoorigen')->get();

        $planes = PlanesServiciosProv::select('id', 'plan', 'proveedorid', 'contrato', 'linea', 'cuenta', 'servicio', 'codigo', 'montofijo', 'ciudad', 'motivo')->get();

        $cuentas = CuentasBancos::where('estado', 'ACTIVO')->get();

        return view('admin.inventario.crearordenes', compact('cuentas','bienesalmacen', 'proveedores', 'planes', 'proveedorespersonal', 'sucursal'));
    }
    
    public function generarpreordencompra(Request $request)
    {
        DB::beginTransaction(); // Iniciamos transacción para evitar datos inconsistentes
        try {
            // === DATOS DEL REQUEST ===
            $ordenesCompra = json_decode($request->input('ordenes_compra'), true);
            $tipotransaccion = $request->input('tipotransaccion');
            $formapago = $request->input('formapago');
            $sucursal = $request->input('sucursal');
            $sucursalgasto = $request->input('sucursalgasto');
            $fechacomprar = $request->input('fechacomprar');
            $fechapagar = $request->input('fechapagar');
            $proveedorid = $request->input('proveedorId');
            $observacion = $request->input('observacion');
            $montototal = $request->input('montototal');
            $subtotal = $request->input('subtotal');
            $descuento = $request->input('descuento');
            $nroBancoOrigen = $request->input('nrocuenta');
            $bancosDestino = $request->input('bancodestino');
            $usuarioAutenticado = auth()->user()->id;
            $usuarioAutenticadonombre = auth()->user()->name;

            // === VALIDAR PROVEEDOR ===
            $proveedor = Proveedoresservicios::find($proveedorid);
            if (!$proveedor) {
                return redirect()->back()->with('error', 'Proveedor no encontrado');
            }

            // === GENERAR ID PREORDEN ===
            $ultimoNumeroPre = PreOrdenes::select(DB::raw('MAX(CAST(SUBSTRING(preordenid, 1, LENGTH(preordenid) - 2) AS UNSIGNED)) as max_num'))
                ->where('preordenid', 'LIKE', '%PO')
                ->value('max_num');
            $nuevoPreOrdenId = ($ultimoNumeroPre ? ($ultimoNumeroPre + 1) : 1) . 'PO';

            $idsPreOrdenes = [];

            // === CREAR PREORDENES ===
            foreach ($ordenesCompra as $detalle) {
                $preOrden = PreOrdenes::create([
                    'detalle' => $detalle['detalle'],
                    'cantidad' => $detalle['cantidad'],
                    'tipotransaccion' => $tipotransaccion,
                    'preciounitario' => $detalle['subtotal'],
                    'codigo' => $detalle['id'],
                    'descuentounitario' => $detalle['descuento'],
                    'totalunitario' => $detalle['subtotal'] - $detalle['descuento'],
                    'proveedorid' => $proveedorid,
                    'proveedornombre' => $proveedor->razonsocial,
                    'fechacomprar' => $fechacomprar,
                    'fechapagar' => $fechapagar,
                    'tipoorden' => 'ORDEN DE COMPRA',
                    'estado' => 'PENDIENTE',
                    'formapago' => $formapago,
                    'sucursal' => $sucursal,
                    'sucursalgasto' => $sucursalgasto,
                    'observacion' => $observacion,
                    'preordenid' => $nuevoPreOrdenId,
                    'usuarioregistroid' => $usuarioAutenticado,
                    'usuarioregistronombre' => $usuarioAutenticadonombre,
                ]);
                $idsPreOrdenes[] = $preOrden->id;
            }

            // === GENERAR ID ORDEN ===
            $ultimoNumeroOrden = Ordenes::where('tipoorden', 'ORDEN DE COMPRA')
                ->where('id', 'LIKE', '%C')
                ->select(DB::raw('MAX(CAST(SUBSTRING(id, 1, LENGTH(id) - 1) AS UNSIGNED)) as max_id'))
                ->value('max_id');
            $nuevoIdOrden = (($ultimoNumeroOrden ?? 0) + 1) . 'C';

            // === CREAR ORDEN DE COMPRA ===
            $orden = Ordenes::create([
                'id' => $nuevoIdOrden,
                'observacion' => $observacion,
                'tipotransaccion' => $tipotransaccion,
                'formapago' => $formapago,
                'sucursalgasto' => $sucursalgasto,
                'sucursal' => $sucursal,
                'fechacomprar' => $fechacomprar,
                'fechapagar' => $fechapagar,
                'proveedorid' => $proveedorid,
                'proveedornombre' => $proveedor->razonsocial,
                'montototal' => $montototal,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'usuarioregistroid' => $usuarioAutenticado,
                'usuarioregistronombre' => $usuarioAutenticadonombre,
                'tipoorden' => 'ORDEN DE COMPRA',
                'estado' => 'APROBADO',
                'usuariopreorden' => $usuarioAutenticadonombre,
            ]);

            // === CREAR DETALLE ORDEN Y CUENTAS POR PAGAR ===
            $detallePreOrdenes = PreOrdenes::whereIn('id', $idsPreOrdenes)->get();
            $ultimacp = CuentasPagar::orderByRaw("LENGTH(id) DESC, id DESC")->first();
            $nuevoIdcp = $ultimacp ? ((int) filter_var($ultimacp->id, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;

            foreach ($detallePreOrdenes as $detalle) {
                $cuentaDestino = $bancosDestino[$detalle->id] ?? null;

                // Crear DetalleOrden
                $detalleOrden = DetalleOrdenes::create([
                    'detalle' => $detalle->detalle,
                    'cantidad' => $detalle->cantidad,
                    'preciounitario' => $detalle->preciounitario,
                    'codigo' => $detalle->codigo,
                    'descuentounitario' => $detalle->descuentounitario,
                    'totalunitario' => $detalle->totalunitario,
                    'tipoorden' => 'ORDEN DE COMPRA',
                    'ordenid' => $orden->id,
                    'observacion' => $observacion,
                    'nrobancoorigen' => $cuentaDestino,
                    'tipotransaccion' => $tipotransaccion,
                    'formapago' => $formapago,
                    'fechacomprar' => $fechacomprar,
                    'fechapagar' => $fechapagar,
                    'proveedorid' => $proveedorid,
                    'proveedornombre' => $proveedor->razonsocial,
                    'usuarioregistroid' => $usuarioAutenticado,
                    'usuarioregistronombre' => $usuarioAutenticadonombre,
                    'usuariopreorden' => $usuarioAutenticadonombre,
                ]);

                /* $proveedorBanco = Proveedoresservicios::find($orden->proveedorid);
                $nroBancoOrigen = 0;
                if ($proveedorBanco) {
                    if ($proveedorBanco->bancoorigen === 'CUENTA FACTURADA') {
                        $nroBancoOrigen = '3000189269';
                    } elseif ($proveedorBanco->bancoorigen === 'CUENTA NO FACTURADA') {
                        $nroBancoOrigen = '2505314878';
                    }
                } */

                // Crear CuentasPagar
                $idUnicoCP = $nuevoIdcp . 'CP';
                CuentasPagar::create([
                    'id' => $idUnicoCP,
                    'proveedorid' => $orden->proveedorid,
                    'nrobancoorigen'=> $nroBancoOrigen,
                    'proveedornombre' => $orden->proveedornombre,
                    'detalleproducto' => $detalleOrden->detalle,
                    'fechaasignada' => $orden->fechapagar,
                    'fechacomprar' => $orden->fechacomprar,
                    'subtotal' => $detalleOrden->preciounitario,
                    'descuento' => $detalleOrden->descuentounitario,
                    'montototal' => $detalleOrden->totalunitario,
                    'preciocompra' => '0.00',
                    'sucursalgasto' => $orden->sucursalgasto,
                    'ciudad' => $sucursal,
                    'tipoorden' => $orden->tipoorden,
                    'tipoproveedorservicio' => $proveedor->categoria,
                    'ordenid' => $orden->id,
                    'cantidad' => $detalleOrden->cantidad,
                    'estado' => 'PENDIENTE',
                    'usuarioregistroid' => $orden->usuarioregistroid,
                    'usuarioregistronombre' => $orden->usuarioregistronombre,
                    'detalleordenid' => $detalleOrden->id,
                ]);
                $nuevoIdcp++;
            }

            // === GENERAR PDF ===
            $pdfData = [
                'ordenesCompra' => $detallePreOrdenes,
                'tipotransaccion' => $tipotransaccion,
                'formapago' => $formapago,
                'fechacomprar' => $fechacomprar,
                'fechapagar' => $fechapagar,
                'proveedor' => $proveedor,
                'observacion' => $observacion,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'montototal' => $montototal,
                'saldo' => $subtotal - ($descuento + $montototal),
                'usuarioAutenticado' => $usuarioAutenticado,
                'usuarioAutenticadonombre' => $usuarioAutenticadonombre,
                'ordenId' => $orden->id,
                'usuarioregistro' => $usuarioAutenticadonombre,
            ];

            $pdf = PDF::loadView('admin.inventario.pdfgenerarordencompra', $pdfData);
            $nombreArchivo = 'ORDEN_COMPRA_' . $orden->id . '.pdf';
            $directorio = public_path('ordenesaprobadas/' . $usuarioAutenticado);
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }
            $rutaArchivo = $directorio . '/' . $nombreArchivo;
            $pdf->save($rutaArchivo);

            $orden->update(['documentoorden' => $nombreArchivo]);

            DB::commit();
            return redirect()->route('admin.inventario.listaordenes')->with('info', 'Pre-orden y orden de compra generadas con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /* public function generarpreordenservicio(Request $request)  
    {
        $ordenesVenta = json_decode($request->input('ordenes_venta'), true);
        $tipotransaccion = $request->input('tipotransaccion3');
        $formapago = $request->input('formapago2');
        $sucursal = $request->input('sucursal2');
        $sucursalgasto = $request->input('sucursalgasto2');
        $fechacomprar = $request->input('fechacomprar2');
        $fechapagar = $request->input('fechapagar2');
        $proveedorid = $request->input('proveedorId2');
        $observacion = $request->input('observacion2');
        $proveedornombre = $request->input('proveedorNombre2');
        $proveedorid = $request->input('proveedorId2');
        $usuarioAutenticado = auth()->user()->id;
        $usuarioAutenticadonombre = auth()->user()->name;

        $ultimoNumero = PreOrdenes::select(DB::raw('MAX(CAST(SUBSTRING(preordenid, 1, LENGTH(preordenid) - 2) AS UNSIGNED)) as max_num'))
            ->where('preordenid', 'LIKE', '%PO')
            ->value('max_num');
        
        $nuevoPreOrdenId = ($ultimoNumero ? ($ultimoNumero + 1) : 1) . 'PO';

        foreach ($ordenesVenta as $detalle) {
            PreOrdenes::create([
                'detalle' => $detalle['detalle'],
                'cantidad' => $detalle['cantidad'],
                'tipotransaccion' => $tipotransaccion,
                'preciounitario' => $detalle['precio_unitario'],
                'descuentounitario' => $detalle['descuento'],
                'totalunitario' => $detalle['subtotal'],
                'proveedorid' => $proveedorid,
                'proveedornombre' => $proveedornombre,
                'fechacomprar' => $fechacomprar,
                'fechapagar' => $fechapagar,
                'tipoorden' => 'ORDEN DE SERVICIO',
                'estado' => 'PENDIENTE',
                'formapago' => $formapago,
                'sucursal' => $sucursal,
                'sucursalgasto' => $sucursalgasto,
                'observacion' => $observacion,
                'preordenid' => $nuevoPreOrdenId,
                'usuarioregistroid' => $usuarioAutenticado,
                'usuarioregistronombre' => $usuarioAutenticadonombre,
            ]);
        }
        
        return redirect()->route('admin.inventario.crearordenes')->with('info', 'Pre - Orden de servicio registrada con éxito.');
    }
    public function generarOrdenServicio(Request $request)
    {
        $tipotransaccion = $request->input('tipotransaccion');
        $formapago = $request->input('formapago');
        $sucursalgasto = $request->input('sucursalgasto2');
        $fechacomprar = $request->input('fechacomprar');
        $fechapagar = $request->input('fechapagar');
        $proveedorid = $request->input('proveedorid');
        $proveedornombre = $request->input('proveedornombre');
        $observacion = $request->input('observacion');
        $montototal = $request->input('montototal');
        $subtotal = $request->input('subtotal');
        $descuento = $request->input('descuento');
        $usuariopreorden = $request->input('usuariopreorden');
        $usuarioAutenticado = auth()->user()->id;
        $usuarioAutenticadonombre = auth()->user()->name;
        $sucursal = auth()->user()->sucursal;
        $bancosDestino = $request->input('bancodestino2');
        $totalunitariosEditados = $request->input('totalunitario2');

        $proveedor = Proveedoresservicios::find($proveedorid);
        if (!$proveedor) {
            return redirect()->back()->with('error', 'Proveedor no encontrado');
        }

        $ultimoNumero = Ordenes::where('tipoorden', 'ORDEN DE SERVICIO')
            ->where('id', 'LIKE', '%S')
            ->select(DB::raw('MAX(CAST(SUBSTRING(id, 1, LENGTH(id) - 1) AS UNSIGNED)) as max_id'))
            ->value('max_id');

        $nuevoIdConSufijo = (($ultimoNumero ?? 0) + 1) . 'S';

        $ordenData = [
            'id' => $nuevoIdConSufijo,
            'observacion' => $observacion,
            'tipotransaccion' => $tipotransaccion,
            'formapago' => $formapago,
            'sucursalgasto' => $sucursalgasto,
            'sucursal' => $sucursal,
            'fechacomprar' => $fechacomprar,
            'fechapagar' => $fechapagar,
            'proveedorid' => $proveedorid,
            'proveedornombre' => $proveedornombre,
            'montototal' => $montototal,
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'observacion' => $observacion,
            'usuarioregistroid' => $usuarioAutenticado,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
            'tipoorden' => 'ORDEN DE SERVICIO',
            'estado' => 'APROBADO',
            'usuariopreorden' => $usuariopreorden,
        ];

        $orden = Ordenes::create($ordenData);
        $ordenesServicio = $request->input('ordenes2');
        $preOrdenesSeleccionadas = PreOrdenes::whereIn('id', $ordenesServicio)->get();
        $agrupadosPorPreordenId = $preOrdenesSeleccionadas->groupBy('preordenid');
        foreach ($agrupadosPorPreordenId as $preordenid => $preOrdenes) {
            $codigosSeleccionados = $preOrdenes->pluck('id')->toArray();

            PreOrdenes::where('preordenid', $preordenid)->update([
                'estado' => 'RECHAZADO'
            ]);

            PreOrdenes::whereIn('id', $codigosSeleccionados)->update([
                'estado' => 'APROBADO'
            ]);
        }
            if ($ordenesServicio) {
                $detallesPreOrdenes = [];
                foreach ($ordenesServicio as $id) {
                    $detalleOrden = PreOrdenes::where('id', $id)->first();

                    $totalUnitarioFinal = $totalunitariosEditados[$id] ?? $detalleOrden->totalunitario;
                    $totalUnitarioFinal = str_replace(',', '', $totalUnitarioFinal);
                    $totalUnitarioFinal = floatval($totalUnitarioFinal);

                    if ($detalleOrden) {
                        $detallesPreOrdenes[] = $detalleOrden;
                        $cuentaSeleccionada = $bancosDestino[$id] ?? null;
                
                        DetalleOrdenes::create([
                            'detalle' => $detalleOrden->detalle,
                            'cantidad' => $detalleOrden->cantidad,
                            'preciounitario' => $detalleOrden->preciounitario,
                            'codigo' => $detalleOrden->codigo,
                            'descuentounitario' => $detalleOrden->descuentounitario,
                            'totalunitario' => $totalUnitarioFinal,
                            'tipoorden' => 'ORDEN DE SERVICIO',
                            'ordenid' => $orden->id,
                            'observacion' => $observacion,
                            'nrobancoorigen' => $cuentaSeleccionada,
                            'tipotransaccion' => $tipotransaccion,
                            'formapago' => $formapago,
                            'fechacomprar' => $fechacomprar,
                            'fechapagar' => $fechapagar,
                            'proveedorid' => $proveedorid,
                            'proveedornombre' => $proveedornombre,
                            'usuarioregistroid' => $usuarioAutenticado,
                            'usuarioregistronombre' => $usuarioAutenticadonombre,
                            'usuariopreorden' => $usuariopreorden,
                            'sucursal' => $detalleOrden->sucursal,
                            'sucursalgasto' => $detalleOrden->sucursalgasto,
                        ]);
                    }
                }
            }
            $detalleOrdenes = DetalleOrdenes::where('ordenid', $orden->id)->get(); 

            $ultimacp = CuentasPagar::orderByRaw("LENGTH(id) DESC, id DESC")
                ->first();
            $nuevoIdcp = $ultimacp ? ((int) filter_var($ultimacp->id, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;

            foreach ($detalleOrdenes as $detalle) {
                $idUnico = $nuevoIdcp . 'CP';

                $preordenConSaldo = PreOrdenes::where('detalle', 'LIKE', '%' . $detalle->ordenid . ')')
                    ->where('codigo', $detalle->codigo)
                    ->where('preciounitario', '>', 0)
                    ->first();

                $subtotalFinal = $detalle->preciounitario;
                if ($preordenConSaldo) {
                    $subtotalFinal -= $preordenConSaldo->preciounitario;
                }

                $cuentasPagar = new CuentasPagar();
                $cuentasPagar->id = $idUnico;
                $cuentasPagar->proveedorid = $orden->proveedorid;
                $cuentasPagar->proveedornombre = $orden->proveedornombre;
                $cuentasPagar->detalleproducto = $detalle->detalle;
                $cuentasPagar->fechaasignada = $orden->fechapagar;
                $cuentasPagar->fechacomprar = $orden->fechacomprar;
                $cuentasPagar->nrobancoorigen = $detalle->nrobancoorigen;
                $cuentasPagar->subtotal = $subtotalFinal;
                $cuentasPagar->descuento = $detalle->descuentounitario;
                $cuentasPagar->montototal = $detalle->totalunitario;
                $cuentasPagar->preciocompra = '0.00';
                $cuentasPagar->sucursalgasto = $detalle->sucursalgasto;
                $cuentasPagar->ciudad = $detalle->sucursal;
                $cuentasPagar->tipoorden = $orden->tipoorden;
                $cuentasPagar->tipoproveedorservicio = $proveedor->categoria;
                $cuentasPagar->ordenid = $orden->id;
                $cuentasPagar->cantidad = $detalle->cantidad;
                $cuentasPagar->estado = 'PENDIENTE';
                $cuentasPagar->usuarioregistroid = $orden->usuarioregistroid;
                $cuentasPagar->usuarioregistronombre = $orden->usuarioregistronombre;
                $cuentasPagar->detalleordenid = $detalle->id;
                $cuentasPagar->save();
                $nuevoIdcp++;
            }

            $pdfData = [
                'ordenesServicio' => $detalleOrdenes,
                'tipotransaccion' => $tipotransaccion,
                'formapago' => $formapago,
                'fechacomprar' => $fechacomprar,
                'fechapagar' => $fechapagar,
                'proveedor' => $proveedornombre,
                'observacion' => $observacion,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'montototal' => $montototal,
                'saldo' => $subtotal - ($descuento + $montototal),
                'usuarioAutenticado' => $usuarioAutenticado,
                'usuarioAutenticadonombre' => $usuarioAutenticadonombre,
                'ordenId' => $orden->id,
                'proveedor' => $proveedor,
                'usuarioregistro' => $usuariopreorden,
            ];

            $pdf = PDF::loadView('admin.inventario.pdfgenerarordenservicio', $pdfData);
            $nombreArchivo = 'ORDEN_SERVICIO_' . $orden->id . '.pdf';

            $directorio = public_path('ordenesaprobadas/' . $usuarioAutenticado);
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }
            
            $rutaArchivo = $directorio . '/' . $nombreArchivo;
            $pdf->save($rutaArchivo);

            $orden->update([
                'documentoorden' => $nombreArchivo,
            ]);

        return redirect()->route('admin.inventario.listaordenes')->with('info', 'Orden de servicio generada con éxito.');
    } */
    public function generarpreordenservicio(Request $request)
    {
        DB::beginTransaction();
        try {

            $ordenesVenta = json_decode($request->input('ordenes_venta'), true);
            $tipotransaccion = $request->input('tipotransaccion3');
            $formapago = $request->input('formapago2');
            $sucursal = $request->input('sucursal2');
            $sucursalgasto = $request->input('sucursalgasto2');
            $fechacomprar = $request->input('fechacomprar2');
            $fechapagar = $request->input('fechapagar2');
            $proveedorid = $request->input('proveedorId2');
            $proveedornombre = $request->input('proveedorNombre2');
            $observacion = $request->input('observacion2');
            $montototal = $request->input('montototalgeneral');
            $subtotal = $request->input('subtotalgeneral');
            $descuento = $request->input('descuentogeneral');
            $bancosDestino = $request->input('bancodestino2');
            $nroBancoOrigen = $request->input('nrocuenta2');
            $usuarioAutenticado = auth()->user()->id;
            $usuarioAutenticadonombre = auth()->user()->name;

            // === VALIDAR PROVEEDOR ===
            $proveedor = Proveedoresservicios::find($proveedorid);
            if (!$proveedor) {
                return redirect()->back()->with('error', 'Proveedor no encontrado');
            }

            // === GENERAR ID PREORDEN ===
            $ultimoNumeroPre = PreOrdenes::select(DB::raw('MAX(CAST(SUBSTRING(preordenid, 1, LENGTH(preordenid) - 2) AS UNSIGNED)) as max_num'))
                ->where('preordenid', 'LIKE', '%PO')
                ->value('max_num');
            $nuevoPreOrdenId = ($ultimoNumeroPre ? ($ultimoNumeroPre + 1) : 1) . 'PO';

            $idsPreOrdenes = [];

            // === CREAR PREORDENES ===
            foreach ($ordenesVenta as $detalle) {
                $preOrden = PreOrdenes::create([
                    'detalle' => $detalle['detalle'],
                    'cantidad' => $detalle['cantidad'],
                    'tipotransaccion' => $tipotransaccion,
                    'preciounitario' => $detalle['precio_unitario'],
                    'descuentounitario' => $detalle['descuento'],
                    'totalunitario' => $detalle['subtotal'],
                    'proveedorid' => $proveedorid,
                    'proveedornombre' => $proveedornombre,
                    'fechacomprar' => $fechacomprar,
                    'fechapagar' => $fechapagar,
                    'tipoorden' => 'ORDEN DE SERVICIO',
                    'estado' => 'PENDIENTE',
                    'formapago' => $formapago,
                    'sucursal' => $sucursal,
                    'sucursalgasto' => $sucursalgasto,
                    'observacion' => $observacion,
                    'preordenid' => $nuevoPreOrdenId,
                    'usuarioregistroid' => $usuarioAutenticado,
                    'usuarioregistronombre' => $usuarioAutenticadonombre,
                ]);
                $idsPreOrdenes[] = $preOrden->id;
            }

            // === GENERAR ID ORDEN ===
            $ultimoNumeroOrden = Ordenes::where('tipoorden', 'ORDEN DE SERVICIO')
                ->where('id', 'LIKE', '%S')
                ->select(DB::raw('MAX(CAST(SUBSTRING(id, 1, LENGTH(id) - 1) AS UNSIGNED)) as max_id'))
                ->value('max_id');
            $nuevoIdOrden = (($ultimoNumeroOrden ?? 0) + 1) . 'S';

            // === CREAR ORDEN DE SERVICIO ===
            $orden = Ordenes::create([
                'id' => $nuevoIdOrden,
                'observacion' => $observacion,
                'tipotransaccion' => $tipotransaccion,
                'formapago' => $formapago,
                'sucursalgasto' => $sucursalgasto,
                'sucursal' => $sucursal,
                'fechacomprar' => $fechacomprar,
                'fechapagar' => $fechapagar,
                'proveedorid' => $proveedorid,
                'proveedornombre' => $proveedor->razonsocial,
                'montototal' => $montototal,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'usuarioregistroid' => $usuarioAutenticado,
                'usuarioregistronombre' => $usuarioAutenticadonombre,
                'tipoorden' => 'ORDEN DE SERVICIO',
                'estado' => 'APROBADO',
                'usuariopreorden' => $usuarioAutenticadonombre,
            ]);

            // === CREAR DETALLE ORDEN Y CUENTAS POR PAGAR ===
            $detallePreOrdenes = PreOrdenes::whereIn('id', $idsPreOrdenes)->get();
            $ultimacp = CuentasPagar::orderByRaw("LENGTH(id) DESC, id DESC")->first();
            $nuevoIdcp = $ultimacp ? ((int) filter_var($ultimacp->id, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;

            foreach ($detallePreOrdenes as $detalle) {
                $cuentaDestino = $bancosDestino[$detalle->id] ?? null;

                // Crear DetalleOrden
                $detalleOrden = DetalleOrdenes::create([
                    'detalle' => $detalle->detalle,
                    'cantidad' => $detalle->cantidad,
                    'preciounitario' => $detalle->preciounitario,
                    'codigo' => $detalle->codigo,
                    'descuentounitario' => $detalle->descuentounitario,
                    'totalunitario' => $detalle->totalunitario,
                    'tipoorden' => 'ORDEN DE SERVICIO',
                    'ordenid' => $orden->id,
                    'observacion' => $observacion,
                    'nrobancoorigen' => $cuentaDestino,
                    'tipotransaccion' => $tipotransaccion,
                    'formapago' => $formapago,
                    'fechacomprar' => $fechacomprar,
                    'fechapagar' => $fechapagar,
                    'proveedorid' => $proveedorid,
                    'proveedornombre' => $proveedor->razonsocial,
                    'usuarioregistroid' => $usuarioAutenticado,
                    'usuarioregistronombre' => $usuarioAutenticadonombre,
                    'usuariopreorden' => $usuarioAutenticadonombre,
                ]);

                /* $proveedorBanco = Proveedoresservicios::find($orden->proveedorid);
                $nroBancoOrigen = 0;
                if ($proveedorBanco) {
                    if ($proveedorBanco->bancoorigen === 'CUENTA FACTURADA') {
                        $nroBancoOrigen = '3000189269';
                    } elseif ($proveedorBanco->bancoorigen === 'CUENTA NO FACTURADA') {
                        $nroBancoOrigen = '2505314878';
                    }
                } */

                // Crear CuentasPagar
                $idUnicoCP = $nuevoIdcp . 'CP';
                CuentasPagar::create([
                    'id' => $idUnicoCP,
                    'proveedorid' => $orden->proveedorid,
                    'nrobancoorigen'=> $nroBancoOrigen,
                    'proveedornombre' => $orden->proveedornombre,
                    'detalleproducto' => $detalleOrden->detalle,
                    'fechaasignada' => $orden->fechapagar,
                    'fechacomprar' => $orden->fechacomprar,
                    'subtotal' => $detalleOrden->preciounitario,
                    'descuento' => $detalleOrden->descuentounitario,
                    'montototal' => $detalleOrden->totalunitario,
                    'preciocompra' => '0.00',
                    'sucursalgasto' => $orden->sucursalgasto,
                    'ciudad' => $sucursal,
                    'tipoorden' => $orden->tipoorden,
                    'tipoproveedorservicio' => $proveedor->categoria,
                    'ordenid' => $orden->id,
                    'cantidad' => $detalleOrden->cantidad,
                    'estado' => 'PENDIENTE',
                    'usuarioregistroid' => $orden->usuarioregistroid,
                    'usuarioregistronombre' => $orden->usuarioregistronombre,
                    'detalleordenid' => $detalleOrden->id,
                ]);
                $nuevoIdcp++;
            }

            // === GENERAR PDF ===
            $pdfData = [
                'ordenesServicio' => $detallePreOrdenes,
                'tipotransaccion' => $tipotransaccion,
                'formapago' => $formapago,
                'fechacomprar' => $fechacomprar,
                'fechapagar' => $fechapagar,
                'proveedor' => $proveedor,
                'observacion' => $observacion,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'montototal' => $montototal,
                'saldo' => $subtotal - ($descuento + $montototal),
                'usuarioAutenticado' => $usuarioAutenticado,
                'usuarioAutenticadonombre' => $usuarioAutenticadonombre,
                'ordenId' => $orden->id,
                'usuarioregistro' => $usuarioAutenticadonombre,
            ];

            $pdf = PDF::loadView('admin.inventario.pdfgenerarordenservicio', $pdfData);
            $nombreArchivo = 'ORDEN_SERVICIO_' . $orden->id . '.pdf';
            $directorio = public_path('ordenesaprobadas/' . $usuarioAutenticado);
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }
            $rutaArchivo = $directorio . '/' . $nombreArchivo;
            $pdf->save($rutaArchivo);

            $orden->update(['documentoorden' => $nombreArchivo]);

            DB::commit();
            return redirect()->route('admin.inventario.crearordenes')->with('info', 'Pre-orden y orden de compra generadas con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    /* public function generarpreordenpersonal(Request $request)  
    {
        $ordenesVenta = json_decode($request->input('ordenes_venta'), true);
        $tipotransaccion = $request->input('tipotransaccion32');
        $formapago = $request->input('formapago22');
        $sucursal = $request->input('sucursal22');
        $sucursalgasto = $request->input('sucursalgasto22');
        $fechacomprar = $request->input('fechacomprar22');
        $fechapagar = $request->input('fechapagar22');
        $proveedorid = $request->input('proveedorId22');
        $observacion = $request->input('observacion22');
        $proveedornombre = $request->input('proveedorNombre22');
        $proveedorid = $request->input('proveedorId22');
        $usuarioAutenticado = auth()->user()->id;
        $usuarioAutenticadonombre = auth()->user()->name;

        $ultimoNumero = PreOrdenes::select(DB::raw('MAX(CAST(SUBSTRING(preordenid, 1, LENGTH(preordenid) - 2) AS UNSIGNED)) as max_num'))
            ->where('preordenid', 'LIKE', '%PO')
            ->value('max_num');

        $nuevoPreOrdenId = ($ultimoNumero ? ($ultimoNumero + 1) : 1) . 'PO';

        foreach ($ordenesVenta as $detalle) {
            PreOrdenes::create([
                'detalle' => $detalle['detalle'],
                'cantidad' => $detalle['cantidad'],
                'tipotransaccion' => $tipotransaccion,
                'preciounitario' => $detalle['precio_unitario'],
                'descuentounitario' => $detalle['descuento'],
                'totalunitario' => $detalle['subtotal'],
                'proveedorid' => $proveedorid,
                'proveedornombre' => $proveedornombre,
                'fechacomprar' => $fechacomprar,
                'fechapagar' => $fechapagar,
                'tipoorden' => 'ORDEN DE PERSONAL',
                'estado' => 'PENDIENTE',
                'formapago' => $formapago,
                'sucursal' => $sucursal,
                'sucursalgasto' => $sucursalgasto,
                'observacion' => $observacion,
                'preordenid' => $nuevoPreOrdenId,
                'usuarioregistroid' => $usuarioAutenticado,
                'usuarioregistronombre' => $usuarioAutenticadonombre,
            ]);
        }
        
        return redirect()->route('admin.inventario.crearordenes')->with('info', 'Pre - Orden de servicio registrada con éxito.');
    }
    public function generarOrdenpersonal(Request $request)
    {
        $tipotransaccion = $request->input('tipotransaccion');
        $formapago = $request->input('formapago');
        $sucursalgasto = $request->input('sucursalgasto3');
        $fechacomprar = $request->input('fechacomprar');
        $fechapagar = $request->input('fechapagar');
        $proveedorid = $request->input('proveedorid');
        $proveedornombre = $request->input('proveedornombre');
        $observacion = $request->input('observacion');
        $montototal = $request->input('montototal');
        $subtotal = $request->input('subtotal');
        $descuento = $request->input('descuento');
        $usuariopreorden = $request->input('usuariopreorden');
        $usuarioAutenticado = auth()->user()->id;
        $usuarioAutenticadonombre = auth()->user()->name;
        $bancosDestino = $request->input('bancodestino3');
        $sucursal = auth()->user()->sucursal;
        $totalunitariosEditados = $request->input('totalunitario3');

        $proveedor = Proveedoresservicios::find($proveedorid);
        if (!$proveedor) {
            return redirect()->back()->with('error', 'Proveedor no encontrado');
        }

        $ultimoNumero = Ordenes::where('tipoorden', 'ORDEN DE PERSONAL')
            ->where('id', 'LIKE', '%P')
            ->select(DB::raw('MAX(CAST(SUBSTRING(id, 1, LENGTH(id) - 1) AS UNSIGNED)) as max_id'))
            ->value('max_id');
        $nuevoIdConSufijo = (($ultimoNumero ?? 0) + 1) . 'P';

        $ordenData = [
            'id' => $nuevoIdConSufijo,
            'observacion' => $observacion,
            'tipotransaccion' => $tipotransaccion,
            'formapago' => $formapago,
            'sucursalgasto' => $sucursalgasto,
            'sucursal' => $sucursal,
            'fechacomprar' => $fechacomprar,
            'fechapagar' => $fechapagar,
            'proveedorid' => $proveedorid,
            'proveedornombre' => $proveedornombre,
            'montototal' => $montototal,
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'observacion' => $observacion,
            'usuarioregistroid' => $usuarioAutenticado,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
            'tipoorden' => 'ORDEN DE PERSONAL',
            'estado' => 'APROBADO',
            'usuariopreorden' => $usuariopreorden,
        ];

        $orden = Ordenes::create($ordenData);
        $ordenesPersonal = $request->input('ordenes3');
        $preOrdenesSeleccionadas = PreOrdenes::whereIn('id', $ordenesPersonal)->get();
        $agrupadosPorPreordenId = $preOrdenesSeleccionadas->groupBy('preordenid');
        foreach ($agrupadosPorPreordenId as $preordenid => $preOrdenes) {
            $codigosSeleccionados = $preOrdenes->pluck('id')->toArray();

            PreOrdenes::where('preordenid', $preordenid)->update([
                'estado' => 'RECHAZADO'
            ]);

            PreOrdenes::whereIn('id', $codigosSeleccionados)->update([
                'estado' => 'APROBADO'
            ]);
        }
            if ($ordenesPersonal) {
                $detallesPreOrdenes = [];

                foreach ($ordenesPersonal as $id) {
                    $detalleOrden = PreOrdenes::where('id', $id)->first();

                    $totalUnitarioFinal = $totalunitariosEditados[$id] ?? $detalleOrden->totalunitario;
                    $totalUnitarioFinal = str_replace(',', '', $totalUnitarioFinal);
                    $totalUnitarioFinal = floatval($totalUnitarioFinal);

                    if ($detalleOrden) {
                        $detallesPreOrdenes[] = $detalleOrden;
                        $cuentaSeleccionada = $bancosDestino[$id] ?? null;

                        DetalleOrdenes::create([
                            'detalle' => $detalleOrden->detalle,
                            'cantidad' => $detalleOrden->cantidad,
                            'preciounitario' => $detalleOrden->preciounitario,
                            'codigo' => $detalleOrden->codigo,
                            'descuentounitario' => $detalleOrden->descuentounitario,
                            'totalunitario' => $totalUnitarioFinal,
                            'tipoorden' => 'ORDEN DE PERSONAL',
                            'ordenid' => $orden->id,
                            'observacion' => $observacion,
                            'nrobancoorigen' => $cuentaSeleccionada,
                            'tipotransaccion' => $tipotransaccion,
                            'formapago' => $formapago,
                            'fechacomprar' => $fechacomprar,
                            'fechapagar' => $fechapagar,
                            'proveedorid' => $proveedorid,
                            'proveedornombre' => $proveedornombre,
                            'usuarioregistroid' => $usuarioAutenticado,
                            'usuarioregistronombre' => $usuarioAutenticadonombre,
                            'usuariopreorden' => $usuariopreorden,
                        ]);

                        $sumaTotalYDescuento = floatval($detalleOrden->descuentounitario) + $totalUnitarioFinal;
                        $precioOriginal = floatval($detalleOrden->preciounitario);

                        if (round($sumaTotalYDescuento, 2) < round($precioOriginal, 2)) {
                            $saldoFaltante = $precioOriginal - $sumaTotalYDescuento;
                            $ultimoNumero = PreOrdenes::select(DB::raw('MAX(CAST(SUBSTRING(preordenid, 1, LENGTH(preordenid) - 2) AS UNSIGNED)) as max_num'))
                                ->where('preordenid', 'LIKE', '%PO')
                                ->value('max_num');

                            $nuevoPreOrdenId = (($ultimoNumero ? $ultimoNumero + 1 : 1) . 'PO');

                            PreOrdenes::create([
                                'detalle' => $detalleOrden->detalle . ' (SALDO ' . $orden->id .')',
                                'cantidad' => 0,
                                'tipotransaccion' => $tipotransaccion,
                                'preciounitario' => $saldoFaltante,
                                'descuentounitario' => 0,
                                'totalunitario' => $saldoFaltante,
                                'proveedorid' => $proveedorid,
                                'proveedornombre' => $proveedornombre,
                                'fechacomprar' => $fechacomprar,
                                'fechapagar' => $fechapagar,
                                'tipoorden' => 'ORDEN DE PERSONAL',
                                'estado' => 'PENDIENTE',
                                'formapago' => $formapago,
                                'sucursal' => $sucursal,
                                'sucursalgasto' => $sucursalgasto,
                                'observacion' => $observacion,
                                'preordenid' => $nuevoPreOrdenId,
                                'usuarioregistroid' => $usuarioAutenticado,
                                'usuarioregistronombre' => $usuarioAutenticadonombre,
                            ]);
                        }
                    }
                }
            }

            $detalleOrdenes = DetalleOrdenes::where('ordenid', $orden->id)->get(); 

            $ultimacp = CuentasPagar::orderByRaw("LENGTH(id) DESC, id DESC")
                ->first();
            $nuevoIdcp = $ultimacp ? ((int) filter_var($ultimacp->id, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;

            foreach ($detalleOrdenes as $detalle) {
                $idUnico = $nuevoIdcp . 'CP';

                $preordenConSaldo = PreOrdenes::where('detalle', 'LIKE', '%' . $detalle->ordenid . ')')
                    ->where('codigo', $detalle->codigo)
                    ->where('preciounitario', '>', 0)
                    ->first();

                $subtotalFinal = $detalle->preciounitario;
                if ($preordenConSaldo) {
                    $subtotalFinal -= $preordenConSaldo->preciounitario;
                }
                
                $cuentasPagar = new CuentasPagar();
                $cuentasPagar->id = $idUnico;
                $cuentasPagar->proveedorid = $orden->proveedorid;
                $cuentasPagar->proveedornombre = $orden->proveedornombre;
                $cuentasPagar->detalleproducto = $detalle->detalle;
                $cuentasPagar->fechaasignada = $orden->fechapagar;
                $cuentasPagar->fechacomprar = $orden->fechacomprar;
                $cuentasPagar->nrobancoorigen = $detalle->nrobancoorigen;
                $cuentasPagar->subtotal = $subtotalFinal;
                $cuentasPagar->descuento = $detalle->descuentounitario;
                $cuentasPagar->montototal = $detalle->totalunitario;
                $cuentasPagar->preciocompra = '0.00';
                $cuentasPagar->sucursalgasto = $orden->sucursalgasto;
                $cuentasPagar->ciudad = $sucursal;
                $cuentasPagar->tipoorden = $orden->tipoorden;
                $cuentasPagar->tipoproveedorservicio = $proveedor->categoria;
                $cuentasPagar->ordenid = $orden->id;
                $cuentasPagar->cantidad = $detalle->cantidad;
                $cuentasPagar->estado = 'PENDIENTE';
                $cuentasPagar->usuarioregistroid = $orden->usuarioregistroid;
                $cuentasPagar->usuarioregistronombre = $orden->usuarioregistronombre;
                $cuentasPagar->detalleordenid = $detalle->id;
                $cuentasPagar->save();
                $nuevoIdcp++;
            }

            $pdfData = [
                'ordenesPersonal' => $detalleOrdenes,
                'tipotransaccion' => $tipotransaccion,
                'formapago' => $formapago,
                'fechacomprar' => $fechacomprar,
                'fechapagar' => $fechapagar,
                'proveedor' => $proveedornombre,
                'observacion' => $observacion,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'montototal' => $montototal,
                'saldo' => $subtotal - ($descuento + $montototal),
                'usuarioAutenticado' => $usuarioAutenticado,
                'usuarioAutenticadonombre' => $usuarioAutenticadonombre,
                'ordenId' => $orden->id,
                'proveedor' => $proveedor,
                'usuarioregistro' => $usuariopreorden,
            ];

            $pdf = PDF::loadView('admin.inventario.pdfgenerarordenpersonal', $pdfData);
            $nombreArchivo = 'ORDEN_PERSONAL_' . $orden->id . '.pdf';

            $directorio = public_path('ordenesaprobadas/' . $usuarioAutenticado);
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }
            
            $rutaArchivo = $directorio . '/' . $nombreArchivo;
            $pdf->save($rutaArchivo);

            $orden->update([
                'documentoorden' => $nombreArchivo,
            ]);

        return redirect()->route('admin.inventario.listaordenes')->with('info', 'Orden de personal generada con éxito.');
    } */
    public function generarpreordenpersonal(Request $request)
    {
        DB::beginTransaction();
        try {

            $ordenesVenta = json_decode($request->input('ordenes_venta'), true);
            $tipotransaccion = $request->input('tipotransaccion32');
            $formapago = $request->input('formapago22');
            $sucursal = $request->input('sucursal22');
            $sucursalgasto = $request->input('sucursalgasto22');
            $fechacomprar = $request->input('fechacomprar22');
            $fechapagar = $request->input('fechapagar22');
            $proveedorid = $request->input('proveedorId22');
            $proveedornombre = $request->input('proveedorNombre22');
            $observacion = $request->input('observacion22');
            $montototal = $request->input('montototalgeneral2');
            $subtotal = $request->input('subtotalgeneral2');
            $descuento = $request->input('descuentogeneral2');
            $bancosDestino = $request->input('bancodestino3');
            $nroBancoOrigen = $request->input('nrocuenta3');
            $usuarioAutenticado = auth()->user()->id;
            $usuarioAutenticadonombre = auth()->user()->name;

            // === VALIDAR PROVEEDOR ===
            $proveedor = Proveedoresservicios::find($proveedorid);
            if (!$proveedor) {
                return redirect()->back()->with('error', 'Proveedor no encontrado');
            }

            // === GENERAR ID PREORDEN ===
            $ultimoNumeroPre = PreOrdenes::select(DB::raw('MAX(CAST(SUBSTRING(preordenid, 1, LENGTH(preordenid) - 2) AS UNSIGNED)) as max_num'))
                ->where('preordenid', 'LIKE', '%PO')
                ->value('max_num');
            $nuevoPreOrdenId = ($ultimoNumeroPre ? ($ultimoNumeroPre + 1) : 1) . 'PO';

            $idsPreOrdenes = [];

            // === CREAR PREORDENES ===
            foreach ($ordenesVenta as $detalle) {
                $preOrden = PreOrdenes::create([
                    'detalle' => $detalle['detalle'],
                    'cantidad' => $detalle['cantidad'],
                    'tipotransaccion' => $tipotransaccion,
                    'preciounitario' => $detalle['precio_unitario'],
                    'descuentounitario' => $detalle['descuento'],
                    'totalunitario' => $detalle['subtotal'],
                    'proveedorid' => $proveedorid,
                    'proveedornombre' => $proveedornombre,
                    'fechacomprar' => $fechacomprar,
                    'fechapagar' => $fechapagar,
                    'tipoorden' => 'ORDEN DE PERSONAL',
                    'estado' => 'PENDIENTE',
                    'formapago' => $formapago,
                    'sucursal' => $sucursal,
                    'sucursalgasto' => $sucursalgasto,
                    'observacion' => $observacion,
                    'preordenid' => $nuevoPreOrdenId,
                    'usuarioregistroid' => $usuarioAutenticado,
                    'usuarioregistronombre' => $usuarioAutenticadonombre,
                ]);
                $idsPreOrdenes[] = $preOrden->id;
            }

            // === GENERAR ID ORDEN ===
            $ultimoNumeroOrden = Ordenes::where('tipoorden', 'ORDEN DE PERSONAL')
                ->where('id', 'LIKE', '%P')
                ->select(DB::raw('MAX(CAST(SUBSTRING(id, 1, LENGTH(id) - 1) AS UNSIGNED)) as max_id'))
                ->value('max_id');
            $nuevoIdOrden = (($ultimoNumeroOrden ?? 0) + 1) . 'P';

            // === CREAR ORDEN DE SERVICIO ===
            $orden = Ordenes::create([
                'id' => $nuevoIdOrden,
                'observacion' => $observacion,
                'tipotransaccion' => $tipotransaccion,
                'formapago' => $formapago,
                'sucursalgasto' => $sucursalgasto,
                'sucursal' => $sucursal,
                'fechacomprar' => $fechacomprar,
                'fechapagar' => $fechapagar,
                'proveedorid' => $proveedorid,
                'proveedornombre' => $proveedor->razonsocial,
                'montototal' => $montototal,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'usuarioregistroid' => $usuarioAutenticado,
                'usuarioregistronombre' => $usuarioAutenticadonombre,
                'tipoorden' => 'ORDEN DE PERSONAL',
                'estado' => 'APROBADO',
                'usuariopreorden' => $usuarioAutenticadonombre,
            ]);

            // === CREAR DETALLE ORDEN Y CUENTAS POR PAGAR ===
            $detallePreOrdenes = PreOrdenes::whereIn('id', $idsPreOrdenes)->get();
            $ultimacp = CuentasPagar::orderByRaw("LENGTH(id) DESC, id DESC")->first();
            $nuevoIdcp = $ultimacp ? ((int) filter_var($ultimacp->id, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;

            foreach ($detallePreOrdenes as $detalle) {
                $cuentaDestino = $bancosDestino[$detalle->id] ?? null;

                // Crear DetalleOrden
                $detalleOrden = DetalleOrdenes::create([
                    'detalle' => $detalle->detalle,
                    'cantidad' => $detalle->cantidad,
                    'preciounitario' => $detalle->preciounitario,
                    'codigo' => $detalle->codigo,
                    'descuentounitario' => $detalle->descuentounitario,
                    'totalunitario' => $detalle->totalunitario,
                    'tipoorden' => 'ORDEN DE PERSONAL',
                    'ordenid' => $orden->id,
                    'observacion' => $observacion,
                    'nrobancoorigen' => $cuentaDestino,
                    'tipotransaccion' => $tipotransaccion,
                    'formapago' => $formapago,
                    'fechacomprar' => $fechacomprar,
                    'fechapagar' => $fechapagar,
                    'proveedorid' => $proveedorid,
                    'proveedornombre' => $proveedor->razonsocial,
                    'usuarioregistroid' => $usuarioAutenticado,
                    'usuarioregistronombre' => $usuarioAutenticadonombre,
                    'usuariopreorden' => $usuarioAutenticadonombre,
                ]);

                /* $proveedorBanco = Proveedoresservicios::find($orden->proveedorid);
                $nroBancoOrigen = 0;
                if ($proveedorBanco) {
                    if ($proveedorBanco->bancoorigen === 'CUENTA FACTURADA') {
                        $nroBancoOrigen = '3000189269';
                    } elseif ($proveedorBanco->bancoorigen === 'CUENTA NO FACTURADA') {
                        $nroBancoOrigen = '2505314878';
                    }
                } */
                
                // Crear CuentasPagar
                $idUnicoCP = $nuevoIdcp . 'CP';
                CuentasPagar::create([
                    'id' => $idUnicoCP,
                    'proveedorid' => $orden->proveedorid,
                    'nrobancoorigen'=> $nroBancoOrigen,
                    'proveedornombre' => $orden->proveedornombre,
                    'detalleproducto' => $detalleOrden->detalle,
                    'fechaasignada' => $orden->fechapagar,
                    'fechacomprar' => $orden->fechacomprar,
                    'subtotal' => $detalleOrden->preciounitario,
                    'descuento' => $detalleOrden->descuentounitario,
                    'montototal' => $detalleOrden->totalunitario,
                    'preciocompra' => '0.00',
                    'sucursalgasto' => $orden->sucursalgasto,
                    'ciudad' => $sucursal,
                    'tipoorden' => $orden->tipoorden,
                    'tipoproveedorservicio' => $proveedor->categoria,
                    'ordenid' => $orden->id,
                    'cantidad' => $detalleOrden->cantidad,
                    'estado' => 'PENDIENTE',
                    'usuarioregistroid' => $orden->usuarioregistroid,
                    'usuarioregistronombre' => $orden->usuarioregistronombre,
                    'detalleordenid' => $detalleOrden->id,
                ]);
                $nuevoIdcp++;
            }

            // === GENERAR PDF ===
            $pdfData = [
                'ordenesPersonal' => $detallePreOrdenes,
                'tipotransaccion' => $tipotransaccion,
                'formapago' => $formapago,
                'fechacomprar' => $fechacomprar,
                'fechapagar' => $fechapagar,
                'proveedor' => $proveedornombre,
                'observacion' => $observacion,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'montototal' => $montototal,
                'saldo' => $subtotal - ($descuento + $montototal),
                'usuarioAutenticado' => $usuarioAutenticado,
                'usuarioAutenticadonombre' => $usuarioAutenticadonombre,
                'ordenId' => $orden->id,
                'usuarioregistro' => $usuarioAutenticadonombre,
            ];

            $pdf = PDF::loadView('admin.inventario.pdfgenerarordenpersonal', $pdfData);
            $nombreArchivo = 'ORDEN_PERSONAL_' . $orden->id . '.pdf';
            $directorio = public_path('ordenesaprobadas/' . $usuarioAutenticado);
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }
            $rutaArchivo = $directorio . '/' . $nombreArchivo;
            $pdf->save($rutaArchivo);

            $orden->update(['documentoorden' => $nombreArchivo]);

            DB::commit();
            return redirect()->route('admin.inventario.crearordenes')->with('info', 'Pre-orden y orden de compra generadas con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
//
    /* APROBAR CON FIRMA Y SELLO */
    /* public function aprobar($id)
    {
        $orden = Ordenes::find($id);
        if (!$orden) {
            return redirect()->back()->with('error', 'Orden no encontrada');
        }
        $orden->estado = 'APROBADO';
        $orden->save();
        $usuarioAutenticado = auth()->user()->id;
        $pdfPath = public_path("ordenes/{$usuarioAutenticado}/{$orden->documentoorden}");
        if (!file_exists($pdfPath)) {
            return redirect()->back()->with('error', 'PDF no encontrado');
        }
        $firmaPath = public_path("glfirmasello/{$usuarioAutenticado}/firmadigital.png");
        $selloPath = public_path("glfirmasello/{$usuarioAutenticado}/sellodigital.png");
        if (!file_exists($firmaPath) || !file_exists($selloPath)) {
            return redirect()->back()->with('error', 'Firma o sello no encontrados');
        }
        $pdf = new Fpdi();

        $pdf->setSourceFile($pdfPath);
        $totalPages = $pdf->setSourceFile($pdfPath);

        for ($pagina = 1; $pagina <= $totalPages; $pagina++) {
            $pdf->AddPage();
            $templateId = $pdf->importPage($pagina);
            $pdf->useTemplate($templateId);

            if ($pagina == $totalPages) {
                $pdf->Image($firmaPath, 150, 250, 40);
                $pdf->Image($selloPath, 50, 250, 40);
            }
        }
        $nuevoNombreArchivo = 'ORDEN_COMPRA_APROBADO_' . $orden->id . '.pdf';
        $nuevoPdfPath = public_path("ordenes/{$usuarioAutenticado}/{$nuevoNombreArchivo}");
        $pdf->Output('F', $nuevoPdfPath);
        $orden->documentoorden = $nuevoNombreArchivo;
        $orden->save();

        $cuentasPagar = new CuentasPagar();
        $cuentasPagar->proveedorid = $orden->proveedorid;
        $cuentasPagar->proveedornombre = $orden->proveedornombre;
        $cuentasPagar->detalle = $orden->detalle;
        $cuentasPagar->fechaasignada = $orden->fechacomprar;
        $cuentasPagar->subtotal = $orden->subtotal;
        $cuentasPagar->descuento = $orden->descuento;
        $cuentasPagar->montototal = $orden->montototal;
        $cuentasPagar->preciocompra = $orden->montototal;
        $cuentasPagar->tipoorden = $orden->tipoorden;
        $cuentasPagar->ordenid = $orden->id;
        $cuentasPagar->estado = 'PENDIENTE';
        $cuentasPagar->usuarioregistroid = $orden->usuarioregistroid;
        $cuentasPagar->usuarioregistronombre = $orden->usuarioregistronombre;
        $cuentasPagar->save();

        return redirect()->back()->with('info', 'Orden aprobada y PDF actualizado con firma y sello');
    } */

    /* APROBAR O RECHAZAR PRE ORDENES */
    public function aprobar($id)  
    {
        $orden = Ordenes::find($id);
        if (!$orden) {
            return redirect()->back()->with('error', 'Orden no encontrada');
        }
        $orden->estado = 'APROBADO';
        $orden->save();

        $detalleOrdenes = DetalleOrdenes::where('ordenid', $orden->id)->get();

        if ($detalleOrdenes->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron detalles para esta orden');
        }

        $usuarioAutenticado = auth()->user()->id;
        $pdfPath = public_path("ordenes/{$usuarioAutenticado}/{$orden->documentoorden}");
        if (!file_exists($pdfPath)) {
            return redirect()->back()->with('error', 'PDF no encontrado');
        }

        $pdf = new Fpdi(); 
        $totalPages = $pdf->setSourceFile($pdfPath);

        for ($pagina = 1; $pagina <= $totalPages; $pagina++) {
            $pdf->AddPage();
            $templateId = $pdf->importPage($pagina);
            $pdf->useTemplate($templateId);

            $posX = 12;
            $posY = 270;
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY($posX, $posY);
            $pdf->Cell(25, 5, 'Autorizado por: ', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(0, 5, auth()->user()->name, 0, 1, 'L');
        }

        $nuevoNombreArchivo = 'ORDEN_COMPRA_APROBADO_' . $orden->id . '.pdf';
        $nuevoPdfPath = public_path("ordenes/{$usuarioAutenticado}/{$nuevoNombreArchivo}");
        $pdf->Output('F', $nuevoPdfPath);
        $orden->documentoorden = $nuevoNombreArchivo;
        $orden->save();

        foreach ($detalleOrdenes as $detalle) {
            $cuentasPagar = new CuentasPagar();
            $cuentasPagar->proveedorid = $orden->proveedorid;
            $cuentasPagar->proveedornombre = $orden->proveedornombre;
            $cuentasPagar->detalleproducto = $detalle->detalle;
            $cuentasPagar->fechaasignada = $orden->fechapagar;
            $cuentasPagar->fechacomprar = $orden->fechacomprar;
            $cuentasPagar->subtotal = $detalle->preciounitario;
            $cuentasPagar->descuento = $detalle->descuentounitario;
            $cuentasPagar->montototal = $detalle->totalunitario;
            $cuentasPagar->preciocompra = $detalle->totalunitario;
            $cuentasPagar->tipoorden = $orden->tipoorden;
            $cuentasPagar->ordenid = $orden->id;
            $cuentasPagar->cantidad = $detalle->cantidad;
            $cuentasPagar->estado = 'PENDIENTE';
            $cuentasPagar->usuarioregistroid = $orden->usuarioregistroid;
            $cuentasPagar->usuarioregistronombre = $orden->usuarioregistronombre;
            $cuentasPagar->save();
        }

        return redirect()->back()->with('info', 'Orden aprobada y registros de Cuentas por Pagar creados');
    }
    public function rechazar($id)
    {
        $orden = Ordenes::find($id);

        if (!$orden) {
            return redirect()->back()->with('error', 'Orden no encontrada');
        }
        $orden->estado = 'RECHAZADO';
        $orden->save();

        return redirect()->back()->with('info', 'Orden rechazada exitosamente');
    }
    public function listaordenes(Request $request) 
    {
        $nombreproducto = $request->get('buscarpor');
        $ordenesaprobadas = Ordenes::where('tipoorden', 'ORDEN DE COMPRA')
        ->whereNull('deleted_at')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.ordenid', 'ordenes.id')
                    ->whereNull('detallerecibos.deleted_at')
                    ->groupBy('detallerecibos.ordenid')
                    ->havingRaw('SUM(CASE WHEN estado != "PAGO PROCESADO" THEN 1 ELSE 0 END) = 0');
            })
            ->orderBy('created_at', 'asc')
        ->get();

        $ordenesaprobadasprocesadas = Ordenes::where('tipoorden', 'ORDEN DE COMPRA')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.ordenid', 'ordenes.id');
            })
            ->with('detallesrecibos')
            ->orderBy('created_at', 'asc')
        ->get();
        
        $ordenes = PreOrdenes::where('tipoorden', 'ORDEN DE COMPRA')
            ->with(['portafolio', 'inventario', 'proveedorServicio'])
            ->get()
            ->groupBy('preordenid')
            ->sortBy(function ($grupo) {
            $orden = $grupo->first(); // tomamos el primer elemento del grupo
            return [
                $orden->prioridad === 'PRIORITARIO' ? 0 : 1, // PRIORITARIO primero
                $orden->fechapagar ?? now()->addYears(10)    // luego por fecha de pago
            ];
        });

        $usuarios = User::has('roles')
                        ->orderBy('name', 'asc')
        ->get();

        $subtotal = 0;
        $descuentoTotal = 0;
        $montoTotal = 0;

        $ordenesaprobadasservicio = Ordenes::where('tipoorden', 'ORDEN DE SERVICIO')
        ->whereNull('deleted_at')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.ordenid', 'ordenes.id')
                    ->whereNull('detallerecibos.deleted_at')
                    ->groupBy('detallerecibos.ordenid')
                    ->havingRaw('SUM(CASE WHEN estado != "PAGO PROCESADO" THEN 1 ELSE 0 END) = 0');
            })
            ->orderBy('created_at', 'asc')
        ->get();

        $ordenesaprobadasprocesadasservicio = Ordenes::where('tipoorden', 'ORDEN DE SERVICIO')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.ordenid', 'ordenes.id');
            })
            ->with('detallesrecibos')
            ->orderBy('created_at', 'asc')
        ->get();
        
        $ordenesservicio = PreOrdenes::where('tipoorden', 'ORDEN DE SERVICIO')
            ->with(['portafolio', 'inventario'])
            ->get()
            ->groupBy('preordenid')
            ->sortBy(function ($grupo) {
            $orden = $grupo->first(); // tomamos el primer elemento del grupo
            return [
                $orden->prioridad === 'PRIORITARIO' ? 0 : 1, // PRIORITARIO primero
                $orden->fechapagar ?? now()->addYears(10)    // luego por fecha de pago
            ];
        });

        $usuariosservicio = User::has('roles')
                        ->orderBy('name', 'asc')
        ->get();

        $subtotalservicio = 0;
        $descuentoTotalservicio = 0;
        $montoTotalservicio = 0;

        $ordenesaprobadaspersonal = Ordenes::where('tipoorden', 'ORDEN DE PERSONAL')
        ->whereNull('deleted_at')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.ordenid', 'ordenes.id')
                    ->whereNull('detallerecibos.deleted_at')
                    ->groupBy('detallerecibos.ordenid')
                    ->havingRaw('SUM(CASE WHEN estado != "PAGO PROCESADO" THEN 1 ELSE 0 END) = 0');
            })
            ->orderBy('created_at', 'asc')
        ->get();

        $ordenesaprobadasprocesadaspersonal = Ordenes::where('tipoorden', 'ORDEN DE PERSONAL')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.ordenid', 'ordenes.id');
            })
            ->with('detallesrecibos')
            ->orderBy('created_at', 'asc')
        ->get();
        
        $ordenespersonal = PreOrdenes::where('tipoorden', 'ORDEN DE PERSONAL')
            ->with(['portafolio', 'inventario'])
            ->get()
            ->groupBy('preordenid')
            ->sortBy(function ($grupo) {
            $orden = $grupo->first(); // tomamos el primer elemento del grupo
            return [
                $orden->prioridad === 'PRIORITARIO' ? 0 : 1, // PRIORITARIO primero
                $orden->fechapagar ?? now()->addYears(10)    // luego por fecha de pago
            ];
        });

        $usuariospersonal = User::has('roles')
                        ->orderBy('name', 'asc')
        ->get();

        $subtotalpersonal = 0;
        $descuentoTotalpersonal = 0;
        $montoTotalpersonal = 0;

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

        $cuentas = CuentasBancos::where('estado', 'ACTIVO')->get();

        $cuentaporpagar1 = DB::table('cuentasporpagar')
            ->where('estado', 'PENDIENTE')
            ->where('estadoaprobacion', null)
            ->whereNotNull('updated_at')
            ->where('nrobancoorigen', '3000189269')
        ->sum('montototal');
        $cuentaporpagar2 = DB::table('cuentasporpagar')
            ->where('estado', 'PENDIENTE')
            ->where('estadoaprobacion', null)
            ->whereNotNull('updated_at')
            ->where('nrobancoorigen', '2505314878')
        ->sum('montototal');
        $cuentaporpagar3 = DB::table('cuentasporpagar')
            ->where('estado', 'PENDIENTE')
            ->where('estadoaprobacion', null)
            ->whereNotNull('updated_at')
            ->where('nrobancoorigen', '4011113557')
        ->sum('montototal');
        $programacioncuentaporpagar1 = DB::table('bateriasubclientes')
            ->where('prioridad', 'CUENTA POR PAGAR')
            ->where('estadoaprobacion', null)
            ->where('nrobancoorigen', '3000189269')
        ->sum('preciocompra');
        $programacioncuentaporpagar2 = DB::table('bateriasubclientes')
            ->where('prioridad', 'CUENTA POR PAGAR')
            ->where('estadoaprobacion', null)
            ->where('nrobancoorigen', '2505314878')
        ->sum('preciocompra');
        $programacioncuentaporpagar3 = DB::table('bateriasubclientes')
            ->where('prioridad', 'CUENTA POR PAGAR')
            ->where('estadoaprobacion', null)
            ->where('nrobancoorigen', '4011113557')
        ->sum('preciocompra');
        
        $saldoanteriorcuenta1 = '-174910.55';
        $saldoanteriorcuenta2 = '34508.22';
        $saldoanteriorcuenta3 = '3500.00';

        $cuentasConSaldo = [
            '3000189269' => $saldoanteriorcuenta1 + $totalCuenta1Ingreso - $totalCuenta1Egreso - $cuentaporpagar1 - $programacioncuentaporpagar1,
            '2505314878' => $saldoanteriorcuenta2 + $totalCuenta2Ingreso - $totalCuenta2Egreso - $cuentaporpagar2 - $programacioncuentaporpagar2,
            '4011113557' => $saldoanteriorcuenta3 + $totalCuenta3Ingreso - $totalCuenta3Egreso - $cuentaporpagar3 - $programacioncuentaporpagar3,
        ];

        $registrosbateria = Bateriasubcliente::where('prioridad', 'PRIORITARIO')
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


            ->orderBy('bateriasubclientes.proveedorasignado')
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


        $fechas = Bateriasubcliente::pluck('fechabateria')->unique()->sort()->toArray();

        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;

        $userRole = auth()->user()->getRoleNames()->first(); 
        
        $query = Bateriasubcliente::with(['estadoprogramacionsubcliente', 'documentacionsubcliente', 'programacionsubcliente','informesfinales','pagoservicio','pagoservicioinformefinal','provinfofinal',
            'estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria', 'programacionsubclienteauditoria','informesfinalesauditoria','provinfofinalauditoria',
            'estadoprogramacionsubclientecomun', 'documentacionsubclientecomun', 'programacionsubclientecomun','informesfinalescomun','provinfofinalcomun','tramitesubclienteita','tramitesubclienteauditoria','tramitesubclientecomun',
            'clienteita2','clienteauditoria2','clientecomun2'])
            ->whereNotNull('proveedorasignado')
            ->where('prioridad', 'PRIORITARIO')
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
            return $item->prioridad;
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

                   $sucursal = collect([
                        $item->clienteita2, 
                        $item->clienteauditoria2, 
                        $item->clientecomun2
                    ])->filter();

                    $resultadosucursal = $sucursal->first(function ($suc) use ($item) {
                        return $suc && (
                            $suc->id === $item->clienteitaid ||
                            $suc->id === $item->clienteauditoriaid ||
                            $suc->id === $item->clientecomunid
                        );
                    });
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
                $sucursalcliente = $resultadosucursal ? $resultadosucursal->sucursal : null;

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
                    'sucursalcliente' => $sucursalcliente,
                    'prioridad' => $item->prioridad,
                    'estadoaprobacion' => $item->estadoaprobacion,
                    'proveedorasignado' => $item->proveedorasignado,
                ];
            }
            $result[] = [
                'proveedorasignado' => $item->proveedorasignado,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'fechabateria' => $item->fechabateria,
            ];
        }


        return view('admin.inventario.listaordenes', compact('registrosbateria','ordenesaprobadasprocesadas','ordenesaprobadas','ordenes', 'usuarios', 'subtotal', 'descuentoTotal', 'montoTotal',
                                                            'ordenesaprobadasprocesadasservicio','ordenesaprobadasservicio','ordenesservicio', 'usuariosservicio', 'subtotalservicio', 'descuentoTotalservicio', 'montoTotalservicio',
                                                            'ordenesaprobadasprocesadaspersonal','ordenesaprobadaspersonal','ordenespersonal', 'usuariospersonal', 'subtotalpersonal', 'descuentoTotalpersonal', 'montoTotalpersonal'
                                                            , 'totalCuenta1Ingreso', 'totalCuenta1Egreso', 'totalCuenta2Ingreso', 'totalCuenta2Egreso', 'totalCuenta3Ingreso', 'totalCuenta3Egreso', 'cuentas' , 'cuentasConSaldo'
                                                            , 'cuentaporpagar1', 'cuentaporpagar2', 'cuentaporpagar3', 'saldoanteriorcuenta1', 'saldoanteriorcuenta2', 'saldoanteriorcuenta3'
                                                            , 'programacioncuentaporpagar1', 'programacioncuentaporpagar2', 'programacioncuentaporpagar3', 'result'));
    }
    /* public function unificarPreordenes(Request $request)
    {
        $request->validate([
            'preordenes' => 'required|array|min:2',
            'preorden_destino' => 'required|string'
        ]);

        $preordenes = $request->preordenes;
        $preordenDestino = $request->preorden_destino;

        DB::beginTransaction();
        try {
            foreach ($preordenes as $preorden) {
                if ($preorden === $preordenDestino) continue;

                PreOrdenes::where('preordenid', $preorden)->update([
                    'anteriorpreordenid' => DB::raw('preordenid'),
                    'preordenid' => $preordenDestino
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Preórdenes unificadas correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al unificar: ' . $e->getMessage()], 500);
        }
    } */

    /* public function unificarPreordenes(Request $request)
    {
        $request->validate([
            'preordenes' => 'required|array|min:2',
            'preorden_destino' => 'required|string'
        ]);

        $preordenes = $request->preordenes;
        $preordenDestino = $request->preorden_destino;

        DB::beginTransaction();
        try {
            foreach ($preordenes as $preorden) {
                if ($preorden === $preordenDestino) continue;

                Ordenes::where('id', $preorden)->update([
                    'ordenunificado' => $preordenDestino
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Preórdenes unificadas correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al unificar: ' . $e->getMessage()], 500);
        }
    } */

    public function unificarPreordenes(Request $request)
    {
        $request->validate([
            'preordenes' => 'required|array|min:2',
            'preorden_destino' => 'required|string'
        ]);

        $preordenes = $request->preordenes;
        $preordenDestino = $request->preorden_destino;

        DB::beginTransaction();
        try {
            foreach ($preordenes as $preorden) {
                if ($preorden === $preordenDestino) continue;

                // 🔹 Actualizar en la tabla ORDENES
                Ordenes::where('id', $preorden)->update([
                    'ordenunificado' => $preordenDestino
                ]);

                // 🔹 Actualizar en la tabla CUENTASPAGAR
                DB::table('cuentasporpagar')
                    ->where('ordenid', $preorden)
                    ->update(['ordenid' => $preordenDestino]);
            }

            DB::commit();
            return response()->json(['message' => 'Órdenes unificadas correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al unificar: ' . $e->getMessage()], 500);
        }
    }


    public function actualizarPrioridad(Request $request)
    {
        foreach ($request->prioridades as $item) {
            PreOrdenes::where('preordenid', $item['preordenid'])
                ->update(['prioridad' => $item['priorizado']]);
        }

        return response()->json(['message' => 'Prioridades actualizadas correctamente.']);
    }

    public function guardarPagos(Request $request)
    {
        $accion = $request->input('accion');
        $selectedRegistros = $request->input('registros', []);

        if ($accion === 'despriorizar') {
            foreach ($selectedRegistros as $registroId) {
                $registro = BateriaSubCliente::find($registroId);
                if ($registro) {
                    $registro->prioridad = null;
                    $registro->save();
                }
            }
            return redirect()->route('admin.inventario.listaordenes')->with('info', 'Registros despriorizados correctamente');
        }

        $request->validate([
            'bancoorigenprogramacion' => '',
            'fechapago' => '',
        ]);

        $selectedCuenta = $request->input('bancoorigenprogramacion');
        $fechapago = $request->input('fechapago');

        $ultimoOrden = BateriaSubCliente::whereNotNull('ordenid')
            ->where('ordenid', 'LIKE', '%M')
            ->orderByRaw("CAST(SUBSTRING_INDEX(ordenid, 'M', 1) AS UNSIGNED) DESC")
            ->first();

        $nuevoNumero = 1;
        if ($ultimoOrden && preg_match('/^(\d+)M$/', $ultimoOrden->ordenid, $matches)) {
            $nuevoNumero = intval($matches[1]) + 1;
        }
        $nuevoOrdenId = $nuevoNumero . 'M';

        foreach ($selectedRegistros as $registroId) {
            $registro = BateriaSubCliente::find($registroId);
            if ($registro) {
                $registro->nrobancoorigen = $selectedCuenta;
                $registro->fechapago = $fechapago;
                $registro->prioridad = 'CUENTA POR PAGAR';
                $registro->ordenid = $nuevoOrdenId;
                $registro->save();
            }
        }

        return redirect()->route('admin.inventario.listaordenes')->with('info', 'Pagos guardados correctamente');
    }

    public function opcionesinventario(Request $request)
    {
        $nombreopciones = $request->get('buscarpor');

        $opcionesalmacen = OpcionesInventario::where('tipo', 'ALMACEN')
            ->orderBy('seccion')
            ->orderBy('tiposeccion')
            ->orderBy('opcion')
            ->get();

        $opcionesactivofijo = OpcionesInventario::where('tipo', 'ACTIVO FIJO')
            ->orderBy('tiposeccion')
            ->orderBy('opcion')
            ->get();

        $seccionesAlmacen = OpcionesInventario::where('tipo', 'ALMACEN')->distinct()->pluck('seccion')->filter()->values();
        $tiposeccionesAlmacen = OpcionesInventario::where('tipo', 'ALMACEN')->distinct()->pluck('tiposeccion')->filter()->values();
        $tiposeccionesActivoFijo = OpcionesInventario::where('tipo', 'ACTIVO FIJO')->distinct()->pluck('tiposeccion')->filter()->values();

        return view('admin.inventario.opcionesinventario', compact('opcionesalmacen', 'opcionesactivofijo','seccionesAlmacen', 'tiposeccionesAlmacen', 'tiposeccionesActivoFijo'));
    }

    public function guardaropcioninventario(Request $request)
    {
        $request->validate([
            'tipo' => 'required',
            'tiposeccion' => 'required|string|max:255',
            'opcion' => 'required|string|max:255',
            'seccion' => 'nullable|string|max:255',
        ]);

        $usuario = auth()->user();

        OpcionesInventario::create([
            'tipo' => $request->tipo,
            'seccion' => $request->tipo === 'ALMACEN' ? $request->seccion : null,
            'tiposeccion' => $request->tiposeccion,
            'opcion' => $request->opcion,
            'usuarioregistroid' => $usuario->id,
            'usuarioregistronombre' => $usuario->name,
        ]);

        return redirect()->back()->with('info', 'Opción registrada correctamente.');
    }


    public function actualizarFechaPagar(Request $request)
    {
        $request->validate([
            'preordenid' => 'required',
            'fechapagar' => 'required|date',
        ]);

        // Actualización de la fecha
        $updated = DB::table('preordenes')
            ->where('preordenid', $request->preordenid)
            ->update(['fechapagar' => $request->fechapagar]);

        // Verificar si la actualización fue exitosa
        if ($updated) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }


    
    
    public function guardaractivofijo(Request $request)
    {
        $nuevoID = null;

        if ($request->tipo_inventario == 'ALMACEN') {
            $prefijo = null;
            if ($request->seccion == 'ESCRITORIO') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'COCINA') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'ALIMENTOS Y BEBIDAS') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'CONTRUCCION Y FERRETERIA') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'LIMPIEZA') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'PLASTICO') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'PROMOCIONAL') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'USO MEDICO') {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'INSUMOS DECORATIVOS') {
                $prefijo = 'OTIN-';
            } else {
                $prefijo = 'OTIN-';
            }
            
        } elseif ($request->tipo_inventario == 'ACTIVO FIJO') {
            $prefijo = null;
            if ($request->seccion == 'ALMACEN') {
                $prefijo = 'OTIN-';
            } elseif (in_array($request->seccion, ['BAÑO 1', 'BAÑO 2', 'BAÑO GERENCIAL', 'BAÑO PLANTA ALTA'])) {
                $prefijo = 'OTIN-';
            } elseif ($request->seccion == 'COCINA') {
                $prefijo = 'OTIN-';
            } elseif (in_array($request->seccion, ['CONSULTORIO 1', 'CONSULTORIO 2', 'CONSULTORIO 3'])) {
                $prefijo = 'OTIN-';
            } elseif (in_array($request->seccion, ['GERENCIA COMERCIAL Y FINANCIERA', 'GERENCIA GENERAL', 'GERENCIA FINANCIERA'])) {
                $prefijo = 'OTIN-';
            } elseif (in_array($request->seccion, ['LAVANDERIA', 'GRADAS','PASILLO PLANTA ALTA','PASILLO PLANTA BAJA','DEPOSITO SECUNDARIO','DEPOSITO PRINCIPAL'])) {
                $prefijo = 'OTIN-';
            } elseif (in_array($request->seccion, ['OFICINA 1', 'OFICINA 2', 'ADMINISTRACION', 'AUDIOMETRIA', 'CAJA', 'ELECTROCARDIOGRAMA', 'ERGOMETRIA', 'ESPIROMETRIA', 'FISIOTERAPIA-MEDICINA LABORAL', 'LABORATORIO', 'OFICINA ADMINISTRATIVA', 'OFTALMOLOGIA', 'PRESTACIONES 1', 'PRESTACIONES 2', 'PROGRAMACION', 'PSICOLOGIA', 'SISTEMAS','OFICINA 1 PLANTA ALTA','OFICINA 2 PLANTA BAJA','OFICINA 3 PLANTA BAJA','CONSULTORIO 1 PLANTA ALTA','CONSULTORIO 2 PLANTA ALTA','CONSULTORIO 3 PLANTA ALTA','CONSULTORIO 4 PLANTA ALTA','CONSULTORIO 5 PLANTA ALTA','CONSULTORIO 6 PLANTA BAJA','CONSULTORIO 7 PLANTA BAJA','CONSULTORIO 8 PLANTA BAJA','CONSULTORIO 9 PLANTA BAJA'])) {
                $prefijo = 'OTIN-';
            } elseif (in_array($request->seccion, ['SALA DE ESPERA PLANTA BAJA', 'SALA 1', 'SALA DE ESPERA', 'SALA DE REUNIONES', 'ZONA DE MONITOREO','VISTA FRONTAL','ENTRADA PRINCIPAL','SALA DE ATENCION AL CLIENTE'])) {
                $prefijo = 'OTIN-';
            } else {
                $prefijo = 'OTIN-';
            }
            
        }
        if ($prefijo) {
            $ultimoRegistro = Inventario::where('codigo', 'LIKE', $prefijo . '%')
                                        ->orderByRaw("CAST(SUBSTRING(codigo, " . (strlen($prefijo) + 1) . ", LENGTH(codigo)) AS UNSIGNED) DESC")
                                        ->first();
            if ($ultimoRegistro) {
                $ultimoNumero = (int) substr($ultimoRegistro->codigo, strlen($prefijo));
                $nuevoNumero = $ultimoNumero + 1;
            } else {
                $nuevoNumero = 1;
            }
            $nuevoID = $prefijo . $nuevoNumero;
            $request->merge(['id' => $nuevoID]);
        }

        $usuarioAutenticado = Auth::user();

        Inventario::create([
            'tipoinventario' => $request->tipo_inventario,
            'codigo' => $nuevoID,
            'nombreproducto' => $request->nombreproducto,
            'materiaprima' => $request->materia_prima,
            'especificacionmedida' => $request->especificacionmedida,
            'color' => $request->color,
            'marca' => $request->marca,
            'unidadmedida' => $request->unidad_medida,
            'presentacion' => $request->presentacion,
            'unidades' => $request->unidades,
            'minimocantidad' => 0,
            'inventario' => $request->inventario,
            'seccion' => $request->seccion,
            'stockinicial' => $request->stockinicial,
            'stockactual' => $request->stockactual,
            'precio' => $request->precio,
            'preciounitario' => $request->preciounitario,
            'cantidad' => $request->cantidad,
            'deposito' => $request->deposito,
            'ciudad' => $request->ciudad,
            'modelo' => $request->modelo,
            'serie' => $request->serie,
            'usuarioregistroid' => $usuarioAutenticado->id,
            'usuarioregistronombre' => $usuarioAutenticado->name,
            'proveedorid' => $request->proveedorid,
            'proveedornombre' => $request->proveedornombre,
        ]);

        return redirect()->route('admin.inventario.index')->with('info', 'Inventario registrados con éxito.');
    }
    public function guardarEntrada(Request $request)
    {
        $usuario = Auth::user();

        $entrada = new EntradaSalidaInventario();
        $entrada->tipo = 'ENTRADA';
        $entrada->codigoproducto = $request->codigoproducto;
        $entrada->usuarioregistroid = $usuario->id;
        $entrada->usuarioregistronombre = $usuario->name;
        $entrada->nrofactura = $request->nrofactura;
        $entrada->nrorecibo = $request->nrorecibo;
        $entrada->fechamovimiento = $request->fecha;
        $entrada->precio = $request->precio;
        $entrada->cantidad = $request->cantidad;
        $entrada->fechacompra = $request->fechacompra;
        $entrada->fechavencimiento = $request->fechavencimiento;
        $entrada->garantia = $request->garantia;
        $entrada->save();

        $bien = Inventario::where('codigo', $request->codigoproducto)->first();
        if ($bien) {
            $bien->stockactual += $request->cantidad;
            $bien->save();
        }

        return redirect()->route('admin.inventario.index')->with('info', 'Entrada registrada con éxito.');
    }
    public function guardarSalida(Request $request)
    {
        $usuario = Auth::user();

        $entrada = new EntradaSalidaInventario();
        $entrada->tipo = 'SALIDA';
        $entrada->codigoproducto = $request->codigoproducto;
        $entrada->usuarioregistroid = $usuario->id;
        $entrada->usuarioregistronombre = $usuario->name;
        $entrada->fechamovimiento = $request->fecha;
        $entrada->cantidad = $request->cantidad;
        $entrada->usuarioreceptor = $request->usuarioreceptor;
        $usuarioReceptor = User::find($request->usuarioreceptor);
        $entrada->usuarioreceptor = $usuarioReceptor ? $usuarioReceptor->name : 'Desconocido';
        $entrada->save();

        $bien = Inventario::where('codigo', $request->codigoproducto)->first();
        if ($bien) {
            $bien->stockactual -= $request->cantidad;
            $bien->save();

            if ($bien->stockactual <= 3) {
                $usuariosNotificar = User::whereIn('id', [1, 3, 10, 11, 25, 31])->get();
                foreach ($usuariosNotificar as $usuarioDestino) {
                    $usuarioDestino->notify(new StockBajoNotification($bien));
                }
            }
        }
        return redirect()->route('admin.inventario.index')->with('info', 'Salida registrada con éxito.');
    }

    /* SOLICITUDES DE INVENTARIO */
    public function solicitarproducto(Request $request)  
    {
        $user = auth()->user();
        $nombreUsuario = $user->name;
        $sucursalUsuario = $user->sucursal;

        /* if (in_array($nombreUsuario, ['CARLOS ALEJANDRO GUARACHI SANDOVAL', 'DENISSE MAUREN LOPEZ FLORES', 'CRISTHIAN ALAIN DURAN SULLCA', 'JHOSELINE EVA VELASQUEZ ESCOBAR', 'ROLANDO RAFAEL RAMOS TORRICO'])) {
            $solicitudinventarios = SolicitudInventario::where('sucursal', $sucursalUsuario)->orderBy('created_at', 'desc')->get();
        } else {
            $solicitudinventarios = SolicitudInventario::where('usuariosolicitante', $nombreUsuario)->orderBy('created_at', 'desc')->get();
        } */
        $usuariosTodos = [
            'CARLOS ALEJANDRO GUARACHI SANDOVAL',
            'JHOSELINE EVA VELASQUEZ ESCOBAR',
        ];
        // Detectar si el usuario tiene el rol CONTABLE usando getRoleNames()
        $tieneRolContable = $user->getRoleNames()->contains('CONTABLE');

        // Determinar si es usuario con acceso total (por nombre o rol contable)
        $esUsuarioTotal = in_array($nombreUsuario, $usuariosTodos) || $tieneRolContable;
        
        $usuariosSinSucursal = [
            'CARLOS ALEJANDRO GUARACHI SANDOVAL',
            'JHOSELINE EVA VELASQUEZ ESCOBAR',
        ];

        if ($esUsuarioTotal) {
            if (in_array($nombreUsuario, $usuariosSinSucursal)) {
                // No filtra por sucursal
                $solicitudinventarios = SolicitudInventario::orderBy('created_at', 'desc')->simplePaginate(30);
            } else {
                // Sí filtra por sucursal
                $solicitudinventarios = SolicitudInventario::where('sucursal', $sucursalUsuario)->orderBy('created_at', 'desc')->simplePaginate(30);
            }
        } else {
            // Para usuarios comunes, solo ven lo suyo
            $solicitudinventarios = SolicitudInventario::where('usuariosolicitante', $nombreUsuario)->orderBy('created_at', 'desc')->simplePaginate(30);
        }

            $coincidencias = [];
            foreach ($solicitudinventarios as $solicitud) {
                $sucursalSolicitante = $solicitud->sucursal;
                $productosSolicitados = $solicitud->productosolicitado;

                $inventariosCoincidentes = Inventario::where('ciudad', $sucursalSolicitante)
                    ->get()
                    ->filter(function ($inventario) use ($productosSolicitados) {
                        return Str::contains(strtolower($inventario->nombreproducto), strtolower($productosSolicitados)) ||
                            Str::contains(strtolower($productosSolicitados), strtolower($inventario->nombreproducto));
                    });
                if ($inventariosCoincidentes->isNotEmpty()) {
                    $coincidencias[$solicitud->id] = $inventariosCoincidentes;
                }
            }

            $primerSolicitud = $solicitudinventarios->first();

            if ($primerSolicitud) {
                $productoCodigo = $primerSolicitud->codigoproducto;
            } else {
                $productoCodigo = null;
            }

            $productoDetalles = Inventario::where('codigo', $productoCodigo)->first();
            $rolusuario = auth()->user()->getRoleNames()->first();

            // Supongamos que ya tienes estas variables antes:
            $clientesITA = Cliente::orderBy('nombrecompleto')->get();
            $clientesAuditoria = ClienteAuditoria::orderBy('nombrecompleto')->get();
            $clientesComun = ClienteComun::orderBy('nombrecompleto')->get();
            $proveedoresMedicos = Proveedor::orderBy('proveedor')->get();
            $personal = User::where('estado', 'ACTIVO')
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['ADMINISTRADOR', 'CONTABLE']);
            })
            ->orderBy('name')
            ->get();


            $rolesUsuario = auth()->user()->getRoleNames()->toArray();

        $usuarioSucursal = Auth::user()->sucursal;
        $productos = Inventario::where('ciudad', $usuarioSucursal)
            ->select('id', 'nombreproducto', 'marca', 'stockactual', 'especificacionmedida', 'color')
            ->where('inventario', '!=', 'ANULADO')
            ->get();


        $solicitudesanuladas = SolicitudInventario::withTrashed()
            ->where('estado', 'ANULADO')
            ->get();


        return view('admin.inventario.solicitarproducto', compact('sucursalUsuario','nombreUsuario','rolusuario','solicitudinventarios', 'coincidencias', 'productoDetalles','clientesITA',
        'clientesAuditoria', 'clientesComun', 'proveedoresMedicos','personal','rolesUsuario','productos','solicitudesanuladas'));
    }
    public function anularSeleccionadosinventario(Request $request)
    {
        $request->validate([
            'solicitudes_json' => 'required|string',
            'motivo_anulacion' => 'required|string|max:255',
        ]);

        $ids = json_decode($request->solicitudes_json, true);

        if (!is_array($ids) || empty($ids)) {
            return back()->withErrors(['solicitudes_json' => 'No se seleccionaron solicitudes válidas.']);
        }

        $usuario = Auth::user()->name;
        $fechaActual = now();

        foreach ($ids as $id) {
            $solicitud = SolicitudInventario::find($id);

            if ($solicitud) {
                $solicitud->estado = 'ANULADO';
                $solicitud->deleted_at = $fechaActual;
                $solicitud->motivoanulacion = $request->motivo_anulacion;
                $solicitud->usuarioanulacion = $usuario;
                $solicitud->save();
            }
        }

        return back()->with('info', 'Solicitudes anuladas correctamente.');
    }


    public function guardarsolicitudproducto(Request $request)
    {
        $request->validate([
            'productosolicitado' => 'required|string|max:255',
            'cantidadsolicitud' => 'required',
            'usuariosolicitante_id' => '',
            'usuariosolicitante' => '',
            'usuarioregistro' => '',
            'productosolicitadousuario' => '',
        ]);

        $solicitud = SolicitudInventario::create([
            'usuarioregistro' => auth()->user()->name,
            'usuariosolicitanteid' => $request->usuariosolicitante_id,
            'usuariosolicitante' => $request->usuariosolicitante,
            'productosolicitado' => $request->productosolicitado,
            'productosolicitadousuario' => $request->productosolicitadousuario,
            'cantidad' => $request->cantidadsolicitud,
            'estado' => 'SOLICITADO',
            'sucursal' => auth()->user()->sucursal,
        ]);

        $usuariosNotificar = User::role(['ADMINISTRADOR', 'CONTABLE', 'MAESTRO'])
            ->where('sucursal', auth()->user()->sucursal)
            ->get();

        foreach ($usuariosNotificar as $usuarioDestino) {
            $usuarioDestino->notify(new SolicitudProductoNotification($solicitud));
        }

        return redirect()->back()->with('info', 'Solicitud registrada correctamente.');
    }
    public function procesarsolicitud(Request $request, $solicitudId) 
    {
        $user = auth()->user();
        $nombreUsuario = $user->name;
        $idUsuario = $user->id;
        // Buscar la solicitud
        $solicitud = SolicitudInventario::findOrFail($solicitudId);

        // Obtener el ID del producto
        $productoId = $request->input('producto_id');
        $producto = Inventario::findOrFail($productoId);

        // Actualizar la solicitud
        $solicitud->productoofertado = $producto->nombreproducto . ' - ' . $producto->marca;
        $solicitud->codigoproducto = $producto->codigo;
        $solicitud->cantidadofertado = $solicitud->cantidad;
        $solicitud->estado = 'ACEPTADO';
        $solicitud->usuarioactualizacion = $nombreUsuario;
        $solicitud->usuarioactualizacionid = $idUsuario;

        $solicitud->save();


        // Registrar en entrada_salida_inventario
        EntradaSalidaInventario::create([
            'tipo' => 'SALIDA',
            'codigoproducto' => $producto->codigo,
            'precio' => $producto->precio,
            'nombre_producto' => $producto->nombre,
            'cantidad' => $solicitud->cantidad,
            'fechamovimiento' => now(),
            'usuarioreceptor' => $solicitud->usuariosolicitante,
            'usuarioregistronombre' => $nombreUsuario,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Verificar si hay usuarios a los que notificar
        $usuariosNotificar = [];
        if (!empty($solicitud->usuariosolicitante)) {
            // Convertir a un array si es un string
            $usuariosNotificar = is_string($solicitud->usuariosolicitante) ? explode(',', $solicitud->usuariosolicitante) : $solicitud->usuariosolicitante;
        }

        // Notificar a los usuarios si existen
        if (!empty($usuariosNotificar)) {
            $usuariosNotificar = User::whereIn('name', $usuariosNotificar)->get();
            foreach ($usuariosNotificar as $usuarioDestino) {
                $usuarioDestino->notify(new AceptaSolicitudNotification($solicitud));
            }
        }

        // Redirigir con mensaje de éxito
        return redirect()->back()->with('info', 'Registro exitoso!');
    }
    public function actualizarStock(Request $request, $solicitudId)  
    {
        // Buscar la solicitud y el producto relacionado
        $solicitud = SolicitudInventario::findOrFail($solicitudId);
        $producto = Inventario::where('codigo', $solicitud->codigoproducto)->first();
    
        // Obtener la cantidad a restar
        $cantidad = $request->input('cantidad');
        $cantidadOfertada = $solicitud->cantidadofertado;
    
        // Restar la cantidad del stock actual
        $producto->stockactual -= $cantidad;
        if ($producto->stockactual <= 0) {
            $producto->inventario = 'AGOTADO';
        }
        $producto->save();
    
        // Obtener datos del usuario autenticado
        $usuario = auth()->user();
        $usuarioNombre = $usuario->name;
        $fecha = now()->toDateString();
    
        // Marcar la solicitud como procesada
        $solicitud->estado = 'PROCESADO';
        $solicitud->save();
    
        if ($producto->stockactual <= $producto->minimocantidad) {
            $usuariosNotificar = User::role(['ADMINISTRADOR', 'CONTABLE', 'MAESTRO'])
                ->where('sucursal', auth()->user()->sucursal)
                ->get();

            foreach ($usuariosNotificar as $usuarioDestino) {
                $usuarioDestino->notify(new StockBajoNotification($solicitud));
            }
        }

        $htmlContent = view('admin.inventario.comprobante', compact('fecha', 'producto', 'cantidadOfertada', 'usuarioNombre', 'solicitud'))->render();

        $folderPath = public_path("comprobanteinventario/{$solicitud->usuarioregistroid}");
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $fileName = "comprobante_{$solicitudId}.html";
        $filePath = $folderPath . '/' . $fileName;

        file_put_contents($filePath, $htmlContent);

        $solicitud->save();

        session()->flash('success', 'Salida registrada exitosamente.');
        session()->flash('download_url', asset("comprobanteinventario/{$solicitud->usuarioregistroid}/{$fileName}"));

        return redirect()->back();
    }
    public function generarComprobanteMasivo(Request $request)
    {
        $ids = $request->input('ids');
        $usuario = auth()->user();
        $usuarioNombre = $usuario->name;
        $fecha = now()->toDateString();

        $solicitudesProcesadas = [];

        foreach ($ids as $id) {
            $solicitud = SolicitudInventario::findOrFail($id);
            $producto = Inventario::where('codigo', trim($solicitud->codigoproducto))->first();

            if (!$producto) continue;

            $cantidad = $solicitud->cantidadofertado;

            // Actualizar stock
            $producto->stockactual -= $cantidad;
            $producto->save();

            // Marcar como procesado
            $solicitud->estado = 'PROCESADO';
            $solicitud->save();

            // Notificar stock bajo
            /* if ($producto->stockactual <= 3) {
                $usuariosNotificar = User::whereIn('id', [1, 3, 10, 11, 25, 31])->get();
                foreach ($usuariosNotificar as $usuarioDestino) {
                    $usuarioDestino->notify(new StockBajoNotification($producto));
                }
            } */
            if ($producto->stockactual <= $producto->minimocantidad) {
                // Notificar solo a usuarios con roles específicos y misma sucursal
                $usuariosNotificar = User::role(['ADMINISTRADOR', 'CONTABLE', 'MAESTRO'])
                    ->where('sucursal', auth()->user()->sucursal)
                    ->get();

                foreach ($usuariosNotificar as $usuarioDestino) {
                    $usuarioDestino->notify(new StockBajoNotification($solicitud));
                }
            }

            // Agregar solicitud y producto al arreglo
            $solicitudesProcesadas[] = [
                'solicitud' => $solicitud,
                'producto' => $producto,
                'cantidadOfertada' => $cantidad
            ];
        }

        // Generar comprobante único
        $htmlContent = view('admin.inventario.comprobante_masivo', compact('fecha', 'usuarioNombre', 'solicitudesProcesadas'))->render();

        $folderPath = public_path("comprobanteinventario/{$usuario->id}");
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $fileName = "comprobante_masivo_" . now()->format('Ymd_His') . ".html";
        $filePath = $folderPath . '/' . $fileName;

        file_put_contents($filePath, $htmlContent);

        session()->flash('success', 'Salidas registradas exitosamente.');
        session()->flash('download_url', asset("comprobanteinventario/{$usuario->id}/{$fileName}"));

        return redirect()->back();
    }


    public function pasarAEspera($id)
    {
        $user = auth()->user();
        $nombreUsuario = $user->name;
        $idUsuario = $user->id;
        $solicitud = SolicitudInventario::findOrFail($id);
        $solicitud->estado = 'EN ESPERA';
        $solicitud->usuarioactualizacion = $nombreUsuario;
        $solicitud->usuarioactualizacionid = $idUsuario;
        $solicitud->save();

        $usuariosNotificar = [];
        if (!empty($solicitud->usuariosolicitante)) {
            // Convertir a un array si es un string
            $usuariosNotificar = is_string($solicitud->usuariosolicitante) ? explode(',', $solicitud->usuariosolicitante) : $solicitud->usuariosolicitante;
        }

        if (!empty($usuariosNotificar)) {
            $usuariosNotificar = User::whereIn('name', $usuariosNotificar)->get();
            foreach ($usuariosNotificar as $usuarioDestino) {
                $usuarioDestino->notify(new SolicitudInvEsperaNotification($solicitud));
            }
        }

        return redirect()->back()->with('info', 'Solicitud actualizada a EN ESPERA.');
    }
    public function subirDocumento(Request $request, $id) 
    {
        $request->validate([
            'documento' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $solicitud = SolicitudInventario::findOrFail($id);
        $usuario = Auth::user();

        if ($request->hasFile('documento')) {
            $file = $request->file('documento');
            
            // Crear carpeta dentro de public/comprobanteinventario/{id_usuario}
            $carpetaCliente = public_path("comprobanteinventario/{$usuario->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            // Generar el nombre del archivo con timestamp
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            
            // Mover el archivo a la carpeta
            $file->move($carpetaCliente, $archivo_name);

            // Guardar solo el nombre del archivo en la base de datos
            $solicitud->documento = $archivo_name;
            $solicitud->timestamps = false;
            $solicitud->save();
        }

        return redirect()->back()->with('success', 'Documento subido correctamente.');
    }
    public function aceptarOferta(Request $request, $solicitudId)
    {
        $solicitud = SolicitudInventario::findOrFail($solicitudId);
        $solicitud->estado = 'ACEPTADO';
        $solicitud->save();

        if ($solicitud) {
            // Notificar solo a usuarios con roles específicos y misma sucursal
            $usuariosNotificar = User::role(['ADMINISTRADOR', 'CONTABLE', 'MAESTRO'])
                ->where('sucursal', auth()->user()->sucursal)
                ->get();

            foreach ($usuariosNotificar as $usuarioDestino) {
                $usuarioDestino->notify(new AceptoRechazosolicitudNotification($solicitud));
            }
        }

        return redirect()->back()->with('info', 'Oferta aceptada con éxito!');
    }
    public function rechazarOferta(Request $request, $solicitudId) 
    {
        $solicitud = SolicitudInventario::findOrFail($solicitudId);
        $solicitud->estado = 'RECHAZADO';
        $solicitud->save();

        if ($solicitud) {
            // Notificar solo a usuarios con roles específicos y misma sucursal
            $usuariosNotificar = User::role(['ADMINISTRADOR', 'CONTABLE', 'MAESTRO'])
                ->where('sucursal', auth()->user()->sucursal)
                ->get();

            foreach ($usuariosNotificar as $usuarioDestino) {
                $usuarioDestino->notify(new AceptoRechazosolicitudNotification($solicitud));
            }
        }

        return redirect()->back()->with('info', 'Oferta rechazada con éxito!');
    }
    public function ofertarProducto(Request $request, $solicitudId) 
    {
        $user = auth()->user();
        $nombreUsuario = $user->name;
        $idUsuario = $user->id;
        $solicitud = SolicitudInventario::findOrFail($solicitudId);

        $productoId = $request->input('producto_id');
        $cantidadofertado = $request->input('cantidadofertado');
        $producto = Inventario::findOrFail($productoId);

        $solicitud->productoofertado = $producto->nombreproducto . ' - ' . $producto->marca;
        $solicitud->codigoproducto = $producto->codigo;
        $solicitud->cantidadofertado = $cantidadofertado;
        $solicitud->estado = 'OFERTADO';
        $solicitud->usuarioactualizacion = $nombreUsuario;
        $solicitud->usuarioactualizacionid = $idUsuario;
        $solicitud->save();

        $usuariosNotificar = [];
        if (!empty($solicitud->usuariosolicitante)) {
            // Convertir a un array si es un string
            $usuariosNotificar = is_string($solicitud->usuariosolicitante) ? explode(',', $solicitud->usuariosolicitante) : $solicitud->usuariosolicitante;
        }

        if (!empty($usuariosNotificar)) {
            $usuariosNotificar = User::whereIn('name', $usuariosNotificar)->get();
            foreach ($usuariosNotificar as $usuarioDestino) {
                $usuarioDestino->notify(new ActSolicitudNotification($solicitud));
            }
        }
        return redirect()->back()->with('info', 'Producto ofertado con éxito!');
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
    public function edit(Inventario $inventario)
    {
        return view('admin.inventario.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventario $inventario)
    {

        return redirect()->route('admin.bienes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventario $inventario)
    {
        return redirect()->route('admin.inventario.index');
    }
    
    


    /* PLANILLAS DE PAGOS */
    public function planillatercerinter(Request $request)
    {
        return view('admin.inventario.planillatercerinter');
    }
    
}

