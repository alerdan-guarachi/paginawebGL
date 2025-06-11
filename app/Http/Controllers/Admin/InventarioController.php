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

        $bienesalmacen = Inventario::where('nombreproducto','Like',"%$nombreproducto%")
                                    ->where('tipoinventario','ALMACEN')->simplePaginate(50);

        $bienesalmacenstockbajo = Inventario::where('nombreproducto', 'Like', "%$nombreproducto%")
            ->where('tipoinventario', 'ALMACEN')
            ->whereColumn('stockactual', '<', 'minimocantidad')
        ->simplePaginate(1000);


        $bienesactivosfijos = Inventario::where('nombreproducto','Like',"%$nombreproducto%")
                                    ->where('tipoinventario','ACTIVO FIJO')->simplePaginate(50);

        $usuarios = User::has('roles')
                        ->orderBy('name', 'asc')
                        ->get();

        $historiales = EntradaSalidaInventario::where('tipo','ENTRADA')->orderBy('created_at', 'desc')->get();
        $historialessalidas = EntradaSalidaInventario::where('tipo','SALIDA')->orderBy('created_at', 'desc')->get();

        $detalleOrdenes = DB::table('detalleordenes')
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

        return view('admin.inventario.index', compact('bienesalmacenstockbajo','detalleOrdenes', 'bienesalmacen', 'bienesactivosfijos', 'usuarios', 'historiales', 'historialessalidas'));
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
            'minimocantidad' => '',
            'fechavencimiento' => '',
            'garantia' => '',
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
        $nuevoProducto->minimocantidad = $request->minimocantidad;
        $nuevoProducto->usuarioregistroid = $usuario->id;
        $nuevoProducto->usuarioregistronombre = $usuario->name;

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

        return view('admin.inventario.create', compact('proveedores', 'sucursal'));
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
    
        return view('admin.inventario.crearactivofijo', compact('proveedores','sucursal'));
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
                $prefijo = 'ESAL-';
            } elseif ($request->seccion == 'COCINA') {
                $prefijo = 'COAL-';
            } elseif ($request->seccion == 'ALIMENTOS Y BEBIDAS') {
                $prefijo = 'ABAL-';
            } elseif ($request->seccion == 'CONTRUCCION Y FERRETERIA') {
                $prefijo = 'CFAL-';
            } elseif ($request->seccion == 'LIMPIEZA') {
                $prefijo = 'LIAL-';
            } elseif ($request->seccion == 'PLASTICO') {
                $prefijo = 'PLAL-';
            } elseif ($request->seccion == 'PROMOCIONAL') {
                $prefijo = 'PRAL-';
            } elseif ($request->seccion == 'USO MEDICO') {
                $prefijo = 'UMAL-';
            } elseif ($request->seccion == 'INSUMOS DECORATIVOS') {
                $prefijo = 'IDAL-';
            } else {
                $prefijo = 'OTAL-';
            }
            
        } elseif ($request->tipo_inventario == 'ACTIVO FIJO') {
            $prefijo = null;
            if ($request->seccion == 'ALMACEN') {
                $prefijo = 'ALAF-';
            } elseif (in_array($request->seccion, ['BAÑO 1', 'BAÑO 2', 'BAÑO GERENCIAL', 'BAÑO PLANTA ALTA'])) {
                $prefijo = 'BAAF-';
            } elseif ($request->seccion == 'COCINA') {
                $prefijo = 'COAF-';
            } elseif (in_array($request->seccion, ['CONSULTORIO 1', 'CONSULTORIO 2', 'CONSULTORIO 3'])) {
                $prefijo = 'CNAF-';
            } elseif (in_array($request->seccion, ['GERENCIA COMERCIAL Y FINANCIERA', 'GERENCIA GENERAL', 'GERENCIA FINANCIERA'])) {
                $prefijo = 'GEAF-';
            } elseif (in_array($request->seccion, ['LAVANDERIA', 'GRADAS','PASILLO PLANTA ALTA','PASILLO PLANTA BAJA','DEPOSITO SECUNDARIO','DEPOSITO PRINCIPAL'])) {
                $prefijo = 'LAAF-';
            } elseif (in_array($request->seccion, ['OFICINA 1', 'OFICINA 2', 'ADMINISTRACION', 'AUDIOMETRIA', 'CAJA', 'ELECTROCARDIOGRAMA', 'ERGOMETRIA', 'ESPIROMETRIA', 'FISIOTERAPIA-MEDICINA LABORAL', 'LABORATORIO', 'OFICINA ADMINISTRATIVA', 'OFTALMOLOGIA', 'PRESTACIONES 1', 'PRESTACIONES 2', 'PROGRAMACION', 'PSICOLOGIA', 'SISTEMAS','OFICINA 1 PLANTA ALTA','OFICINA 2 PLANTA BAJA','OFICINA 3 PLANTA BAJA','CONSULTORIO 1 PLANTA ALTA','CONSULTORIO 2 PLANTA ALTA','CONSULTORIO 3 PLANTA ALTA','CONSULTORIO 4 PLANTA ALTA','CONSULTORIO 5 PLANTA ALTA','CONSULTORIO 6 PLANTA BAJA','CONSULTORIO 7 PLANTA BAJA','CONSULTORIO 8 PLANTA BAJA','CONSULTORIO 9 PLANTA BAJA'])) {
                $prefijo = 'OFAF-';
            } elseif (in_array($request->seccion, ['SALA DE ESPERA PLANTA BAJA', 'SALA 1', 'SALA DE ESPERA', 'SALA DE REUNIONES', 'ZONA DE MONITOREO','VISTA FRONTAL','ENTRADA PRINCIPAL','SALA DE ATENCION AL CLIENTE'])) {
                $prefijo = 'SEAF-';
            } else {
                $prefijo = 'OTAF-';
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

        $bienesalmacen = PortafolioProveedores::leftJoin('inventario', 'portafolioproveedores.id', '=', 'inventario.codigo')
            ->select('portafolioproveedores.*', 'inventario.stockactual as cantidadalmacen');

        if ($nombreproducto) {
            $bienesalmacen = $bienesalmacen->where('portafolioproveedores.nombreproducto', 'LIKE', "%$nombreproducto%");
        }

        $bienesalmacen = $bienesalmacen->paginate(10);

        if ($request->ajax()) {
            return view('admin.inventario.partials.tabla', compact('bienesalmacen'))->render();
        }

        return view('admin.inventario.adquisicioninventario', compact('bienesalmacen'));
    }
    
//PRE ORDENES
    public function generarpreordencompra(Request $request)  
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
    public function generarpreordenservicio(Request $request)  
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

        /* $ultimoPreOrden = PreOrdenes::latest('preordenid')->first();
        if ($ultimoPreOrden && preg_match('/^(\d+)PO$/', $ultimoPreOrden->preordenid, $matches)) {
            $nuevoPreOrdenId = ((int)$matches[1] + 1) . 'PO';
        } else {
            $nuevoPreOrdenId = '1PO';
        } */
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
                /* 'codigo' => $detalle['id'], */
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
    public function generarpreordenpersonal(Request $request)  
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

        // Obtener el último ordenid que sigue el patrón número + 'M'
        $ultimoOrden = BateriaSubCliente::whereNotNull('ordenid')
            ->where('ordenid', 'LIKE', '%M')
            ->orderByRaw("CAST(SUBSTRING_INDEX(ordenid, 'M', 1) AS UNSIGNED) DESC")
            ->first();

        // Calcular nuevo ordenid
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
                                'cantidad' => $detalleOrden->cantidad,
                                'codigo' => $detalleOrden->codigo,
                                'tipotransaccion' => $tipotransaccion,
                                'preciounitario' => $saldoFaltante,
                                'descuentounitario' => 0,
                                'totalunitario' => $saldoFaltante,
                                'proveedorid' => $proveedorid,
                                'proveedornombre' => $proveedornombre,
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
                    }
                }
            }
            $detalleOrdenes = DetalleOrdenes::where('ordenid', $orden->id)->get(); 

            $ultimacp = CuentasPagar::orderByRaw("LENGTH(id) DESC, id DESC")
                ->first();
            $nuevoIdcp = $ultimacp ? ((int) filter_var($ultimacp->id, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;

            foreach ($detalleOrdenes as $detalle) {
                $idUnico = $nuevoIdcp . 'CP';

                $cuentasPagar = new CuentasPagar();
                $cuentasPagar->id = $idUnico;
                $cuentasPagar->proveedorid = $orden->proveedorid;
                $cuentasPagar->proveedornombre = $orden->proveedornombre;
                $cuentasPagar->detalleproducto = $detalle->detalle;
                $cuentasPagar->fechaasignada = $orden->fechapagar;
                $cuentasPagar->fechacomprar = $orden->fechacomprar;
                $cuentasPagar->nrobancoorigen = $detalle->nrobancoorigen;
                $cuentasPagar->subtotal = $detalle->preciounitario;
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

            // Generar el PDF
            $pdf = PDF::loadView('admin.inventario.pdfgenerarordencompra', $pdfData);
            $nombreArchivo = 'ORDEN_COMPRA_' . $orden->id . '.pdf';
            
            // Ruta para guardar el PDF
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
                                'tipoorden' => 'ORDEN DE SERVICIO',
                                'estado' => 'PENDIENTE',
                                'formapago' => $formapago,
                                'sucursal' => $detalleOrden->sucursal,
                                'sucursalgasto' => $detalleOrden->sucursalgasto,
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

                $cuentasPagar = new CuentasPagar();
                $cuentasPagar->id = $idUnico;
                $cuentasPagar->proveedorid = $orden->proveedorid;
                $cuentasPagar->proveedornombre = $orden->proveedornombre;
                $cuentasPagar->detalleproducto = $detalle->detalle;
                $cuentasPagar->fechaasignada = $orden->fechapagar;
                $cuentasPagar->fechacomprar = $orden->fechacomprar;
                $cuentasPagar->nrobancoorigen = $detalle->nrobancoorigen;
                $cuentasPagar->subtotal = $detalle->preciounitario;
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

                $cuentasPagar = new CuentasPagar();
                $cuentasPagar->id = $idUnico;
                $cuentasPagar->proveedorid = $orden->proveedorid;
                $cuentasPagar->proveedornombre = $orden->proveedornombre;
                $cuentasPagar->detalleproducto = $detalle->detalle;
                $cuentasPagar->fechaasignada = $orden->fechapagar;
                $cuentasPagar->fechacomprar = $orden->fechacomprar;
                $cuentasPagar->nrobancoorigen = $detalle->nrobancoorigen;
                $cuentasPagar->subtotal = $detalle->preciounitario;
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
    }
    public function guardaractivofijo(Request $request)
    {
        $nuevoID = null;

        if ($request->tipo_inventario == 'ALMACEN') {
            $prefijo = null;
            if ($request->seccion == 'ESCRITORIO') {
                $prefijo = 'ESAL-';
            } elseif ($request->seccion == 'COCINA') {
                $prefijo = 'COAL-';
            } elseif ($request->seccion == 'ALIMENTOS Y BEBIDAS') {
                $prefijo = 'ABAL-';
            } elseif ($request->seccion == 'CONTRUCCION Y FERRETERIA') {
                $prefijo = 'CFAL-';
            } elseif ($request->seccion == 'LIMPIEZA') {
                $prefijo = 'LIAL-';
            } elseif ($request->seccion == 'PLASTICO') {
                $prefijo = 'PLAL-';
            } elseif ($request->seccion == 'PROMOCIONAL') {
                $prefijo = 'PRAL-';
            } elseif ($request->seccion == 'USO MEDICO') {
                $prefijo = 'UMAL-';
            } elseif ($request->seccion == 'INSUMOS DECORATIVOS') {
                $prefijo = 'IDAL-';
            } else {
                $prefijo = 'OTAL-';
            }
            
        } elseif ($request->tipo_inventario == 'ACTIVO FIJO') {
            $prefijo = null;
            if ($request->seccion == 'ALMACEN') {
                $prefijo = 'ALAF-';
            } elseif (in_array($request->seccion, ['BAÑO 1', 'BAÑO 2', 'BAÑO GERENCIAL', 'BAÑO PLANTA ALTA'])) {
                $prefijo = 'BAAF-';
            } elseif ($request->seccion == 'COCINA') {
                $prefijo = 'COAF-';
            } elseif (in_array($request->seccion, ['CONSULTORIO 1', 'CONSULTORIO 2', 'CONSULTORIO 3'])) {
                $prefijo = 'CNAF-';
            } elseif (in_array($request->seccion, ['GERENCIA COMERCIAL Y FINANCIERA', 'GERENCIA GENERAL', 'GERENCIA FINANCIERA'])) {
                $prefijo = 'GEAF-';
            } elseif (in_array($request->seccion, ['LAVANDERIA', 'GRADAS','PASILLO PLANTA ALTA','PASILLO PLANTA BAJA','DEPOSITO SECUNDARIO','DEPOSITO PRINCIPAL'])) {
                $prefijo = 'LAAF-';
            } elseif (in_array($request->seccion, ['OFICINA 1', 'OFICINA 2', 'ADMINISTRACION', 'AUDIOMETRIA', 'CAJA', 'ELECTROCARDIOGRAMA', 'ERGOMETRIA', 'ESPIROMETRIA', 'FISIOTERAPIA-MEDICINA LABORAL', 'LABORATORIO', 'OFICINA ADMINISTRATIVA', 'OFTALMOLOGIA', 'PRESTACIONES 1', 'PRESTACIONES 2', 'PROGRAMACION', 'PSICOLOGIA', 'SISTEMAS','OFICINA 1 PLANTA ALTA','OFICINA 2 PLANTA BAJA','OFICINA 3 PLANTA BAJA','CONSULTORIO 1 PLANTA ALTA','CONSULTORIO 2 PLANTA ALTA','CONSULTORIO 3 PLANTA ALTA','CONSULTORIO 4 PLANTA ALTA','CONSULTORIO 5 PLANTA ALTA','CONSULTORIO 6 PLANTA BAJA','CONSULTORIO 7 PLANTA BAJA','CONSULTORIO 8 PLANTA BAJA','CONSULTORIO 9 PLANTA BAJA'])) {
                $prefijo = 'OFAF-';
            } elseif (in_array($request->seccion, ['SALA DE ESPERA PLANTA BAJA', 'SALA 1', 'SALA DE ESPERA', 'SALA DE REUNIONES', 'ZONA DE MONITOREO','VISTA FRONTAL','ENTRADA PRINCIPAL','SALA DE ATENCION AL CLIENTE'])) {
                $prefijo = 'SEAF-';
            } else {
                $prefijo = 'OTAF-';
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

        if (in_array($nombreUsuario, ['CARLOS ALEJANDRO GUARACHI SANDOVAL', 'DENISSE MAUREN LOPEZ FLORES', 'CRISTHIAN ALAIN DURAN SULLCA', 'JHOSELINE EVA VELASQUEZ ESCOBAR', 'ROLANDO RAFAEL RAMOS TORRICO'])) {
            $solicitudinventarios = SolicitudInventario::where('sucursal', $sucursalUsuario)->orderBy('created_at', 'desc')->get();
        } else {
            $solicitudinventarios = SolicitudInventario::where('usuariosolicitante', $nombreUsuario)->orderBy('created_at', 'desc')->get();
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
        $personal = Proveedoresservicios::whereIn('id', ['22PS', '21PS', '28PS'])
            ->orderBy('razonsocial')
            ->get();

            $rolesUsuario = auth()->user()->getRoleNames()->toArray();


        return view('admin.inventario.solicitarproducto', compact('sucursalUsuario','nombreUsuario','rolusuario','solicitudinventarios', 'coincidencias', 'productoDetalles','clientesITA',
        'clientesAuditoria',
        'clientesComun',
        'proveedoresMedicos','personal','rolesUsuario'));
    }
    public function guardarsolicitudproducto(Request $request)
    {
        $request->validate([
            'productosolicitado' => 'required|string|max:255',
            'cantidadsolicitud' => 'required',
            'usuariosolicitante_id' => '',
            'usuariosolicitante' => '',
            'usuarioregistro' => '',
        ]);

        $solicitud = SolicitudInventario::create([
            'usuarioregistro' => auth()->user()->name,
            'usuariosolicitanteid' => $request->usuariosolicitante_id,
            'usuariosolicitante' => $request->usuariosolicitante,
            'productosolicitado' => $request->productosolicitado,
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
        $producto->save();
    
        // Obtener datos del usuario autenticado
        $usuario = auth()->user();
        $usuarioNombre = $usuario->name;
        $fecha = now()->toDateString();
    
        // Marcar la solicitud como procesada
        $solicitud->estado = 'PROCESADO';
        $solicitud->save();
    
        // Verificar si el stock es bajo y notificar
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
    
        // Generar el contenido HTML de la vista
        $htmlContent = view('admin.inventario.comprobante', compact('fecha', 'producto', 'cantidadOfertada', 'usuarioNombre', 'solicitud'))->render();
    
        // Definir la carpeta de almacenamiento
        $folderPath = public_path("comprobanteinventario/{$solicitud->usuarioregistroid}");
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
    
        // Crear el nombre del archivo
        $fileName = "comprobante_{$solicitudId}.html";
        $filePath = $folderPath . '/' . $fileName;
    
        // Guardar el HTML en un archivo
        file_put_contents($filePath, $htmlContent);
    
        // Guardar el nombre del archivo en la base de datos
        /* $solicitud->documento = $fileName; */
        $solicitud->save();
    
        // Guardar en la sesión la URL de descarga y el mensaje de confirmación
        session()->flash('success', 'Salida registrada exitosamente.');
        session()->flash('download_url', asset("comprobanteinventario/{$solicitud->usuarioregistroid}/{$fileName}"));
    
        // Redirigir de vuelta
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
            'documento' => 'required|file|mimes:pdf,jpg,png|max:2048', // Máximo 2MB
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
    
    /* NUEVAS ORDENES */
    public function crearordenes(Request $request)  
    {
        $nombreproducto = $request->get('buscarpor');
        $sucursal = auth()->user()->sucursal;
        $bienesalmacen = PortafolioProveedores::leftJoin('inventario', 'portafolioproveedores.id', '=', 'inventario.codigo')
            ->select('portafolioproveedores.*', 'inventario.stockactual as cantidadalmacen');
        if ($nombreproducto) {
            $bienesalmacen = $bienesalmacen->where('portafolioproveedores.nombreproducto', 'LIKE', "%$nombreproducto%");
        }
        $bienesalmacen = $bienesalmacen->paginate(10);
        if ($request->ajax()) {
            return view('admin.inventario.partials.tabla', compact('bienesalmacen'))->render();
        }

        $proveedores = ProveedoresServicios::where(function($query) {
            $query->where('tipoorden1', 'ORDEN DE SERVICIO')
                  ->orWhere('tipoorden2', 'ORDEN DE SERVICIO')
                  ->orWhere('tipoorden3', 'ORDEN DE SERVICIO');
        })
        ->select('id', 'razonsocial', 'tipotransaccion', 'ciudad', 'ciudad2')
        ->orderBy('razonsocial')
        ->get();
    
        $proveedorespersonal = ProveedoresServicios::whereIn('categoria', ['PROVEEDOR EXTERNO', 'PROVEEDOR INTERNO'])->select('id', 'razonsocial', 'tipotransaccion', 'categoria', 'ciudad', 'ciudad2')->get();

        $planes = PlanesServiciosProv::select('id', 'plan', 'proveedorid', 'contrato', 'linea', 'cuenta', 'servicio', 'codigo', 'montofijo', 'ciudad', 'motivo')->get();

        return view('admin.inventario.crearordenes', compact('bienesalmacen', 'proveedores', 'planes', 'proveedorespersonal', 'sucursal'));
    }



    /* PLANILLAS DE PAGOS */
    public function planillatercerinter(Request $request)
    {
        return view('admin.inventario.planillatercerinter');
    }
    
}

