<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Empresa;
use App\Models\Area;
use App\Models\Areaaccion;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreAreaaccionRequest;
use App\Http\Requests\StoreAreaRequest;
use App\Models\Bateriaproveedor;

class AreaaccionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $areaaccionesestudios = Bateriaproveedor::where('tipoid', 2)
        ->orderBy('area')
        ->orderBy('estado')
        ->orderBy('accion')
        ->get()
        ->groupBy(['area', 'sucursal']);

        $areaacciones = Bateriaproveedor::where('tipoid', 1)
            ->orderBy('area')
            ->get()
            ->groupBy(['area', 'sucursal']);
        $areastipo = Area::select('tipoarea', 'nombrearea')
            ->orderBy('nombrearea')
            ->get();

        return view('admin.areaacciones.index', compact('areastipo','areaaccionesestudios', 'areaacciones'));
    }

    public function listadoareas(Request $request)
    {
        $estudios = Area::select('id', 'idtipoarea', 'tipoarea', 'nombrearea')->where('tipoarea','ESTUDIO')
            ->orderBy('nombrearea')
            ->get();
        $especialidades = Area::select('id', 'idtipoarea', 'tipoarea', 'nombrearea')->where('tipoarea','ESPECIALIDAD')
            ->orderBy('nombrearea')
            ->get();

        return view('admin.areaacciones.listadoareas', compact('estudios', 'especialidades'));
    }
    public function crearaccionarea(Areaaccion $areaaccion)
    {
        $areas = Area::orderBy('nombrearea')->pluck('nombrearea', 'id');
        
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
        return view('admin.areaacciones.crearaccionarea', compact('areaaccion','areas', 'estado', 'tipocliente', 'sucursal'));
    }
    public function guardaraccionarea(StoreAreaaccionRequest $request)
    {
        $areaData = $request->all();
        
        $areaccion = Areaaccion::create($areaData);

        return redirect()->route('admin.areaacciones.index', $areaccion)->with('info', 'La acción se creó con éxito');
    }
    public function buscarareaacciones(Request $request)
    {
        $busqueda = $request->get('buscarpor');
        $areaacciones = Areaaccion::where(function ($query) use ($busqueda) {
                    $query->where('area', 'like', "%$busqueda%")
                            ->orWhere('accion', 'like', "%$busqueda%");
                            /* ->orWhere('id', 'like', "%$busqueda%")
                            ->orderBy('proveedor'); */
                          })->simplePaginate(1000);

        return view('admin.areaacciones.index', compact('areaacciones'));
    }

    public function show(Area $area)
    {
       /*  $areas = Area::select('id', 'idtipoarea', 'tipoarea', 'nombrearea')
            ->orderBy('nombrearea')
            ->get();
        
        return view('admin.areaacciones.listadoareas', compact('areas')); */
    }
    public function edit($areaaccion)
{
    // Aquí debes buscar el área de acción basado en el $areaaccion recibido
    $areaaccion = Bateriaproveedor::findOrFail($areaaccion);

    // Luego, puedes pasar los datos a la vista
    $estadoproveedor = [
        'ACTIVO' => 'ACTIVO',
        'INACTIVO' => 'INACTIVO',
    ];
    $tipocliente = [
        'CLEINTES ITA' => 'CLEINTES ITA',
        'CLIENTES COMUNES' => 'CLIENTES COMUNES',
    ];
    $pagoservicio = [
        'INTERNO' => 'INTERNO',
        'EXTERNO' => 'EXTERNO',
    ];

    return view('admin.areaacciones.edit', compact('pagoservicio','tipocliente','estadoproveedor', 'areaaccion'));
}
    public function editarareaaccion(Areaaccion $areaaccion)
    {
        $estadoproveedor = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];

        return view('admin.areaacciones.editarareaaccion', compact('estadoproveedor', 'areaaccion'));
    }

    public function update(Bateriaproveedor $areaaccion, Request $request)
    {
        $areaaccion->update($request->all());

        return redirect()->route('admin.areaacciones.index', $areaaccion)->with('info', 'El área se actualizó con éxito');
    }

    public function actualizarEstado(Request $request, $id)
    {
        // Validar y actualizar el estado del registro
        $areaaccion = Areaaccion::findOrFail($id);
        $areaaccion->estado = $request->estado;
        $areaaccion->save();
    
        return response()->json(['message' => 'Estado actualizado correctamente']);
    }
    public function listadoestudioacciones(Request $request,  Areaaccion $area)
    {
        $areaaccionesestudios = Areaaccion::select('area', 'sucursal')
                                ->where('tipoid', 2)
                                ->orderBy('area')
                                ->distinct()
                                ->get();
        return view('admin.areaacciones.listadoestudioacciones', compact('area', 'areaaccionesestudio'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Area $area)
    {
        $tipoareas = [
            'ESTUDIO' => 'ESTUDIO',
            'ESPECIALIDAD' => 'ESPECIALIDAD',
        ];
        return view('admin.areaacciones.create', compact('tipoareas','area'));
    }

    public function store(StoreAreaRequest $request)
    {
        $areaData = $request->all();

        $area = Area::create($areaData);

        return redirect()->route('admin.areaacciones.index', $area)->with('info', 'El área se creó con éxito');
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    
    public function crearareabateria(Area $area)
    {
        $tipoareas = [
            'ESTUDIO' => 'ESTUDIO',
            'ESPECIALIDAD' => 'ESPECIALIDAD',
        ];
        return view('admin.areaacciones.crearareabateria', compact('tipoareas','area'));
    }

    public function guardarareabateria(StoreAreaRequest $request)
    {
        $areaData = $request->all();

        $area = Area::create($areaData);

        return redirect()->route('admin.areaacciones.index', $area)->with('info', 'El área se creó con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /* public function edit(Empresa $empresa)
    {
        return view('admin.empresas.edit', compact('empresa'));
    } */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    

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
