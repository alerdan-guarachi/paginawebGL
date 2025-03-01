<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CuentasPagar;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Inventario;
use App\Models\SeccionesInventario;
use App\Models\Proveedoresservicios;
use App\Models\EntradaSalidaInventario;
use App\Models\SolicitudInventario;
use Illuminate\Support\Facades\Auth;
use App\Notifications\StockBajoNotification;
use App\Notifications\SolicitudProductoNotification;
use Illuminate\Support\Str;
use App\Notifications\ActSolicitudNotification;
use App\Notifications\AceptaSolicitudNotification;
use App\Notifications\AceptoRechazosolicitudNotification;
use Dompdf\Dompdf;
use PDF;
use Illuminate\Support\Facades\Log;

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

    public function index(Request $request)
    {
        $nombreproducto = $request->get('buscarpor');

        $bienesalmacen = Inventario::where('nombreproducto','Like',"%$nombreproducto%")
                                ->where('tipoinventario','ALMACEN')->simplePaginate(1000);

        $bienesactivosfijos = Inventario::where('nombreproducto','Like',"%$nombreproducto%")
                                ->where('tipoinventario','ACTIVO FIJO')->simplePaginate(1000);

        $usuarios = User::has('roles')
                    ->orderBy('name', 'asc')
                    ->get();

        $historiales = EntradaSalidaInventario::where('tipo','ENTRADA')->orderBy('created_at', 'desc')->get();
        $historialessalidas = EntradaSalidaInventario::where('tipo','SALIDA')->orderBy('created_at', 'desc')->get();

        return view('admin.inventario.index', compact('bienesalmacen', 'bienesactivosfijos', 'usuarios', 'historiales', 'historialessalidas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /* CREAR PRODUCTO ALMACEN */
    public function create(Request $request)
    {
        $secciones = SeccionesInventario::where('tipoinventario', 'ALMACEN')
            ->where('tiposeccion', 'PRODUCTO')
            ->where('estado', 'ACTIVO')
            ->distinct('seccion')
            ->orderby('seccion', 'asc')
            ->pluck('seccion', 'seccion');

        $subsecciones = SeccionesInventario::where('tipoinventario', 'ALMACEN')
            ->where('tiposeccion', 'PRODUCTO')
            ->where('estado', 'ACTIVO')
            ->orderby('subseccion', 'asc')
            ->get();
        
        $materiaprimas = SeccionesInventario::where('tipoinventario', 'ALMACEN')
            ->where('tiposeccion', 'MATERIA PRIMA')
            ->where('estado', 'ACTIVO')
            ->orderby('subseccion', 'asc')
            ->get();
        
        $marcas = SeccionesInventario::where('tipoinventario', 'ALMACEN')
            ->where('tiposeccion', 'MARCA')
            ->where('estado', 'ACTIVO')
            ->orderby('subseccion', 'asc')
            ->get();

        $unidadmedidas = SeccionesInventario::where('tipoinventario', 'ALMACEN')
            ->where('tiposeccion', 'UNIDAD MEDIDA')
            ->where('estado', 'ACTIVO')
            ->orderby('subseccion', 'asc')
            ->get();

        $user = auth()->user();
        $sucursal = $user->sucursal; 

        return view('admin.inventario.create', compact('secciones', 'subsecciones', 'materiaprimas', 'marcas', 'unidadmedidas' , 'sucursal'));
    }

    /* CREAR ACTIVO FIJO */
    public function crearactivofijo(Request $request)
    {
        $secciones = SeccionesInventario::where('seccion', 'ACTIVO FIJO')
            ->where('tiposeccion', 'SECCION ACTIVO FIJO')
            ->where('estado', 'ACTIVO')
            ->distinct('subseccion')
            ->orderby('subseccion', 'asc')
            ->pluck('subseccion', 'subseccion');

        $subsecciones = SeccionesInventario::where('tipoinventario', 'ACTIVO FIJO')
            ->where('tiposeccion', 'PRODUCTO')
            ->where('estado', 'ACTIVO')
            ->orderby('subseccion', 'asc')
            ->get(['subseccion', 'codigo']);
        
        $materiaprimas = SeccionesInventario::where('tipoinventario', 'ACTIVO FIJO')
            ->where('tiposeccion', 'MATERIA PRIMA')
            ->where('estado', 'ACTIVO')
            ->orderby('subseccion', 'asc')
            ->pluck('subseccion', 'subseccion');
        
        $marcas = SeccionesInventario::where('tipoinventario', 'ACTIVO FIJO')
            ->where('tiposeccion', 'MARCA')
            ->where('estado', 'ACTIVO')
            ->orderby('subseccion', 'asc')
            ->pluck('subseccion', 'subseccion');

        $unidadmedidas = SeccionesInventario::where('tipoinventario', 'ACTIVO FIJO')
            ->where('tiposeccion', 'UNIDAD MEDIDA')
            ->where('estado', 'ACTIVO')
            ->orderby('subseccion', 'asc')
            ->pluck('subseccion', 'subseccion');

        $user = auth()->user();
        $sucursal = $user->sucursal; 
    
        return view('admin.inventario.crearactivofijo', compact('secciones', 'subsecciones', 'materiaprimas', 'marcas', 'unidadmedidas' , 'sucursal'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'seccion' => '',
            'subseccion' => '',
            'codigo' => '',
            'materiaprima' => '',
            'marca' => '',
            'unidadmedida' => '',
            'especificacionmedida' => '',
            'color' => '',
            'inventario' => '',
            'deposito' => '',
            'stockinicial' => '',
            'stockactual' => '',
            'precio' => '',
        ]);

        $usuario = auth()->user();

        $ultimoBien = Inventario::latest()->first();
        $ultimoId = $ultimoBien ? $ultimoBien->id : 0;
        $nuevoId = $ultimoId + 1;

        $codigo = $request->codigo . $nuevoId;

        $bien = new Inventario();
        $bien->tipoinventario = 'ALMACEN';
        $bien->seccion = $request->seccion;
        $bien->nombreproducto = $request->subseccion;
        $bien->codigo = $codigo;
        $bien->materiaprima = $request->materiaprima;
        $bien->marca = $request->marca;
        $bien->unidadmedida = $request->unidadmedida;
        $bien->especificacionmedida = $request->especificacionmedida;
        $bien->color = $request->color;
        $bien->inventario = $request->inventario;
        $bien->deposito = $request->deposito;
        $bien->stockinicial = $request->stockinicial;
        $bien->stockactual = $request->stockactual;
        $bien->precio = $request->precio;
        $bien->ciudad = $usuario->sucursal;
        $bien->usuarioregistroid = $usuario->id;
        $bien->usuarioregistronombre = $usuario->name;
        $bien->save();

        return redirect()->route('admin.inventario.index')->with('info', 'Inventario registrados con éxito.');
    }

    public function adquisicioninventario(Request $request)
{
    // Obtener el valor de la búsqueda
    $nombreproducto = $request->get('buscarpor');
    
    // Verificar si hay un nombre de producto para buscar
    if ($nombreproducto) {
        // Realizar la consulta de acuerdo con el nombre del producto
        $bienesalmacen = Inventario::where('nombreproducto', 'LIKE', "%$nombreproducto%")
                                    ->simplePaginate(1000);
    } else {
        // Si no hay un término de búsqueda, traer todos los registros
        $bienesalmacen = Inventario::simplePaginate(1000);
    }

    // Devolver la vista con los registros filtrados
    return view('admin.inventario.adquisicioninventario', compact('bienesalmacen'));
}


    public function generarordencompra(Request $request) 
    {
        $ordenesCompra = json_decode($request->input('ordenes_compra'), true);
        $tipotransaccion = $request->input('tipotransaccion');
        $formapago = $request->input('formapago');
        $fechacomprar = $request->input('fechacomprar');
        $proveedorid = $request->input('proveedorId');
        $observacion = $request->input('observacion');
        $montototal = $request->input('montototal');
        $subtotal = $request->input('subtotal');
        $descuento = $request->input('descuento');
        $usuarioAutenticado = auth()->user()->name;

        if (!is_array($ordenesCompra)) {
            $ordenesCompra = [];
        }
    
        $proveedor = Proveedoresservicios::find($proveedorid);
        if (!$proveedor) {
            return redirect()->back()->with('error', 'Proveedor no encontrado');
        }

        $pdfData = [
            'ordenesCompra' => $ordenesCompra,
            'tipotransaccion' => $tipotransaccion,
            'formapago' => $formapago,
            'fechacomprar' => $fechacomprar,
            'proveedor' => $proveedor,
            'observacion' => $observacion,
            'montototal' => $montototal,
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'usuarioAutenticado' => $usuarioAutenticado,
        ];

        $pdf = PDF::loadView('admin.inventario.pdfgenerarordencompra', $pdfData);
        $nombreArchivo = 'ORDEN_COMPRA_' . Str::random(10) . '.pdf';
    
        return $pdf->download($nombreArchivo);
    }

    public function guardaractivofijo(Request $request)
    {
        $request->validate([
            'seccion' => '',
            'subseccion' => '',
            'codigo' => '',
            'materiaprima' => '',
            'marca' => '',
            'unidadmedida' => '',
            'especificacionmedida' => '',
            'color' => '',
            'stockinicial' => '',
            'stockactual' => '',
            'precio' => '',
            'modelo' => '',
            'serie' => '',
        ]);

        $usuario = auth()->user();

        $ultimoBien = Inventario::latest()->first();
        $ultimoId = $ultimoBien ? $ultimoBien->id : 0;
        $nuevoId = $ultimoId + 1;

        $codigo = $request->codigo . $nuevoId;

        $bien = new Inventario();
        $bien->tipoinventario = 'ACTIVO FIJO';
        $bien->seccion = $request->seccion;
        $bien->nombreproducto = $request->subseccion;
        $bien->codigo = $codigo;
        $bien->materiaprima = $request->materiaprima;
        $bien->marca = $request->marca;
        $bien->unidadmedida = $request->unidadmedida;
        $bien->especificacionmedida = $request->especificacionmedida;
        $bien->color = $request->color;
        $bien->inventario = 'ACTIVOS FIJOS';
        $bien->stockinicial = $request->stockinicial;
        $bien->stockactual = $request->stockactual;
        $bien->precio = $request->precio;
        $bien->modelo = $request->modelo;
        $bien->serie = $request->serie;
        $bien->ciudad = $usuario->sucursal;
        $bien->usuarioregistroid = $usuario->id;
        $bien->usuarioregistronombre = $usuario->name;
        $bien->save();

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

    public function solicitarbienes(Request $request)  
    {
        $user = auth()->user();
        $nombreUsuario = $user->name;

        if (in_array($nombreUsuario, ['CARLOS ALEJANDRO GUARACHI SANDOVAL', 'DENISSE MAUREN LOPEZ FLORES', 'VANESSA MAMANI HUANACO', 'MARLENE ANDREA MONTELLANO ORTIZ', 'CRISTHIAN ALAIN DURAN SULLCA', 'JHOSELINE EVA VELASQUEZ ESCOBAR'])) {
            $solicitudinventarios = SolicitudInventario::orderBy('created_at', 'desc')->get();
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

         // Obtén los detalles del inventario para el código de producto específico
        $productoCodigo = $solicitudinventarios->first()->codigoproducto; // Asumiendo que tomas el primer producto de la solicitud
        $productoDetalles = Inventario::where('codigo', $productoCodigo)->first();

        return view('admin.inventario.solicitarbienes', compact('solicitudinventarios', 'coincidencias', 'productoDetalles'));
    }

    public function procesarsolicitud(Request $request, $solicitudId) 
    {
        $user = auth()->user();
        $nombreUsuario = $user->name;

        // Buscar la solicitud
        $solicitud = SolicitudInventario::findOrFail($solicitudId);

        // Obtener el ID del producto
        $productoId = $request->input('producto_id');
        $producto = Inventario::findOrFail($productoId);

        // Actualizar la solicitud
        $solicitud->productoofertado = $solicitud->productosolicitado . ' - ' . $solicitud->cantidad;
        $solicitud->codigoproducto = $producto->codigo;
        $solicitud->cantidadofertado = $solicitud->cantidad;
        $solicitud->estado = 'ACEPTADO';
        $solicitud->usuarioactualizacion = $nombreUsuario;

        $solicitud->save();

        // Verificar si hay usuarios a los que notificar
        $usuariosNotificar = [];
        if (!empty($solicitud->usuarioregistroid)) {
            // Convertir a un array si es un string
            $usuariosNotificar = is_string($solicitud->usuarioregistroid) ? explode(',', $solicitud->usuarioregistroid) : $solicitud->usuarioregistroid;
        }

        // Notificar a los usuarios si existen
        if (!empty($usuariosNotificar)) {
            $usuariosNotificar = User::whereIn('id', $usuariosNotificar)->get();
            foreach ($usuariosNotificar as $usuarioDestino) {
                $usuarioDestino->notify(new AceptaSolicitudNotification($solicitud));
            }
        }

        // Redirigir con mensaje de éxito
        return redirect()->back()->with('info', 'Producto ofertado con éxito!');
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

        

        // Obtener datos del usuario
        $usuario = auth()->user();
        $usuarioNombre = $usuario->name;

        // Crear el PDF
        $fecha = now()->toDateString();
        $pdf = PDF::loadView('admin.inventario.comprobante', compact('fecha', 'producto', 'cantidadOfertada', 'usuarioNombre', 'solicitud'));

        // Definir la carpeta de almacenamiento
        $folderPath = public_path("comprobanteinventario/{$solicitud->usuarioregistroid}");
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Crear el nombre del archivo
        $fileName = "comprobante_{$solicitudId}.pdf";

        // Guardar el PDF en la carpeta
        $pdf->save($folderPath . '/' . $fileName);

        // Actualizar la solicitud con el nombre del archivo PDF
        $solicitud->documento = $fileName;
        $solicitud->estado = 'PROCESADO'; // O el estado que consideres adecuado
        $solicitud->save();

        $bien = Inventario::where('codigo', $solicitud->codigoproducto)->first();
        if ($bien) {
            if ($bien->stockactual <= 3) {
                $usuariosNotificar = User::whereIn('id', [1, 3, 10, 11, 25, 31])->get();
                foreach ($usuariosNotificar as $usuarioDestino) {
                    $usuarioDestino->notify(new StockBajoNotification($bien));
                }
            }
        }

        // Redirigir de vuelta con un mensaje de éxito
        return redirect()->back()->with('info', 'Stock actualizado y solicitud procesada con comprobante guardado!');
    }


    public function aceptarOferta(Request $request, $solicitudId)
    {
        $solicitud = SolicitudInventario::findOrFail($solicitudId);
        $solicitud->estado = 'ACEPTADO';
        $solicitud->save();

        if ($solicitud) {
            $solicitud->save();

                $usuariosNotificar = User::whereIn('id', [1, 3, 10, 11, 25, 31])->get();
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
            $solicitud->save();

                $usuariosNotificar = User::whereIn('id', [1, 3, 10, 11, 25, 31])->get();
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

        $solicitud = SolicitudInventario::findOrFail($solicitudId);

        $productoId = $request->input('producto_id');
        $cantidadofertado = $request->input('cantidadofertado');
        $producto = Inventario::findOrFail($productoId);

        $solicitud->productoofertado = $producto->nombreproducto . ' - ' . $producto->marca;
        $solicitud->codigoproducto = $producto->codigo;
        $solicitud->cantidadofertado = $cantidadofertado;
        $solicitud->estado = 'OFERTADO';
        $solicitud->usuarioactualizacion = $nombreUsuario;
        $solicitud->save();

        $usuariosNotificar = [];
        if (!empty($solicitud->usuarioregistroid)) {
            $usuariosNotificar = is_string($solicitud->usuarioregistroid) ? explode(',', $solicitud->usuarioregistroid) : $solicitud->usuarioregistroid;
        }

        if (!empty($usuariosNotificar)) {
            $usuariosNotificar = User::whereIn('id', $usuariosNotificar)->get();
            foreach ($usuariosNotificar as $usuarioDestino) {
                $usuarioDestino->notify(new ActSolicitudNotification($solicitud));
            }
        }
        return redirect()->back()->with('info', 'Producto ofertado con éxito!');
    }

    public function guardarsolicitudproducto(Request $request)
    {
        $request->validate([
            'productosolicitado' => 'required|string|max:255',
            'cantidad' => 'required',
        ]);

        SolicitudInventario::create([
            'usuarioregistroid' => auth()->user()->id,
            'usuariosolicitante' => auth()->user()->name,
            'productosolicitado' => $request->productosolicitado,
            'cantidad' => $request->cantidad,
            'estado' => 'SOLICITADO',
            'sucursal' => auth()->user()->sucursal,
        ]);

        $producto = SolicitudInventario::where('productosolicitado', $request->productosolicitado)
                                        ->where('usuariosolicitante', $request->usuariosolicitante)
                                        ->first();
        if ($producto) {
            $producto->save();

                $usuariosNotificar = User::whereIn('id', [1, 3, 10, 11, 25, 31])->get();
                foreach ($usuariosNotificar as $usuarioDestino) {
                    $usuarioDestino->notify(new SolicitudProductoNotification($producto));
                }
        }
    
        return redirect()->back()->with('info', 'Solicitud registrada correctamente.');
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
    
    
}
