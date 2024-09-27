<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Proveedor;
use App\Models\Areaaccion;
use App\Models\Departamento;
use App\Http\Requests\StoreProveedorRequest;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /* public function __construct() { 
        $this->middleware('can:admin.users.index')->only('index');
    } */

    public function index(Request $request)
    {
        $nombreproveedor = $request->get('buscarpor');

        $proveedores = Proveedor::where('proveedor','Like',"%$nombreproveedor%")->simplePaginate(1000);
       

        return view('admin.proveedores.index', compact('proveedores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areas = AreaAccion::select('area')->distinct()->pluck('area');
        $estadoproveedor = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
        return view('admin.proveedores.create', compact('departamentos', 'estadoproveedor', 'areas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProveedorRequest $request)
{
    $idCiudad = $request->input('ciudad');
    $ciudad = Departamento::findOrFail($idCiudad);
    $ciudadNombre = $ciudad->departamento;

    $areasSeleccionadas = $request->input('area');
    $estadoSeleccionado = $request->input('estadoproveedor');



    foreach ($areasSeleccionadas as $areaNombre) { // Iterar sobre los nombres de las áreas seleccionadas
        $proveedorData = $request->except(['area', '_token']);
        $proveedorData['ciudad'] = $ciudadNombre;
        $proveedorData['area'] = $areaNombre;
        $proveedorData['estadoproveedor'] = $estadoSeleccionado;



        Proveedor::create($proveedorData);
    }

    return redirect()->route('admin.proveedores.index')->with('info', 'Los proveedores se crearon con éxito');
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
    public function edit(Proveedor $proveedore)
{
    // Obtener las áreas asociadas al proveedor
    $areas_proveedor = explode(',', $proveedore->area);
    
    // Obtener las áreas de acciones asociadas al área del proveedor
    $areaacciones_proveedor = AreaAccion::whereIn('area', $areas_proveedor)
                                        ->select('areaaccion')
                                        ->distinct()
                                        ->pluck('areaaccion')
                                        ->toArray();
    
    // Obtener todas las áreas de acciones únicas
    $areaacciones = AreaAccion::select('areaaccion')
                              ->distinct()
                              ->pluck('areaaccion')
                              ->toArray();
    
    // Pasar las áreas de acciones del proveedor a la vista
    return view('admin.proveedores.edit', compact('proveedore', 'areas_proveedor', 'areaacciones', 'areaacciones_proveedor'));
}



    


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proveedor $proveedore)
    {

        $proveedore->update($request->all());

        return redirect()->route('admin.proveedores.index', $proveedore)->with('info', 'El proveedor se actualizó con éxito');
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
