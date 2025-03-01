<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Mensaje;
use App\Models\Proveedoresservicios;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class MensajeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() { 
        $this->middleware ('can:admin.mensajes.index')->only('index');
    }

    public function index(Request $request)
{

    $mensajes = Mensaje::orderBy('created_at', 'desc')->limit(10)->get();
   
    return view('admin.mensajes.index', compact('mensajes'));
}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Mensaje $mensaje)
    {
        $usuarioAutenticado = Auth::user()->name;
        
        // Obtén todos los usuarios
    $todosPersonal = Proveedoresservicios::pluck('nombrecompleto', 'id')->toArray();

    // Filtra el personal para excluir al usuario autenticado
    $personal = array_filter($todosPersonal, function ($nombre) use ($usuarioAutenticado) {
        return $nombre !== $usuarioAutenticado;
    });


         // Obtén el nombre del usuario autenticado
    

    // Define la fecha de inicio del día
    $hoy = Carbon::now()->startOfDay();

    // Obtén los mensajes enviados por el usuario autenticado
    $mensajesenviados = Mensaje::where('usuarioregistro', $usuarioAutenticado)
        ->where('created_at', '>=', $hoy)
        ->orderBy('created_at', 'desc')
        ->get(); // Elimina el límite si quieres todos los mensajes

        return view('admin.mensajes.create', compact('mensaje', 'personal', 'mensajesenviados'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    // Valida los datos del formulario
    $request->validate([
        'opcion' => 'required|string',
        'usuariodestino' => 'nullable|array', // Permite un array para múltiples usuarios
        'usuariodestino.*' => 'nullable|integer|exists:personal,id' // Valida cada ID de usuario en la tabla 'personal'
    ]);

    // Verifica la opción seleccionada
    if ($request->input('opcion') === 'TODOS') {
        // Si se selecciona 'Todos', guarda un mensaje con 'todos' como destinatario
        Mensaje::create(array_merge($request->all(), ['usuariodestino' => 'TODOS']));
    } else {
        // Si se selecciona 'Elegir usuarios', guarda un mensaje para cada usuario seleccionado
        $usuarios = $request->input('usuariodestino');
        foreach ($usuarios as $usuarioId) {
            // Encuentra al usuario en la tabla 'personal' por ID
            $usuario = Proveedoresservicios::find($usuarioId);
            if ($usuario) {
                // Crea un nuevo mensaje para cada usuario usando el nombre completo
                Mensaje::create(array_merge($request->except('usuariodestino'), ['usuariodestino' => $usuario->nombrecompleto]));
            }
        }
    }

    return redirect()->route('admin.mensajes.create')->with('info', 'El mensaje se envió con éxito');
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
    public function edit(Empresa $empresa)
    {
        return view('admin.empresas.edit', compact('empresa'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Empresa $empresa)
    {

        $empresa->update($request->all());

        return redirect()->route('admin.empresas.index', $empresa)->with('info', 'La empresa se actualizó con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Empresa $empresa)
    {
        $empresa->delete();

        return redirect()->route('admin.empresas.index', $empresa)->with('eliminar', 'ok');
    }
    
    
}
