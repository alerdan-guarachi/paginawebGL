<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Cliente;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\StoreClienteBancoRequest;
use App\Http\Requests\StoreClienteAuditoriaRequest;
use App\Http\Requests\StoreBateriasubclienteRequest;
use App\Http\Requests\StoreProgramacionsubclienteRequest;
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
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\View;
use App\Models\Pregunta;
use App\Models\Formulario;
use App\Models\ClienteBanco;
use App\Models\ClienteAuditoria;
use App\Models\Area;
use App\Models\Areaaccion;
use App\Models\Bancosubcliente;
use App\Models\Bateriasubcliente;
use App\Models\Proveedor;
use App\Models\Programacionsubcliente;

class ClienteAuditoriaController extends Controller
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
    $clienteauditorias = collect();
    return view('admin.clientesauditorias.index', compact('clienteauditorias'));
}

public function buscar(Request $request)
{
    $busqueda = $request->get('buscarpor');
    $clienteauditorias = ClienteAuditoria::where(function ($query) use ($busqueda) {
        $query->where('nombrecompleto', 'like', "%$busqueda%")
              /* ->orWhere('ciudad', 'like', "%$busqueda%") */
              ->orWhere('ci', 'like', "%$busqueda%");
    })->simplePaginate(1000);
    return view('admin.clientesbancos.index', compact('clienteauditorias'));
}

