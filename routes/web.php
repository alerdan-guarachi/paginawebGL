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
use App\Http\Controllers\Admin\PersonalController;
use App\Http\Controllers\Admin\InformeFinalController;
use App\Http\Controllers\Admin\TramitesController;
use App\Http\Controllers\Admin\ServiciosrequisitosController;
use App\Http\Controllers\TemporalController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PaginawebController;
use App\Http\Controllers\Admin\AdministrarProgramacionController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\ControlProgController;

Route::get('/', function () {
    return redirect('/welcome');
});

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
Route::resource('instructivaspoder', InstructivaPoderController::class)/* ->middleware('can:admin.mensajes.index') */->names('admin.instructivaspoder');
Route::resource('personal', PersonalController::class)/* ->middleware('can:admin.mensajes.index') */->names('admin.personal');
Route::resource('informesfinales', InformeFinalController::class)/* ->middleware('can:admin.mensajes.index') */->names('admin.informesfinales');
Route::resource('tramites', TramitesController::class)/* ->middleware('can:admin.mensajes.index') */->names('admin.tramites');
Route::resource('serviciosrequisitos', ServiciosrequisitosController::class)/* ->middleware('can:admin.mensajes.index') */->names('admin.serviciosrequisitos');

//
Route::resource('controlprogramacion', ControlProgController::class)/* ->middleware('can:admin.mensajes.index') */->names('admin.controlprogramacion');
Route::get('/buscar-usuario', [ControlProgController::class, 'buscarPorUsuario'])
    ->name('admin.controlprogramacion.buscarPorUsuario');
//GENERAR PDF DEL CLIENTE ITA
Route::get('admin/asociados/generarpdfcliente/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generarPDFCliente')->name('admin.asociados.generarpdfcliente');
Route::post('admin/asociados/generarpdfcliente/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@generarPDFCliente')->name('admin.asociados.generarpdfcliente');
Route::post('admin/asociados/eliminar-pdf', 'App\Http\Controllers\Admin\AsociadoController@eliminarPDF')->name('admin.asociados.eliminarpdf');



Route::get('asociados/{id}/download-pdf', [AsociadoController::class, 'downloadPDF'])->name('admin.asociados.downloadPDF');

Route::get('informesfinales/documentosprogramaciones/7', 'App\Http\Controllers\Admin\InformeFinalController@documentosprogramaciones')->name('admin.informesfinales.documentosprogramaciones');
Route::get('/buscarprogramacionesclienteita', 'App\Http\Controllers\Admin\InformeFinalController@buscarporproveedor')->name('buscarporproveedor');
Route::get('/buscarporproveedor', 'App\Http\Controllers\Admin\InformeFinalController@buscarprogramacionesclienteita')->name('buscarprogramacionesclienteita');
Route::get('/buscarprogramacionescomclienteita', 'App\Http\Controllers\Admin\InformeFinalController@buscarprogramacionescomclienteita')->name('buscarprogramacionescomclienteita');
Route::get('/buscarreservamedicaclienteita', 'App\Http\Controllers\Admin\InformeFinalController@buscarreservamedicaclienteita')->name('buscarreservamedicaclienteita');
Route::post('informesfinales/guardaraprobacioninformefinal/{item}', 'App\Http\Controllers\Admin\InformeFinalController@guardaraprobacioninformefinal')->name('admin.informesfinales.guardaraprobacioninformefinal');
Route::post('informesfinales/guardarproveedorinformefinal/{item}', 'App\Http\Controllers\Admin\InformeFinalController@guardarproveedorinformefinal')->name('admin.informesfinales.guardarproveedorinformefinal');
Route::post('informesfinales/guardarinformefinal/{item}', 'App\Http\Controllers\Admin\InformeFinalController@guardarinformefinal')->name('admin.informesfinales.guardarinformefinal');
Route::delete('informesfinales/solrevisioninformefinal/{item}', 'App\Http\Controllers\Admin\InformeFinalController@solrevisioninformefinal')->name('admin.informesfinales.solrevisioninformefinal');
Route::put('informesfinales/aprobarinformefinalfs/{item}', 'App\Http\Controllers\Admin\InformeFinalController@aprobarinformefinalfs')->name('admin.informesfinales.aprobarinformefinalfs');

