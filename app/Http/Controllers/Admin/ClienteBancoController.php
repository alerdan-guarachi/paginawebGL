<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Cliente;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\StoreClienteBancoRequest;
use App\Http\Requests\StoreBateriasubclienteRequest;
use App\Http\Requests\StoreDocumentacionsubclienteRequest;
use App\Http\Requests\StoreProgramacionsubclienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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
use App\Models\Area;
use App\Models\Areaaccion;
use App\Models\Bancosubcliente;
use App\Models\Bateriasubcliente;
use App\Models\Bateriaproveedor;
use App\Models\Documentacionsubcliente;
use App\Models\Proveedor;
use App\Models\Programacionsubcliente;
use Microsoft\Graph\Graph;
use Illuminate\Database\Eloquent\Model;
use Microsoft\Graph\Model as GraphModel;
use Google\Client as GoogleClient;
use Google\Service\Drive;

class ClienteBancoController extends Controller
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
        $clientebancos = ClienteBanco::all();
        return view('admin.clientesbancos.index', compact('clientebancos'));
    }

    public function buscar(Request $request)
    {
        $busqueda = $request->get('buscarpor');
        $clientebancos = ClienteBanco::where(function ($query) use ($busqueda) {
            $query->where('nombrecompleto', 'like', "%$busqueda%")
                ->orWhere('ciudad', 'like', "%$busqueda%")
                ->orWhere('ci', 'like', "%$busqueda%");
        })->simplePaginate(1000);
        return view('admin.clientesbancos.index', compact('clientebancos'));
    }

    public function mostrarTodos()
    {
        $clientebancos = ClienteBanco::paginate(1000);
        return view('admin.clientesbancos.index', compact('clientebancos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $genero = [
            'Masculino' => 'Masculino',
            'Femenino' => 'Femenino',
        ];
        $estciv = [
            'Solter@' => 'Solter@',
            'Casad@' => 'Casad@',
            'Union libre' => 'Union libre',
            'Divorciad@' => 'Divorciad@',
            'Viud@' => 'Viud@',
        ];
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
        return view('admin.clientesbancos.create', compact('genero', 'departamentos', 'estciv'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClienteBancoRequest $request)
    {
        $id = $request->input('ciudad');
        $ciudad = Departamento::findOrFail($id);
        $ciudadNombre = $ciudad->departamento;

        $clienteData = $request->all();
        $clienteData['ciudad'] = $ciudadNombre;

        $clientebanco = ClienteBanco::create($clienteData);

        return redirect()->route('admin.clientesbancos.index', $clientebanco)->with('info', 'El cliente se creó con exito');
    }

    public function create2(ClienteBanco $clientebanco)
    {
        $nombreCliente = $clientebanco->nombrecompleto;
        $accionesCliente = BateriaSubCliente::where('clientenombre', $nombreCliente)->pluck('accionnombre')->toArray();

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

        return view('admin.clientesbancos.create2', compact('departamentos', 'estadoproveedor', 'areas', 'accionesPorArea', 'clientebanco', 'id', 'accionesCliente'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store2(StoreBateriasubclienteRequest $request)
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

                $idCliente = $request->input('clienteid');
                $clientebanco = ClienteBanco::findOrFail($idCliente);
                $clienteID = $clientebanco->id;

                $proveedorData['accionnombre'] = $accionNombre;
                $proveedorData['clienteid'] = $clienteID;
                $proveedorData['areanombre'] = $areaNombre;
                $proveedorData['clientenombre'] = $request->input('nombrecompleto');
                Bateriasubcliente::create($proveedorData);
            }
        }
        return redirect()->route('admin.clientesbancos.create2', ['clientebanco' => $clientebanco])->with('info', 'ELa beteria se creó con éxito');
    }
   
    public function create3(ClienteBanco $clientebanco)
    {
        $nombreCliente = $clientebanco->nombrecompleto;
        $accionesCliente = BateriaSubCliente::where('clientenombre', $nombreCliente)->pluck('accionnombre')->toArray();

        return view('admin.clientesbancos.create3', compact('clientebanco', 'accionesCliente'));
    }

    public function create4(ClienteBanco $clientebanco)
    {
        $nombreCliente = $clientebanco->nombrecompleto;
        
        // Paso 1: Obtener todas las acciones del cliente
        $accionesCliente = BateriaSubCliente::where('clientenombre', $nombreCliente)->pluck('accionnombre')->toArray();
        
        // Paso 2: Encontrar los proveedores asociados agrupados por acción
        $proveedoresAsociados = BateriaProveedor::whereIn('accion', $accionesCliente)
            ->get()
            ->groupBy('accion');

        // Paso 3: Marcar las acciones ya registradas para el cliente
        $accionesRegistradas = Programacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clientenombre', $nombreCliente)
            ->pluck('accionnombre')->toArray();

        return view('admin.clientesbancos.create4', compact('clientebanco', 'accionesCliente', 'proveedoresAsociados', 'accionesRegistradas'));
    }

    public function store3(StoreProgramacionsubclienteRequest $request)
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

            $proveedorData['accionnombre'] = $accionnombre;
            $proveedorData['horaasignada'] = $horaasignada;
            $proveedorData['clientenombre'] = $request->input('nombrecompleto');
            $proveedorData['fechaasignada'] = $fechaasignada;

            $clientebanco = Programacionsubcliente::create($proveedorData);
        }

        return redirect()->route('admin.clientesbancos.create4', $request->clientebanco)->with('info', 'La programacion del cliente se creo con éxito');
    }

    public function documentacioncliente(ClienteBanco $clientebanco)
    {
        $nombreCliente = $clientebanco->nombrecompleto;
        $accionesCliente = BateriaSubCliente::where('clientenombre', $nombreCliente)->pluck('accionnombre')->toArray();
        $id = $clientebanco->nombrecompleto ? ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('id') : null;

        $accionesPorArea = Programacionsubcliente::where('clientenombre', $nombreCliente)->pluck('accionnombre', 'id');
        
        $documentosRegistrados = Documentacionsubcliente::whereIn('accion', $accionesCliente)
        ->where('nombrecompleto', $nombreCliente)
        ->pluck('accion')->toArray();
        
        // Filtrar las acciones disponibles para mostrar en el select
        $accionesDisponibles = $accionesPorArea->reject(function ($accion) use ($documentosRegistrados) {
            return in_array($accion, $documentosRegistrados);
        });

        return view('admin.clientesbancos.documentacioncliente', compact('accionesPorArea','accionesDisponibles', 'clientebanco', 'id', 'accionesCliente', 'documentosRegistrados'));
    }

    public function subirdocumentacioncliente(StoreDocumentacionsubclienteRequest $request)
    {
        $archivo_name = null;

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path("/file"), $archivo_name);
        }

        // Obtener el nombre de la acción usando el ID de la acción
        $accionNombre = Programacionsubcliente::where('id', $request->accion)->value('accionnombre');

        // Crear la documentación del cliente con el nombre de la acción
        $documentacioncliente = Documentacionsubcliente::create(
            $request->except('accion') + [
                'document' => $archivo_name,
                'accion' => $accionNombre // Guardar el nombre de la acción en lugar del ID
            ]
        );

        return redirect()->route('admin.clientesbancos.documentacioncliente', $request->clientebanco)->with('info', 'El documento se subió con éxito');
    }
    public function downloadPDF($id)
        {
            $documentacioncliente = Documentacionsubcliente::findOrFail($id);
            $documentName = $documentacioncliente->document;
            if (!file_exists(public_path('file/' . $documentName))) {
                abort(404);
            }
            return response()->download(public_path('file/' . $documentName), $documentName, ['Content-Type' => 'application/pdf']);
        }

    public function verdocumentacioncliente(Request $request, ClienteBanco $clientebanco)
    {
        $documentacionclientes = Documentacionsubcliente::where('nombrecompleto', $clientebanco->nombrecompleto)->get();
        // Recuperar los documentos asociados con el cliente
        $clientebanco = $clientebanco->nombrecompleto ? ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('nombrecompleto') : null;

        return view('admin.clientesbancos.verdocumentacioncliente', compact('clientebanco', 'documentacionclientes'));
    }

    public function listadoclientebanco(Request $request, Asociado $asociado)
        {
            $clientebancos = ClienteBanco::where('asociadonombre', $asociado->asociado)->get();
            $asociado = $asociado->asociado ? Asociado::where('asociado', $asociado->asociado)->value('asociado') : null;
            return view('admin.asociados.listadoclientebanco', compact('asociado', 'clientebancos'));
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
            
        }
        

}