public function mostrarTodos2()
{
    $clienteauditorias = ClienteAuditoria::paginate(1000);
    return view('admin.clientesauditorias.index', compact('clienteauditorias'));
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
{
    $genero = [
        'MASCULINO' => 'MASCULINO',
        'FEMENINO' => 'FEMENINO',
    ];
    $estciv = [
        'SOLTER@' => 'SOLTER@',
        'CASAD@' => 'CASAD@',
        'UNION LIBRE' => 'UNION LIBRE',
        'DIVORCIAD@' => 'DIVORCIAD@',
        'VIUD@' => 'VIUD@',
    ];
    $gradoins = [
        'ANALFABETO' => 'ANALFABETO',
        'PRIMARIA' => 'PRIMARIA',
        'SECUNDARIA' => 'SECUNDARIA',
        'TECNICO' => 'TECNICO',
        'UNIVERSITARIO' => 'UNIVERSITARIO',
        'COMPLETO' => 'COMPLETO',
        'INCOMPLETO' => 'INCOMPLETO',
    ];
    $actlab = [
        'ACTIVO' => 'ACTIVO',
        'INACTIVO' => 'INACTIVO',
    ];
    $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
    return view('admin.clientesauditorias.create', compact('genero', 'departamentos', 'estciv', 'gradoins', 'actlab'));
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClienteAuditoriaRequest $request)
    {
        $id = $request->input('lugarnacimiento');
        $lugarnacimiento = Departamento::findOrFail($id);
        $ciudadNombre = $lugarnacimiento->departamento;

        $id = $request->input('lugarresidencia');
        $lugarresidencia = Departamento::findOrFail($id);
        $ciudadNombre = $lugarresidencia->departamento;

        $clienteData = $request->all();
        $clienteData['lugarnacimiento'] = $ciudadNombre;
        $clienteData['lugarresidencia'] = $ciudadNombre;

        $clienteauditoria = ClienteAuditoria::create($clienteData);

        return redirect()->route('admin.clientesauditorias.index', $clienteauditoria)->with('info', 'El cliente se creó con exito');
    }

    public function create2(ClienteBanco $clientebanco)
{
    $areas = Area::pluck('nombrearea', 'id');
    $accionesPorArea = [];
    foreach ($areas as $id => $nombreArea) {
        $accionesPorArea[$id] = AreaAccion::where('areasid', $id)->pluck('accion');
    }
    $estadoproveedor = [
        'ACTIVO' => 'ACTIVO',
        'INACTIVO' => 'INACTIVO',
    ];
    $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
    $id = $clientebanco->nombrecompleto ? ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('id') : null;

    return view('admin.clientesbancos.create2', compact('departamentos', 'estadoproveedor', 'areas', 'accionesPorArea', 'clientebanco', 'id'));
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store2(StoreBateriasubclienteRequest $request, ClienteBanco $clientebanco)
{
    $accionesSeleccionadas = $request->input('accionnombre');
    $areasSeleccionadas = $request->input('areanombre');
    if (!is_array($areasSeleccionadas)) {
        $areasSeleccionadas = [$areasSeleccionadas];
    }
    foreach ($areasSeleccionadas as $areaId) {
        $area = Area::findOrFail($areaId);
        $areaNombre = $area->nombrearea;
        foreach ($accionesSeleccionadas as $accionNombre) {
            $proveedorData = $request->except(['accionnombre', '_token']);
            $proveedorData['accionnombre'] = $accionNombre;
            $proveedorData['areanombre'] = $areaNombre;
            $proveedorData['clientenombre'] = $request->input('nombrecompleto');
            Bateriasubcliente::create($proveedorData);
        }
    }
    return redirect()->route('admin.clientesbancos.index', $clientebanco)->with('info', 'El cliente se creó con éxito');
}
   
public function create3(ClienteBanco $clientebanco)
{
    $nombreCliente = $clientebanco->nombrecompleto;
    $accionesCliente = BateriaSubCliente::where('clientenombre', $nombreCliente)->pluck('accionnombre')->toArray();

    return view('admin.clientesbancos.create3', compact('clientebanco', 'accionesCliente'));
}
/* public function create4(ClienteBanco $clientebanco)
{
    $nombreCliente = $clientebanco->nombrecompleto;
    $accionesCliente = BateriaSubCliente::where('clientenombre', $nombreCliente)->pluck('accionnombre')->toArray();

    // Obtener proveedores para cada acción requerida
    $proveedores = [];
    foreach ($accionesCliente as $accion) {
        $proveedores[$accion] = Proveedor::where('accion', $accion)
            ->pluck('proveedor', 'proveedor')
            ->map(function ($proveedor, $key) use ($accion) {
                $proveedorInfo = Proveedor::where('accion', $accion)
                    ->where('proveedor', $key)
                    ->first(['horarioinicial', 'horariofinal', 'tiempoatencion']);
                $horarioFormateado = $proveedorInfo->horarioinicial . ' - ' . $proveedorInfo->horariofinal;
                $tiempoAtencion = $proveedorInfo->tiempoatencion;
                if ($proveedorInfo->horarioinicial == $proveedorInfo->horariofinal) {
                    $horarioFormateado = $proveedorInfo->horarioinicial;
                }
                return $key . ' (' . $horarioFormateado . ' - ' . $tiempoAtencion . ' - ' . $accion . ')';
            });
    }

    return view('admin.clientesbancos.create4', compact('clientebanco', 'accionesCliente', 'proveedores'));
} */
public function create4(ClienteBanco $clientebanco, Request $request, Proveedor $proveedor)
{
    $nombreCliente = $clientebanco->nombrecompleto;
    $accionesCliente = BateriaSubCliente::where('clientenombre', $nombreCliente)->pluck('accionnombre')->toArray();

    $proveedores = [];
    $accionSeleccionada = $request->input('accion');
    if ($accionSeleccionada) {
        $proveedores = Proveedor::whereIn('accion', function($query) use ($accionSeleccionada) {
            $query->select('accionnombre')->from('bateriasubclientes')->where('accionnombre', $accionSeleccionada);
        })->pluck('proveedor')->toArray();
    }
    return view('admin.clientesbancos.create4', compact('clientebanco', 'accionesCliente', 'proveedores'));
}


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store3(StoreProgramacionsubclienteRequest $request, ClienteBanco $clientebanco)
{
    $proveedoresSeleccionados = $request->input('proveedornombre');
    $horaasignada = $request->input('horaasignada');
    $fechaasignada = $request->input('fechaasignada');
    $accionnombre = $request->input('accionnombre');

    // Verificar si los datos necesarios son arrays
    if (!is_array($proveedoresSeleccionados)) {
        $proveedoresSeleccionados = [$proveedoresSeleccionados];
    }
    foreach ($proveedoresSeleccionados as $proveedor) {
        $proveedorData = $request->except(['proveedornombre', '_token']);
        $proveedorData['proveedornombre'] = $proveedor;
        /* $proveedorData['proveedornombre'] = $request->input('proveedornombre'); */

        $proveedorData['accionnombre'] = $accionnombre;
        $proveedorData['horaasignada'] = $horaasignada;
        $proveedorData['clientenombre'] = $request->input('nombrecompleto');
        $proveedorData['fechaasignada'] = $fechaasignada;

        $clientebanco = Programacionsubcliente::create($proveedorData);
    }

    return redirect()->route('admin.clientesbancos.index', $clientebanco)->with('info', 'Las programaciones del cliente se crearon con éxito');
}




    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
       
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        /* // Obtener el número ingresado por el usuario
$numero = $request->input('periodotiempolaboral');

// Obtener la unidad de tiempo seleccionada por el usuario
$unidad = $request->input('periodo_tipo');

// Concatenar el número y la unidad de tiempo
$periodoTiempo = $numero . $unidad;

// Guardar $periodoTiempo en tu base de datos */

    }
    public function formulario(ClienteAuditoria $clienteauditoria)
    {
        /* $generoCliente = $clienteauditoria->genero; */

        return view('admin.clientesauditorias.formulario', compact('clienteauditoria'/* , 'generoCliente' */));
    }

}
