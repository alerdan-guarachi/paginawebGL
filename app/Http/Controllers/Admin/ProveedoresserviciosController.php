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
use App\Models\PortafolioProveedores;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\StoreProveedoresserviciosRequest;
use App\Http\Requests\UpdateProveedoresserviciosRequest;
use App\Models\CuentasPagar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\ProveedoresDocumentos;
use App\Models\PersonalViajes;
use App\Models\PersonalViajesItinerario;
use App\Models\PersonalViajesCronograma;
use App\Models\PersonalVacaciones;
use App\Models\Departamento;
use App\Models\PlanesServiciosProv;
use App\Notifications\VacacionNotificacion;
use App\Notifications\RespuestaVacacionNotificacion;
use Illuminate\Support\Facades\DB;

class ProveedoresserviciosController extends Controller
{
    /* public function __construct() { 
        $this->middleware('can:admin.proveedoresservicios.index')->only('index');
    } */
    public function index(Request $request) 
    {
        $razonsocial = $request->get('buscarpor');
        $usuarioAutenticadoNombre = Auth::user()->name;

        $soloproveedorservicio = Proveedoresservicios::where('razonsocial', $usuarioAutenticadoNombre)->get();

        $todosproveedoresservicios = Proveedoresservicios::where('razonsocial', 'Like', "%$razonsocial%")
                                    ->simplePaginate(1000);

        $inventarios = PortafolioProveedores::whereIn('proveedorid', $todosproveedoresservicios->pluck('id'))->orderBy('nombreproducto', 'asc')->get();
        $bateriaproveedorservicios = Bateriaproveedorservicios::whereIn('proveedorid', $todosproveedoresservicios->pluck('id'))->get();

        return view('admin.proveedoresservicios.index', compact('todosproveedoresservicios', 'soloproveedorservicio', 'inventarios', 'bateriaproveedorservicios'));
    }
    public function listaproveedoresservicios(Request $request) 
    {
        $razonsocial = $request->get('buscarpor');
        $usuarioAutenticadoNombre = Auth::user()->name;

        $soloproveedorservicio = Proveedoresservicios::where('razonsocial', $usuarioAutenticadoNombre)->get();

        $todosproveedoresservicios = Proveedoresservicios::with('planesServicios')
                                    ->where('razonsocial', 'Like', "%$razonsocial%")
                                    ->orderBy('razonsocial')
                                    ->simplePaginate(1000);

        $proveedores = Proveedor::where('proveedor', 'LIKE', "%$razonsocial%")
                         ->where('id', '!=', 56)
                         ->orderBy('proveedor')
                         ->simplePaginate(1000);

        $inventarios = PortafolioProveedores::whereIn('proveedorid', $todosproveedoresservicios->pluck('id'))->orderBy('nombreproducto', 'asc')->get();
        $bateriaproveedorservicios = Bateriaproveedorservicios::whereIn('proveedorid', $todosproveedoresservicios->pluck('id'))->get();

        return view('admin.proveedoresservicios.listaproveedoresservicios', compact('todosproveedoresservicios', 'soloproveedorservicio', 'inventarios', 'bateriaproveedorservicios', 'proveedores'));
    }
    public function guardarnuevoproducto(Request $request)  
    {
        $nuevoID = null;

        if ($request->tipo_inventario == 'ALMACEN') {
            $prefijo = null;
            if ($request->seccion == 'ESCRITORIO') {
                $prefijo = 'ESAL-';
            } elseif ($request->seccion == 'COCINA') {
                $prefijo = 'COAL-';
            } elseif ($request->seccion == 'ALIMENTOS Y BEBIDAS') {
                $prefijo = 'ABAL-';
            } elseif ($request->seccion == 'CONTRUCCION Y FERRETERIA') {
                $prefijo = 'CFAL-';
            } elseif ($request->seccion == 'LIMPIEZA') {
                $prefijo = 'LIAL-';
            } elseif ($request->seccion == 'PLASTICO') {
                $prefijo = 'PLAL-';
            } elseif ($request->seccion == 'PROMOCIONAL') {
                $prefijo = 'PRAL-';
            } elseif ($request->seccion == 'USO MEDICO') {
                $prefijo = 'UMAL-';
            } elseif ($request->seccion == 'INSUMOS DECORATIVOS') {
                $prefijo = 'IDAL-';
            } else {
                $prefijo = 'OTAL-';
            }
            
        } elseif ($request->tipo_inventario == 'ACTIVO FIJO') {
            $prefijo = null;
            if ($request->seccion == 'ALMACEN') {
                $prefijo = 'ALAF-';
            } elseif (in_array($request->seccion, ['BAÑO 1', 'BAÑO 2', 'BAÑO GERENCIAL', 'BAÑO PLANTA ALTA'])) {
                $prefijo = 'BAAF-';
            } elseif ($request->seccion == 'COCINA') {
                $prefijo = 'COAF-';
            } elseif (in_array($request->seccion, ['CONSULTORIO 1', 'CONSULTORIO 2', 'CONSULTORIO 3'])) {
                $prefijo = 'CNAF-';
            } elseif (in_array($request->seccion, ['GERENCIA COMERCIAL Y FINANCIERA', 'GERENCIA GENERAL', 'GERENCIA FINANCIERA'])) {
                $prefijo = 'GEAF-';
            } elseif (in_array($request->seccion, ['LAVANDERIA', 'GRADAS','PASILLO PLANTA ALTA','PASILLO PLANTA BAJA','DEPOSITO SECUNDARIO','DEPOSITO PRINCIPAL'])) {
                $prefijo = 'LAAF-';
            } elseif (in_array($request->seccion, ['OFICINA 1', 'OFICINA 2', 'ADMINISTRACION', 'AUDIOMETRIA', 'CAJA', 'ELECTROCARDIOGRAMA', 'ERGOMETRIA', 'ESPIROMETRIA', 'FISIOTERAPIA-MEDICINA LABORAL', 'LABORATORIO', 'OFICINA ADMINISTRATIVA', 'OFTALMOLOGIA', 'PRESTACIONES 1', 'PRESTACIONES 2', 'PROGRAMACION', 'PSICOLOGIA', 'SISTEMAS','OFICINA 1 PLANTA ALTA','OFICINA 2 PLANTA BAJA','OFICINA 3 PLANTA BAJA','CONSULTORIO 1 PLANTA ALTA','CONSULTORIO 2 PLANTA ALTA','CONSULTORIO 3 PLANTA ALTA','CONSULTORIO 4 PLANTA ALTA','CONSULTORIO 5 PLANTA ALTA','CONSULTORIO 6 PLANTA BAJA','CONSULTORIO 7 PLANTA BAJA','CONSULTORIO 8 PLANTA BAJA','CONSULTORIO 9 PLANTA BAJA'])) {
                $prefijo = 'OFAF-';
            } elseif (in_array($request->seccion, ['SALA DE ESPERA PLANTA BAJA', 'SALA 1', 'SALA DE ESPERA', 'SALA DE REUNIONES', 'ZONA DE MONITOREO','VISTA FRONTAL','ENTRADA PRINCIPAL','SALA DE ATENCION AL CLIENTE'])) {
                $prefijo = 'SEAF-';
            } else {
                $prefijo = 'OTAF-';
            }
            
        }
        if ($prefijo) {
            $ultimoRegistro = PortafolioProveedores::where('tipoinventario', $request->tipo_inventario)
                                                    ->where('seccion', $request->seccion)
                                                    ->orderBy('created_at', 'desc')
                                                    ->first();
            if ($ultimoRegistro) {
                $ultimoNumero = (int) substr($ultimoRegistro->id, strlen($prefijo));
                $nuevoNumero = $ultimoNumero + 1;
            } else {
                $nuevoNumero = 1;
            }
            $nuevoID = $prefijo . $nuevoNumero;
            $request->merge(['id' => $nuevoID]);
        }
        if (!$nuevoID) {
            $nuevoID = 'DEFAULT-ID';
        }
        $usuarioAutenticado = Auth::user();

        PortafolioProveedores::create([
            'id' => $nuevoID,
            'proveedorid' => $request->proveedorid,
            'proveedornombre' => $request->proveedornombre,
            'tipoinventario' => $request->tipo_inventario,
            'seccion' => $request->seccion,
            'nombreproducto' => $request->nombreproducto,
            'especificacionmedida' => $request->especificacionmedida,
            'color' => $request->color,
            'presentacion' => $request->presentacion,
            'unidades' => $request->unidades,
            'cantidad' => $request->cantidad,
            'precio' => $request->precio,
            'preciounitario' => $request->preciounitario,
            'ciudad' => $request->ciudad,
            'materiaprima' => $request->materia_prima,
            'marca' => $request->marca,
            'unidadmedida' => $request->unidad_medida,
            'modelo' => $request->modelo,
            'usuarioregistroid' => $usuarioAutenticado->id,
            'usuarioregistronombre' => $usuarioAutenticado->name,
        ]);

        return redirect()->route('admin.proveedoresservicios.listaproveedoresservicios')->with('info', 'El producto se creó con éxito');
    }
    
