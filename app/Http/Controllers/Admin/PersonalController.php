<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Personal;
use App\Models\Banco;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\StorePersonalRequest;
use App\Http\Requests\UpdatePersonalRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /* public function __construct() { 
        $this->middleware('can:admin.profiles.index')->only('index');
    } */

    public function index(Request $request)
    {
        $user = Auth::user();
        $personal = Personal::where('usuarioid', $user->id)->first();

        
        $usuarioAutenticadoId = Auth::id();

        // Solo el registro del usuario autenticado
        $personales = Personal::where('usuarioid', $usuarioAutenticadoId)->get();

        // Todos los registros excepto el del usuario autenticado
        $todospersonales = Personal::where('usuarioid', '!=', $usuarioAutenticadoId)->get();

        return view('admin.personal.index', compact ('todospersonales', 'personales', 'personal'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sucursal = [
            'COCHABAMBA' => 'COCHABAMBA',
            'SANTA CRUZ' => 'SANTA CRUZ',
        ];
        $estado = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $ciudadexp = [
            'CBB' => 'CBB',
            'SCZ' => 'SCZ',
            'ORU' => 'ORU',
            'LPZ' => 'LPZ',
            'PTS' => 'PTS',
            'TRJ' => 'TRJ',
            'CHQ' => 'CHQ',
            'BNI' => 'BNI',
            'PND' => 'PND',
        ];
        $bancos = Banco::orderBy('nombrebanco')->pluck('nombrebanco', 'nombrebanco');
        $usuarionombre = auth()->user()->name;
        return view('admin.personal.create', compact('usuarionombre','sucursal', 'estado', 'ciudadexp', 'bancos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePersonalRequest $request)
    {
        if($request->hasFile('picture')){
            $file = $request->file('picture');
            $image_name=time().'_'.$file->getClientOriginalName();
            $file->move(public_path("/personalfotos"),$image_name);
        }

        $personal = Personal::create($request->all()+['image'=>$image_name]);
    

        return redirect()->route('admin.personal.index', $personal)->with('info', 'El perfil se creó con exito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Personal $personal)
    {
        return view('admin.personal.show', compact('personal'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Personal $personal)
    {
        $sucursal = [
            'COCHABAMBA' => 'COCHABAMBA',
            'SANTA CRUZ' => 'SANTA CRUZ',
        ];
        $estado = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $ciudadexp = [
            'CBB' => 'CBB',
            'SCZ' => 'SCZ',
            'ORU' => 'ORU',
            'LPZ' => 'LPZ',
            'PTS' => 'PTS',
            'TRJ' => 'TRJ',
            'CHQ' => 'CHQ',
            'BNI' => 'BNI',
            'PND' => 'PND',
        ];
        $bancos = Banco::orderBy('nombrebanco')->pluck('nombrebanco', 'nombrebanco');

        $imagenCliente = null;
        if ($personal->image) {
            $imagenCliente = asset('personalfotos/' . $personal->image);
        }
        $usuarionombre = auth()->user()->name;

        return view('admin.personal.edit', compact('usuarionombre', 'personal', 'sucursal', 'estado', 'ciudadexp', 'bancos', 'imagenCliente'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePersonalRequest $request, Personal $personal)
    {
        $data = $request->validated();
        if($request->hasFile('picture')){
            $file = $request->file('picture');
            $image_name=time().'_'.$file->getClientOriginalName();
            $file->move(public_path("/personalfotos"),$image_name);
            $data['image'] = $image_name;
        }
        
        $personal->update($data);

        return redirect()->route('admin.personal.show', $personal)->with('info', 'El perfil se actualizó con éxito');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Personal $personal)
    {
        $personal->delete();

        return redirect()->route('admin.personal.index', $personal)->with('eliminar', 'ok');
    }
}
