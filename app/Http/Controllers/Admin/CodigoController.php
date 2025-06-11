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
                          ->simplePaginate(5);

        $permisos = PermisoCodigo::all();
        /* $usuarios = User::where('estado', 'ACTIVO')->pluck('name', 'id'); */
        $usuarios = User::where('estado', 'ACTIVO')
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['PROVEEDOR', 'PROVEEDOR IF']);
            })
            ->orderBy('name')
            ->pluck('name', 'id');

        // Usamos mapWithKeys para que el select solo reciba el id y la descripción
        /* $permisosSolicitados = Permission::where('name', 'admin.asociados.crearbateriaclienteita')->orwhere('name', 'admin.ingreso.index')->orwhere('name', 'admin.asociados.crearbateriaclienteauditoria')->orwhere('name', 'admin.caja.ingresos.concederdescuentosingresos')->orwhere('name', 'admin.caja.ingresos.cambiarfecharegistro')->get()->mapWithKeys(function ($permiso) {
            return [$permiso->id => $permiso->description];
        });  */    
        $permisosSolicitados = Permission::whereIn('name', [
            'admin.asociados.crearbateriaclienteita',
            'admin.ingreso.index',
            'admin.asociados.crearbateriaclienteauditoria',
            'admin.caja.ingresos.concederdescuentosingresos',
            'admin.caja.ingresos.cambiarfecharegistro'
        ])->get()->mapWithKeys(function ($permiso) {
            $nombresPersonalizados = [
                'admin.asociados.crearbateriaclienteita' => 'CREAR BATERIA CLIENTE ITA',
                'admin.asociados.crearbateriaclienteauditoria' => 'CREAR BATERIA CLIENTE AUDITORIA',
                'admin.caja.ingresos.concederdescuentosingresos' => 'CONCEDER DESCUENTO INGRESO',
                'admin.caja.ingresos.cambiarfecharegistro' => 'CAMBIAR FECHA DE CAJA INGRESO',
                'admin.ingreso.index' => 'DESBLOQUEAR CAJA',
            ];

            return [$permiso->id => $nombresPersonalizados[$permiso->name] ?? $permiso->name];
        });
   
        $descripcionesPermisos = [
            'admin.asociados.crearbateriaclienteita' => 'CREAR BATERIA CLIENTE ITA',
            'admin.asociados.crearbateriaclienteauditoria' => 'CREAR BATERIA CLIENTE AUDITORIA',
            'admin.caja.ingresos.concederdescuentosingresos' => 'CONCEDER DESCUENTO INGRESO',
            'admin.caja.ingresos.cambiarfecharegistro' => 'CAMBIAR FECHA DE CAJA INGRESO',
            'admin.ingreso.index' => 'DESBLOQUEAR CAJA',
        ];


        // Generar el código una vez en el controlador
        $codigoGenerado = strtoupper(Str::random(7));

        return view('admin.codigo.index', compact('registroscodigos','permisos', 'usuarios', 'permisosSolicitados', 'codigoGenerado', 'descripcionesPermisos'));
    }

    public function store(Request $request)
    {
        // Validar los campos requeridos
        $request->validate([
            'usuarioSolicitante' => 'required|exists:users,id',
            'tiempoLimiteMinutos' => 'required|integer|min:1',
            'permisoSolicitado' => 'required|exists:permissions,id',
            'clienteid' => '',
        ]);

        $permisoSolicitado = Permission::find($request->permisoSolicitado);
        $usuarioSolicitante = User::find($request->usuarioSolicitante);

        $permisoCodigo = new PermisoCodigo([
            'usuarioSolicitante' => $usuarioSolicitante->name,
            'usuarioAutorizador' => auth()->user()->name,
            'codigo' => strtoupper(Str::random(7)),
            'fechaSolicitada' => Carbon::parse($request->fechaSolicitada)->format('Y-m-d'),
            'tiempoLimite' => $request->tiempoLimiteMinutos,
            'permisoSolicitado' => $permisoSolicitado->name,
            'clienteid' => $request->clienteid,
        ]);

        $permisoCodigo->save();

        if (in_array($permisoSolicitado->name, [
            'admin.asociados.crearbateriaclienteita',
            'admin.asociados.crearbateriaclienteauditoria',
            'admin.caja.ingresos.concederdescuentosingresos',
            'admin.caja.ingresos.cambiarfecharegistro'
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

        return redirect()->route('admin.codigo.index')->with('success', 'Permiso asignado correctamente.');
    }
}