    public function destroy(Proveedoresservicios $proveedoresservicios)
    {
        $proveedoresservicios->delete();

        return redirect()->route('admin.proveedoresservicios.index', $proveedoresservicios)->with('eliminar', 'ok');
    }

//SECCIONES
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
//

//PROVEEDORES GENERALES
    public function create()
    {
        $sucursal = [
            'SANTA CRUZ' => 'SANTA CRUZ',
            'COCHABAMBA' => 'COCHABAMBA',
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
    public function store(StoreProveedoresserviciosRequest $request)
    {
        $usuarioAutenticado = auth()->user()->id;
        $usuarioAutenticadonombre = auth()->user()->name;

        $ultimoRegistro = Proveedoresservicios::orderBy('created_at', 'desc')->first();
        $ultimoId = $ultimoRegistro ? (int) filter_var($ultimoRegistro->id, FILTER_SANITIZE_NUMBER_INT) : 0;
        $nuevoId = ($ultimoId + 1) . 'PS';

        $rutaBase = "proveedoresservicios/$nuevoId";
        $image_name = null;
        $image_name2 = null;
        if ($request->hasFile('documentorespaldo')) {
            $file = $request->file('documentorespaldo');
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($rutaBase), $image_name);
        }
        if ($request->hasFile('imagenqr')) {
            $file = $request->file('imagenqr');
            $image_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($rutaBase), $image_name2);
        }
        $proveedorData = $request->all();
        $proveedorData['id'] = $nuevoId;
        $proveedorData['usuarioregistroid'] = $usuarioAutenticado;
        $proveedorData['usuarioregistronombre'] = $usuarioAutenticadonombre;
        $proveedorData['documentorespaldo'] = $image_name;
        $proveedorData['imagenqr'] = $image_name2;

        $proveedoresservicios = Proveedoresservicios::create($proveedorData);

        return redirect()->route('admin.proveedoresservicios.listaproveedoresservicios')
            ->with('info', 'El perfil se creó con éxito');
    }
    public function show($id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);

