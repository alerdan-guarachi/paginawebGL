<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Cliente;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;
use App\Models\Pais;
use App\Models\Ciudad;
use App\Models\Departamento;
use App\Models\Aseguradora;
use App\Models\Afp;
use PDF;
use App\Models\Etiqueta;
use Illuminate\Support\Facades\View;

class EtiquetaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /* public function __construct() { 
        $this->middleware('can:admin.profiles.index')->only('index');
    } */

    public function index(Request $request, Cliente $cliente)
    {
        /* $user = Auth::user();
        $cliente = Cliente::where('users_id', $user->id)->first();
        $cliente = Cliente::where('usuarioregistro', $user->name)->first();
        $cliente = Cliente::where('usuarioultimaactualizacion', $user->name)->first();
        $clientes = Cliente::simplePaginate(7);
        

        return view('admin.etiquetas.index', compact ('clientes', 'cliente')); */
        return view('admin.etiquetas.index', compact('cliente'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Empresa $empresa)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClienteRequest $request)
    {
      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Cliente $cliente)
    {
       /*  $pdf = PDF::loadView('admin.etiquetas.show', compact('cliente'));
        return $pdf->download('clientes.PDF'); */

        return view('admin.etiquetas.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Cliente $cliente)
    {
        return view('admin.clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('admin.clientes.index', $cliente)->with('eliminar', 'ok');
    }
}
