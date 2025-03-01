<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Proveedoresservicios;
use App\Models\Banco;
use App\Models\Seccionprovservicio;
use App\Models\Subseccionprovservicio;
use App\Models\Detalleseccionprov;
use App\Models\User;
use App\Models\Inventario;
use App\Models\Bateriaproveedorservicios;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\StoreProveedoresserviciosRequest;
use App\Http\Requests\UpdateProveedoresserviciosRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProveedoresserviciosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /* public function __construct() { 
        $this->middleware('can:admin.profiles.index')->only('index');
    } */

    /* public function index(Request $request)
    {
        $razonsocial = $request->get('buscarpor');

        $usuarioAutenticadoNombre = Auth::user()->name;

        $soloproveedorservicio = Proveedoresservicios::where('razonsocial', $usuarioAutenticadoNombre)->get();

        $todosproveedoresservicios = Proveedoresservicios::where('razonsocial','Like',"%$razonsocial%")
                                ->simplePaginate(1000);


        return view('admin.proveedoresservicios.index', compact ('todosproveedoresservicios', 'soloproveedorservicio'));
    } */
    public function index(Request $request) 
    {
        $razonsocial = $request->get('buscarpor');
        $usuarioAutenticadoNombre = Auth::user()->name;

        // Obtener el proveedor autenticado si existe en ProveedoresServicios
        $soloproveedorservicio = Proveedoresservicios::where('razonsocial', $usuarioAutenticadoNombre)->get();

        // Obtener todos los proveedores que coincidan con la búsqueda
        $todosproveedoresservicios = Proveedoresservicios::where('razonsocial', 'Like', "%$razonsocial%")
                                    ->simplePaginate(1000);

        // Obtener inventario de todos los proveedores en la lista
        $inventarios = Inventario::whereIn('proveedorid', $todosproveedoresservicios->pluck('id'))->get();
        $bateriaproveedorservicios = Bateriaproveedorservicios::whereIn('proveedorid', $todosproveedoresservicios->pluck('id'))->get();

        return view('admin.proveedoresservicios.index', compact('todosproveedoresservicios', 'soloproveedorservicio', 'inventarios', 'bateriaproveedorservicios'));
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
        return view('admin.proveedoresservicios.create', compact('usuarionombre','sucursal', 'estado', 'ciudadexp', 'bancos'));
    }

    public function crearprovserviciobasico()
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
        return view('admin.proveedoresservicios.crearprovserviciobasico', compact('usuarionombre','sucursal', 'estado', 'ciudadexp', 'bancos'));
    }

    public function crearprovserviciohumano()
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
        return view('admin.proveedoresservicios.crearprovserviciohumano', compact('usuarionombre','sucursal', 'estado', 'ciudadexp', 'bancos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProveedoresserviciosRequest $request)
    {
        if($request->hasFile('picture')){
            $file = $request->file('picture');
            $image_name=time().'_'.$file->getClientOriginalName();
            $file->move(public_path("/proveedoresserviciosfotos"),$image_name);
        }

        $proveedoresservicios = Proveedoresservicios::create($request->all()+['image'=>$image_name]);
    

        return redirect()->route('admin.proveedoresservicios.index', $proveedoresservicios)->with('info', 'El perfil se creó con exito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);

        if (!$proveedoresservicios) {
            abort(404, 'Registro no encontrado.');
        }

        return view('admin.proveedoresservicios.show', compact('proveedoresservicios'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Proveedoresservicios $proveedoresservicios, $id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);
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
        if ($proveedoresservicios->image) {
            $imagenCliente = asset('proveedoresserviciosfotos/' . $proveedoresservicios->image);
        }
        $usuarionombre = auth()->user()->name;

        return view('admin.proveedoresservicios.edit', compact('usuarionombre', 'proveedoresservicios', 'sucursal', 'estado', 'ciudadexp', 'bancos', 'imagenCliente'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProveedoresserviciosRequest $request, Proveedoresservicios $proveedoresservicios, $id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);
        $data = $request->validated();
        if($request->hasFile('picture')){
            $file = $request->file('picture');
            $image_name=time().'_'.$file->getClientOriginalName();
            $file->move(public_path("/proveedoresserviciosfotos"),$image_name);
            $data['image'] = $image_name;
        }
        
        $proveedoresservicios->update($data);

        return redirect()->route('admin.proveedoresservicios.show', $proveedoresservicios)->with('info', 'El perfil se actualizó con éxito');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Proveedoresservicios $proveedoresservicios)
    {
        $proveedoresservicios->delete();

        return redirect()->route('admin.proveedoresservicios.index', $proveedoresservicios)->with('eliminar', 'ok');
    }

    public function listasecciones(Request $request)
{
    // Obtener todas las secciones
    $listasecciones = Seccionprovservicio::get();

    // Obtener las subsecciones para cada sección, agrupadas por seccionid
    $subsecciones = Subseccionprovservicio::all()->groupBy('seccionid');

    // Pasar las secciones y las subsecciones a la vista
    return view('admin.proveedoresservicios.listasecciones', compact('listasecciones', 'subsecciones'));
}

    public function mostrarSubsecciones(Request $request, $seccionId) 
{
    // Obtener las subsecciones para la sección seleccionada
    $subsecciones = Subseccionprovservicio::where('seccionid', $seccionId)->get();
    $nombreSeccion = Seccionprovservicio::find($seccionId)->nombreseccion;
    
    // Pasar las subsecciones y el nombre de la sección a la vista
    return view('admin.proveedoresservicios.listasecciones', compact('subsecciones', 'nombreSeccion'));
}

    public function getSubsecciones($seccionId)
    {
        $subsecciones = Subseccionprovservicio::where('seccionid', $seccionId)->get();
        return response()->json($subsecciones);
    }
    
    public function actualizarSeccion(Request $request)
    {
        $seccion = Seccionprovservicio::find($request->id);
        if ($seccion) {
            $seccion->nombreseccion = $request->nombreseccion;
            $seccion->estado = $request->estado;
            $seccion->save();
        }
        
        return redirect()->route('admin.proveedoresservicios.listasecciones')->with('info', 'Sección actualizada con éxito');
    }

    public function crearSubseccion(Request $request)
    {
        $validated = $request->validate([
            'subseccion' => '',
            'seccionid' => '',
            'seccionnombre' => '',
            'usuarioregistroid' => '',
            'usuarioregistronombre' => '',
        ]);

        $subseccion = new Subseccionprovservicio();
            $subseccion->subseccion = $request->subseccion;
            $subseccion->seccionid = $request->seccionid;
            $subseccion->seccionnombre = $request->seccionnombre;
            $subseccion->usuarioregistroid = $request->usuarioregistroid;
            $subseccion->usuarioregistronombre = $request->usuarioregistronombre;
        $subseccion->save();

        return redirect()->route('admin.proveedoresservicios.listasecciones')->with('info', 'Sub Sección agregada con éxito');
    }

}