        if (!$proveedoresservicios) {
            abort(404, 'Registro no encontrado.');
        }

        return view('admin.proveedoresservicios.show', compact('proveedoresservicios'));
    }
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
    public function update(UpdateProveedoresserviciosRequest $request, $id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);
        $data = $request->validated();
        $proveedoresservicios->update($data);

        return redirect()->route('admin.proveedoresservicios.show', $proveedoresservicios)->with('info', 'El proveedor se actualizó con éxito');
    }
    public function inactivarProducto($id)
    {
        $producto = PortafolioProveedores::where('id', $id)->first();

        if (!$producto) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
        }

        $producto->estado = 'INACTIVO';
        $producto->save();

        return response()->json(['success' => true, 'message' => 'Producto inactivado correctamente']);
    }
//

//PROVEEDORES EXTERNOS
    public function crearprovpersonalexterno()
    {
        $sucursal = [
            'SANTA CRUZ' => 'SANTA CRUZ',
            'COCHABAMBA' => 'COCHABAMBA',
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
        return view('admin.proveedoresservicios.crearprovpersonalexterno', compact('usuarionombre','sucursal', 'estado', 'ciudadexp', 'bancos'));
    }
    public function verproveedorexterno($id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);

        if (!$proveedoresservicios) {
            abort(404, 'Registro no encontrado.');
        }

        return view('admin.proveedoresservicios.verproveedorexterno', compact('proveedoresservicios'));
    }
    public function editarproveedorexterno($id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);
        $sucursal = [
            'SANTA CRUZ' => 'SANTA CRUZ',
            'COCHABAMBA' => 'COCHABAMBA',
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

        return view('admin.proveedoresservicios.editarproveedorexterno', compact('proveedoresservicios','usuarionombre','sucursal', 'estado', 'ciudadexp', 'bancos'));
    }
    public function actualizarproveedorexterno(UpdateProveedoresserviciosRequest $request, $id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);
        $data = $request->validated();
        $proveedoresservicios->update($data);

        return redirect()->route('admin.proveedoresservicios.verproveedorexterno', $proveedoresservicios)->with('info', 'El proveedor se actualizó con éxito');
    }
    public function pasarAPersonal($id) 
    {
        $proveedor = ProveedoresServicios::find($id);
    
        if (!$proveedor) {
            return response()->json(['error' => 'Proveedor no encontrado'], 404);
        }
    
        $proveedor->categoria = 'PERSONAL';
        $proveedor->save();
    
        return response()->json(['success' => true, 'id' => $proveedor->id]);
    }
