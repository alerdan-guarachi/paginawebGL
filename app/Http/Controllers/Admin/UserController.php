<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use App\Models\Evento;
use App\Models\Arqueocaja;
use App\Models\Consolidadocaja;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() { 
        $this->middleware('can:admin.users.index')->only('index');
    }

    public function index(Request $request)
    {
        $name = $request->get('buscarpor');

        $users = User::where('name', 'LIKE', "%$name%")
                    ->where('id', '!=', 1)
                    ->with('roles')
                    ->get();

        foreach ($users as $user) {
            $user->rolesFormatted = $user->roles->pluck('name')->join(', ') ?: 'NINGUNO';
        }

        return view('admin.users.index', compact('users'));
    }

    // En app/Http/Controllers/UserController.php (o el nombre que tenga tu controlador)


    public function eliminarCuenta($id)
    {
        // 1. Busca al usuario
        $user = User::find($id);

        if ($user) {
            // 2. Cambia el valor (asegúrate que la columna se llame 'estado')
            $user->estado = 'INACTIVO'; 
            
            // 3. ¡ESTA LÍNEA ES LA QUE GUARDA EN LA DB!
            $user->save(); 

            return response()->json(['message' => 'Eliminado'], 200);
        }

        return response()->json(['message' => 'Usuario no encontrado'], 404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
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
            'name' => 'required'
        ]);

        if($request->hasFile('picture')){
            $file = $request->file('picture');
            $image_name=time().'_'.$file->getClientOriginalName();
            $file->move(public_path("/image"),$image_name);
        }
        /* request()->validate(Profile::$rules); */

        $user = User::create($request->all()+['image'=>$image_name]);

        return redirect()->route('admin.users.index', $user)->with('info', 'El usuario se creó con exito');
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
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /* public function update(Request $request, User $user)
    {
        $user->roles()->sync($request->roles);

        return redirect()->route('admin.users.edit', $user)->with('info', 'Se asignaron los roles correctamente');
    } */

    public function update(Request $request, User $user)
    {
        $user->roles()->sync($request->roles);

        $tieneContable = $user->roles()->where('name', 'CONTABLE')->exists();

        if ($tieneContable) {

            $existeArqueo = Arqueocaja::where('usuarioarqueoid', $user->id)
                ->where('usuarioarqueonombre', $user->name)
                ->exists();

            if (!$existeArqueo) {
                Arqueocaja::create([
                    'usuarioarqueoid' => $user->id,
                    'usuarioarqueonombre' => $user->name,
                    'billetecorte200' => 0,
                    'billetecorte100' => 0,
                    'billetecorte50'  => 0,
                    'billetecorte20'  => 0,
                    'billetecorte10'  => 0,
                    'monedacorte5'    => 0,
                    'monedacorte2'    => 0,
                    'monedacorte1'    => 0,
                    'monedacorte050'  => 0,
                    'monedacorte020'  => 0,
                    'monedacorte010'  => 0,
                ]);
            }

            $existeConsolidado = Consolidadocaja::where('usuarioconsolidadoid', $user->id)
                ->where('usuarioconsolidadonombre', $user->name)
                ->exists();

            if (!$existeConsolidado) {
                Consolidadocaja::create([
                    'usuarioconsolidadoid'     => $user->id,
                    'usuarioconsolidadonombre' => $user->name,
                    'consolidadoefectivo'      => 0.00,
                    'consolidadodeposito'      => 0.00,
                    'consolidadotransferencia' => 0.00,
                    'consolidadocheque'        => 0.00,
                    'consolidadoatc'           => 0.00,
                    'consolidadocxc'           => 0.00,
                    'consolidadocpp'           => 0.00,
                ]);
            }
        }

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('info', 'Se asignaron los roles correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /* public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index', $user)->with('eliminar', 'ok');
    } */
    public function destroy(User $user)
    {
        $count = User::where('email', 'LIKE', 'N%')
                    ->whereRaw("email REGEXP '^N[0-9]+$'")
                    ->count();

        $nuevoEmail = 'N' . ($count + 1);
        $user->roles()->sync([]);
        $user->email = $nuevoEmail;
        $user->password = '';
        $user->remember_token = null;
        $user->estado = 'INACTIVO';
        $user->save();

        return redirect()->route('admin.users.index')->with('eliminar', 'ok');
    }
}