Route::post('/update-observacion', [InformeFinalController::class, 'updateObservacion'])->name('updateObservacion');
// En routes/web.php
Route::post('/update-document/{id}', [InformeFinalController::class, 'updateDocument'])->name('updateDocument');


Route::get('tramites/procmasahereditaria/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procmasahereditaria')->name('admin.tramites.procmasahereditaria');
Route::get('tramites/procjubilacion/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procjubilacion')->name('admin.tramites.procjubilacion');
Route::get('tramites/procretiroaportestotal/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procretiroaportestotal')->name('admin.tramites.procretiroaportestotal');
Route::get('tramites/procretiroaportesparcial/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procretiroaportesparcial')->name('admin.tramites.procretiroaportesparcial');
Route::get('tramites/procpensionpormuerte/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procpensionpormuerte')->name('admin.tramites.procpensionpormuerte');
Route::get('tramites/procsegundasolicitud/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procsegundasolicitud')->name('admin.tramites.procsegundasolicitud');
Route::get('tramites/procinvalidez/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procinvalidez')->name('admin.tramites.procinvalidez');
Route::get('tramites/procapelacion/{cliente}', 'App\Http\Controllers\Admin\TramitesController@procapelacion')->name('admin.tramites.procapelacion');
Route::get('tramites/proccompensacionsenasir/{cliente}', 'App\Http\Controllers\Admin\TramitesController@proccompensacionsenasir')->name('admin.tramites.proccompensacionsenasir');


Route::get('informesfinales/estadodocumentacionprogramacion/7', 'App\Http\Controllers\Admin\InformeFinalController@estadodocumentacionprogramacion')->name('admin.informesfinales.estadodocumentacionprogramacion');
Route::get('admprogramaciones/controlregistros/7', 'App\Http\Controllers\Admin\AdministrarProgramacionController@controlregistros')->name('admin.admprogramaciones.controlregistros');
Route::get('informesfinales/reservasmedicas/8', 'App\Http\Controllers\Admin\InformeFinalController@reservasmedicas')->name('admin.informesfinales.reservasmedicas');

Route::post('tramites/guardartramitesclienteita/{cliente}', 'App\Http\Controllers\Admin\TramitesController@guardartramitesclienteita')->name('admin.tramites.guardartramitesclienteita');

Route::get('tramites/generarcartareclamo/{cliente}', 'App\Http\Controllers\Admin\TramitesController@generarcartareclamo')->name('admin.tramites.generarcartareclamo');
Route::get('tramites/generaradjuntoyrespuesta/{cliente}', 'App\Http\Controllers\Admin\TramitesController@generaradjuntoyrespuesta')->name('admin.tramites.generaradjuntoyrespuesta');
Route::get('tramites/generarsolicitud/{cliente}', 'App\Http\Controllers\Admin\TramitesController@generarsolicitud')->name('admin.tramites.generarsolicitud');
/* Route::get('tramites/sitsegundacarta/{cliente}', 'App\Http\Controllers\Admin\TramitesController@sitsegundacarta')->name('admin.tramites.sitsegundacarta');
Route::get('tramites/sitterceracarta/{cliente}', 'App\Http\Controllers\Admin\TramitesController@sitterceracarta')->name('admin.tramites.sitterceracarta');
Route::get('tramites/reclamoprimeracarta/{cliente}', 'App\Http\Controllers\Admin\TramitesController@sitprimeracarta')->name('admin.tramites.reclamoprimeracarta');
Route::get('tramites/reclamosegundacarta/{cliente}', 'App\Http\Controllers\Admin\TramitesController@sitprimeracarta')->name('admin.tramites.reclamosegundacarta');
Route::get('tramites/reclamoterceracarta/{cliente}', 'App\Http\Controllers\Admin\TramitesController@sitprimeracarta')->name('admin.tramites.reclamoterceracarta');
Route::get('tramites/reclamoaps/{cliente}', 'App\Http\Controllers\Admin\TramitesController@reclamoaps')->name('admin.tramites.reclamoaps'); */

//CREAR Y EDITAR BATERIA DE GOOD LIFE
    Route::get('asociados/verbateriaasociado/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@verbateriaasociado')->name('admin.asociados.verbateriaasociado');
    Route::get('asociados/editarbateriaasociado/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@editarbateriaasociado')->name('admin.asociados.editarbateriaasociado');
    Route::put('asociados/actualizarbateriaasociado/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@actualizarbateriaasociado')->name('admin.asociados.actualizarbateriaasociado');
    Route::get('asociados/crearbateriaasociado/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@crearbateriaasociado')->name('admin.asociados.crearbateriaasociado');
    Route::post('asociados/guardarbateriaasociado/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@guardarbateriaasociado')->name('admin.asociados.guardarbateriaasociado');
