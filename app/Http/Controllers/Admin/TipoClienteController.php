<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\User;
use App\Models\TipoCliente;

class TipoClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /* public function __construct() { 
        $this->middleware('can:admin.categories.index')->only('index');
    } */
    
    public function index()
    {
        $tipo_clientes = TipoCliente::all();
        return view('admin.tipo_clientes.index', compact ('tipo_clientes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.tipo_clientes.create');
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
            'nombre' => 'required'
        ]);

        $tipocliente = TipoCliente::create($request->all());

        return redirect()->route('admin.tipo_clientes.index', $tipocliente)->with('info', 'La categoría se creó con exito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TipoCliente $tipocliente)
    {
        return view('admin.tipo_clientes.show', compact('tipo_cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TipoCliente $tipocliente)
    {
        return view('admin.tipo_clientes.edit', compact('tipo_cliente'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TipoCliente $tipocliente)
    {
        $request->validate([
            'nombre' => 'required'
        ]);

        $tipocliente->update($request->all());

        return redirect()->route('admin.tipo_clientes.index', $tipocliente)->with('info', 'La categoría se actualizó con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        /* $tipocliente = Category::findOrFail($id);

        if ($tipocliente->eventos()->count() > 0) {
            return redirect()->route('admin.categories.index', $tipocliente)
                ->with('error', 'No se puede eliminar la categoría porque tiene eventos asociados.');
        }

        $tipocliente->delete();

        return redirect()->route('admin.categories.index', $tipocliente)->with('eliminar', 'ok'); */

        /* $category->delete();

        return redirect()->route('admin.categories.index', $category)->with('eliminar', 'ok'); */
    }
}
