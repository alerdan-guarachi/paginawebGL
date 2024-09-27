<?php

namespace App\Http\Controllers;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class TemporalController extends Controller
{
    public function index()
    {
        //PAGINAS
        /* Permission::create(['name' => 'admin.roles.index', 'description' => 'Ver lista de roles']);
        Permission::create(['name' => 'admin.users.index', 'description' => 'Ver lista de usuarios']);
        Permission::create(['name' => 'admin.empresas.index', 'description' => 'Ver lista de empresas']);
        Permission::create(['name' => 'admin.proveedores.index', 'description' => 'Ver lista de proveedores']);
        Permission::create(['name' => 'admin.asociados.index', 'description' => 'Ver lista de asociados']);
        Permission::create(['name' => 'admin.areaacciones.index', 'description' => 'Ver lista de acciones']);
        Permission::create(['name' => 'login', 'description' => 'INICIAR SESIÓN']);
        Permission::create(['name' => 'admin.roles.create', 'description' => 'Crear rol']);
        Permission::create(['name' => 'admin.roles.edit', 'description' => 'Editar rol']);
        Permission::create(['name' => 'admin.users.edit', 'description' => 'Editar usuario']);
        Permission::create(['name' => 'admin.proveedores.create', 'description' => 'Crear proveedor']);
        Permission::create(['name' => 'admin.proveedores.edit', 'description' => 'Editar proveedor']);
        Permission::create(['name' => 'admin.proveedores.crearbateriaproveedor', 'description' => 'Crear batería de proveedores']);
        Permission::create(['name' => 'admin.proveedores.verbateriaproveedor', 'description' => 'Ver batería de proveedor']);
        Permission::create(['name' => 'admin.empresas.edit', 'description' => 'Editar empresa']);
        Permission::create(['name' => 'admin.empresas.create', 'description' => 'Crear empresa']);
        Permission::create(['name' => 'admin.empresas.destroy', 'description' => 'Eliminar empresa']); */

        //CLIENTES ITA
        /* Permission::create(['name' => 'admin.asociados.crearclienteita', 'description' => 'Crear cliente ITA']);
        Permission::create(['name' => 'admin.asociados.listadoclienteita', 'description' => 'Ver lista de clientes ITA']);
        Permission::create(['name' => 'admin.asociados.documentacionmultipleclienteita', 'description' => 'Subir documentación de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.verclienteita', 'description' => 'Ver cliente ITA']);
        Permission::create(['name' => 'admin.asociados.editarclienteita', 'description' => 'Editar cliente ITA']);
        Permission::create(['name' => 'admin.asociados.crearbateriaclienteita', 'description' => 'Crear batería de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.aprobacioncotizacionclienteita', 'description' => 'Ver cotización de programación de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.generarpdfcotizacionclienteita', 'description' => 'Generar PDF de cotización de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.aprobarcotizacionprogramacionclienteita', 'description' => 'Aprobar cotización de programación de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.crearprogramacionclienteita', 'description' => 'Crear programación de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.reprogramacionclienteita', 'description' => 'Reprogramar cliente ITA']);
        Permission::create(['name' => 'admin.asociados.estadoprogramacionclienteita', 'description' => 'Actualizar estado de programación de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.generarpdfprogramacionclienteita', 'description' => 'Generar PDF de programación de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.creardocumentacionclienteita', 'description' => 'Subir documentación de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.listadodocumentacionclienteita', 'description' => 'Ver lista de documentos de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.crearformularioclienteita', 'description' => 'Crear formulario médico de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.generaretiquetaclienteita', 'description' => 'Generar PDF de etiqueta de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.generarchecklistclienteita', 'description' => 'Crear requisitos de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.descargarchecklistclienteita', 'description' => 'Generar PDF  de requisitos de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.vercontactoclienteita', 'description' => 'Ver contactos de cliente ITA']);
        Permission::create(['name' => 'admin.asociados.crearcontactoclienteita', 'description' => 'Crear contactos de cliente ITA']); */
        /* Permission::create(['name' => 'descargardocumentacion', 'description' => 'Descargar documentación de clientes']); */

        //CLIENTES AUDITORIA
        /* Permission::create(['name' => 'admin.asociados.crearclienteauditoria', 'description' => 'Crear cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.listadoclienteauditoria', 'description' => 'Ver lista de clientes auditoría']);
        Permission::create(['name' => 'admin.asociados.documentacionmultipleclienteauditoria', 'description' => 'Subir documentación de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.verclienteauditoria', 'description' => 'Ver cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.editarclienteauditoria', 'description' => 'Editar cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.crearbateriaclienteauditoria', 'description' => 'Crear batería de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.aprobacioncotizacionclienteauditoria', 'description' => 'Ver cotización de prog. de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.generarpdfcotizacionclienteauditoria', 'description' => 'Generar PDF de cotización de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.aprobarcotizacionprogramacionclienteauditoria', 'description' => 'Aprobar cotización de prog. de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.crearprogramacionclienteauditoria', 'description' => 'Crear programación de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.reprogramacionclienteauditoria', 'description' => 'Reprogramar cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.estadoprogramacionclienteauditoria', 'description' => 'Actualizar estado de programación de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.generarpdfprogramacionclienteauditoria', 'description' => 'Generar PDF de programación de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.creardocumentacionclienteauditoria', 'description' => 'Subir documentación de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.listadodocumentacionclienteauditoria', 'description' => 'Ver lista de documentos de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.crearformularioclienteauditoria', 'description' => 'Crear formulario médico de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.generaretiquetaclienteauditoria', 'description' => 'Generar PDF de etiqueta de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.generarchecklistclienteauditoria', 'description' => 'Crear requisitos de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.descargarchecklistclienteauditoria', 'description' => 'Generar PDF  de requisitos de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.vercontactoclienteauditoria', 'description' => 'Ver contactos de cliente auditoría']);
        Permission::create(['name' => 'admin.asociados.crearcontactoclienteauditoria', 'description' => 'Crear contactos de cliente auditoría']); */

        //CLIENTES COMUNES
        /* Permission::create(['name' => 'admin.asociados.crearclientecomun', 'description' => 'Crear cliente común']);
        Permission::create(['name' => 'admin.asociados.listadoclientecomun', 'description' => 'Ver lista de clientes común']);
        Permission::create(['name' => 'admin.asociados.documentacionmultipleclientecomun', 'description' => 'Subir documentación de cliente común']);
        Permission::create(['name' => 'admin.asociados.verclientecomun', 'description' => 'Ver cliente común']);
        Permission::create(['name' => 'admin.asociados.editarclientecomun', 'description' => 'Editar cliente común']);
        Permission::create(['name' => 'admin.asociados.crearbateriaclientecomun', 'description' => 'Crear batería de cliente común']);
        Permission::create(['name' => 'admin.asociados.aprobacioncotizacionclientecomun', 'description' => 'Ver cotización de prog. de cliente común']);
        Permission::create(['name' => 'admin.asociados.generarpdfcotizacionclientecomun', 'description' => 'Generar PDF de cotización de cliente común']);
        Permission::create(['name' => 'admin.asociados.aprobarcotizacionprogramacionclientecomun', 'description' => 'Aprobar cotización de prog. de cliente común']);
        Permission::create(['name' => 'admin.asociados.crearprogramacionclientecomun', 'description' => 'Crear programación de cliente común']);
        Permission::create(['name' => 'admin.asociados.reprogramacionclientecomun', 'description' => 'Reprogramar cliente común']);
        Permission::create(['name' => 'admin.asociados.estadoprogramacionclientecomun', 'description' => 'Actualizar estado de programación de cliente común']);
        Permission::create(['name' => 'admin.asociados.generarpdfprogramacionclientecomun', 'description' => 'Generar PDF de programación de cliente común']);
        Permission::create(['name' => 'admin.asociados.creardocumentacionclientecomun', 'description' => 'Subir documentación de cliente común']);
        Permission::create(['name' => 'admin.asociados.listadodocumentacionclientecomun', 'description' => 'Ver lista de documentos de cliente común']); */

        //CLIENTES BANCOS
        /* Permission::create(['name' => 'admin.asociados.crearclientebanco', 'description' => 'Crear cliente banco']);
        Permission::create(['name' => 'admin.asociados.listadoclientebanco', 'description' => 'Ver lista de clientes banco']);
        Permission::create(['name' => 'admin.asociados.documentacionmultipleclientebanco', 'description' => 'Subir documentación de cliente banco']);
        Permission::create(['name' => 'admin.asociados.verclientebanco', 'description' => 'Ver cliente banco']);
        Permission::create(['name' => 'admin.asociados.editarclientebanco', 'description' => 'Editar cliente banco']);
        Permission::create(['name' => 'admin.asociados.crearbateriaclientebanco', 'description' => 'Crear batería de cliente banco']);
        Permission::create(['name' => 'admin.asociados.aprobacioncotizacionclientebanco', 'description' => 'Ver cotización de prog. de cliente banco']);
        Permission::create(['name' => 'admin.asociados.generarpdfcotizacionclientebanco', 'description' => 'Generar PDF de cotización de cliente banco']);
        Permission::create(['name' => 'admin.asociados.aprobarcotizacionprogramacionclientebanco', 'description' => 'Aprobar cotización de prog. de cliente banco']);
        Permission::create(['name' => 'admin.asociados.crearprogramacionclientebanco', 'description' => 'Crear programación de cliente banco']);
        Permission::create(['name' => 'admin.asociados.reprogramacionclientebanco', 'description' => 'Reprogramar cliente banco']);
        Permission::create(['name' => 'admin.asociados.estadoprogramacionclientebanco', 'description' => 'Actualizar estado de programación de cliente banco']);
        Permission::create(['name' => 'admin.asociados.generarpdfprogramacionclientebanco', 'description' => 'Generar PDF de programación de cliente banco']);
        Permission::create(['name' => 'admin.asociados.creardocumentacionclientebanco', 'description' => 'Subir documentación de cliente banco']);
        Permission::create(['name' => 'admin.asociados.listadodocumentacionclientebanco', 'description' => 'Ver lista de documentos de cliente banco']);
        Permission::create(['name' => 'admin.asociados.crearformularioclientebanco', 'description' => 'Crear formulario médico de cliente banco']);
        Permission::create(['name' => 'admin.asociados.generaretiquetaclientebanco', 'description' => 'Generar PDF de etiqueta de cliente banco']);
        Permission::create(['name' => 'admin.asociados.generarchecklistclientebanco', 'description' => 'Crear requisitos de cliente banco']);
        Permission::create(['name' => 'admin.asociados.descargarchecklistclientebanco', 'description' => 'Generar PDF  de requisitos de cliente banco']);
        Permission::create(['name' => 'admin.asociados.vercontactoclientebanco', 'description' => 'Ver contactos de cliente banco']);
        Permission::create(['name' => 'admin.asociados.crearcontactoclientebanco', 'description' => 'Crear contactos de cliente banco']); */

        Permission::create(['name' => 'admin.areaacciones.index', 'description' => 'Ver Bateria']);
        /* Permission::create(['name' => 'admin.admprogramaciones.index', 'description' => 'Ver control de programaciones']); */
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