//

//CREAR Y EDITAR BATERIA DE BANCOS
    Route::get('asociados/verbateriasbanco/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@verbateriasbanco')->name('admin.asociados.verbateriasbanco');
    Route::get('asociados/crearbateriabanco/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@crearbateriabanco')->name('admin.asociados.crearbateriabanco');
    Route::post('asociados/guardarbateriabanco/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@guardarbateriabanco')->name('admin.asociados.guardarbateriabanco');
    Route::get('asociados/editarbateriabanco/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@editarbateriabanco')->name('admin.asociados.editarbateriabanco');
    Route::put('asociados/actualizarbateriabanco/{areaaccion}', 'App\Http\Controllers\Admin\AsociadoController@actualizarbateriabanco')->name('admin.asociados.actualizarbateriabanco');
    Route::get('/buscarbateriabanco', 'App\Http\Controllers\Admin\AsociadoController@buscarbateriabanco')->name('buscarbateriabanco');
//

//VER Y ADMINISTRAR REGISTROS 
    Route::get('admprogramaciones/documentacionpendiente/7', 'App\Http\Controllers\Admin\AdministrarProgramacionController@documentacionpendiente')->name('admin.admprogramaciones.documentacionpendiente');
    Route::get('admprogramaciones/documentacionactiva/7', 'App\Http\Controllers\Admin\AdministrarProgramacionController@documentacionactiva')->name('admin.admprogramaciones.documentacionactiva');
    Route::get('admprogramaciones/clientescreadoshoy/7', 'App\Http\Controllers\Admin\AdministrarProgramacionController@clientescreadoshoy')->name('admin.admprogramaciones.clientescreadoshoy');
    Route::get('admprogramaciones/programacionescreadoshoy/7', 'App\Http\Controllers\Admin\AdministrarProgramacionController@programacionescreadoshoy')->name('admin.admprogramaciones.programacionescreadoshoy');
    Route::get('admprogramaciones/bateriascreadoshoy/7', 'App\Http\Controllers\Admin\AdministrarProgramacionController@bateriascreadoshoy')->name('admin.admprogramaciones.bateriascreadoshoy');
    Route::get('/buscarclientesporfecha', 'App\Http\Controllers\Admin\AdministrarProgramacionController@buscarclientesporfecha')->name('buscarclientesporfecha');
    Route::get('/buscarbateriasporfecha', 'App\Http\Controllers\Admin\AdministrarProgramacionController@buscarbateriasporfecha')->name('buscarbateriasporfecha');
    Route::get('/buscarprogramacionesporfecha', 'App\Http\Controllers\Admin\AdministrarProgramacionController@buscarprogramacionesporfecha')->name('buscarprogramacionesporfecha');
//
Route::get('/tramites/actualizarEstado/{id}/{clienteId}', [TramitesController::class, 'actualizarEstado'])->name('tramites.actualizarEstado');
// web.php
// web.php
Route::post('/tramites/{id}/subir-archivo/{clienteId}', [TramitesController::class, 'subirArchivo'])->name('tramites.subirArchivo');
Route::post('tramites/asignarapoderadotramiteclienteita/{cliente}', 'App\Http\Controllers\Admin\TramitesController@asignarapoderadotramiteclienteita')->name('admin.tramites.asignarapoderadotramiteclienteita');


Route::post('tramites/guardartramitesclienteitaseguimiento/{cliente}', 'App\Http\Controllers\Admin\TramitesController@guardartramitesclienteitaseguimiento')->name('admin.tramites.guardartramitesclienteitaseguimiento');

Route::get('instructivaspoder/crearinspoderinvalidez/{cliente}', 'App\Http\Controllers\Admin\InstructivaPoderController@crearinspoderinvalidez')->name('admin.instructivaspoder.crearinspoderinvalidez');
Route::get('instructivaspoder/generarpdfinspoderinvalidez/{cliente}', 'App\Http\Controllers\Admin\InstructivaPoderController@generarpdfinspoderinvalidez')->name('admin.instructivaspoder.generarpdfinspoderinvalidez');

