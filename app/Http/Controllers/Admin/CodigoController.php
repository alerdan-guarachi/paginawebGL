<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermisoCodigo;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use Illuminate\Support\Str;


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
                          ->orderBy('fechasolicitada','desc')
                          ->simplePaginate(10);

        $permisos = PermisoCodigo::all();
        $usuarios = User::pluck('name', 'id');

        // Usamos mapWithKeys para que el select solo reciba el id y la descripción
        $permisosSolicitados = Permission::where('name', 'admin.asociados.crearbateriaclienteita')->orwhere('name', 'admin.ingreso.index')->get()->mapWithKeys(function ($permiso) {
            return [$permiso->id => $permiso->description];
        });        

        // Generar el código una vez en el controlador
        $codigoGenerado = strtoupper(Str::random(15));

        return view('admin.codigo.index', compact('registroscodigos','permisos', 'usuarios', 'permisosSolicitados', 'codigoGenerado'));
    }

    public function store(Request $request)
    {
        // Validar los campos requeridos
        $request->validate([
            'usuarioSolicitante' => 'required|exists:users,id',
            'tiempoLimiteMinutos' => 'required|integer|min:1',
            'permisoSolicitado' => 'required|exists:permissions,id',
        ]);

        $permisoCodigo = new PermisoCodigo([
            'usuarioSolicitante' => User::find($request->usuarioSolicitante)->name,
            'usuarioAutorizador' => auth()->user()->name,
            'codigo' => strtoupper(Str::random(15)),
            'fechaSolicitada' => Carbon::parse($request->fechaSolicitada)->format('Y-m-d'),
            'tiempoLimite' => $request->tiempoLimiteMinutos,
            'permisoSolicitado' => Permission::find($request->permisoSolicitado)->name,
        ]);

        $permisoCodigo->save();

        return redirect()->route('admin.codigo.index')->with('success', 'Permiso asignado correctamente.');
    }
}
