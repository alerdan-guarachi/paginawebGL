<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Empresa;
use App\Models\Proveedor;
use App\Models\Areaaccion;
use App\Models\Area;
use App\Http\Requests\StoreAreaaccionRequest;
use App\Http\Requests\StoreBateriaproveedorRequest;
use App\Models\Bateriaproveedor;
use Illuminate\Support\Facades\Auth;

class AccionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct() { 
        $this->middleware ('can:admin.areaacciones.index')->only('index');
    }

    public function index(Request $request)
    {
        $nombreareaaccion = $request->get('buscarpor');

        $areaacciones = Bateriaproveedor::where('area', 'LIKE', "%$nombreareaaccion%")
            ->orderBy('area')
            ->distinct()
            ->get();

        return view('admin.acciones.index', compact('areaacciones'));
    }

    public function buscarareaacciones(Request $request)
    {
        $busqueda = $request->get('buscarpor');
        $areaacciones = Bateriaproveedor::where(function ($query) use ($busqueda) {
                    $query->where('area', 'like', "%$busqueda%")
                            ->orWhere('accion', 'like', "%$busqueda%")
                            ->orWhere('sucursal', 'like', "%$busqueda%")
                            ->orderBy('area');
                          })->simplePaginate(1000);

        return view('admin.acciones.index', compact('areaacciones'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Bateriaproveedor $bateriaproveedor)
    {
        $tiponombre = Area::orderBy('tipoarea')
            ->distinct()
            ->pluck('tipoarea', 'tipoarea')
            ->toArray();

        // Obtener todas las áreas
        $areas = Area::orderBy('nombrearea')->get();

        $estado = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];

        $tipocliente = [
            'CLIENTES ITA' => 'CLIENTES ITA',
            'CLIENTES COMUNES' => 'CLIENTES COMUNES',
        ];

        $sucursal = [
            'SANTA CRUZ' => 'SANTA CRUZ',
            'COCHABAMBA' => 'COCHABAMBA',
        ];

        $servicio = [
            'INTERNO' => 'INTERNO',
            'EXTERNO' => 'EXTERNO',
        ];

        $proveedores = Proveedor::orderBy('proveedor')->pluck('proveedor', 'id')->toArray();

        return view('admin.acciones.create', compact('tiponombre', 'bateriaproveedor', 'areas', 'estado', 'tipocliente', 'sucursal', 'proveedores', 'servicio'));
    }
    public function store(StoreBateriaproveedorRequest $request)
    {
        $bateriaProveedorData = $request->all();

        // Obtener el nombre del proveedor basado en el proveedorid seleccionado
        $proveedor = Proveedor::find($bateriaProveedorData['proveedorid']);
        if ($proveedor) {
            $bateriaProveedorData['proveedor'] = $proveedor->proveedor;  // Guardar el nombre del proveedor en vez del ID
        }

        // Obtener el nombre del tipo de área basado en el areasid seleccionado
        $tipoArea = Area::find($bateriaProveedorData['areasid']);
        if ($tipoArea) {
            $bateriaProveedorData['tipoarea'] = $tipoArea->nombrearea;  // Guardar el nombre del área en vez del ID
        }

        // Asignar el ID del usuario autenticado al campo 'usuarioid'
        $bateriaProveedorData['usuarioid'] = Auth::user()->id;

        // Asignar el nombre del usuario autenticado al campo 'usuarioregistro'
        $bateriaProveedorData['usuarioregistro'] = Auth::user()->name;

        // Asignar el ID del asociado basado en el nombre seleccionado en 'asociado'
        if ($bateriaProveedorData['asociado'] === 'CLIENTES ITA') {
            $bateriaProveedorData['asociadoid'] = 6;
        } elseif ($bateriaProveedorData['asociado'] === 'CLIENTES COMUNES') {
            $bateriaProveedorData['asociadoid'] = 3;
        }

        // Crear el registro en bateriaproveedores
        $bateriaproveedor = Bateriaproveedor::create($bateriaProveedorData);

        return redirect()->route('admin.acciones.index', $bateriaproveedor)->with('info', 'La acción se creó con éxito');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

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