//CLIENTES ITA
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
        Route::post('asociados/asignar-fechaita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@asignarFecha_ITA')->name('admin.asociados.asignarFecha_ITA');
        Route::get('asociados/asignartramiteclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@asignartramiteclienteita')->name('admin.asociados.asignartramiteclienteita');
        Route::post('asociados/guardartramiteclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardartramiteclienteita')->name('admin.asociados.guardartramiteclienteita');
    //CREAR BATERIA CLIENTE ITA
        Route::get('asociados/crearbateriaclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@crearbateriaclienteita')->name('admin.asociados.crearbateriaclienteita');
        Route::post('asociados/guardarbateriaclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarbateriaclienteita')->name('admin.asociados.guardarbateriaclienteita');
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

        Route::post('/generar-pdf-dermedlab', [AsociadoController::class, 'generarPDFconsentimiento'])->name('generar.pdf.consentimiento');
        Route::post('/generar-pdf-guardardocumentoconsentimiento', [AsociadoController::class, 'generarPDFguardarconsentimiento'])->name('guardar.pdf.consentimiento');
        Route::post('/generar-pdf-conocinfor', [AsociadoController::class, 'generarPDFconsentimientoinformado'])->name('generar.pdf.consentimientoinformado');
        Route::post('/aprobariniciarcrearbateria', [AsociadoController::class, 'aprobariniciarcrearbateria'])->name('aprobariniciarcrearbateria');

        //CONTACTOS CLIENTES ITA
        Route::get('asociados/vercontactoclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@vercontactoclienteita')->name('admin.asociados.vercontactoclienteita');
        Route::get('asociados/crearcontactoclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@crearcontactoclienteita')->name('admin.asociados.crearcontactoclienteita');
        Route::post('asociados/guardarcontactoclienteita/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarcontactoclienteita')->name('admin.asociados.guardarcontactoclienteita');
//
Route::post('asociados/guardarproveedorinformefinal/{cliente}', 'App\Http\Controllers\Admin\AsociadoController@guardarproveedorinformefinal')->name('admin.asociados.guardarproveedorinformefinal');

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
        /* Route::post('asociados/guardaraprobacionprogramacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@guardaraprobacionprogramacionclientecomun')->name('admin.asociados.guardaraprobacionprogramacionclientecomun');
        Route::get('asociados/aprobarcotizacionprogramacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@aprobarcotizacionprogramacionclientecomun')->name('admin.asociados.aprobarcotizacionprogramacionclientecomun');
        Route::post('asociados/guardaraprobacioncotizacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@guardaraprobacioncotizacionclientecomun')->name('admin.asociados.guardaraprobacioncotizacionclientecomun');
        Route::get('asociados/generarpdfcotizacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@generarpdfcotizacionclientecomun')->name('admin.asociados.generarpdfcotizacionclientecomun'); */
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
    //CREAR DOCUMENTACION DE CLIENTE COMUN
        Route::get('asociados/creardocumentacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@creardocumentacionclientecomun')->name('admin.asociados.creardocumentacionclientecomun');
        Route::post('asociados/guardardocumentacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@guardardocumentacionclientecomun')->name('admin.asociados.guardardocumentacionclientecomun');
        Route::get('asociados/listadodocumentacionclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@listadodocumentacionclientecomun')->name('admin.asociados.listadodocumentacionclientecomun');
        Route::get('/buscardocumentoclientecomun/{clientecomun}', 'App\Http\Controllers\Admin\AsociadoController@buscardocumentoclientecomun')->name('buscardocumentoclientecomun');
//