//

//PROVEEDORES SERVICIOS BASICOS
    public function crearprovserviciobasico()
    {
        $sucursal = [
            'SANTA CRUZ' => 'SANTA CRUZ',
            'COCHABAMBA' => 'COCHABAMBA',
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
    public function guardarproveedor(StoreProveedoresserviciosRequest $request)
    {
        $usuarioAutenticado = auth()->user()->id;
        $usuarioAutenticadonombre = auth()->user()->name;

        $ultimoRegistro = Proveedoresservicios::orderBy('created_at', 'desc')->first();
        $ultimoId = $ultimoRegistro ? (int) filter_var($ultimoRegistro->id, FILTER_SANITIZE_NUMBER_INT) : 0;
        $nuevoId = ($ultimoId + 1) . 'PS';

        $rutaBase = "proveedoresservicios/$nuevoId";

        $image_name = null;
        $image_name2 = null;

        if ($request->hasFile('documentorespaldo')) {
            $file = $request->file('documentorespaldo');
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($rutaBase), $image_name);
        }
        if ($request->hasFile('imagenqr')) {
            $file = $request->file('imagenqr');
            $image_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($rutaBase), $image_name2);
        }
        $proveedorData = $request->all();
        $proveedorData['id'] = $nuevoId;
        $proveedorData['usuarioregistroid'] = $usuarioAutenticado;
        $proveedorData['usuarioregistronombre'] = $usuarioAutenticadonombre;
        $proveedorData['documentorespaldo'] = $image_name;
        $proveedorData['imagenqr'] = $image_name2;

        $proveedoresservicios = Proveedoresservicios::create($proveedorData);

        return redirect()->route('admin.proveedoresservicios.listaproveedoresservicios')->with('info', 'El proveedor se creó con éxito');
    }
    public function verproveedorserviciobasico($id)
    {
        $proveedoresservicios = Proveedoresservicios::with('planesServicios')->findOrFail($id);
        $planes = $proveedoresservicios->planesServicios;

        $mostrarColumnas = [
            'codigo'   => $planes->pluck('codigo')->filter(fn($v) => $v != 0 && $v !== '0')->isNotEmpty(),
            'contrato' => $planes->pluck('contrato')->filter(fn($v) => $v != 0 && $v !== '0')->isNotEmpty(),
            'linea'    => $planes->pluck('linea')->filter(fn($v) => $v != 0 && $v !== '0')->isNotEmpty(),
            'cuenta'   => $planes->pluck('cuenta')->filter(fn($v) => $v != 0 && $v !== '0')->isNotEmpty(),
            'servicio' => $planes->pluck('servicio')->filter(fn($v) => $v != 0 && $v !== '0')->isNotEmpty(),
        ];

        return view('admin.proveedoresservicios.verproveedorserviciobasico', compact('proveedoresservicios', 'planes', 'mostrarColumnas'));
    }
    public function editarproveedorserviciobasico($id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);
        $sucursal = [
            'SANTA CRUZ' => 'SANTA CRUZ',
            'COCHABAMBA' => 'COCHABAMBA',
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

        return view('admin.proveedoresservicios.editarproveedorserviciobasico', compact('proveedoresservicios','usuarionombre','sucursal', 'estado', 'ciudadexp', 'bancos'));
    }
    public function actualizarproveedorserviciobasico(UpdateProveedoresserviciosRequest $request, $id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);
        $data = $request->validated();
        $proveedoresservicios->update($data);

        return redirect()->route('admin.proveedoresservicios.verproveedorserviciobasico', $proveedoresservicios)->with('info', 'El proveedor se actualizó con éxito');
    }
    public function guardarnuevoplan(Request $request)  
    {
        $usuarioAutenticado = Auth::user();

        $ultimoPlan = PlanesServiciosProv::where('proveedorid', $request->proveedor_id)
        ->max('plan');
        $siguientePlan = $ultimoPlan ? $ultimoPlan + 1 : 1;

        PlanesServiciosProv::create([
            'plan' => $siguientePlan,
            'proveedorid' => $request->proveedor_id,
            'razonsocial' => $request->razon_social,
            'sigla' => $request->sigla,
            'codigo' => $request->codigo,
            'contrato' => $request->contrato,
            'linea' => $request->linea,
            'cuenta' => $request->cuenta,
            'servicio' => $request->servicio,
            'motivo' => $request->motivo,
            'ciudad' => $request->ciudad,
            'montofijo' => $request->montofijo,
            'estado' => 'ACTIVO',
            'usuarioregistroid' => $usuarioAutenticado->id,
            'usuarioregistronombre' => $usuarioAutenticado->name,
        ]);

        return redirect()->route('admin.proveedoresservicios.listaproveedoresservicios')->with('info', 'El plan se creó con éxito');
    }
    public function planesinactivar($id)
    {
        $plan = PlanesServiciosProv::findOrFail($id);
        $plan->estado = 'INACTIVO';
        $plan->save();

        return response()->json(['success' => true]);
    }
