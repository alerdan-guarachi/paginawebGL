<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermisoCodigo;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Notifications\CodigoPermiso;
use App\Notifications\CodigoPermisoCaja;
use App\Notifications\CodigoPermisoInventario;
use App\Notifications\CodigoPermisoFechaPres;
use App\Notifications\CodigoPermisoArchivoPres;
use App\Notifications\CodigoPermisoContinuidadPres;
use App\Notifications\CodigoPermisoAdelantoVacacion;
use App\Notifications\CodigoPermisoFacturaRazonSocial;
use App\Notifications\CodigoPermisoDesbloqueoPrestaciones;

class CodigoController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.codigo.index')->only('index');
    }

    public function index()
    {
        $nombreusuario = auth()->user()->name;
        $registroscodigos = PermisoCodigo::where('usuarioAutorizador', $nombreusuario)
                          ->orderBy('created_at','desc')
                          ->simplePaginate(10);

        $solicitudcodigos = PermisoCodigo::where('estado', 'solicitado')
                          ->orderBy('created_at','desc')
                          ->simplePaginate(10);

        $permisos = PermisoCodigo::all();
        $usuarios = User::where('estado', 'ACTIVO')
        ->whereHas('roles', function ($query) {
            $query->whereIn('name', [
                'MAESTRO',
                'ADMINISTRADOR',
                'OPERATIVO',
                'CONTABLE',
                'PASANTE',
                'ASISTENTE ADMINISTRATIVO',
                'SUPERVISOR PRESTACIONES',
                'EJECUTIVO PRESTACIONES'
            ]);
        })
        ->orderBy('name')
        ->pluck('name', 'id');
  
        $permisosSolicitados = Permission::whereIn('name', [
            'admin.asociados.crearbateriaclienteita',
            'admin.ingreso.index',
            'admin.asociados.crearbateriaclienteauditoria',
            'admin.caja.ingresos.concederdescuentosingresos',
            'admin.caja.ingresos.cambiarfecharegistro',
            'admin.caja.egresos.cambiarfecharegistro',
            'admin.inventario.cambiarstockinventario',
            'admin.tramites.cambiarfechaprestaciones',
            'admin.tramites.editararchivoprestaciones',
            'admin.tramites.continuidadtramiteprestaciones',
            'admin.proveedoresservicios.adelantovacaciones',
            'admin.facturasegreso.cambiarrazonsocial',
            'admin.tramites.index'
        ])->get()->mapWithKeys(function ($permiso) {
            $nombresPersonalizados = [
                'admin.asociados.crearbateriaclienteita' => 'CREAR BATERIA CLIENTE ITA',
                'admin.asociados.crearbateriaclienteauditoria' => 'CREAR BATERIA CLIENTE AUDITORIA',
                'admin.caja.ingresos.concederdescuentosingresos' => 'CONCEDER DESCUENTO INGRESO',
                'admin.caja.ingresos.cambiarfecharegistro' => 'CAMBIAR FECHA DE CAJA INGRESO',
                'admin.caja.egresos.cambiarfecharegistro' => 'CAMBIAR FECHA DE CAJA EGRESO',
                'admin.ingreso.index' => 'DESBLOQUEAR CAJA',
                'admin.inventario.cambiarstockinventario' => 'CAMBIAR STOCK DE INVENTARIO',
                'admin.tramites.cambiarfechaprestaciones' => 'MODIFICAR FECHA DE PROCEDIMIENTO TRAMITE',
                'admin.tramites.editararchivoprestaciones' => 'EDITAR ARCHIVO DE PROCEDIMIENTO TRAMITE',
                'admin.tramites.continuidadtramiteprestaciones' => 'DAR CONTINUIDAD DE PROCEDIMIENTO TRAMITE',
                'admin.proveedoresservicios.adelantovacaciones' => 'ADELANTO DE VACACIONES',
                'admin.facturasegreso.cambiarrazonsocial' => 'MODIFICAR RAZON SOCIAL DE FACTURAS IMPUESTOS',
                'admin.tramites.index' => 'DESBLOQUEAR SECCIÓN DE PRESTACIONES'
            ];

            return [$permiso->id => $nombresPersonalizados[$permiso->name] ?? $permiso->name];
        });
   
        $descripcionesPermisos = [
            'admin.asociados.crearbateriaclienteita' => 'CREAR BATERIA CLIENTE ITA',
            'admin.asociados.crearbateriaclienteauditoria' => 'CREAR BATERIA CLIENTE AUDITORIA',
            'admin.caja.ingresos.concederdescuentosingresos' => 'CONCEDER DESCUENTO INGRESO',
            'admin.caja.ingresos.cambiarfecharegistro' => 'CAMBIAR FECHA DE CAJA INGRESO',
            'admin.caja.egresos.cambiarfecharegistro' => 'CAMBIAR FECHA DE CAJA EGRESO',
            'admin.ingreso.index' => 'DESBLOQUEAR CAJA',
            'admin.inventario.cambiarstockinventario' => 'CAMBIAR STOCK DE INVENTARIO',
            'admin.tramites.cambiarfechaprestaciones' => 'MODIFICAR FECHA DE PROCEDIMIENTO TRAMITE',
            'admin.tramites.editararchivoprestaciones' => 'EDITAR ARCHIVO DE PROCEDIMIENTO TRAMITE',
            'admin.tramites.continuidadtramiteprestaciones' => 'DAR CONTINUIDAD DE PROCEDIMIENTO TRAMITE',
            'admin.proveedoresservicios.adelantovacaciones' => 'ADELANTO DE VACACIONES',
            'admin.facturasegreso.cambiarrazonsocial' => 'MODIFICAR RAZON SOCIAL DE FACTURAS IMPUESTOS',
            'admin.tramites.index' => 'DESBLOQUEAR SECCIÓN DE PRESTACIONES'
        ];

        $codigoGenerado = strtoupper(Str::random(7));

        return view('admin.codigo.index', compact('solicitudcodigos','registroscodigos','permisos', 'usuarios', 'permisosSolicitados', 'codigoGenerado', 'descripcionesPermisos'));
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'usuarioSolicitante' => 'required',
            'tiempoLimiteMinutos' => '',
            'permisoSolicitado' => 'required',
            'clienteid' => '',
        ]);

        $permisoSolicitado = Permission::find($request->permisoSolicitado);
        $usuarioSolicitante = User::find($request->usuarioSolicitante);

        /* $permisoCodigo = new PermisoCodigo([
            'usuarioSolicitante' => $usuarioSolicitante->name,
            'usuarioAutorizador' => auth()->user()->name,
            'codigo' => strtoupper(Str::random(7)),
            'fechaSolicitada' => Carbon::parse($request->fechaSolicitada)->format('Y-m-d'),
            'tiempoLimite' => $request->tiempoLimiteMinutos,
            'permisoSolicitado' => $permisoSolicitado->name,
            'clienteid' => $request->clienteid,
        ]);

        $permisoCodigo->save(); */

        $permisoCodigo = null;

        if ($request->solicitud_id) {
            $permisoCodigo = PermisoCodigo::find($request->solicitud_id);
        }

        if ($permisoCodigo) {
            $permisoCodigo->usuarioAutorizador = auth()->user()->name;
            $permisoCodigo->codigo = strtoupper(Str::random(7));
            $permisoCodigo->estado = 'pendiente';
            $permisoCodigo->save();

        } else {
            $permisoCodigo = new PermisoCodigo([
                'usuarioSolicitante' => $usuarioSolicitante->name,
                'usuarioAutorizador' => auth()->user()->name,
                'codigo' => strtoupper(Str::random(7)),
                'fechaSolicitada' => Carbon::parse($request->fechaSolicitada)->format('Y-m-d'),
                'tiempoLimite' => $request->tiempoLimiteMinutos,
                'permisoSolicitado' => $permisoSolicitado->name,
                'clienteid' => $request->clienteid,
                'motivo' => $request->motivo,
                'estado' => 'activo',
            ]);
            $permisoCodigo->save();
        }


        if (in_array($permisoSolicitado->name, [
            'admin.asociados.crearbateriaclienteita',
            'admin.asociados.crearbateriaclienteauditoria',
            'admin.caja.ingresos.concederdescuentosingresos',
            'admin.caja.ingresos.cambiarfecharegistro',
            'admin.caja.egresos.cambiarfecharegistro'
        ])) {
            if ($usuarioSolicitante) {
                $usuarioSolicitante->notify(new CodigoPermiso($permisoCodigo));
            }
        }

        if (in_array($permisoSolicitado->name, [
            'admin.ingreso.index',
        ])) {
            if ($usuarioSolicitante) {
                $usuarioSolicitante->notify(new CodigoPermisoCaja($permisoCodigo));
            }
        }

        if (in_array($permisoSolicitado->name, [
            'admin.inventario.cambiarstockinventario',
        ])) {
            if ($usuarioSolicitante) {
                $usuarioSolicitante->notify(new CodigoPermisoInventario($permisoCodigo));
            }
        }

        if (in_array($permisoSolicitado->name, [
            'admin.tramites.cambiarfechaprestaciones',
        ])) {
            if ($usuarioSolicitante) {
                $usuarioSolicitante->notify(new CodigoPermisoFechaPres($permisoCodigo));
            }
        }

        if (in_array($permisoSolicitado->name, [
            'admin.tramites.editararchivoprestaciones',
        ])) {
            if ($usuarioSolicitante) {
                $usuarioSolicitante->notify(new CodigoPermisoArchivoPres($permisoCodigo));
            }
        }

        if (in_array($permisoSolicitado->name, [
            'admin.tramites.continuidadtramiteprestaciones',
        ])) {
            if ($usuarioSolicitante) {
                $usuarioSolicitante->notify(new CodigoPermisoContinuidadPres($permisoCodigo));
            }
        }

        if (in_array($permisoSolicitado->name, [
            'admin.proveedoresservicios.adelantovacaciones',
        ])) {
            if ($usuarioSolicitante) {
                $usuarioSolicitante->notify(new CodigoPermisoAdelantoVacacion($permisoCodigo));
            }
        }

        if (in_array($permisoSolicitado->name, [
            'admin.facturasegreso.cambiarrazonsocial',
        ])) {
            if ($usuarioSolicitante) {
                $usuarioSolicitante->notify(new CodigoPermisoFacturaRazonSocial($permisoCodigo));
            }
        }

        if (in_array($permisoSolicitado->name, [
            'admin.tramites.index',
        ])) {
            if ($usuarioSolicitante) {
                $usuarioSolicitante->notify(new CodigoPermisoDesbloqueoPrestaciones($permisoCodigo));
            }
        }

        return redirect()->route('admin.codigo.index')->with('success', 'Permiso asignado correctamente.');
    }
}