//CLIENTES AUDITORIA
    //CREAR Y EDITAR CLIENTE AUDITORIA
        Route::get('asociados/crearclienteauditoria/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@crearclienteauditoria')->name('admin.asociados.crearclienteauditoria');
        Route::post('asociados/guardarclienteauditoria/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@guardarclienteauditoria')->name('admin.asociados.guardarclienteauditoria');
        Route::get('asociados/listadoclienteauditoria/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@listadoclienteauditoria')->name('admin.asociados.listadoclienteauditoria');
        Route::get('asociados/documentacionmultipleclienteauditoria/{asociado}', 'App\Http\Controllers\Admin\AsociadoController@documentacionmultipleclienteauditoria')->name('admin.asociados.documentacionmultipleclienteauditoria');
        Route::get('/buscarclientesauditoria', 'App\Http\Controllers\Admin\AsociadoController@buscarclientesauditoria')->name('buscarclientesauditoria');
        Route::get('asociados/verclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@verclienteauditoria')->name('admin.asociados.verclienteauditoria');
        Route::get('asociados/editarclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@editarclienteauditoria')->name('admin.asociados.editarclienteauditoria');
        Route::put('asociados/actualizarclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@actualizarclienteauditoria')->name('admin.asociados.actualizarclienteauditoria');
    //CREAR BATERIA CLIENTE AUDITORIA
        Route::get('asociados/crearbateriaclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@crearbateriaclienteauditoria')->name('admin.asociados.crearbateriaclienteauditoria');
        Route::post('asociados/guardarbateriaclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardarbateriaclienteauditoria')->name('admin.asociados.guardarbateriaclienteauditoria');
    //APROBAR COTIZACION DE PROGRAMACION DE CLIENTE AUDITORIA
        Route::get('asociados/aprobacioncotizacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@aprobacioncotizacionclienteauditoria')->name('admin.asociados.aprobacioncotizacionclienteauditoria');
        Route::get('/buscarbateriaclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@buscarbateriaclienteauditoria')->name('buscarbateriaclienteauditoria');
        Route::get('asociados/generarpdfcotizacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@generarpdfcotizacionclienteauditoria')->name('admin.asociados.generarpdfcotizacionclienteauditoria');
        Route::get('asociados/aprobarcotizacionprogramacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@aprobarcotizacionprogramacionclienteauditoria')->name('admin.asociados.aprobarcotizacionprogramacionclienteauditoria');
        Route::post('asociados/guardaraprobacioncotizacionclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardaraprobacioncotizacionclienteauditoria')->name('admin.asociados.guardaraprobacioncotizacionclienteauditoria');
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
    //CREAR FORMULARIO DE CLIENTE AUDITORIA
        Route::get('asociados/crearformularioclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@crearformularioclienteauditoria')->name('admin.asociados.crearformularioclienteauditoria');
        Route::post('asociados/guardarformularioclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardarformularioclienteauditoria')->name('admin.asociados.guardarformularioclienteauditoria');
        Route::post('asociados/generarpdfclienteauditoria{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@generarpdfclienteauditoria')->name('admin.asociados.generarpdfclienteauditoria');
    //GENERAR PDF
        Route::get('asociados/generaretiquetaclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@generaretiquetaclienteauditoria')->name('admin.asociados.generaretiquetaclienteauditoria');
        Route::get('asociados/generarchecklistclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@generarchecklistclienteauditoria')->name('admin.asociados.generarchecklistclienteauditoria');
        Route::post('asociados/descargarchecklistclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@descargarchecklistclienteauditoria')->name('admin.asociados.descargarchecklistclienteauditoria');
    //CONTACTOS CLIENTES ITA
        Route::get('asociados/vercontactoclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@vercontactoclienteauditoria')->name('admin.asociados.vercontactoclienteauditoria');
        Route::get('asociados/crearcontactoclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@crearcontactoclienteauditoria')->name('admin.asociados.crearcontactoclienteauditoria');
        Route::post('asociados/guardarcontactoclienteauditoria/{clienteauditoria}', 'App\Http\Controllers\Admin\AsociadoController@guardarcontactoclienteauditoria')->name('admin.asociados.guardarcontactoclienteauditoria');
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
    //CONTACTOS CLIENTES ITA
        Route::get('asociados/vercontactoclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@vercontactoclientebanco')->name('admin.asociados.vercontactoclientebanco');
        Route::get('asociados/crearcontactoclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@crearcontactoclientebanco')->name('admin.asociados.crearcontactoclientebanco');
        Route::post('asociados/guardarcontactoclientebanco/{clientebanco}', 'App\Http\Controllers\Admin\AsociadoController@guardarcontactoclientebanco')->name('admin.asociados.guardarcontactoclientebanco');
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
Route::put('/areaacciones/{id}/cambiarEstado', [AreaaccionController::class, 'cambiarEstado'])->name('cambiarEstado');


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

use App\Http\Controllers\Admin\QrController;

Route::post('/generar-qr', [FormularioController::class, 'generarQR'])->name('generar.qr');

