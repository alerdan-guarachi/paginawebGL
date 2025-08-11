<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TipoClienteController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\ClienteBancoController;
use App\Http\Controllers\Admin\ClienteAuditoriaController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\EtiquetaController;
use App\Http\Controllers\Admin\FormularioController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\AsociadoController;
use App\Http\Controllers\Admin\AreaaccionController;
use App\Http\Controllers\Admin\AccionesController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\MensajeController;
use App\Http\Controllers\Admin\InstructivaPoderController;
use App\Http\Controllers\Admin\ProveedoresserviciosController;
use App\Http\Controllers\Admin\InformeFinalController;
use App\Http\Controllers\Admin\CajaController;
use App\Http\Controllers\Admin\TramitesController;
use App\Http\Controllers\Admin\OrdenVentaController;
use App\Http\Controllers\Admin\ServiciosrequisitosController;
use App\Http\Controllers\Admin\SoporteController;
use App\Http\Controllers\Admin\BancoController;
use App\Http\Controllers\TemporalController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PaginawebController;
use App\Http\Controllers\Admin\AdministrarProgramacionController;
use App\Http\Controllers\Admin\CodigoController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\ControlProgController;
use App\Http\Controllers\Admin\CartasPolizasController;
use App\Http\Controllers\Admin\QrController;
use App\Http\Controllers\Admin\MovimientosCajaController;
use App\Http\Controllers\Admin\CajaCentralController;
use App\Http\Controllers\Admin\CuentasController;
use App\Http\Controllers\Admin\FacturasEgresoController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\FacturasEgresoControllerController;
use App\Models\Recibo;
use App\Http\Controllers\ExcelController;

Route::get('/', function () {return view('welcome');});
Route::get('/welcome', [App\Http\Controllers\PaginawebController::class, 'welcome'])->name('welcome');
Route::get('/asesoramientolegal', [App\Http\Controllers\PaginawebController::class, 'asesoramientolegal'])->name('asesoramientolegal');
Route::get('/contact', [App\Http\Controllers\PaginawebController::class, 'contact'])->name('contact');
Route::get('/medicina', [App\Http\Controllers\PaginawebController::class, 'medicina'])->name('medicina');
Route::get('/sobrenosotros', [App\Http\Controllers\PaginawebController::class, 'sobrenosotros'])->name('sobrenosotros');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])/* ->middleware('can:home') */->name('home');

Route::post('/home/store', [App\Http\Controllers\HomeController::class, 'store'])->name('admin.home.store');
Route::post('/vista-previa', 'AsociadoController@mostrarVistaPrevia');
Route::post('/generar-documento', 'AsociadoController@generarDocumento');

Route::resource("temporal",TemporalController::class);