//

//PROVEEDORES PERSONAL
    public function listapersonal(Request $request) 
    {
        $razonsocial = $request->get('buscarpor');

        $personales = Proveedoresservicios::where('razonsocial', 'LIKE', "%$razonsocial%")
                          ->orderBy('id')
                          ->simplePaginate(1000);

        return view('admin.proveedoresservicios.listapersonal', compact('personales'));
    }
    public function verpersonal($id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);

        if (!$proveedoresservicios) {
            abort(404, 'Registro no encontrado.');
        }

        $proveedordocumentos = ProveedoresDocumentos::where('proveedorid', $id)
                          ->orderBy('created_at', 'asc')
                          ->simplePaginate(1000);

        return view('admin.proveedoresservicios.verpersonal', compact('proveedoresservicios','id','proveedordocumentos'));
    }
    public function editarpersonal($id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);
        $sucursal = [
            'SANTA CRUZ' => 'SANTA CRUZ',
            'COCHABAMBA' => 'COCHABAMBA',
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

        return view('admin.proveedoresservicios.editarpersonal', compact('proveedoresservicios','usuarionombre','sucursal', 'estado', 'ciudadexp', 'bancos'));
    }
    public function actualizarpersonal(UpdateProveedoresserviciosRequest $request, $id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);
        $data = $request->validated();
        $proveedoresservicios->update($data);

        return redirect()->route('admin.proveedoresservicios.verpersonal', $proveedoresservicios)->with('info', 'El proveedor se actualizó con éxito');
    }
    public function guardardocumentacionproveedor(Request $request, $id)
    {
        $archivo_name = null;
        if ($request->hasFile('documento')) {
            $file = $request->file('documento');
            $carpetaCliente = public_path("/proveedoresdocumentos/$id");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }

        $usuarioAutenticadoid = Auth::user()->id;
        $usuarioAutenticadonombre = Auth::user()->name;
        $tipodocumento = $request->input('tipoDocumento');
        $nombredocumento = $request->input('nombreDocumento') ?? $request->input('nombreDocumentoTexto');
        $proveedornombre = $request->input('proveedornombre');
        $proveedorid = $request->input('proveedorid');

            ProveedoresDocumentos::create([
                'usuarioregistroid' => $usuarioAutenticadoid,
                'usuarioregistronombre' => $usuarioAutenticadonombre,
                'tipodocumento' => $tipodocumento,
                'nombredocumento' => $nombredocumento,
                'proveedorid' => $proveedorid,
                'proveedornombre' => $proveedornombre,
                'documento' => $archivo_name,
            ]);

        return redirect()->route('admin.proveedoresservicios.verpersonal', $id)
            ->with('info', 'El documento se creó con éxito');
    }
    public function viajespersonal($id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);

        $personalviajes = PersonalViajes::where('proveedorid', $id)
                          ->orderBy('created_at', 'asc')
                          ->with('itinerario', 'cronograma')
                          ->simplePaginate(1000);

        return view('admin.proveedoresservicios.viajespersonal', compact('proveedoresservicios','id','personalviajes'));
    }
    public function guardarviajespersonal(Request $request, $id)
    {
        $proveedorid = $request->input('proveedorid');
        $proveedornombre = $request->input('proveedornombre');
        $destino = $request->input('destino');
        $motivoviaje = $request->input('motivo_viaje');
        $fechainicio = $request->input('fecha_salida');
        $fechafinal = $request->input('fecha_retorno');
        $cantidaddias = $request->input('cantidad_dias');
        $mediotransporte = $request->input('medio_transporte');
        $hospedaje = $request->input('hospedaje_requerido');
        $montosolicitado = $request->input('monto_solicitado');
        $detallemotivo = $request->input('motivoviaje');
        $usuarioAutenticadoid = Auth::user()->id;
        $usuarioAutenticadonombre = Auth::user()->name;

        $solicitudvacacion = PersonalViajes::create([
            'proveedorid' => $proveedorid,
            'proveedornombre' => $proveedornombre,
            'destino' => $destino,
            'motivoviaje' => $motivoviaje,
            'fechasalida' => $fechainicio,
            'fecharetorno' => $fechafinal,
            'cantidaddias' => $cantidaddias,
            'mediotransporte' => $mediotransporte,
            'requierehospedaje' => $hospedaje,
            'montosolicitado' => $montosolicitado,
            'observaciones' => $detallemotivo,
            'estado' => 'PENDIENTE',
            'usuarioregistroid' => $usuarioAutenticadoid,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
        ]);

        return redirect()->route('admin.proveedoresservicios.viajespersonal', $id)
            ->with('info', 'La solicitud se creó con éxito');
    }
    public function guardarviajespersonaldetallado(Request $request)
    {
        // Guarda primero el viaje principal (si aplica) o recibe el ID
        $viaje_id = $request->input('viajeid'); // puede venir de hidden input
        $usuarioAutenticadoid = Auth::user()->id;
        $usuarioAutenticadonombre = Auth::user()->name;
        // GUARDAR ITINERARIO
        $itinerario = new PersonalViajesItinerario();
        $itinerario->viajeid = $viaje_id;
        $itinerario->transporte = $request->input('transporte');
        $itinerario->numerovuelo = $request->input('numero_vuelo');
        $itinerario->fechahorasalida = $request->input('fecha_salida');
        $itinerario->fechahorallegada = $request->input('fecha_llegada');
        $itinerario->nombrehotel = $request->input('hotel');
        $itinerario->direccionhotel = $request->input('direccion_hotel');
        $itinerario->ingresohotel = $request->input('ingresohotel');
        $itinerario->salidahotel = $request->input('salidahotel');
        $itinerario->montotransporte = $request->input('gasto_transporte');
        $itinerario->montoalimentacion = $request->input('gasto_alimentacion');
        $itinerario->montootrosgastos = $request->input('otros_gastos');
        $itinerario->montototal = $request->input('monto_total');
        $itinerario->usuarioregistroid = $usuarioAutenticadoid;
        $itinerario->usuarioregistronombre = $usuarioAutenticadonombre;

        $boleto_transporte = null;
        if ($request->hasFile('boleto_transporte')) {
            $file = $request->file('boleto_transporte');
            $carpetaCliente = public_path("/personal/viajes/$usuarioAutenticadoid");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $boleto_transporte = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $boleto_transporte);
        }
        $itinerario->boletotransporte = $boleto_transporte;

        $reserva_hotel = null;
        if ($request->hasFile('reserva_hotel')) {
            $file = $request->file('reserva_hotel');
            $carpetaCliente = public_path("/personal/viajes/$usuarioAutenticadoid");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $reserva_hotel = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $reserva_hotel);
        }
        $itinerario->reservahotel = $reserva_hotel;

        $itinerario->save();

        $fechas = $request->input('fecha_actividad');
        $ubicaciones = $request->input('ubicacion_actividad');
        $descripciones = $request->input('descripcion_actividad');


        foreach ($fechas as $i => $fecha) {
            if ($fecha || $ubicaciones[$i] || $descripciones[$i]) {
                $cronograma = new PersonalViajesCronograma();
                $cronograma->viajeid = $viaje_id;
                $cronograma->nroactividad = $i + 1;
                $cronograma->fechahoraactividad = $fecha;
                $cronograma->ubicacionactividad = $ubicaciones[$i];
                $cronograma->descripcionactividad = $descripciones[$i];
                $cronograma->usuarioregistroid = $usuarioAutenticadoid;
                $cronograma->usuarioregistronombre = $usuarioAutenticadonombre;
                $cronograma->estado = 'PENDIENTE';
                $cronograma->save();
            }
        }

        $viaje = PersonalViajes::find($viaje_id);
        if ($viaje) {
            $viaje->estado = 'PROGRAMADO';
            $viaje->save();
        }

        return redirect()->back()->with('info', 'Programación de viaje registrado correctamente.');
    }
    public function guardarrendicionviajespersonal(Request $request)
    {
        $viaje_id = $request->input('viajeid');
        $usuarioAutenticadoid = Auth::user()->id;
        $usuarioAutenticadonombre = Auth::user()->name;

        $itinerario = PersonalViajesItinerario::where('viajeid', $viaje_id)->first();

        if (!$itinerario) {
            return redirect()->back()->with('error', 'No se encontró el itinerario asociado al viaje.');
        }

        $itinerario->rendicionmontotransporte = $request->input('gasto_transporte_real');
        $itinerario->rendicionmontoalimentacion = $request->input('gasto_alimentacion_real');
        $itinerario->rendicionmontootrosgastos = $request->input('otros_gastos_real');
        $itinerario->rendicionmontototal = $request->input('total-real');
        $diferencia = $request->input('diferencia_monto_real');
        $itinerario->rendicionmontodiferencia = $diferencia;
        $itinerario->usuarioregistroid = $usuarioAutenticadoid;
        $itinerario->usuarioregistronombre = $usuarioAutenticadonombre;

        if ($request->hasFile('comprobante')) {
            $file = $request->file('comprobante');
            $carpetaCliente = public_path("/personal/viajes/$usuarioAutenticadoid");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $comprobante = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $comprobante);
            $itinerario->rendicioncomprobante = $comprobante;
        }

            $ultimaOrden = CuentasPagar::where('tipoorden', 'ORDEN DE TRABAJO')
                ->orderBy('created_at', 'desc')
                ->first();
            $nuevoId = $ultimaOrden ? ((int) filter_var($ultimaOrden->id, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
            $nuevoIdConSufijo = $nuevoId . 'CP';

        if (is_numeric($diferencia)) {
            $diferencia = floatval($diferencia);
            if ($diferencia > 0) {
                DB::table('cuentasporpagar')->insert([
                    'id' => $nuevoIdConSufijo,
                    'usuarioregistroid' => $usuarioAutenticadoid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $itinerario->rendicionresultado = 'CUENTA POR PAGAR';
            } elseif ($diferencia < 0) {
                DB::table('cuentasporpagar')->insert([
                    'id' => $nuevoIdConSufijo,
                    'usuarioregistroid' => $usuarioAutenticadoid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $itinerario->rendicionresultado = 'CUENTA POR COBRAR';
            } else {
                $itinerario->rendicionresultado = 'SIN DIFERENCIA';
            }
        }

        $itinerario->save();
        $actividadesSeleccionadas = $request->input('actividades_seleccionadas', []);
        $todasLasActividades = PersonalViajesCronograma::where('viajeid', $viaje_id)->get();

        foreach ($todasLasActividades as $actividad) {
            if (in_array($actividad->id, $actividadesSeleccionadas)) {
                $actividad->estado = 'FINALIZADO';
            } else {
                $actividad->estado = 'INCUMPLIDO';
            }
            $actividad->save();
        }

        $viaje = PersonalViajes::find($viaje_id);
        if ($viaje) {
            $viaje->estado = 'FINALIZADO';
            $viaje->save();
        }


        return redirect()->back()->with('info', 'Rendición de viaje actualizada correctamente.');
    }

    public function aprobarsolicitudviaje($id, Request $request)
    {
        try {
            $viaje = PersonalViajes::find($id);

            if (!$viaje) {
                return response()->json(['success' => false, 'message' => 'Solicitud no encontrada.']);
            }

            $viaje->estado = 'APROBADO';
            $viaje->usuarioautorizacion = Auth::user()->name;
            $viaje->save();

            return response()->json(['success' => true, 'message' => 'Solicitud aprobada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }
    public function rechazarsolicitudviaje($id, Request $request)
    {
        try {
            $viaje = PersonalViajes::find($id);

            if (!$viaje) {
                return response()->json(['success' => false, 'message' => 'Solicitud no encontrada.']);
            }

            $viaje->estado = 'RECHAZADO';
            $viaje->usuarioautorizacion = Auth::user()->name;
            $viaje->motivorechazo = $request->input('motivorechazo');
            $viaje->save();

            return response()->json(['success' => true, 'message' => 'Solicitud rechazada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }
    public function vacacionespersonal($id)
    {
        $proveedoresservicios = Proveedoresservicios::find($id);

        $personalvacaciones = PersonalVacaciones::where('proveedorid', $id)
                          ->orderBy('created_at', 'asc')
                          ->simplePaginate(1000);

        return view('admin.proveedoresservicios.vacacionespersonal', compact('proveedoresservicios','id','personalvacaciones'));
    }
    public function guardarvacacionespersonal(Request $request, $id)
    {
        $proveedorid = $request->input('proveedorid');
        $proveedornombre = $request->input('proveedornombre');
        $fechainicio = $request->input('fecha_salida');
        $fechafinal = $request->input('fecha_retorno');
        $cantidaddias = $request->input('cantidad_dias');
        $detallemotivo = $request->input('motivo');
        $usuarioAutenticadoid = Auth::user()->id;
        $usuarioAutenticadonombre = Auth::user()->name;

        $solicitudvacacion = PersonalVacaciones::create([
            'proveedorid' => $proveedorid,
            'proveedornombre' => $proveedornombre,
            'fechainicial' => $fechainicio,
            'fechafinal' => $fechafinal,
            'cantidaddias' => $cantidaddias,
            'motivo' => $detallemotivo,
            'estado' => 'PENDIENTE',
            'usuarioregistroid' => $usuarioAutenticadoid,
            'usuarioregistronombre' => $usuarioAutenticadonombre,
        ]);

        if ($solicitudvacacion) {
            $usuariosNotificar = User::role(['MAESTRO', 'ADMINISTRADOR'])->get();

            foreach ($usuariosNotificar as $usuario) {
                $usuario->notify(new VacacionNotificacion($solicitudvacacion));
            }
        }


        return redirect()->route('admin.proveedoresservicios.vacacionespersonal', $id)
            ->with('info', 'La solicitud se creó con éxito');
    }
    public function aprobarsolicitudvacacion($id, Request $request)
    {
        try {
            $vacacion = PersonalVacaciones::find($id);

            if (!$vacacion) {
                return response()->json(['success' => false, 'message' => 'Solicitud no encontrada.']);
            }

            $vacacion->estado = 'APROBADO';
            $vacacion->usuarioautorizacion = Auth::user()->name;
            $vacacion->save();

            $usuariosNotificar = User::where('name', $vacacion->proveedornombre)->get();
            foreach ($usuariosNotificar as $usuarioDestino) {
                $usuarioDestino->notify(new RespuestaVacacionNotificacion($vacacion));
            }

            return response()->json(['success' => true, 'message' => 'Solicitud aprobada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }
    public function rechazarsolicitudvacacion($id, Request $request)
    {
        try {
            $vacacion = PersonalVacaciones::find($id);

            if (!$vacacion) {
                return response()->json(['success' => false, 'message' => 'Solicitud no encontrada.']);
            }

            $vacacion->estado = 'RECHAZADO';
            $vacacion->usuarioautorizacion = Auth::user()->name;
            $vacacion->motivorechazo = $request->input('motivorechazo');
            $vacacion->save();

            $usuariosNotificar = User::where('name', $vacacion->proveedornombre)->get();
            foreach ($usuariosNotificar as $usuarioDestino) {
                $usuarioDestino->notify(new RespuestaVacacionNotificacion($vacacion));
            }

            return response()->json(['success' => true, 'message' => 'Solicitud rechazada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }


//
}