Route::middleware(['check.session'])->group(function(){
Route::resource('roles', RoleController::class)->middleware('can:admin.roles.index')->names('admin.roles');
Route::resource('users', UserController::class)->middleware('can:admin.users.index')->names('admin.users');
Route::resource('empresas', EmpresaController::class)->middleware('can:admin.empresas.index')->names('admin.empresas');
Route::resource('proveedores', ProveedorController::class)->middleware('can:admin.proveedores.index')->names('admin.proveedores');
Route::resource('asociados', AsociadoController::class)->middleware('can:admin.asociados.index')->names('admin.asociados');
Route::resource('areaacciones', AreaaccionController::class)->middleware('can:admin.areaacciones.index')->names('admin.areaacciones');
Route::resource('acciones', AccionesController::class)->middleware('can:admin.areaacciones.index')->names('admin.acciones');
Route::resource('admprogramaciones', AdministrarProgramacionController::class)->middleware('can:admin.admprogramaciones.index')->names('admin.admprogramaciones');
Route::resource('reportes', ReporteController::class)->middleware('can:admin.reportes.index')->names('admin.reportes');
Route::resource('mensajes', MensajeController::class)->middleware('can:admin.mensajes.index')->names('admin.mensajes');
Route::resource('cartaspolizas', CartasPolizasController::class)->middleware('can:admin.cartaspolizas.index')->names('admin.cartaspolizas');
Route::resource('instructivaspoder', InstructivaPoderController::class)/* ->middleware('can:admin.mensajes.index') */->names('admin.instructivaspoder');
Route::resource('proveedoresservicios', ProveedoresserviciosController::class)/* ->middleware('can:admin.proveedoresservicios.index') */->names('admin.proveedoresservicios');
Route::resource('informesfinales', InformeFinalController::class)/* ->middleware('can:admin.mensajes.index') */->names('admin.informesfinales');
/* Route::resource('informesfinales', InformeFinalController::class)->middleware('auth')->names('admin.informesfinales'); */
Route::resource('tramites', TramitesController::class)/* ->middleware('can:admin.mensajes.index') */->names('admin.tramites');
Route::resource('serviciosrequisitos', ServiciosrequisitosController::class)/* ->middleware('can:admin.mensajes.index') */->names('admin.serviciosrequisitos');
Route::resource('ordenes/ordenesventa', OrdenVentaController::class)/* ->middleware('can:admin.mensajes.index') */->names('admin.ordenes.ordenesventa');
Route::resource('caja', MovimientosCajaController::class)->middleware('can:admin.ingreso.index')->names('admin.caja.ingreso');
Route::resource('inventario', InventarioController::class)->middleware('can:admin.inventario.index')->names('admin.inventario');
Route::resource('banco', BancoController::class)->middleware('can:admin.banco.index')->names('admin.banco');
Route::resource('facturasegreso', FacturasEgresoController::class)/* ->middleware('can:admin.banco.index') */->names('admin.facturasegreso');
Route::post('/saldocreditofiscal/guardar', [FacturasEgresoController::class, 'guardarsaldocreditofiscal'])->name('saldocreditofiscal.guardar');
Route::post('/registro-impuestos/doble', [FacturasEgresoController::class, 'guardarAmbosFormularios'])->name('registro-impuestos.doble');

Route::get('facturascontadorexterno', [FacturasEgresoController::class, 'facturascontadorexterno'])->name('admin.facturasegreso.facturascontadorexterno');
Route::post('/guardarfacturacontaext', [FacturasEgresoController::class, 'guardarfacturacontaext'])->name('admin.facturasegreso.guardarfacturacontaext');
Route::post('/compras/actualizar-estado', [FacturasEgresoController::class, 'actualizarEstado'])->name('compras.actualizar.estado');

Route::post('/permisoscodigo/cambiofacturacambiorsocial', [FacturasEgresoController::class, 'codigocambiofacturacambiorsocial'])->name('permisoscodigo.codigocambiofacturacambiorsocial');
Route::put('/facturasegreso/actualizarfacturaimpuestos/{id}', [FacturasEgresoController::class, 'actualizarfacturaimpuestos'])->name('facturasegreso.actualizarfacturaimpuestos');

/* Route::get('/notifications/check', function () {
    $user = auth()->user();
    $notificaciones = $user->notifications;
    $notificacionesNoLeidas = $notificaciones->whereNull('read_at');
    
    return response()->json([
        'new_notifications_count' => $notificacionesNoLeidas->count(),
        'notifications' => $notificaciones->map(function ($notification) {
            return [
                'id' => $notification->id,
                'read_at' => $notification->read_at,
                'data' => $notification->data
            ];
        }),
    ]);
})->name('notifications.check'); */

Route::get('/notifications/check', function () {
    $user = auth()->user();

    // Recuperamos solo notificaciones de los últimos 2 días
    $notificaciones = $user->notifications()
        ->where('created_at', '>=', now()->subDays(2))
        ->orderBy('created_at', 'desc')
        ->get();

    // Contamos las no leídas
    $notificacionesNoLeidasCount = $notificaciones->whereNull('read_at')->count();

    // Mappeamos para la respuesta JSON, incluyendo fecha formateada
    $payload = $notificaciones->map(function ($notification) {
        return [
            'id'   => $notification->id,
            'read_at' => $notification->read_at,
            'data' => $notification->data,
            'created_at_formatted' => $notification->created_at->format('d/m/Y H:i'),
        ];
    });

    return response()->json([
        'new_notifications_count' => $notificacionesNoLeidasCount,
        'notifications'           => $payload,
    ]);
})->name('notifications.check');

Route::post('/notification/{id}/read', [NotificationController::class, 'markAsRead'])->name('notification.markAsRead');


Route::get('/upload', function () {
    return view('admin.banco.index');
})->name('upload.form');

/* Route::post('/upload', [ExcelController::class, 'upload'])->name('upload.excel'); */

Route::post('/permisoscodigo/expirar', [MovimientosCajaController::class, 'expirar'])->name('permisoscodigo.expirar');

Route::post('/permisoscodigo/cajaegresos', [MovimientosCajaController::class, 'codigocajaegresos'])->name('permisoscodigo.cajaegresos');

Route::post('/permisoscodigo/cambiarstock', [InventarioController::class, 'permisoscodigocambiarstock'])->name('permisoscodigo.cambiarstock');
Route::put('/inventario/actualizarStockcodigo/{codigo}', [InventarioController::class, 'actualizarStockcodigo'])->name('inventario.actualizarStockcodigo');

Route::post('/uploadexcel', [BancoController::class, 'uploadexcel'])->name('upload.excel');
//BANCOS
    Route::get('montototalbancos', [BancoController::class, 'montototalbancos'])->name('admin.banco.montototalbancos');
    Route::get('consolidadoegresos', [BancoController::class, 'consolidadoegresos'])->name('admin.banco.consolidadoegresos');
    Route::get('detallemovimientos', [BancoController::class, 'detallemovimientos'])->name('admin.banco.detallemovimientos');
//

//INVENTARIO
    Route::get('crearactivofijo', [InventarioController::class, 'crearactivofijo'])->name('admin.inventario.crearactivofijo');
    Route::post('guardaractivofijo', [InventarioController::class, 'guardaractivofijo'])->name('admin.inventario.guardaractivofijo');
    Route::post('/guardar-entrada', [InventarioController::class, 'guardarEntrada'])->name('guardar.entrada');
    Route::post('/guardar-salida', [InventarioController::class, 'guardarSalida'])->name('guardar.salida');
    Route::get('/historial/{codigo}', 'InventarioController@obtenerHistorial')->name('historial.obtener');
    Route::get('solicitarproducto', [InventarioController::class, 'solicitarproducto'])->name('admin.inventario.solicitarproducto');
    Route::post('/inventario/solicitar', [InventarioController::class, 'guardarsolicitudproducto'])->name('admin.inventario.guardarsolicitudproducto');
    Route::post('/ofertar-producto/{solicitudId}', [InventarioController::class, 'ofertarProducto'])->name('ofertarProducto');
    Route::post('/procesarsolicitud-producto/{solicitudId}', [InventarioController::class, 'procesarsolicitud'])->name('procesarsolicitud');
    Route::put('/aceptar-oferta/{solicitudId}', [InventarioController::class, 'aceptarOferta'])->name('aceptarOferta');
    Route::put('/rechazar-oferta/{solicitudId}', [InventarioController::class, 'rechazarOferta'])->name('rechazarOferta');
    Route::put('/actualizar-stock/{solicitudId}', [InventarioController::class, 'actualizarStock'])->name('actualizarStock');
    Route::get('adquisicioninventario', [InventarioController::class, 'adquisicioninventario'])->name('admin.inventario.adquisicioninventario');
    Route::get('listaordenes', [InventarioController::class, 'listaordenes'])->name('admin.inventario.listaordenes');
    Route::post('/actualizar-prioridad-preorden', [InventarioController::class, 'actualizarPrioridad'])->name('actualizar.prioridad.preorden');
    Route::post('/unificar-preordenes', [InventarioController::class, 'unificarPreordenes'])->name('unificar.preordenes');

    Route::post('/solicitudinventario/{id}/espera', [InventarioController::class, 'pasarAEspera'])->name('solicitudinventario.espera');


    Route::post('/inventario/actualizar-stock', [InventarioController::class, 'actualizarStockinventario'])->name('inventario.actualizarStock');
    Route::post('/inventario/registrarProducto', [InventarioController::class, 'registrarProducto'])->name('inventario.registrarProducto');
    Route::get('/portfolio-proveedor/{codigo}', [InventarioController::class, 'getByCodigo'])->name('portfolioProveedores.getByCodigo');
    Route::put('/solicitudes-inventario/{id}/subir-documento', [InventarioController::class, 'subirDocumento'])->name('subirDocumento');
    Route::get('crearordenes', [InventarioController::class, 'crearordenes'])->name('admin.inventario.crearordenes');


    Route::post('admin/inventario/generarpreordencompra', [InventarioController::class, 'generarpreordencompra'])->name('generar.preordencompra');
    Route::post('admin/inventario/generarordencompra', [InventarioController::class, 'generarordencompra'])->name('generar.ordencompra');

    Route::post('admin/inventario/generarpreordenservicio', [InventarioController::class, 'generarpreordenservicio'])->name('generar.preordenservicio');
    Route::post('admin/inventario/generarordenservicio', [InventarioController::class, 'generarordenservicio'])->name('generar.ordenservicio');

    Route::post('admin/inventario/generarpreordenpersonal', [InventarioController::class, 'generarpreordenpersonal'])->name('generar.preordenpersonal');
    Route::post('admin/inventario/generarordenpersonal', [InventarioController::class, 'generarordenpersonal'])->name('generar.ordenpersonal');


    Route::post('/actualizar-fechapagar', [InventarioController::class, 'actualizarFechaPagar'])->name('actualizar.fechapagar');

    Route::post('/comprobante/masivo', [InventarioController::class, 'generarComprobanteMasivo'])->name('generarComprobanteMasivo');

    Route::post('/guardar-pagos', [InventarioController::class, 'guardarPagos'])->name('guardar.pagos');

    Route::put('/ordenes/{id}/aprobar', [InventarioController::class, 'aprobar'])->name('ordenes.aprobar');
    Route::put('/ordenes/{id}/rechazar', [InventarioController::class, 'rechazar'])->name('ordenes.rechazar');
    Route::get('planillatercerinter', [InventarioController::class, 'planillatercerinter'])->name('admin.inventario.planillatercerinter');
    Route::get('opcionesinventario', [InventarioController::class, 'opcionesinventario'])->name('admin.inventario.opcionesinventario');
    Route::post('/guardaropcioninventario', [InventarioController::class, 'guardaropcioninventario'])->name('admin.opcionesinventario.guardaropcioninventario');

    Route::post('/anular-solicitudes-inventario', [InventarioController::class, 'anularSeleccionadosinventario'])->name('anular.solicitudes.inventario');

//

//PROVEEDORES DE SERVICIOS
    Route::get('/listasecciones', [ProveedoresserviciosController::class, 'listasecciones'])->name('admin.proveedoresservicios.listasecciones');
    Route::get('/listar-subsecciones/{id}', [ProveedoresserviciosController::class, 'listarSubsecciones'])->name('listar.subsecciones');
    Route::put('/seccion/actualizar', [ProveedoresserviciosController::class, 'actualizarSeccion'])->name('seccion.actualizar');
    Route::post('/subseccion/crear', [ProveedoresserviciosController::class, 'crearSubseccion'])->name('subseccion.crear');
    Route::get('/mostrar-subsecciones/{seccionId}', [ProveedoresserviciosController::class, 'mostrarSubsecciones'])->name('mostrarSubsecciones');
    Route::get('crearprovserviciobasico/', [ProveedoresserviciosController::class, 'crearprovserviciobasico'])->name('admin.proveedoresservicios.crearprovserviciobasico');
    Route::get('crearprovpersonalexterno/', [ProveedoresserviciosController::class, 'crearprovpersonalexterno'])->name('admin.proveedoresservicios.crearprovpersonalexterno');
    Route::post('/guardarnuevoproducto', [ProveedoresserviciosController::class, 'guardarnuevoproducto'])->name('admin.proveedoresservicios.guardarnuevoproducto');
    Route::get('listaproveedoresservicios/', [ProveedoresserviciosController::class, 'listaproveedoresservicios'])->name('admin.proveedoresservicios.listaproveedoresservicios');
    Route::post('/guardarproveedor', [ProveedoresserviciosController::class, 'guardarproveedor'])->name('admin.proveedoresservicios.guardarproveedor');
    Route::post('/inactivarProducto/{id}', [ProveedoresserviciosController::class, 'inactivarProducto'])->name('admin.proveedoresservicios.inactivarproducto');
    Route::get('verproveedorexterno/{proveedoresservicios}', [ProveedoresserviciosController::class, 'verproveedorexterno'])->name('admin.proveedoresservicios.verproveedorexterno');
    Route::get('editarproveedorexterno/{proveedoresservicios}', [ProveedoresserviciosController::class, 'editarproveedorexterno'])->name('admin.proveedoresservicios.editarproveedorexterno');
    Route::post('actualizarproveedorexterno/{proveedoresservicios}', [ProveedoresserviciosController::class, 'actualizarproveedorexterno'])->name('admin.proveedoresservicios.actualizarproveedorexterno');
    Route::get('verproveedorserviciobasico/{proveedoresservicios}', [ProveedoresserviciosController::class, 'verproveedorserviciobasico'])->name('admin.proveedoresservicios.verproveedorserviciobasico');
    Route::get('editarproveedorserviciobasico/{proveedoresservicios}', [ProveedoresserviciosController::class, 'editarproveedorserviciobasico'])->name('admin.proveedoresservicios.editarproveedorserviciobasico');
    Route::post('actualizarproveedorserviciobasico/{proveedoresservicios}', [ProveedoresserviciosController::class, 'actualizarproveedorserviciobasico'])->name('admin.proveedoresservicios.actualizarproveedorserviciobasico');
    Route::post('/guardarnuevoplan', [ProveedoresserviciosController::class, 'guardarnuevoplan'])->name('admin.proveedoresservicios.guardarnuevoplan');
    Route::post('/planesinactivar/{id}', [ProveedoresserviciosController::class, 'planesinactivar'])->name('admin.proveedoresservicios.planesinactivar');
    Route::put('/admin/proveedoresservicios/pasar-a-personal/{id}', [ProveedoresserviciosController::class, 'pasarAPersonal'])->name('admin.proveedoresservicios.pasarapersonal');
    Route::get('/admin/listapersonal', [ProveedoresserviciosController::class, 'listapersonal'])->name('admin.proveedoresservicios.listapersonal');
    Route::get('/admin/verpersonal/{id}', [ProveedoresserviciosController::class, 'verpersonal'])->name('admin.proveedoresservicios.verpersonal');
    Route::get('/admin/editarpersonal/{id}', [ProveedoresserviciosController::class, 'editarpersonal'])->name('admin.proveedoresservicios.editarpersonal');
    Route::post('actualizarpersonal/{proveedoresservicios}', [ProveedoresserviciosController::class, 'actualizarpersonal'])->name('admin.proveedoresservicios.actualizarpersonal');
    Route::post('/guardardocumentacionproveedor/{id}', [ProveedoresserviciosController::class, 'guardardocumentacionproveedor'])->name('admin.proveedoresservicios.guardardocumentacionproveedor');
    Route::get('/admin/viajespersonal/{id}', [ProveedoresserviciosController::class, 'viajespersonal'])->name('admin.proveedoresservicios.viajespersonal');
    Route::get('/admin/vacacionespersonal/{id}', [ProveedoresserviciosController::class, 'vacacionespersonal'])->name('admin.proveedoresservicios.vacacionespersonal');
    Route::post('/guardarvacacionespersonal/{id}', [ProveedoresserviciosController::class, 'guardarvacacionespersonal'])->name('admin.proveedoresservicios.guardarvacacionespersonal');
    Route::post('/vacaciones/aprobarsolicitudvacacion/{id}', [ProveedoresserviciosController::class, 'aprobarsolicitudvacacion'])->name('admin.proveedoresservicios.aprobarsolicitudvacacion');
    Route::post('/vacaciones/rechazarsolicitudvacacion/{id}', [ProveedoresserviciosController::class, 'rechazarsolicitudvacacion'])->name('admin.proveedoresservicios.rechazarsolicitudvacacion');
    Route::post('/guardarviajespersonal/{id}', [ProveedoresserviciosController::class, 'guardarviajespersonal'])->name('admin.proveedoresservicios.guardarviajespersonal');
    Route::post('/guardarviajespersonaldetallado/{id}', [ProveedoresserviciosController::class, 'guardarviajespersonaldetallado'])->name('admin.proveedoresservicios.guardarviajespersonaldetallado');
    Route::post('/vacaciones/aprobarsolicitudviaje/{id}', [ProveedoresserviciosController::class, 'aprobarsolicitudviaje'])->name('admin.proveedoresservicios.aprobarsolicitudviaje');
    Route::post('/vacaciones/rechazarsolicitudviaje/{id}', [ProveedoresserviciosController::class, 'rechazarsolicitudviaje'])->name('admin.proveedoresservicios.rechazarsolicitudviaje');
    Route::post('/guardarrendicionviajespersonal/{id}', [ProveedoresserviciosController::class, 'guardarrendicionviajespersonal'])->name('admin.proveedoresservicios.guardarrendicionviajespersonal');

    Route::post('/verificar-codigo-adelantovacaciones', [ProveedoresserviciosController::class, 'verificarCodigoAdelantovacaciones'])->name('verificar.codigo.adelantovacaciones'); 
//

//CARTAS POLIZAS
    Route::post('/admin/cartaspolizas/descargarsolicitudpolizas', [CartasPolizasController::class, 'descargarsolicitudpolizas'])->name('admin.cartaspolizas.descargarsolicitudpolizas');
    Route::post('/admin/cartaspolizas/descargarreclamosolicitudpolizas', [CartasPolizasController::class, 'descargarreclamosolicitudpolizas'])->name('admin.cartaspolizas.descargarreclamosolicitudpolizas');
    Route::post('/admin/cartaspolizas/descargarreclamosolicitudpolizasgenerales', [CartasPolizasController::class, 'descargarreclamosolicitudpolizasgenerales'])->name('admin.cartaspolizas.descargarreclamosolicitudpolizasgenerales');
    Route::get('admin/cartaspolizas/listacartaspolizas', [CartasPolizasController::class, 'listacartaspolizas'])->name('admin.cartaspolizas.listacartaspolizas');
//

//INGRESOS
    Route::get('caja/cierreIngreso', [MovimientosCajaController::class, 'cierreCaja_Ingresos'])->name('admin.caja.ingreso.cierre');
    Route::post('/buscar-cliente', [MovimientosCajaController::class, 'buscarPorCliente'])->name('buscar.cliente');
    Route::post('/guardar-cajacentral', [MovimientosCajaController::class, 'guardarCajaCentral'])->name('guardar.cajacentral');
    Route::post('/guardar-arqueo', [MovimientosCajaController::class, 'guardarArqueo'])->name('guardar.arqueo');
    Route::post('/verificar-codigo', [MovimientosCajaController::class, 'verificarCodigo'])->name('verificar.codigo'); 
    Route::post('/verificar-codigo2', [MovimientosCajaController::class, 'verificarCodigo2'])->name('verificar.codigo2'); 
    Route::post('/verificar-codigo3', [MovimientosCajaController::class, 'verificarCodigo3'])->name('verificar.codigo3');
    Route::post('/verificar-codigo4', [MovimientosCajaController::class, 'verificarCodigo4'])->name('verificar.codigo4');  
    Route::post('/apertura/guardar', [MovimientosCajaController::class, 'storeAperturaCaja'])->name('apertura.guardar');
    
    Route::get('caja/ingreso/ingresosexternos/', [MovimientosCajaController::class, 'ingresosexternos'])->name('admin.caja.ingreso.ingresosexternos');
    Route::post('/buscar-proveedor-ingresoexterno', [MovimientosCajaController::class, 'buscarPorProveedoringresoexterno'])->name('buscar.proveedor.ingresoexterno');
    Route::post('/guardar-cajacentral-ingresoexterno', [MovimientosCajaController::class, 'guardarCajaCentralingresoexterno'])->name('guardar.cajacentral.ingresoexterno');
    Route::get('/admin/caja/ingreso/siguiente-id', function () {$ultimoId = Recibo::max('id');$siguienteId = $ultimoId ? $ultimoId + 1 : 1;return response()->json(['siguienteId' => $siguienteId]);})->name('actualizar_id');
    Route::get('/recibos/siguiente-id', [MovimientosCajaController::class, 'obtenerSiguienteId']);

    Route::get('/admin/caja/ingreso/siguiente-idexterno', function () {$ultimoId = Recibo::max('id');$siguienteId = $ultimoId ? $ultimoId + 1 : 1;return response()->json(['siguienteId' => $siguienteId]);})->name('actualizar_id_externo');
    Route::post('/obtener-creditos', [MovimientosCajaController::class, 'obtenerCreditos'])->name('obtener.creditos');

    Route::get('caja/ingreso/historialcierrescaja/', [MovimientosCajaController::class, 'historialcierrescaja'])->name('admin.caja.ingreso.historialcierrescaja');

//

//CIERRE DE CAJA Y DOCUMENTACION INGRESOS
    Route::get('caja/ingreso/cierre/', [MovimientosCajaController::class, 'cierrecajaingresos'])->name('admin.caja.ingreso.cierre');
    Route::get('caja/ingreso/documentacion/', [MovimientosCajaController::class, 'respaldodocumentacioningreso'])->name('admin.caja.ingreso.documentacion');
    Route::post('/admin/caja/ingreso/guardar', [MovimientosCajaController::class, 'guardarRespaldo'])->name('guardar.respaldo');
    Route::post('caja/ingreso/cierre', [MovimientosCajaController::class, 'manejarCierreCaja'])->name('cierre.caja');
    Route::post('/actualizar-estado', [MovimientosCajaController::class, 'actualizarEstado'])->name('actualizar.estado');

    Route::get('caja/ingreso/depositosbancarios/', [MovimientosCajaController::class, 'depositosbancarios'])->name('admin.caja.ingreso.depositosbancarios');
    Route::post('/guardar-depositobancario', [MovimientosCajaController::class, 'guardardepositobancario'])->name('guardardepositobancario');
//

//CUENTAS POR COBRAR
    Route::get('caja/cuentascobrar/cobrarhoy/', [MovimientosCajaController::class, 'cobrarhoy'])->name('admin.caja.cuentascobrar.cobrarhoy');
    Route::get('caja/cuentascobrar/ccporcredito/', [MovimientosCajaController::class, 'ccporcredito'])->name('admin.caja.cuentascobrar.ccporcredito');
    Route::get('/buscarccporfecha', [MovimientosCajaController::class, 'buscarccporfecha'])->name('buscarccporfecha');
    Route::post('/actualizar-registros', [MovimientosCajaController::class, 'actualizarRegistros'])->name('actualizarRegistros');
    Route::get('caja/cuentascobrar/listacuentascobrar/', [MovimientosCajaController::class, 'listacuentascobrar'])->name('admin.caja.cuentascobrar.listacuentascobrar');
    Route::get('/buscarlistacuentascobrar', [MovimientosCajaController::class, 'buscarlistacuentascobrar'])->name('buscarlistacuentascobrar');
    Route::post('/actualizar-cantidadcuotas', [MovimientosCajaController::class, 'actualizarCantidadCuotas'])->name('actualizar.cantidadcuotas');
    Route::post('/agregarcartacredito', [MovimientosCajaController::class, 'agregarcartacredito'])->name('agregarcartacredito');
    Route::get('caja/cuentascobrar/creditosaprobados/', [MovimientosCajaController::class, 'creditosaprobados'])->name('admin.caja.cuentascobrar.creditosaprobados');
    Route::put('/creditos/update-multiple', [MovimientosCajaController::class, 'creditosupdatefecha'])->name('creditos.update');

    Route::get('caja/cuentascobrar/nuevacuentacobrar/', [MovimientosCajaController::class, 'nuevacuentacobrar'])->name('admin.caja.cuentascobrar.nuevacuentacobrar');
    Route::post('/guardarcuentacobrar', [MovimientosCajaController::class, 'guardarcuentacobrar'])->name('guardar.cuentacobrar');

    Route::post('/actualizar-prioridad-programacion', [MovimientosCajaController::class, 'actualizarPrioridadProgramacion'])->name('actualizar.prioridad.programacion');

    Route::post('/guardardetallecxc', [MovimientosCajaController::class, 'guardardetallecxc'])->name('admin.caja.cuentascobrar.guardardetallecxc');
//

//EGRESOS
    Route::get('caja/egreso/cierrecajaegreso/', [MovimientosCajaController::class, 'cierrecajaegreso'])->name('admin.caja.egreso.cierrecajaegreso');
    Route::get('caja/egreso/cajaegresos/', [MovimientosCajaController::class, 'cajaegresos'])->name('admin.caja.egreso.cajaegresos');
    Route::get('caja/egreso/documentacion/', [MovimientosCajaController::class, 'respaldodocumentacionegreso'])->name('admin.caja.egreso.documentacion');
    Route::POST('/admin/caja/egreso/cierrecajaegreso', [MovimientosCajaController::class, 'manejarCierreCajaegreso'])->name('cierre.caja.egreso');
    Route::post('/actualizar-estado-egreso', [MovimientosCajaController::class, 'actualizarEstadoegreso'])->name('actualizar.estado.egreso');
    Route::get('/admin/caja/egreso/siguiente-id', function () {$ultimoId = Recibo::max('id');$siguienteId = $ultimoId ? $ultimoId + 1 : 1;return response()->json(['siguienteId' => $siguienteId]);})->name('actualizar_id_egreso');
    Route::post('/buscar-proveedor-egreso', [MovimientosCajaController::class, 'buscarPorProveedoregreso'])->name('buscar.proveedor.egreso');
    Route::post('/guardar-arqueo-egreso', [MovimientosCajaController::class, 'guardarArqueoegreso'])->name('guardar.arqueo.egreso');
    Route::post('/guardar-cajacentral-egreso', [MovimientosCajaController::class, 'guardarCajaCentralegreso'])->name('guardar.cajacentral.egreso');
    Route::get('caja/egreso/documentacionegreso/', [MovimientosCajaController::class, 'respaldodocumentacionegreso'])->name('admin.caja.egreso.documentacionegreso');
    Route::post('/admin/caja/egreso/guardarrespaldoegreso', [MovimientosCajaController::class, 'guardarRespaldoegreso'])->name('guardar.respaldo.egreso');

    Route::get('caja/egreso/cajaegresoscomprobantes/', [MovimientosCajaController::class, 'cajaegresoscomprobantes'])->name('admin.caja.egreso.cajaegresoscomprobantes');
    Route::post('/buscar-proveedor-egresocomprobantes', [MovimientosCajaController::class, 'buscarPorProveedoregresocomprobantes'])->name('buscar.proveedor.egresocomprobantes');
    Route::post('/guardar-cajacentral-egresocomprobantes', [MovimientosCajaController::class, 'guardarCajaCentralegresocomprobantes'])->name('guardar.cajacentral.egresocomprobantes');
//

//CUENTAS POR PAGAR
    Route::get('caja/cuentaspagar/cppregistradas/', [MovimientosCajaController::class, 'cppregistradas'])->name('admin.caja.cuentaspagar.cppregistradas');
    Route::get('caja/cuentaspagar/registrarcpp/', [MovimientosCajaController::class, 'registrarcpp'])->name('admin.caja.cuentaspagar.registrarcpp');
    Route::get('/buscarcppporfecha', [MovimientosCajaController::class, 'buscarcppporfecha'])->name('buscarcppporfecha');
    Route::post('/proveedores/obtener', [MovimientosCajaController::class, 'obtenerProveedores'])->name('obtener.proveedores');
    Route::post('caja/cuentaspagar/registrarcpp/', [MovimientosCajaController::class, 'guardarCuentaPagar'])->name('guardar.cuenta.pagar');

    Route::get('caja/panel/resumenfinanciero/', [MovimientosCajaController::class, 'resumenfinanciero'])->name('admin.caja.panel.resumenfinanciero');
    Route::get('caja/anulaciones/anularcaja/', [MovimientosCajaController::class, 'anularcaja'])->name('admin.caja.anulaciones.anularcaja');
    Route::post('/anular-regitro-caja', [MovimientosCajaController::class, 'anularregitrocaja'])->name('anularregitrocaja');

    Route::get('caja/cuentaspagar/listacuentaspagar/', [MovimientosCajaController::class, 'listacuentaspagar'])->name('admin.caja.cuentaspagar.listacuentaspagar');
    Route::get('/buscarlistacuentaspagar', [MovimientosCajaController::class, 'buscarlistacuentaspagar'])->name('buscarlistacuentaspagar');

    Route::post('/actualizarFactura', [MovimientosCajaController::class, 'actualizarFactura'])->name('actualizarFactura');

    Route::get('/reporte-cuentas-pagar', [MovimientosCajaController::class, 'generarPDF'])->name('reporte.cuentaspagar');
    Route::get('/cuentas/pdf/{fecha}', [MovimientosCajaController::class, 'generarPDFprovservicios'])->name('cuentas.pdf');
    Route::post('/aprobar-registros', [MovimientosCajaController::class, 'aprobarSeleccionados'])->name('aprobar.registros');
    Route::post('/rechazar-registros', [MovimientosCajaController::class, 'rechazarSeleccionados'])->name('rechazar.registros');
    Route::post('/cambiarfecha-registros', [MovimientosCajaController::class, 'cambiarfechaSeleccionados'])->name('cambiarfecha.registros');
    Route::post('/sugerir-pagos', [MovimientosCajaController::class, 'sugerirpagosSeleccionados'])->name('sugerir.pagos');
    Route::post('/sugerir-pagos-nomora', [MovimientosCajaController::class, 'sugerirpagosSeleccionadosnomora'])->name('sugerir.pagos.nomora');
    Route::post('/cuentas/marcar-cargado', [MovimientosCajaController::class, 'marcarComoCargado'])->name('marcar.cargado');

    Route::post('/cuentasporpagar/facturas/otrosprov', [MovimientosCajaController::class, 'subirFacturasOtrosProv'])->name('cuentasporpagar.facturas.otrosprov');

    // routes/web.php
    Route::post('/guardar-qr', [MovimientosCajaController::class, 'guardarQR'])->name('guardar.qr');
    Route::get('caja/cuentaspagar/cpppendientes/', [MovimientosCajaController::class, 'cpppendientes'])->name('admin.caja.cuentaspagar.cpppendientes');
    Route::post('/actualizar-estado-aprobacion', [MovimientosCajaController::class, 'actualizarEstadoCargado'])->name('actualizar.estado.aprobacion');
    Route::get('caja/cuentaspagar/cppcomprobantes/', [MovimientosCajaController::class, 'cppcomprobantes'])->name('admin.caja.cuentaspagar.cppcomprobantes');
    Route::post('/actualizar-comprobante', [MovimientosCajaController::class, 'actualizarComprobante'])->name('actualizar.comprobante');
    Route::post('/informar-subida', [MovimientosCajaController::class, 'informarSubida'])->name('informar.subida');
    Route::post('/informar-subida-cxplistas', [MovimientosCajaController::class, 'informarSubidaCxPlistas'])->name('informar.subida.cxplistas');

    Route::post('/cuentasporpagar/actualizar-monto/{id}', [MovimientosCajaController::class, 'actualizarMonto'])->name('cuentasporpagar.actualizar.monto');




//

//ORDENS DE VENTA CLIENTE BANCO
    /* Route::get('ordenes/ordenesventa', [OrdenVentaController::class, 'index'])->name('admin.ordenes.ordenesventa.index'); */
    Route::get('ordenes/ordenesventa/create/{clientebanco}', [OrdenVentaController::class, 'create'])->name('admin.ordenes.ordenesventa.create');
    Route::post('ordenes/ordenesventa/create/crearNotadeVenta', [OrdenVentaController::class, 'crearNotadeVenta'])->name('admin.ordenes.ordenesventa.create.crearnotadeventa');
    Route::get('ordenes/ordenesventa/{clientebanco}/generarPDF', [OrdenVentaController::class, 'generarPDFNotaDeVenta'])->name('admin.ordenes.ordenesventa.generarPDF');
//

//CONTROL DE PROGRAMACIONES
    Route::resource('controlprogramacion', ControlProgController::class)/* ->middleware('can:admin.mensajes.index') */->names('admin.controlprogramacion');
    Route::get('/buscar-usuario', [ControlProgController::class, 'buscarPorUsuario'])->name('admin.controlprogramacion.buscarPorUsuario');
    Route::post('/confirmar-pagos', [AdministrarProgramacionController::class, 'confirmarPagos'])->name('confirmar-pagos');
    Route::post('/confirmar-pagos-informefinal', [AdministrarProgramacionController::class, 'confirmarPagosInformesfinales'])->name('confirmar-pagos-informefinal');

    Route::post('/pdf/upload', [AdministrarProgramacionController::class, 'upload'])->name('pdf.upload');
    Route::post('/pdf/merge', [AdministrarProgramacionController::class, 'merge'])->name('pdf.merge');
    Route::get('/unirpdf', [AdministrarProgramacionController::class, 'unirpdf'])->name('admin.admprogramaciones.unirpdf');
//

//INFORMES DE PROVEEDORES Y DIAGNOSTICOS
    Route::get('asociados/{id}/download-pdf', [AsociadoController::class, 'downloadPDF'])->name('admin.asociados.downloadPDF');

    Route::post('/procesar-informe', [InformeFinalController::class, 'procesarInforme'])->name('procesar.informe');
    Route::post('/procesar-informe-multiple', [InformeFinalController::class, 'procesarInformeMultiple'])->name('procesar.informe.multiple');
    Route::post('/procesar-informeauditoria', [InformeFinalController::class, 'procesarInformeauditoria'])->name('procesar.informeauditoria');
    Route::post('/procesar-informe-multipleauditoria', [InformeFinalController::class, 'procesarInformeMultipleauditoria'])->name('procesar.informe.multiple.auditoria');


    Route::post('/procesar-diagnostico', [InformeFinalController::class, 'procesardiagnostico'])->name('procesar.diagnostico');
    Route::post('/procesar-diagnosticoauditoria', [InformeFinalController::class, 'procesardiagnosticoauditoria'])->name('procesar.diagnosticoauditoria');

//

//INFORMES FINALES
    Route::get('informesfinales/documentosprogramaciones/7', 'App\Http\Controllers\Admin\InformeFinalController@documentosprogramaciones')->name('admin.informesfinales.documentosprogramaciones');
    Route::get('/buscarprogramacionesclienteita', 'App\Http\Controllers\Admin\InformeFinalController@buscarporproveedor')->name('buscarporproveedor');
    Route::get('/buscarclientereservamedica', 'App\Http\Controllers\Admin\InformeFinalController@buscarclientereservamedica')->name('buscarclientereservamedica');
    Route::get('/buscarporproveedor', 'App\Http\Controllers\Admin\InformeFinalController@buscarprogramacionesclienteita')->name('buscarprogramacionesclienteita');
    Route::get('/buscarprogramacionesclienteauditoria', 'App\Http\Controllers\Admin\InformeFinalController@buscarprogramacionesclienteauditoria')->name('buscarprogramacionesclienteauditoria');
    Route::get('/buscarprogramacionescomclienteita', 'App\Http\Controllers\Admin\InformeFinalController@buscarprogramacionescomclienteita')->name('buscarprogramacionescomclienteita');
    Route::get('/buscarreservamedicaclienteita', 'App\Http\Controllers\Admin\InformeFinalController@buscarreservamedicaclienteita')->name('buscarreservamedicaclienteita');
    Route::post('informesfinales/guardaraprobacioninformefinal/{item}', 'App\Http\Controllers\Admin\InformeFinalController@guardaraprobacioninformefinal')->name('admin.informesfinales.guardaraprobacioninformefinal');
    Route::post('informesfinales/guardaraprobacioninformefinalauditoria/{item}', 'App\Http\Controllers\Admin\InformeFinalController@guardaraprobacioninformefinalauditoria')->name('admin.informesfinales.guardaraprobacioninformefinalauditoria');
    Route::post('informesfinales/guardarproveedorinformefinal/{item}', 'App\Http\Controllers\Admin\InformeFinalController@guardarproveedorinformefinal')->name('admin.informesfinales.guardarproveedorinformefinal');
    Route::post('informesfinales/guardarinformefinal/{item}', 'App\Http\Controllers\Admin\InformeFinalController@guardarinformefinal')->name('admin.informesfinales.guardarinformefinal');
    Route::post('informesfinales/guardarinformefinalauditoria/{item}', 'App\Http\Controllers\Admin\InformeFinalController@guardarinformefinalauditoria')->name('admin.informesfinales.guardarinformefinalauditoria');
    Route::delete('informesfinales/solrevisioninformefinal/{item}', 'App\Http\Controllers\Admin\InformeFinalController@solrevisioninformefinal')->name('admin.informesfinales.solrevisioninformefinal');
    Route::delete('informesfinales/solrevisioninformefinalauditoria/{item}', 'App\Http\Controllers\Admin\InformeFinalController@solrevisioninformefinalauditoria')->name('admin.informesfinales.solrevisioninformefinalauditoria');
    Route::put('informesfinales/aprobarinformefinalfs/{item}', 'App\Http\Controllers\Admin\InformeFinalController@aprobarinformefinalfs')->name('admin.informesfinales.aprobarinformefinalfs');
    Route::put('informesfinales/aprobarinformefinalfsauditoria/{item}', 'App\Http\Controllers\Admin\InformeFinalController@aprobarinformefinalfsauditoria')->name('admin.informesfinales.aprobarinformefinalfsauditoria');
    Route::get('/buscarresultadosclientebanco', 'App\Http\Controllers\Admin\InformeFinalController@buscarresultadosclientebanco')->name('buscarresultadosclientebanco');
    Route::get('/buscarconsiliacionclientebanco', 'App\Http\Controllers\Admin\InformeFinalController@buscarconsiliacionclientebanco')->name('buscarconsiliacionclientebanco');

    Route::get('informesfinales/estadodocumentacionprogramacion/7', 'App\Http\Controllers\Admin\InformeFinalController@estadodocumentacionprogramacion')->name('admin.informesfinales.estadodocumentacionprogramacion');
    Route::get('informesfinales/resultadosmedicosclientesauditoria/8', 'App\Http\Controllers\Admin\InformeFinalController@resultadosmedicosclientesauditoria')->name('admin.informesfinales.resultadosmedicosclientesauditoria');
    Route::get('informesfinales/resultadosmedicosclientesbancos/9', 'App\Http\Controllers\Admin\InformeFinalController@resultadosmedicosclientesbancos')->name('admin.informesfinales.resultadosmedicosclientesbancos');
    Route::get('informesfinales/informesfinalesauditoria/6', 'App\Http\Controllers\Admin\InformeFinalController@informesfinalesauditoria')->name('admin.informesfinales.informesfinalesauditoria');
    Route::get('admprogramaciones/controlregistros/7', 'App\Http\Controllers\Admin\AdministrarProgramacionController@controlregistros')->name('admin.admprogramaciones.controlregistros');
    Route::get('informesfinales/reservasmedicas/8', 'App\Http\Controllers\Admin\InformeFinalController@reservasmedicas')->name('admin.informesfinales.reservasmedicas');
    Route::get('informesfinales/consiliacionesclientesbanco/10', 'App\Http\Controllers\Admin\InformeFinalController@consiliacionesclientesbanco')->name('admin.informesfinales.consiliacionesclientesbanco');

    Route::get('/buscarresultadosmedicosclientesauditoria', 'App\Http\Controllers\Admin\InformeFinalController@buscarresultadosmedicosclientesauditoria')->name('buscarresultadosmedicosclientesauditoria');
    
    Route::post('/update-observacion', [InformeFinalController::class, 'updateObservacion'])->name('updateObservacion');
    Route::post('/update-document/{id}', [InformeFinalController::class, 'updateDocument'])->name('updateDocument');

    Route::get('/generar-pdf-orden/{clienteitaid}/{fechabateria}/{clienteitanombre}/{proveedornombre}', [InformeFinalController::class, 'generarPDForden'])->name('generar.pdf.orden');
    Route::get('/generar-pdf-ordenauditoria/{clienteauditoriaid}/{fechabateria}/{clienteauditorianombre}/{proveedornombre}', [InformeFinalController::class, 'generarPDFordenauditoria'])->name('generar.pdf.ordenauditoria');

//

//TRAMITES
    Route::get('tramites/procmasahereditaria/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procmasahereditaria')->name('admin.tramites.procmasahereditaria');
    Route::get('tramites/procjubilacion/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procjubilacion')->name('admin.tramites.procjubilacion');
    Route::get('tramites/procretiroaportestotal/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procretiroaportestotal')->name('admin.tramites.procretiroaportestotal');
    Route::get('tramites/procretiroaportesparcial/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procretiroaportesparcial')->name('admin.tramites.procretiroaportesparcial');
    Route::get('tramites/procpensionpormuerte/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procpensionpormuerte')->name('admin.tramites.procpensionpormuerte');
    Route::get('tramites/procsegundasolicitud/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procsegundasolicitud')->name('admin.tramites.procsegundasolicitud');
    Route::get('tramites/procinvalidez/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procinvalidez')->name('admin.tramites.procinvalidez');
    Route::get('tramites/procapelacion/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procapelacion')->name('admin.tramites.procapelacion');
    Route::get('tramites/proccompensacionsenasir/{cliente}', 'App\Http\Controllers\Admin\TramitesController@proccompensacionsenasir')->name('admin.tramites.proccompensacionsenasir');

    Route::post('tramites/guardartramitesclienteita/{cliente}', 'App\Http\Controllers\Admin\TramitesController@guardartramitesclienteita')->name('admin.tramites.guardartramitesclienteita');
    Route::post('tramites/guardariniciotramiteclienteita/{cliente}', 'App\Http\Controllers\Admin\TramitesController@guardariniciotramiteclienteita')->name('admin.tramites.guardariniciotramiteclienteita');
    
    Route::get('tramites/generarcartareclamo/{cliente}', 'App\Http\Controllers\Admin\TramitesController@generarcartareclamo')->name('admin.tramites.generarcartareclamo');
    Route::get('tramites/generaradjuntoyrespuesta/{cliente}', 'App\Http\Controllers\Admin\TramitesController@generaradjuntoyrespuesta')->name('admin.tramites.generaradjuntoyrespuesta');
    Route::get('tramites/generarsolicitud/{cliente}', 'App\Http\Controllers\Admin\TramitesController@generarsolicitud')->name('admin.tramites.generarsolicitud');

    Route::get('/tramites/actualizarEstado/{id}/{clienteId}', [TramitesController::class, 'actualizarEstado'])->name('tramites.actualizarEstado');
    Route::post('/tramites/{id}/subir-archivo/{clienteId}', [TramitesController::class, 'subirArchivo'])->name('tramites.subirArchivo');
    Route::post('tramites/asignarapoderadotramiteclienteita/{cliente}', 'App\Http\Controllers\Admin\TramitesController@asignarapoderadotramiteclienteita')->name('admin.tramites.asignarapoderadotramiteclienteita');
    Route::post('tramites/guardartramitesclienteitaseguimiento/{cliente}', 'App\Http\Controllers\Admin\TramitesController@guardartramitesclienteitaseguimiento')->name('admin.tramites.guardartramitesclienteitaseguimiento');

    Route::get('tramites/modelocartareclamo/1', 'App\Http\Controllers\Admin\TramitesController@modelocartareclamo')->name('admin.tramites.modelocartareclamo');

    Route::post('/permisoscodigo/cambiofechaarchivoprestaciones', [TramitesController::class, 'codigocambiofechaarchivoprestaciones'])->name('permisoscodigo.cambiofechaarchivoprestaciones');

    Route::post('/tramites/reemplazar-archivo', [TramitesController::class, 'reemplazarArchivo'])->name('tramites.reemplazarArchivo');
    Route::put('tramites/actualizardatoscliente/{cliente}', [TramitesController::class, 'actualizardatoscliente'])->name('admin.tramites.actualizardatoscliente');

    Route::post('tramites/guardarrespuesta/{cliente}', [TramitesController::class, 'guardarrespuesta'])->name('admin.tramites.guardarrespuesta');
    Route::post('tramites/guardarcriterios/{cliente}', [TramitesController::class, 'guardarcriterios'])->name('admin.tramites.guardarcriterios');
//


/* Route::get('tramites/sitsegundacarta/{cliente}', 'App\Http\Controllers\Admin\TramitesController@sitsegundacarta')->name('admin.tramites.sitsegundacarta');
Route::get('tramites/sitterceracarta/{cliente}', 'App\Http\Controllers\Admin\TramitesController@sitterceracarta')->name('admin.tramites.sitterceracarta');
Route::get('tramites/reclamoprimeracarta/{cliente}', 'App\Http\Controllers\Admin\TramitesController@sitprimeracarta')->name('admin.tramites.reclamoprimeracarta');
Route::get('tramites/reclamosegundacarta/{cliente}', 'App\Http\Controllers\Admin\TramitesController@sitprimeracarta')->name('admin.tramites.reclamosegundacarta');
Route::get('tramites/reclamoterceracarta/{cliente}', 'App\Http\Controllers\Admin\TramitesController@sitprimeracarta')->name('admin.tramites.reclamoterceracarta');
Route::get('tramites/reclamoaps/{cliente}', 'App\Http\Controllers\Admin\TramitesController@reclamoaps')->name('admin.tramites.reclamoaps'); */

Route::get('asociados/programacionpendienteauditoria/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@verProgramacionPendienteAuditoria')->name('admin.asociados.verprogramacionauditoria');
Route::get('asociados/buscarprogramacionpendienteauditoria/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@buscarProgramacionPendienteAuditoria')->name('admin.asociados.buscarprogramacionpendienteauditoria');
Route::get('asociados/programacionpendienteita/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@verProgramacionPendienteITA')->name('admin.asociados.verprogramacionita');
Route::get('asociados/buscarprogramacionpendienteita/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@buscarProgramacionPendienteITA')->name('admin.asociados.buscarprogramacionpendienteita');
Route::get('asociados/programacionpendientecomun/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@verProgramacionPendienteComun')->name('admin.asociados.verprogramacioncomun');
Route::get('asociados/buscarprogramacionpendientecomun/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@buscarProgramacionPendienteComun')->name('admin.asociados.buscarprogramacionpendientecomun');

//CREAR Y EDITAR BATERIA DE GOOD LIFE
    Route::get('asociados/verbateriaasociado/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@verbateriaasociado')->name('admin.asociados.verbateriaasociado');
    Route::get('asociados/editarbateriaasociado/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@editarbateriaasociado')->name('admin.asociados.editarbateriaasociado');
    Route::put('asociados/actualizarbateriaasociado/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@actualizarbateriaasociado')->name('admin.asociados.actualizarbateriaasociado');
    Route::get('asociados/crearbateriaasociado/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@crearbateriaasociado')->name('admin.asociados.crearbateriaasociado');
    Route::post('asociados/guardarbateriaasociado/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@guardarbateriaasociado')->name('admin.asociados.guardarbateriaasociado');

    Route::put('/areaacciones/{id}/cambiarEstado', [AreaaccionController::class, 'cambiarEstado'])->name('cambiarEstado');
//

//VER Y ADMINISTRAR REGISTROS 
    Route::get('admprogramaciones/documentacionpendiente/7', 'App\Http\Controllers\Admin\AdministrarProgramacionController@documentacionpendiente')->name('admin.admprogramaciones.documentacionpendiente');
    Route::get('admprogramaciones/documentacionactiva/7', 'App\Http\Controllers\Admin\AdministrarProgramacionController@documentacionactiva')->name('admin.admprogramaciones.documentacionactiva');
    Route::get('admprogramaciones/clientescreadoshoy/7', 'App\Http\Controllers\Admin\AdministrarProgramacionController@clientescreadoshoy')->name('admin.admprogramaciones.clientescreadoshoy');
    Route::get('admprogramaciones/pagosprogramaciones/7', 'App\Http\Controllers\Admin\AdministrarProgramacionController@pagosprogramaciones')->name('admin.admprogramaciones.pagosprogramaciones');
    Route::get('admprogramaciones/bateriascreadoshoy/7', 'App\Http\Controllers\Admin\AdministrarProgramacionController@bateriascreadoshoy')->name('admin.admprogramaciones.bateriascreadoshoy');
    Route::get('/buscarclientesporfecha', 'App\Http\Controllers\Admin\AdministrarProgramacionController@buscarclientesporfecha')->name('buscarclientesporfecha');
    Route::get('/buscarbateriasporfecha', 'App\Http\Controllers\Admin\AdministrarProgramacionController@buscarbateriasporfecha')->name('buscarbateriasporfecha');
    Route::get('/buscarprogramacionesporfecha', 'App\Http\Controllers\Admin\AdministrarProgramacionController@buscarprogramacionesporfecha')->name('buscarprogramacionesporfecha');
    Route::post('/eliminar-documentos', [AdministrarProgramacionController::class, 'eliminarDocumentos'])->name('eliminar-documentos');
    Route::post('/eliminar-informesfinal', [AdministrarProgramacionController::class, 'eliminarinformesfinal'])->name('eliminar-informesfinal');
    Route::post('/eliminar-bateria', [AdministrarProgramacionController::class, 'eliminarbateria'])->name('eliminar-bateria');
    Route::post('/anular-pendiente-requisitos', [AdministrarProgramacionController::class, 'anularpendienterequisitos'])->name('anular-pendiente-requisitos');
//

//INSTRUCTIVAS DE PODER
    Route::get('instructivaspoder/crearinstructivapoder/{cliente}', 'App\Http\Controllers\Admin\InstructivaPoderController@crearinstructivapoder')->name('admin.instructivaspoder.crearinstructivapoder');
    Route::get('instructivaspoder/generarpdfinstructivaspoder/{cliente}', 'App\Http\Controllers\Admin\InstructivaPoderController@generarpdfinstructivaspoder')->name('admin.instructivaspoder.generarpdfinstructivaspoder');

    Route::get('instructivaspoder/crearinstructivapoderauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\InstructivaPoderController@crearinstructivapoderauditoria')->name('admin.instructivaspoder.crearinstructivapoderauditoria');
    Route::get('instructivaspoder/generarpdfinstructivaspoderauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\InstructivaPoderController@generarpdfinstructivaspoderauditoria')->name('admin.instructivaspoder.generarpdfinstructivaspoderauditoria');

    Route::get('/buscarclientesitainstructiva', 'App\Http\Controllers\Admin\InstructivaPoderController@buscarclientesitainstructiva')->name('buscarclientesitainstructiva');

    Route::get('instructivaspoder/nuevainstructiva/1', [InstructivaPoderController::class, 'nuevainstructiva'])->name('admin.instructivaspoder.nuevainstructiva');
//

//CLIENTES ITA
        Route::post('/crear-empresa', [AsociadoController::class, 'crearempresa'])->name('admin.asociados.crearempresa');
        Route::get('admin/asociados/obtenerEmpresas', [AsociadoController::class, 'obtenerEmpresas'])->name('admin.asociados.obtenerEmpresas');
        Route::post('asociados/guardarcartaclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarcartaclienteita')->name('admin.asociados.guardarcartaclienteita');
        Route::post('duplicar-registros', [AsociadoController::class, 'duplicar'])->name('ruta.duplicar');

    //CREAR Y EDITAR CLIENTE ITA
        Route::get('asociados/crearclienteita/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@crearclienteita')->name('admin.asociados.crearclienteita');
        Route::post('asociados/guardarclienteita/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@guardarclienteita')->name('admin.asociados.guardarclienteita');
        Route::get('asociados/listadoclienteita/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@listadoclienteita')->name('admin.asociados.listadoclienteita');
        Route::get('asociados/documentacionmultipleclienteita/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@documentacionmultipleclienteita')->name('admin.asociados.documentacionmultipleclienteita');
        Route::get('/buscarclientesita', 'App\Http\Controllers\Admin\AsociadoController@buscarclientesita')->name('buscarclientesita');
        Route::get('asociados/verclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@verclienteita')->name('admin.asociados.verclienteita');
        Route::get('asociados/editarclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@editarclienteita')->name('admin.asociados.editarclienteita');
        Route::put('asociados/actualizarclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@actualizarclienteita')->name('admin.asociados.actualizarclienteita');
    //ASIGNAR TRAMITE CLIENTE ITA
        Route::get('asociados/listadotramiteclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@listadotramiteclienteita')->name('admin.asociados.listadotramiteclienteita');
        Route::get('asociados/asignartramiteclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@asignartramiteclienteita')->name('admin.asociados.asignartramiteclienteita');
        Route::post('asociados/guardartramiteclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardartramiteclienteita')->name('admin.asociados.guardartramiteclienteita');
    //CREAR BATERIA CLIENTE ITA
        Route::get('asociados/crearbateriaclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@crearbateriaclienteita')->name('admin.asociados.crearbateriaclienteita');
        Route::post('asociados/guardarbateriaclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarbateriaclienteita')->name('admin.asociados.guardarbateriaclienteita');
        Route::post('eliminar-registros', [AsociadoController::class, 'anular'])->name('ruta.anular');
        Route::post('eliminar-registrosbateriaauditoria', [AsociadoController::class, 'anularbateriaauditoria'])->name('ruta.anularbateriaauditoria');

    //APROBAR COTIZACION DE PROGRAMACION DE CLIENTE ITA
        Route::get('asociados/aprobacioncotizacionclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@aprobacioncotizacionclienteita')->name('admin.asociados.aprobacioncotizacionclienteita');
        Route::get('/buscarbateriaclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@buscarbateriaclienteita')->name('buscarbateriaclienteita');
        Route::get('asociados/generarpdfcotizacionclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generarpdfcotizacionclienteita')->name('admin.asociados.generarpdfcotizacionclienteita');
        Route::get('asociados/aprobarcotizacionprogramacionclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@aprobarcotizacionprogramacionclienteita')->name('admin.asociados.aprobarcotizacionprogramacionclienteita');
        Route::post('asociados/guardaraprobacioncotizacionclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardaraprobacioncotizacionclienteita')->name('admin.asociados.guardaraprobacioncotizacionclienteita');
        Route::post('/guardar-Facturacotclienteita', [AsociadoController::class, 'guardarFacturacotclienteita'])->name('guardarFacturacotclienteita');

        Route::post('/actualizar-pdf', [AsociadoController::class, 'actualizarPdf'])->name('admin.actualizarPdf');

    //CREAR PROGRAMACION Y REPROGRAMACION DE CLIENTE ITA
        Route::get('asociados/crearprogramacionclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@crearprogramacionclienteita')->name('admin.asociados.crearprogramacionclienteita');
        Route::post('asociados/guardarprogramacionclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarprogramacionclienteita')->name('admin.asociados.guardarprogramacionclienteita');
        Route::get('asociados/reprogramacionclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@reprogramacionclienteita')->name('admin.asociados.reprogramacionclienteita');
        Route::get('/buscarprogramacionclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@buscarprogramacionclienteita')->name('buscarprogramacionclienteita');
        Route::delete('asociados/guardarreprogramacionclienteita/{programacionsubcliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarreprogramacionclienteita')->name('admin.asociados.guardarreprogramacionclienteita');
        Route::get('asociados/estadoprogramacionclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@estadoprogramacionclienteita')->name('admin.asociados.estadoprogramacionclienteita');
        Route::get('/buscarprogramacionclientesita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@buscarprogramacionclientesita')->name('buscarprogramacionclientesita');
        Route::get('asociados/generarpdfprogramacionclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generarpdfprogramacionclienteita')->name('admin.asociados.generarpdfprogramacionclienteita');
        Route::post('asociados/guardarestadoprogramacionclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarestadoprogramacionclienteita')->name('admin.asociados.guardarestadoprogramacionclienteita');
        Route::post('asociados/actualizarproveedorfecha/{cliente}', [AsociadoController::class, 'actualizarProveedorFecha'])->name('admin.asociados.actualizarproveedorfecha');
    //CREAR DOCUMENTACION DE CLIENTE ITA
        Route::get('asociados/creardocumentacionclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@creardocumentacionclienteita')->name('admin.asociados.creardocumentacionclienteita');
        Route::post('asociados/guardardocumentacionclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardardocumentacionclienteita')->name('admin.asociados.guardardocumentacionclienteita');
        Route::post('asociados/guardardocumentacionclienteitadeproveedor/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardardocumentacionclienteitadeproveedor')->name('admin.asociados.guardardocumentacionclienteitadeproveedor');
        Route::post('asociados/guardarhistoriamedica/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarhistoriamedica')->name('admin.asociados.guardarhistoriamedica');
        Route::get('asociados/listadodocumentacionclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@listadodocumentacionclienteita')->name('admin.asociados.listadodocumentacionclienteita');
        Route::get('/buscardocumentoclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@buscardocumentoclienteita')->name('buscardocumentoclienteita');

        Route::get('/ver-documento/{id}', [AsociadoController::class, 'verDocumento'])->name('ver.documento');
    
    //CREAR FORMULARIO DE CLIENTE ITA
        Route::get('asociados/crearformularioclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@crearformularioclienteita')->name('admin.asociados.crearformularioclienteita');
        Route::post('asociados/guardarformularioclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarformularioclienteita')->name('admin.asociados.guardarformularioclienteita');
        Route::post('asociados/generarpdfclienteita{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generarpdfclienteita')->name('admin.asociados.generarpdfclienteita');
    //GENERAR PDF
        Route::get('asociados/generaretiquetaclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generaretiquetaclienteita')->name('admin.asociados.generaretiquetaclienteita');
        Route::get('asociados/generaretiquetaclienteitaauditoria/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generaretiquetaclienteitaauditoria')->name('admin.asociados.generaretiquetaclienteitaauditoria');
        Route::get('asociados/generaretiquetaclienteitaapelacion/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generaretiquetaclienteitaapelacion')->name('admin.asociados.generaretiquetaclienteitaapelacion');
        Route::get('asociados/generaretiquetaclienteitasegundasolicitud/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generaretiquetaclienteitasegundasolicitud')->name('admin.asociados.generaretiquetaclienteitasegundasolicitud');
        Route::get('asociados/generarchecklistclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generarchecklistclienteita')->name('admin.asociados.generarchecklistclienteita');
        Route::post('asociados/descargarchecklistclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@descargarchecklistclienteita')->name('admin.asociados.descargarchecklistclienteita');
        Route::get('asociados/subirdocrequisitos/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@subirdocrequisitos')->name('admin.asociados.subirdocrequisitos');
        Route::put('asociados/guardardocrequisitos/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardardocrequisitos')->name('admin.asociados.guardardocrequisitos');
        Route::post('asociados/descargarsolochecklistclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@descargarSOLOchecklistclienteITA')->name('admin.asociados.descargarsolochecklistclienteita');

        Route::get('asociados/generarchecklistclienteitaaudi/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generarchecklistclienteitaaudi')->name('admin.asociados.generarchecklistclienteitaaudi');
        Route::post('asociados/descargarchecklistclienteitaaudi/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@descargarchecklistclienteitaaudi')->name('admin.asociados.descargarchecklistclienteitaaudi');
        Route::get('asociados/subirdocrequisitosaudi/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@subirdocrequisitosaudi')->name('admin.asociados.subirdocrequisitosaudi');
        Route::put('asociados/guardardocrequisitosaudi/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardardocrequisitosaudi')->name('admin.asociados.guardardocrequisitosaudi');

        Route::get('asociados/generarchecklistclienteitaapelacion/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generarchecklistclienteitaapelacion')->name('admin.asociados.generarchecklistclienteitaapelacion');
        Route::post('asociados/descargarchecklistclienteitaapelacion/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@descargarchecklistclienteitaapelacion')->name('admin.asociados.descargarchecklistclienteitaapelacion');
        Route::get('asociados/subirdocrequisitosapelacion/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@subirdocrequisitosapelacion')->name('admin.asociados.subirdocrequisitosapelacion');
        Route::put('asociados/guardardocrequisitosapelacion/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardardocrequisitosapelacion')->name('admin.asociados.guardardocrequisitosapelacion');

        Route::get('asociados/generarchecklistclienteitasegsolicitud/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generarchecklistclienteitasegsolicitud')->name('admin.asociados.generarchecklistclienteitasegsolicitud');
        Route::post('asociados/descargarchecklistclienteitasegsolicitud/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@descargarchecklistclienteitasegsolicitud')->name('admin.asociados.descargarchecklistclienteitasegsolicitud');
        Route::get('asociados/subirdocrequisitossegsolicitud/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@subirdocrequisitossegsolicitud')->name('admin.asociados.subirdocrequisitossegsolicitud');
        Route::put('asociados/guardardocrequisitossegsolicitud/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardardocrequisitossegsolicitud')->name('admin.asociados.guardardocrequisitossegsolicitud');

        Route::get('asociados/generarchecklistclienteitatercerasolicitud/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generarchecklistclienteitatercerasolicitud')->name('admin.asociados.generarchecklistclienteitatercerasolicitud');
        Route::post('asociados/descargarchecklistclienteitatercerasolicitud/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@descargarchecklistclienteitatercerasolicitud')->name('admin.asociados.descargarchecklistclienteitatercerasolicitud');
        Route::get('asociados/subirdocrequisitostercerasolicitud/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@subirdocrequisitostercerasolicitud')->name('admin.asociados.subirdocrequisitostercerasolicitud');
        Route::put('asociados/guardardocrequisitostercerasolicitud/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardardocrequisitostercerasolicitud')->name('admin.asociados.guardardocrequisitostercerasolicitud');

        Route::post('/generar-pdf-dermedlab', [AsociadoController::class, 'generarPDFconsentimiento'])->name('generar.pdf.consentimiento');
        Route::post('/generar-pdf-dermedlab2', [AsociadoController::class, 'generarsoloPDFconsentimiento'])->name('generar.pdf.soloconsentimiento');
        Route::post('/generar-pdf-guardardocumentoconsentimiento', [AsociadoController::class, 'generarPDFguardarconsentimiento'])->name('guardar.pdf.consentimiento');
        Route::post('/generar-pdf-conocinfor', [AsociadoController::class, 'generarPDFconsentimientoinformado'])->name('generar.pdf.consentimientoinformado');
        Route::post('/aprobariniciarcrearbateria', [AsociadoController::class, 'aprobariniciarcrearbateria'])->name('aprobariniciarcrearbateria');
        Route::post('/aprobarinformefinaldirecto', [AsociadoController::class, 'aprobarinformefinaldirecto'])->name('aprobarinformefinaldirecto');

    //CONTACTOS CLIENTES ITA
        Route::get('asociados/vercontactoclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@vercontactoclienteita')->name('admin.asociados.vercontactoclienteita');
        Route::get('asociados/crearcontactoclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@crearcontactoclienteita')->name('admin.asociados.crearcontactoclienteita');
        Route::post('asociados/guardarcontactoclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarcontactoclienteita')->name('admin.asociados.guardarcontactoclienteita');

    //PROVEEDOR INFORME FINAL
        Route::post('asociados/guardarproveedorinformefinal/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarproveedorinformefinal')->name('admin.asociados.guardarproveedorinformefinal');
    
    //GENERAR PDF DEL CLIENTE ITA
        Route::get('admin/asociados/generarpdfcliente/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generarPDFCliente')->name('admin.asociados.generarpdfcliente');
        Route::post('admin/asociados/generarpdfcliente/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generarPDFCliente')->name('admin.asociados.generarpdfcliente');
        Route::post('admin/asociados/eliminar-pdf', 'App\Http\Controllers\Admin\AsociadoController@eliminarPDF')->name('admin.asociados.eliminarpdf');

        Route::get('asociados/anulaciones/anularcuentacobrar/', [AsociadoController::class, 'anularcuentacobrar'])->name('admin.asociados.anulaciones.anularcuentacobrar');
        Route::post('/anular-regitro-cuentacobrar', [AsociadoController::class, 'anularregitrocuentacobrar'])->name('anularregitrocuentacobrar');

    //DICTAMEN
        Route::post('asociados/guardardictamenita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardardictamenita')->name('admin.asociados.guardardictamenita');
        Route::post('asociados/guardarcartaclienteauditoriaita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarcartaclienteauditoriaita')->name('admin.asociados.guardarcartaclienteauditoriaita');
//

//CLIENTES COMUNES
    //CREAR Y EDITAR CLIENTE COMUN
        Route::get('asociados/crearclientecomun/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@crearclientecomun')->name('admin.asociados.crearclientecomun');
        Route::post('asociados/guardarclientecomun/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@guardarclientecomun')->name('admin.asociados.guardarclientecomun');
        Route::get('asociados/listadoclientecomun/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@listadoclientecomun')->name('admin.asociados.listadoclientecomun');
        Route::get('asociados/documentacionmultipleclientecomun/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@documentacionmultipleclientecomun')->name('admin.asociados.documentacionmultipleclientecomun');
        Route::get('/buscarclientescomun', 'App\Http\Controllers\Admin\AsociadoController@buscarclientescomun')->name('buscarclientescomun');
        Route::get('asociados/verclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@verclientecomun')->name('admin.asociados.verclientecomun');
        Route::get('asociados/editarclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@editarclientecomun')->name('admin.asociados.editarclientecomun');
        Route::put('asociados/actualizarclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@actualizarclientecomun')->name('admin.asociados.actualizarclientecomun');
    //CREAR BATERIA CLIENTE COMUN
        Route::get('asociados/crearbateriaclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@crearbateriaclientecomun')->name('admin.asociados.crearbateriaclientecomun');
        Route::post('asociados/guardarbateriaclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@guardarbateriaclientecomun')->name('admin.asociados.guardarbateriaclientecomun');
    //APROBAR COTIZACION DE PROGRAMACION DE CLIENTE COMUN
        Route::get('asociados/aprobacioncotizacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@aprobacioncotizacionclientecomun')->name('admin.asociados.aprobacioncotizacionclientecomun');
        Route::get('/buscarbateriaclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@buscarbateriaclientecomun')->name('buscarbateriaclientecomun');
        Route::post('asociados/guardaraprobacionprogramacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@guardaraprobacionprogramacionclientecomun')->name('admin.asociados.guardaraprobacionprogramacionclientecomun');
        Route::get('asociados/aprobarcotizacionprogramacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@aprobarcotizacionprogramacionclientecomun')->name('admin.asociados.aprobarcotizacionprogramacionclientecomun');
        Route::post('asociados/guardaraprobacioncotizacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@guardaraprobacioncotizacionclientecomun')->name('admin.asociados.guardaraprobacioncotizacionclientecomun');
        Route::get('asociados/generarpdfcotizacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@generarpdfcotizacionclientecomun')->name('admin.asociados.generarpdfcotizacionclientecomun');
    //CREAR PROGRAMACION Y REPROGRAMACION DE CLIENTE COMUN
        Route::get('asociados/crearprogramacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@crearprogramacionclientecomun')->name('admin.asociados.crearprogramacionclientecomun');
        Route::post('asociados/guardarprogramacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@guardarprogramacionclientecomun')->name('admin.asociados.guardarprogramacionclientecomun');
        Route::get('asociados/reprogramacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@reprogramacionclientecomun')->name('admin.asociados.reprogramacionclientecomun');
        Route::get('/buscarprogramacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@buscarprogramacionclientecomun')->name('buscarprogramacionclientecomun');
        Route::delete('asociados/guardarreprogramacionclientecomun/{programacionsubcliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarreprogramacionclientecomun')->name('admin.asociados.guardarreprogramacionclientecomun');
        Route::get('asociados/estadoprogramacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@estadoprogramacionclientecomun')->name('admin.asociados.estadoprogramacionclientecomun');
        Route::get('/buscarprogramacionclientescomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@buscarprogramacionclientescomun')->name('buscarprogramacionclientescomun');
        Route::post('asociados/guardarestadoprogramacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@guardarestadoprogramacionclientecomun')->name('admin.asociados.guardarestadoprogramacionclientecomun');
        Route::get('/buscarprogramacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@buscarprogramacionclientecomun')->name('buscarprogramacionclientecomun');
        Route::post('actualizarfecha/{id}', 'App\Http\Controllers\Admin\AsociadoController@actualizarFecha')->name('admin.asociados.actualizarFecha');
        Route::post('/actualizarfecha/{id}', [AsociadoController::class, 'actualizarFecha'])->name('actualizar.fecha');


    //CREAR DOCUMENTACION DE CLIENTE COMUN
        Route::get('asociados/creardocumentacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@creardocumentacionclientecomun')->name('admin.asociados.creardocumentacionclientecomun');
        Route::post('asociados/guardardocumentacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@guardardocumentacionclientecomun')->name('admin.asociados.guardardocumentacionclientecomun');
        Route::get('asociados/listadodocumentacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@listadodocumentacionclientecomun')->name('admin.asociados.listadodocumentacionclientecomun');
        Route::get('/buscardocumentoclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@buscardocumentoclientecomun')->name('buscardocumentoclientecomun');
//

//CLIENTES AUDITORIA
        Route::post('asociados/guardarcartaclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardarcartaclienteauditoria')->name('admin.asociados.guardarcartaclienteauditoria');
    //CREAR Y EDITAR CLIENTE AUDITORIA
        Route::get('asociados/crearclienteauditoria/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@crearclienteauditoria')->name('admin.asociados.crearclienteauditoria');
        Route::post('asociados/guardarclienteauditoria/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@guardarclienteauditoria')->name('admin.asociados.guardarclienteauditoria');
        Route::get('asociados/listadoclienteauditoria/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@listadoclienteauditoria')->name('admin.asociados.listadoclienteauditoria');
        Route::get('asociados/documentacionmultipleclienteauditoria/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@documentacionmultipleclienteauditoria')->name('admin.asociados.documentacionmultipleclienteauditoria');
        Route::get('/buscarclientesauditoria', 'App\Http\Controllers\Admin\AsociadoController@buscarclientesauditoria')->name('buscarclientesauditoria');
        Route::get('asociados/verclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@verclienteauditoria')->name('admin.asociados.verclienteauditoria');
        Route::get('asociados/editarclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@editarclienteauditoria')->name('admin.asociados.editarclienteauditoria');
        Route::put('asociados/actualizarclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@actualizarclienteauditoria')->name('admin.asociados.actualizarclienteauditoria');
    //ASIGNAR TRAMITE CLIENTE AUDITORIA
        Route::get('asociados/listadotramiteclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@listadotramiteclienteauditoria')->name('admin.asociados.listadotramiteclienteauditoria');
        Route::get('asociados/asignartramiteclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@asignartramiteclienteauditoria')->name('admin.asociados.asignartramiteclienteauditoria');
        Route::post('asociados/guardartramiteclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardartramiteclienteauditoria')->name('admin.asociados.guardartramiteclienteauditoria');
    //CREAR BATERIA CLIENTE AUDITORIA
        Route::get('asociados/crearbateriaclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@crearbateriaclienteauditoria')->name('admin.asociados.crearbateriaclienteauditoria');
        Route::post('asociados/guardarbateriaclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardarbateriaclienteauditoria')->name('admin.asociados.guardarbateriaclienteauditoria');
        Route::get('admin/asociados/generarpdfclienteauditoriabateria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@generarPDFClienteauditoriabateria')->name('admin.asociados.generarpdfclienteauditoriabateria');
        Route::post('admin/asociados/generarpdfclienteauditoriabateria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@generarPDFClienteauditoriabateria')->name('admin.asociados.generarpdfclienteauditoriabateria');

    //APROBAR COTIZACION DE PROGRAMACION DE CLIENTE AUDITORIA
        Route::get('asociados/aprobacioncotizacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@aprobacioncotizacionclienteauditoria')->name('admin.asociados.aprobacioncotizacionclienteauditoria');
        Route::get('/buscarbateriaclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@buscarbateriaclienteauditoria')->name('buscarbateriaclienteauditoria');
        Route::get('asociados/generarpdfcotizacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@generarpdfcotizacionclienteauditoria')->name('admin.asociados.generarpdfcotizacionclienteauditoria');
        Route::get('asociados/aprobarcotizacionprogramacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@aprobarcotizacionprogramacionclienteauditoria')->name('admin.asociados.aprobarcotizacionprogramacionclienteauditoria');
        Route::post('asociados/guardaraprobacioncotizacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardaraprobacioncotizacionclienteauditoria')->name('admin.asociados.guardaraprobacioncotizacionclienteauditoria');
        Route::post('/actualizar-pdfauditoria', [AsociadoController::class, 'actualizarPdfcotauditoria'])->name('admin.actualizarPdfcotauditoria');
    
    //CREAR PROGRAMACION Y REPROGRAMACION DE CLIENTE AUDITORIA
        Route::get('asociados/crearprogramacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@crearprogramacionclienteauditoria')->name('admin.asociados.crearprogramacionclienteauditoria');
        Route::post('asociados/guardarprogramacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardarprogramacionclienteauditoria')->name('admin.asociados.guardarprogramacionclienteauditoria');
        Route::get('asociados/reprogramacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@reprogramacionclienteauditoria')->name('admin.asociados.reprogramacionclienteauditoria');
        Route::get('/buscarprogramacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@buscarprogramacionclienteauditoria')->name('buscarprogramacionclienteauditoria');
        Route::delete('asociados/guardarreprogramacionclienteauditoria/{programacionsubcliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarreprogramacionclienteauditoria')->name('admin.asociados.guardarreprogramacionclienteauditoria');
        Route::get('asociados/estadoprogramacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@estadoprogramacionclienteauditoria')->name('admin.asociados.estadoprogramacionclienteauditoria');
        Route::get('/buscarprogramacionclientesauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@buscarprogramacionclientesauditoria')->name('buscarprogramacionclientesauditoria');
        Route::get('asociados/generarpdfprogramacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@generarpdfprogramacionclienteauditoria')->name('admin.asociados.generarpdfprogramacionclienteauditoria');
        Route::post('asociados/guardarestadoprogramacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardarestadoprogramacionclienteauditoria')->name('admin.asociados.guardarestadoprogramacionclienteauditoria');
    //CREAR DOCUMENTACION DE CLIENTE AUDITORIA
        Route::get('asociados/creardocumentacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@creardocumentacionclienteauditoria')->name('admin.asociados.creardocumentacionclienteauditoria');
        Route::post('asociados/guardardocumentacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardardocumentacionclienteauditoria')->name('admin.asociados.guardardocumentacionclienteauditoria');
        Route::get('asociados/listadodocumentacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@listadodocumentacionclienteauditoria')->name('admin.asociados.listadodocumentacionclienteauditoria');
        Route::get('/buscardocumentoclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@buscardocumentoclienteauditoria')->name('buscardocumentoclienteauditoria');
        Route::post('asociados/guardardocumentacionclienteauditoriadeproveedor/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardardocumentacionclienteauditoriadeproveedor')->name('admin.asociados.guardardocumentacionclienteauditoriadeproveedor');
        Route::get('asociados/documentacionmultipleclienteauditoria/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@documentacionmultipleclienteauditoria')->name('admin.asociados.documentacionmultipleclienteauditoria');

    //CREAR FORMULARIO DE CLIENTE AUDITORIA
        Route::get('asociados/crearformularioclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@crearformularioclienteauditoria')->name('admin.asociados.crearformularioclienteauditoria');
        Route::post('asociados/guardarformularioclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardarformularioclienteauditoria')->name('admin.asociados.guardarformularioclienteauditoria');
        Route::post('asociados/generarpdfclienteauditoria{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@generarpdfclienteauditoria')->name('admin.asociados.generarpdfclienteauditoria');
    //GENERAR ETIQUETA Y REQUISITOS
        Route::get('asociados/generaretiquetaclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@generaretiquetaclienteauditoria')->name('admin.asociados.generaretiquetaclienteauditoria');
        Route::get('asociados/generarchecklistclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@generarchecklistclienteauditoria')->name('admin.asociados.generarchecklistclienteauditoria');
        Route::post('asociados/descargarchecklistclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@descargarchecklistclienteauditoria')->name('admin.asociados.descargarchecklistclienteauditoria');
        Route::get('asociados/subirdocrequisitosauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@subirdocrequisitosauditoria')->name('admin.asociados.subirdocrequisitosauditoria');
        Route::put('asociados/guardardocrequisitosauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardardocrequisitosauditoria')->name('admin.asociados.guardardocrequisitosauditoria');
    //CONTACTOS CLIENTES ITA
        Route::get('asociados/vercontactoclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@vercontactoclienteauditoria')->name('admin.asociados.vercontactoclienteauditoria');
        Route::get('asociados/crearcontactoclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@crearcontactoclienteauditoria')->name('admin.asociados.crearcontactoclienteauditoria');
        Route::post('asociados/guardarcontactoclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardarcontactoclienteauditoria')->name('admin.asociados.guardarcontactoclienteauditoria');

    //HISTORIAL MEDICO
        Route::post('asociados/guardarhistoriamedicaauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardarhistoriamedicaauditoria')->name('admin.asociados.guardarhistoriamedicaauditoria');
        Route::get('/ver-documentoauditoria/{id}', [AsociadoController::class, 'verDocumentoauditoria'])->name('ver.documentoauditoria');
        Route::post('/generar-pdf-dermedlabauditoria', [AsociadoController::class, 'generarPDFconsentimientoauditoria'])->name('generar.pdf.consentimientoauditoria');
        Route::post('/generar-pdf-dermedlab2auditoria', [AsociadoController::class, 'generarsoloPDFconsentimientoauditoria'])->name('generar.pdf.soloconsentimientoauditoria');
        Route::post('/generar-pdf-guardardocumentoconsentimientoauditoria', [AsociadoController::class, 'generarPDFguardarconsentimientoauditoria'])->name('guardar.pdf.consentimientoauditoria');
        Route::post('/generar-pdf-conocinforauditoria', [AsociadoController::class, 'generarPDFconsentimientoinformadoauditoria'])->name('generar.pdf.consentimientoinformadoauditoria');
        Route::post('/aprobariniciarcrearbateriaauditoria', [AsociadoController::class, 'aprobariniciarcrearbateriaauditoria'])->name('aprobariniciarcrearbateriaauditoria');
        Route::post('/aprobarinformefinaldirectoauditoria', [AsociadoController::class, 'aprobarinformefinaldirectoauditoria'])->name('aprobarinformefinaldirectoauditoria');
    //PROVEEDOR INFORME FINAL
        Route::post('asociados/guardarproveedorinformefinalauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardarproveedorinformefinalauditoria')->name('admin.asociados.guardarproveedorinformefinalauditoria');
    
    //DICTAMEN
    Route::post('asociados/guardardictamenauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardardictamenauditoria')->name('admin.asociados.guardardictamenauditoria');

    Route::get('asociados/cartasdesgravamen/cartasactdesgravamen/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@cartasactdesgravamen')->name('admin.asociados.cartasdesgravamen.cartasactdesgravamen');

    //////////////////////////////////////
Route::post('/admin/asociados/cartasdesgravamen/descargarcartaactdesgravamen', [AsociadoController::class, 'descargarcartaactdesgravamen'])->name('admin.asociados.cartasdesgravamen.descargarcartaactdesgravamen');
Route::post('/admin/asociados/cartasdesgravamen/descargarcartaactcoberturadesgravamen', [AsociadoController::class, 'descargarcartaactcoberturadesgravamen'])->name('admin.asociados.cartasdesgravamen.descargarcartaactcoberturadesgravamen');
////////////////////////////////

//

//CLIENTES BANCOS
    //CREAR Y EDITAR CLIENTE BANCO
        Route::get('asociados/crearclientebanco/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@crearclientebanco')->name('admin.asociados.crearclientebanco');
        Route::post('asociados/guardarclientebanco/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@guardarclientebanco')->name('admin.asociados.guardarclientebanco');
        Route::get('asociados/listadoclientebanco/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@listadoclientebanco')->name('admin.asociados.listadoclientebanco');
        Route::get('/buscarclientesbanco', 'App\Http\Controllers\Admin\AsociadoController@buscarclientesbanco')->name('buscarclientesbanco');
        Route::get('asociados/verclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@verclientebanco')->name('admin.asociados.verclientebanco');
        Route::get('asociados/editarclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@editarclientebanco')->name('admin.asociados.editarclientebanco');
        Route::put('asociados/actualizarclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@actualizarclientebanco')->name('admin.asociados.actualizarclientebanco');
    //CREAR BATERIA CLIENTE BANCO
        Route::get('asociados/crearbateriaclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@crearbateriaclientebanco')->name('admin.asociados.crearbateriaclientebanco');
        Route::post('asociados/guardarbateriaclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@guardarbateriaclientebanco')->name('admin.asociados.guardarbateriaclientebanco');
    //APROBAR COTIZACION DE PROGRAMACION DE CLIENTE BANCO
        Route::get('asociados/aprobacioncotizacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@aprobacioncotizacionclientebanco')->name('admin.asociados.aprobacioncotizacionclientebanco');
        Route::get('/buscarbateriaclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@buscarbateriaclientebanco')->name('buscarbateriaclientebanco');
        Route::get('asociados/aprobarcotizacionprogramacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@aprobarcotizacionprogramacionclientebanco')->name('admin.asociados.aprobarcotizacionprogramacionclientebanco');
        Route::get('asociados/generarpdfcotizacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@generarpdfcotizacionclientebanco')->name('admin.asociados.generarpdfcotizacionclientebanco');
        Route::post('asociados/guardaraprobacioncotizacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@guardaraprobacioncotizacionclientebanco')->name('admin.asociados.guardaraprobacioncotizacionclientebanco');
    //CREAR PROGRAMACION Y REPROGRAMACION DE CLIENTE BANCO
        Route::get('asociados/crearprogramacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@crearprogramacionclientebanco')->name('admin.asociados.crearprogramacionclientebanco');
        Route::post('asociados/guardarprogramacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@guardarprogramacionclientebanco')->name('admin.asociados.guardarprogramacionclientebanco');
        Route::get('asociados/reprogramacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@reprogramacionclientebanco')->name('admin.asociados.reprogramacionclientebanco');
        Route::get('/buscarprogramacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@buscarprogramacionclientebanco')->name('buscarprogramacionclientebanco');
        Route::delete('asociados/guardarreprogramacionclientebanco/{programacionsubcliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarreprogramacionclientebanco')->name('admin.asociados.guardarreprogramacionclientebanco');
        Route::get('asociados/estadoprogramacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@estadoprogramacionclientebanco')->name('admin.asociados.estadoprogramacionclientebanco');
        Route::get('/buscarprogramacionclientesbanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@buscarprogramacionclientesbanco')->name('buscarprogramacionclientesbanco');
        Route::get('asociados/generarpdfprogramacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@generarpdfprogramacionclientebanco')->name('admin.asociados.generarpdfprogramacionclientebanco');
        Route::post('asociados/guardarestadoprogramacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@guardarestadoprogramacionclientebanco')->name('admin.asociados.guardarestadoprogramacionclientebanco');
        
    //CREAR DOCUMENTACION DE CLIENTE BANCO
        Route::get('asociados/creardocumentacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@creardocumentacionclientebanco')->name('admin.asociados.creardocumentacionclientebanco');
        Route::post('asociados/guardardocumentacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@guardardocumentacionclientebanco')->name('admin.asociados.guardardocumentacionclientebanco');
        Route::get('asociados/listadodocumentacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@listadodocumentacionclientebanco')->name('admin.asociados.listadodocumentacionclientebanco');
        Route::get('/buscardocumentoclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@buscardocumentoclientebanco')->name('buscardocumentoclientebanco');
        Route::get('asociados/documentacionmultipleclientebanco/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@documentacionmultipleclientebanco')->name('admin.asociados.documentacionmultipleclientebanco');
    //CREAR FORMULARIO DE CLIENTE BANCO
        Route::get('asociados/crearformularioclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@crearformularioclientebanco')->name('admin.asociados.crearformularioclientebanco');
        Route::post('asociados/guardarformularioclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@guardarformularioclientebanco')->name('admin.asociados.guardarformularioclientebanco');
        Route::post('asociados/generarpdfclientebanco{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@generarpdfclientebanco')->name('admin.asociados.generarpdfclientebanco');
    //GENERAR PDF
        Route::get('asociados/generaretiquetaclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@generaretiquetaclientebanco')->name('admin.asociados.generaretiquetaclientebanco');
        Route::get('asociados/generarchecklistclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@generarchecklistclientebanco')->name('admin.asociados.generarchecklistclientebanco');
        Route::post('asociados/descargarchecklistclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@descargarchecklistclientebanco')->name('admin.asociados.descargarchecklistclientebanco');
    //CONTACTOS CLIENTES BANCO
        Route::get('asociados/vercontactoclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@vercontactoclientebanco')->name('admin.asociados.vercontactoclientebanco');
        Route::get('asociados/crearcontactoclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@crearcontactoclientebanco')->name('admin.asociados.crearcontactoclientebanco');
        Route::post('asociados/guardarcontactoclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@guardarcontactoclientebanco')->name('admin.asociados.guardarcontactoclientebanco');

        Route::get('asociados/formularios/declaracionesmedico/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@declaracionesmedico')->name('admin.asociados.formularios.declaracionesmedico');
        Route::post('asociados/formularios/guardardeclaracion/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@guardardeclaracion')->name('admin.asociados.formularios.guardardeclaracion');
        Route::get('asociados/formularios/generarQR/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@generarQR')->name('admin.asociados.formularios.generarQR');

        Route::post('/generar-pdf-dermedlabbanco', [AsociadoController::class, 'generarPDFconsentimientobanco'])->name('generar.pdf.consentimientobanco');
        Route::post('/generar-pdf-dermedlab2banco', [AsociadoController::class, 'generarsoloPDFconsentimientobanco'])->name('generar.pdf.soloconsentimientobanco');
        Route::post('/generar-pdf-guardardocumentoconsentimientobanco', [AsociadoController::class, 'generarPDFguardarconsentimientobanco'])->name('guardar.pdf.consentimientobanco');
        Route::post('/generar-pdf-conocinforbanco', [AsociadoController::class, 'generarPDFconsentimientoinformadobanco'])->name('generar.pdf.consentimientoinformadobanco');
        Route::post('/aprobariniciarcrearbateribanco', [AsociadoController::class, 'aprobariniciarcrearbateriabanco'])->name('aprobariniciarcrearbateriabanco');

        Route::post('informesfinales/guardarinformefinalclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\InformeFinalController@guardarinformefinalclientebanco')->name('admin.informesfinales.guardarinformefinalclientebanco');
        Route::post('informesfinales/guardarconsiliacionclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\InformeFinalController@guardarconsiliacionclientebanco')->name('admin.informesfinales.guardarconsiliacionclientebanco');

        Route::post('asociados/formularios/guardarSOLOdeclaracion/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@guardarSOLOdeclaracion')->name('admin.asociados.formularios.guardarSOLOdeclaracion');

        Route::get('informesfinales/generarordenventaclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\InformeFinalController@generarordenventaclientebanco')->name('admin.informesfinales.ordenes.generarordenventaclientebanco');


        Route::post('asociados/guardar-firma', [AsociadoController::class, 'guardarFirma']);

//

//CREAR Y EDITAR BATERIA DE BANCOS
    Route::get('asociados/verbateriasbanco/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@verbateriasbanco')->name('admin.asociados.verbateriasbanco');
    Route::get('asociados/crearbateriabanco/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@crearbateriabanco')->name('admin.asociados.crearbateriabanco');
    Route::post('asociados/guardarbateriabanco/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@guardarbateriabanco')->name('admin.asociados.guardarbateriabanco');
    Route::get('asociados/editarbateriabanco/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@editarbateriabanco')->name('admin.asociados.editarbateriabanco');
    Route::put('asociados/actualizarbateriabanco/{areaaccion}', 'App\Http\Controllers\Admin\AsociadoController@actualizarbateriabanco')->name('admin.asociados.actualizarbateriabanco');
    Route::get('/buscarbateriabanco', 'App\Http\Controllers\Admin\AsociadoController@buscarbateriabanco')->name('buscarbateriabanco');
//

//PROVEEDORES
    Route::get('proveedores/crearbateriaproveedor/{proveedor}', 'App\Http\Controllers\Admin\ProveedorController@crearbateriaproveedor')->name('admin.proveedores.crearbateriaproveedor');
    Route::post('proveedores/guardarbateriaproveedor/{proveedor}', 'App\Http\Controllers\Admin\ProveedorController@guardarbateriaproveedor')->name('admin.proveedores.guardarbateriaproveedor');
    Route::get('/buscarproveedor', 'App\Http\Controllers\Admin\ProveedorController@buscarproveedor')->name('buscarproveedor');
    Route::get('proveedores/edit/{proveedor}', 'App\Http\Controllers\Admin\ProveedorController@edit')->name('admin.proveedores.edit');
    Route::put('proveedores/update/{proveedor}', 'App\Http\Controllers\Admin\ProveedorController@update')->name('admin.proveedores.update');
    Route::get('proveedores/show/{proveedor}', 'App\Http\Controllers\Admin\ProveedorController@show')->name('admin.proveedores.show');
    Route::get('proveedores/verbateriaproveedor/{proveedor}', 'App\Http\Controllers\Admin\ProveedorController@verbateriaproveedor')->name('admin.proveedores.verbateriaproveedor');
    Route::delete('proveedores/eliminaraccionproveedor/{bateriaproveedor}', 'App\Http\Controllers\Admin\ProveedorController@eliminaraccionproveedor')->name('admin.proveedores.eliminaraccionproveedor');
    Route::post('/actualizar-estado-acciones', [ProveedorController::class, 'actualizarEstadoAcciones'])->name('actualizar_estado_acciones');

//

//CREAR, EDITAR AREAS Y ACCIONES
    Route::get('areaacciones/crearareabateria', 'App\Http\Controllers\Admin\AreaaccionController@crearareabateria')->name('admin.areaacciones.crearareabateria');
    Route::get('areaacciones/edit/{areaaccion}', 'App\Http\Controllers\Admin\AreaaccionController@edit')->name('admin.areaacciones.edit');
    Route::put('areaacciones/update/{areaaccion}', 'App\Http\Controllers\Admin\AreaaccionController@update')->name('admin.areaacciones.update');
    Route::put('proveedores/actualizarareaaccion/{areaaccion}', 'App\Http\Controllers\Admin\AreaaccionController@actualizarareaaccion')->name('admin.areaacciones.actualizarareaaccion');
    Route::post('areaacciones/guardarareabateria/{area}', 'App\Http\Controllers\Admin\AreaaccionController@guardarareabateria')->name('admin.areaacciones.guardarareabateria');

    Route::get('/buscarareaacciones', 'App\Http\Controllers\Admin\AccionesController@buscarareaacciones')->name('buscarareaacciones');

    Route::get('areaacciones/listadoareas/7', 'App\Http\Controllers\Admin\AreaaccionController@listadoareas')->name('admin.areaacciones.listadoareas');
    Route::get('areaacciones/crearaccionarea/7', 'App\Http\Controllers\Admin\AreaaccionController@crearaccionarea')->name('admin.areaacciones.crearaccionarea');
    Route::post('areaacciones/guardaraccionarea/{areaaccion}', 'App\Http\Controllers\Admin\AreaaccionController@guardaraccionarea')->name('admin.areaacciones.guardaraccionarea');
//

//REPORTES
    Route::post('reportes/generarreportes', 'App\Http\Controllers\Admin\ReporteController@generarreportes')->name('admin.reportes.generarreportes');
    Route::get('/generar-pdf', 'App\Http\Controllers\Admin\ReporteController@generarPDF')->name('generar.pdf');
    Route::get('/generar-excel', 'App\Http\Controllers\Admin\ReporteController@generarExcel')->name('generar.excel');
//

//GENERAR CÓDIGOS
    Route::get('codigo/asignacion', [CodigoController::class, 'index'])->name('admin.codigo.index');
    Route::post('codigo', [CodigoController::class, 'store'])->name('admin.codigo.store');
//

//SOPORTE TÉCNICO
Route::get('soporte/solicitud', [SoporteController::class, 'index'])->name('admin.soporte.index');
Route::post('soporte', [SoporteController::class, 'store'])->name('admin.soporte.store');
/* Route::get('soporte/historial', [SoporteController::class, 'historial'])->name('admin.soporte.historial'); */
Route::get('soporte/revision', [SoporteController::class, 'review'])->name('admin.soporte.review');
Route::post('soporte/atender/{id}', [SoporteController::class, 'atender'])->name('admin.soporte.atender');
//

//CONTABILIDAD
    //CENTRAL DE CAJA
    /* Route::get('caja/nuevaFactura', [CajaCentralController::class, 'nuevaFactura'])->name('admin.caja.nuevaFactura');
    Route::get('caja/historialFacturas', [CajaCentralController::class, 'historialFacturas'])->name('admin.caja.historialFacturas'); */
    //

    //CONTROL INGRESO
    /* Route::get('caja/ingreso', [MovimientosCajaController::class, 'listarIngresos'])->name('admin.caja.ingreso.index');
    Route::get('caja/cierreIngreso', [MovimientosCajaController::class, 'cierreCaja_Ingresos'])->name('admin.caja.ingreso.cierre'); */
    //

    //CONTROL EGRESO
    /* Route::get('caja/egreso', [MovimientosCajaController::class, 'listarEgresos'])->name('admin.caja.egreso.index');
    Route::get('caja/cierreEgreso', [MovimientosCajaController::class, 'cierreCaja_Egresos'])->name('admin.caja.egreso.cierre'); */
    //

    //CUENTAS POR COBRAR
    /* Route::get('caja/cuentasCobrar', [CuentasController::class, 'cuentasCobrar'])->name('admin.caja.cobrar');
    Route::get('caja/aprobacionesCobrar', [CuentasController::class, 'aprobacionesCobrar'])->name('admin.caja.aprobacionesCobrar'); */
    //

    //CUENTAS POR PAGAR
    /* Route::get('caja/cuentasPagar', [CuentasController::class, 'cuentasPagar'])->name('admin.caja.pagar');
    Route::get('caja/aprobacionesPagar', [CuentasController::class, 'aprobacionesPagar'])->name('admin.caja.aprobacionesPagar'); */
    //

//
Route::get('/print', [ClienteController::class, 'print'])->name('admin.clientes.print');
Route::get('clientes/print/{cliente}', 'App\Http\Controllers\Admin\ClienteController@print')->name('admin.clientes.print');
Route::post('clientes/print2/{cliente}', 'App\Http\Controllers\Admin\ClienteController@print2')->name('admin.clientes.print2');
Route::get('clientes/show2/{cliente}', 'App\Http\Controllers\Admin\ClienteController@show2')->name('admin.clientes.show2');
Route::get('proveedores/edit2/{proveedor}', 'App\Http\Controllers\Admin\ProveedorController@edit2')->name('admin.proveedores.edit2');
Route::get('proveedores/update2/{proveedor}', 'App\Http\Controllers\Admin\ProveedorController@update2')->name('admin.proveedores.update2');

Route::get('clientes/formulario/{cliente}', 'App\Http\Controllers\Admin\ClienteController@formulario')->name('admin.clientes.formulario');
Route::get('clientesauditorias/formulario/{clienteauditoria}', 'App\Http\Controllers\Admin\ClienteAuditoriaController@formulario')->name('admin.clientesauditorias.formulario');
Route::get('clientes/generarQR/{cliente}', 'App\Http\Controllers\Admin\ClienteController@generarQR')->name('admin.clientes.generarQR');
Route::get('proveedores/create2/{proveedor}', 'App\Http\Controllers\Admin\ProveedorController@create2')->name('admin.proveedores.create2');
Route::post('proveedores/store2/{proveedor}', 'App\Http\Controllers\Admin\ProveedorController@store2')->name('admin.proveedores.store2');
Route::get('clientesbancos/create2/{clientebanco}', 'App\Http\Controllers\Admin\ClienteBancoController@create2')->name('admin.clientesbancos.create2');
Route::post('clientesbancos/store2/{clientebanco}', 'App\Http\Controllers\Admin\ClienteBancoController@store2')->name('admin.clientesbancos.store2');
Route::get('clientesbancos/create3/{clientebanco}', 'App\Http\Controllers\Admin\ClienteBancoController@create3')->name('admin.clientesbancos.create3');
Route::post('clientesbancos/store3/{clientebanco}', 'App\Http\Controllers\Admin\ClienteBancoController@store3')->name('admin.clientesbancos.store3');
Route::get('clientesbancos/create4/{clientebanco}', 'App\Http\Controllers\Admin\ClienteBancoController@create4')->name('admin.clientesbancos.create4');
Route::get('clientesbancos/documentacioncliente/{clientebanco}', 'App\Http\Controllers\Admin\ClienteBancoController@documentacioncliente')->name('admin.clientesbancos.documentacioncliente');
Route::post('clientesbancos/subirdocumentacioncliente/{clientebanco}', 'App\Http\Controllers\Admin\ClienteBancoController@subirdocumentacioncliente')->name('admin.clientesbancos.subirdocumentacioncliente');


Route::get('clientes/create2/{cliente}', 'App\Http\Controllers\Admin\ClienteController@create2')->name('admin.clientes.create2');
Route::post('clientes/diagnostico/{cliente}', 'App\Http\Controllers\Admin\ClienteController@diagnostico')->name('admin.clientes.diagnostico');

Route::get('/mostrar-todos', 'App\Http\Controllers\Admin\ClienteBancoController@mostrarTodos')->name('ruta.para.mostrar.todos');
Route::get('/mostrar-todos2', 'App\Http\Controllers\Admin\ClienteAuditoriaController@mostrarTodos2')->name('ruta.para.mostrar.todos2');
Route::get('/buscar-clientes', 'App\Http\Controllers\Admin\ClienteBancoController@buscar')->name('buscar.clientes');
Route::get('/buscar-clientes', 'App\Http\Controllers\Admin\ClienteBancoController@buscar')->name('buscar.clientes');

Route::get('/buscar-clientes', 'App\Http\Controllers\Admin\AsociadoController@buscar')->name('buscar.clientes');
Route::post('/get-area-acciones/{area}', 'App\Http\Controllers\Admin\ProveedorController@getAreaAcciones')->name('get.area.acciones');
Route::get('/get-acciones/{areaId}', 'App\Http\Controllers\Admin\ProveedorController@getAcciones');

Route::post('/generar-qr', [FormularioController::class, 'generarQR'])->name('generar.qr');

Route::post('asociados/asignar-fechaita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@asignarFecha_ITA')->name('admin.asociados.asignarFecha_ITA');

});