<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Asociado;
use App\Models\Bateriaproveedor;
use App\Models\Bateriaclientecomun;
use App\Models\Areaaccion;
use App\Models\Departamento;
use App\Models\Area;
use App\Models\Banco;
use App\Models\Formulario;
use App\Models\Tipoareaaccion;
use App\Models\ClienteAuditoria;
use App\Models\ClienteComun;
use App\Models\ClienteBanco;
use App\Models\Empresa;
use App\Models\Pais;
use App\Models\Ciudad;
use App\Models\Cliente;
use App\Models\Aseguradora;
use App\Models\Contactosubcliente;
use App\Models\Requisitosubcliente;
use App\Models\Requisitosclientesauditoria;
use App\Models\Afp;
use App\Models\Bateriasubcliente;
use App\Models\Estadoprogramacionsubcliente;
use App\Models\Estadocotizacionsubcliente;
use App\Models\Programacionsubcliente;
use App\Models\Documentacionsubcliente;
use App\Models\Tramitesubcliente;
use App\Models\Personal;
use App\Models\Accion;
use App\Http\Requests\StoreAsociadoRequest;
use App\Http\Requests\StoreProgramacionsubclienteRequest;
use App\Http\Requests\StoreEstadocotizacionsubclienteRequest;
use App\Http\Requests\StoreEstadoprogramacionsubclienteRequest;
use App\Http\Requests\StoreDocumentacionsubclienteRequest;
use App\Http\Requests\StoreContactosubclienteRequest;
use App\Http\Requests\StoreBateriasubclienteRequest;
use App\Http\Requests\UpdateClienteitaRequest;
use App\Http\Requests\UpdateClienteBancoRequest;
use App\Http\Requests\UpdateClienteComunRequest;
use App\Http\Requests\UpdateClienteAuditoriaRequest;
use App\Http\Requests\UpdateRequisitosubclienteRequest;
use App\Http\Requests\StoreTramitesubclienteRequest;
use App\Http\Requests\StoreClienteAuditoriaRequest;
use App\Http\Requests\StoreClienteComunRequest;
use App\Http\Requests\StoreClienteBancoRequest;
use App\Http\Requests\StoreClienteRequest;
use App\Services\WhatsAppService;
use Dompdf\Dompdf;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Fichamedicasubcliente;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf as DompdfWriter;
use App\Http\Requests\StoreAreaaccionRequest;
use App\Http\Requests\UpdateAreaaccionRequest;
use App\Http\Requests\StoreProveedorInformefinalRequest;
use App\Models\ProveedorInformefinal;
use App\Models\Proveedor;
use Illuminate\Support\Facades\File;
use App\Models\PermisoCodigo;
use Carbon\Carbon;

class AsociadoController extends Controller
{
    public function __construct() { 
        $this->middleware ('can:admin.asociados.index')->only('index');
    }
    public function index(Request $request)
    {
        $nombreasociado = $request->get('buscarpor');

        $asociados = Asociado::whereNotIn('id', [2, 3, 6])
                          ->where('asociado', 'LIKE', "%$nombreasociado%")
                          ->orderBy('asociado')
                          ->simplePaginate(1000);
        
        $grupoclientes = Asociado::whereIn('id', [2, 3, 6])
                          ->where('asociado', 'LIKE', "%$nombreasociado%")
                          ->orderBy('asociado')
                          ->simplePaginate(1000);
  
        $rolusuario = auth()->user()->getRoleNames()->first();
        $empresaUsuario = auth()->user()->empresa;

        return view('admin.asociados.index', compact('empresaUsuario','rolusuario','asociados', 'grupoclientes'));
    }
    public function create()
    {
        $estadoasociado = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $mododepago = [
            'EFECTIVO' => 'EFECTIVO',
            'TRANSFERENCIA BANCARIA' => 'TRANSFERENCIA BANCARIA',
            'CHEQUE' => 'CHEQUE',
        ];
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
        $bancos = Banco::orderBy('nombrebanco')->pluck('nombrebanco', 'id');

        return view('admin.asociados.create', compact('departamentos', 'estadoasociado','mododepago', 'bancos'));
    }
    public function show(User $users)
    {
        //
    }
    public function store(StoreAsociadoRequest $request)
    {
        $asociadorData = $request->all();

        $asociado = Asociado::create($asociadorData);

        return redirect()->route('admin.asociados.index', $asociado)->with('info', 'El asociado se creo con éxito');
    }
    public function edit(Asociado $asociado)
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
        return view('admin.proveedores.edit', compact('proveedore', 'departamentos', 'estadoproveedor', 'areas', 'accionesPorArea'));
    }
    public function update(StoreAsociadoRequest $request)
    {

        $idCiudad = $request->input('ciudad');
        $ciudad = Departamento::findOrFail($idCiudad);
        $ciudadNombre = $ciudad->departamento;
    
        $estadoSeleccionado = $request->input('estadoproveedor');
    
        $accionesSeleccionadas = $request->input('accion');
    
        foreach ($accionesSeleccionadas as $accionNombre) {
            $proveedorData = $request->except(['accion', '_token']);
            $proveedorData['ciudad'] = $ciudadNombre;
            $proveedorData['accion'] = $accionNombre;
            $proveedorData['estadoproveedor'] = $estadoSeleccionado;
            
            // Obteniendo el nombre del área seleccionada
            $id = $request->input('area');
            $area = Area::findOrFail($id);
            $areaNombre = $area->nombrearea;
            $proveedorData['area'] = $areaNombre;
    
            Proveedor::create($proveedorData);
        }
    
        return redirect()->route('admin.proveedores.index')->with('info', 'Los proveedores se crearon con éxito');
    }
    public function destroy(Empresa $empresa)
    {
        $empresa->delete();

        return redirect()->route('admin.empresas.index', $empresa)->with('eliminar', 'ok');
    } 

/////////////////CLIENTES ITA (INVALIVEZ, TECNICO MEDICO, APELACION)
//CREAR Y EDITAR CLIENTE ITA
    public function crearclienteita(Asociado $asociado)
    {
        $suc = [
            'COCHABAMBA' => 'COCHABAMBA',
            'SANTA CRUZ' => 'SANTA CRUZ',
        ];
        $tipoide = [
            'CI' => 'CI',
            'PASAPORTE' => 'PASAPORTE',
            'LIBRETA DE SERVICIO MILITAR' => 'LIBRETA DE SERVICIO MILITAR',
            'LICENCIA DE CONDUCIR' => 'LICENCIA DE CONDUCIR',
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
        $estciv = [
            'SOLTER@' => 'SOLTER@',
            'CASAD@' => 'CASAD@',
            'UNION LIBRE' => 'UNION LIBRE',
            'DIVORCIAD@' => 'DIVORCIAD@',
            'VIUD@' => 'VIUD@',
        ];
        $genero = [
            'MASCULINO' => 'MASCULINO',
            'FEMENINO' => 'FEMENINO',
        ];
        $gradins = [
            'ANALFABETO' => 'ANALFABETO',
            'PRIMARIA' => 'PRIMARIA',
            'SECUNDARIA' => 'SECUNDARIA',
            'TECNICO' => 'TECNICO',
            'UNIVERSITARIO' => 'UNIVERSITARIO',
            'POSTGRADO' => 'POSTGRADO',
        ];
        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $tipocliente = [
            'APELACION' => 'APELACION',
            'COMPENSACION DE COTIZACIONES SENASIR' => 'COMPENSACION DE COTIZACIONES SENASIR',
            'INVALIDEZ' => 'INVALIDEZ',
            'JUBILACION' => 'JUBILACION',
            'MASA HEREDITARIA' => 'MASA HEREDITARIA',
            'PENSION POR MUERTE' => 'PENSION POR MUERTE',
            'RETIRO DE APORTES TOTAL' => 'RETIRO DE APORTES TOTAL',
            'RETIRO DE APORTES PARCIAL' => 'RETIRO DE APORTES PARCIAL',
            'SEGUNDA SOLICITUD' => 'SEGUNDA SOLICITUD',
            
        ];

        $empresas = Empresa::orderBy('nombreempresa')->pluck('nombreempresa', 'id');
        $pais = Pais::orderBy('pais')->pluck('pais', 'id');
        $ciudades = Ciudad::orderBy('ciudad')->pluck('ciudad', 'id');
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'id');
        $afp = Afp::where('id', 3)->value('afp');
        
        return view('admin.asociados.crearclienteita', compact('asociado', 'tipocliente', 'suc','tipoide', 'ciudadexp', 'estciv', 'genero', 'gradins', 'estlab', 'empresas', 'pais', 'ciudades', 'departamentos', 'aseguradoras', 'afp'));
    }
    public function guardarclienteita(StoreClienteRequest $request)
    {
        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $image_name = time().'_'.$file->getClientOriginalName();
            $file->move(public_path("/image"), $image_name);
        }

        $empresaNombre = $request->input('empresa') ? Empresa::findOrFail($request->input('empresa'))->nombreempresa : null;

        $id = $request->input('ciudadresidencia');
        $ciudadresidencia = Ciudad::findOrFail($id);
        $ciudadNombre = $ciudadresidencia->ciudad;

        $id = $request->input('lugarnacimiento');
        $lugarnacimiento = Departamento::findOrFail($id);
        $lugarnacNombre = $lugarnacimiento->departamento;

        $aseguradoraNombre = $request->input('aseguradora') ? Aseguradora::findOrFail($request->input('aseguradora'))->aseguradora : null;

        $id = $request->input('paisresidencia');
        $paisresidencia = Pais::findOrFail($id);
        $paisNombre = $paisresidencia->pais;

        $id = $request->input('departamentoresidencia');
        $departamentoresidencia = Departamento::findOrFail($id);
        $departamentoNombre = $departamentoresidencia->departamento;

        $clienteData = $request->all();
        $clienteData['empresa'] = $empresaNombre;
        $clienteData['ciudadresidencia'] = $ciudadNombre;
        $clienteData['lugarnacimiento'] = $lugarnacNombre;
        $clienteData['aseguradora'] = $aseguradoraNombre;
        $clienteData['paisresidencia'] = $paisNombre;
        $clienteData['departamentoresidencia'] = $departamentoNombre;

        if ($image_name) {
            $clienteData['image'] = $image_name;
        }
        $cliente = Cliente::create($clienteData);

        return redirect()->route('admin.asociados.verclienteita', $cliente->id)->with('info', 'El cliente se creó con éxito');
    }
    /* public function listadoclienteita(Request $request, Asociado $asociado)
    {
        $nombrecompleto = $request->get('buscarpor');
        $clientes = Cliente::where('nombrecompleto', 'LIKE', "%$nombrecompleto%")
                            ->orderBy('nombrecompleto')
                            ->simplePaginate(10000);
        return view('admin.asociados.listadoclienteita', compact('asociado', 'clientes'));
    } */
    public function listadoclienteita(Request $request, Asociado $asociado) 
    {
        $nombrecompleto = $request->get('buscarpor');
        $clientes = Cliente::with('servicios') // Cargar servicios relacionados
                            ->where('nombrecompleto', 'LIKE', "%$nombrecompleto%")
                            ->orderBy('nombrecompleto')
                            ->simplePaginate(10000);
        return view('admin.asociados.listadoclienteita', compact('asociado', 'clientes'));
    }
    public function buscarclientesita(Request $request, Asociado $asociado)
    {
        $busqueda = $request->get('buscarpor');
        $clientes = Cliente::where(function ($query) use ($busqueda) {
            $query->where('nombrecompleto', 'like', "%$busqueda%")
                ->orWhere('ciudadresidencia', 'like', "%$busqueda%")
                ->orWhere('ci', 'like', "%$busqueda%")
                ->orWhere('id', 'like', "%$busqueda%");
        })->simplePaginate(1000);
        return view('admin.asociados.listadoclienteita', compact('asociado','clientes'));
    }
    public function verclienteita(Cliente $cliente)
    {
        $nombreusuario = auth()->user()->name; 
        $requisitosubclientes = ProveedorInformefinal::where('clienteitaid', $cliente->id)->get();
        $proveedores = Proveedor::whereIn('id', [3, 54])->get(['id', 'proveedor', 'celular']);
        $tieneRequisitos = RequisitoSubCliente::where('clienteitaid', $cliente->id)->exists();
        $tieneBateria = Bateriasubcliente::where('clienteitaid', $cliente->id)->exists();
        $tieneContactos = ContactoSubCliente::where('clienteitaid', $cliente->id)->exists();
        $tieneCotizacionaprobada = Estadocotizacionsubcliente::where('clienteitaid', $cliente->id)->exists();
        $tieneProgramacion = Programacionsubcliente::where('clienteitaid', $cliente->id)->exists();
        $tieneProgramacionatentido = Estadoprogramacionsubcliente::where('clienteitaid', $cliente->id)->exists();
        $tienerequisitosauditoria = Requisitosclientesauditoria::where('clienteitaid', $cliente->id)->exists();

        $tienerequisitosapelacion = RequisitoSubCliente::where('clienteitaid', $cliente->id)->exists();
        $tienerequisitossegundasolicitud = RequisitoSubCliente::where('clienteitaid', $cliente->id)->exists();

        $tieneTramites = Tramitesubcliente::where('clienteitaid', $cliente->id)->exists();
        $cartaconsentimientoExistente = Estadocotizacionsubcliente::where('clienteitaid', $cliente->id) 
            ->where('detalle', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS')
            ->whereNotNull('document')
            ->first();
        $bateriaaprobadaExistente = DocumentacionSubcliente::where('clienteitaid', $cliente->id) 
            ->where('accion', 'APROBADO PARA INICIAR A CREAR BATERIA')
            ->first();

        $documentacion = Documentacionsubcliente::where('clienteitaid', $cliente->id)
        ->where('accion', 'HISTORIA MÉDICA')
        ->first();

        $tieneAuditoriaMedica = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'AUDITORIA MEDICA')
        ->exists();

        $tieneApelacion = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'APELACION')
        ->exists();

        $tieneSegundasolicitud = Tramitesubcliente::where('clienteitaid', $cliente->id)
        ->where('tramite', 'SEGUNDA SOLICITUD')
        ->exists();

        $IDCliente = $cliente->id;
        $sucursalCliente = $cliente->sucursal;

        $historiamedica = Documentacionsubcliente::withTrashed()
        ->where('clienteitaid', $cliente->id)
        ->where('accion', 'HISTORIA MÉDICA')
        ->first();
        $historiamedicaclienteita = $historiamedica ? $historiamedica->document : null;

        $accionesCliente = BateriaSubCliente::where('clienteitaid', $IDCliente)->pluck('accionnombre')->toArray();

        $fechasbateriasSubCliente = BateriaSubCliente::where('clienteitaid', $IDCliente)
            ->distinct()
            ->pluck('fechabateria');

        $fechasRegistradas = ProveedorInformefinal::where('clienteitaid', $cliente->id)
            ->pluck('fechabateria');

        $fechasDisponibles = $fechasbateriasSubCliente->diff($fechasRegistradas);

        $fechasBateriaPorAccion = BateriaSubCliente::whereIn('accionnombre', $accionesCliente)
            ->where('clienteitaid', $IDCliente)
            ->whereIn('fechabateria', $fechasDisponibles)
            ->distinct()
            ->pluck('fechabateria', 'accionnombre');

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accion => $fecha) {
            $accionesPorFecha[$fecha][] = $accion;
        }
        $tramitesPorFecha = Tramitesubcliente::where('clienteitaid', $cliente->id)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->fechabateria => $item->tramite];
            });
            $clienteConInvalidez = Tramitesubcliente::where('clienteitaid', $cliente->id)
    ->where('tramite', 'INVALIDEZ')
    ->exists();

$clienteConApelacionOSegunda = Tramitesubcliente::where('clienteitaid', $cliente->id)
    ->whereIn('tramite', ['APELACION', 'SEGUNDA SOLICITUD'])
    ->exists();

        return view('admin.asociados.verclienteita', compact('clienteConInvalidez','clienteConApelacionOSegunda','tramitesPorFecha','historiamedicaclienteita','nombreusuario','tienerequisitosapelacion','tienerequisitossegundasolicitud','tieneTramites','tienerequisitosauditoria','tieneApelacion','tieneSegundasolicitud','tieneAuditoriaMedica','tieneProgramacion','tieneProgramacionatentido','tieneCotizacionaprobada','bateriaaprobadaExistente','tieneBateria','cartaconsentimientoExistente','tieneContactos','requisitosubclientes','accionesPorFecha','fechasBateriaPorAccion','proveedores', 'cliente', 'tieneRequisitos', 'documentacion'));
    }
    public function editarclienteita(Cliente $cliente)
    {
        $suc = [
            'COCHABAMBA' => 'COCHABAMBA',
            'SANTA CRUZ' => 'SANTA CRUZ',
        ];
        $tipoide = [
            'CI' => 'CI',
            'PASAPORTE' => 'PASAPORTE',
            'LIBRETA DE SERVICIO MILITAR' => 'LIBRETA DE SERVICIO MILITAR',
            'LICENCIA DE CONDUCIR' => 'LICENCIA DE CONDUCIR',
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
        $estciv = [
            'SOLTER@' => 'SOLTER@',
            'CASAD@' => 'CASAD@',
            'UNIION LIBRE' => 'UNIION LIBRE',
            'DIVORCIAD@' => 'DIVORCIAD@',
            'VIUD@' => 'VIUD@',
        ];
        $genero = [
            'MASCULINO' => 'MASCULINO',
            'FEMENINO' => 'FEMENINO',
        ];
        $gradins = [
            'ANALFABET@' => 'ANALFABET@',
            'PRIMARIA' => 'PRIMARIA',
            'SECUNDARIA' => 'SECUNDARIA',
            'TECNICO' => 'TECNICO',
            'UNIVERSITARIO' => 'UNIVERSITARIO',
            'POSTGRADO' => 'POSTGRADO',
        ];
        $estlab = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $tipocliente = [
            'APELACION' => 'APELACION',
            'COMPENSASION DE COTIZACIONES SENASIR' => 'COMPENSASION DE COTIZACIONES SENASIR',
            'INVALIDEZ' => 'INVALIDEZ',
            'JUBILACION' => 'JUBILACION',
            'MASA HEREDITARIA' => 'MASA HEREDITARIA',
            'PENSION POR MUERTE' => 'PENSION POR MUERTE',
            'RETIRO DE APORTES TOTAL' => 'RETIRO DE APORTES TOTAL',
            'RETIRO DE APORTES PARCIAL' => 'RETIRO DE APORTES PARCIAL',
            'SEGUNDA SOLICITUD' => 'SEGUNDA SOLICITUD',
        ];

        $empresas = Empresa::orderBy('nombreempresa')->pluck('nombreempresa', 'nombreempresa');

        $pais = Pais::orderBy('pais')->pluck('pais', 'pais');

        $ciudades = Ciudad::orderBy('ciudad')->pluck('ciudad', 'ciudad');

        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'departamento');

        $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'aseguradora');

        $imagenCliente = null;
        if ($cliente->image) {
            $imagenCliente = asset('image/' . $cliente->image);
        }

        return view('admin.asociados.editarclienteita', compact('cliente', 'tipocliente', 'suc', 'tipoide', 'ciudadexp', 'estciv', 'genero', 'gradins', 'estlab', 'empresas', 'pais', 'ciudades', 'departamentos', 'aseguradoras', 'imagenCliente'));
    }
    public function actualizarclienteita(UpdateClienteitaRequest $request, Cliente $cliente)
    {
        $clienteData = $request->validated();
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $image_name = time().'_'.$file->getClientOriginalName();
            $file->move(public_path("/image"), $image_name);

            if ($cliente->image) {
                unlink(public_path("/image/{$cliente->image}"));
            }
            $clienteData['image'] = $image_name;
        }
        $cliente->update($clienteData);

        return redirect()->route('admin.asociados.verclienteita', $cliente)->with('info', 'El cliente se actualizó con éxito');
    }
//
//ASIGNAR TRAMITE
    public function listadotramiteclienteita(Asociado $asociado, Cliente $cliente)
    {
        $nombreclienteita = $cliente->nombrecompleto;
        $tramitesubclientes = Tramitesubcliente::where('clienteitanombre', $nombreclienteita)
                                    ->simplePaginate(10000);

        // Obtén todos los apoderados ordenados por nombre
        /* $apoderados = Personal::orderBy('nombrecompleto')
                            ->where('cargo', 'EJECUTIVO PRESTACIONES')
                            ->pluck('nombrecompleto', 'nombrecompleto'); */

        // Convertir a array para acceso por índice
        /* $apoderadosArray = $apoderados->keys()->toArray(); */

        // Recuperar el último apoderado registrado en Tramitesubcliente sin importar el cliente
        /* $ultimoApoderado = Tramitesubcliente::latest('created_at')
                                            ->value('usuarioasignado'); */

        // Encuentra el índice del último apoderado en el array de apoderados
        /* $indiceActual = array_search($ultimoApoderado, $apoderadosArray); */

        // Si no se encontró el último apoderado, empieza desde el primer apoderado
        /* if ($indiceActual === false) {
            $indiceActual = -1;
        } */

        // Determinar el apoderado siguiente
        /* $indiceSiguiente = ($indiceActual + 1) % count($apoderadosArray);
        $apoderadoSiguiente = $apoderadosArray[$indiceSiguiente]; */

        // Actualizar el índice para la próxima vez
        /* session(['indice_apoderado' => $indiceSiguiente]); */

        $tramites = [
            'APELACION' => 'APELACION',
            'AUDITORIA MEDICA' => 'AUDITORIA MEDICA',
            'INVALIDEZ' => 'INVALIDEZ',
            /* 'COMPENZACIÓN SENASIR' => 'COMPENZACIÓN SENASIR',
            'JUBILACION' => 'JUBILACION',
            'MASA HEREDITARIA' => 'MASA HEREDITARIA',
            'PENSIÓN POR MUERTE' => 'PENSIÓN POR MUERTE',
            'RETIRO DE APORTES TOTAL' => 'RETIRO DE APORTES TOTAL',
            'RETIRO DE APORTES PARCIAL' => 'RETIRO DE APORTES PARCIAL', */
            'SEGUNDA SOLICITUD' => 'SEGUNDA SOLICITUD',
        ];

        $ciudades = [
            'COCHABAMBA' => 'COCHABAMBA',
            'SANTA CRUZ' => 'SANTA CRUZ',
        ];

        $nombreCliente = $cliente->nombrecompleto;
        $sucursalCliente = $cliente->sucursal;

        $accionesCliente = BateriaSubCliente::where('clienteitanombre', $nombreCliente)
            ->whereIn('accionnombre', function ($query) use ($sucursalCliente) {
                $query->select('accionnombre')->from('clientes')->where('sucursal', $sucursalCliente);
            })
            ->pluck('accionnombre')
            ->unique();

        $fechasEnEstadoCotizacionSubCliente = EstadoCotizacionSubCliente::where('clienteitanombre', $nombreCliente)
        ->distinct()
        ->pluck('fechabateria');

        $fechasBateriaPorAccion = BateriaSubCliente::whereIn('accionnombre', $accionesCliente)
        ->where('clienteitanombre', $nombreCliente)
        /* ->whereIn('fechabateria', $fechasEnEstadoCotizacionSubCliente) */
        ->distinct()
        ->pluck('fechabateria', 'accionnombre');

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accion => $fecha) {
        $accionesPorFecha[$fecha][] = $accion;
        }

        return view('admin.asociados.listadotramiteclienteita', compact('ciudades', 'tramitesubclientes', 'cliente', 'asociado', /* 'apoderados', */ 'tramites'/* , 'apoderadoSiguiente' */, 'accionesPorFecha'));
    }
    public function guardartramiteclienteita(StoreTramitesubclienteRequest $request)
    {
        $clienteID = $request->input('clienteitaid');
        $cliente = Cliente::findOrFail($clienteID);

        $clienteData = $request->all();
        $clienteData['clienteitanombre'] = $cliente->nombrecompleto;
        $clienteData['apoderado'] = $request->input('apoderado');
        $clienteData['usuarioid'] = $request->usuarioid;
        $clienteData['usuarioregistro'] = $request->usuarioregistro;
        $clienteData['estado'] = $request->input('estado');
        $tramitesubcliente = Tramitesubcliente::create($clienteData);

        // Obtener el trámite seleccionado
        $tramite = $request->input('tramite');

        // Verificar el trámite y generar el PDF
        if ($tramite == 'INVALIDEZ') {
            $pdf = PDF::loadView('admin.asociados.generaretiquetaclienteita', compact('cliente', 'tramite'));
            $pdfName = 'Etiqueta_Invalidez_' . $cliente->nombrecompleto . '.pdf';
        } elseif ($tramite == 'AUDITORIA MEDICA') {
            $pdf = PDF::loadView('admin.asociados.generaretiquetaclienteitaauditoria', compact('cliente', 'tramite'));
            $pdfName = 'Etiqueta_Auditoria_' . $cliente->nombrecompleto . '.pdf';
        } elseif ($tramite == 'APELACION') {
            $pdf = PDF::loadView('admin.asociados.generaretiquetaclienteitaapelacion', compact('cliente', 'tramite'));
            $pdfName = 'Etiqueta_Apelacion_' . $cliente->nombrecompleto . '.pdf';
        } elseif ($tramite == 'SEGUNDA SOLICITUD') {
            $pdf = PDF::loadView('admin.asociados.generaretiquetaclienteitasegundasolicitud', compact('cliente', 'tramite'));
            $pdfName = 'Etiqueta_SegundaSolicitud_' . $cliente->nombrecompleto . '.pdf';
        } else {
            return response()->json(['error' => 'Servicio no válido'], 400);
        }

        // Guarda el PDF directamente en la carpeta 'public'
        $pdf->save(public_path($pdfName));

        // Retorna la URL directamente desde 'public'
        return response()->json([
            'pdf_url' => asset($pdfName), // Se asume que el archivo está en 'public'
            'redirect_url' => route('admin.asociados.listadotramiteclienteita', $cliente->id)
        ]);
    }
    public function asignarFecha_ITA(Request $request, $clienteId)
    {
        // Validar que se haya seleccionado una fecha de batería
        $request->validate([
            'fechabateria' => 'required'
        ], [
            'fechabateria.required' => 'Debe seleccionar una fecha de batería.'
        ]);

        // Encuentra el trámite del cliente por su ID
        $clienteTramite = Tramitesubcliente::find($clienteId);

        // Asignar la nueva fecha de batería
        $clienteTramite->fechabateria = $request->input('fechabateria');
        $clienteTramite->save();

        // Redirigir a la misma URL donde estaba el usuario
        return back()->with('info', 'Fecha asignada correctamente.');
    }
//
//CREAR BATERIA CLIENTE ITA 
    
    /* public function guardarbateriaclienteita(StoreBateriasubclienteRequest $request)
    {
        $clienteID = $request->input('clienteitaid');
        $cliente = Cliente::findOrFail($clienteID);
        $accionesSeleccionadas = $request->input('accionnombre');
        $tipoArea = $request->input('tipoarea');
        $antecedentes = $request->input('antecedentes');
        $informe = $request->input('informe');
        $sucursalCliente = $cliente->sucursal;
        if ($tipoArea === 'Estudios') {
            $areasSeleccionadas = $request->input('areanombre');
            if (!is_array($areasSeleccionadas)) {
                $areasSeleccionadas = [$areasSeleccionadas];
            }
            foreach ($areasSeleccionadas as $areaId) {
                $area = Area::findOrFail($areaId);
                $areaNombre = $area->nombrearea;
                foreach ($accionesSeleccionadas as $accionNombre) {
                    $areaAccion = AreaAccion::where('areasid', $areaId)
                                            ->where('accion', $accionNombre)
                                            ->where('sucursal', $sucursalCliente)
                                            ->first();
                    $precioAccion = $areaAccion ? $areaAccion->precio : 0;
                    $fechaSeleccionada = $request->input('fechabateria');
                    $clienteitaData = $request->except(['accionnombre', '_token']);
                    $clienteitaData['accionnombre'] = $accionNombre;
                    $clienteitaData['clienteitaid'] = $clienteID;
                    $clienteitaData['areanombre'] = $areaNombre;
                    $clienteitaData['clienteitanombre'] = $cliente->nombrecompleto;
                    $clienteitaData['tipoarea'] = 'ESTUDIO';
                    $clienteitaData['precio'] = $precioAccion;
                    if ($fechaSeleccionada === 'nueva_bateria') {
                        // Asigna la fecha de hoy si se seleccionó "FECHA DE HOY"
                        $clienteitaData['fechabateria'] = now()->toDateString();
                    } else {
                        // De lo contrario, usa la fecha seleccionada
                        $clienteitaData['fechabateria'] = $fechaSeleccionada;
                    }
                    $clienteitaData['informe'] = $informe;
                    Bateriasubcliente::create($clienteitaData);
                }
            }
        } elseif ($tipoArea === 'Especialidades') {
            foreach ($accionesSeleccionadas as $accionNombre) {
                $areaAccion = AreaAccion::where('accion', $accionNombre)
                                        ->where('sucursal', $sucursalCliente)
                                        ->first();
                $precioAccion = $areaAccion ? $areaAccion->precio : 0;
                $fechaSeleccionada = $request->input('fechabateria');
                $clienteitaData = $request->except(['accionnombre', '_token']);
                $clienteitaData['accionnombre'] = $accionNombre;
                $clienteitaData['antecedentes'] = $antecedentes;
                $clienteitaData['clienteitaid'] = $clienteID;
                $clienteitaData['areanombre'] = $accionNombre;
                $clienteitaData['clienteitanombre'] = $cliente->nombrecompleto;
                $clienteitaData['tipoarea'] = 'ESPECIALIDAD';
                $clienteitaData['precio'] = $precioAccion;
                if ($fechaSeleccionada === 'nueva_bateria') {
                    // Asigna la fecha de hoy si se seleccionó "FECHA DE HOY"
                    $clienteitaData['fechabateria'] = now()->toDateString();
                } else {
                    // De lo contrario, usa la fecha seleccionada
                    $clienteitaData['fechabateria'] = $fechaSeleccionada;
                }
                $clienteitaData['informe'] = $informe;
                Bateriasubcliente::create($clienteitaData);
            }
        }
        return redirect()->route('admin.asociados.crearbateriaclienteita', ['cliente' => $cliente])->with('info', 'La batería se creó con éxito');
    } */
    public function crearbateriaclienteita(Cliente $cliente)
    {
        $nombreusuario = auth()->user()->name;
        $fechaHoraActual = now();
        $fechaActual = $fechaHoraActual->toDateString();

        $usuariosConPermisoSiempre = [
            'CARLOS ALEJANDRO GUARACHI SANDOVAL',
            'DENISSE MAUREN LOPEZ FLORES',
            'VANESSA MAMANI HUANACO',
            'JHOSELINE EVA VELASQUEZ ESCOBAR',
            'AGUIRRE VASQUEZ MARIA RENEE',
        ];

        $permisoValido = false;
        $permisoCodigo = false;
        $fechaExpiracion = null;

        if (in_array($nombreusuario, $usuariosConPermisoSiempre)) {
            $permisoValido = true;
        } else {
            $permisoCodigo = PermisoCodigo::where('usuarioSolicitante', $nombreusuario)
                ->where('estado', 'activo')
                ->whereDate('fechaSolicitada', $fechaActual)
                ->latest('horaActivacion')
                ->first();

            if ($permisoCodigo) {
                // Verificar que 'permisoSolicitado' coincide exactamente con el permiso requerido
                if ($permisoCodigo->permisoSolicitado === 'admin.asociados.crearbateriaclienteita') {
                    $horaActivacion = Carbon::parse($permisoCodigo->horaActivacion);
                    $tiempoLimite = $permisoCodigo->tiempoLimite;
                    $horaExpiracion = $horaActivacion->copy()->addMinutes($tiempoLimite);

                    if ($fechaHoraActual->lessThanOrEqualTo($horaExpiracion)) {
                        $permisoValido = true;
                        $fechaExpiracion = $horaExpiracion;
                    } else {
                        // El permiso ha expirado
                        $permisoCodigo->estado = 'expirado';
                        $permisoCodigo->save();
                    }
                } else {
                    // Si 'permisoSolicitado' no coincide con el permiso requerido, invalidar el permiso
                    $permisoCodigo->estado = 'expirado';
                    $permisoCodigo->save();
                }
            }
        }

        if ($permisoValido && $permisoCodigo) {
            $fechaExpiracion = Carbon::parse($permisoCodigo->horaActivacion)->addMinutes($permisoCodigo->tiempoLimite);
        } else {
            $fechaExpiracion =null;
        }




        $sucursalCliente = $cliente->sucursal;
        $rolusuario = auth()->user()->getRoleNames()->first(); 
        $areas = Area::orderBy('nombrearea', 'asc')
                    ->where('idtipoarea', 2)
                    ->pluck('nombrearea', 'id');

        $accionesPorArea = [];
        foreach ($areas as $id => $nombreArea) {
            $accionesPorArea[$id] = Bateriaproveedor::where('areasid', $id)
                ->where('sucursal', $sucursalCliente)
                ->where('estado', 'ACTIVO')
                ->where('asociado', 'CLIENTES ITA')
                ->orderBy('accion', 'asc')
                ->get(['id', 'accion', 'proveedor', 'precio']);
        }

        $areas2 = Bateriaproveedor::orderBy('area', 'asc')
            ->where('tipoid', 1)
            ->where('estado', 'ACTIVO')
            ->where('sucursal', $sucursalCliente)
            ->where('asociado', 'CLIENTES ITA')
            ->get(['area', 'id', 'proveedor', 'precio']);

        $estadoproveedor = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
        $id = $cliente->nombrecompleto ? Cliente::where('nombrecompleto', $cliente->nombrecompleto)->value('id') : null;

        $nombreCliente = $cliente->nombrecompleto; 
        $idCliente = $cliente->id; 

        $accionesCliente = BateriaSubCliente::where('clienteitaid', $idCliente)
            ->pluck('accionid')
            ->toArray();

        $fechasbateriasSubCliente = BateriaSubCliente::where('clienteitaid', $idCliente)
            ->distinct()
            ->pluck('fechabateria');

        $fechasBateriaPorAccion = BateriaSubCliente::whereIn('accionid', $accionesCliente)
            ->where('clienteitaid', $idCliente)
            ->whereIn('fechabateria', $fechasbateriasSubCliente)
            ->distinct()
            ->pluck('fechabateria', 'accionid');

        $accionesNombres = BateriaSubCliente::whereIn('accionid', $accionesCliente)
            ->pluck('accionnombre', 'accionid')
            ->toArray();

        /* $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accionid => $fecha) {
            $idbateirasubcliente = Bateriasubcliente::where('accionid', $accionid)->where('clienteitaid', $idCliente)->value('id');
            $precioaccion = Bateriasubcliente::where('accionid', $accionid)->where('clienteitaid', $idCliente)->value('precio');
            $informeaccion = Bateriasubcliente::where('accionid', $accionid)->where('clienteitaid', $idCliente)->value('informe');
            $proveedorbateria = Bateriasubcliente::where('accionid', $accionid)->where('clienteitaid', $idCliente)->value('proveedorasignado');

            $accionNombre = $accionesNombres[$accionid] ?? 'Desconocida';

            $accionesPorFecha[$fecha][] = [
                'id' => $idbateirasubcliente,
                'accion' => $accionNombre,
                'proveedor' => $proveedorbateria,
                'precio' => $precioaccion,
                'informe' => $informeaccion
            ];
        } */

        /* $accionesPorFecha = [];
            $registros = BateriaSubCliente::where('clienteitaid', $idCliente)
                ->get(['id','accionid', 'fechabateria', 'precio', 'informe', 'proveedorasignado']);
            
            foreach ($registros as $registro) {
                $accionid = $registro->accionid;
                $fecha = $registro->fechabateria;

                if (!isset($accionesPorFecha[$fecha])) {
                    $accionesPorFecha[$fecha] = [];
                }
                $accionesPorFecha[$fecha][] = [
                    'id' => $registro->id,
                    'accion' => $accionesNombres[$accionid] ?? 'Desconocida',
                    'proveedor' => $registro->proveedorasignado,
                    'precio' => $registro->precio,
                    'informe' => $registro->informe,
                ];
            } */

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accionid => $fecha) {
            $acciones = Bateriasubcliente::where('accionid', $accionid)
                ->where('clienteitaid', $idCliente)
                ->get(['id', 'precio', 'informe', 'proveedorasignado', 'fechabateria']);

            foreach ($acciones as $accion) {
                if ($accion->id && $accion->fechabateria) {
                    $accionesPorFecha[$accion->fechabateria][] = [
                        'id' => $accion->id,
                        'accion' => $accionesNombres[$accionid] ?? 'Desconocida',
                        'proveedor' => $accion->proveedorasignado,
                        'precio' => $accion->precio,
                        'informe' => $accion->informe,
                    ];
                }
            }
        }
 
        return view('admin.asociados.crearbateriaclienteita', compact('permisoValido', 'fechaExpiracion','nombreusuario','accionesPorFecha','fechasBateriaPorAccion','departamentos','estadoproveedor','areas','accionesPorArea','cliente','id','accionesCliente','areas2','rolusuario'));
    }
    public function generarPDFCliente(Request $request, $clienteId) 
    {
        // Obtener el cliente
        $cliente = Cliente::find($clienteId);

        // Validar que la fecha esté presente
        $fechaSeleccionada = $request->input('fecha');
        if (!$fechaSeleccionada) {
            return response()->json(['error' => 'No se seleccionó ninguna fecha.'], 400);
        }

        // Filtrar los registros asociados al cliente ITA según la fecha seleccionada
        $bateriasEvaluaciones = BateriaSubCliente::where('clienteitaid', $clienteId)
            ->whereDate('fechabateria', $fechaSeleccionada)
            ->where('tipoarea', 'ESPECIALIDAD')
            ->whereIn('areanombre', ['PSIQUIATRIA', 'PSICOLOGIA', 'FISIOTERAPIA', 'TRABAJO SOCIAL'])
            ->pluck('areanombre')
            ->unique();

        // Filtrar los registros asociados al cliente ITA según la fecha seleccionada
        $bateriasEspecialidades = BateriaSubCliente::where('clienteitaid', $clienteId)
            ->whereDate('fechabateria', $fechaSeleccionada)
            ->where('tipoarea', 'ESPECIALIDAD')
            ->whereNotIn('areanombre', ['PSIQUIATRIA', 'PSICOLOGIA', 'FISIOTERAPIA', 'TRABAJO SOCIAL', 'INFORME FINAL'])
            ->pluck('areanombre')
            ->unique();


        $bateriasEstudios = BateriaSubCliente::where('clienteitaid', $clienteId)
            ->whereDate('fechabateria', $fechaSeleccionada)
            ->where('tipoarea', 'ESTUDIO')
            ->pluck('areanombre')
            ->unique();

        // Definir los estudios que siempre deben aparecer en "EVALUACIONES MEDICAS TÉCNICAS"
        $tituloEvaluaciones = 'EVALUACIONES MEDICAS TÉCNICAS';
        $estudiosFijos = ['TRABAJO SOCIAL', 'FISIOTERAPIA Y KINESIOLOGIA', 'PSICOLOGIA', 'PSIQUIATRA'];

        // Definir los títulos de las otras secciones
        $tituloEspecialidades = 'SOLICITUD DE INTERCONSULTAS';
        $tituloComplementarios = 'SOLICITUD DE ESTUDIOS COMPLEMENTARIOS';

        // Dividimos los resultados en grupos de 9
        $especialidadesPorFilas = $bateriasEspecialidades->chunk(9);
        $estudiosPorFilas = $bateriasEstudios->chunk(9);
        $evaluacionesPorFilas = $bateriasEvaluaciones->chunk(9);

        // Crear el PDF
        $pdf = PDF::loadView('admin.asociados.pdf.checklistcliente', [
            'cliente' => $cliente,
            'fechaSeleccionada' => $fechaSeleccionada,
            'tituloEvaluaciones' => $tituloEvaluaciones,
            'estudiosFijos' => $estudiosFijos,
            'especialidadesAsociadas' => $especialidadesPorFilas,
            'estudiosAsociados' => $estudiosPorFilas,
            'evaluacionesAsociados' => $evaluacionesPorFilas,
            'tituloEspecialidades' => $tituloEspecialidades,
            'tituloComplementarios' => $tituloComplementarios,
        ]);

        // Generar el archivo PDF y retornarlo como descarga
        $fileName = 'checklist_' . $cliente->nombrecompleto . '_' . time() . '.pdf';
        return $pdf->download($fileName);
    }
    public function eliminarPDF(Request $request)
    {
        // Obtener la ruta del archivo desde la solicitud
        $filePath = $request->input('filePath');

        // Verificar si el archivo existe y eliminarlo
        if (File::exists($filePath)) {
            File::delete($filePath); // Eliminar el archivo
            return response()->json(['status' => 'Archivo eliminado correctamente']);
        } else {
            return response()->json(['status' => 'Archivo no encontrado'], 404);
        }
    }
    public function guardarbateriaclienteita(StoreBateriasubclienteRequest $request, Cliente $cliente)
    {
        if ($request->filled('codigo')) {

            $codigoIngresado = strtoupper($request->input('codigo'));

            // Obtener el usuario autenticado
            $usuarioSolicitante = auth()->user()->name; // Si usas el ID, cambia a 'id'

            // Obtener la fecha actual
            $fechaHoy = now()->toDateString();

            // Buscar el registro en 'permisos_codigo' que cumpla con las condiciones
            $permisoCodigo = PermisoCodigo::where('codigo', $codigoIngresado)
                ->where('usuarioSolicitante', $usuarioSolicitante)
                ->whereDate('fechaSolicitada', $fechaHoy)
                ->where('estado', 'pendiente')
                ->first();

            // Aquí implementas la lógica específica para el código ingresado
            if ($permisoCodigo) {

                // Verificar que 'permisoSolicitado' coincide exactamente con el permiso requerido
                if ($permisoCodigo->permisoSolicitado === 'admin.asociados.crearbateriaclienteita') {
                    // Actualizar 'horaActivacion' y 'estado'
                    $permisoCodigo->horaActivacion = now();
                    $permisoCodigo->estado = 'activo';
                    $permisoCodigo->save();

                    // Redirigir a la ruta con mensaje de éxito
                    return redirect()->route('admin.asociados.crearbateriaclienteita', ['cliente' => $cliente->id])
                        ->with('success', 'Código ingresado correctamente.');
                } else {
                    // Si 'permisoSolicitado' no coincide con el permiso requerido, invalidar el permiso
                    $permisoCodigo->estado = 'expirado';
                    $permisoCodigo->save();

                    // Redirigir con mensaje de error
                    return redirect()->route('admin.asociados.crearbateriaclienteita', ['cliente' => $cliente->id])
                        ->withErrors(['mensaje' => 'El permiso solicitado no es válido.'])
                        ->withInput();
                }
            } else {
                // Si el código es incorrecto, regresas con un mensaje de error
                return redirect()->route('admin.asociados.crearbateriaclienteita', ['cliente' => $cliente->id])
                    ->withErrors(['codigo' => 'El código ingresado es incorrecto, no está autorizado o ha expirado.'])
                    ->withInput();
            }
        } else {

            // Antes de crear la batería, verifica si el usuario tiene permiso

            $nombreusuario = auth()->user()->name;
            $fechaHoraActual = now();
            $fechaActual = $fechaHoraActual->toDateString();

            $usuariosConPermisoSiempre = [
                'CARLOS ALEJANDRO GUARACHI SANDOVAL',
                'DENISSE MAUREN LOPEZ FLORES',
                'VANESSA MAMANI HUANACO',
                'JHOSELINE EVA VELASQUEZ ESCOBAR',
                'AGUIRRE VASQUEZ MARIA RENEE',
            ];

            $permisoValido = false;

            if (in_array($nombreusuario, $usuariosConPermisoSiempre)) {
                $permisoValido = true;
            } else {
                $permisoCodigo = PermisoCodigo::where('usuarioSolicitante', $nombreusuario)
                    ->where('estado', 'activo')
                    ->whereDate('fechaSolicitada', $fechaActual)
                    ->latest('horaActivacion')
                    ->first();

                if ($permisoCodigo) {
                    $horaActivacion = Carbon::parse($permisoCodigo->horaActivacion);
                    $tiempoLimite = $permisoCodigo->tiempoLimite;
                    $horaExpiracion = $horaActivacion->copy()->addMinutes($tiempoLimite);

                    if ($fechaHoraActual->lessThanOrEqualTo($horaExpiracion)) {
                        $permisoValido = true;
                    } else {
                        // El permiso ha expirado
                        $permisoCodigo->estado = 'expirado';
                        $permisoCodigo->save();
                    }
                }
            }

            // Si el permiso no es válido, redirige con un mensaje de error
            if (!$permisoValido) {
                return redirect()->route('admin.asociados.crearbateriaclienteita', ['cliente' => $cliente->id])
                    ->withErrors(['mensaje' => 'Tiempo límite excedido, el permiso ha sido revocado'])
                    ->withInput();
                }
            }


            
        $clienteID = $request->input('clienteitaid');
        $cliente = Cliente::findOrFail($clienteID);
        $accionesSeleccionadas = $request->input('acciones');
        $tipoArea = $request->input('tipoarea');
        $informe = $request->input('informe');
        $sucursalCliente = $cliente->sucursal;
        $fechaActual = now()->toDateString();
        $antecedentes = $request->input('antecedentes');
        $fechainforme = $request->input('fechainforme');
        $horaActual = now()->format('H:i:s');
        $usuarioID = auth()->user()->id;
        $usuarioNombre = auth()->user()->name;

        if ($tipoArea === 'Estudios') {
            $areasSeleccionadas = $request->input('areanombre');
            if (!is_array($areasSeleccionadas)) {
                $areasSeleccionadas = [$areasSeleccionadas];
            }
            foreach ($areasSeleccionadas as $areaId) {
                $area = Area::findOrFail($areaId);
                $areaNombre = $area->nombrearea;
                foreach ($accionesSeleccionadas as $accionId) {
                    $areaAccion = Bateriaproveedor::where('id', $accionId)
                                                ->where('areasid', $areaId)
                                                ->where('sucursal', $sucursalCliente)
                                                ->first();
                    if ($areaAccion) {
                        $accionNombre = $areaAccion->accion;
                        $precioAccion = $areaAccion->precio;
                        $preciocompraAccion = $areaAccion->preciocompra;
                        $proveedorAsignado = $areaAccion->proveedor;
                        $servicio = $areaAccion->servicio;
                    } else {
                        $accionNombre = 'DATO NO ENCONTRADO';
                        $precioAccion = 0;
                        $preciocompraAccion = 0;
                        $proveedorAsignado = 'DATO NO ENCONTRADO';
                    }
                    $fechaSeleccionada = $request->input('fechabateria');
                    $clienteitaData = $request->except(['acciones', '_token']);
                    
                    $clienteitaData['clienteitaid'] = $clienteID;
                    $clienteitaData['areanombre'] = $areaNombre;
                    $clienteitaData['clienteitanombre'] = $cliente->nombrecompleto;
                    $clienteitaData['tipoarea'] = 'ESTUDIO';
                    if ($informe === 'SI TIENE INFORME') {
                        $clienteitaData['accionid'] = $accionId . 'PA';
                        $clienteitaData['accionnombre'] = $accionNombre . ' - PA';
                        $clienteitaData['precio'] = '0';
                        $clienteitaData['preciocompra'] = '0';
                        $clienteitaData['proveedorasignado'] = 'PROVEEDOR AJENO';
                        $clienteitaData['servicio'] = 'AJENO';
                    } else {
                        $clienteitaData['accionid'] = $accionId;
                        $clienteitaData['accionnombre'] = $accionNombre;
                        $clienteitaData['precio'] = $precioAccion;
                        $clienteitaData['preciocompra'] = $preciocompraAccion;
                        $clienteitaData['proveedorasignado'] = $proveedorAsignado;
                        $clienteitaData['servicio'] = $servicio;
                    }
                    $clienteitaData['fechabateria'] = $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada;
                    $clienteitaData['informe'] = $informe;
                    $clienteitaData['usuarioid'] = $usuarioID;
                    $clienteitaData['usuarioregistro'] = $usuarioNombre;
                    $clienteitaData['fechainforme'] = $fechainforme;
                    Bateriasubcliente::create($clienteitaData);

                    if ($informe === 'SI TIENE INFORME') {
                        Programacionsubcliente::create([
                            'proveedornombre' => 'PROVEEDOR AJENO',
                            'clienteitaid' => $clienteID,
                            'clienteitanombre' => $cliente->nombrecompleto,
                            'fechaasignada' => $fechaActual,
                            'fechabateria' => $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada,
                            'horaasignada' => $horaActual,
                            'horadesde' => $horaActual,
                            'horahasta' => $horaActual,
                            'areanombre' => $areaNombre,
                            'accionnombre' => $accionNombre . ' - PA',
                            'precio' => '0',
                            'usuarioid' => $usuarioID,
                            'usuarioregistro' => $usuarioNombre,
                        ]);
                        Estadoprogramacionsubcliente::create([
                            'clienteitaid' => $clienteID,
                            'clienteitanombre' => $cliente->nombrecompleto,
                            'fechaatencionprogramacion' => $fechaActual,
                            'fechabateria' => $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada,
                            'accionnombre' => $accionNombre . ' - PA',
                            'usuarioid' => $usuarioID,
                            'usuarioregistro' => $usuarioNombre,
                        ]);
                    }
                }
            }
        } elseif ($tipoArea === 'Especialidades') {
            $accionesSeleccionadas = $request->input('accionnombre');
            if (!is_array($accionesSeleccionadas)) {
                $accionesSeleccionadas = [$accionesSeleccionadas];
            }
            foreach ($accionesSeleccionadas as $accionId) {
                $areaAccion = Bateriaproveedor::where('id', $accionId)
                                            ->where('sucursal', $sucursalCliente)
                                            ->first();
                if ($areaAccion) {
                    $accionNombre = $areaAccion->accion;
                    $precioAccion = $areaAccion->precio;
                    $preciocompraAccion = $areaAccion->preciocompra;
                    $proveedorAsignado = $areaAccion->proveedor;
                    $servicio = $areaAccion->servicio;
                } else {
                    $accionNombre = 'DATO NO ENCONTRADO';
                    $precioAccion = 0;
                    $preciocompraAccion = 0;
                    $proveedorAsignado = 'DATO NO ENCONTRADO';
                    $servicio = 'DATO NO ENCONTRADO';
                }
                $fechaSeleccionada = $request->input('fechabateria');
                $clienteitaData = $request->except(['accionnombre', '_token']);
                
                $clienteitaData['antecedentes'] = $antecedentes;
                $clienteitaData['clienteitaid'] = $clienteID;
                $clienteitaData['areanombre'] = $accionNombre;
                $clienteitaData['clienteitanombre'] = $cliente->nombrecompleto;
                $clienteitaData['tipoarea'] = 'ESPECIALIDAD';
                if ($informe === 'SI TIENE INFORME') {
                    $clienteitaData['accionid'] = $accionId . 'PA';
                    $clienteitaData['accionnombre'] = $accionNombre . ' - PA';
                    $clienteitaData['precio'] = '0';
                    $clienteitaData['preciocompra'] = '0';
                    $clienteitaData['proveedorasignado'] = 'PROVEEDOR AJENO';
                    $clienteitaData['servicio'] = 'AJENO';
                } else{
                    $clienteitaData['accionid'] = $accionId;
                    $clienteitaData['accionnombre'] = $accionNombre;
                    $clienteitaData['precio'] = $precioAccion;
                    $clienteitaData['preciocompra'] = $preciocompraAccion;
                    $clienteitaData['proveedorasignado'] = $proveedorAsignado;
                    $clienteitaData['servicio'] = $servicio;
                }
                $clienteitaData['fechabateria'] = $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada;
                $clienteitaData['informe'] = $informe;
                $clienteitaData['usuarioid'] = $usuarioID;
                $clienteitaData['usuarioregistro'] = $usuarioNombre;
                $clienteitaData['fechainforme'] = $fechainforme;
                Bateriasubcliente::create($clienteitaData);

                if ($informe === 'SI TIENE INFORME') {
                    Programacionsubcliente::create([
                        'proveedornombre' => 'PROVEEDOR AJENO',
                        'clienteitaid' => $clienteID,
                        'clienteitanombre' => $cliente->nombrecompleto,
                        'fechaasignada' => $fechaActual,
                        'fechabateria' => $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada,
                        'horaasignada' => $horaActual,
                        'horadesde' => $horaActual,
                        'horahasta' => $horaActual,
                        'areanombre' => $accionNombre,
                        'accionnombre' => $accionNombre . ' - PA',
                        'precio' => '0',
                        'usuarioid' => $usuarioID,
                        'usuarioregistro' => $usuarioNombre,
                    ]);
                    Estadoprogramacionsubcliente::create([
                        'clienteitaid' => $clienteID,
                        'clienteitanombre' => $cliente->nombrecompleto,
                        'fechaatencionprogramacion' => $fechaActual,
                        'fechabateria' => $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada,
                        'accionnombre' => $accionNombre . ' - PA',
                        'usuarioid' => $usuarioID,
                        'usuarioregistro' => $usuarioNombre,
                    ]);
                }
            }
        }
        return redirect()->route('admin.asociados.crearbateriaclienteita', ['cliente' => $cliente])->with('info', 'La batería se creó con éxito');
    }

//
//APROBAR COTIZACION DE PROGRAMACION DE CLIENTE ITA
    
    public function aprobacioncotizacionclienteita(Cliente $cliente, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarporfecha');
        $servicioSeleccionado = $request->get('buscarporservicio');
        $areasSeleccionadas = $request->get('buscarporarea', []);

        if (!is_array($areasSeleccionadas)) {
            $areasSeleccionadas = [];
        }
    
        $fechas = Tramitesubcliente::where('clienteitaid', $cliente->id)
                                    ->pluck('fechabateria')
                                    ->unique();
                                    
        $areasPorFecha = BateriaSubCliente::where('clienteitaid', $cliente->id)
                                    ->get()
                                    ->groupBy('fechabateria')
                                    ->map(function ($items) {
                                        return $items->pluck('areanombre')->unique()->values();
                                    });
        
    
        $bateriasubclientes = collect();
        $total = 0;
    
        $query = BateriaSubCliente::where('clienteitaid', $cliente->id);
        if ($fechaSeleccionada) {
            $query->where('fechabateria', $fechaSeleccionada);
        }
        if ($servicioSeleccionado) {
            $query->where('servicio', $servicioSeleccionado);
        }
        if (!empty($areasSeleccionadas)) {
            $query->whereIn('areanombre', $areasSeleccionadas);
        }
    
        if ($fechaSeleccionada || !empty($areasSeleccionadas)) {
            $bateriasubclientes = $query->simplePaginate(1000);
    
            $total = $bateriasubclientes->sum(function ($bateriasubcliente) {
                return str_replace(',', '.', $bateriasubcliente->precio);
            });
            $total = number_format($total, 2, '.', '');
        }
        $id = $cliente->id;
    
        return view('admin.asociados.aprobacioncotizacionclienteita', compact('servicioSeleccionado','bateriasubclientes', 'id', 'cliente', 'fechas', 'total', 'fechaSeleccionada', 'areasPorFecha', 'areasSeleccionadas'));
    }
    public function buscarbateriaclienteita(Cliente $cliente, Request $request)
    {
        return $this->aprobacioncotizacionclienteita($cliente, $request);
    }
    public function generarpdfcotizacionclienteita(Cliente $cliente, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarporfecha');
        $servicioSeleccionado = $request->get('buscarporservicio');
        $areasSeleccionadas = $request->get('buscarporarea', []);
        $total = $request->get('total');
    
        $areasSeleccionadas = is_array($areasSeleccionadas) ? $areasSeleccionadas : explode(',', $areasSeleccionadas);
    
        $query = BateriaSubCliente::where('clienteitaid', $cliente->id);
    
        if ($fechaSeleccionada) {
            $query->where('fechabateria', $fechaSeleccionada);
        }
        if ($servicioSeleccionado) {
            $query->where('servicio', $servicioSeleccionado);
        }
        if (!empty($areasSeleccionadas)) {
            $query->whereIn('areanombre', $areasSeleccionadas);
        }
    
        $bateriasubclientes = $query->get();
    
        if (!$total) {
            $total = $bateriasubclientes->sum(function ($bateriasubcliente) {
                return str_replace(',', '.', $bateriasubcliente->precio);
            });
            $total = number_format($total, 2, '.', '');
        }
    
        // Determina qué vista usar basado en el valor de buscarporservicio
        $vistaPdf = $servicioSeleccionado === 'AJENO'
            ? 'admin.asociados.pdfcotizacionajenoclienteita'
            : 'admin.asociados.pdfcotizacionclienteita';
    
        // Determina el nombre del archivo PDF basado en el valor de buscarporservicio
        $pdfName = $servicioSeleccionado === 'AJENO'
            ? 'Informes_a_presentar_' . $cliente->nombres
            : 'Cotización_' . $cliente->nombres;
    
        if ($cliente->apepaterno) {
            $pdfName .= ' ' . $cliente->apepaterno;
        }
        if ($cliente->apematerno) {
            $pdfName .= ' ' . $cliente->apematerno;
        }
        $pdfName .= '.pdf';
    
        // Genera el PDF
        $pdf = Pdf::loadView($vistaPdf, [
            'cliente' => $cliente,
            'bateriasubclientes' => $bateriasubclientes,
            'total' => $total
        ]);
    
        return $pdf->download($pdfName);
    }
    public function aprobarcotizacionprogramacionclienteita(Cliente $cliente) 
    {
        $nombreCliente = $cliente->nombrecompleto;

        $id = $cliente->nombrecompleto ? Cliente::where('nombrecompleto', $cliente->nombrecompleto)->value('id') : null;

        $fechasRegistradas = EstadoCotizacionSubCliente::where('clienteitaid', $cliente->id)
                                        ->pluck('fechabateria')
                                        ->unique();

        $fechasDisponibles = BateriaSubCliente::where('clienteitaid', $cliente->id)
                                        ->pluck('fechabateria')
                                        ->unique();

        $fechasConFactura = EstadoCotizacionSubCliente::where('clienteitaid', $cliente->id)
                                        ->whereNotNull('nrofactura')
                                        ->pluck('fechabateria')
                                        ->unique();

        $fechas = $fechasDisponibles->filter(function ($fecha) use ($fechasRegistradas) {
            return !$fechasRegistradas->contains($fecha);
        });

        $fechasregis = $fechasDisponibles->filter(function ($fecha) use ($fechasConFactura) {
            return !$fechasConFactura->contains($fecha);
        });

        $documentosPorFecha = EstadoCotizacionSubCliente::where('clienteitaid', $cliente->id)
            ->get(['fechabateria', 'document', 'documentconsinfo'])
            ->groupBy('fechabateria');
        $fecha = '';
        return view('admin.asociados.aprobarcotizacionprogramacionclienteita', compact('fecha','documentosPorFecha','fechasregis','cliente', 'id', 'fechas', 'fechasRegistradas','fechasDisponibles'));
    } 
    public function actualizarPdf(Request $request) 
    {
        // Validar el formulario
        $request->validate([
            'fechabateria' => 'required|date',
            'archivo' => 'required|file|mimes:pdf|max:20480', // max:20MB
        ]);

        // Encuentra el estado de cotización existente
        $estadoCotizacion = EstadoCotizacionSubCliente::where('clienteitaid', $request->clienteitaid)
            ->where('fechabateria', $request->fechabateria)
            ->first();

        // Verifica si el registro existe
        if ($estadoCotizacion) {
            $carpetaCliente = public_path("/cotizacionesaprobadasita/{$request->clienteitaid}");
            
            // Elimina el archivo PDF existente si es necesario
            if ($estadoCotizacion->document) {
                $archivoAntiguo = $carpetaCliente . '/' . $estadoCotizacion->document;
                if (file_exists($archivoAntiguo)) {
                    unlink($archivoAntiguo);
                }
            }
            
            // Guarda el nuevo archivo PDF
            $archivo_name = null;
            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $archivo_name = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name);
            }
            
            // Actualiza el registro existente
            $estadoCotizacion->update([
                'document' => $archivo_name,
                'usuarioid' => auth()->user()->id,
                'usuarioregistro' => auth()->user()->name
            ]);
            
            return redirect()->route('admin.asociados.aprobarcotizacionprogramacionclienteita', $request->clienteitaid)
                ->with('info', 'El documento se actualizó con éxito');
        } else {
            return redirect()->route('admin.asociados.aprobarcotizacionprogramacionclienteita', $request->clienteitaid)
                ->with('error', 'No se encontró el registro para actualizar');
        }
    }
    public function guardaraprobacioncotizacionclienteita(StoreEstadocotizacionsubclienteRequest $request, Cliente $cliente)
    {
        $archivo_name = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $carpetaCliente = public_path("/cotizacionesaprobadasita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }
        $archivo_name2 = null;
        if ($request->hasFile('archivo2')) {
            $file = $request->file('archivo2');
            $carpetaCliente = public_path("/cotizacionesaprobadasita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name2);
        }
        $documentacioncotizacioncliente = Estadocotizacionsubcliente::create([
            'document' => $archivo_name,
            'documentconsinfo' => $archivo_name2,
            'usuarioid' => auth()->user()->id,
            'usuarioregistro' => auth()->user()->name,
            'clienteitaid' => $request->clienteitaid,
            'clienteitanombre' => $request->clienteitanombre,
            'fechabateria' => $request->input('fechabateria'),
        ]);
    
        return redirect()->route('admin.asociados.aprobarcotizacionprogramacionclienteita', $request->cliente)->with('info', 'El documento se subió con éxito');
    }
    public function guardarFacturacotclienteita(Request $request, Cliente $cliente)
        {
            $validated = $request->validate([
                'fechabateriaseleccionada' => 'required|string',
                'nrofactura' => 'required|max:255',
                'clienteitaid' => 'required|max:255',
            ]);
        
            $registro = Estadocotizacionsubcliente::where('fechabateria', $validated['fechabateriaseleccionada'])
            ->where('clienteitaid', $validated['clienteitaid'])
            ->first();
        
            if ($registro) {
                $registro->nrofactura = $validated['nrofactura'];
                $registro->save();
            }
            return redirect()->back()->with('info', 'El nro. de factura de actualizo con éxito.');
        }

//
//CREAR PROGRAMACION Y REPROGRAMACION DE CLIENTE ITA
    public function crearprogramacionclienteita(Cliente $cliente, Request $request) 
    {
        $nombreCliente = $cliente->nombrecompleto;
        $idCliente = $cliente->id;
        $clienteitaid = $cliente->id;
        $sucursalCliente = $cliente->sucursal;
        $rolusuario = auth()->user()->getRoleNames()->first(); 
        $esProveedor = ($rolusuario === 'PROVEEDOR');
        $id = $cliente->nombrecompleto ? Cliente::where('nombrecompleto', $cliente->nombrecompleto)->value('id') : null;

        $accionesCliente = BateriaSubCliente::where('clienteitanombre', $nombreCliente)
            ->whereIn('accionnombre', function ($query) use ($sucursalCliente) {
                $query->select('accionnombre')->from('clientes')->where('sucursal', $sucursalCliente);
            })
            ->pluck('accionnombre')
            ->unique();

        $proveedoresAsignados = BateriaSubCliente::where('clienteitanombre', $nombreCliente)
            ->whereIn('accionnombre', $accionesCliente)
            ->pluck('proveedorasignado', 'accionnombre')
            ->toArray();

        $fechasEnEstadoCotizacionSubCliente = EstadoCotizacionSubCliente::where('clienteitanombre', $nombreCliente)  
            ->distinct()
            ->pluck('fechabateria');
    
        $fechasBateriaPorAccion = BateriaSubCliente::where('clienteitanombre', $nombreCliente)
            ->where(function ($query) use ($fechasEnEstadoCotizacionSubCliente) {
                $query->whereIn('fechabateria', $fechasEnEstadoCotizacionSubCliente)
                    ->orWhere('accionnombre', 'MEDICINA LABORAL');
            })
            ->whereIn('accionnombre', $accionesCliente)
            ->where('accionnombre', '!=', 'INFORME FINAL')
            ->select('accionnombre', 'fechabateria')
            ->get();
        
        $accionesRegistradas = Programacionsubcliente::where('clienteitaid', $idCliente)
            ->pluck('accionnombre', 'fechabateria')
            ->toArray();

        foreach ($accionesRegistradas as $fecha => $accion) {
        if (!isset($accionesRegistradas[$fecha])) {
            $accionesRegistradas[$fecha] = [];
            }

            if (!is_array($accionesRegistradas[$fecha])) {
                $accionesRegistradas[$fecha] = [$accion];
            } else {
                $accionesRegistradas[$fecha][] = $accion;
            }
        }

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $item) {
            $accion = $item->accionnombre;
            $fecha = $item->fechabateria;

            $accionYaRegistrada = Programacionsubcliente::where('clienteitaid', $idCliente)
                ->where('fechabateria', $fecha)
                ->where('accionnombre', $accion)
                ->exists();
        
            if (!isset($accionesPorFecha[$fecha])) {
                $accionesPorFecha[$fecha] = [];
            }

            if (!$accionYaRegistrada) {
                $accionesPorFecha[$fecha][] = $accion;
            }
        }

        
        $proveedoresDetalles = [];
        foreach ($proveedoresAsignados as $accion => $nombreProveedor) {
            $proveedor = BateriaSubCliente::where('accionnombre', $accion)->where('clienteitaid', $idCliente)
                ->latest()
                ->first();

            if ($proveedor) {
                $proveedoresDetalles[$accion] = [
                    'proveedor' => $proveedor->proveedorasignado,
                    'horarioinicial' => $proveedor->horarioinicial,
                    'horariofinal' => $proveedor->horariofinal,
                    'fechabateria' => $proveedor->fechabateria,
                    'fechaasignada' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clienteitanombre', $nombreCliente)
                        ->value('fechaasignada'),
                    'horadesde' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clienteitanombre', $nombreCliente)
                        ->value('horadesde'),
                    'horahasta' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clienteitanombre', $nombreCliente)
                        ->value('horahasta'),
                    'tiempoatencion' => $proveedor->tiempoatencion,
                    'accion' => $proveedor->accionnombre,
                    'area' => $proveedor->areanombre,
                    'precio' => $proveedor->precio,
                    'preciocompra' => $proveedor->preciocompra,
                    'programacionid' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clienteitanombre', $nombreCliente)
                        ->value('id'),
                ];
            }
        }

        $fechasBateria = BateriaSubCliente::where('clienteitaid', $clienteitaid)
            ->distinct()
            ->pluck('fechabateria');


        $accionesPorFechaBateria = [];
        foreach ($fechasBateria as $fecha) {
            $accionesBateria = BateriaSubCliente::where('fechabateria', $fecha)
                ->where('clienteitaid', $clienteitaid)
                ->pluck('accionnombre')
                ->toArray();

            $accionesPorFechaBateria[$fecha] = $accionesBateria;
        }

        $accionesDetallesPorFecha = [];
        foreach ($fechasBateria as $fecha) {
            $accionesProgramadas = ProgramacionSubCliente::where('fechabateria', $fecha)
                ->where('clienteitaid', $clienteitaid)
                ->get(['id', 'accionnombre','proveedornombre', 'fechaasignada', 'horadesde', 'horahasta', 'horahasta', 'precio']);

            foreach ($accionesProgramadas as $accion) {
                $accionesDetallesPorFecha[$fecha][$accion->accionnombre] = $accion;
            }
        }

        // Obtén la URL previa
        $previousUrl = url()->previous();

        // Verifica si la URL previa es diferente a la almacenada y si no es la misma que la URL actual
        if (session('previous_url') !== $previousUrl && $previousUrl !== url()->current()) {
            session(['previous_url' => $previousUrl]);
        }

        return view('admin.asociados.crearprogramacionclienteita', compact('esProveedor','accionesDetallesPorFecha','accionesPorFechaBateria','fechasBateria','id','rolusuario', 'cliente', 'accionesPorFecha', 'proveedoresDetalles', 'accionesRegistradas', 'fechasBateriaPorAccion', 'accionesCliente'));
    }
    /* public function guardarprogramacionclienteita(StoreProgramacionsubclienteRequest $request)
    {
        $proveedoresSeleccionados = $request->input('proveedornombre');
        $horaasignada = $request->input('horaasignada');
        $fechaasignada = $request->input('fechaasignada');
        $accionnombre = $request->input('accionnombre');
        $precio = $request->input('precio');
        $preciocompra = $request->input('preciocompra');

        if (!is_array($proveedoresSeleccionados)) {
            $proveedoresSeleccionados = [$proveedoresSeleccionados];
        }
        foreach ($proveedoresSeleccionados as $proveedor) {
                $existente = Programacionsubcliente::where('horaasignada', $horaasignada)
                ->where('accionnombre', $accionnombre)
                ->where('proveedornombre', $proveedor)
                ->where('fechaasignada', $fechaasignada)
                ->exists();

            $clienteitaData = $request->except(['proveedornombre', '_token']);
            $clienteitaData['proveedornombre'] = $proveedor;

            $idCliente = $request->input('clienteitaid');
            $cliente = Cliente::findOrFail($idCliente);
            $clienteID = $cliente->id;

            $clienteitaData['clienteitaid'] = $clienteID;
            $clienteitaData['accionnombre'] = $accionnombre;
            $clienteitaData['horaasignada'] = $horaasignada;
            $clienteitaData['clienteitanombre'] = $request->input('nombrecompleto');
            $clienteitaData['fechaasignada'] = $fechaasignada;
            $clienteitaData['precio'] = $precio;
            $clienteitaData['preciocompra'] = $preciocompra;
            $clienteitaData['fechabateria'] = $request->input('fechabateria');

            $cliente = Programacionsubcliente::create($clienteitaData);
        }

        return redirect()->route('admin.asociados.crearprogramacionclienteita', $request->cliente)->with('info', 'La programacion del cliente se creo con éxito');
    } */
    public function guardarprogramacionclienteita(StoreProgramacionsubclienteRequest $request)
    {
        // Recoge las acciones seleccionadas
        $accionesSeleccionadas = $request->input('accionesSeleccionadas', []);
        $horaasignada = $request->input('horaasignada');
        $fechaasignada = $request->input('fechaasignada');
        $clienteitaid = $request->input('clienteitaid');
        $clienteitanombre = $request->input('clienteitanombre');
        $fechabateria = $request->input('fechabateria');
        $horadesde = $request->input('horadesde');
        $horahasta = $request->input('horahasta');

        foreach ($accionesSeleccionadas as $accion) {
            // Sanitiza el nombre de la acción
            $accionSanitizada = str_replace([' ', '.'], ['_', '-'], $accion);
            
            // Captura los datos específicos de cada acción
            $proveedornombre = $request->input("proveedor_$accionSanitizada");
            $areanombre = $request->input("areanombre_$accionSanitizada");
            $precio = $request->input("precio_$accionSanitizada");
            $preciocompra = $request->input("preciocompra_$accionSanitizada");

            // Verifica si ya existe la programación
            $existente = Programacionsubcliente::where('accionnombre', $accion)
                ->where('fechabateria', $fechabateria)
                ->where('clienteitaid', $clienteitaid)
                ->exists();

            // Solo crea un nuevo registro si no existe
            if (!$existente) {
                Programacionsubcliente::create([
                    'accionnombre' => $accion,
                    'horaasignada' => $horaasignada,
                    'fechaasignada' => $fechaasignada,
                    'proveedornombre' => $proveedornombre,
                    'clienteitaid' => $clienteitaid,
                    'clienteitanombre' => $clienteitanombre,
                    'horadesde' => $horadesde,
                    'horahasta' => $horahasta,
                    'fechabateria' => $fechabateria,
                    'areanombre' => $areanombre,
                    'precio' => $precio,
                    'preciocompra' => $preciocompra,
                    'usuarioid' => Auth::id(), // ID del usuario autenticado
                    'usuarioregistro' => Auth::user()->name, // Nombre del usuario autenticado
                ]);
            }
        }

        return redirect()->route('admin.asociados.crearprogramacionclienteita', $request->cliente)->with('info', 'La programación del cliente se creó con éxito');
    }
    public function reprogramacionclienteita(Cliente $cliente, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarpor');
        $fechas = ProgramacionSubCliente::where('clienteitaid', $cliente->id)
                                    ->pluck('fechabateria')
                                    ->unique();
        $programacionsubclientes = collect();
        
        $reprogramaciones = ProgramacionSubCliente::where('clienteitaid', $cliente->id)
        ->onlyTrashed()
        ->get();
        $total = 0;
        if ($fechaSeleccionada) {
            $programacionsubclientes = ProgramacionSubCliente::where('clienteitaid', $cliente->id)
                                                    ->where('fechabateria', $fechaSeleccionada)
                                                    ->simplePaginate(1000);
            $total = $programacionsubclientes->sum(function ($programacionsubcliente) {
                return str_replace(',', '.', $programacionsubcliente->precio);
            });
            $total = number_format($total, 2, '.', '');
        }

        $proveedorprogramacion = Proveedor::orderBy('proveedor')->pluck('proveedor', 'proveedor');

        $proveedorprogramacion->put('PROVEEDOR AJENO', 'PROVEEDOR AJENO');

        $id = Cliente::where('nombrecompleto', $cliente->nombrecompleto)->value('id');
        return view('admin.asociados.reprogramacionclienteita', compact('proveedorprogramacion', 'reprogramaciones', 'programacionsubclientes', 'id', 'cliente', 'fechas', 'total', 'fechaSeleccionada'));
    }
    public function buscarprogramacionclienteita(Cliente $cliente, Request $request)
    {
        return $this->reprogramacionclienteita($cliente, $request);
    }
    public function guardarreprogramacionclienteita(Request $request, Programacionsubcliente $programacionsubcliente)
    {
        $request->validate([
            'motivoreprogramacion' => 'required|string|max:255',
            'fechaasignada' => 'required|date',
            'horadesde' => 'required|date_format:H:i',
            'horahasta' => 'required|date_format:H:i',
            'fechaasignada' => 'required|date',
            'horadesde' => 'required|date_format:H:i',
            'horahasta' => 'required|date_format:H:i',
            'usuarioactualizacion' => 'required|string',
            'proveedornombre' => 'required|string',
            'proveedorajeno' => 'nullable|string|max:255',
        ]);

        // Validar si es "Proveedor Ajeno"
        $proveedornombre = $request->proveedornombre === 'PROVEEDOR AJENO'
            ? $request->proveedorajeno
            : $request->proveedornombre;

        $usuarioActualizacion = $request->input('usuarioactualizacion');
        $programacionsubcliente->motivoreprogramacion = $request->motivoreprogramacion;
        $programacionsubcliente->usuarioactualizacion = $usuarioActualizacion;
        $programacionsubcliente->save();

        $programacionsubcliente->delete();

        $nuevaReprogramacion = $programacionsubcliente->replicate(['deleted_at']);
        $nuevaReprogramacion->motivoreprogramacion = null;
        $nuevaReprogramacion->fechaasignada = $request->fechaasignada;
        $nuevaReprogramacion->horadesde = $request->horadesde;
        $nuevaReprogramacion->horahasta = $request->horahasta;
        $nuevaReprogramacion->proveedornombre = $proveedornombre;

        $nuevaReprogramacion->save();

        $cliente = Cliente::where('nombrecompleto', $programacionsubcliente->clienteitanombre)->first();
        $fechaSeleccionada = $request->input('buscarpor');

        return redirect()->route('admin.asociados.reprogramacionclienteita', ['cliente' => $cliente, 'buscarpor' => $fechaSeleccionada])
            ->with('eliminar', 'ok');
    }
    public function estadoprogramacionclienteita(Cliente $cliente, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarpor');
        
        $fechas = Programacionsubcliente::where('clienteitaid', $cliente->id)
                                    ->pluck('fechabateria')
                                    ->unique();

        $accionesDisponibles = collect();
        
        if ($fechaSeleccionada) {
            $accionesDisponibles = ProgramacionSubCliente::where('clienteitaid', $cliente->id)
                                                    ->where('fechabateria', $fechaSeleccionada)
                                                    ->simplePaginate(1000);
        }
        $IDCliente = $cliente->id;
        $accionesCliente = BateriaSubCliente::where('clienteitaid', $IDCliente)->pluck('accionnombre')->toArray();
        $id = $cliente->nombrecompleto ? Cliente::where('nombrecompleto', $cliente->nombrecompleto)->value('id') : null;
        $nombreclienteita = $cliente->nombrecompleto ? Cliente::where('nombrecompleto', $cliente->nombrecompleto)->value('nombrecompleto') : null;

        $accionesPorArea = Programacionsubcliente::where('clienteitaid', $IDCliente)
            ->get(['accionnombre', 'proveedornombre','fechabateria','fechaasignada', 'horadesde', 'horahasta']);

        $estadoRegistrados = Estadoprogramacionsubcliente::where('clienteitaid', $IDCliente)
                ->get(['accionnombre', 'fechabateria']);

        $estadoMapeado = [];
            foreach ($estadoRegistrados as $estado) {
                $estadoMapeado[$estado->accionnombre][$estado->fechabateria] = true;
            }

        $accionesDisponibles = $accionesDisponibles ?? $accionesPorArea;
        
        $accionesRegistradas = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clienteitaid', $IDCliente)
            ->pluck('accionnombre')
            ->toArray();

        $fechasEnEstadoCotizacionSubCliente = EstadoCotizacionSubCliente::where('clienteitaid', $IDCliente)
            ->distinct()
            ->pluck('fechabateria');

        $fechasBateriaPorAccion = Programacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clienteitaid', $IDCliente)
            /* ->whereIn('fechabateria', $fechasEnEstadoCotizacionSubCliente) */
            ->distinct()
            ->pluck('fechabateria', 'accionnombre');

        $accionesPorFecha = Programacionsubcliente::where('clienteitaid', $IDCliente)
            ->where('fechabateria', $fechaSeleccionada)
            ->get(['accionnombre'])
            ->pluck('accionnombre')
            ->toArray();
        $accionesNoRegistradas = array_filter($accionesPorFecha, function ($accion) use ($estadoMapeado, $fechaSeleccionada) {
                return empty($estadoMapeado[$accion][$fechaSeleccionada]);
            });
            
        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accion => $fecha) {
        $accionesPorFecha[$fecha][] = $accion;
        }
        
        foreach ($accionesDisponibles as $accion) {
            $bateriaProveedor = BateriaProveedor::where('proveedor', $accion->proveedornombre)->first();
            $proveedor = Proveedor::where('proveedor', $accion->proveedornombre)->first();

            if ($bateriaProveedor && $bateriaProveedor->servicio === 'EXTERNO' && $proveedor) {
                $accion->direccion = $proveedor->direccion;
                $accion->direccion2 = $proveedor->direccion2;
                $accion->direccion3 = $proveedor->direccion3;
            } else {
                $accion->direccion = 'GOOD LIFE SRL';
                $accion->direccion2 = '';
                $accion->direccion3 = '';
            }
            if ($bateriaProveedor && $bateriaProveedor->servicio === 'EXTERNO' && $proveedor) {
                $accion->linkubicacion = $proveedor->linkubicacion;
                $accion->linkubicacion2 = $proveedor->linkubicacion2;
                $accion->linkubicacion3 = $proveedor->linkubicacion3;
            } else {
                $accion->linkubicacion = '';
                $accion->linkubicacion2 = '';
                $accion->linkubicacion3 = '';

            }
            if ($bateriaProveedor && $bateriaProveedor->servicio === 'INTERNO' && $proveedor) {
                if ($bateriaProveedor->sucursal === 'SANTA CRUZ') {
                    $accion->linkubicacion = 'https://maps.app.goo.gl/8Ye9G5fUDrLGjueNA';
                } elseif ($bateriaProveedor->sucursal === 'COCHABAMBA') {
                    $accion->linkubicacion = 'https://maps.app.goo.gl/aXPo8s2T3QB6NoH47';
                } else {
                    $accion->linkubicacion = '';
                }
            } else {
                $accion->linkubicacion = '';
            }
            
        }

        $id = Cliente::where('nombrecompleto', $cliente->nombrecompleto)->value('id');

        return view('admin.asociados.estadoprogramacionclienteita', compact('accionesNoRegistradas','estadoMapeado','fechaSeleccionada', 'id','fechas','nombreclienteita','accionesDisponibles', 'cliente', 'id', 'accionesCliente', 'estadoRegistrados', 'fechasBateriaPorAccion', 'accionesPorFecha', 'accionesRegistradas'));
    }
    public function buscarprogramacionclientesita(Cliente $cliente, Request $request)
    {
        return $this->estadoprogramacionclienteita($cliente, $request);
    }
    public function generarpdfprogramacionclienteita(Cliente $cliente, Request $request) 
    {
        $fechaSeleccionada = $request->get('buscarpor');

        $accionesDisponibles = Programacionsubcliente::where('clienteitaid', $cliente->id)
                        ->when($fechaSeleccionada, function ($query) use ($fechaSeleccionada) {
                            return $query->where('fechabateria', $fechaSeleccionada);
                        })
                        ->get();
        
        $fechabateria = $fechaSeleccionada;
                    
        $pdf = PDF::loadView('admin.asociados.pdfprogramacionclienteita', compact('fechabateria','cliente', 'accionesDisponibles'));
        $pdfName = 'Programación_' . $cliente->nombres;
        if ($cliente->apepaterno) {
            $pdfName .= ' ' . $cliente->apepaterno;
        }
        if ($cliente->apematerno) {
            $pdfName .= ' ' . $cliente->apematerno;
        }
        $pdfName .= '.pdf';
        
        return $pdf->download($pdfName);
    }
    /* public function guardarestadoprogramacionclienteita(StoreEstadoprogramacionsubclienteRequest $request)
    {
        $accionNombre = $request->input('accionnombre');

        $estadoprogramacioncliente = Estadoprogramacionsubcliente::create(
            $request->except('accionid') + [
                'accionnombre' => $accionNombre
            ]
        );
        return redirect()->route('admin.asociados.estadoprogramacionclienteita', $request->cliente)->with('info', 'El estado se actualizó con éxito');
    } */
    /* public function guardarestadoprogramacionclienteita(StoreEstadoprogramacionsubclienteRequest $request) 
    {
        $accionesSeleccionadas = $request->input('accionesSeleccionadas', []);
        $fechaBateria = $request->input('fechabateria'); // Obtiene la fecha de batería del input oculto

        foreach ($accionesSeleccionadas as $accionNombre) {
            Estadoprogramacionsubcliente::create(
                $request->except('accionid') + [
                    'accionnombre' => $accionNombre,
                    'fechabateria' => $fechaBateria // Asegúrate de incluir la fecha aquí
                ]
            );
        }

        return redirect()->route('admin.asociados.estadoprogramacionclienteita', $request->cliente)
                        ->with('info', 'El estado se actualizó con éxito');
    } */
    public function guardarestadoprogramacionclienteita(StoreEstadoprogramacionsubclienteRequest $request) 
    {
        $accionesSeleccionadas = $request->input('accionesSeleccionadas', []);
        $fechaBateria = $request->input('fechabateria'); // Obtiene la fecha de batería del input oculto
    
        foreach ($accionesSeleccionadas as $accionNombre) {
            Estadoprogramacionsubcliente::create(
                $request->except('accionid') + [
                    'accionnombre' => $accionNombre,
                    'fechabateria' => $fechaBateria // Asegúrate de incluir la fecha aquí
                ]
            );
        }
    
        // Redirige a la vista con la fecha seleccionada
        return redirect()->route('admin.asociados.estadoprogramacionclienteita', [
            'cliente' => $request->cliente,
            'buscarpor' => $fechaBateria // Incluye la fecha en la redirección
        ])->with('info', 'El estado se actualizó con éxito');
    }
    public function actualizarProveedorFecha(Cliente $cliente, Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer',
            'proveedor' => 'required|string',
            'fechaasignada' => 'required|date',
        ]);

        $id = $data['id'];
        $proveedorNombre = $data['proveedor'];
        $fechaAsignada = $data['fechaasignada'];

        // Actualizar en programacionsubclientes
        $programacion = Programacionsubcliente::find($id);
        if ($programacion) {
            $programacion->proveedornombre = $proveedorNombre;
            $programacion->fechaasignada = $fechaAsignada;
            $programacion->save();

            // Actualizar en estadoprogramacionsubclientes
            $estadoProgramacion = Estadoprogramacionsubcliente::where('clienteitaid', $programacion->clienteitaid)
                ->where('fechabateria', $programacion->fechabateria)
                ->where('accionnombre', $programacion->accionnombre)
                ->first();
            if ($estadoProgramacion) {
                $estadoProgramacion->fechaatencionprogramacion = $fechaAsignada;
                $estadoProgramacion->save();
            }

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }

//
//CREAR DOCUMENTACION DE CLIENTE ITA
    public function creardocumentacionclienteita(Cliente $cliente, Asociado $asociado)
    {
        $IDcliente = $cliente->id;
        $rolusuario = auth()->user()->getRoleNames()->first();
        $accionesCliente = Programacionsubcliente::where('clienteitaid', $IDcliente)
            ->pluck('accionnombre')
            ->unique();

        /* $accionesRegistradasPorFecha = Documentacionsubcliente::where('clienteitaid', $IDcliente)
            ->get(['accion', 'fechabateria'])
            ->groupBy('fechabateria');

        $accionesNoRegistradasPorFecha = Estadoprogramacionsubcliente::where('clienteitaid', $IDcliente)
            ->get(['accionnombre', 'fechabateria'])
            ->filter(function($accion) use ($accionesRegistradasPorFecha) {
                $fechabateria = $accion->fechabateria;
                $accionnombre = $accion->accionnombre;
                return !isset($accionesRegistradasPorFecha[$fechabateria]) || !in_array($accionnombre, $accionesRegistradasPorFecha[$fechabateria]->pluck('accion')->toArray());
            })
            ->groupBy('fechabateria'); */

        $usuario = Auth::user();

        $accionesRegistradasPorFecha = Documentacionsubcliente::where('clienteitaid', $IDcliente)
            ->get(['accion', 'fechabateria'])
            ->groupBy('fechabateria');

        $accionesNoRegistradasPorFecha = Estadoprogramacionsubcliente::join('programacionsubclientes', function ($join) {
                $join->on('estadoprogramacionsubclientes.accionnombre', '=', 'programacionsubclientes.accionnombre')
                    ->on('estadoprogramacionsubclientes.clienteitaid', '=', 'programacionsubclientes.clienteitaid')
                    ->on('estadoprogramacionsubclientes.fechabateria', '=', 'programacionsubclientes.fechabateria');
            })
            ->where('estadoprogramacionsubclientes.clienteitaid', $IDcliente)
            ->whereNull('estadoprogramacionsubclientes.deleted_at')
            ->whereNull('programacionsubclientes.deleted_at')
            ->when($usuario->hasRole('PROVEEDOR'), function ($query) use ($usuario) {

                return $query->where('programacionsubclientes.proveedornombre', $usuario->name);
            })
            ->get(['estadoprogramacionsubclientes.accionnombre', 'estadoprogramacionsubclientes.fechabateria'])
            ->filter(function ($accion) use ($accionesRegistradasPorFecha) {
                $fechabateria = $accion->fechabateria;
                $accionnombre = $accion->accionnombre;
                return !isset($accionesRegistradasPorFecha[$fechabateria]) || !in_array($accionnombre, $accionesRegistradasPorFecha[$fechabateria]->pluck('accion')->toArray());
            })
            ->groupBy('fechabateria');

        $accionesRegistradas = Documentacionsubcliente::whereIn('accion', $accionesCliente)
            ->where('clienteitaid', $IDcliente)
            ->pluck('accion')
            ->toArray();

        $id = $cliente->nombrecompleto ? Cliente::where('nombrecompleto', $cliente->nombrecompleto)->value('id') : null;

        $fechasBateriaPorAccion = Programacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clienteitaid', $IDcliente)
            ->get(['accionnombre', 'fechabateria', 'proveedornombre'])
            ->groupBy('fechabateria');
        
        $accionesEnEstado = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clienteitaid', $IDcliente)
            ->pluck('accionnombre')
            ->toArray();
        $documentosRegistrados = Documentacionsubcliente::whereIn('accion', $accionesCliente)
            ->where('clienteitaid', $IDcliente)
            ->pluck('accion')->toArray();

        $accionesPorFecha = [];

        foreach ($fechasBateriaPorAccion as $fecha => $acciones) {
            foreach ($acciones as $accion) {
                $accionesPorFecha[$fecha][] = $accion;
            }
        }
        
        $documentosRegistradosPorFecha = Documentacionsubcliente::where('clienteitaid', $IDcliente)
            ->get(['accion', 'fechabateria'])
            ->groupBy('fechabateria');

        $accionesPorFecha2 = Programacionsubcliente::where('clienteitaid', $IDcliente)
            ->get(['accionnombre', 'fechabateria'])
            ->groupBy('fechabateria');

        $accionesConEstadoPorFecha = [];
        foreach ($accionesPorFecha as $fecha => $acciones) {
            foreach ($acciones as $accion) {
                $registrado = isset($documentosRegistradosPorFecha[$fecha]) && 
                            in_array($accion->accionnombre, $documentosRegistradosPorFecha[$fecha]->pluck('accion')->toArray());

                $documento = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clienteitaid', $IDcliente)
                                                        ->value('document') : null;

                $image = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clienteitaid', $IDcliente)
                                                        ->value('image') : null;

                $image2 = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clienteitaid', $IDcliente)
                                                        ->value('image2') : null;
                $id = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clienteitaid', $IDcliente)
                                                        ->value('id') : null;

                $creacionregistro = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre) 
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clienteitaid', $IDcliente)
                                                        ->value('created_at') : null;
                if ($creacionregistro) {
                    $creacionregistro = \Carbon\Carbon::parse($creacionregistro);
                    $creacionregistroFormatted = $creacionregistro->format('Y-m-d') . ' - ' . $creacionregistro->format('H:i:s');
                } else {
                    $creacionregistroFormatted = null;
                }

                $proveedor = $accion->proveedornombre;

                $accionesConEstadoPorFecha[$fecha][] = [
                    'id' => $id,
                    'accionnombre' => $accion->accionnombre,
                    'proveedornombre' => $proveedor,
                    'registrado' => $registrado,
                    'document' => $documento,
                    'image' => $image,
                    'image2' => $image2,
                    'creacionregistro' => $creacionregistroFormatted
                ];
            }
        }

        return view('admin.asociados.creardocumentacionclienteita', compact('rolusuario','accionesConEstadoPorFecha','accionesRegistradasPorFecha','accionesNoRegistradasPorFecha','asociado', 'accionesEnEstado','id', 'cliente', 'accionesPorFecha', 'accionesRegistradas', 'fechasBateriaPorAccion', 'accionesCliente', 'documentosRegistrados'));
    }
    /* public function guardardocumentacionclienteita(StoreDocumentacionsubclienteRequest $request, Cliente $cliente)
    {
        $archivo_name = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            
            $carpetaCliente = public_path("/documentacionclientesita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }
        
        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $carpetaCliente = public_path("/documentacionclientesita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name);
        }

        $image_name2 = null;
        if ($request->hasFile('picture2')) {
            $file = $request->file('picture2');
            $carpetaCliente = public_path("/documentacionclientesita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $image_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name2);
        }

        $accionNombre = Programacionsubcliente::where('id', $request->accion)->value('accionnombre');
        $accion = $request->input('accion');
        $nombrecliente = $request->input('nombrecompleto');
        $idcliente = $request->input('clienteitaid');
        $documentacioncliente = Documentacionsubcliente::create(
            $request->except('accion') + [
                'document' => $archivo_name,
                'accion' => $accion,
                'clienteitaid' => $idcliente,
                'clienteitanombre' => $nombrecliente,
                'image' => $image_name,
                'image2' => $image_name2
            ]
        );
        return redirect()->route('admin.asociados.creardocumentacionclienteita', $request->cliente)->with('info', 'El documento se subió con éxito');
    } */
    public function guardardocumentacionclienteita(StoreDocumentacionsubclienteRequest $request, Cliente $cliente) 
    {
        $archivo_name = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $carpetaCliente = public_path("/documentacionclientesita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }

        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $carpetaCliente = public_path("/documentacionclientesita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name);
        }

        $image_name2 = null;
        if ($request->hasFile('picture2')) {
            $file = $request->file('picture2');
            $carpetaCliente = public_path("/documentacionclientesita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $image_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name2);
        }

        $nombrecliente = $request->input('nombrecompleto');
        $idcliente = $request->input('clienteitaid');

        // Iterar sobre las acciones seleccionadas (enviadas como array)
        $accionesSeleccionadas = $request->input('acciones', []); // 'acciones' viene de los checkboxes
        
        foreach ($accionesSeleccionadas as $accionId) {
            $accionNombre = Programacionsubcliente::where('id', $accionId)->value('accionnombre');

            // Guardar cada acción con el mismo PDF e imágenes
            Documentacionsubcliente::create(
                $request->except('acciones') + [
                    'document' => $archivo_name,
                    'accion' => $accionId,  // Guardar el ID de la acción
                    'accionnombre' => $accionNombre,  // Guardar el nombre de la acción (opcional)
                    'clienteitaid' => $idcliente,
                    'clienteitanombre' => $nombrecliente,
                    'image' => $image_name,
                    'image2' => $image_name2
                ]
            );
        }

        return redirect()->route('admin.asociados.creardocumentacionclienteita', $request->cliente)->with('info', 'El documento se subió con éxito');
    }
    public function guardardocumentacionclienteitadeproveedor(StoreDocumentacionsubclienteRequest $request, Cliente $cliente)
    {
        $archivo_name = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            
            $carpetaCliente = public_path("/documentacionclientesita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }
        
        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $carpetaCliente = public_path("/documentacionclientesita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name);
        }

        $image_name2 = null;
        if ($request->hasFile('picture2')) {
            $file = $request->file('picture2');
            $carpetaCliente = public_path("/documentacionclientesita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $image_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name2);
        }

        $accionNombre = Programacionsubcliente::where('id', $request->accion)->value('accionnombre');
        $accion = $request->input('accion');
        $nombrecliente = $request->input('nombrecompleto');
        $documentacioncliente = Documentacionsubcliente::create(
            $request->except('accion') + [
                'document' => $archivo_name,
                'accion' => $accion,
                'clienteitanombre' => $nombrecliente,
                'image' => $image_name,
                'image2' => $image_name2
            ]
        );
        return redirect()->route('admin.informesfinales.reservasmedicas', $request->cliente)->with('info', 'El documento se subió con éxito');
    }
    public function listadodocumentacionclienteita(Cliente $cliente, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarpor');
        $fechas = Documentacionsubcliente::where('clienteitaid', $cliente->id)
                                    ->pluck('fechabateria')
                                    ->unique();
        $documentacionclientes = collect();
        if ($fechaSeleccionada) {
            $documentacionclientes = Documentacionsubcliente::where('clienteitaid', $cliente->id)
                                                    ->where('fechabateria', $fechaSeleccionada)
                                                    ->simplePaginate(1000);
        }
        /* $documentacionclientes = Documentacionsubcliente::where('clienteitanombre', $cliente->nombrecompleto)->get(); */

        $id = Cliente::where('nombrecompleto', $cliente->nombrecompleto)->value('id');
        return view('admin.asociados.listadodocumentacionclienteita', compact('id','fechas','fechaSeleccionada','cliente', 'documentacionclientes'));
    }
    public function buscardocumentoclienteita(Cliente $cliente, Request $request)
    {
        return $this->listadodocumentacionclienteita($cliente, $request);
    }
    public function documentacionmultipleclienteita(Request $request, Asociado $asociado, Cliente $cliente)
    {
        $proveedor = $request->get('buscarpor');

        $clientes = Programacionsubcliente::where('proveedornombre', 'LIKE', "%$proveedor%")
            ->whereIn('accionnombre', function ($query) use ($proveedor) {
                $query->select('accionnombre')
                    ->from('estadoprogramacionsubclientes')
                    ->where('proveedornombre', 'LIKE', "%$proveedor%");
            })
            ->whereNotNull('clienteitaid')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('documentacionsubclientes')
                    ->whereRaw('documentacionsubclientes.clienteitaid = programacionsubclientes.clienteitaid')
                    ->whereRaw('documentacionsubclientes.accion = programacionsubclientes.accionnombre')
                    ->whereRaw('documentacionsubclientes.fechabateria = programacionsubclientes.fechabateria');
            })
            ->orderBy('proveedornombre')
            ->simplePaginate(10000);

        return view('admin.asociados.documentacionmultipleclienteita', compact('cliente', 'asociado', 'clientes'));
    }
    /* public function guardarhistoriamedica(StoreDocumentacionsubclienteRequest $request, Cliente $cliente)
    {
        $archivo_name = null;
        $archivo_comprimido_name = null;

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            
            $carpetaCliente = public_path("/historiamedica/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            
            // Nombre del archivo PDF
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);

            // Crear un archivo ZIP para comprimir el PDF
            $zip = new \ZipArchive();
            $archivo_comprimido_name = 'HISTORIA_MEDICA_' . $cliente->nombrecompleto . '.zip';
            $zip_path = $carpetaCliente . '/' . $archivo_comprimido_name;

            if ($zip->open($zip_path, \ZipArchive::CREATE) === TRUE) {
                $zip->addFile($carpetaCliente . '/' . $archivo_name, $archivo_name);
                $zip->close();
            } else {
                return redirect()->back()->with('error', 'No se pudo crear el archivo comprimido');
            }

            // Eliminar el archivo PDF original después de comprimirlo
            unlink($carpetaCliente . '/' . $archivo_name);
        }

        $accionNombre = Programacionsubcliente::where('id', $request->accion)->value('accionnombre');
        $accion = $request->input('accion');
        $clienteitaid = $request->input('usuarioid');
        $clienteitanombre = $request->input('usuarioregistro');

        $documentacioncliente = Documentacionsubcliente::create(
            $request->except('accion') + [
                'document' => $archivo_comprimido_name,
                'accion' => $accion,
                'usuarioregistro' => $clienteitanombre,
                'usuarioid' => $clienteitaid,
                'clienteitaid' => $cliente->id,
                'clienteitanombre' => $cliente->nombrecompleto
            ]
        );

        return redirect()->route('admin.asociados.verclienteita', $request->cliente)->with('info', 'El documento se subió con éxito');
    } */
    public function guardarhistoriamedica(StoreDocumentacionsubclienteRequest $request, Cliente $cliente) 
    {
        $archivo_name = null;
        $archivo_comprimido_name = null;
    
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            
            $carpetaCliente = public_path("/historiamedica/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            
            // Nombre del archivo PDF
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
    
            // Crear un archivo ZIP para comprimir el PDF
            $zip = new \ZipArchive();
            $archivo_comprimido_name = 'HISTORIA_MEDICA_' . $cliente->nombrecompleto . '.zip';
            $zip_path = $carpetaCliente . '/' . $archivo_comprimido_name;
    
            if ($zip->open($zip_path, \ZipArchive::CREATE) === TRUE) {
                $zip->addFile($carpetaCliente . '/' . $archivo_name, $archivo_name);
                $zip->close();
            } else {
                return redirect()->back()->with('error', 'No se pudo crear el archivo comprimido');
            }
    
            // Eliminar el archivo PDF original después de comprimirlo
            unlink($carpetaCliente . '/' . $archivo_name);
    
            // Descomprimir el archivo en la carpeta `extracted`
            $extractPath = $carpetaCliente . '/extracted';
            if (!file_exists($extractPath)) {
                mkdir($extractPath, 0755, true);
            }
    
            $zip = new \ZipArchive();
            if ($zip->open($zip_path) === TRUE) {
                $zip->extractTo($extractPath);
                $zip->close();
    
                // Obtener el nombre del archivo descomprimido
                $archivosDescomprimidos = scandir($extractPath);
                $archivoPDFDescomprimido = null;
                foreach ($archivosDescomprimidos as $archivo) {
                    if ($archivo !== '.' && $archivo !== '..' && pathinfo($archivo, PATHINFO_EXTENSION) === 'pdf') {
                        $archivoPDFDescomprimido = $archivo;
                        break;
                    }
                }
    
                if ($archivoPDFDescomprimido === null) {
                    return redirect()->back()->with('error', 'No se encontró un archivo PDF en el ZIP');
                }
            } else {
                return redirect()->back()->with('error', 'No se pudo descomprimir el archivo');
            }
        }
    
        $accionNombre = Programacionsubcliente::where('id', $request->accion)->value('accionnombre');
        $accion = $request->input('accion');
        $clienteitaid = $request->input('usuarioid');
        $clienteitanombre = $request->input('usuarioregistro');
    
        $documentacioncliente = Documentacionsubcliente::create(
            $request->except('accion') + [
                'documentfirmado' => $archivo_comprimido_name,
                'document' => $archivoPDFDescomprimido, // Guarda el nombre del archivo PDF descomprimido
                'accion' => $accion,
                'usuarioregistro' => $clienteitanombre,
                'usuarioid' => $clienteitaid,
                'clienteitaid' => $cliente->id,
                'clienteitanombre' => $cliente->nombrecompleto
            ]
        );
    
        return redirect()->route('admin.asociados.verclienteita', $request->cliente)->with('info', 'El documento se subió con éxito');
    }
    
    public function verDocumento($id)
    {
        $documentacion = Documentacionsubcliente::find($id);
        $carpetaCliente = public_path("/historiamedica/{$documentacion->clienteitaid}");
        $zip_path = $carpetaCliente . '/' . $documentacion->document;

        if (file_exists($zip_path)) {
            $zip = new \ZipArchive();
            if ($zip->open($zip_path) === TRUE) {
                $extract_path = $carpetaCliente . '/extracted/';
                if (!file_exists($extract_path)) {
                    mkdir($extract_path, 0755, true);
                }
                $zip->extractTo($extract_path);
                $zip->close();

                // Asumiendo que el ZIP contiene solo un archivo PDF
                $files = scandir($extract_path);
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                        $pdf_path = $extract_path . $file;
                        return response()->file($pdf_path);
                    }
                }
            }
        }

        return redirect()->back()->with('error', 'El documento no se encontró');
    }
//
//CREAR FORMULARIO DE CLIENTE ITA
    public function crearformularioclienteita(Cliente $cliente)
    {
        /* $generoCliente = $clienteauditoria->genero; */
        $userRole = auth()->user()->getRoleNames()->first(); 
        return view('admin.asociados.crearformularioclienteita', compact('cliente', 'userRole'));
    }
    public function generarpdfclienteita(Cliente $cliente, Request $request) 
    {
       
        $request->validate([
            'fechaatencion' => 'date',
            'antecedentespatologicos' => '',
            //IDENTIFICACION DE PELIGROS
                'preguntas.4.respuesta' => 'nullable|string','detpe4' => 'nullable|string',
                'preguntas.5.respuesta' => 'nullable|string','detpe5' => 'nullable|string',
                'preguntas.6.respuesta' => 'nullable|string','detpe6' => 'nullable|string',
                'preguntas.7.respuesta' => 'nullable|string','detpe7' => 'nullable|string',
                'preguntas.8.respuesta' => 'nullable|string','detpe8' => 'nullable|string',
                'preguntas.9.respuesta' => 'nullable|string','detpe9' => 'nullable|string',
                'preguntas.10.respuesta' => 'nullable|string','detpe10' => 'nullable|string',
                'preguntas.11.respuesta' => 'nullable|string','detpe11' => 'nullable|string',
                'otros' => '',
            //
            //OFTALMOLOGIA
                'preguntas.001.respuesta' => 'nullable|string','hacecuanto001' => 'nullable|string','periodotipo001' => 'nullable|string',
                'preguntas.002.respuesta' => 'nullable|string','hacecuanto002' => 'nullable|string','periodotipo002' => 'nullable|string',
                'preguntas.003.respuesta' => 'nullable|string','hacecuanto003' => 'nullable|string','periodotipo003' => 'nullable|string',
                'preguntas.004.respuesta' => 'nullable|string','hacecuanto004' => 'nullable|string','periodotipo004' => 'nullable|string',
                'preguntas.005.respuesta' => 'nullable|string','hacecuanto005' => 'nullable|string','periodotipo005' => 'nullable|string',
                'preguntas.006.respuesta' => 'nullable|string','hacecuanto006' => 'nullable|string','periodotipo006' => 'nullable|string',
            //
            //OTORRINOLARINGOLOGIA
                'preguntas.007.respuesta' => 'nullable|string','hacecuanto007' => 'nullable|string','periodotipo007' => 'nullable|string',
                'preguntas.008.respuesta' => 'nullable|string','hacecuanto008' => 'nullable|string','periodotipo008' => 'nullable|string',
                'preguntas.009.respuesta' => 'nullable|string','hacecuanto009' => 'nullable|string','periodotipo009' => 'nullable|string',
                'preguntas.010.respuesta' => 'nullable|string','hacecuanto010' => 'nullable|string','periodotipo010' => 'nullable|string',
            //
            //NEUROLOGIA
                'preguntas.011.respuesta' => 'nullable|string','hacecuanto011' => 'nullable|string','periodotipo011' => 'nullable|string',
                'preguntas.012.respuesta' => 'nullable|string','hacecuanto012' => 'nullable|string','periodotipo012' => 'nullable|string',
                'preguntas.013.respuesta' => 'nullable|string','hacecuanto013' => 'nullable|string','periodotipo013' => 'nullable|string',
                'preguntas.014.respuesta' => 'nullable|string','hacecuanto014' => 'nullable|string','periodotipo014' => 'nullable|string',
                'preguntas.015.respuesta' => 'nullable|string','hacecuanto015' => 'nullable|string','periodotipo015' => 'nullable|string',
                'preguntas.016.respuesta' => 'nullable|string','hacecuanto016' => 'nullable|string','periodotipo016' => 'nullable|string',
                'preguntas.017.respuesta' => 'nullable|string','hacecuanto017' => 'nullable|string','periodotipo017' => 'nullable|string',
                'preguntas.018.respuesta' => 'nullable|string','hacecuanto018' => 'nullable|string','periodotipo018' => 'nullable|string',
            //
            //CARDIOLOGIA
                'preguntas.019.respuesta' => 'nullable|string','hacecuanto019' => 'nullable|string','periodotipo019' => 'nullable|string',
                'preguntas.020.respuesta' => 'nullable|string','hacecuanto020' => 'nullable|string','periodotipo020' => 'nullable|string',
                'preguntas.021.respuesta' => 'nullable|string','hacecuanto021' => 'nullable|string','periodotipo021' => 'nullable|string',
                'preguntas.022.respuesta' => 'nullable|string','hacecuanto022' => 'nullable|string','periodotipo022' => 'nullable|string',
                'preguntas.023.respuesta' => 'nullable|string','hacecuanto023' => 'nullable|string','periodotipo023' => 'nullable|string',
                'preguntas.024.respuesta' => 'nullable|string','hacecuanto024' => 'nullable|string','periodotipo024' => 'nullable|string',
                'preguntas.025.respuesta' => 'nullable|string','hacecuanto025' => 'nullable|string','periodotipo025' => 'nullable|string',
                'preguntas.026.respuesta' => 'nullable|string','hacecuanto026' => 'nullable|string','periodotipo026' => 'nullable|string',
            //
            //ENDICRONOLOGIA
                'preguntas.027.respuesta' => 'nullable|string','hacecuanto027' => 'nullable|string','periodotipo027' => 'nullable|string',
                'preguntas.028.respuesta' => 'nullable|string','hacecuanto028' => 'nullable|string','periodotipo028' => 'nullable|string',
                'preguntas.029.respuesta' => 'nullable|string','hacecuanto029' => 'nullable|string','periodotipo029' => 'nullable|string',
                'preguntas.030.respuesta' => 'nullable|string','hacecuanto030' => 'nullable|string','periodotipo030' => 'nullable|string',
                'preguntas.031.respuesta' => 'nullable|string','hacecuanto031' => 'nullable|string','periodotipo031' => 'nullable|string',
            //
            //TRAUMATOLOGIA
                'preguntas.032.respuesta' => 'nullable|string','hacecuanto032' => 'nullable|string','periodotipo032' => 'nullable|string',
                'preguntas.033.respuesta' => 'nullable|string','hacecuanto033' => 'nullable|string','periodotipo033' => 'nullable|string',
                'preguntas.034.respuesta' => 'nullable|string','hacecuanto034' => 'nullable|string','periodotipo034' => 'nullable|string',
                'preguntas.035.respuesta' => 'nullable|string','hacecuanto035' => 'nullable|string','periodotipo035' => 'nullable|string',
                'preguntas.036.respuesta' => 'nullable|string','hacecuanto036' => 'nullable|string','periodotipo036' => 'nullable|string',
                'preguntas.037.respuesta' => 'nullable|string','hacecuanto037' => 'nullable|string','periodotipo037' => 'nullable|string',
            //
            //NEUMOLOGIA
                'preguntas.038.respuesta' => 'nullable|string','hacecuanto038' => 'nullable|string','periodotipo038' => 'nullable|string',
                'preguntas.039.respuesta' => 'nullable|string','hacecuanto039' => 'nullable|string','periodotipo039' => 'nullable|string',
                'preguntas.040.respuesta' => 'nullable|string','hacecuanto040' => 'nullable|string','periodotipo040' => 'nullable|string',
                'preguntas.041.respuesta' => 'nullable|string','hacecuanto041' => 'nullable|string','periodotipo041' => 'nullable|string',
                'preguntas.042.respuesta' => 'nullable|string','hacecuanto042' => 'nullable|string','periodotipo042' => 'nullable|string',
            //
            //GASTROENTEROLOGIA
                'preguntas.043.respuesta' => 'nullable|string','hacecuanto043' => 'nullable|string','periodotipo043' => 'nullable|string',
                'preguntas.044.respuesta' => 'nullable|string','hacecuanto044' => 'nullable|string','periodotipo044' => 'nullable|string',
                'preguntas.045.respuesta' => 'nullable|string','hacecuanto045' => 'nullable|string','periodotipo045' => 'nullable|string',
                'preguntas.046.respuesta' => 'nullable|string','hacecuanto046' => 'nullable|string','periodotipo046' => 'nullable|string',
                'preguntas.047.respuesta' => 'nullable|string','hacecuanto047' => 'nullable|string','periodotipo047' => 'nullable|string',
                'preguntas.048.respuesta' => 'nullable|string','hacecuanto048' => 'nullable|string','periodotipo048' => 'nullable|string',
                'preguntas.049.respuesta' => 'nullable|string','hacecuanto049' => 'nullable|string','periodotipo049' => 'nullable|string',
                'preguntas.050.respuesta' => 'nullable|string','hacecuanto050' => 'nullable|string','periodotipo050' => 'nullable|string',
            //
            //UROLOGIA / NEFROLOGIA
                'preguntas.051.respuesta' => 'nullable|string','hacecuanto051' => 'nullable|string','periodotipo051' => 'nullable|string',
                'preguntas.052.respuesta' => 'nullable|string','hacecuanto052' => 'nullable|string','periodotipo052' => 'nullable|string',
                'preguntas.053.respuesta' => 'nullable|string','hacecuanto053' => 'nullable|string','periodotipo053' => 'nullable|string',
                'preguntas.054.respuesta' => 'nullable|string','hacecuanto054' => 'nullable|string','periodotipo054' => 'nullable|string',
            //
            //DERMATOLOGIA
                'preguntas.055.respuesta' => 'nullable|string','hacecuanto055' => 'nullable|string','periodotipo055' => 'nullable|string',
                'preguntas.056.respuesta' => 'nullable|string','hacecuanto056' => 'nullable|string','periodotipo056' => 'nullable|string',
                'preguntas.057.respuesta' => 'nullable|string','hacecuanto057' => 'nullable|string','periodotipo057' => 'nullable|string',
                'preguntas.058.respuesta' => 'nullable|string','hacecuanto058' => 'nullable|string','periodotipo058' => 'nullable|string',
                'preguntas.059.respuesta' => 'nullable|string','hacecuanto059' => 'nullable|string','periodotipo059' => 'nullable|string',
                'preguntas.060.respuesta' => 'nullable|string','hacecuanto060' => 'nullable|string','periodotipo060' => 'nullable|string',
            //
            //CIRUGIA VASCULAR
                'preguntas.061.respuesta' => 'nullable|string','hacecuanto061' => 'nullable|string','periodotipo061' => 'nullable|string',
                'preguntas.062.respuesta' => 'nullable|string','hacecuanto062' => 'nullable|string','periodotipo062' => 'nullable|string',
                'preguntas.063.respuesta' => 'nullable|string','hacecuanto063' => 'nullable|string','periodotipo063' => 'nullable|string',
            //
            //REUMATOLOGIA
                'preguntas.064.respuesta' => 'nullable|string','hacecuanto064' => 'nullable|string','periodotipo064' => 'nullable|string',
                'preguntas.065.respuesta' => 'nullable|string','hacecuanto065' => 'nullable|string','periodotipo065' => 'nullable|string',
                'preguntas.066.respuesta' => 'nullable|string','hacecuanto066' => 'nullable|string','periodotipo066' => 'nullable|string',
                'preguntas.067.respuesta' => 'nullable|string','hacecuanto067' => 'nullable|string','periodotipo067' => 'nullable|string',
                'preguntas.068.respuesta' => 'nullable|string','hacecuanto068' => 'nullable|string','periodotipo068' => 'nullable|string',
                'preguntas.069.respuesta' => 'nullable|string','hacecuanto069' => 'nullable|string','periodotipo069' => 'nullable|string',
                'preguntas.070.respuesta' => 'nullable|string','hacecuanto070' => 'nullable|string','periodotipo070' => 'nullable|string',
                'preguntas.071.respuesta' => 'nullable|string','hacecuanto071' => 'nullable|string','periodotipo071' => 'nullable|string',
            //
            //ONCOLOGIA
                'preguntas.072.respuesta' => 'nullable|string','hacecuanto072' => 'nullable|string','periodotipo072' => 'nullable|string',
            //
            //CIRUGIA GENERAL
                'preguntas.073.respuesta' => 'nullable|string','hacecuanto073' => 'nullable|string','periodotipo073' => 'nullable|string',
                'preguntas.074.respuesta' => 'nullable|string','hacecuanto074' => 'nullable|string','periodotipo074' => 'nullable|string',
            //
            //GINECOLOGIA
                'preguntas.075.respuesta' => 'nullable|string','hacecuanto075' => 'nullable|string','periodotipo075' => 'nullable|string',
                'preguntas.076.respuesta' => 'nullable|string','hacecuanto076' => 'nullable|string','periodotipo076' => 'nullable|string',
                'preguntas.077.respuesta' => 'nullable|string','hacecuanto077' => 'nullable|string','periodotipo077' => 'nullable|string',
                'preguntas.078.respuesta' => 'nullable|string','hacecuanto078' => 'nullable|string','periodotipo078' => 'nullable|string',
                'preguntas.079.respuesta' => 'nullable|string','hacecuanto079' => 'nullable|string','periodotipo079' => 'nullable|string',
            //
            //ANTECEDENTES PATOLOGICOS ADICIONALES
                'fracturas' => '','alergias' => '','transfusiones' => '','intoxicaciones' => '','enfermedadessexual' => '','alteracionvision' => '','alteracionoido' => '','enfermedaddigestivo' => '','enfermedadurogenital' => '',
                //ANTECEDENTES PERSONALES NO PATOLOGICOS
                //CIGARILLOS
                'estadocigarrillos' => '','suspcigarillos' => '','tiemposuspcigarillos' => '','freccigarillos' => '','tiempofreccigarillos' => '','consumocigarillos' => '','tiempoconscigarillos' => '','numerocigarrillos' => '',
                //ALCOHOL
                'estadoalcoholismo' => '','suspensionalcohol' => '','tiemposuspalcohol' => '','frecuenciaalcohol' => '','tiempofrecalcohol' => '','consumoalcohol' => '','tiempoconsalcohol' => '','tipobebida' => '',
                //COCA
                'estadococa' => '','consumococa' => '','tiempoconscoca' => '','frecuenciacoca' => '','tiempofreccoca' => '',
                //MEDICAMENTOS
                'estadomedicamento' => '','cualesmedicamentos' => '',
                //ADICIONAL
                'vivienda' => '','alimentacion' => '','drogas' => '','deporte' => '','catarsis' => '','diuresis' => '','combe' => '',
                //ANTECEDENTES QUIRURGICOS
                'preguntas.100.antecedente' => 'nullable|string','preguntas.100.periodotiempo' => 'nullable|string',
                'preguntas.200.antecedente' => 'nullable|string','preguntas.200.periodotiempo' => 'nullable|string',
                'preguntas.300.antecedente' => 'nullable|string','preguntas.300.periodotiempo' => 'nullable|string',
                //ANTECEDENTES TRAUMATICOS
                'preguntas.1000.antecedente' => 'nullable|string','preguntas.1000.periodotiempo' => 'nullable|string',
                'preguntas.2000.antecedente' => 'nullable|string','preguntas.2000.periodotiempo' => 'nullable|string',
                'preguntas.3000.antecedente' => 'nullable|string','preguntas.3000.periodotiempo' => 'nullable|string',
                //ANTECEDENTES FAMILIARES
                'estadosaludpadre' => '','edadvivopadre' => '','edadfallecidopadre' => '','causafallecidopadre' => '','enfermedadespadre' => '',
                'estadosaludmadre' => '','edadvivomadre' => '','edadfallecemadre' => '','causafallecemadre' => '','enfermedadesmadre' => '',
                'cantidadhermanos' => '','hermanovivo' => '','hermanofallece' => '','caudafallecehermano' => '','enfermedadeshermano' => '',
                'estadosaludesposo' => '','edadvivoesposo' => '','edadfalleceesposo' => '','causafalleceesposo' => '','enfermedadesesposo' => '',
                'cantidadhijos' => '','hijosvivo' => '','hijosfallece' => '','causafallecehijos' => '','enfermedadeshijos' => '',
                //ANTECENTES FAMILIARES ADICIONALES
                'preguntas.30.respuesta' => 'nullable|string','hacecuanto30' => 'nullable|string','periodotipo30' => 'nullable|string',
                'preguntas.31.respuesta' => 'nullable|string','hacecuanto31' => 'nullable|string','periodotipo31' => 'nullable|string',
                'preguntas.32.respuesta' => 'nullable|string','hacecuanto32' => 'nullable|string','periodotipo32' => 'nullable|string',
                'preguntas.33.respuesta' => 'nullable|string','hacecuanto33' => 'nullable|string','periodotipo33' => 'nullable|string',
                'preguntas.34.respuesta' => 'nullable|string','hacecuanto34' => 'nullable|string','periodotipo34' => 'nullable|string',
                'preguntas.35.respuesta' => 'nullable|string','hacecuanto35' => 'nullable|string','periodotipo35' => 'nullable|string',
                'preguntas.36.respuesta' => 'nullable|string','hacecuanto36' => 'nullable|string','periodotipo36' => 'nullable|string',
                'preguntas.37.respuesta' => 'nullable|string','hacecuanto37' => 'nullable|string','periodotipo37' => 'nullable|string',
                'preguntas.38.respuesta' => 'nullable|string','hacecuanto38' => 'nullable|string','periodotipo38' => 'nullable|string',
                'preguntas.39.respuesta' => 'nullable|string','hacecuanto39' => 'nullable|string','periodotipo39' => 'nullable|string',
                'preguntas.40.respuesta' => 'nullable|string','hacecuanto40' => 'nullable|string','periodotipo40' => 'nullable|string',
                'preguntas.41.respuesta' => 'nullable|string','hacecuanto41' => 'nullable|string','periodotipo41' => 'nullable|string',
                //ANTECEDENTES LABORALES
                'fechainicioatclab' => '','fechafinalatclab' => '',
                'preguntas.1.carac' => 'nullable|string','preguntas.1.denun' => 'nullable|string','preguntas.1.aten' => 'nullable|string',
                'preguntas.2.carac' => 'nullable|string','preguntas.2.denun' => 'nullable|string','preguntas.2.aten' => 'nullable|string',
                'preguntas.3.carac' => 'nullable|string','preguntas.3.denun' => 'nullable|string','preguntas.3.aten' => 'nullable|string',
                //HISTORIA DE LA ENFERMEDAD ACTUAL
                'historiaenfermedad' => '',
                //EXAMEN FISICO
                'examenfisicogeneral' => '','llenadocapilar' => '','lateralidad' => '',
                'pulso' => '','satO2' => '','frespiracion' => '','temperatura' => '','presionarterial' => '',
                'agudezavisual' => '','usalentes' => '',
                'peso' => '','estatura' => '','imc' => '',
                //EXAMEN FISICO SEGMENTADO
                'exficabeza' => '','exfiojos' => '','exfinariz' => '','exfioidos' => '','exfiboca' => '','exficuello' => '','exfitorax' => '','exficorazon' => '','exfipulmones' => '',
                'exfiabdomen' => '','exfiextremidadesmmss' => '','exfiextremidadesmmii' => '','exfineurologico' => '','exfivestibulocereboloso' => '','exfimarcha' => '','exficraneoycolumna' => '','exfiexploracionneuro' => '',
            //
        ]);

        //IDENTIFICACION DE PELIGROS
            Session::put('fechaatencion', $request->fechaatencion);
            Session::put('antecedentespatologicos', $request->antecedentespatologicos);
            Session::put('peligrosfisicos', $request->input('preguntas.4.respuesta'));
            Session::put('descripcionpeligrosfisicos', $request->input('detpe4'));
            Session::put('peligrosquimicos', $request->input('preguntas.5.respuesta'));
            Session::put('descripcionpeligrosquimicos', $request->input('detpe5'));
            Session::put('peligrosergonomicos', $request->input('preguntas.6.respuesta'));
            Session::put('descripcionpeligrosergonomicos', $request->input('detpe6'));
            Session::put('peligrosepps', $request->input('preguntas.7.respuesta'));
            Session::put('descripcionpeligrosepps', $request->input('detpe7'));
            Session::put('peligrosbiologicos', $request->input('preguntas.8.respuesta'));
            Session::put('descripcionpeligrosbiologicos', $request->input('detpe8'));
            Session::put('peligrosmecanicos', $request->input('preguntas.9.respuesta'));
            Session::put('descripcionpeligrosmecanicos', $request->input('detpe9'));
            Session::put('peligrosambientales', $request->input('preguntas.10.respuesta'));
            Session::put('descripcionpeligrosambientales', $request->input('detpe10'));
            Session::put('peligrospsicosociales', $request->input('preguntas.11.respuesta'));
            Session::put('descripcionpeligrospsicosociales', $request->input('detpe11'));
            Session::put('otros', $request->otros);
            //OFTALMOLOGIA
            Session::put('cefalea', $request->input('preguntas.001.respuesta'));
            Session::put('hacecuanto001', $request->input('hacecuanto001'));
            Session::put('periodotipo001', $request->input('periodotipo001'));
            Session::put('defectovisual', $request->input('preguntas.002.respuesta'));
            Session::put('hacecuanto002', $request->input('hacecuanto002'));
            Session::put('periodotipo002', $request->input('periodotipo002'));
            Session::put('irritacionocular', $request->input('preguntas.003.respuesta'));
            Session::put('hacecuanto003', $request->input('hacecuanto003'));
            Session::put('periodotipo003', $request->input('periodotipo003'));
            Session::put('sequedadocular', $request->input('preguntas.004.respuesta'));
            Session::put('hacecuanto004', $request->input('hacecuanto004'));
            Session::put('periodotipo004', $request->input('periodotipo004'));
            Session::put('lagrimeo', $request->input('preguntas.005.respuesta'));
            Session::put('hacecuanto005', $request->input('hacecuanto005'));
            Session::put('periodotipo005', $request->input('periodotipo005'));
            Session::put('visionborrosa', $request->input('preguntas.006.respuesta'));
            Session::put('hacecuanto006', $request->input('hacecuanto006'));
            Session::put('periodotipo006', $request->input('periodotipo006'));
            //OTORRINOLARINGOLOGIA
            Session::put('hipoacuasia', $request->input('preguntas.007.respuesta'));
            Session::put('hacecuanto007', $request->input('hacecuanto007'));
            Session::put('periodotipo007', $request->input('periodotipo007'));
            Session::put('otitismedia', $request->input('preguntas.008.respuesta'));
            Session::put('hacecuanto008', $request->input('hacecuanto008'));
            Session::put('periodotipo008', $request->input('periodotipo008'));
            Session::put('sinusitis', $request->input('preguntas.009.respuesta'));
            Session::put('hacecuanto009', $request->input('hacecuanto009'));
            Session::put('periodotipo009', $request->input('periodotipo009'));
            Session::put('tinitus', $request->input('preguntas.010.respuesta'));
            Session::put('hacecuanto010', $request->input('hacecuanto010'));
            Session::put('periodotipo010', $request->input('periodotipo010'));
            //NEUROLOGIA
            Session::put('convulsiones', $request->input('preguntas.011.respuesta'));
            Session::put('hacecuanto011', $request->input('hacecuanto011'));
            Session::put('periodotipo011', $request->input('periodotipo011'));
            Session::put('epilepsia', $request->input('preguntas.012.respuesta'));
            Session::put('hacecuanto012', $request->input('hacecuanto012'));
            Session::put('periodotipo012', $request->input('periodotipo012'));
            Session::put('lumbalgia', $request->input('preguntas.013.respuesta'));
            Session::put('hacecuanto013', $request->input('hacecuanto013'));
            Session::put('periodotipo013', $request->input('periodotipo013'));
            Session::put('neuropatia', $request->input('preguntas.014.respuesta'));
            Session::put('hacecuanto014', $request->input('hacecuanto014'));
            Session::put('periodotipo014', $request->input('periodotipo014'));
            Session::put('acv', $request->input('preguntas.015.respuesta'));
            Session::put('hacecuanto015', $request->input('hacecuanto015'));
            Session::put('periodotipo015', $request->input('periodotipo015'));
            Session::put('cefaleaneurologia', $request->input('preguntas.016.respuesta'));
            Session::put('hacecuanto016', $request->input('hacecuanto016'));
            Session::put('periodotipo016', $request->input('periodotipo016'));
            Session::put('disformiamuscular', $request->input('preguntas.017.respuesta'));
            Session::put('hacecuanto017', $request->input('hacecuanto017'));
            Session::put('periodotipo017', $request->input('periodotipo017'));
            Session::put('lesionmedulaespinal', $request->input('preguntas.018.respuesta'));
            Session::put('hacecuanto018', $request->input('hacecuanto018'));
            Session::put('periodotipo018', $request->input('periodotipo018'));
            //CARDIOLOGIA
            Session::put('hta', $request->input('preguntas.019.respuesta'));
            Session::put('hacecuanto019', $request->input('hacecuanto019'));
            Session::put('periodotipo019', $request->input('periodotipo019'));
            Session::put('arritmia', $request->input('preguntas.020.respuesta'));
            Session::put('hacecuanto020', $request->input('hacecuanto020'));
            Session::put('periodotipo020', $request->input('periodotipo020'));
            Session::put('chagas', $request->input('preguntas.021.respuesta'));
            Session::put('hacecuanto021', $request->input('hacecuanto021'));
            Session::put('periodotipo021', $request->input('periodotipo021'));
            Session::put('taquicardia', $request->input('preguntas.022.respuesta'));
            Session::put('hacecuanto022', $request->input('hacecuanto022'));
            Session::put('periodotipo022', $request->input('periodotipo022'));
            Session::put('bradicardia', $request->input('preguntas.023.respuesta'));
            Session::put('hacecuanto023', $request->input('hacecuanto023'));
            Session::put('periodotipo023', $request->input('periodotipo023'));
            Session::put('bloqueoderama', $request->input('preguntas.024.respuesta'));
            Session::put('hacecuanto024', $request->input('hacecuanto024'));
            Session::put('periodotipo024', $request->input('periodotipo024'));
            Session::put('stentcoronario', $request->input('preguntas.025.respuesta'));
            Session::put('hacecuanto025', $request->input('hacecuanto025'));
            Session::put('periodotipo025', $request->input('periodotipo025'));
            Session::put('marcapaso', $request->input('preguntas.026.respuesta'));
            Session::put('hacecuanto026', $request->input('hacecuanto026'));
            Session::put('periodotipo026', $request->input('periodotipo026'));
            //ENDICRONOLOGIA
            Session::put('dmt2', $request->input('preguntas.027.respuesta'));
            Session::put('hacecuanto027', $request->input('hacecuanto027'));
            Session::put('periodotipo027', $request->input('periodotipo027'));
            Session::put('lupuseritematoso', $request->input('preguntas.028.respuesta'));
            Session::put('hacecuanto028', $request->input('hacecuanto028'));
            Session::put('periodotipo028', $request->input('periodotipo028'));
            Session::put('colesterolelevado', $request->input('preguntas.029.respuesta'));
            Session::put('hacecuanto029', $request->input('hacecuanto029'));
            Session::put('periodotipo029', $request->input('periodotipo029'));
            Session::put('hipotiroidismo', $request->input('preguntas.030.respuesta'));
            Session::put('hacecuanto030', $request->input('hacecuanto030'));
            Session::put('periodotipo030', $request->input('periodotipo030'));
            Session::put('hipertiroidismo', $request->input('preguntas.031.respuesta'));
            Session::put('hacecuanto031', $request->input('hacecuanto031'));
            Session::put('periodotipo031', $request->input('periodotipo031'));
            //TRAUMATOLOGIA
            Session::put('artritis', $request->input('preguntas.032.respuesta'));
            Session::put('hacecuanto032', $request->input('hacecuanto032'));
            Session::put('periodotipo032', $request->input('periodotipo032'));
            Session::put('doloresarticulares', $request->input('preguntas.033.respuesta'));
            Session::put('hacecuanto033', $request->input('hacecuanto033'));
            Session::put('periodotipo033', $request->input('periodotipo033'));
            Session::put('lumbalgia', $request->input('preguntas.034.respuesta'));
            Session::put('hacecuanto034', $request->input('hacecuanto034'));
            Session::put('periodotipo034', $request->input('periodotipo034'));
            Session::put('cervicalgia', $request->input('preguntas.035.respuesta'));
            Session::put('hacecuanto035', $request->input('hacecuanto035'));
            Session::put('periodotipo035', $request->input('periodotipo035'));
            Session::put('dorsalgia', $request->input('preguntas.036.respuesta'));
            Session::put('hacecuanto036', $request->input('hacecuanto036'));
            Session::put('periodotipo036', $request->input('periodotipo036'));
            Session::put('silicosis', $request->input('preguntas.037.respuesta'));
            Session::put('hacecuanto037', $request->input('hacecuanto037'));
            Session::put('periodotipo037', $request->input('periodotipo037'));
            //NEUMOLOGIA
            Session::put('bronquitis', $request->input('preguntas.038.respuesta'));
            Session::put('hacecuanto038', $request->input('hacecuanto038'));
            Session::put('periodotipo038', $request->input('periodotipo038'));
            Session::put('asma', $request->input('preguntas.039.respuesta'));
            Session::put('hacecuanto039', $request->input('hacecuanto039'));
            Session::put('periodotipo039', $request->input('periodotipo039'));
            Session::put('tuberculosis', $request->input('preguntas.040.respuesta'));
            Session::put('hacecuanto040', $request->input('hacecuanto040'));
            Session::put('periodotipo040', $request->input('periodotipo040'));
            Session::put('epoc', $request->input('preguntas.041.respuesta'));
            Session::put('hacecuanto041', $request->input('hacecuanto041'));
            Session::put('periodotipo041', $request->input('periodotipo041'));
            Session::put('enfisemapulmonar', $request->input('preguntas.042.respuesta'));
            Session::put('hacecuanto042', $request->input('hacecuanto042'));
            Session::put('periodotipo042', $request->input('periodotipo042'));
            //GASTROENTEROLOGIA
            Session::put('gastritis', $request->input('preguntas.043.respuesta'));
            Session::put('hacecuanto043', $request->input('hacecuanto043'));
            Session::put('periodotipo043', $request->input('periodotipo043'));
            Session::put('enfacidopeptica', $request->input('preguntas.044.respuesta'));
            Session::put('hacecuanto044', $request->input('hacecuanto044'));
            Session::put('periodotipo044', $request->input('periodotipo044'));
            Session::put('colonirritable', $request->input('preguntas.045.respuesta'));
            Session::put('hacecuanto045', $request->input('hacecuanto045'));
            Session::put('periodotipo045', $request->input('periodotipo045'));
            Session::put('cololetiasis', $request->input('preguntas.046.respuesta'));
            Session::put('hacecuanto046', $request->input('hacecuanto046'));
            Session::put('periodotipo046', $request->input('periodotipo046'));
            Session::put('distencion', $request->input('preguntas.047.respuesta'));
            Session::put('hacecuanto047', $request->input('hacecuanto047'));
            Session::put('periodotipo047', $request->input('periodotipo047'));
            Session::put('calculosbiliares', $request->input('preguntas.048.respuesta'));
            Session::put('hacecuanto048', $request->input('hacecuanto048'));
            Session::put('periodotipo048', $request->input('periodotipo048'));
            Session::put('ulceraintestinal', $request->input('preguntas.049.respuesta'));
            Session::put('hacecuanto049', $request->input('hacecuanto049'));
            Session::put('periodotipo049', $request->input('periodotipo049'));
            Session::put('hepatitis', $request->input('preguntas.050.respuesta'));
            Session::put('hacecuanto050', $request->input('hacecuanto050'));
            Session::put('periodotipo050', $request->input('periodotipo050'));
            //UROLOGIA / NEFROLOGIA
            Session::put('urolitiasis', $request->input('preguntas.051.respuesta'));
            Session::put('hacecuanto051', $request->input('hacecuanto051'));
            Session::put('periodotipo051', $request->input('periodotipo051'));
            Session::put('infeccionurinaria', $request->input('preguntas.052.respuesta'));
            Session::put('hacecuanto052', $request->input('hacecuanto052'));
            Session::put('periodotipo052', $request->input('periodotipo052'));
            Session::put('prostatitis', $request->input('preguntas.053.respuesta'));
            Session::put('hacecuanto053', $request->input('hacecuanto053'));
            Session::put('periodotipo053', $request->input('periodotipo053'));
            Session::put('varicocele', $request->input('preguntas.054.respuesta'));
            Session::put('hacecuanto054', $request->input('hacecuanto054'));
            Session::put('periodotipo054', $request->input('periodotipo054'));
            //DERMATOLOGIA
            Session::put('dermatitis', $request->input('preguntas.055.respuesta'));
            Session::put('hacecuanto055', $request->input('hacecuanto055'));
            Session::put('periodotipo055', $request->input('periodotipo055'));
            Session::put('lupuseritematosoder', $request->input('preguntas.056.respuesta'));
            Session::put('hacecuanto056', $request->input('hacecuanto056'));
            Session::put('periodotipo056', $request->input('periodotipo056'));
            Session::put('vitiligo', $request->input('preguntas.057.respuesta'));
            Session::put('hacecuanto057', $request->input('hacecuanto057'));
            Session::put('periodotipo057', $request->input('periodotipo057'));
            Session::put('eccema', $request->input('preguntas.058.respuesta'));
            Session::put('hacecuanto058', $request->input('hacecuanto058'));
            Session::put('periodotipo058', $request->input('periodotipo058'));
            Session::put('impetigo', $request->input('preguntas.059.respuesta'));
            Session::put('hacecuanto059', $request->input('hacecuanto059'));
            Session::put('periodotipo059', $request->input('periodotipo059'));
            Session::put('psoriasis', $request->input('preguntas.060.respuesta'));
            Session::put('hacecuanto060', $request->input('hacecuanto060'));
            Session::put('periodotipo060', $request->input('periodotipo060'));
            //CIRUGIA VASCULAR
            Session::put('varicesenpiernas', $request->input('preguntas.061.respuesta'));
            Session::put('hacecuanto061', $request->input('hacecuanto061'));
            Session::put('periodotipo061', $request->input('periodotipo061'));
            Session::put('celulitisenmmii', $request->input('preguntas.062.respuesta'));
            Session::put('hacecuanto062', $request->input('hacecuanto062'));
            Session::put('periodotipo062', $request->input('periodotipo062'));
            Session::put('trombosis', $request->input('preguntas.063.respuesta'));
            Session::put('hacecuanto063', $request->input('hacecuanto063'));
            Session::put('periodotipo063', $request->input('periodotipo063'));
            //REUMATOLOGIA
            Session::put('artritisreumatoidea', $request->input('preguntas.064.respuesta'));
            Session::put('hacecuanto064', $request->input('hacecuanto064'));
            Session::put('periodotipo064', $request->input('periodotipo064'));
            Session::put('artrosisreu', $request->input('preguntas.065.respuesta'));
            Session::put('hacecuanto065', $request->input('hacecuanto065'));
            Session::put('periodotipo065', $request->input('periodotipo065'));
            Session::put('psoriasisreu', $request->input('preguntas.066.respuesta'));
            Session::put('hacecuanto066', $request->input('hacecuanto066'));
            Session::put('periodotipo066', $request->input('periodotipo066'));
            Session::put('lupuseritematosoreu', $request->input('preguntas.067.respuesta'));
            Session::put('hacecuanto067', $request->input('hacecuanto067'));
            Session::put('periodotipo067', $request->input('periodotipo067'));
            Session::put('gota', $request->input('preguntas.068.respuesta'));
            Session::put('hacecuanto068', $request->input('hacecuanto068'));
            Session::put('periodotipo068', $request->input('periodotipo068'));
            Session::put('espondilitisanquilosante', $request->input('preguntas.069.respuesta'));
            Session::put('hacecuanto069', $request->input('hacecuanto069'));
            Session::put('periodotipo069', $request->input('periodotipo069'));
            Session::put('fibromialgia', $request->input('preguntas.070.respuesta'));
            Session::put('hacecuanto070', $request->input('hacecuanto070'));
            Session::put('periodotipo070', $request->input('periodotipo070'));
            Session::put('reumatismo', $request->input('preguntas.071.respuesta'));
            Session::put('hacecuanto071', $request->input('hacecuanto071'));
            Session::put('periodotipo071', $request->input('periodotipo071'));
            //ONCOLOGIA
            Session::put('cancer', $request->input('preguntas.072.respuesta'));
            Session::put('hacecuanto072', $request->input('hacecuanto072'));
            Session::put('periodotipo072', $request->input('periodotipo072'));
            //CIRUGIA GENERAL
            Session::put('herniainguinal', $request->input('preguntas.073.respuesta'));
            Session::put('hacecuanto073', $request->input('hacecuanto073'));
            Session::put('periodotipo073', $request->input('periodotipo073'));
            Session::put('herniaumbilical', $request->input('preguntas.074.respuesta'));
            Session::put('hacecuanto074', $request->input('hacecuanto074'));
            Session::put('periodotipo074', $request->input('periodotipo074'));
            //GINECOLOGIA
            Session::put('endometriosis', $request->input('preguntas.075.respuesta'));
            Session::put('hacecuanto075', $request->input('hacecuanto075'));
            Session::put('periodotipo075', $request->input('periodotipo075'));
            Session::put('miomasuterinos', $request->input('preguntas.076.respuesta'));
            Session::put('hacecuanto076', $request->input('hacecuanto076'));
            Session::put('periodotipo076', $request->input('periodotipo076'));
            Session::put('poliposuterinos', $request->input('preguntas.077.respuesta'));
            Session::put('hacecuanto077', $request->input('hacecuanto077'));
            Session::put('periodotipo077', $request->input('periodotipo077'));
            Session::put('quistesdeovarios', $request->input('preguntas.078.respuesta'));
            Session::put('hacecuanto078', $request->input('hacecuanto078'));
            Session::put('periodotipo078', $request->input('periodotipo078'));
            Session::put('prolapsogenital', $request->input('preguntas.079.respuesta'));
            Session::put('hacecuanto079', $request->input('hacecuanto079'));
            Session::put('periodotipo079', $request->input('periodotipo079'));
            //ANTECEDENTES PATOLOGICOS ADICIONALES
            Session::put('fracturas', $request->fracturas);
            Session::put('alergias', $request->alergias);
            Session::put('transfusiones', $request->transfusiones);
            Session::put('intoxicaciones', $request->intoxicaciones);
            Session::put('enfermedadessexual', $request->enfermedadessexual);
            Session::put('alteracionvision', $request->alteracionvision);
            Session::put('alteracionoido', $request->alteracionoido);
            Session::put('enfermedaddigestivo', $request->enfermedaddigestivo);
            Session::put('enfermedadurogenital', $request->enfermedadurogenital);
            //ANTECEDENTES PERSONALES NO PATOLOGICOS
            //CIGARRILLOS
            Session::put('estadocigarrillos', $request->estadocigarrillos);
            Session::put('suspcigarillos', $request->suspcigarillos);
            Session::put('tiemposuspcigarillos', $request->tiemposuspcigarillos);
            Session::put('freccigarillos', $request->freccigarillos);
            Session::put('tiempofreccigarillos', $request->tiempofreccigarillos);
            Session::put('consumocigarillos', $request->consumocigarillos);
            Session::put('tiempoconscigarillos', $request->tiempoconscigarillos);
            Session::put('numerocigarrillos', $request->numerocigarrillos);
            //ALCOHOL
            Session::put('estadoalcoholismo', $request->estadoalcoholismo);
            Session::put('suspensionalcohol', $request->suspensionalcohol);
            Session::put('tiemposuspalcohol', $request->tiemposuspalcohol);
            Session::put('frecuenciaalcohol', $request->frecuenciaalcohol);
            Session::put('tiempofrecalcohol', $request->tiempofrecalcohol);
            Session::put('consumoalcohol', $request->consumoalcohol);
            Session::put('tiempoconsalcohol', $request->tiempoconsalcohol);
            Session::put('tipobebida', $request->tipobebida);
            //COCA
            Session::put('estadococa', $request->estadococa);
            Session::put('consumococa', $request->consumococa);
            Session::put('tiempoconscoca', $request->tiempoconscoca);
            Session::put('frecuenciacoca', $request->frecuenciacoca);
            Session::put('tiempofreccoca', $request->tiempofreccoca);
            //MEDICAMENTOS
            Session::put('estadomedicamento', $request->estadomedicamento);
            Session::put('cualesmedicamentos', $request->cualesmedicamentos);
            //ADICIONAL
            Session::put('vivienda', $request->vivienda);
            Session::put('alimentacion', $request->alimentacion);
            Session::put('drogas', $request->drogas);
            Session::put('deporte', $request->deporte);
            Session::put('catarsis', $request->catarsis);
            Session::put('diuresis', $request->diuresis);
            Session::put('combe', $request->combe);
            //ANTECEDENTES QUIRUGICOS
            Session::put('atcquirurgico1', $request->input('preguntas.100.antecedente'));
            Session::put('atcperiodo1', $request->input('preguntas.100.periodotiempo'));
            Session::put('atcquirurgico2', $request->input('preguntas.200.antecedente'));
            Session::put('atcperiodo2', $request->input('preguntas.200.periodotiempo'));
            Session::put('atcquirurgico3', $request->input('preguntas.300.antecedente'));
            Session::put('atcperiodo3', $request->input('preguntas.300.periodotiempo'));
            //ANTECEDENTES TRAUMATICOS
            Session::put('atctrau1', $request->input('preguntas.100.antecedente'));
            Session::put('atctrauperiodo1', $request->input('preguntas.1000.periodotiempo'));
            Session::put('atctrau2', $request->input('preguntas.200.antecedente'));
            Session::put('atctrauperiodo2', $request->input('preguntas.2000.periodotiempo'));
            Session::put('atctrau3', $request->input('preguntas.300.antecedente'));
            Session::put('atctrauperiodo3', $request->input('preguntas.3000.periodotiempo'));
            //ANTECEDENTES FAMILIARES
            Session::put('estadosaludpadre', $request->estadosaludpadre);
            Session::put('edadvivopadre', $request->edadvivopadre);
            Session::put('edadfallecidopadre', $request->edadfallecidopadre);
            Session::put('causafallecidopadre', $request->causafallecidopadre);
            Session::put('enfermedadespadre', $request->enfermedadespadre);
            Session::put('estadosaludmadre', $request->estadosaludmadre);
            Session::put('edadvivomadre', $request->edadvivomadre);
            Session::put('edadfallecemadre', $request->edadfallecemadre);
            Session::put('causafallecemadre', $request->causafallecemadre);
            Session::put('enfermedadesmadre', $request->enfermedadesmadre);
            Session::put('cantidadhermanos', $request->cantidadhermanos);
            Session::put('hermanovivo', $request->hermanovivo);
            Session::put('hermanofallece', $request->hermanofallece);
            Session::put('caudafallecehermano', $request->caudafallecehermano);
            Session::put('enfermedadeshermano', $request->enfermedadeshermano);
            Session::put('estadosaludesposo', $request->estadosaludesposo);
            Session::put('edadvivoesposo', $request->edadvivoesposo);
            Session::put('edadfalleceesposo', $request->edadfalleceesposo);
            Session::put('causafalleceesposo', $request->causafalleceesposo);
            Session::put('enfermedadesesposo', $request->enfermedadesesposo);
            Session::put('cantidadhijos', $request->cantidadhijos);
            Session::put('hijosvivo', $request->hijosvivo);
            Session::put('hijosfallece', $request->hijosfallece);
            Session::put('causafallecehijos', $request->causafallecehijos);
            Session::put('enfermedadeshijos', $request->enfermedadeshijos);
            //ANTECEDENTES FAMILIARES ADICIONALES
            Session::put('afhta', $request->input('preguntas.30.respuesta'));
            Session::put('hacecuanto30', $request->input('hacecuanto30'));
            Session::put('periodotipo30', $request->input('periodotipo30'));
            Session::put('afinfarto', $request->input('preguntas.31.respuesta'));
            Session::put('hacecuanto31', $request->input('hacecuanto31'));
            Session::put('periodotipo31', $request->input('periodotipo31'));
            Session::put('afacv', $request->input('preguntas.32.respuesta'));
            Session::put('hacecuanto32', $request->input('hacecuanto32'));
            Session::put('periodotipo32', $request->input('periodotipo32'));
            Session::put('afalergias', $request->input('preguntas.33.respuesta'));
            Session::put('hacecuanto33', $request->input('hacecuanto33'));
            Session::put('periodotipo33', $request->input('periodotipo33'));
            Session::put('afulcerapeptica', $request->input('preguntas.34.respuesta'));
            Session::put('hacecuanto34', $request->input('hacecuanto34'));
            Session::put('periodotipo34', $request->input('periodotipo34'));
            Session::put('afdiabetes', $request->input('preguntas.35.respuesta'));
            Session::put('hacecuanto35', $request->input('hacecuanto35'));
            Session::put('periodotipo35', $request->input('periodotipo35'));
            Session::put('afasma', $request->input('preguntas.36.respuesta'));
            Session::put('hacecuanto36', $request->input('hacecuanto36'));
            Session::put('periodotipo36', $request->input('periodotipo36'));
            Session::put('aftbc', $request->input('preguntas.37.respuesta'));
            Session::put('hacecuanto37', $request->input('hacecuanto37'));
            Session::put('periodotipo37', $request->input('periodotipo37'));
            Session::put('afartritis', $request->input('preguntas.38.respuesta'));
            Session::put('hacecuanto38', $request->input('hacecuanto38'));
            Session::put('periodotipo38', $request->input('periodotipo38'));
            Session::put('afenfermedadmental', $request->input('preguntas.39.respuesta'));
            Session::put('hacecuanto39', $request->input('hacecuanto39'));
            Session::put('periodotipo39', $request->input('periodotipo39'));
            Session::put('afcancer', $request->input('preguntas.40.respuesta'));
            Session::put('hacecuanto40', $request->input('hacecuanto40'));
            Session::put('periodotipo40', $request->input('periodotipo40'));
            Session::put('afotros', $request->input('preguntas.41.respuesta'));
            Session::put('hacecuanto41', $request->input('hacecuanto41'));
            Session::put('periodotipo41', $request->input('periodotipo41'));
            //ANTECEDENTES LABORALES
            Session::put('fechainicioatclab', $request->fechainicioatclab);
            Session::put('fechafinalatclab', $request->fechafinalatclab);
            Session::put('caracatclaboral1', $request->input('preguntas.1.carac'));
            Session::put('denunatclaboral1', $request->input('preguntas.1.denun'));
            Session::put('atenatclaboral1', $request->input('preguntas.1.aten'));
            Session::put('caracatclaboral2', $request->input('preguntas.2.carac'));
            Session::put('denunatclaboral2', $request->input('preguntas.2.denun'));
            Session::put('atenatclaboral2', $request->input('preguntas.2.aten'));
            Session::put('caracatclaboral3', $request->input('preguntas.3.carac'));
            Session::put('denunatclaboral3', $request->input('preguntas.3.denun'));
            Session::put('atenatclaboral3', $request->input('preguntas.3.aten'));
            //HISTORIA DE LA ENFERMEDAD ACTUAL
            Session::put('historiaenfermedad', $request->historiaenfermedad);
            //SIGNOS VITALES
            Session::put('examenfisicogeneral', $request->examenfisicogeneral);
            Session::put('llenadocapilar', $request->llenadocapilar);
            Session::put('lateralidad', $request->lateralidad);
            Session::put('pulso', $request->pulso);
            Session::put('satO2', $request->satO2);
            Session::put('frespiracion', $request->frespiracion);
            Session::put('temperatura', $request->temperatura);
            Session::put('presionarterial', $request->presionarterial);
            Session::put('agudezavisual', $request->agudezavisual);
            Session::put('usalentes', $request->usalentes);
            Session::put('peso', $request->peso);
            Session::put('estatura', $request->estatura);
            Session::put('imc', $request->imc);
            //EXAMEN FISICO SEGMENTADO
            Session::put('exficabeza', $request->exficabeza);
            Session::put('exfiojos', $request->exfiojos);
            Session::put('exfinariz', $request->exfinariz);
            Session::put('exfioidos', $request->exfioidos);
            Session::put('exfiboca', $request->exfiboca);
            Session::put('exficuello', $request->exficuello);
            Session::put('exfitorax', $request->exfitorax);
            Session::put('exficorazon', $request->exficorazon);
            Session::put('exfipulmones', $request->exfipulmones);
            Session::put('exfiabdomen', $request->exfiabdomen);
            Session::put('exfiextremidadesmmss', $request->exfiextremidadesmmss);
            Session::put('exfiextremidadesmmii', $request->exfiextremidadesmmii);
            Session::put('exfineurologico', $request->exfineurologico);
            Session::put('exfivestibulocereboloso', $request->exfivestibulocereboloso);
            Session::put('exfimarcha', $request->exfimarcha);
            Session::put('exficraneoycolumna', $request->exficraneoycolumna);
            Session::put('exfiexploracionneuro', $request->exfiexploracionneuro);
        //

        $pdf = PDF::loadView('admin.asociados.fichamedicaclienteita', compact('cliente'));
        $pdfName = 'Fichamedica_'. $cliente->nombres;
        if ($cliente->apepaterno) {
            $pdfName .= ' ' . $cliente->apepaterno;
        }
        if ($cliente->apematerno) {
            $pdfName .= ' ' . $cliente->apematerno;
        }
        $pdfName .= '.pdf';


        $usuario = auth()->user();
        $clientFolder = public_path('fichamedicaclientesita/' . $cliente->id);
        $pdfPath = $clientFolder . '/' . $pdfName;
        if (!file_exists($clientFolder)) {
            mkdir($clientFolder, 0755, true);
        }
        $pdf->save($pdfPath);
        Fichamedicasubcliente::create([
            'clienteid' => $cliente->id,
            'nombrecompleto' => $cliente->nombres . ' ' . ($cliente->apepaterno ?? '') . ' ' . ($cliente->apematerno ?? ''),
            'document' =>/*  'fichamedicaclientesita/' . $cliente->id . '/' .  */$pdfName,
            'detalle' => 'FICHA MEDICA',
            'usuarioid' => $usuario->id,
            'usuarioregistro' => $usuario->name,
        ]);

        return $pdf->download($pdfName);
        

        /* return view('admin.asociados.fichamedicaclienteita', compact('cliente')); */
    }
    public function guardarformularioclienteita(Cliente $cliente)
    {
        return view('admin.asociados.crearformularioclienteita');
    }
    public function regresarclientes()
    {
        return view('admin.asociados.index');
    }
//
//CONTACTOS CLIENTE ITA
    public function vercontactoclienteita(Cliente $cliente)
    {
        $nombreclienteita = $cliente->nombrecompleto;
        $contactos = Contactosubcliente::where('clienteitanombre', $nombreclienteita)
                                ->simplePaginate(10000);

        return view('admin.asociados.vercontactoclienteita', compact('contactos', 'cliente'));
    }
    public function crearcontactoclienteita(Cliente $cliente)
    {
        $parentesco = [
            'ABUEL@' => 'ABUEL@',
            'ESPOS@' => 'ESPOS@',
            'HERMAN@' => 'HERMAN@',
            'HIJ@' => 'HIJ@',
            'MADRE' => 'MADRE',
            'NIET@' => 'NIET@',
            'PADRE' => 'PADRE',
            'PRIM@' => 'PRIM@',
            'SOBRIN@' => 'SOBRIN@',
            'TI@' => 'TI@',
            'UNIÓN LIBRE' => 'UNIÓN LIBRE',
        ];

        $id = $cliente->id;

        return view('admin.asociados.crearcontactoclienteita', compact('id', 'parentesco', 'cliente'));
    }
    public function guardarcontactoclienteita(StoreContactosubclienteRequest $request)
    {
        $clienteID = $request->input('clienteitaid');
        $cliente = Cliente::findOrFail($clienteID);

        $clienteData = $request->all();
        $clienteData['clienteitanombre'] = $cliente->nombrecompleto;
        $contacto = Contactosubcliente::create($clienteData);
        return redirect()->route('admin.asociados.vercontactoclienteita', ['cliente' => $cliente])->with('info', 'El contacto se creó con éxito');
    }
//
//ETIQUETAS CLIENTE ITA
    public function generaretiquetaclienteita(Request $request, Cliente $cliente)
        {
            $pdf = PDF::loadView('admin.asociados.generaretiquetaclienteita', compact('cliente'));
            $pdfName = 'Etiqueta_Invalidez_' . $cliente->nombrecompleto . '.pdf';
            return $pdf->download($pdfName);
        }
    public function generaretiquetaclienteitaauditoria(Request $request, Cliente $cliente)
        {
            $pdf = PDF::loadView('admin.asociados.generaretiquetaclienteitaauditoria', compact('cliente'));
            $pdfName = 'Etiqueta_Auditoria_' . $cliente->nombrecompleto . '.pdf';
            return $pdf->download($pdfName);
        }
    public function generaretiquetaclienteitaapelacion(Request $request, Cliente $cliente)
        {
            $pdf = PDF::loadView('admin.asociados.generaretiquetaclienteitaapelacion', compact('cliente'));
            $pdfName = 'Etiqueta_Apelacion_' . $cliente->nombrecompleto . '.pdf';
            return $pdf->download($pdfName);
        }
    public function generaretiquetaclienteitasegundasolicitud(Request $request, Cliente $cliente)
        {
            $pdf = PDF::loadView('admin.asociados.generaretiquetaclienteitasegundasolicitud', compact('cliente'));
            $pdfName = 'Etiqueta_SegundaSolicitud_' . $cliente->nombrecompleto . '.pdf';
            return $pdf->download($pdfName);
        }
//
//REQUISITOS CLIENTE ITA
    public function generarchecklistclienteita(Cliente $cliente) 
        {
            $tieneRequisitos = Requisitosubcliente::where('clienteitaid', $cliente->id)
                ->where('servicio', 'INVALIDEZ')->exists();
            $tieneInvalidez = Tramitesubcliente::where('clienteitaid', $cliente->id)
                ->where('tramite', 'INVALIDEZ')->exists();
            $estadoLaboral = strtolower($cliente->estadolaboral);
            $numHijosMenores = $cliente->numhijosmenores;
            $estadoCivil = strtolower($cliente->estadocivil);
            $servicio1 = strtolower($cliente->tipocliente);
            $rolusuario = auth()->user()->getRoleNames()->first(); 

            $registroExistente = Estadocotizacionsubcliente::where('clienteitaid', $cliente->id)
                ->where('detalle', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS')
                ->first();
            $registroaprobadoExistente = Estadocotizacionsubcliente::where('clienteitaid', $cliente->id)
                ->where('detalle', 'APROBADO PARA INICIAR A CREAR BATERIA')
                ->first();
        
            return view('admin.asociados.generarchecklistclienteita', compact(
                'cliente', 
                'tieneRequisitos', 
                'estadoLaboral',
                'numHijosMenores',  
                'estadoCivil', 
                'registroExistente','rolusuario','registroaprobadoExistente','servicio1','tieneInvalidez'
            ));
        }
    public function descargarchecklistclienteita(Request $request, Cliente $cliente)
        {
            $usuarioAutenticado = Auth::user();
            $documentosSeleccionados = json_decode($request->input('documentosSeleccionados'));
            $documentosSeleccionados2 = json_decode($request->input('documentosSeleccionados2'));

            $requisito = new RequisitoSubCliente();
            $requisito->clienteitaid = $cliente->id;
            $requisito->clienteitanombre = $cliente->nombrecompleto;
            $requisito->usuarioid = $usuarioAutenticado->id;
            $requisito->usuarioregistro = $usuarioAutenticado->name;
            $requisito->servicio = 'INVALIDEZ';

            $nombreDocumentos = [
                'poder' => 'PODER',
                'avcci' => 'AVC / CARNET ASEGURADO',
                'cnacasegurado' => 'CERTIFICADO NACIMIENTO ASEGURADO',
                'ciasegurado' => 'CARNET IDENTIDAD ASEGURADO',
                'cmatrimonio' => 'CERTIFICADO MATRIMONIO',
                'cnacconyuge' => 'CERTIFICADO NACIMIENTO CONYUGE',
                'ciconyuge' => 'CARNET IDENTIDAD CONYUGE',
                'cnacjihos' => 'CERTIFICADO NACIMIENTO HIJOS < 25',
                'cihijos' => 'CARNET IDENTIDAD HIJOS < 25',
                'crodomicilio' => 'CROQUIS DOMICILIO',
                'contrato' => 'CONTRATO',
                'cunionlibre' => 'CERTIFICADO UNIÓN LIBRE',
                'cnacimientounionlibre' => 'CERTIFICADO NACIMIENTO UNIÓN LIBRE',
                'ciunionlibre' => 'CARNET IDENTIDAD UNIÓN LIBRE',
                'cdivorcio' => 'CERTIFICADO DIVORCIO',
                'cdefuncion' => 'CERTIFICADO DIFUNCIÓN',
                
                
            ];
            $nombreDocumentos2 = [
                'ctrabajo' => 'CERTIFICADO TRABAJO',
                'boletapago' => 'BOLETA PAGO',
                'egestora' => 'EXTRACTO GESTORA',
                'denfaccidente' => 'DENUNCIA ENFERMEDAD ACCIDENTE',
                'actdatos' => 'ACTUALIZACIÓN DATOS',
                'resolinvhijos' => 'RESOL. INVAL. HIJOS < 25',
                'recordservicios' => 'RECORD SERVICIOS',
                'infomedicasalud' => 'INFORMACION MEDICA',
            ];

            foreach ($documentosSeleccionados as $documento) {
                $nombreCompleto = isset($nombreDocumentos[$documento]) ? $nombreDocumentos[$documento] : $documento;
            $valorDocumento = $request->has($documento) && $request->input($documento) === 'NO' ? 'NO' : 'PENDIENTE';
                switch ($documento) {
                    case 'poder':
                        $requisito->poder = $valorDocumento;
                        break;
                    case 'avcci':
                        $requisito->avcci = $valorDocumento;
                        break;
                    case 'cnacasegurado':
                        $requisito->cnacasegurado = $valorDocumento;
                        break;
                    case 'ciasegurado':
                        $requisito->ciasegurado = $valorDocumento;
                        break;
                    case 'cmatrimonio':
                        $requisito->cmatrimonio = $valorDocumento;
                        break;
                    case 'cnacconyuge':
                        $requisito->cnacconyuge = $valorDocumento;
                        break;
                    case 'ciconyuge':
                        $requisito->ciconyuge = $valorDocumento;
                        break;
                    case 'cnacjihos':
                        $requisito->cnacjihos = $valorDocumento;
                        break;
                    case 'cihijos':
                        $requisito->cihijos = $valorDocumento;
                        break;
                    case 'crodomicilio':
                        $requisito->crodomicilio = $valorDocumento;
                        break;
                    case 'contrato':
                        $requisito->contrato = $valorDocumento;
                        break;
                    case 'cunionlibre':
                        $requisito->cunionlibre = $valorDocumento;
                        break;
                    case 'cnacimientounionlibre':
                        $requisito->cnacimientounionlibre = $valorDocumento;
                        break;
                    case 'ciunionlibre':
                        $requisito->ciunionlibre = $valorDocumento;
                        break;
                    case 'cdivorcio':
                        $requisito->cdivorcio = $valorDocumento;
                        break;
                    case 'cdefuncion':
                        $requisito->cdefuncion = $valorDocumento;
                        break;
                    
                    default:
                        break;
                }
            }

            foreach ($documentosSeleccionados2 as $documento) {
                $nombreCompleto = isset($nombreDocumentos2[$documento]) ? $nombreDocumentos2[$documento] : $documento;
            $valorDocumento = $request->has($documento) && $request->input($documento) === 'NO' ? 'NO' : 'PENDIENTE';
                switch ($documento) {
                    case 'ctrabajo':
                        $requisito->ctrabajo = $valorDocumento;
                        break;
                    case 'boletapago':
                        $requisito->boletapago = $valorDocumento;
                        break;
                    case 'egestora':
                        $requisito->egestora = $valorDocumento;
                        break;
                    case 'denfaccidente':
                        $requisito->denfaccidente = $valorDocumento;
                        break;
                    case 'actdatos':
                        $requisito->actdatos = $valorDocumento;
                        break;
                    case 'resolinvhijos':
                        $requisito->resolinvhijos = $valorDocumento;
                        break;
                    case 'recordservicios':
                        $requisito->recordservicios = $valorDocumento;
                        break;
                    case 'infomedicasalud':
                        $requisito->infomedicasalud = $valorDocumento;
                        break;
                    default:
                        break;
                }
            }

            $requisito->save();

            $pdf = PDF::loadView('admin.asociados.descargarchecklistclienteita', compact('cliente', 'documentosSeleccionados', 'nombreDocumentos', 'documentosSeleccionados2', 'nombreDocumentos2'));
            $pdfName = 'Requisitos_Invalidez_' . $cliente->nombrecompleto . '.pdf';
            return $pdf->download($pdfName);
        } 
        public function descargarSOLOchecklistclienteITA(Request $request, Cliente $cliente)
        {
            // Obtener los documentos seleccionados, igual que antes
            $documentosSeleccionados = json_decode($request->input('documentosSeleccionados'));
            $documentosSeleccionados2 = json_decode($request->input('documentosSeleccionados2'));
    
            // Definir los nombres de los documentos
            $nombreDocumentos = [
                'poder' => 'PODER',
                'avcci' => 'AVC / CARNET ASEGURADO',
                'cnacasegurado' => 'CERTIFICADO NACIMIENTO ASEGURADO',
                'ciasegurado' => 'CARNET IDENTIDAD ASEGURADO',
                'cmatrimonio' => 'CERTIFICADO MATRIMONIO',
                'cnacconyuge' => 'CERTIFICADO NACIMIENTO CONYUGE',
                'ciconyuge' => 'CARNET IDENTIDAD CONYUGE',
                'cnacjihos' => 'CERTIFICADO NACIMIENTO HIJOS < 25',
                'cihijos' => 'CARNET IDENTIDAD HIJOS < 25',
                'crodomicilio' => 'CROQUIS DOMICILIO',
                'contrato' => 'CONTRATO',
                'cunionlibre' => 'CERTIFICADO UNIÓN LIBRE',
                'cnacimientounionlibre' => 'CERTIFICADO NACIMIENTO UNIÓN LIBRE',
                'ciunionlibre' => 'CARNET IDENTIDAD UNIÓN LIBRE',
                'cdivorcio' => 'CERTIFICADO DIVORCIO',
                'cdefuncion' => 'CERTIFICADO DIFUNCIÓN',
            ];
            $nombreDocumentos2 = [
                'ctrabajo' => 'CERTIFICADO TRABAJO',
                'boletapago' => 'BOLETA PAGO',
                'egestora' => 'EXTRACTO GESTORA',
                'denfaccidente' => 'DENUNCIA ENFERMEDAD ACCIDENTE',
                'actdatos' => 'ACTUALIZACIÓN DATOS',
                'resolinvhijos' => 'RESOL. INVAL. HIJOS < 25',
                'recordservicios' => 'RECORD SERVICIOS',
                'infomedicasalud' => 'INFORMACION MEDICA',
            ];
    
            // Generar el PDF sin registrar nada en la base de datos
            $pdf = PDF::loadView('admin.asociados.descargarchecklistclienteita', compact('cliente', 'documentosSeleccionados', 'nombreDocumentos', 'documentosSeleccionados2', 'nombreDocumentos2'));
            $pdfName = 'Requisitos_Invalidez_' . $cliente->nombrecompleto . '.pdf';
            return $pdf->download($pdfName);
        }
    public function subirdocrequisitos(Cliente $cliente)
        {
            $clienteitaid = $cliente->id; 
            $userRole = auth()->user()->getRoleNames()->first(); 
            $requisitosCliente = RequisitoSubCliente::where('clienteitaid', $clienteitaid)
            ->where('servicio', 'INVALIDEZ')->first();
    
            $poderPendiente = $requisitosCliente ? $requisitosCliente->poder === 'PENDIENTE' : false;
            $avcciPendiente = $requisitosCliente ? $requisitosCliente->avcci === 'PENDIENTE' : false;
            $cnacaseguradoPendiente = $requisitosCliente ? $requisitosCliente->cnacasegurado === 'PENDIENTE' : false;
            $ciaseguradoPendiente = $requisitosCliente ? $requisitosCliente->ciasegurado === 'PENDIENTE' : false;
            $cmatrimonioPendiente = $requisitosCliente ? $requisitosCliente->cmatrimonio === 'PENDIENTE' : false;
            $cnacconyugePendiente = $requisitosCliente ? $requisitosCliente->cnacconyuge === 'PENDIENTE' : false;
            $ciconyugePendiente = $requisitosCliente ? $requisitosCliente->ciconyuge === 'PENDIENTE' : false;
            $cnacjihosPendiente = $requisitosCliente ? $requisitosCliente->cnacjihos === 'PENDIENTE' : false;
            $cihijosPendiente = $requisitosCliente ? $requisitosCliente->cihijos === 'PENDIENTE' : false;
            $denfaccidentePendiente = $requisitosCliente ? $requisitosCliente->denfaccidente === 'PENDIENTE' : false;
            $crodomicilioPendiente = $requisitosCliente ? $requisitosCliente->crodomicilio === 'PENDIENTE' : false;
            $contratoPendiente = $requisitosCliente ? $requisitosCliente->contrato === 'PENDIENTE' : false;
            $recordserviciosPendiente = $requisitosCliente ? $requisitosCliente->recordservicios === 'PENDIENTE' : false;
            
            $cunionlibrePendiente = $requisitosCliente ? $requisitosCliente->cunionlibre === 'PENDIENTE' : false;
            $cnacimientounionlibrePendiente = $requisitosCliente ? $requisitosCliente->cnacimientounionlibre === 'PENDIENTE' : false;
            $ciunionlibrePendiente = $requisitosCliente ? $requisitosCliente->ciunionlibre === 'PENDIENTE' : false;
            $cdivorcioPendiente = $requisitosCliente ? $requisitosCliente->cdivorcio === 'PENDIENTE' : false;
            $cdefuncionPendiente = $requisitosCliente ? $requisitosCliente->cdefuncion === 'PENDIENTE' : false;

            $ctrabajoPendiente = $requisitosCliente ? $requisitosCliente->ctrabajo === 'PENDIENTE' : false;
            $boletapagoPendiente = $requisitosCliente ? $requisitosCliente->boletapago === 'PENDIENTE' : false;
            $egestoraPendiente = $requisitosCliente ? $requisitosCliente->egestora === 'PENDIENTE' : false;
            $actdatosPendiente = $requisitosCliente ? $requisitosCliente->actdatos === 'PENDIENTE' : false;
            $resolinvhijosPendiente = $requisitosCliente ? $requisitosCliente->resolinvhijos === 'PENDIENTE' : false;
            
            
            $requisito = RequisitoSubCliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'INVALIDEZ')->firstOrFail();
            $poderSubido = $requisitosCliente && strpos($requisitosCliente->poder, '.pdf') !== false ? true:false;
            $avcciSubido = $requisitosCliente && strpos($requisitosCliente->avcci, '.pdf') !== false ? true:false;
            $cnacaseguradoSubido = $requisitosCliente && strpos($requisitosCliente->cnacasegurado, '.pdf') !== false ? true:false;
            $ciaseguradoSubido = $requisitosCliente && strpos($requisitosCliente->ciasegurado, '.pdf') !== false ? true:false;
            $cmatrimonioSubido = $requisitosCliente && strpos($requisitosCliente->cmatrimonio, '.pdf') !== false ? true:false;
            $cnacconyugeSubido = $requisitosCliente && strpos($requisitosCliente->cnacconyuge, '.pdf') !== false ? true:false;
            $ciconyugeSubido = $requisitosCliente && strpos($requisitosCliente->ciconyuge, '.pdf') !== false ? true:false;
            $cnacjihosSubido = $requisitosCliente && strpos($requisitosCliente->cnacjihos, '.pdf') !== false ? true:false;
            $cihijosSubido = $requisitosCliente && strpos($requisitosCliente->cihijos, '.pdf') !== false ? true:false;
            $denfaccidenteSubido = $requisitosCliente && strpos($requisitosCliente->denfaccidente, '.pdf') !== false ? true:false;
            $crodomicilioSubido = $requisitosCliente && strpos($requisitosCliente->crodomicilio, '.pdf') !== false ? true:false;
            $contratoSubido = $requisitosCliente && strpos($requisitosCliente->contrato, '.pdf') !== false ? true:false;
            $recordserviciosSubido = $requisitosCliente && strpos($requisitosCliente->recordservicios, '.pdf') !== false ? true:false;
            
            $cunionlibreSubido = $requisitosCliente && strpos($requisitosCliente->cunionlibre, '.pdf') !== false ? true:false;
            $cnacimientounionlibreSubido = $requisitosCliente && strpos($requisitosCliente->cnacimientounionlibre, '.pdf') !== false ? true:false;
            $ciunionlibreSubido = $requisitosCliente && strpos($requisitosCliente->ciunionlibre, '.pdf') !== false ? true:false;
            $cdivorcioSubido = $requisitosCliente && strpos($requisitosCliente->cdivorcio, '.pdf') !== false ? true:false;
            $cdefuncionSubido = $requisitosCliente && strpos($requisitosCliente->cdefuncion, '.pdf') !== false ? true:false;

            $ctrabajoSubido = $requisitosCliente && strpos($requisitosCliente->ctrabajo, '.pdf') !== false ? true:false;
            $boletapagoSubido = $requisitosCliente && strpos($requisitosCliente->boletapago, '.pdf') !== false ? true:false;
            $egestoraSubido = $requisitosCliente && strpos($requisitosCliente->egestora, '.pdf') !== false ? true:false;
            $actdatosSubido = $requisitosCliente && strpos($requisitosCliente->actdatos, '.pdf') !== false ? true:false;
            $resolinvhijosSubido = $requisitosCliente && strpos($requisitosCliente->resolinvhijos, '.pdf') !== false ? true:false;
            
            return view('admin.asociados.subirdocrequisitos', compact('cliente', 'poderPendiente', 'avcciPendiente', 
            'cnacaseguradoPendiente','ciaseguradoPendiente','cmatrimonioPendiente','cnacconyugePendiente','ciconyugePendiente',
            'cnacjihosPendiente','cihijosPendiente','denfaccidentePendiente','recordserviciosPendiente','crodomicilioPendiente','contratoPendiente', 'requisito'
            , 'poderSubido', 'avcciSubido', 'cnacaseguradoSubido', 'ciaseguradoSubido', 'cmatrimonioSubido', 'cnacconyugeSubido'
            , 'ciconyugeSubido', 'cnacjihosSubido', 'cihijosSubido', 'denfaccidenteSubido', 'crodomicilioSubido', 'contratoSubido'
            , 'ctrabajoPendiente', 'boletapagoPendiente', 'egestoraPendiente', 'actdatosPendiente', 'resolinvhijosPendiente'
            , 'ctrabajoSubido', 'boletapagoSubido', 'egestoraSubido', 'actdatosSubido', 'resolinvhijosSubido'
            , 'cunionlibrePendiente', 'cnacimientounionlibrePendiente', 'ciunionlibrePendiente', 'cdivorcioPendiente', 'cdefuncionPendiente'
            , 'cunionlibreSubido', 'recordserviciosSubido', 'cnacimientounionlibreSubido', 'ciunionlibreSubido', 'cdivorcioSubido', 'cdefuncionSubido', 'userRole'));
        }
    protected function manejarArchivo(Request $request, $campo, $requisito, $clienteId)
        {
            if ($request->hasFile($campo)) {
                $file = $request->file($campo);
                $carpetaCliente = public_path("/requisitosclientesita/{$clienteId}");
    
                // Crear la carpeta si no existe
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }
    
                // Generar un nombre único para el archivo
                $archivo = time() . '_' . $file->getClientOriginalName();
    
                // Mover el archivo a la carpeta
                $file->move($carpetaCliente, $archivo);
    
                // Actualizar el modelo
                $requisito->update([$campo => $archivo]);
            }
        }
    public function guardardocrequisitos(Request $request, Cliente $cliente)
        {
            $requisito = RequisitoSubCliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'INVALIDEZ')->firstOrFail();
    
            $request->validate([
                'poder' => 'nullable|mimes:pdf',
                'numeropoder' => 'nullable|max:45',
                'avcci' => 'nullable|mimes:pdf',
                'cnacasegurado' => 'nullable|mimes:pdf',
                'ciasegurado' => 'nullable|mimes:pdf',
                'cmatrimonio' => 'nullable|mimes:pdf',
                'cnacconyuge' => 'nullable|mimes:pdf',
                'ciconyuge' => 'nullable|mimes:pdf',
                'cnacjihos' => 'nullable|mimes:pdf',
                'cihijos' => 'nullable|mimes:pdf',
                'denfaccidente' => 'nullable|mimes:pdf',
                'crodomicilio' => 'nullable|mimes:pdf',
                'contrato' => 'nullable|mimes:pdf',
                'ctrabajo' => 'nullable|mimes:pdf',
                'boletapago' => 'nullable|mimes:pdf',
                'egestora' => 'nullable|mimes:pdf',
                'actdatos' => 'nullable|mimes:pdf',
                'resolinvhijos' => 'nullable|mimes:pdf',
                'cunionlibre' => 'nullable|mimes:pdf',
                'cnacimientounionlibre' => 'nullable|mimes:pdf',
                'ciunionlibre' => 'nullable|mimes:pdf',
                'cdivorcio' => 'nullable|mimes:pdf',
                'cdefuncion' => 'nullable|mimes:pdf',
                'recordservicios' => 'nullable|mimes:pdf',
            ]);

            $camposArchivos = [
                'poder', 'avcci', 'cnacasegurado', 'ciasegurado', 'cmatrimonio', 
                'cnacconyuge', 'ciconyuge', 'cnacjihos', 'cihijos', 'denfaccidente', 
                'crodomicilio', 'contrato', 'ctrabajo', 'boletapago', 'egestora', 'actdatos', 'resolinvhijos'
                , 'cunionlibre', 'cnacimientounionlibre', 'ciunionlibre', 'cdivorcio', 'cdefuncion', 'recordservicios'
            ];

            foreach ($camposArchivos as $campo) {
                $this->manejarArchivo($request, $campo, $requisito, $cliente->id);
            }

            if ($request->filled('numeropoder')) {
                $requisito->update(['numeropoder' => $request->input('numeropoder')]);
            }
    
            return redirect()->route('admin.asociados.subirdocrequisitos', $cliente)
                             ->with('info', 'El documento se subió con éxito');
        }

//
//REQUISITOS CLIENTE ITA AUDITORIA 
    public function generarchecklistclienteitaaudi(Cliente $cliente) 
        {
            $tieneRequisitos = Requisitosclientesauditoria::where('clienteitaid', $cliente->id)->exists();
            $estadoLaboral = strtolower($cliente->estadolaboral);
            $numHijosMenores = $cliente->numhijosmenores;
            $estadoCivil = strtolower($cliente->estadocivil);
            $bancos = Banco::orderBy('nombrebanco')->pluck('nombrebanco', 'nombrebanco');

            $tieneauditoriamedica = Tramitesubcliente::where('clienteitaid', $cliente->id)
                ->where('tramite', 'AUDITORIA MEDICA')->exists();

            $rolusuario = auth()->user()->getRoleNames()->first(); 

            $registroExistente = Estadocotizacionsubcliente::where('clienteitaid', $cliente->id)
                ->where('detalle', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS')
                ->first();
            $registroaprobadoExistente = Estadocotizacionsubcliente::where('clienteitaid', $cliente->id)
                ->where('detalle', 'APROBADO PARA INICIAR A CREAR BATERIA')
                ->first();
        
            return view('admin.asociados.generarchecklistclienteitaaudi', compact(
                'cliente', 
                'tieneRequisitos', 
                'estadoLaboral',
                'numHijosMenores',  
                'estadoCivil', 
                'registroExistente','rolusuario','registroaprobadoExistente','tieneauditoriamedica','bancos'
            ));
        }
    public function descargarchecklistclienteitaaudi(Request $request, Cliente $cliente)   
        {
            $usuarioAutenticado = Auth::user();
            
            // Guardar requisitos en la base de datos
            $requisito1 = new Requisitosclientesauditoria();
            $requisito1->clienteitaid = $cliente->id;
            $requisito1->clienteitanombre = $cliente->nombrecompleto;
            $requisito1->usuarioid = $usuarioAutenticado->id;
            $requisito1->usuarioregistro = $usuarioAutenticado->name;
            $requisito1->ciasegurado = 'PENDIENTE';
            $requisito1->cnacasegurado = 'PENDIENTE';
            $requisito1->save();
        
            $numPolizas = $request->input('numPolizas');
            for ($i = 1; $i <= $numPolizas; $i++) {
                $banco = $request->input('banco' . $i);
        
                if (!empty($banco)) { 
                    $requisitoPoliza = new Requisitosclientesauditoria();
                    $requisitoPoliza->clienteitaid = $cliente->id;
                    $requisitoPoliza->clienteitanombre = $cliente->nombrecompleto;
                    $requisitoPoliza->usuarioid = $usuarioAutenticado->id;
                    $requisitoPoliza->usuarioregistro = $usuarioAutenticado->name;
                    $requisitoPoliza->banco = $banco;
                    $requisitoPoliza->nropolizageneral = $request->input('nropolizageneral' . $i);
                    $requisitoPoliza->polizageneral = $request->input('polizageneral' . $i) ? 'PENDIENTE' : 'NO APLICA';
                    $requisitoPoliza->declasalud = $request->input('declasalud' . $i) ? 'PENDIENTE' : 'NO APLICA';
                    $requisitoPoliza->nropolizadesgravamen = $request->input('nropolizadesgravamen' . $i);
                    $requisitoPoliza->polizasegurodesgravamen = $request->input('polizasegurodesgravamen' . $i) ? 'PENDIENTE' : 'NO APLICA';
                    $requisitoPoliza->save(); 
                }
            }
        
            // Pasar los datos a la vista del PDF
            $pdf = PDF::loadView('admin.asociados.descargarchecklistclienteitaaudi', compact('cliente', 'numPolizas', 'request'));
            $pdfName = 'Requisitos_AuditoriaMedica_' . $cliente->nombrecompleto . '.pdf';
            return $pdf->download($pdfName);
        }
    public function subirdocrequisitosaudi(Cliente $cliente) 
    {
        $clienteitaid = $cliente->id; 
        $userRole = auth()->user()->getRoleNames()->first(); 
        $requisitosCliente = Requisitosclientesauditoria::where('clienteitaid', $clienteitaid)->first();

        $ciaseguradoPendiente = $requisitosCliente ? $requisitosCliente->ciasegurado === 'PENDIENTE' : false;
        $cnacaseguradoPendiente = $requisitosCliente ? $requisitosCliente->cnacasegurado === 'PENDIENTE' : false;
        $polizasgenPendiente = $requisitosCliente ? $requisitosCliente->polizageneral === 'PENDIENTE' : false;
        $declasaludPendiente = $requisitosCliente ? $requisitosCliente->declasalud === 'PENDIENTE' : false;
        $polizaseguroPendiente = $requisitosCliente ? $requisitosCliente->polizasegurodesgravamen === 'PENDIENTE' : false;
        
        $requisitosubido = Requisitosclientesauditoria::where('clienteitaid', $cliente->id)->firstOrFail();
        $ciaseguradoSubido = $requisitosCliente && strpos($requisitosCliente->ciasegurado, '.pdf') !== false ? true : false;
        $cnacaseguradoSubido = $requisitosCliente && strpos($requisitosCliente->cnacasegurado, '.pdf') !== false ? true : false;
        $polizasgenSubido = $requisitosCliente && strpos($requisitosCliente->polizageneral, '.pdf') !== false ? true : false;
        $declasaludSubido = $requisitosCliente && strpos($requisitosCliente->declasalud, '.pdf') !== false ? true : false;
        $polizaseguroSubido = $requisitosCliente && strpos($requisitosCliente->polizasegurodesgravamen, '.pdf') !== false ? true : false;

        $requisitosClientepolizas = Requisitosclientesauditoria::where('clienteitaid', $clienteitaid)->wherenotNull('banco')->get();

        return view('admin.asociados.subirdocrequisitosaudi', compact( 'requisitosubido','requisitosClientepolizas','cliente', 'requisitosCliente', 'cnacaseguradoPendiente', 'ciaseguradoPendiente', 'polizasgenPendiente', 'declasaludPendiente', 'polizaseguroPendiente', 'userRole', 'cnacaseguradoSubido', 'ciaseguradoSubido', 'polizasgenSubido', 'declasaludSubido', 'polizaseguroSubido'));
    }
    public function guardardocrequisitosaudi(Request $request, Cliente $cliente) 
    {
        // Validar archivos y campos adicionales
        $request->validate([
            'cnacasegurado' => 'nullable|mimes:pdf',
            'ciasegurado' => 'nullable|mimes:pdf',
            'polizageneral.*' => 'nullable|mimes:pdf',
            'declasalud.*' => 'nullable|mimes:pdf',
            'polizasegurodesgravamen.*' => 'nullable|mimes:pdf',
            'nropolizageneral.*' => 'nullable|string',
            'nropolizadesgravamen.*' => 'nullable|string',
        ]);

        // Lista de campos a procesar
        $camposArchivos = ['cnacasegurado', 'ciasegurado', 'polizageneral', 'declasalud', 'polizasegurodesgravamen'];
        $nroPolizas = ['nropolizageneral', 'nropolizadesgravamen'];



        $requisito = Requisitosclientesauditoria::where('clienteitaid', $cliente->id)->first();
        $camposArchivos3 = [
            'cnacasegurado', 'ciasegurado'
        ];

        foreach ($camposArchivos3 as $campo) {
            $this->manejarArchivo3($request, $campo, $requisito, $cliente->id);
        }

        // Manejo de archivos
        foreach ($camposArchivos as $campo) {
            if ($request->hasFile($campo)) {
                foreach ($request->file($campo) as $id => $file) {
                    $requisito = Requisitosclientesauditoria::find($id);
                    if ($requisito) {
                        $this->manejarArchivopolizas($request, $campo, $requisito);
                    }
                }
            }
        }

        // Manejo de números de póliza
        foreach ($nroPolizas as $nroPoliza) {
            if ($request->has($nroPoliza)) {
                foreach ($request->input($nroPoliza) as $id => $nro) {
                    $requisito = Requisitosclientesauditoria::find($id);
                    if ($requisito) {
                        // Actualizar el número de póliza correspondiente
                        $requisito->update([$nroPoliza => $nro]);
                    }
                }
            }
        }

        return redirect()->route('admin.asociados.subirdocrequisitosaudi', $cliente)->with('info', 'Los documentos y números de póliza se subieron con éxito');
    }
    protected function manejarArchivopolizas(Request $request, string $campo, $requisito)
    {
        if ($request->hasFile($campo)) {
            // Obtén los archivos de la solicitud
            $files = $request->file($campo);

            // Itera sobre cada archivo
            foreach ($files as $file) {
                // Asegúrate de que $file sea un objeto UploadedFile
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $carpetaCliente = public_path("/requisitosclientesita/{$requisito->clienteitaid}");

                    // Crear la carpeta si no existe
                    if (!file_exists($carpetaCliente)) {
                        mkdir($carpetaCliente, 0755, true);
                    }

                    // Generar un nombre único para el archivo
                    $archivo = time() . '_' . $file->getClientOriginalName();

                    // Mover el archivo a la carpeta
                    $file->move($carpetaCliente, $archivo);

                    // Actualizar el modelo para el requisito específico
                    $requisito->update([$campo => $archivo]);
                }
            }
        }
    }
    protected function manejarArchivo3(Request $request, $campo, $requisito, $clienteId)
    {
        if ($request->hasFile($campo)) {
            $file = $request->file($campo);
            $carpetaCliente = public_path("/requisitosclientesita/{$clienteId}");

            // Crear la carpeta si no existe
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            // Generar un nombre único para el archivo
            $archivo = time() . '_' . $file->getClientOriginalName();

            // Mover el archivo a la carpeta
            $file->move($carpetaCliente, $archivo);

            // Actualizar el modelo
            $requisito->update([$campo => $archivo]);
        }
    }
//
//REQUISITOS CLIENTE ITA APELACION 

    public function generarchecklistclienteitaapelacion(Cliente $cliente) 
        {
            $tieneRequisitos = RequisitoSubCliente::where('clienteitaid', $cliente->id)
                ->where('servicio', 'APELACION')->exists();
            $estadoLaboral = strtolower($cliente->estadolaboral);
            $numHijosMenores = $cliente->numhijosmenores;
            $estadoCivil = strtolower($cliente->estadocivil);

            $rolusuario = auth()->user()->getRoleNames()->first(); 

            $registroExistente = Estadocotizacionsubcliente::where('clienteitaid', $cliente->id)
                ->where('detalle', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS')
                ->first();
            $registroaprobadoExistente = Estadocotizacionsubcliente::where('clienteitaid', $cliente->id)
                ->where('detalle', 'APROBADO PARA INICIAR A CREAR BATERIA')
                ->first();
        
            return view('admin.asociados.generarchecklistclienteitaapelacion', compact(
                'cliente', 
                'tieneRequisitos', 
                'estadoLaboral',
                'numHijosMenores',  
                'estadoCivil', 
                'registroExistente','rolusuario','registroaprobadoExistente'
            ));
        }
    public function descargarchecklistclienteitaapelacion(Request $request, Cliente $cliente)
        {
            $usuarioAutenticado = Auth::user();
            $documentosSeleccionados = json_decode($request->input('documentosSeleccionados'));
            $documentosSeleccionados2 = json_decode($request->input('documentosSeleccionados2'));

            $requisito = new RequisitoSubCliente();
            $requisito->clienteitaid = $cliente->id;
            $requisito->clienteitanombre = $cliente->nombrecompleto;
            $requisito->usuarioid = $usuarioAutenticado->id;
            $requisito->usuarioregistro = $usuarioAutenticado->name;
            $requisito->servicio = 'APELACION';

            $nombreDocumentos = [
                'poder' => 'PODER',
                'avcci' => 'AVC / CARNET ASEGURADO',
                'cnacasegurado' => 'CERTIFICADO NACIMIENTO ASEGURADO',
                'ciasegurado' => 'CARNET IDENTIDAD ASEGURADO',
                'cmatrimonio' => 'CERTIFICADO MATRIMONIO',
                'cnacconyuge' => 'CERTIFICADO NACIMIENTO CONYUGE',
                'ciconyuge' => 'CARNET IDENTIDAD CONYUGE',
                'cnacjihos' => 'CERTIFICADO NACIMIENTO HIJOS < 25',
                'cihijos' => 'CARNET IDENTIDAD HIJOS < 25',
                'crodomicilio' => 'CROQUIS DOMICILIO',
                'contrato' => 'CONTRATO',
                'cunionlibre' => 'CERTIFICADO UNIÓN LIBRE',
                'cnacimientounionlibre' => 'CERTIFICADO NACIMIENTO UNIÓN LIBRE',
                'ciunionlibre' => 'CARNET IDENTIDAD UNIÓN LIBRE',
                'cdivorcio' => 'CERTIFICADO DIVORCIO',
                'cdefuncion' => 'CERTIFICADO DIFUNCIÓN',
                'recordservicios' => 'RECORD SERVICIOS',
                'dictamencalentenc' => 'DICTAMEN CALIFICACION ENTIDAD ENCARGADA',
                
            ];

            $nombreDocumentos2 = [
                'ctrabajo' => 'CERTIFICADO TRABAJO',
                'boletapago' => 'BOLETA PAGO',
                'egestora' => 'EXTRACTO GESTORA',
                'denfaccidente' => 'DENUNCIA ENFERMEDAD ACCIDENTE',
                'actdatos' => 'ACTUALIZACIÓN DATOS',
                'resolinvhijos' => 'RESOL. INVAL. HIJOS < 25',
                'infomedicasalud' => 'INFORMACION MEDICA',
                'anteriordictamen' => 'ANTERIOR DICTAMEN O RESOLUCION',
            ];

            foreach ($documentosSeleccionados as $documento) {
                $nombreCompleto = isset($nombreDocumentos[$documento]) ? $nombreDocumentos[$documento] : $documento;
            $valorDocumento = $request->has($documento) && $request->input($documento) === 'NO' ? 'NO' : 'PENDIENTE';
                switch ($documento) {
                    case 'poder':
                        $requisito->poder = $valorDocumento;
                        break;
                    case 'avcci':
                        $requisito->avcci = $valorDocumento;
                        break;
                    case 'cnacasegurado':
                        $requisito->cnacasegurado = $valorDocumento;
                        break;
                    case 'ciasegurado':
                        $requisito->ciasegurado = $valorDocumento;
                        break;
                    case 'cmatrimonio':
                        $requisito->cmatrimonio = $valorDocumento;
                        break;
                    case 'cnacconyuge':
                        $requisito->cnacconyuge = $valorDocumento;
                        break;
                    case 'ciconyuge':
                        $requisito->ciconyuge = $valorDocumento;
                        break;
                    case 'cnacjihos':
                        $requisito->cnacjihos = $valorDocumento;
                        break;
                    case 'cihijos':
                        $requisito->cihijos = $valorDocumento;
                        break;
                    case 'crodomicilio':
                        $requisito->crodomicilio = $valorDocumento;
                        break;
                    case 'contrato':
                        $requisito->contrato = $valorDocumento;
                        break;
                    case 'cunionlibre':
                        $requisito->cunionlibre = $valorDocumento;
                        break;
                    case 'cnacimientounionlibre':
                        $requisito->cnacimientounionlibre = $valorDocumento;
                        break;
                    case 'ciunionlibre':
                        $requisito->ciunionlibre = $valorDocumento;
                        break;
                    case 'cdivorcio':
                        $requisito->cdivorcio = $valorDocumento;
                        break;
                    case 'cdefuncion':
                        $requisito->cdefuncion = $valorDocumento;
                        break;
                    case 'recordservicios':
                        $requisito->recordservicios = $valorDocumento;
                        break;
                    case 'dictamencalentenc':
                        $requisito->dictamencalentenc = $valorDocumento;
                        break;
                    default:
                        break;
                }
            }

            foreach ($documentosSeleccionados2 as $documento) {
                $nombreCompleto = isset($nombreDocumentos2[$documento]) ? $nombreDocumentos2[$documento] : $documento;
            $valorDocumento = $request->has($documento) && $request->input($documento) === 'NO' ? 'NO' : 'PENDIENTE';
                switch ($documento) {
                    case 'ctrabajo':
                        $requisito->ctrabajo = $valorDocumento;
                        break;
                    case 'boletapago':
                        $requisito->boletapago = $valorDocumento;
                        break;
                    case 'egestora':
                        $requisito->egestora = $valorDocumento;
                        break;
                    case 'denfaccidente':
                        $requisito->denfaccidente = $valorDocumento;
                        break;
                    case 'actdatos':
                        $requisito->actdatos = $valorDocumento;
                        break;
                    case 'resolinvhijos':
                        $requisito->resolinvhijos = $valorDocumento;
                        break;
                    case 'infomedicasalud':
                        $requisito->infomedicasalud = $valorDocumento;
                        break;
                    case 'anteriordictamen':
                        $requisito->anteriordictamen = $valorDocumento;
                        break;
                    default:
                        break;
                }
            }

            $requisito->save();

            $pdf = PDF::loadView('admin.asociados.descargarchecklistclienteitaapelacion', compact('cliente', 'documentosSeleccionados', 'nombreDocumentos', 'documentosSeleccionados2', 'nombreDocumentos2'));
            $pdfName = 'Requisitos_Apelacion_' . $cliente->nombrecompleto . '.pdf';
            return $pdf->download($pdfName);
        }
    public function subirdocrequisitosapelacion(Cliente $cliente)
        {
            $clienteitaid = $cliente->id; 
            $userRole = auth()->user()->getRoleNames()->first(); 
            $requisitosCliente = RequisitoSubCliente::where('clienteitaid', $clienteitaid)
            ->where('servicio', 'APELACION')->first();
    
            $poderPendiente = $requisitosCliente ? $requisitosCliente->poder === 'PENDIENTE' : false;
            $avcciPendiente = $requisitosCliente ? $requisitosCliente->avcci === 'PENDIENTE' : false;
            $cnacaseguradoPendiente = $requisitosCliente ? $requisitosCliente->cnacasegurado === 'PENDIENTE' : false;
            $ciaseguradoPendiente = $requisitosCliente ? $requisitosCliente->ciasegurado === 'PENDIENTE' : false;
            $cmatrimonioPendiente = $requisitosCliente ? $requisitosCliente->cmatrimonio === 'PENDIENTE' : false;
            $cnacconyugePendiente = $requisitosCliente ? $requisitosCliente->cnacconyuge === 'PENDIENTE' : false;
            $ciconyugePendiente = $requisitosCliente ? $requisitosCliente->ciconyuge === 'PENDIENTE' : false;
            $cnacjihosPendiente = $requisitosCliente ? $requisitosCliente->cnacjihos === 'PENDIENTE' : false;
            $cihijosPendiente = $requisitosCliente ? $requisitosCliente->cihijos === 'PENDIENTE' : false;
            $denfaccidentePendiente = $requisitosCliente ? $requisitosCliente->denfaccidente === 'PENDIENTE' : false;
            $crodomicilioPendiente = $requisitosCliente ? $requisitosCliente->crodomicilio === 'PENDIENTE' : false;
            $contratoPendiente = $requisitosCliente ? $requisitosCliente->contrato === 'PENDIENTE' : false;
            $recordserviciosPendiente = $requisitosCliente ? $requisitosCliente->recordservicios === 'PENDIENTE' : false;
            
            $cunionlibrePendiente = $requisitosCliente ? $requisitosCliente->cunionlibre === 'PENDIENTE' : false;
            $cnacimientounionlibrePendiente = $requisitosCliente ? $requisitosCliente->cnacimientounionlibre === 'PENDIENTE' : false;
            $ciunionlibrePendiente = $requisitosCliente ? $requisitosCliente->ciunionlibre === 'PENDIENTE' : false;
            $cdivorcioPendiente = $requisitosCliente ? $requisitosCliente->cdivorcio === 'PENDIENTE' : false;
            $cdefuncionPendiente = $requisitosCliente ? $requisitosCliente->cdefuncion === 'PENDIENTE' : false;

            $ctrabajoPendiente = $requisitosCliente ? $requisitosCliente->ctrabajo === 'PENDIENTE' : false;
            $boletapagoPendiente = $requisitosCliente ? $requisitosCliente->boletapago === 'PENDIENTE' : false;
            $egestoraPendiente = $requisitosCliente ? $requisitosCliente->egestora === 'PENDIENTE' : false;
            $actdatosPendiente = $requisitosCliente ? $requisitosCliente->actdatos === 'PENDIENTE' : false;
            $resolinvhijosPendiente = $requisitosCliente ? $requisitosCliente->resolinvhijos === 'PENDIENTE' : false;

            $dictamencalentencPendiente = $requisitosCliente ? $requisitosCliente->dictamencalentenc === 'PENDIENTE' : false;
            $infomedicasaludPendiente = $requisitosCliente ? $requisitosCliente->infomedicasalud === 'PENDIENTE' : false;
            $anteriordictamenPendiente = $requisitosCliente ? $requisitosCliente->anteriordictamen === 'PENDIENTE' : false;
            
            
            $requisito = RequisitoSubCliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'APELACION')->firstOrFail();
            $poderSubido = $requisitosCliente && strpos($requisitosCliente->poder, '.pdf') !== false ? true:false;
            $avcciSubido = $requisitosCliente && strpos($requisitosCliente->avcci, '.pdf') !== false ? true:false;
            $cnacaseguradoSubido = $requisitosCliente && strpos($requisitosCliente->cnacasegurado, '.pdf') !== false ? true:false;
            $ciaseguradoSubido = $requisitosCliente && strpos($requisitosCliente->ciasegurado, '.pdf') !== false ? true:false;
            $cmatrimonioSubido = $requisitosCliente && strpos($requisitosCliente->cmatrimonio, '.pdf') !== false ? true:false;
            $cnacconyugeSubido = $requisitosCliente && strpos($requisitosCliente->cnacconyuge, '.pdf') !== false ? true:false;
            $ciconyugeSubido = $requisitosCliente && strpos($requisitosCliente->ciconyuge, '.pdf') !== false ? true:false;
            $cnacjihosSubido = $requisitosCliente && strpos($requisitosCliente->cnacjihos, '.pdf') !== false ? true:false;
            $cihijosSubido = $requisitosCliente && strpos($requisitosCliente->cihijos, '.pdf') !== false ? true:false;
            $denfaccidenteSubido = $requisitosCliente && strpos($requisitosCliente->denfaccidente, '.pdf') !== false ? true:false;
            $crodomicilioSubido = $requisitosCliente && strpos($requisitosCliente->crodomicilio, '.pdf') !== false ? true:false;
            $contratoSubido = $requisitosCliente && strpos($requisitosCliente->contrato, '.pdf') !== false ? true:false;
            $recordserviciosSubido = $requisitosCliente && strpos($requisitosCliente->recordservicios, '.pdf') !== false ? true:false;
            
            $cunionlibreSubido = $requisitosCliente && strpos($requisitosCliente->cunionlibre, '.pdf') !== false ? true:false;
            $cnacimientounionlibreSubido = $requisitosCliente && strpos($requisitosCliente->cnacimientounionlibre, '.pdf') !== false ? true:false;
            $ciunionlibreSubido = $requisitosCliente && strpos($requisitosCliente->ciunionlibre, '.pdf') !== false ? true:false;
            $cdivorcioSubido = $requisitosCliente && strpos($requisitosCliente->cdivorcio, '.pdf') !== false ? true:false;
            $cdefuncionSubido = $requisitosCliente && strpos($requisitosCliente->cdefuncion, '.pdf') !== false ? true:false;

            $ctrabajoSubido = $requisitosCliente && strpos($requisitosCliente->ctrabajo, '.pdf') !== false ? true:false;
            $boletapagoSubido = $requisitosCliente && strpos($requisitosCliente->boletapago, '.pdf') !== false ? true:false;
            $egestoraSubido = $requisitosCliente && strpos($requisitosCliente->egestora, '.pdf') !== false ? true:false;
            $actdatosSubido = $requisitosCliente && strpos($requisitosCliente->actdatos, '.pdf') !== false ? true:false;
            $resolinvhijosSubido = $requisitosCliente && strpos($requisitosCliente->resolinvhijos, '.pdf') !== false ? true:false;

            $dictamencalentencSubido = $requisitosCliente && strpos($requisitosCliente->dictamencalentenc, '.pdf') !== false ? true:false;
            $infomedicasaludSubido = $requisitosCliente && strpos($requisitosCliente->infomedicasalud, '.pdf') !== false ? true:false;
            $anteriordictamenSubido = $requisitosCliente && strpos($requisitosCliente->anteriordictamen, '.pdf') !== false ? true:false;
            
            return view('admin.asociados.subirdocrequisitosapelacion', compact('cliente', 'poderPendiente', 'avcciPendiente', 
            'cnacaseguradoPendiente','ciaseguradoPendiente','cmatrimonioPendiente','cnacconyugePendiente','ciconyugePendiente',
            'cnacjihosPendiente','cihijosPendiente','denfaccidentePendiente','recordserviciosPendiente','crodomicilioPendiente','contratoPendiente', 'requisito'
            , 'poderSubido', 'avcciSubido', 'cnacaseguradoSubido', 'ciaseguradoSubido', 'cmatrimonioSubido', 'cnacconyugeSubido'
            , 'ciconyugeSubido', 'cnacjihosSubido', 'cihijosSubido', 'denfaccidenteSubido', 'crodomicilioSubido', 'contratoSubido'
            , 'ctrabajoPendiente', 'boletapagoPendiente', 'egestoraPendiente', 'actdatosPendiente', 'resolinvhijosPendiente'
            , 'ctrabajoSubido', 'boletapagoSubido', 'egestoraSubido', 'actdatosSubido', 'resolinvhijosSubido'
            , 'cunionlibrePendiente', 'cnacimientounionlibrePendiente', 'ciunionlibrePendiente', 'cdivorcioPendiente', 'cdefuncionPendiente'
            , 'cunionlibreSubido', 'recordserviciosSubido', 'cnacimientounionlibreSubido', 'ciunionlibreSubido', 'cdivorcioSubido', 'cdefuncionSubido', 'userRole'
            , 'dictamencalentencPendiente','infomedicasaludPendiente','anteriordictamenPendiente','dictamencalentencSubido','infomedicasaludSubido','anteriordictamenSubido',));
        }
    public function guardardocrequisitosapelacion(Request $request, Cliente $cliente)
        {
            $requisito = RequisitoSubCliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'APELACION')->firstOrFail();
    
            $request->validate([
                'poder' => 'nullable|mimes:pdf',
                'numeropoder' => 'nullable|max:45',
                'avcci' => 'nullable|mimes:pdf',
                'cnacasegurado' => 'nullable|mimes:pdf',
                'ciasegurado' => 'nullable|mimes:pdf',
                'cmatrimonio' => 'nullable|mimes:pdf',
                'cnacconyuge' => 'nullable|mimes:pdf',
                'ciconyuge' => 'nullable|mimes:pdf',
                'cnacjihos' => 'nullable|mimes:pdf',
                'cihijos' => 'nullable|mimes:pdf',
                'denfaccidente' => 'nullable|mimes:pdf',
                'crodomicilio' => 'nullable|mimes:pdf',
                'contrato' => 'nullable|mimes:pdf',
                'ctrabajo' => 'nullable|mimes:pdf',
                'boletapago' => 'nullable|mimes:pdf',
                'egestora' => 'nullable|mimes:pdf',
                'actdatos' => 'nullable|mimes:pdf',
                'resolinvhijos' => 'nullable|mimes:pdf',
                'cunionlibre' => 'nullable|mimes:pdf',
                'cnacimientounionlibre' => 'nullable|mimes:pdf',
                'ciunionlibre' => 'nullable|mimes:pdf',
                'cdivorcio' => 'nullable|mimes:pdf',
                'cdefuncion' => 'nullable|mimes:pdf',
                'dictamencalentenc' => 'nullable|mimes:pdf',
                'infomedicasalud' => 'nullable|mimes:pdf',
                'anteriordictamen' => 'nullable|mimes:pdf',
            ]);

            $camposArchivos = [
                'poder', 'avcci', 'cnacasegurado', 'ciasegurado', 'cmatrimonio', 
                'cnacconyuge', 'ciconyuge', 'cnacjihos', 'cihijos', 'denfaccidente', 
                'crodomicilio', 'contrato', 'ctrabajo', 'boletapago', 'egestora', 'actdatos', 'resolinvhijos'
                , 'cunionlibre', 'cnacimientounionlibre', 'ciunionlibre', 'cdivorcio', 'cdefuncion'
                , 'dictamencalentenc', 'infomedicasalud', 'anteriordictamen'
            ];

            foreach ($camposArchivos as $campo) {
                $this->manejarArchivo($request, $campo, $requisito, $cliente->id);
            }

            if ($request->filled('numeropoder')) {
                $requisito->update(['numeropoder' => $request->input('numeropoder')]);
            }
    
            return redirect()->route('admin.asociados.subirdocrequisitosapelacion', $cliente)
                             ->with('info', 'El documento se subió con éxito');
        }
//
//REQUISITOS CLIENTE ITA SEGUNDA SOLICITUD 

    public function generarchecklistclienteitasegsolicitud(Cliente $cliente) 
        {
            $tieneRequisitos = RequisitoSubCliente::where('clienteitaid', $cliente->id)
                ->where('servicio', 'SEGUNDA SOLICITUD')->exists();
            $estadoLaboral = strtolower($cliente->estadolaboral);
            $numHijosMenores = $cliente->numhijosmenores;
            $estadoCivil = strtolower($cliente->estadocivil);

            $rolusuario = auth()->user()->getRoleNames()->first(); 

            $registroExistente = Estadocotizacionsubcliente::where('clienteitaid', $cliente->id)
                ->where('detalle', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS')
                ->first();
            $registroaprobadoExistente = Estadocotizacionsubcliente::where('clienteitaid', $cliente->id)
                ->where('detalle', 'APROBADO PARA INICIAR A CREAR BATERIA')
                ->first();
        
            return view('admin.asociados.generarchecklistclienteitasegsolicitud', compact(
                'cliente', 
                'tieneRequisitos', 
                'estadoLaboral',
                'numHijosMenores',  
                'estadoCivil', 
                'registroExistente','rolusuario','registroaprobadoExistente'
            ));
        }
    public function descargarchecklistclienteitasegsolicitud(Request $request, Cliente $cliente)
        {
            $usuarioAutenticado = Auth::user();
            $documentosSeleccionados = json_decode($request->input('documentosSeleccionados'));
            $documentosSeleccionados2 = json_decode($request->input('documentosSeleccionados2'));

            $requisito = new RequisitoSubCliente();
            $requisito->clienteitaid = $cliente->id;
            $requisito->clienteitanombre = $cliente->nombrecompleto;
            $requisito->usuarioid = $usuarioAutenticado->id;
            $requisito->usuarioregistro = $usuarioAutenticado->name;
            $requisito->servicio = 'SEGUNDA SOLICITUD';

            $nombreDocumentos = [
                'poder' => 'PODER',
                'avcci' => 'AVC / CARNET ASEGURADO',
                'cnacasegurado' => 'CERTIFICADO NACIMIENTO ASEGURADO',
                'ciasegurado' => 'CARNET IDENTIDAD ASEGURADO',
                'cmatrimonio' => 'CERTIFICADO MATRIMONIO',
                'cnacconyuge' => 'CERTIFICADO NACIMIENTO CONYUGE',
                'ciconyuge' => 'CARNET IDENTIDAD CONYUGE',
                'cnacjihos' => 'CERTIFICADO NACIMIENTO HIJOS < 25',
                'cihijos' => 'CARNET IDENTIDAD HIJOS < 25',
                'crodomicilio' => 'CROQUIS DOMICILIO',
                'contrato' => 'CONTRATO',
                'cunionlibre' => 'CERTIFICADO UNIÓN LIBRE',
                'cnacimientounionlibre' => 'CERTIFICADO NACIMIENTO UNIÓN LIBRE',
                'ciunionlibre' => 'CARNET IDENTIDAD UNIÓN LIBRE',
                'cdivorcio' => 'CERTIFICADO DIVORCIO',
                'cdefuncion' => 'CERTIFICADO DIFUNCIÓN',
                'recordservicios' => 'RECORD SERVICIOS',
                'dictamencalentenc' => 'DICTAMEN CALIFICACION ENTIDAD ENCARGADA',
                
            ];

            $nombreDocumentos2 = [
                'ctrabajo' => 'CERTIFICADO TRABAJO',
                'boletapago' => 'BOLETA PAGO',
                'egestora' => 'EXTRACTO GESTORA',
                'denfaccidente' => 'DENUNCIA ENFERMEDAD ACCIDENTE',
                'actdatos' => 'ACTUALIZACIÓN DATOS',
                'resolinvhijos' => 'RESOL. INVAL. HIJOS < 25',
                'infomedicasalud' => 'INFORMACION MEDICA',
                'anteriordictamen' => 'ANTERIOR DICTAMEN O RESOLUCION',
                'poderciapoderado' => 'PODER Y CARNET IDENTIDAD APODERADO',
            ];

            foreach ($documentosSeleccionados as $documento) {
                $nombreCompleto = isset($nombreDocumentos[$documento]) ? $nombreDocumentos[$documento] : $documento;
            $valorDocumento = $request->has($documento) && $request->input($documento) === 'NO' ? 'NO' : 'PENDIENTE';
                switch ($documento) {
                    case 'poder':
                        $requisito->poder = $valorDocumento;
                        break;
                    case 'avcci':
                        $requisito->avcci = $valorDocumento;
                        break;
                    case 'cnacasegurado':
                        $requisito->cnacasegurado = $valorDocumento;
                        break;
                    case 'ciasegurado':
                        $requisito->ciasegurado = $valorDocumento;
                        break;
                    case 'cmatrimonio':
                        $requisito->cmatrimonio = $valorDocumento;
                        break;
                    case 'cnacconyuge':
                        $requisito->cnacconyuge = $valorDocumento;
                        break;
                    case 'ciconyuge':
                        $requisito->ciconyuge = $valorDocumento;
                        break;
                    case 'cnacjihos':
                        $requisito->cnacjihos = $valorDocumento;
                        break;
                    case 'cihijos':
                        $requisito->cihijos = $valorDocumento;
                        break;
                    case 'crodomicilio':
                        $requisito->crodomicilio = $valorDocumento;
                        break;
                    case 'contrato':
                        $requisito->contrato = $valorDocumento;
                        break;
                    case 'cunionlibre':
                        $requisito->cunionlibre = $valorDocumento;
                        break;
                    case 'cnacimientounionlibre':
                        $requisito->cnacimientounionlibre = $valorDocumento;
                        break;
                    case 'ciunionlibre':
                        $requisito->ciunionlibre = $valorDocumento;
                        break;
                    case 'cdivorcio':
                        $requisito->cdivorcio = $valorDocumento;
                        break;
                    case 'cdefuncion':
                        $requisito->cdefuncion = $valorDocumento;
                        break;
                    case 'recordservicios':
                        $requisito->recordservicios = $valorDocumento;
                        break;
                    case 'dictamencalentenc':
                        $requisito->dictamencalentenc = $valorDocumento;
                        break;
                    default:
                        break;
                }
            }

            foreach ($documentosSeleccionados2 as $documento) {
                $nombreCompleto = isset($nombreDocumentos2[$documento]) ? $nombreDocumentos2[$documento] : $documento;
            $valorDocumento = $request->has($documento) && $request->input($documento) === 'NO' ? 'NO' : 'PENDIENTE';
                switch ($documento) {
                    case 'ctrabajo':
                        $requisito->ctrabajo = $valorDocumento;
                        break;
                    case 'boletapago':
                        $requisito->boletapago = $valorDocumento;
                        break;
                    case 'egestora':
                        $requisito->egestora = $valorDocumento;
                        break;
                    case 'denfaccidente':
                        $requisito->denfaccidente = $valorDocumento;
                        break;
                    case 'actdatos':
                        $requisito->actdatos = $valorDocumento;
                        break;
                    case 'resolinvhijos':
                        $requisito->resolinvhijos = $valorDocumento;
                        break;
                    case 'infomedicasalud':
                        $requisito->infomedicasalud = $valorDocumento;
                        break;
                    case 'anteriordictamen':
                        $requisito->anteriordictamen = $valorDocumento;
                        break;
                    case 'poderciapoderado':
                        $requisito->poderciapoderado = $valorDocumento;
                        break;
                    default:
                        break;
                }
            }

            $requisito->save();

            $pdf = PDF::loadView('admin.asociados.descargarchecklistclienteitasegsolicitud', compact('cliente', 'documentosSeleccionados', 'nombreDocumentos', 'documentosSeleccionados2', 'nombreDocumentos2'));
            $pdfName = 'Requisitos_SegundaSolicitud_' . $cliente->nombrecompleto . '.pdf';
            return $pdf->download($pdfName);
        }
    public function subirdocrequisitossegsolicitud(Cliente $cliente)
        {
            $clienteitaid = $cliente->id; 
            $userRole = auth()->user()->getRoleNames()->first(); 
            $requisitosCliente = RequisitoSubCliente::where('clienteitaid', $clienteitaid)
            ->where('servicio', 'SEGUNDA SOLICITUD')->first();
    
            $poderPendiente = $requisitosCliente ? $requisitosCliente->poder === 'PENDIENTE' : false;
            $avcciPendiente = $requisitosCliente ? $requisitosCliente->avcci === 'PENDIENTE' : false;
            $cnacaseguradoPendiente = $requisitosCliente ? $requisitosCliente->cnacasegurado === 'PENDIENTE' : false;
            $ciaseguradoPendiente = $requisitosCliente ? $requisitosCliente->ciasegurado === 'PENDIENTE' : false;
            $cmatrimonioPendiente = $requisitosCliente ? $requisitosCliente->cmatrimonio === 'PENDIENTE' : false;
            $cnacconyugePendiente = $requisitosCliente ? $requisitosCliente->cnacconyuge === 'PENDIENTE' : false;
            $ciconyugePendiente = $requisitosCliente ? $requisitosCliente->ciconyuge === 'PENDIENTE' : false;
            $cnacjihosPendiente = $requisitosCliente ? $requisitosCliente->cnacjihos === 'PENDIENTE' : false;
            $cihijosPendiente = $requisitosCliente ? $requisitosCliente->cihijos === 'PENDIENTE' : false;
            $denfaccidentePendiente = $requisitosCliente ? $requisitosCliente->denfaccidente === 'PENDIENTE' : false;
            $crodomicilioPendiente = $requisitosCliente ? $requisitosCliente->crodomicilio === 'PENDIENTE' : false;
            $contratoPendiente = $requisitosCliente ? $requisitosCliente->contrato === 'PENDIENTE' : false;
            $recordserviciosPendiente = $requisitosCliente ? $requisitosCliente->recordservicios === 'PENDIENTE' : false;
            
            $cunionlibrePendiente = $requisitosCliente ? $requisitosCliente->cunionlibre === 'PENDIENTE' : false;
            $cnacimientounionlibrePendiente = $requisitosCliente ? $requisitosCliente->cnacimientounionlibre === 'PENDIENTE' : false;
            $ciunionlibrePendiente = $requisitosCliente ? $requisitosCliente->ciunionlibre === 'PENDIENTE' : false;
            $cdivorcioPendiente = $requisitosCliente ? $requisitosCliente->cdivorcio === 'PENDIENTE' : false;
            $cdefuncionPendiente = $requisitosCliente ? $requisitosCliente->cdefuncion === 'PENDIENTE' : false;

            $ctrabajoPendiente = $requisitosCliente ? $requisitosCliente->ctrabajo === 'PENDIENTE' : false;
            $boletapagoPendiente = $requisitosCliente ? $requisitosCliente->boletapago === 'PENDIENTE' : false;
            $egestoraPendiente = $requisitosCliente ? $requisitosCliente->egestora === 'PENDIENTE' : false;
            $actdatosPendiente = $requisitosCliente ? $requisitosCliente->actdatos === 'PENDIENTE' : false;
            $resolinvhijosPendiente = $requisitosCliente ? $requisitosCliente->resolinvhijos === 'PENDIENTE' : false;

            $dictamencalentencPendiente = $requisitosCliente ? $requisitosCliente->dictamencalentenc === 'PENDIENTE' : false;
            $infomedicasaludPendiente = $requisitosCliente ? $requisitosCliente->infomedicasalud === 'PENDIENTE' : false;
            $anteriordictamenPendiente = $requisitosCliente ? $requisitosCliente->anteriordictamen === 'PENDIENTE' : false;
            $poderciapoderadoPendiente = $requisitosCliente ? $requisitosCliente->poderciapoderado === 'PENDIENTE' : false;
            
            
            $requisito = RequisitoSubCliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'SEGUNDA SOLICITUD')->firstOrFail();
            $poderSubido = $requisitosCliente && strpos($requisitosCliente->poder, '.pdf') !== false ? true:false;
            $avcciSubido = $requisitosCliente && strpos($requisitosCliente->avcci, '.pdf') !== false ? true:false;
            $cnacaseguradoSubido = $requisitosCliente && strpos($requisitosCliente->cnacasegurado, '.pdf') !== false ? true:false;
            $ciaseguradoSubido = $requisitosCliente && strpos($requisitosCliente->ciasegurado, '.pdf') !== false ? true:false;
            $cmatrimonioSubido = $requisitosCliente && strpos($requisitosCliente->cmatrimonio, '.pdf') !== false ? true:false;
            $cnacconyugeSubido = $requisitosCliente && strpos($requisitosCliente->cnacconyuge, '.pdf') !== false ? true:false;
            $ciconyugeSubido = $requisitosCliente && strpos($requisitosCliente->ciconyuge, '.pdf') !== false ? true:false;
            $cnacjihosSubido = $requisitosCliente && strpos($requisitosCliente->cnacjihos, '.pdf') !== false ? true:false;
            $cihijosSubido = $requisitosCliente && strpos($requisitosCliente->cihijos, '.pdf') !== false ? true:false;
            $denfaccidenteSubido = $requisitosCliente && strpos($requisitosCliente->denfaccidente, '.pdf') !== false ? true:false;
            $crodomicilioSubido = $requisitosCliente && strpos($requisitosCliente->crodomicilio, '.pdf') !== false ? true:false;
            $contratoSubido = $requisitosCliente && strpos($requisitosCliente->contrato, '.pdf') !== false ? true:false;
            $recordserviciosSubido = $requisitosCliente && strpos($requisitosCliente->recordservicios, '.pdf') !== false ? true:false;
            
            $cunionlibreSubido = $requisitosCliente && strpos($requisitosCliente->cunionlibre, '.pdf') !== false ? true:false;
            $cnacimientounionlibreSubido = $requisitosCliente && strpos($requisitosCliente->cnacimientounionlibre, '.pdf') !== false ? true:false;
            $ciunionlibreSubido = $requisitosCliente && strpos($requisitosCliente->ciunionlibre, '.pdf') !== false ? true:false;
            $cdivorcioSubido = $requisitosCliente && strpos($requisitosCliente->cdivorcio, '.pdf') !== false ? true:false;
            $cdefuncionSubido = $requisitosCliente && strpos($requisitosCliente->cdefuncion, '.pdf') !== false ? true:false;

            $ctrabajoSubido = $requisitosCliente && strpos($requisitosCliente->ctrabajo, '.pdf') !== false ? true:false;
            $boletapagoSubido = $requisitosCliente && strpos($requisitosCliente->boletapago, '.pdf') !== false ? true:false;
            $egestoraSubido = $requisitosCliente && strpos($requisitosCliente->egestora, '.pdf') !== false ? true:false;
            $actdatosSubido = $requisitosCliente && strpos($requisitosCliente->actdatos, '.pdf') !== false ? true:false;
            $resolinvhijosSubido = $requisitosCliente && strpos($requisitosCliente->resolinvhijos, '.pdf') !== false ? true:false;

            $dictamencalentencSubido = $requisitosCliente && strpos($requisitosCliente->dictamencalentenc, '.pdf') !== false ? true:false;
            $infomedicasaludSubido = $requisitosCliente && strpos($requisitosCliente->infomedicasalud, '.pdf') !== false ? true:false;
            $anteriordictamenSubido = $requisitosCliente && strpos($requisitosCliente->anteriordictamen, '.pdf') !== false ? true:false;
            $poderciapoderadoSubido = $requisitosCliente && strpos($requisitosCliente->poderciapoderado, '.pdf') !== false ? true:false;
            
            return view('admin.asociados.subirdocrequisitossegsolicitud', compact('cliente', 'poderPendiente', 'avcciPendiente', 
            'cnacaseguradoPendiente','ciaseguradoPendiente','cmatrimonioPendiente','cnacconyugePendiente','ciconyugePendiente',
            'cnacjihosPendiente','cihijosPendiente','denfaccidentePendiente','recordserviciosPendiente','crodomicilioPendiente','contratoPendiente', 'requisito'
            , 'poderSubido', 'avcciSubido', 'cnacaseguradoSubido', 'ciaseguradoSubido', 'cmatrimonioSubido', 'cnacconyugeSubido'
            , 'ciconyugeSubido', 'cnacjihosSubido', 'cihijosSubido', 'denfaccidenteSubido', 'crodomicilioSubido', 'contratoSubido'
            , 'ctrabajoPendiente', 'boletapagoPendiente', 'egestoraPendiente', 'actdatosPendiente', 'resolinvhijosPendiente'
            , 'ctrabajoSubido', 'boletapagoSubido', 'egestoraSubido', 'actdatosSubido', 'resolinvhijosSubido'
            , 'cunionlibrePendiente', 'cnacimientounionlibrePendiente', 'ciunionlibrePendiente', 'cdivorcioPendiente', 'cdefuncionPendiente'
            , 'cunionlibreSubido', 'recordserviciosSubido', 'cnacimientounionlibreSubido', 'ciunionlibreSubido', 'cdivorcioSubido', 'cdefuncionSubido', 'userRole'
            , 'dictamencalentencPendiente','infomedicasaludPendiente','anteriordictamenPendiente','dictamencalentencSubido','infomedicasaludSubido','anteriordictamenSubido'
            , 'poderciapoderadoPendiente', 'poderciapoderadoSubido'));
        }
    public function guardardocrequisitossegsolicitud(Request $request, Cliente $cliente)
        {
            $requisito = RequisitoSubCliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'SEGUNDA SOLICITUD')->firstOrFail();
    
            $request->validate([
                'poder' => 'nullable|mimes:pdf',
                'numeropoder' => 'nullable|max:45',
                'avcci' => 'nullable|mimes:pdf',
                'cnacasegurado' => 'nullable|mimes:pdf',
                'ciasegurado' => 'nullable|mimes:pdf',
                'cmatrimonio' => 'nullable|mimes:pdf',
                'cnacconyuge' => 'nullable|mimes:pdf',
                'ciconyuge' => 'nullable|mimes:pdf',
                'cnacjihos' => 'nullable|mimes:pdf',
                'cihijos' => 'nullable|mimes:pdf',
                'denfaccidente' => 'nullable|mimes:pdf',
                'crodomicilio' => 'nullable|mimes:pdf',
                'contrato' => 'nullable|mimes:pdf',
                'ctrabajo' => 'nullable|mimes:pdf',
                'boletapago' => 'nullable|mimes:pdf',
                'egestora' => 'nullable|mimes:pdf',
                'actdatos' => 'nullable|mimes:pdf',
                'resolinvhijos' => 'nullable|mimes:pdf',
                'cunionlibre' => 'nullable|mimes:pdf',
                'cnacimientounionlibre' => 'nullable|mimes:pdf',
                'ciunionlibre' => 'nullable|mimes:pdf',
                'cdivorcio' => 'nullable|mimes:pdf',
                'cdefuncion' => 'nullable|mimes:pdf',
                'dictamencalentenc' => 'nullable|mimes:pdf',
                'infomedicasalud' => 'nullable|mimes:pdf',
                'anteriordictamen' => 'nullable|mimes:pdf',
                'poderciapoderado' => 'nullable|mimes:pdf',
            ]);

            $camposArchivos = [
                'poder', 'avcci', 'cnacasegurado', 'ciasegurado', 'cmatrimonio', 
                'cnacconyuge', 'ciconyuge', 'cnacjihos', 'cihijos', 'denfaccidente', 
                'crodomicilio', 'contrato', 'ctrabajo', 'boletapago', 'egestora', 'actdatos', 'resolinvhijos'
                , 'cunionlibre', 'cnacimientounionlibre', 'ciunionlibre', 'cdivorcio', 'cdefuncion'
                , 'dictamencalentenc', 'infomedicasalud', 'anteriordictamen', 'poderciapoderado'
            ];

            foreach ($camposArchivos as $campo) {
                $this->manejarArchivo($request, $campo, $requisito, $cliente->id);
            }

            if ($request->filled('numeropoder')) {
                $requisito->update(['numeropoder' => $request->input('numeropoder')]);
            }
    
            return redirect()->route('admin.asociados.subirdocrequisitossegsolicitud', $cliente)
                             ->with('info', 'El documento se subió con éxito');
        }
    public function generarPDFconsentimiento(Cliente $cliente, Request $request)
        {
            $nombres = $request->input('nombres');
            $apellidoPaterno = $request->input('apepaterno');
            $apellidoMaterno = $request->input('apematerno');
            $ci = $request->input('ci');
            $fechahoy = date('Y-m-d'); 
            $clienteitaId = $request->input('clienteitaid');
            $sucursalCliente = $request->input('sucursal');

            $nombreArchivo = "Consentimiento_Informado_Inicial {$nombres} {$apellidoPaterno} {$apellidoMaterno}.pdf";

            $nombreCompleto = "{$nombres} {$apellidoPaterno} {$apellidoMaterno}";

            $usuarioId = auth()->id();
            $usuarioNombre = auth()->user()->name;

            $documentacion = Estadocotizacionsubcliente::create([
                'clienteitaid' => $clienteitaId,
                'clienteitanombre' => $nombreCompleto,
                'detalle' => 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS',
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioNombre,
                'document' => null, 
            ]);
        
            
            // Buscar el proveedor, precio y precio de compra en BateriaProveedor
            $bateriaProveedor = BateriaProveedor::where('sucursal', $sucursalCliente)
                ->where('accion', 'MEDICINA LABORAL')
                ->first();

            if ($bateriaProveedor) {
            $programacion = BateriaSubCliente::create([
                'clienteitaid' => $clienteitaId,
                'clienteitanombre' => $nombreCompleto,
                'tipoarea' => 'ESPECIALIDAD',
                'areanombre' => 'MEDICINA LABORAL',
                'accionnombre' => 'MEDICINA LABORAL',
                'precio' => $bateriaProveedor->precio,
                'informe' => 'NO TIENE INFORME',
                'preciocompra' => $bateriaProveedor->preciocompra,
                'proveedorasignado' => $bateriaProveedor->proveedor, 
                'accionid' => $bateriaProveedor->id, 
                'servicio' => $bateriaProveedor->servicio, 
                'fechabateria' => now(),
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioNombre,
            ]);
            } else {

            }

            $data = [
                'nombres' => $nombres,
                'apellidoPaterno' => $apellidoPaterno,
                'apellidoMaterno' => $apellidoMaterno,
                'ci' => $ci,
                'fechahoy' => $fechahoy,
            ];

            $pdf = PDF::loadView('admin.asociados.pdfconsentimientoclienteita', $data);

            return $pdf->download($nombreArchivo);
        }
    public function generarsoloPDFconsentimiento(Request $request)
        {
            // Obtener los datos del cliente desde el request
            $nombres = $request->input('nombres');
            $apellidoPaterno = $request->input('apepaterno');
            $apellidoMaterno = $request->input('apematerno');
            $ci = $request->input('ci');
            $fechahoy = date('Y-m-d'); 
            $clienteitaId = $request->input('clienteitaid');

            // Generar el nombre del archivo PDF
            $nombreArchivo = "Consentimiento_Informado_Inicial {$nombres} {$apellidoPaterno} {$apellidoMaterno}.pdf";

            // Crear el nombre completo del cliente
            $nombreCompleto = "{$nombres} {$apellidoPaterno} {$apellidoMaterno}";

            // Obtener el ID del usuario autenticado
            $usuarioId = auth()->id();
            $usuarioNombre = auth()->user()->name;

            // Guardar el registro en DocumentacionSubcliente
            $documentacion = Estadocotizacionsubcliente::create([
                'clienteitaid' => $clienteitaId,
                'clienteitanombre' => $nombreCompleto,
                'detalle' => 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS',
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioNombre,
                'document' => null, // Inicialmente sin documento
            ]);


            // Pasar los datos a la vista para generar el PDF
            $data = [
                'nombres' => $nombres,
                'apellidoPaterno' => $apellidoPaterno,
                'apellidoMaterno' => $apellidoMaterno,
                'ci' => $ci,
                'fechahoy' => $fechahoy,
            ];

            // Cargar la vista para el PDF
            $pdf = PDF::loadView('admin.asociados.pdfconsentimientoclienteita', $data);
            
            // Retornar el PDF con el nombre generado
            return $pdf->download($nombreArchivo);
        }
    public function aprobariniciarcrearbateria(Request $request, Cliente $cliente)
        {
            // Obtener los datos del cliente desde el request
            $nombres = $request->input('nombres');
            $apellidoPaterno = $request->input('apepaterno');
            $apellidoMaterno = $request->input('apematerno');
            $clienteitaId = $request->input('clienteitaid');

            // Crear el nombre completo del cliente
            $nombreCompleto = "{$nombres} {$apellidoPaterno} {$apellidoMaterno}";

            // Obtener el ID del usuario autenticado
            $usuarioId = auth()->id();
            $usuarioNombre = auth()->user()->name;

            // Guardar el registro en DocumentacionSubcliente
            $documentacion = Estadocotizacionsubcliente::create([
                'clienteitaid' => $clienteitaId,
                'clienteitanombre' => $nombreCompleto,
                'detalle' => 'APROBADO PARA INICIAR A CREAR BATERIA',
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioNombre,
                'document' => null, // Inicialmente sin documento
            ]);

            return redirect()->back()->with('info', 'Aprobación exitosa.');
        }
    public function generarPDFguardarconsentimiento(Request $request)
        {
            $request->validate(['pdf_file' => 'required|mimes:pdf|max:2048']); // Validar archivo

            // Obtener el clienteitaid y la acción
            $clienteitaId = $request->input('clienteitaid');
            $detalle = $request->input('detalle');

            // Buscar el registro existente
            $documentacion = Estadocotizacionsubcliente::where('clienteitaid', $clienteitaId)
                ->where('detalle', $detalle)
                ->first();

            if ($documentacion) {
                // Guardar el archivo
                $file = $request->file('pdf_file');
                $carpetaCliente = public_path("/cotizacionesaprobadasita/{$clienteitaId}");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }
                $archivo_name = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name);

                // Actualizar el campo document en el registro
                $documentacion->document = $archivo_name;
                $documentacion->save();

                return redirect()->back()->with('info', 'PDF guardado exitosamente.');
            }

            return redirect()->back()->with('error', 'Registro no encontrado.');
        }
    public function generarPDFconsentimientoinformado(Request $request)
        {
            // Obtener los datos del cliente desde el request
            $nombres = $request->input('nombres');
            $apellidoPaterno = $request->input('apepaterno');
            $apellidoMaterno = $request->input('apematerno');
            $clienteitaId = $request->input('clienteitaid');
            $ci = $request->input('ci');
            $fechahoy = date('Y-m-d'); 

            // Generar el nombre del archivo PDF
            $nombreArchivo = "Consentimiento_Informado_Evaluaciones_Estudios {$nombres} {$apellidoPaterno} {$apellidoMaterno}.pdf";

            // Crear el nombre completo del cliente
            $nombreCompleto = "{$nombres} {$apellidoPaterno} {$apellidoMaterno}";

            // Obtener el ID del usuario autenticado
            $usuarioId = auth()->id();
            $usuarioNombre = auth()->user()->name;

            /* $documentacion = DocumentacionSubcliente::create([
                'clienteitaid' => $clienteitaId,
                'clienteitanombre' => $nombreCompleto,
                'accion' => 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS',
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioNombre,
                'document' => null, // Inicialmente sin documento
            ]); */

            // Pasar los datos a la vista para generar el PDF
            $data = [
                'nombres' => $nombres,
                'apellidoPaterno' => $apellidoPaterno,
                'apellidoMaterno' => $apellidoMaterno,
                'ci' => $ci,
                'fechahoy' => $fechahoy,
            ];

            // Cargar la vista para el PDF
            $pdf = PDF::loadView('admin.asociados.pdfconsentimientoinformadoclienteita', $data);
            
            // Retornar el PDF con el nombre generado
            return $pdf->download($nombreArchivo);
        }  
//
//PROVEEDOR INFORME FINAL CLIENTE ITA
    public function guardarproveedorinformefinal(StoreProveedorInformefinalRequest $request, Cliente $cliente)
    {
        $request->validate([
            'fechabateria' => 'required|date',
            'celularproveedor' => 'required|string',
            'precio' => 'required',
            'preciocompra' => 'required',
            'tramite' => 'required',
        ]);

        $usuarioId = auth()->user()->id;
        $usuarioRegistro = auth()->user()->name;

        $proveedorAsignado = Proveedor::findOrFail($request->proveedorasignado)->proveedor;

        ProveedorInformefinal::create([
            'fechabateria' => $request->fechabateria,
            'proveedorasignado' => $proveedorAsignado,
            'celularproveedor' => $request->celularproveedor,
            'clienteitaid' => $cliente->id,
            'clienteitanombre' => $cliente->nombrecompleto,
            'usuarioid' => $usuarioId,
            'usuarioregistro' => $usuarioRegistro,
            'precio' => $request->precio,
            'preciocompra' => $request->preciocompra,
            'servicio' => $request->tramite,
        ]);

        // Crear el registro en BateriaSubCliente
        BateriaSubCliente::create([
            'fechabateria' => $request->fechabateria,
            'clienteitaid' => $cliente->id,
            'clienteitanombre' => $cliente->nombrecompleto,
            'usuarioid' => $usuarioId,
            'usuarioregistro' => $usuarioRegistro,
            'tipoarea' => 'INFORME FINAL',
            'informe' => 'NINGUNO',
            'areanombre' => 'INFORME FINAL',
            'accionnombre' => 'INFORME FINAL',
            'precio' => $request->precio,
            'preciocompra' => $request->preciocompra,
            'proveedorasignado' => $proveedorAsignado,
            'servicio' => 'INTERNO',
            'accionid' => 'IF',

        ]);

        return redirect()->route('admin.asociados.verclienteita', $cliente)->with('info', 'Proveedor asignado exitosamente.');
    }
//


//CLIENTES COMUNES
//CREAR Y EDITAR CLIENTE COMUN
    public function crearclientecomun(Asociado $asociado)
    {
        $suc = [
            'COCHABAMBA' => 'COCHABAMBA',
            'SANTA CRUZ' => 'SANTA CRUZ',
        ];
        $genero = [
            'MASCULINO' => 'MASCULINO',
            'FEMENINO' => 'FEMENINO',
        ];
        $estciv = [
            'SOLTER@' => 'SOLTER@',
            'CASAD@' => 'CASAD@',
            'UNIÓN LIBRE' => 'UNIÓN LIBRE',
            'DIVORCIAD@' => 'DIVORCIAD@',
            'VIUD@' => 'VIUD@',
        ];
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
        return view('admin.asociados.crearclientecomun', compact('suc', 'asociado', 'genero', 'departamentos', 'estciv'));
    }
    public function guardarclientecomun(StoreClienteComunRequest $request)
    {
        $id = $request->input('ciudad');
        $ciudad = Departamento::findOrFail($id);
        $ciudadNombre = $ciudad->departamento;

        $clienteData = $request->all();
        $clienteData['ciudad'] = $ciudadNombre;

        $clientecomun = ClienteComun::create($clienteData);

        return redirect()->route('admin.asociados.listadoclientecomun', 3)->with('info', 'El cliente se creó con exito');
    }
    public function listadoclientecomun(Request $request, Asociado $asociado)
    {
        $nombrecompleto = $request->get('buscarpor');

        $clientecomunes = ClienteComun::where('nombrecompleto','Like',"%$nombrecompleto%")->simplePaginate(1000);

        return view('admin.asociados.listadoclientecomun', compact('asociado', 'clientecomunes'));
    }
    public function buscarclientescomun(Request $request)
    {
        $busqueda = $request->get('buscarpor');
        $clientecomunes = ClienteComun::where(function ($query) use ($busqueda) {
            $query->where('nombrecompleto', 'like', "%$busqueda%")
                ->orWhere('sucursal', 'like', "%$busqueda%")
                ->orWhere('ci', 'like', "%$busqueda%");
        })->simplePaginate(1000);
        return view('admin.asociados.listadoclientecomun', compact('clientecomunes'));
    }
    public function verclientecomun(ClienteComun $clientecomun)
    {
        $nombreusuario = auth()->user()->name; 
        $proveedores = Proveedor::where('id', 3)->get(['id', 'proveedor', 'celular']);
        $tieneBateria = Bateriasubcliente::where('clientecomunid', $clientecomun->id)->exists();
        $tieneCotizacionaprobada = Estadocotizacionsubcliente::where('clientecomunid', $clientecomun->id)->exists();
        $tieneProgramacion = Programacionsubcliente::where('clientecomunid', $clientecomun->id)->exists();
        $tieneProgramacionatentido = Estadoprogramacionsubcliente::where('clientecomunid', $clientecomun->id)->exists();
        $IDCliente = $clientecomun->id;
        $sucursalCliente = $clientecomun->sucursal;

        $accionesCliente = BateriaSubCliente::where('clientecomunid', $IDCliente)->pluck('accionnombre')->toArray();

        $fechasbateriasSubCliente = BateriaSubCliente::where('clientecomunid', $IDCliente)
            ->distinct()
            ->pluck('fechabateria');


        $fechasBateriaPorAccion = BateriaSubCliente::whereIn('accionnombre', $accionesCliente)
            ->where('clientecomunid', $IDCliente)
            ->distinct()
            ->pluck('fechabateria', 'accionnombre');

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accion => $fecha) {
            $accionesPorFecha[$fecha][] = $accion;
        }

        return view('admin.asociados.verclientecomun', compact('nombreusuario','tieneProgramacion','tieneProgramacionatentido','tieneCotizacionaprobada','tieneBateria','accionesPorFecha','fechasBateriaPorAccion','proveedores', 'clientecomun'));
    }
    public function editarclientecomun(ClienteComun $clientecomun)
    {
        $suc = [
            'COCHABAMBA' => 'COCHABAMBA',
            'SANTA CRUZ' => 'SANTA CRUZ',
        ];
        $genero = [
            'MASCULINO' => 'MASCULINO',
            'FEMENINO' => 'FEMENINO',
        ];
        $estciv = [
            'SOLTER@' => 'SOLTER@',
            'CASAD@' => 'CASAD@',
            'UNIÓN LIBRE' => 'UNIÓN LIBRE',
            'DIVORCIAD@' => 'DIVORCIAD@',
            'VIUD@' => 'VIUD@',
        ];
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'departamento');
        
        return view('admin.asociados.editarclientecomun', compact('clientecomun', 'suc', 'genero', 'departamentos', 'estciv'));
    }
    public function actualizarclientecomun(UpdateClienteComunRequest $request, ClienteComun $clientecomun)
    {
        $clientecomunData = $request->validated();
        $clientecomun->update($clientecomunData);

        return redirect()->route('admin.asociados.verclientecomun', $clientecomun)->with('info', 'El cliente se actualizó con éxito');
    }
//
//CREAR BATERIA CLIENTE COMUN
    public function crearbateriaclientecomun(ClienteComun $clientecomun)
    {
        $sucursalCliente = $clientecomun->sucursal;
        $rolusuario = auth()->user()->getRoleNames()->first(); 
        $areas = Area::orderBy('nombrearea', 'asc')
                    ->where('idtipoarea', 2)
                    ->pluck('nombrearea', 'id');

        $accionesPorArea = [];
        foreach ($areas as $id => $nombreArea) {
            $accionesPorArea[$id] = Bateriaproveedor::where('areasid', $id)
                ->where('sucursal', $sucursalCliente)
                ->where('estado', 'ACTIVO')
                ->where('asociado', 'CLIENTES COMUNES')
                ->orderBy('accion', 'asc')
                ->get(['id', 'accion', 'proveedor', 'precio']);
        }

        $areas2 = Bateriaproveedor::orderBy('area', 'asc')
            ->where('tipoid', 1)
            ->where('estado', 'ACTIVO')
            ->where('sucursal', $sucursalCliente)
            ->where('asociado', 'CLIENTES COMUNES')
            ->get(['area', 'id', 'proveedor', 'precio']);

        $estadoproveedor = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
        $id = $clientecomun->nombrecompleto ? ClienteComun::where('nombrecompleto', $clientecomun->nombrecompleto)->value('id') : null;

        $nombreCliente = $clientecomun->nombrecompleto; 
        $idCliente = $clientecomun->id; 

        $accionesCliente = BateriaSubCliente::where('clientecomunid', $idCliente)
            ->pluck('accionid')
            ->toArray();

        $fechasbateriasSubCliente = BateriaSubCliente::where('clientecomunid', $idCliente)
            ->distinct()
            ->pluck('fechabateria');

        $fechasBateriaPorAccion = BateriaSubCliente::whereIn('accionid', $accionesCliente)
            ->where('clientecomunid', $idCliente)
            ->whereIn('fechabateria', $fechasbateriasSubCliente)
            ->distinct()
            ->pluck('fechabateria', 'accionid');

        $accionesNombres = BateriaSubCliente::whereIn('accionid', $accionesCliente)
            ->pluck('accionnombre', 'accionid')
            ->toArray();

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accionid => $fecha) {
            $acciones = Bateriasubcliente::where('accionid', $accionid)
                ->where('clientecomunid', $idCliente)
                ->get(['id', 'precio', 'informe', 'proveedorasignado', 'fechabateria']);

            foreach ($acciones as $accion) {
                if ($accion->id && $accion->fechabateria) {
                    $accionesPorFecha[$accion->fechabateria][] = [
                        'id' => $accion->id,
                        'accion' => $accionesNombres[$accionid] ?? 'Desconocida',
                        'proveedor' => $accion->proveedorasignado,
                        'precio' => $accion->precio,
                        'informe' => $accion->informe,
                    ];
                }
            }
        }
 
        return view('admin.asociados.crearbateriaclientecomun', compact('accionesPorFecha','fechasBateriaPorAccion','departamentos','estadoproveedor','areas','accionesPorArea','clientecomun','id','accionesCliente','areas2','rolusuario'));
    }
    public function guardarbateriaclientecomun(StoreBateriasubclienteRequest $request)
    {
        $clienteID = $request->input('clientecomunid');
        $clientecomun = ClienteComun::findOrFail($clienteID);
        $accionesSeleccionadas = $request->input('acciones');
        $tipoArea = $request->input('tipoarea');
        $informe = $request->input('informe');
        $sucursalCliente = $clientecomun->sucursal;
        $fechaActual = now()->toDateString();
        $antecedentes = $request->input('antecedentes');
        $fechainforme = $request->input('fechainforme');
        $horaActual = now()->format('H:i:s');
        $usuarioID = auth()->user()->id;
        $usuarioNombre = auth()->user()->name;

        if ($tipoArea === 'Estudios') {
            $areasSeleccionadas = $request->input('areanombre');
            if (!is_array($areasSeleccionadas)) {
                $areasSeleccionadas = [$areasSeleccionadas];
            }
            foreach ($areasSeleccionadas as $areaId) {
                $area = Area::findOrFail($areaId);
                $areaNombre = $area->nombrearea;
                foreach ($accionesSeleccionadas as $accionId) {
                    $areaAccion = Bateriaproveedor::where('id', $accionId)
                                                ->where('areasid', $areaId)
                                                ->where('sucursal', $sucursalCliente)
                                                ->first();
                    if ($areaAccion) {
                        $accionNombre = $areaAccion->accion;
                        $precioAccion = $areaAccion->precio;
                        $preciocompraAccion = $areaAccion->preciocompra;
                        $proveedorAsignado = $areaAccion->proveedor;
                        $servicio = $areaAccion->servicio;
                    } else {
                        $accionNombre = 'DATO NO ENCONTRADO';
                        $precioAccion = 0;
                        $preciocompraAccion = 0;
                        $proveedorAsignado = 'DATO NO ENCONTRADO';
                    }
                    $fechaSeleccionada = $request->input('fechabateria');
                    $clienteitaData = $request->except(['acciones', '_token']);
                    
                    $clienteitaData['clientecomunid'] = $clienteID;
                    $clienteitaData['areanombre'] = $areaNombre;
                    $clienteitaData['clientecomunnombre'] = $clientecomun->nombrecompleto;
                    $clienteitaData['tipoarea'] = 'ESTUDIO';
                    if ($informe === 'SI TIENE INFORME') {
                        $clienteitaData['accionid'] = $accionId . 'PA';
                        $clienteitaData['accionnombre'] = $accionNombre . ' - PA';
                        $clienteitaData['precio'] = '0';
                        $clienteitaData['preciocompra'] = '0';
                        $clienteitaData['proveedorasignado'] = 'PROVEEDOR AJENO';
                        $clienteitaData['servicio'] = 'AJENO';
                    } else {
                        $clienteitaData['accionid'] = $accionId;
                        $clienteitaData['accionnombre'] = $accionNombre;
                        $clienteitaData['precio'] = $precioAccion;
                        $clienteitaData['preciocompra'] = $preciocompraAccion;
                        $clienteitaData['proveedorasignado'] = $proveedorAsignado;
                        $clienteitaData['servicio'] = $servicio;
                    }
                    $clienteitaData['fechabateria'] = $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada;
                    $clienteitaData['informe'] = $informe;
                    $clienteitaData['usuarioid'] = $usuarioID;
                    $clienteitaData['usuarioregistro'] = $usuarioNombre;
                    $clienteitaData['fechainforme'] = $fechainforme;
                    Bateriasubcliente::create($clienteitaData);

                    if ($informe === 'SI TIENE INFORME') {
                        Programacionsubcliente::create([
                            'proveedornombre' => 'PROVEEDOR AJENO',
                            'clientecomunid' => $clienteID,
                            'clientecomunnombre' => $clientecomun->nombrecompleto,
                            'fechaasignada' => $fechaActual,
                            'fechabateria' => $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada,
                            'horaasignada' => $horaActual,
                            'horadesde' => $horaActual,
                            'horahasta' => $horaActual,
                            'areanombre' => $areaNombre,
                            'accionnombre' => $accionNombre . ' - PA',
                            'precio' => '0',
                            'usuarioid' => $usuarioID,
                            'usuarioregistro' => $usuarioNombre,
                        ]);
                        Estadoprogramacionsubcliente::create([
                            'clientecomunid' => $clienteID,
                            'clientecomunnombre' => $clientecomun->nombrecompleto,
                            'fechaatencionprogramacion' => $fechaActual,
                            'fechabateria' => $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada,
                            'accionnombre' => $accionNombre . ' - PA',
                            'usuarioid' => $usuarioID,
                            'usuarioregistro' => $usuarioNombre,
                        ]);
                    }
                }
            }
        } elseif ($tipoArea === 'Especialidades') {
            $accionesSeleccionadas = $request->input('accionnombre');
            if (!is_array($accionesSeleccionadas)) {
                $accionesSeleccionadas = [$accionesSeleccionadas];
            }
            foreach ($accionesSeleccionadas as $accionId) {
                $areaAccion = Bateriaproveedor::where('id', $accionId)
                                            ->where('sucursal', $sucursalCliente)
                                            ->first();
                if ($areaAccion) {
                    $accionNombre = $areaAccion->accion;
                    $precioAccion = $areaAccion->precio;
                    $preciocompraAccion = $areaAccion->preciocompra;
                    $proveedorAsignado = $areaAccion->proveedor;
                    $servicio = $areaAccion->servicio;
                } else {
                    $accionNombre = 'DATO NO ENCONTRADO';
                    $precioAccion = 0;
                    $preciocompraAccion = 0;
                    $proveedorAsignado = 'DATO NO ENCONTRADO';
                    $servicio = 'DATO NO ENCONTRADO';
                }
                $fechaSeleccionada = $request->input('fechabateria');
                $clienteitaData = $request->except(['accionnombre', '_token']);
                
                $clienteitaData['antecedentes'] = $antecedentes;
                $clienteitaData['clientecomunid'] = $clienteID;
                $clienteitaData['areanombre'] = $accionNombre;
                $clienteitaData['clientecomunnombre'] = $clientecomun->nombrecompleto;
                $clienteitaData['tipoarea'] = 'ESPECIALIDAD';
                if ($informe === 'SI TIENE INFORME') {
                    $clienteitaData['accionid'] = $accionId . 'PA';
                    $clienteitaData['accionnombre'] = $accionNombre . ' - PA';
                    $clienteitaData['precio'] = '0';
                    $clienteitaData['preciocompra'] = '0';
                    $clienteitaData['proveedorasignado'] = 'PROVEEDOR AJENO';
                    $clienteitaData['servicio'] = 'AJENO';
                } else{
                    $clienteitaData['accionid'] = $accionId;
                    $clienteitaData['accionnombre'] = $accionNombre;
                    $clienteitaData['precio'] = $precioAccion;
                    $clienteitaData['preciocompra'] = $preciocompraAccion;
                    $clienteitaData['proveedorasignado'] = $proveedorAsignado;
                    $clienteitaData['servicio'] = $servicio;
                }
                $clienteitaData['fechabateria'] = $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada;
                $clienteitaData['informe'] = $informe;
                $clienteitaData['usuarioid'] = $usuarioID;
                $clienteitaData['usuarioregistro'] = $usuarioNombre;
                $clienteitaData['fechainforme'] = $fechainforme;
                Bateriasubcliente::create($clienteitaData);

                if ($informe === 'SI TIENE INFORME') {
                    Programacionsubcliente::create([
                        'proveedornombre' => 'PROVEEDOR AJENO',
                        'clientecomunid' => $clienteID,
                        'clientecomunnombre' => $clientecomun->nombrecompleto,
                        'fechaasignada' => $fechaActual,
                        'fechabateria' => $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada,
                        'horaasignada' => $horaActual,
                        'horadesde' => $horaActual,
                        'horahasta' => $horaActual,
                        'areanombre' => $accionNombre,
                        'accionnombre' => $accionNombre . ' - PA',
                        'precio' => '0',
                        'usuarioid' => $usuarioID,
                        'usuarioregistro' => $usuarioNombre,
                    ]);
                    Estadoprogramacionsubcliente::create([
                        'clientecomunid' => $clienteID,
                        'clientecomunnombre' => $clientecomun->nombrecompleto,
                        'fechaatencionprogramacion' => $fechaActual,
                        'fechabateria' => $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada,
                        'accionnombre' => $accionNombre . ' - PA',
                        'usuarioid' => $usuarioID,
                        'usuarioregistro' => $usuarioNombre,
                    ]);
                }
            }
        }
        return redirect()->route('admin.asociados.crearbateriaclientecomun', ['clientecomun' => $clientecomun])->with('info', 'La batería se creó con éxito');
    }
//
//COTIZACION DE PROGRAMACION DE CLIENTE COMUN
    public function aprobacioncotizacionclientecomun(ClienteComun $clientecomun, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarporfecha');
        $servicioSeleccionado = $request->get('buscarporservicio');
        $areasSeleccionadas = $request->get('buscarporarea', []);

        if (!is_array($areasSeleccionadas)) {
            $areasSeleccionadas = [];
        }
               
        $fechas = Bateriasubcliente::where('clientecomunid', $clientecomun->id)
                                    ->pluck('fechabateria')
                                    ->unique();

        $areasPorFecha = BateriaSubCliente::where('clientecomunid', $clientecomun->id)
                                    ->get()
                                    ->groupBy('fechabateria')
                                    ->map(function ($items) {
                                        return $items->pluck('areanombre')->unique()->values();
                                    });
        
    
        $bateriasubclientes = collect();
        $total = 0;
    
        $query = BateriaSubCliente::where('clientecomunid', $clientecomun->id);
        if ($fechaSeleccionada) {
            $query->where('fechabateria', $fechaSeleccionada);
        }
        if ($servicioSeleccionado) {
            $query->where('servicio', $servicioSeleccionado);
        }
        if (!empty($areasSeleccionadas)) {
            $query->whereIn('areanombre', $areasSeleccionadas);
        }
    
        if ($fechaSeleccionada || !empty($areasSeleccionadas)) {
            $bateriasubclientes = $query->simplePaginate(1000);
    
            $total = $bateriasubclientes->sum(function ($bateriasubcliente) {
                return str_replace(',', '.', $bateriasubcliente->precio);
            });
            $total = number_format($total, 2, '.', '');
        }
        $id = $clientecomun->id;
    
        return view('admin.asociados.aprobacioncotizacionclientecomun', compact('fechas','servicioSeleccionado','bateriasubclientes', 'id', 'clientecomun', 'total', 'fechaSeleccionada', 'areasPorFecha', 'areasSeleccionadas'));
    }
    public function buscarbateriaclientecomun(ClienteComun $clientecomun, Request $request)
    {
        return $this->aprobacioncotizacionclientecomun($clientecomun, $request);
    }
    public function generarpdfcotizacionclientecomun(ClienteComun $clientecomun, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarporfecha');
        $servicioSeleccionado = $request->get('buscarporservicio');
        $areasSeleccionadas = $request->get('buscarporarea', []);
        $total = $request->get('total');
    
        $areasSeleccionadas = is_array($areasSeleccionadas) ? $areasSeleccionadas : explode(',', $areasSeleccionadas);
    
        $query = BateriaSubCliente::where('clientecomunid', $clientecomun->id);
    
        if ($fechaSeleccionada) {
            $query->where('fechabateria', $fechaSeleccionada);
        }
        if ($servicioSeleccionado) {
            $query->where('servicio', $servicioSeleccionado);
        }
        if (!empty($areasSeleccionadas)) {
            $query->whereIn('areanombre', $areasSeleccionadas);
        }
    
        $bateriasubclientes = $query->get();
    
        if (!$total) {
            $total = $bateriasubclientes->sum(function ($bateriasubcliente) {
                return str_replace(',', '.', $bateriasubcliente->precio);
            });
            $total = number_format($total, 2, '.', '');
        }
    
        // Determina qué vista usar basado en el valor de buscarporservicio
        $vistaPdf = 'admin.asociados.pdfcotizacionclientecomun';
    
        // Determina el nombre del archivo PDF basado en el valor de buscarporservicio
        $pdfName = $servicioSeleccionado === 'AJENO'
            ? 'Informes_a_presentar_' . $clientecomun->nombrecompleto
            : 'Cotización_' . $clientecomun->nombrecompleto;
    
        $pdfName .= '.pdf';
    
        // Genera el PDF
        $pdf = Pdf::loadView($vistaPdf, [
            'clientecomun' => $clientecomun,
            'bateriasubclientes' => $bateriasubclientes,
            'total' => $total
        ]);
    
        return $pdf->download($pdfName);
    }
    public function aprobarcotizacionprogramacionclientecomun(ClienteComun $clientecomun) 
    {
        $nombreCliente = $clientecomun->nombrecompleto;

        $id = $clientecomun->nombrecompleto ? ClienteComun::where('nombrecompleto', $clientecomun->nombrecompleto)->value('id') : null;

        $fechasRegistradas = EstadoCotizacionSubCliente::where('clientecomunid', $clientecomun->id)
                                        ->pluck('fechabateria')
                                        ->unique();

        $fechasDisponibles = BateriaSubCliente::where('clientecomunid', $clientecomun->id)
                                        ->pluck('fechabateria')
                                        ->unique();

        $fechasConFactura = EstadoCotizacionSubCliente::where('clientecomunid', $clientecomun->id)
                                        ->whereNotNull('nrofactura')
                                        ->pluck('fechabateria')
                                        ->unique();

        $fechas = $fechasDisponibles->filter(function ($fecha) use ($fechasRegistradas) {
            return !$fechasRegistradas->contains($fecha);
        });

        $fechasregis = $fechasDisponibles->filter(function ($fecha) use ($fechasConFactura) {
            return !$fechasConFactura->contains($fecha);
        });

        $documentosPorFecha = EstadoCotizacionSubCliente::where('clientecomunid', $clientecomun->id)
            ->get(['fechabateria', 'document', 'documentconsinfo'])
            ->groupBy('fechabateria');
        $fecha = '';
        return view('admin.asociados.aprobarcotizacionprogramacionclientecomun', compact('fecha','documentosPorFecha','fechasregis','clientecomun', 'id', 'fechas', 'fechasRegistradas','fechasDisponibles'));
    } 
    public function actualizarPdfclientecomun(Request $request) 
    {
        // Validar el formulario
        $request->validate([
            'fechabateria' => 'required|date',
            'archivo' => 'required|file|mimes:pdf|max:20480', // max:20MB
        ]);

        // Encuentra el estado de cotización existente
        $estadoCotizacion = EstadoCotizacionSubCliente::where('clientecomunid', $request->clientecomunid)
            ->where('fechabateria', $request->fechabateria)
            ->first();

        // Verifica si el registro existe
        if ($estadoCotizacion) {
            $carpetaCliente = public_path("/cotizacionesaprobadascomun/{$request->clientecomunid}");
            
            // Elimina el archivo PDF existente si es necesario
            if ($estadoCotizacion->document) {
                $archivoAntiguo = $carpetaCliente . '/' . $estadoCotizacion->document;
                if (file_exists($archivoAntiguo)) {
                    unlink($archivoAntiguo);
                }
            }
            
            // Guarda el nuevo archivo PDF
            $archivo_name = null;
            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $archivo_name = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name);
            }
            
            // Actualiza el registro existente
            $estadoCotizacion->update([
                'document' => $archivo_name,
                'usuarioid' => auth()->user()->id,
                'usuarioregistro' => auth()->user()->name
            ]);
            
            return redirect()->route('admin.asociados.aprobarcotizacionprogramacionclienteita', $request->clientecomunid)
                ->with('info', 'El documento se actualizó con éxito');
        } else {
            return redirect()->route('admin.asociados.aprobarcotizacionprogramacionclienteita', $request->clientecomunid)
                ->with('error', 'No se encontró el registro para actualizar');
        }
    }
    public function guardaraprobacioncotizacionclientecomun(StoreEstadocotizacionsubclienteRequest $request, ClienteComun $clientecomun)
    {
        $archivo_name = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $carpetaCliente = public_path("/cotizacionesaprobadascomun/{$clientecomun->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }
        $archivo_name2 = null;
        if ($request->hasFile('archivo2')) {
            $file = $request->file('archivo2');
            $carpetaCliente = public_path("/cotizacionesaprobadascomun/{$clientecomun->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name2);
        }
        $documentacioncotizacioncliente = Estadocotizacionsubcliente::create([
            'document' => $archivo_name,
            'documentconsinfo' => $archivo_name2,
            'usuarioid' => auth()->user()->id,
            'usuarioregistro' => auth()->user()->name,
            'clientecomunid' => $request->clientecomunid,
            'clientecomunnombre' => $request->clientecomunnombre,
            'fechabateria' => $request->input('fechabateria'),
        ]);
    
        return redirect()->route('admin.asociados.aprobarcotizacionprogramacionclientecomun', $request->clientecomun)->with('info', 'El documento se subió con éxito');
    }
//
//CREAR PROGRAMACION Y REPROGRAMACION DE CLIENTE COMUN
    public function crearprogramacionclientecomun(ClienteComun $clientecomun)
    {
        $nombreCliente = $clientecomun->nombrecompleto;
        $idCliente = $clientecomun->id;
        $clientecomunid = $clientecomun->id;
        $sucursalCliente = $clientecomun->sucursal;
        $rolusuario = auth()->user()->getRoleNames()->first(); 
        $esProveedor = ($rolusuario === 'PROVEEDOR');
        $id = $clientecomun->nombrecompleto ? ClienteComun::where('nombrecompleto', $clientecomun->nombrecompleto)->value('id') : null;

        $accionesCliente = BateriaSubCliente::where('clientecomunnombre', $nombreCliente)
            ->whereIn('accionnombre', function ($query) use ($sucursalCliente) {
                $query->select('accionnombre')->from('clientescomunes')->where('sucursal', $sucursalCliente);
            })
            ->pluck('accionnombre')
            ->unique();

        $proveedoresAsignados = BateriaSubCliente::where('clientecomunnombre', $nombreCliente)
            ->whereIn('accionnombre', $accionesCliente)
            ->pluck('proveedorasignado', 'accionnombre')
            ->toArray();

        $fechasEnEstadoCotizacionSubCliente = EstadoCotizacionSubCliente::where('clientecomunnombre', $nombreCliente)  
            ->distinct()
            ->pluck('fechabateria');
    
        $fechasBateriaPorAccion = BateriaSubCliente::where('clientecomunnombre', $nombreCliente)
            ->where(function ($query) use ($fechasEnEstadoCotizacionSubCliente) {
                $query->whereIn('fechabateria', $fechasEnEstadoCotizacionSubCliente)
                    ->orWhere('accionnombre', 'MEDICINA LABORAL');
            })
            ->whereIn('accionnombre', $accionesCliente)
            ->select('accionnombre', 'fechabateria')
            ->get();
        
        $accionesRegistradas = Programacionsubcliente::where('clientecomunid', $idCliente)
            ->pluck('accionnombre', 'fechabateria')
            ->toArray();

        foreach ($accionesRegistradas as $fecha => $accion) {
        if (!isset($accionesRegistradas[$fecha])) {
            $accionesRegistradas[$fecha] = [];
            }

            if (!is_array($accionesRegistradas[$fecha])) {
                $accionesRegistradas[$fecha] = [$accion];
            } else {
                $accionesRegistradas[$fecha][] = $accion;
            }
        }

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $item) {
            $accion = $item->accionnombre;
            $fecha = $item->fechabateria;

            $accionYaRegistrada = Programacionsubcliente::where('clientecomunid', $idCliente)
                ->where('fechabateria', $fecha)
                ->where('accionnombre', $accion)
                ->exists();
        
            if (!isset($accionesPorFecha[$fecha])) {
                $accionesPorFecha[$fecha] = [];
            }

            if (!$accionYaRegistrada) {
                $accionesPorFecha[$fecha][] = $accion;
            }
        }

        
        $proveedoresDetalles = [];
        foreach ($proveedoresAsignados as $accion => $nombreProveedor) {
            $proveedor = BateriaSubCliente::where('accionnombre', $accion)->where('clientecomunid', $idCliente)
                ->latest()
                ->first();

            if ($proveedor) {
                $proveedoresDetalles[$accion] = [
                    'proveedor' => $proveedor->proveedorasignado,
                    'horarioinicial' => $proveedor->horarioinicial,
                    'horariofinal' => $proveedor->horariofinal,
                    'fechabateria' => $proveedor->fechabateria,
                    'fechaasignada' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clientecomunnombre', $nombreCliente)
                        ->value('fechaasignada'),
                    'horadesde' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clientecomunnombre', $nombreCliente)
                        ->value('horadesde'),
                    'horahasta' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clientecomunnombre', $nombreCliente)
                        ->value('horahasta'),
                    'tiempoatencion' => $proveedor->tiempoatencion,
                    'accion' => $proveedor->accionnombre,
                    'area' => $proveedor->areanombre,
                    'precio' => $proveedor->precio,
                    'preciocompra' => $proveedor->preciocompra,
                    'programacionid' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clienteitanombre', $nombreCliente)
                        ->value('id'),
                ];
            }
        }

        $fechasBateria = BateriaSubCliente::where('clientecomunid', $clientecomunid)
            ->distinct()
            ->pluck('fechabateria');


        $accionesPorFechaBateria = [];
        foreach ($fechasBateria as $fecha) {
            $accionesBateria = BateriaSubCliente::where('fechabateria', $fecha)
                ->where('clientecomunid', $clientecomunid)
                ->pluck('accionnombre')
                ->toArray();

            $accionesPorFechaBateria[$fecha] = $accionesBateria;
        }

        $accionesDetallesPorFecha = [];
        foreach ($fechasBateria as $fecha) {
            $accionesProgramadas = ProgramacionSubCliente::where('fechabateria', $fecha)
                ->where('clientecomunid', $clientecomunid)
                ->get(['id', 'accionnombre','proveedornombre', 'fechaasignada', 'horadesde', 'horahasta', 'horahasta', 'precio']);

            foreach ($accionesProgramadas as $accion) {
                $accionesDetallesPorFecha[$fecha][$accion->accionnombre] = $accion;
            }
        }

        $previousUrl = url()->previous();
        if (session('previous_url') !== $previousUrl && $previousUrl !== url()->current()) {
            session(['previous_url' => $previousUrl]);
        }

        return view('admin.asociados.crearprogramacionclientecomun', compact('esProveedor','accionesDetallesPorFecha','accionesPorFechaBateria','fechasBateria','id','rolusuario', 'clientecomun', 'accionesPorFecha', 'proveedoresDetalles', 'accionesRegistradas', 'fechasBateriaPorAccion', 'accionesCliente'));
    }
    public function guardarprogramacionclientecomun(StoreProgramacionsubclienteRequest $request)
    {
        // Recoge las acciones seleccionadas
        $accionesSeleccionadas = $request->input('accionesSeleccionadas', []);
        $horaasignada = $request->input('horaasignada');
        $fechaasignada = $request->input('fechaasignada');
        $clientecomunid = $request->input('clientecomunid');
        $clientecomunnombre = $request->input('clientecomunnombre');
        $fechabateria = $request->input('fechabateria');
        $horadesde = $request->input('horadesde');
        $horahasta = $request->input('horahasta');

        foreach ($accionesSeleccionadas as $accion) {
            // Sanitiza el nombre de la acción
            $accionSanitizada = str_replace([' ', '.'], ['_', '-'], $accion);
            
            // Captura los datos específicos de cada acción
            $proveedornombre = $request->input("proveedor_$accionSanitizada");
            $areanombre = $request->input("areanombre_$accionSanitizada");
            $precio = $request->input("precio_$accionSanitizada");
            $preciocompra = $request->input("preciocompra_$accionSanitizada");

            // Verifica si ya existe la programación
            $existente = Programacionsubcliente::where('accionnombre', $accion)
                ->where('fechabateria', $fechabateria)
                ->where('clientecomunid', $clientecomunid)
                ->exists();

            // Solo crea un nuevo registro si no existe
            if (!$existente) {
                Programacionsubcliente::create([
                    'accionnombre' => $accion,
                    'horaasignada' => $horaasignada,
                    'fechaasignada' => $fechaasignada,
                    'proveedornombre' => $proveedornombre,
                    'clientecomunid' => $clientecomunid,
                    'clientecomunnombre' => $clientecomunnombre,
                    'horadesde' => $horadesde,
                    'horahasta' => $horahasta,
                    'fechabateria' => $fechabateria,
                    'areanombre' => $areanombre,
                    'precio' => $precio,
                    'preciocompra' => $preciocompra,
                    'usuarioid' => Auth::id(), // ID del usuario autenticado
                    'usuarioregistro' => Auth::user()->name, // Nombre del usuario autenticado
                ]);
            }
        }

        return redirect()->route('admin.asociados.crearprogramacionclientecomun', $request->clientecomun)->with('info', 'La programación del cliente se creó con éxito');
    }
    public function reprogramacionclientecomun(ClienteComun $clientecomun, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarpor');
        $fechas = ProgramacionSubCliente::where('clientecomunid', $clientecomun->id)
                                    ->pluck('fechabateria')
                                    ->unique();
        $programacionsubclientes = collect();
        
        $reprogramaciones = ProgramacionSubCliente::where('clientecomunid', $clientecomun->id)
        ->onlyTrashed()
        ->get();
        $total = 0;
        if ($fechaSeleccionada) {
            $programacionsubclientes = ProgramacionSubCliente::where('clientecomunid', $clientecomun->id)
                                                    ->where('fechabateria', $fechaSeleccionada)
                                                    ->simplePaginate(1000);
            $total = $programacionsubclientes->sum(function ($programacionsubcliente) {
                return str_replace(',', '.', $programacionsubcliente->precio);
            });
            $total = number_format($total, 2, '.', '');
        }


        $id = ClienteComun::where('nombrecompleto', $clientecomun->nombrecompleto)->value('id');
        return view('admin.asociados.reprogramacionclientecomun', compact('reprogramaciones','programacionsubclientes', 'id', 'clientecomun', 'fechas', 'total', 'fechaSeleccionada'));
    }
    public function buscarprogramacionclientecomun(ClienteComun $clientecomun, Request $request)
    {
        return $this->reprogramacionclientecomun($clientecomun, $request);
    }
    public function guardarreprogramacionclientecomun(Request $request, Programacionsubcliente $programacionsubcliente)
    {
        $request->validate([
            'motivoreprogramacion' => 'required|string|max:255',
            'usuarioactualizacion' => 'required|string',
        ]);
        $usuarioActualizacion = $request->input('usuarioactualizacion');
        $programacionsubcliente->motivoreprogramacion = $request->motivoreprogramacion;
        $programacionsubcliente->usuarioactualizacion = $usuarioActualizacion;
        $programacionsubcliente->save();

        $programacionsubcliente->delete();

        $clientecomun = ClienteComun::where('nombrecompleto', $programacionsubcliente->clientecomunnombre)->first();

        return redirect()->route('admin.asociados.reprogramacionclientecomun', $clientecomun)->with('eliminar', 'ok');
    }
    public function estadoprogramacionclientecomun(ClienteComun $clientecomun, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarpor');
        
        $fechas = Programacionsubcliente::where('clientecomunid', $clientecomun->id)
                                    ->pluck('fechabateria')
                                    ->unique();

        $accionesDisponibles = collect();
        
        if ($fechaSeleccionada) {
            $accionesDisponibles = ProgramacionSubCliente::where('clientecomunid', $clientecomun->id)
                                                    ->where('fechabateria', $fechaSeleccionada)
                                                    ->simplePaginate(1000);
        }
        $IDCliente = $clientecomun->id;
        $accionesCliente = BateriaSubCliente::where('clientecomunid', $IDCliente)->pluck('accionnombre')->toArray();
        $id = $clientecomun->id ? ClienteComun::where('id', $clientecomun->nombrecompleto)->value('id') : null;
        $nombreclienteita = $clientecomun->nombrecompleto ? ClienteComun::where('nombrecompleto', $clientecomun->nombrecompleto)->value('nombrecompleto') : null;

        $accionesPorArea = Programacionsubcliente::where('clientecomunid', $IDCliente)
            ->get(['accionnombre', 'proveedornombre','fechabateria','fechaasignada', 'horadesde', 'horahasta']);

        $estadoRegistrados = Estadoprogramacionsubcliente::where('clientecomunid', $IDCliente)
                ->get(['accionnombre', 'fechabateria']);

        $estadoMapeado = [];
            foreach ($estadoRegistrados as $estado) {
                $estadoMapeado[$estado->accionnombre][$estado->fechabateria] = true;
            }

        $accionesDisponibles = $accionesDisponibles ?? $accionesPorArea;
        
        $accionesRegistradas = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clientecomunid', $IDCliente)
            ->pluck('accionnombre')
            ->toArray();

        $fechasEnEstadoCotizacionSubCliente = EstadoCotizacionSubCliente::where('clientecomunid', $IDCliente)
            ->distinct()
            ->pluck('fechabateria');

        $fechasBateriaPorAccion = Programacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clientecomunid', $IDCliente)
            /* ->whereIn('fechabateria', $fechasEnEstadoCotizacionSubCliente) */
            ->distinct()
            ->pluck('fechabateria', 'accionnombre');

        $accionesPorFecha = Programacionsubcliente::where('clientecomunid', $IDCliente)
            ->where('fechabateria', $fechaSeleccionada)
            ->get(['accionnombre'])
            ->pluck('accionnombre')
            ->toArray();
        $accionesNoRegistradas = array_filter($accionesPorFecha, function ($accion) use ($estadoMapeado, $fechaSeleccionada) {
                return empty($estadoMapeado[$accion][$fechaSeleccionada]);
            });
            
        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accion => $fecha) {
        $accionesPorFecha[$fecha][] = $accion;
        }
        
        foreach ($accionesDisponibles as $accion) {
            $bateriaProveedor = BateriaProveedor::where('proveedor', $accion->proveedornombre)->first();
            $proveedor = Proveedor::where('proveedor', $accion->proveedornombre)->first();

            if ($bateriaProveedor && $bateriaProveedor->servicio === 'EXTERNO' && $proveedor) {
                $accion->direccion = $proveedor->direccion;
            } else {
                $accion->direccion = 'GOOD LIFE SRL';
            }
            if ($bateriaProveedor && $bateriaProveedor->servicio === 'EXTERNO' && $proveedor) {
                $accion->linkubicacion = $proveedor->linkubicacion;
            } else {
                $accion->linkubicacion = '';
            }
            if ($bateriaProveedor && $bateriaProveedor->servicio === 'INTERNO' && $proveedor) {
                if ($bateriaProveedor->sucursal === 'SANTA CRUZ') {
                    $accion->linkubicacion = 'https://maps.app.goo.gl/8Ye9G5fUDrLGjueNA';
                } elseif ($bateriaProveedor->sucursal === 'COCHABAMBA') {
                    $accion->linkubicacion = 'https://maps.app.goo.gl/aXPo8s2T3QB6NoH47';
                } else {
                    $accion->linkubicacion = '';
                }
            } else {
                $accion->linkubicacion = '';
            }
            
        }

        $id = Cliente::where('nombrecompleto', $clientecomun->nombrecompleto)->value('id');

        return view('admin.asociados.estadoprogramacionclientecomun', compact('accionesNoRegistradas','estadoMapeado','fechaSeleccionada', 'id','fechas','nombreclienteita','accionesDisponibles', 'clientecomun', 'id', 'accionesCliente', 'estadoRegistrados', 'fechasBateriaPorAccion', 'accionesPorFecha', 'accionesRegistradas'));
    }
    public function buscarprogramacionclientescomun(ClienteComun $clientecomun, Request $request)
    {
        return $this->estadoprogramacionclientecomun($clientecomun, $request);
    }
    public function guardarestadoprogramacionclientecomun(StoreEstadoprogramacionsubclienteRequest $request)
    {
        $accionesSeleccionadas = $request->input('accionesSeleccionadas', []);
        $fechaBateria = $request->input('fechabateria'); // Obtiene la fecha de batería del input oculto
    
        foreach ($accionesSeleccionadas as $accionNombre) {
            Estadoprogramacionsubcliente::create(
                $request->except('accionid') + [
                    'accionnombre' => $accionNombre,
                    'fechabateria' => $fechaBateria // Asegúrate de incluir la fecha aquí
                ]
            );
        }
    
        // Redirige a la vista con la fecha seleccionada
        return redirect()->route('admin.asociados.estadoprogramacionclientecomun', [
            'clientecomun' => $request->clientecomun,
            'buscarpor' => $fechaBateria // Incluye la fecha en la redirección
        ])->with('info', 'El estado se actualizó con éxito');
    }
//
//CREAR DOCUMENTACION DE CLIENTE COMUN
    public function creardocumentacionclientecomun(ClienteComun $clientecomun, Asociado $asociado)
    {
        $IDcliente = $clientecomun->id;

        $accionesCliente = Programacionsubcliente::where('clientecomunid', $IDcliente)
            ->pluck('accionnombre')
            ->unique();

        $accionesRegistradasPorFecha = Documentacionsubcliente::where('clientecomunid', $IDcliente)
            ->get(['accion', 'fechabateria'])
            ->groupBy('fechabateria');

        $accionesNoRegistradasPorFecha = Estadoprogramacionsubcliente::where('clientecomunid', $IDcliente)
            ->get(['accionnombre', 'fechabateria'])
            ->filter(function($accion) use ($accionesRegistradasPorFecha) {
                $fechabateria = $accion->fechabateria;
                $accionnombre = $accion->accionnombre;
                return !isset($accionesRegistradasPorFecha[$fechabateria]) || !in_array($accionnombre, $accionesRegistradasPorFecha[$fechabateria]->pluck('accion')->toArray());
            })
            ->groupBy('fechabateria');

        $accionesRegistradas = Documentacionsubcliente::whereIn('accion', $accionesCliente)
            ->where('clienteitaid', $IDcliente)
            ->pluck('accion')
            ->toArray();

        $id = $clientecomun->nombrecompleto ? ClienteComun::where('nombrecompleto', $clientecomun->nombrecompleto)->value('id') : null;

        $fechasBateriaPorAccion = Programacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clientecomunid', $IDcliente)
            ->get(['accionnombre', 'fechabateria', 'proveedornombre'])
            ->groupBy('fechabateria');
        
        $accionesEnEstado = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clientecomunid', $IDcliente)
            ->pluck('accionnombre')
            ->toArray();
        $documentosRegistrados = Documentacionsubcliente::whereIn('accion', $accionesCliente)
            ->where('clientecomunid', $IDcliente)
            ->pluck('accion')->toArray();

        $accionesPorFecha = [];

        foreach ($fechasBateriaPorAccion as $fecha => $acciones) {
            foreach ($acciones as $accion) {
                $accionesPorFecha[$fecha][] = $accion;
            }
        }
        

        $documentosRegistradosPorFecha = Documentacionsubcliente::where('clientecomunid', $IDcliente)
            ->get(['accion', 'fechabateria'])
            ->groupBy('fechabateria');

        $accionesPorFecha2 = Programacionsubcliente::where('clientecomunid', $IDcliente)
            ->get(['accionnombre', 'fechabateria'])
            ->groupBy('fechabateria');

        $accionesConEstadoPorFecha = [];
        foreach ($accionesPorFecha as $fecha => $acciones) {
            foreach ($acciones as $accion) {
                $registrado = isset($documentosRegistradosPorFecha[$fecha]) && 
                            in_array($accion->accionnombre, $documentosRegistradosPorFecha[$fecha]->pluck('accion')->toArray());

                $documento = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clientecomunid', $IDcliente)
                                                        ->value('document') : null;

                $image = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clientecomunid', $IDcliente)
                                                        ->value('image') : null;

                $image2 = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clientecomunid', $IDcliente)
                                                        ->value('image2') : null;
                $id = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clientecomunid', $IDcliente)
                                                        ->value('id') : null;

                $creacionregistro = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre) 
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clientecomunid', $IDcliente)
                                                        ->value('created_at') : null;
                if ($creacionregistro) {
                    $creacionregistro = \Carbon\Carbon::parse($creacionregistro);
                    $creacionregistroFormatted = $creacionregistro->format('Y-m-d') . ' - ' . $creacionregistro->format('H:i:s');
                } else {
                    $creacionregistroFormatted = null;
                }

                $proveedor = $accion->proveedornombre;

                $accionesConEstadoPorFecha[$fecha][] = [
                    'id' => $id,
                    'accionnombre' => $accion->accionnombre,
                    'proveedornombre' => $proveedor,
                    'registrado' => $registrado,
                    'document' => $documento,
                    'image' => $image,
                    'image2' => $image2,
                    'creacionregistro' => $creacionregistroFormatted
                ];
            }
        }

        return view('admin.asociados.creardocumentacionclientecomun', compact('accionesConEstadoPorFecha','accionesRegistradasPorFecha','accionesNoRegistradasPorFecha','asociado', 'accionesEnEstado','id', 'clientecomun', 'accionesPorFecha', 'accionesRegistradas', 'fechasBateriaPorAccion', 'accionesCliente', 'documentosRegistrados'));
    }
    public function guardardocumentacionclientecomun(StoreDocumentacionsubclienteRequest $request, ClienteComun $clientecomun)
    {
        $archivo_name = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $carpetaCliente = public_path("/documentacionclientescomunes/{$clientecomun->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }

        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $carpetaCliente = public_path("/documentacionclientescomunes/{$clientecomun->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name);
        }

        $image_name2 = null;
        if ($request->hasFile('picture2')) {
            $file = $request->file('picture2');
            $carpetaCliente = public_path("/documentacionclientescomunes/{$clientecomun->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $image_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name2);
        }

        $nombrecliente = $request->input('nombrecompleto');
        $idcliente = $request->input('clientecomunid');

        // Iterar sobre las acciones seleccionadas (enviadas como array)
        $accionesSeleccionadas = $request->input('acciones', []); // 'acciones' viene de los checkboxes
        
        foreach ($accionesSeleccionadas as $accionId) {
            $accionNombre = Programacionsubcliente::where('id', $accionId)->value('accionnombre');

            // Guardar cada acción con el mismo PDF e imágenes
            Documentacionsubcliente::create(
                $request->except('acciones') + [
                    'document' => $archivo_name,
                    'accion' => $accionId,  // Guardar el ID de la acción
                    'accionnombre' => $accionNombre,  // Guardar el nombre de la acción (opcional)
                    'clientecomunid' => $idcliente,
                    'clientecomunnombre' => $nombrecliente,
                    'image' => $image_name,
                    'image2' => $image_name2
                ]
            );
        }

        return redirect()->route('admin.asociados.creardocumentacionclientecomun', $request->clientecomun)->with('info', 'El documento se subió con éxito');
    }
    public function listadodocumentacionclientecomun(ClienteComun $clientecomun, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarpor');
        $fechas = Documentacionsubcliente::where('clientecomunid', $clientecomun->id)
                                    ->pluck('fechabateria')
                                    ->unique();
        $documentacionclientes = collect();
        if ($fechaSeleccionada) {
            $documentacionclientes = Documentacionsubcliente::where('clientecomunid', $clientecomun->id)
                                                    ->where('fechabateria', $fechaSeleccionada)
                                                    ->simplePaginate(1000);
        }
        /* $documentacionclientes = Documentacionsubcliente::where('clienteitanombre', $cliente->nombrecompleto)->get(); */

        $id = ClienteComun::where('nombrecompleto', $clientecomun->nombrecompleto)->value('id');
        return view('admin.asociados.listadodocumentacionclientecomun', compact('id','fechas','fechaSeleccionada','clientecomun', 'documentacionclientes'));
    }
    public function buscardocumentoclientecomun(ClienteComun $clientecomun, Request $request)
    {
        return $this->listadodocumentacionclientecomun($clientecomun, $request);
    }
    public function documentacionmultipleclientecomun(Request $request, Asociado $asociado, ClienteComun $clientecomun)
    {
        $proveedor = $request->get('buscarpor');

        $clientes = Programacionsubcliente::where('proveedornombre', 'LIKE', "%$proveedor%")
            ->whereIn('accionnombre', function ($query) use ($proveedor) {
                $query->select('accionnombre')
                    ->from('estadoprogramacionsubclientes')
                    ->where('proveedornombre', 'LIKE', "%$proveedor%");
            })
            ->whereNotNull('clientecomunid')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('documentacionsubclientes')
                    ->whereRaw('documentacionsubclientes.clientecomunid = programacionsubclientes.clientecomunid')
                    ->whereRaw('documentacionsubclientes.accion = programacionsubclientes.accionnombre')
                    ->whereRaw('documentacionsubclientes.fechabateria = programacionsubclientes.fechabateria');
            })
            ->orderBy('proveedornombre')
            ->simplePaginate(10000);

        return view('admin.asociados.documentacionmultipleclientecomun', compact('clientecomun', 'asociado', 'clientes'));
    }
//


//CLIENTES AUDITORIA
//CREAR Y EDITAR CLIENTE AUDITORIA
    public function crearclienteauditoria(Asociado $asociado)
    {
        $suc = [
            'COCHABAMBA' => 'COCHABAMBA',
            'SANTA CRUZ' => 'SANTA CRUZ',
        ];
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
        $bancos = Banco::orderBy('nombrebanco')->pluck('nombrebanco', 'id');

        return view('admin.asociados.crearclienteauditoria', compact('suc', 'asociado', 'genero', 'departamentos', 'estciv', 'gradoins', 'actlab', 'bancos'));
    }
    public function guardarclienteauditoria(StoreClienteAuditoriaRequest $request)
    {
        // Verificar si el usuario ingresó un lugar de nacimiento personalizado
        if ($request->filled('custom_lugarnacimiento')) {
            $lugarnacimiento = $request->input('custom_lugarnacimiento');
        } else {
            // Si no se ingresó un valor personalizado, obtener el valor del select
            $id = $request->input('lugarnacimiento');
            $departamento = Departamento::findOrFail($id);
            $lugarnacimiento = $departamento->departamento;
        }

        $id = $request->input('lugarresidencia');
        $lugarresidencia = Departamento::findOrFail($id);
        $ciudadNombre = $lugarresidencia->departamento;

        $idBanco1 = $request->input('banco1');
        if ($idBanco1) {
            $banco1 = Banco::findOrFail($idBanco1);
            $bancoNombre1 = $banco1->nombrebanco;
        } else {
            $bancoNombre1 = "";
        }
        $idBanco2 = $request->input('banco2');
        if ($idBanco2) {
            $banco2 = Banco::findOrFail($idBanco2);
            $bancoNombre2 = $banco2->nombrebanco;
        } else {
            $bancoNombre2 = "";
        }
        $idBanco3 = $request->input('banco3');
        if ($idBanco3) {
            $banco3 = Banco::findOrFail($idBanco3);
            $bancoNombre3 = $banco3->nombrebanco;
        } else {
            $bancoNombre3 = "";
        }

        $clienteData = $request->all();
        $clienteData['lugarnacimiento'] = $lugarnacimiento;
        $clienteData['lugarresidencia'] = $ciudadNombre;
        $clienteData['banco1'] = $bancoNombre1;
        $clienteData['banco2'] = $bancoNombre2;
        $clienteData['banco3'] = $bancoNombre3;

        $clienteauditoria = ClienteAuditoria::create($clienteData);

        return redirect()->route('admin.asociados.verclienteauditoria', $clienteauditoria->id)->with('info', 'El cliente se creó con exito');
    }
    public function listadoclienteauditoria(Request $request, Asociado $asociado)
    {
        $nombrecompleto = $request->get('buscarpor');
        $clientes = ClienteAuditoria::where('nombrecompleto', 'LIKE', "%$nombrecompleto%")
                            ->orderBy('nombrecompleto')
                            ->simplePaginate(10000);
        return view('admin.asociados.listadoclienteauditoria', compact('asociado', 'clientes'));
    }
    public function buscarclientesauditoria(Request $request)
    {
        $busqueda = $request->get('buscarpor');
        $clienteauditorias = ClienteAuditoria::where(function ($query) use ($busqueda) {
            $query->where('nombrecompleto', 'like', "%$busqueda%")
                ->orWhere('ci', 'like', "%$busqueda%");
        })->simplePaginate(1000);
        return view('admin.asociados.listadoclienteauditoria', compact('clienteauditorias'));
    }
    public function verclienteauditoria(ClienteAuditoria $clienteauditoria)
    {
        $nombreusuario = auth()->user()->name; 
        $tieneRequisitos = Requisitosclientesauditoria::where('clienteauditoriaid', $clienteauditoria->id)->exists();
        $requisitosubclientes = ProveedorInformefinal::where('clienteauditoriaid', $clienteauditoria->id)->get();
        $proveedores = Proveedor::whereIn('id', [3, 54])->get(['id', 'proveedor', 'celular']);
        $tieneContactos = ContactoSubCliente::where('clienteauditoriaid', $clienteauditoria->id)->exists();
        $tieneTramites = Tramitesubcliente::where('clienteauditoriaid', $clienteauditoria->id)->exists();
        $tieneBateria = Bateriasubcliente::where('clienteauditoriaid', $clienteauditoria->id)->exists();
        $tieneProgramacion = Programacionsubcliente::where('clienteauditoriaid', $clienteauditoria->id)->exists();
        $tieneCotizacionaprobada = Estadocotizacionsubcliente::where('clienteauditoriaid', $clienteauditoria->id)->exists();
        $tieneProgramacionatentido = Estadoprogramacionsubcliente::where('clienteauditoriaid', $clienteauditoria->id)->exists();
        $tienerequisitosauditoria = Requisitosclientesauditoria::where('clienteauditoriaid', $clienteauditoria->id)->exists();
        $cartaconsentimientoExistente = DocumentacionSubcliente::where('clienteauditoriaid', $clienteauditoria->id) 
                    ->where('accion', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS')
                    ->whereNotNull('document')
                    ->first();
        $bateriaaprobadaExistente = DocumentacionSubcliente::where('clienteauditoriaid', $clienteauditoria->id) 
                    ->where('accion', 'APROBADO PARA INICIAR A CREAR BATERIA')
                    ->first();
        $documentacion = Documentacionsubcliente::where('clienteauditoriaid', $clienteauditoria->id)
                    ->where('accion', 'HISTORIA MÉDICA')
                    ->first();

        $historiamedica = Documentacionsubcliente::where('clienteauditoriaid', $clienteauditoria->id)
            ->where('accion', 'HISTORIA MÉDICA')
            ->first();
        $historiamedicaclienteauditoria = $historiamedica ? $historiamedica->document : null;



        $IDCliente = $clienteauditoria->id;
        $sucursalCliente = $clienteauditoria->sucursal;

        $accionesCliente = BateriaSubCliente::where('clienteauditoriaid', $IDCliente)->pluck('accionnombre')->toArray();

        $fechasbateriasSubCliente = BateriaSubCliente::where('clienteauditoriaid', $IDCliente)
            ->distinct()
            ->pluck('fechabateria');

        $fechasRegistradas = ProveedorInformefinal::where('clienteauditoriaid', $clienteauditoria->id)
            ->pluck('fechabateria');

        $fechasDisponibles = $fechasbateriasSubCliente->diff($fechasRegistradas);

        $fechasBateriaPorAccion = BateriaSubCliente::whereIn('accionnombre', $accionesCliente)
            ->where('clienteauditoriaid', $IDCliente)
            ->whereIn('fechabateria', $fechasDisponibles)
            ->distinct()
            ->pluck('fechabateria', 'accionnombre');

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accion => $fecha) {
            $accionesPorFecha[$fecha][] = $accion;
        }
        $tramitesPorFecha = Tramitesubcliente::where('clienteauditoriaid', $clienteauditoria->id)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->fechabateria => $item->tramite];
            });
        $clienteConInvalidez = Tramitesubcliente::where('clienteauditoriaid', $clienteauditoria->id)
            ->where('tramite', 'INVALIDEZ')
            ->exists();

        $clienteConApelacionOSegunda = Tramitesubcliente::where('clienteauditoriaid', $clienteauditoria->id)
            ->whereIn('tramite', ['APELACION', 'SEGUNDA SOLICITUD'])
            ->exists();


        return view('admin.asociados.verclienteauditoria', compact('requisitosubclientes','proveedores','accionesPorFecha','clienteConInvalidez','clienteConApelacionOSegunda','tramitesPorFecha','historiamedicaclienteauditoria','nombreusuario','tieneCotizacionaprobada','documentacion','tienerequisitosauditoria','tieneBateria','cartaconsentimientoExistente','bateriaaprobadaExistente',
        'tieneContactos','tieneTramites','clienteauditoria','tieneRequisitos','tieneProgramacion','tieneProgramacionatentido'));
    }
    public function editarclienteauditoria(ClienteAuditoria $clienteauditoria)
    {
        $suc = [
            'COCHABAMBA' => 'COCHABAMBA',
            'SANTA CRUZ' => 'SANTA CRUZ',
        ];
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
        /* $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'departamento'); */
        // Obtener los departamentos para el select
        $clienteAuditoria = ClienteAuditoria::findOrFail($clienteauditoria->id);
        
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'departamento');

        // Verificar si el lugarnacimiento está en la lista de departamentos
        $lugarnacimiento = $clienteAuditoria->lugarnacimiento;
        $isCustomPlace = !$departamentos->contains($lugarnacimiento); // Si no está en el select, es un lugar personalizado

        $bancos = Banco::orderBy('nombrebanco')->pluck('nombrebanco', 'nombrebanco');
        $bancoActual = $clienteauditoria->nombrebanco;

        return view('admin.asociados.editarclienteauditoria', compact('clienteauditoria','suc', 'genero', 'departamentos', 'estciv', 'gradoins', 'actlab', 'bancoActual', 'bancos', 'isCustomPlace', 'lugarnacimiento'));
    }
    public function actualizarclienteauditoria(UpdateClienteAuditoriaRequest $request, ClienteAuditoria $clienteauditoria)
    {
        $clienteData = $request->validated();
        $clienteauditoria->update($clienteData);

        return redirect()->route('admin.asociados.verclienteauditoria', $clienteauditoria)->with('info', 'El cliente se actualizó con éxito');
    }
    /* public function actualizarclienteauditoria(UpdateClienteAuditoriaRequest $request, ClienteAuditoria $clienteauditoria) 
    {
        // Verificar si el usuario ingresó un lugar de nacimiento personalizado
        if ($request->filled('custom_lugarnacimiento')) {
            // Si hay un lugar personalizado, usarlo
            $lugarnacimiento = $request->input('custom_lugarnacimiento');
        } else {
            // Si no hay personalizado, usar el lugar del select
            $id = $request->input('lugarnacimiento');
            $departamento = Departamento::findOrFail($id);
            $lugarnacimiento = $departamento->departamento;
        }
    
        // Validar y obtener los otros campos
        $clienteData = $request->validated();
        $clienteData['lugarnacimiento'] = $lugarnacimiento; // Establecer el lugar de nacimiento correcto
    
        // Actualizar el cliente
        $clienteauditoria->update($clienteData);
    
        return redirect()->route('admin.asociados.verclienteauditoria', $clienteauditoria)->with('info', 'El cliente se actualizó con éxito');
    } */
    
//
//ASIGNAR TRAMITE
    public function listadotramiteclienteauditoria(Asociado $asociado, ClienteAuditoria $clienteauditoria)
    {
        $nombreclienteita = $clienteauditoria->nombrecompleto;
        $tramitesubclientes = Tramitesubcliente::where('clienteauditorianombre', $nombreclienteita)
                                    ->simplePaginate(10000);

        $tramites = [
            'AUDITORIA MEDICA' => 'AUDITORIA MEDICA',
        ];

        $ciudades = [
            'COCHABAMBA' => 'COCHABAMBA',
            'SANTA CRUZ' => 'SANTA CRUZ',
        ];

        $nombreCliente = $clienteauditoria->nombrecompleto;
        $sucursalCliente = $clienteauditoria->sucursal;

        $accionesCliente = BateriaSubCliente::where('clienteauditorianombre', $nombreCliente)
            ->whereIn('accionnombre', function ($query) use ($sucursalCliente) {
                $query->select('accionnombre')->from('clienteauditorias')->where('sucursal', $sucursalCliente);
            })
            ->pluck('accionnombre')
            ->unique();

        $fechasEnEstadoCotizacionSubCliente = EstadoCotizacionSubCliente::where('clienteauditorianombre', $nombreCliente)
        ->distinct()
        ->pluck('fechabateria');

        $fechasBateriaPorAccion = BateriaSubCliente::whereIn('accionnombre', $accionesCliente)
        ->where('clienteauditorianombre', $nombreCliente)
        /* ->whereIn('fechabateria', $fechasEnEstadoCotizacionSubCliente) */
        ->distinct()
        ->pluck('fechabateria', 'accionnombre');

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accion => $fecha) {
        $accionesPorFecha[$fecha][] = $accion;
        }

        return view('admin.asociados.listadotramiteclienteauditoria', compact('ciudades', 'tramitesubclientes', 'clienteauditoria', 'asociado', /* 'apoderados', */ 'tramites'/* , 'apoderadoSiguiente' */, 'accionesPorFecha'));
    }
    public function guardartramiteclienteauditoria(StoreTramitesubclienteRequest $request)
    {
        $clienteID = $request->input('clienteauditoriaid');
        $clienteauditoria = ClienteAuditoria::findOrFail($clienteID);

        $clienteData = $request->all();
        $clienteData['clienteauditorianombre'] = $clienteauditoria->nombrecompleto;
        $clienteData['apoderado'] = $request->input('apoderado');
        $clienteData['usuarioid'] = $request->usuarioid;
        $clienteData['usuarioregistro'] = $request->usuarioregistro;
        $clienteData['estado'] = $request->input('estado');
        $tramitesubcliente = Tramitesubcliente::create($clienteData);

        // Obtener el trámite seleccionado
        $tramite = $request->input('tramite');

       
            $pdf = PDF::loadView('admin.asociados.generaretiquetaclienteauditoria', compact('clienteauditoria', 'tramite'));
            $pdfName = 'Etiqueta_Auditoria_' . $clienteauditoria->nombrecompleto . '.pdf';


        // Guarda el PDF directamente en la carpeta 'public'
        $pdf->save(public_path($pdfName));

        // Retorna la URL directamente desde 'public'
        return response()->json([
            'pdf_url' => asset($pdfName), // Se asume que el archivo está en 'public'
            'redirect_url' => route('admin.asociados.listadotramiteclienteauditoria', $clienteauditoria->id)
        ]);
    }
    public function asignarFecha_AUDITORIA(Request $request, $clienteId)
    {
        // Validar que se haya seleccionado una fecha de batería
        $request->validate([
            'fechabateria' => 'required'
        ], [
            'fechabateria.required' => 'Debe seleccionar una fecha de batería.'
        ]);

        // Encuentra el trámite del cliente por su ID
        $clienteTramite = Tramitesubcliente::find($clienteId);

        // Asignar la nueva fecha de batería
        $clienteTramite->fechabateria = $request->input('fechabateria');
        $clienteTramite->save();

        // Redirigir a la misma URL donde estaba el usuario
        return back()->with('info', 'Fecha asignada correctamente.');
    }
//
//CREAR BATERIA CLIENTE AUDITORIA
    public function crearbateriaclienteauditoria(ClienteAuditoria $clienteauditoria)
    {
        $sucursalCliente = $clienteauditoria->sucursal;
        $rolusuario = auth()->user()->getRoleNames()->first(); 
        $areas = Area::orderBy('nombrearea', 'asc')
                    ->where('idtipoarea', 2)
                    ->pluck('nombrearea', 'id');

        $accionesPorArea = [];
        foreach ($areas as $id => $nombreArea) {
            $accionesPorArea[$id] = Bateriaproveedor::where('areasid', $id)
                ->where('sucursal', $sucursalCliente)
                ->where('estado', 'ACTIVO')
                ->where('asociado', 'CLIENTES ITA')
                ->orderBy('accion', 'asc')
                ->get(['id', 'accion', 'proveedor', 'precio']);
        }

        $areas2 = Bateriaproveedor::orderBy('area', 'asc')
            ->where('tipoid', 1)
            ->where('estado', 'ACTIVO')
            ->where('sucursal', $sucursalCliente)
            ->where('asociado', 'CLIENTES ITA')
            ->get(['area', 'id', 'proveedor', 'precio']);

        $estadoproveedor = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
        $id = $clienteauditoria->nombrecompleto ? ClienteAuditoria::where('nombrecompleto', $clienteauditoria->nombrecompleto)->value('id') : null;

        $nombreCliente = $clienteauditoria->nombrecompleto; 
        $idCliente = $clienteauditoria->id; 

        $accionesCliente = BateriaSubCliente::where('clienteauditoriaid', $idCliente)
            ->pluck('accionid')
            ->toArray();

        $fechasbateriasSubCliente = BateriaSubCliente::where('clienteauditoriaid', $idCliente)
            ->distinct()
            ->pluck('fechabateria');

        $fechasBateriaPorAccion = BateriaSubCliente::whereIn('accionid', $accionesCliente)
            ->where('clienteauditoriaid', $idCliente)
            ->whereIn('fechabateria', $fechasbateriasSubCliente)
            ->distinct()
            ->pluck('fechabateria', 'accionid');

        $accionesNombres = BateriaSubCliente::whereIn('accionid', $accionesCliente)
            ->pluck('accionnombre', 'accionid')
            ->toArray();

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accionid => $fecha) {
            $idbateirasubcliente = Bateriasubcliente::where('accionid', $accionid)->where('clienteauditoriaid', $idCliente)->value('id');
            $precioaccion = Bateriasubcliente::where('accionid', $accionid)->where('clienteauditoriaid', $idCliente)->value('precio');
            $informeaccion = Bateriasubcliente::where('accionid', $accionid)->where('clienteauditoriaid', $idCliente)->value('informe');
            $proveedorbateria = Bateriasubcliente::where('accionid', $accionid)->where('clienteauditoriaid', $idCliente)->value('proveedorasignado');

            $accionNombre = $accionesNombres[$accionid] ?? 'Desconocida';

            $accionesPorFecha[$fecha][] = [
                'id' => $idbateirasubcliente,
                'accion' => $accionNombre,
                'proveedor' => $proveedorbateria,
                'precio' => $precioaccion,
                'informe' => $informeaccion
            ];
        }
            
        return view('admin.asociados.crearbateriaclienteauditoria', compact(
            'accionesPorFecha',
            'fechasBateriaPorAccion',
            'departamentos',
            'estadoproveedor',
            'areas',
            'accionesPorArea',
            'clienteauditoria',
            'id',
            'accionesCliente',
            'areas2','rolusuario'
        ));
    }
    public function guardarbateriaclienteauditoria(StoreBateriasubclienteRequest $request)
    {
        $clienteID = $request->input('clienteauditoriaid');
        $clienteauditoria = ClienteAuditoria::findOrFail($clienteID);
        $accionesSeleccionadas = $request->input('acciones');
        $tipoArea = $request->input('tipoarea');
        $informe = $request->input('informe');
        $sucursalCliente = $clienteauditoria->sucursal;
        $fechaActual = now()->toDateString();
        $antecedentes = $request->input('antecedentes');
        $fechainforme = $request->input('fechainforme');
        $horaActual = now()->format('H:i:s');
        $usuarioID = auth()->user()->id;
        $usuarioNombre = auth()->user()->name;

        if ($tipoArea === 'Estudios') {
            $areasSeleccionadas = $request->input('areanombre');
            if (!is_array($areasSeleccionadas)) {
                $areasSeleccionadas = [$areasSeleccionadas];
            }
            foreach ($areasSeleccionadas as $areaId) {
                $area = Area::findOrFail($areaId);
                $areaNombre = $area->nombrearea;
                foreach ($accionesSeleccionadas as $accionId) {
                    $areaAccion = Bateriaproveedor::where('id', $accionId)
                                                ->where('areasid', $areaId)
                                                ->where('sucursal', $sucursalCliente)
                                                ->first();
                    if ($areaAccion) {
                        $accionNombre = $areaAccion->accion;
                        $precioAccion = $areaAccion->precio;
                        $preciocompraAccion = $areaAccion->preciocompra;
                        $proveedorAsignado = $areaAccion->proveedor;
                        $servicio = $areaAccion->servicio;
                    } else {
                        $accionNombre = 'DATO NO ENCONTRADO';
                        $precioAccion = 0;
                        $preciocompraAccion = 0;
                        $proveedorAsignado = 'DATO NO ENCONTRADO';
                    }
                    $fechaSeleccionada = $request->input('fechabateria');
                    $clienteitaData = $request->except(['acciones', '_token']);
                    $clienteitaData['clienteauditoriaid'] = $clienteID;
                    $clienteitaData['areanombre'] = $areaNombre;
                    $clienteitaData['clienteauditorianombre'] = $clienteauditoria->nombrecompleto;
                    $clienteitaData['tipoarea'] = 'ESTUDIO';
                    if ($informe === 'SI TIENE INFORME') {
                        $clienteitaData['accionid'] = $accionId . 'PA';
                        $clienteitaData['accionnombre'] = $accionNombre . ' - PA';
                        $clienteitaData['precio'] = '0';
                        $clienteitaData['preciocompra'] = '0';
                        $clienteitaData['proveedorasignado'] = 'PROVEEDOR AJENO';
                        $clienteitaData['servicio'] = 'AJENO';
                    } else {
                        $clienteitaData['accionid'] = $accionId;
                        $clienteitaData['accionnombre'] = $accionNombre;
                        $clienteitaData['precio'] = $precioAccion;
                        $clienteitaData['preciocompra'] = $preciocompraAccion;
                        $clienteitaData['proveedorasignado'] = $proveedorAsignado;
                        $clienteitaData['servicio'] = $servicio;
                    }
                    $clienteitaData['fechabateria'] = $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada;
                    $clienteitaData['informe'] = $informe;
                    $clienteitaData['usuarioid'] = $usuarioID;
                    $clienteitaData['usuarioregistro'] = $usuarioNombre;
                    $clienteitaData['fechainforme'] = $fechainforme;
                    Bateriasubcliente::create($clienteitaData);

                    if ($informe === 'SI TIENE INFORME') {
                        Programacionsubcliente::create([
                            'proveedornombre' => 'PROVEEDOR AJENO',
                            'clienteauditoriaid' => $clienteID,
                            'clienteauditorianombre' => $clienteauditoria->nombrecompleto,
                            'fechaasignada' => $fechaActual,
                            'fechabateria' => $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada,
                            'horaasignada' => $horaActual,
                            'horadesde' => $horaActual,
                            'horahasta' => $horaActual,
                            'areanombre' => $areaNombre,
                            'accionnombre' => $accionNombre . ' - PA',
                            'precio' => '0',
                            'usuarioid' => $usuarioID,
                            'usuarioregistro' => $usuarioNombre,
                        ]);
                        Estadoprogramacionsubcliente::create([
                            'clienteauditoriaid' => $clienteID,
                            'clienteauditorianombre' => $clienteauditoria->nombrecompleto,
                            'fechaatencionprogramacion' => $fechaActual,
                            'fechabateria' => $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada,
                            'accionnombre' => $accionNombre . ' - PA',
                            'usuarioid' => $usuarioID,
                            'usuarioregistro' => $usuarioNombre,
                        ]);
                    }
                }
            }
        } elseif ($tipoArea === 'Especialidades') {
            $accionesSeleccionadas = $request->input('accionnombre');
            if (!is_array($accionesSeleccionadas)) {
                $accionesSeleccionadas = [$accionesSeleccionadas];
            }
            foreach ($accionesSeleccionadas as $accionId) {
                $areaAccion = Bateriaproveedor::where('id', $accionId)
                                            ->where('sucursal', $sucursalCliente)
                                            ->first();
                if ($areaAccion) {
                    $accionNombre = $areaAccion->accion;
                    $precioAccion = $areaAccion->precio;
                    $preciocompraAccion = $areaAccion->preciocompra;
                    $proveedorAsignado = $areaAccion->proveedor;
                    $servicio = $areaAccion->servicio;
                } else {
                    $accionNombre = 'DATO NO ENCONTRADO';
                    $precioAccion = 0;
                    $preciocompraAccion = 0;
                    $proveedorAsignado = 'DATO NO ENCONTRADO';
                }
                $fechaSeleccionada = $request->input('fechabateria');
                $clienteitaData = $request->except(['accionnombre', '_token']);
                $clienteitaData['antecedentes'] = $antecedentes;
                $clienteitaData['clienteauditoriaid'] = $clienteID;
                $clienteitaData['areanombre'] = $accionNombre;
                $clienteitaData['clienteauditorianombre'] = $clienteauditoria->nombrecompleto;
                $clienteitaData['tipoarea'] = 'ESPECIALIDAD';
                if ($informe === 'SI TIENE INFORME') {
                    $clienteitaData['accionid'] = $accionId . 'PA';
                    $clienteitaData['accionnombre'] = $accionNombre . ' - PA';
                    $clienteitaData['precio'] = '0';
                    $clienteitaData['preciocompra'] = '0';
                    $clienteitaData['proveedorasignado'] = 'PROVEEDOR AJENO';
                    $clienteitaData['servicio'] = 'AJENO';
                } else{
                    $clienteitaData['accionid'] = $accionId;
                    $clienteitaData['accionnombre'] = $accionNombre;
                    $clienteitaData['precio'] = $precioAccion;
                    $clienteitaData['preciocompra'] = $preciocompraAccion;
                    $clienteitaData['proveedorasignado'] = $proveedorAsignado;
                    $clienteitaData['servicio'] = $servicio;
                }
                $clienteitaData['fechabateria'] = $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada;
                $clienteitaData['informe'] = $informe;
                $clienteitaData['usuarioid'] = $usuarioID;
                $clienteitaData['usuarioregistro'] = $usuarioNombre;
                $clienteitaData['fechainforme'] = $fechainforme;
                Bateriasubcliente::create($clienteitaData);

                if ($informe === 'SI TIENE INFORME') {
                    Programacionsubcliente::create([
                        'proveedornombre' => 'PROVEEDOR AJENO',
                        'clienteauditoriaid' => $clienteID,
                        'clienteauditorianombre' => $clienteauditoria->nombrecompleto,
                        'fechaasignada' => $fechaActual,
                        'fechabateria' => $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada,
                        'horaasignada' => $horaActual,
                        'horadesde' => $horaActual,
                        'horahasta' => $horaActual,
                        'areanombre' => $accionNombre,
                        'accionnombre' => $accionNombre . ' - PA',
                        'precio' => '0',
                        'usuarioid' => $usuarioID,
                        'usuarioregistro' => $usuarioNombre,
                    ]);
                    Estadoprogramacionsubcliente::create([
                        'clienteauditoriaid' => $clienteID,
                        'clienteauditorianombre' => $clienteauditoria->nombrecompleto,
                        'fechaatencionprogramacion' => $fechaActual,
                        'fechabateria' => $fechaSeleccionada === 'nueva_bateria' ? $fechaActual : $fechaSeleccionada,
                        'accionnombre' => $accionNombre . ' - PA',
                        'usuarioid' => $usuarioID,
                        'usuarioregistro' => $usuarioNombre,
                    ]);
                }
            }
        }
        return redirect()->route('admin.asociados.crearbateriaclienteauditoria', ['clienteauditoria' => $clienteauditoria])->with('info', 'La batería se creó con éxito');
    }
    public function generarPDFClienteauditoriabateria(Request $request, $clienteId) 
    {
        // Obtener el cliente
        $clienteauditoria = ClienteAuditoria::find($clienteId);

        // Validar que la fecha esté presente
        $fechaSeleccionada = $request->input('fecha');
        if (!$fechaSeleccionada) {
            return response()->json(['error' => 'No se seleccionó ninguna fecha.'], 400);
        }

        // Filtrar los registros asociados al cliente ITA según la fecha seleccionada
        $bateriasEvaluaciones = BateriaSubCliente::where('clienteauditoriaid', $clienteId)
            ->whereDate('fechabateria', $fechaSeleccionada)
            ->where('tipoarea', 'ESPECIALIDAD')
            ->whereIn('areanombre', ['PSIQUIATRIA', 'PSICOLOGIA', 'FISIOTERAPIA', 'TRABAJO SOCIAL'])
            ->pluck('areanombre')
            ->unique();

        // Filtrar los registros asociados al cliente ITA según la fecha seleccionada
        $bateriasEspecialidades = BateriaSubCliente::where('clienteauditoriaid', $clienteId)
            ->whereDate('fechabateria', $fechaSeleccionada)
            ->where('tipoarea', 'ESPECIALIDAD')
            ->whereNotIn('areanombre', ['PSIQUIATRIA', 'PSICOLOGIA', 'FISIOTERAPIA', 'TRABAJO SOCIAL', 'INFORME FINAL'])
            ->pluck('areanombre')
            ->unique();


        $bateriasEstudios = BateriaSubCliente::where('clienteauditoriaid', $clienteId)
            ->whereDate('fechabateria', $fechaSeleccionada)
            ->where('tipoarea', 'ESTUDIO')
            ->pluck('areanombre')
            ->unique();

        // Definir los estudios que siempre deben aparecer en "EVALUACIONES MEDICAS TÉCNICAS"
        $tituloEvaluaciones = 'EVALUACIONES MEDICAS TÉCNICAS';
        $estudiosFijos = ['TRABAJO SOCIAL', 'FISIOTERAPIA Y KINESIOLOGIA', 'PSICOLOGIA', 'PSIQUIATRA'];

        // Definir los títulos de las otras secciones
        $tituloEspecialidades = 'SOLICITUD DE INTERCONSULTAS';
        $tituloComplementarios = 'SOLICITUD DE ESTUDIOS COMPLEMENTARIOS';

        // Dividimos los resultados en grupos de 9
        $especialidadesPorFilas = $bateriasEspecialidades->chunk(9);
        $estudiosPorFilas = $bateriasEstudios->chunk(9);
        $evaluacionesPorFilas = $bateriasEvaluaciones->chunk(9);

        // Crear el PDF
        $pdf = PDF::loadView('admin.asociados.pdf.checklistclienteauditoria', [
            'clienteauditoria' => $clienteauditoria,
            'fechaSeleccionada' => $fechaSeleccionada,
            'tituloEvaluaciones' => $tituloEvaluaciones,
            'estudiosFijos' => $estudiosFijos,
            'especialidadesAsociadas' => $especialidadesPorFilas,
            'estudiosAsociados' => $estudiosPorFilas,
            'evaluacionesAsociados' => $evaluacionesPorFilas,
            'tituloEspecialidades' => $tituloEspecialidades,
            'tituloComplementarios' => $tituloComplementarios,
        ]);

        // Generar el archivo PDF y retornarlo como descarga
        $fileName = 'checklist_' . $clienteauditoria->nombrecompleto . '_' . time() . '.pdf';
        return $pdf->download($fileName);
    }
//
//APROBAR COTIZACION DE PROGRAMACION DE CLIENTE AUDITORIA
    public function aprobacioncotizacionclienteauditoria(ClienteAuditoria $clienteauditoria, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarporfecha');
        $servicioSeleccionado = $request->get('buscarporservicio');
        $areasSeleccionadas = $request->get('buscarporarea', []);

        if (!is_array($areasSeleccionadas)) {
            $areasSeleccionadas = [];
        }
    
        $fechas = Tramitesubcliente::where('clienteauditoriaid', $clienteauditoria->id)
                                    ->pluck('fechabateria')
                                    ->unique();
                                    
        $areasPorFecha = BateriaSubCliente::where('clienteauditoriaid', $clienteauditoria->id)
                                    ->get()
                                    ->groupBy('fechabateria')
                                    ->map(function ($items) {
                                        return $items->pluck('areanombre')->unique()->values();
                                    });
        
    
        $bateriasubclientes = collect();
        $total = 0;
    
        $query = BateriaSubCliente::where('clienteauditoriaid', $clienteauditoria->id);
        if ($fechaSeleccionada) {
            $query->where('fechabateria', $fechaSeleccionada);
        }
        if ($servicioSeleccionado) {
            $query->where('servicio', $servicioSeleccionado);
        }
        if (!empty($areasSeleccionadas)) {
            $query->whereIn('areanombre', $areasSeleccionadas);
        }
    
        if ($fechaSeleccionada || !empty($areasSeleccionadas)) {
            $bateriasubclientes = $query->simplePaginate(1000);
    
            $total = $bateriasubclientes->sum(function ($bateriasubcliente) {
                return str_replace(',', '.', $bateriasubcliente->precio);
            });
            $total = number_format($total, 2, '.', '');
        }
        $id = $clienteauditoria->id;
    
        return view('admin.asociados.aprobacioncotizacionclienteauditoria', compact('servicioSeleccionado','bateriasubclientes', 'id', 'clienteauditoria', 'fechas', 'total', 'fechaSeleccionada', 'areasPorFecha', 'areasSeleccionadas'));
    }
    public function buscarbateriaclienteauditoria(ClienteAuditoria $clienteauditoria, Request $request)
    {
        return $this->aprobacioncotizacionclienteauditoria($clienteauditoria, $request);
    }
    public function generarpdfcotizacionclienteauditoria(ClienteAuditoria $clienteauditoria, Request $request) 
    {
        $fechaSeleccionada = $request->get('buscarporfecha');
        $servicioSeleccionado = $request->get('buscarporservicio');
        $areasSeleccionadas = $request->get('buscarporarea', []);
        $total = $request->get('total');
    
        $areasSeleccionadas = is_array($areasSeleccionadas) ? $areasSeleccionadas : explode(',', $areasSeleccionadas);
    
        $query = BateriaSubCliente::where('clienteauditoriaid', $clienteauditoria->id);
    
        if ($fechaSeleccionada) {
            $query->where('fechabateria', $fechaSeleccionada);
        }
        if ($servicioSeleccionado) {
            $query->where('servicio', $servicioSeleccionado);
        }
        if (!empty($areasSeleccionadas)) {
            $query->whereIn('areanombre', $areasSeleccionadas);
        }
    
        $bateriasubclientes = $query->get();
    
        if (!$total) {
            $total = $bateriasubclientes->sum(function ($bateriasubcliente) {
                return str_replace(',', '.', $bateriasubcliente->precio);
            });
            $total = number_format($total, 2, '.', '');
        }
    
        // Determina qué vista usar basado en el valor de buscarporservicio
        $vistaPdf = $servicioSeleccionado === 'AJENO'
            ? 'admin.asociados.pdfcotizacionajenoclienteauditoria'
            : 'admin.asociados.pdfcotizacionclienteauditoria';
    
        // Determina el nombre del archivo PDF basado en el valor de buscarporservicio
        $pdfName = $servicioSeleccionado === 'AJENO'
            ? 'Informes_a_presentar_' . $clienteauditoria->nombrecompleto
            : 'Cotización_' . $clienteauditoria->nombrecompleto;
    
        $pdfName .= '.pdf';
    
        // Genera el PDF
        $pdf = Pdf::loadView($vistaPdf, [
            'clienteauditoria' => $clienteauditoria,
            'bateriasubclientes' => $bateriasubclientes,
            'total' => $total
        ]);
    
        return $pdf->download($pdfName);
    }
    public function actualizarPdfcotauditoria(Request $request) 
    {
        // Validar el formulario
        $request->validate([
            'fechabateria' => 'required|date',
            'archivo' => 'required|file|mimes:pdf|max:20480', // max:20MB
        ]);

        // Encuentra el estado de cotización existente
        $estadoCotizacion = EstadoCotizacionSubCliente::where('clienteauditoriaid', $request->clienteauditoriaid)
            ->where('fechabateria', $request->fechabateria)
            ->first();

        // Verifica si el registro existe
        if ($estadoCotizacion) {
            $carpetaCliente = public_path("/cotizacionesaprobadasauditoria/{$request->clienteauditoriaid}");
            
            // Elimina el archivo PDF existente si es necesario
            if ($estadoCotizacion->document) {
                $archivoAntiguo = $carpetaCliente . '/' . $estadoCotizacion->document;
                if (file_exists($archivoAntiguo)) {
                    unlink($archivoAntiguo);
                }
            }
            
            // Guarda el nuevo archivo PDF
            $archivo_name = null;
            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $archivo_name = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name);
            }
            
            // Actualiza el registro existente
            $estadoCotizacion->update([
                'document' => $archivo_name,
                'usuarioid' => auth()->user()->id,
                'usuarioregistro' => auth()->user()->name
            ]);
            
            return redirect()->route('admin.asociados.aprobarcotizacionprogramacionclienteauditoria', $request->clienteauditoriaid)
                ->with('info', 'El documento se actualizó con éxito');
        } else {
            return redirect()->route('admin.asociados.aprobarcotizacionprogramacionclienteauditoria', $request->clienteauditoriaid)
                ->with('error', 'No se encontró el registro para actualizar');
        }
    }
    public function aprobarcotizacionprogramacionclienteauditoria(ClienteAuditoria $clienteauditoria)
    {
        $nombreCliente = $clienteauditoria->nombrecompleto;

        $id = $clienteauditoria->nombrecompleto ? ClienteAuditoria::where('nombrecompleto', $clienteauditoria->nombrecompleto)->value('id') : null;

        $fechasRegistradas = EstadoCotizacionSubCliente::where('clienteauditoriaid', $clienteauditoria->id)
                                        ->pluck('fechabateria')
                                        ->unique();

        $fechasDisponibles = BateriaSubCliente::where('clienteauditoriaid', $clienteauditoria->id)
                                        ->pluck('fechabateria')
                                        ->unique();

        $fechasConFactura = EstadoCotizacionSubCliente::where('clienteauditoriaid', $clienteauditoria->id)
                                        ->whereNotNull('nrofactura')
                                        ->pluck('fechabateria')
                                        ->unique();

        $fechas = $fechasDisponibles->filter(function ($fecha) use ($fechasRegistradas) {
            return !$fechasRegistradas->contains($fecha);
        });

        $fechasregis = $fechasDisponibles->filter(function ($fecha) use ($fechasConFactura) {
            return !$fechasConFactura->contains($fecha);
        });

        $documentosPorFecha = EstadoCotizacionSubCliente::where('clienteauditoriaid', $clienteauditoria->id)
            ->get(['fechabateria', 'document', 'documentconsinfo'])
            ->groupBy('fechabateria');
        $fecha = '';
        return view('admin.asociados.aprobarcotizacionprogramacionclienteauditoria', compact('fecha','documentosPorFecha','fechasregis','clienteauditoria', 'id', 'fechas', 'fechasRegistradas','fechasDisponibles'));
    }
    public function guardaraprobacioncotizacionclienteauditoria(StoreEstadocotizacionsubclienteRequest $request, ClienteAuditoria $clienteauditoria)
    {
        $archivo_name = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $carpetaCliente = public_path("/cotizacionesaprobadasauditoria/{$clienteauditoria->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }
        $archivo_name2 = null;
        if ($request->hasFile('archivo2')) {
            $file = $request->file('archivo2');
            $carpetaCliente = public_path("/cotizacionesaprobadasauditoria/{$clienteauditoria->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name2);
        }
        $documentacioncotizacioncliente = Estadocotizacionsubcliente::create([
            'document' => $archivo_name,
            'documentconsinfo' => $archivo_name2,
            'usuarioid' => auth()->user()->id,
            'usuarioregistro' => auth()->user()->name,
            'clienteauditoriaid' => $request->clienteauditoriaid,
            'clienteauditorianombre' => $request->clienteauditorianombre,
            'fechabateria' => $request->input('fechabateria'),
        ]);
    
        return redirect()->route('admin.asociados.aprobarcotizacionprogramacionclienteauditoria', $request->clienteauditoria)->with('info', 'El documento se subió con éxito');
    }
//
//CREAR PROGRAMACION Y REPROGRAMACION DE CLIENTE AUDITORIA
    public function crearprogramacionclienteauditoria(ClienteAuditoria $clienteauditoria, Request $request)
    {
        $nombreCliente = $clienteauditoria->nombrecompleto;
        $idCliente = $clienteauditoria->id;
        $clienteitaid = $clienteauditoria->id;
        $sucursalCliente = $clienteauditoria->sucursal;
        $rolusuario = auth()->user()->getRoleNames()->first(); 
        $esProveedor = ($rolusuario === 'PROVEEDOR');
        $id = $clienteauditoria->nombrecompleto ? ClienteAuditoria::where('nombrecompleto', $clienteauditoria->nombrecompleto)->value('id') : null;

        $accionesCliente = BateriaSubCliente::where('clienteauditorianombre', $nombreCliente)
            ->whereIn('accionnombre', function ($query) use ($sucursalCliente) {
                $query->select('accionnombre')->from('clienteauditorias')->where('sucursal', $sucursalCliente);
            })
            ->pluck('accionnombre')
            ->unique();

        $proveedoresAsignados = BateriaSubCliente::where('clienteauditorianombre', $nombreCliente)
            ->whereIn('accionnombre', $accionesCliente)
            ->pluck('proveedorasignado', 'accionnombre')
            ->toArray();

        $fechasEnEstadoCotizacionSubCliente = EstadoCotizacionSubCliente::where('clienteauditorianombre', $nombreCliente)  
            ->distinct()
            ->pluck('fechabateria');
    
        $fechasBateriaPorAccion = BateriaSubCliente::where('clienteauditorianombre', $nombreCliente)
            ->where(function ($query) use ($fechasEnEstadoCotizacionSubCliente) {
                $query->whereIn('fechabateria', $fechasEnEstadoCotizacionSubCliente)
                    ->orWhere('accionnombre', 'MEDICINA LABORAL');
            })
            ->whereIn('accionnombre', $accionesCliente)
            ->select('accionnombre', 'fechabateria')
            ->get();
        
        $accionesRegistradas = Programacionsubcliente::where('clienteauditoriaid', $idCliente)
            ->pluck('accionnombre', 'fechabateria')
            ->toArray();

        foreach ($accionesRegistradas as $fecha => $accion) {
        if (!isset($accionesRegistradas[$fecha])) {
            $accionesRegistradas[$fecha] = [];
            }

            if (!is_array($accionesRegistradas[$fecha])) {
                $accionesRegistradas[$fecha] = [$accion];
            } else {
                $accionesRegistradas[$fecha][] = $accion;
            }
        }

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $item) {
            $accion = $item->accionnombre;
            $fecha = $item->fechabateria;

            $accionYaRegistrada = Programacionsubcliente::where('clienteauditoriaid', $idCliente)
                ->where('fechabateria', $fecha)
                ->where('accionnombre', $accion)
                ->exists();
        
            if (!isset($accionesPorFecha[$fecha])) {
                $accionesPorFecha[$fecha] = [];
            }

            if (!$accionYaRegistrada) {
                $accionesPorFecha[$fecha][] = $accion;
            }
        }

        
        $proveedoresDetalles = [];
        foreach ($proveedoresAsignados as $accion => $nombreProveedor) {
            $proveedor = BateriaSubCliente::where('accionnombre', $accion)->where('clienteauditoriaid', $idCliente)
                ->latest()
                ->first();

            if ($proveedor) {
                $proveedoresDetalles[$accion] = [
                    'proveedor' => $proveedor->proveedorasignado,
                    'horarioinicial' => $proveedor->horarioinicial,
                    'horariofinal' => $proveedor->horariofinal,
                    'fechabateria' => $proveedor->fechabateria,
                    'fechaasignada' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clienteauditorianombre', $nombreCliente)
                        ->value('fechaasignada'),
                    'horadesde' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clienteauditorianombre', $nombreCliente)
                        ->value('horadesde'),
                    'horahasta' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clienteauditorianombre', $nombreCliente)
                        ->value('horahasta'),
                    'tiempoatencion' => $proveedor->tiempoatencion,
                    'accion' => $proveedor->accionnombre,
                    'area' => $proveedor->areanombre,
                    'precio' => $proveedor->precio,
                    'preciocompra' => $proveedor->preciocompra,
                    'programacionid' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clienteauditorianombre', $nombreCliente)
                        ->value('id'),
                ];
            }
        }

        $fechasBateria = BateriaSubCliente::where('clienteauditoriaid', $clienteitaid)
            ->distinct()
            ->pluck('fechabateria');


        $accionesPorFechaBateria = [];
        foreach ($fechasBateria as $fecha) {
            $accionesBateria = BateriaSubCliente::where('fechabateria', $fecha)
                ->where('clienteauditoriaid', $clienteitaid)
                ->pluck('accionnombre')
                ->toArray();

            $accionesPorFechaBateria[$fecha] = $accionesBateria;
        }

        $accionesDetallesPorFecha = [];
        foreach ($fechasBateria as $fecha) {
            $accionesProgramadas = ProgramacionSubCliente::where('fechabateria', $fecha)
                ->where('clienteauditoriaid', $clienteitaid)
                ->get(['id', 'accionnombre','proveedornombre', 'fechaasignada', 'horadesde', 'horahasta', 'horahasta', 'precio']);

            foreach ($accionesProgramadas as $accion) {
                $accionesDetallesPorFecha[$fecha][$accion->accionnombre] = $accion;
            }
        }

        // Obtén la URL previa
        $previousUrl = url()->previous();

        // Verifica si la URL previa es diferente a la almacenada y si no es la misma que la URL actual
        if (session('previous_url') !== $previousUrl && $previousUrl !== url()->current()) {
            session(['previous_url' => $previousUrl]);
        }
        
        return view('admin.asociados.crearprogramacionclienteauditoria', compact('esProveedor','accionesDetallesPorFecha','accionesPorFechaBateria','fechasBateria','id','rolusuario', 'clienteauditoria', 'accionesPorFecha', 'proveedoresDetalles', 'accionesRegistradas', 'fechasBateriaPorAccion', 'accionesCliente'));
    }
    public function guardarprogramacionclienteauditoria(StoreProgramacionsubclienteRequest $request)
    {
        // Recoge las acciones seleccionadas
        $accionesSeleccionadas = $request->input('accionesSeleccionadas', []);
        $horaasignada = $request->input('horaasignada');
        $fechaasignada = $request->input('fechaasignada');
        $clienteauditoriaid = $request->input('clienteauditoriaid');
        $clienteauditorianombre = $request->input('clienteauditorianombre');
        $fechabateria = $request->input('fechabateria');
        $horadesde = $request->input('horadesde');
        $horahasta = $request->input('horahasta');

        foreach ($accionesSeleccionadas as $accion) {
            // Sanitiza el nombre de la acción
            $accionSanitizada = str_replace([' ', '.'], ['_', '-'], $accion);
            
            // Captura los datos específicos de cada acción
            $proveedornombre = $request->input("proveedor_$accionSanitizada");
            $areanombre = $request->input("areanombre_$accionSanitizada");
            $precio = $request->input("precio_$accionSanitizada");
            $preciocompra = $request->input("preciocompra_$accionSanitizada");

            // Verifica si ya existe la programación
            $existente = Programacionsubcliente::where('accionnombre', $accion)
                ->where('fechabateria', $fechabateria)
                ->where('clienteauditoriaid', $clienteauditoriaid)
                ->exists();

            // Solo crea un nuevo registro si no existe
            if (!$existente) {
                Programacionsubcliente::create([
                    'accionnombre' => $accion,
                    'horaasignada' => $horaasignada,
                    'fechaasignada' => $fechaasignada,
                    'proveedornombre' => $proveedornombre,
                    'clienteauditoriaid' => $clienteauditoriaid,
                    'clienteauditorianombre' => $clienteauditorianombre,
                    'horadesde' => $horadesde,
                    'horahasta' => $horahasta,
                    'fechabateria' => $fechabateria,
                    'areanombre' => $areanombre,
                    'precio' => $precio,
                    'preciocompra' => $preciocompra,
                    'usuarioid' => Auth::id(), // ID del usuario autenticado
                    'usuarioregistro' => Auth::user()->name, // Nombre del usuario autenticado
                ]);
            }
        }

        return redirect()->route('admin.asociados.crearprogramacionclienteauditoria', $request->clienteauditoria)->with('info', 'La programación del cliente se creó con éxito');
    }
    public function reprogramacionclienteauditoria(ClienteAuditoria $clienteauditoria, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarpor');
        $fechas = ProgramacionSubCliente::where('clienteauditoriaid', $clienteauditoria->id)
                                    ->pluck('fechabateria')
                                    ->unique();
        $programacionsubclientes = collect();
        
        $reprogramaciones = ProgramacionSubCliente::where('clienteauditoriaid', $clienteauditoria->id)
        ->onlyTrashed()
        ->get();
        $total = 0;
        if ($fechaSeleccionada) {
            $programacionsubclientes = ProgramacionSubCliente::where('clienteauditoriaid', $clienteauditoria->id)
                                                    ->where('fechabateria', $fechaSeleccionada)
                                                    ->simplePaginate(1000);
            $total = $programacionsubclientes->sum(function ($programacionsubcliente) {
                return str_replace(',', '.', $programacionsubcliente->precio);
            });
            $total = number_format($total, 2, '.', '');
        }


        $id = ClienteAuditoria::where('nombrecompleto', $clienteauditoria->nombrecompleto)->value('id');
        return view('admin.asociados.reprogramacionclienteauditoria', compact('reprogramaciones','programacionsubclientes', 'id', 'clienteauditoria', 'fechas', 'total', 'fechaSeleccionada'));
    }
    public function buscarprogramacionclienteauditoria(ClienteAuditoria $clienteauditoria, Request $request)
    {
        return $this->reprogramacionclienteauditoria($clienteauditoria, $request);
    }
    public function guardarreprogramacionclienteauditoria(Request $request, Programacionsubcliente $programacionsubcliente)
    {
        $request->validate([
            'motivoreprogramacion' => 'required|string|max:255',
            'usuarioactualizacion' => 'required|string',
        ]);
        $usuarioActualizacion = $request->input('usuarioactualizacion');
        $programacionsubcliente->motivoreprogramacion = $request->motivoreprogramacion;
        $programacionsubcliente->usuarioactualizacion = $usuarioActualizacion;
        $programacionsubcliente->save();

        $programacionsubcliente->delete();

        $clienteauditoria = ClienteAuditoria::where('nombrecompleto', $programacionsubcliente->clienteauditorianombre)->first();

        return redirect()->route('admin.asociados.reprogramacionclienteauditoria', $clienteauditoria)->with('eliminar', 'ok');
    }
    public function estadoprogramacionclienteauditoria(ClienteAuditoria $clienteauditoria, Request $request)
    { 
        $fechaSeleccionada = $request->get('buscarpor');
        
        $fechas = Programacionsubcliente::where('clienteauditoriaid', $clienteauditoria->id)
                                    ->pluck('fechabateria')
                                    ->unique();

        $accionesDisponibles = collect();
        
        if ($fechaSeleccionada) {
            $accionesDisponibles = ProgramacionSubCliente::where('clienteauditoriaid', $clienteauditoria->id)
                                                    ->where('fechabateria', $fechaSeleccionada)
                                                    ->simplePaginate(1000);
        }
        $nombreCliente = $clienteauditoria->nombrecompleto;
        $accionesCliente = BateriaSubCliente::where('clienteauditorianombre', $nombreCliente)->pluck('accionnombre')->toArray();
        $id = $clienteauditoria->nombrecompleto ? ClienteAuditoria::where('nombrecompleto', $clienteauditoria->nombrecompleto)->value('id') : null;
        $nombreclienteita = $clienteauditoria->nombrecompleto ? ClienteAuditoria::where('nombrecompleto', $clienteauditoria->nombrecompleto)->value('nombrecompleto') : null;

        $accionesPorArea = Programacionsubcliente::where('clienteauditorianombre', $nombreCliente)
            ->get(['accionnombre', 'proveedornombre','fechabateria','fechaasignada', 'horadesde', 'horahasta']);

        $estadoRegistrados = Estadoprogramacionsubcliente::where('clienteauditorianombre', $nombreCliente)
                ->get(['accionnombre', 'fechabateria']);

        $estadoMapeado = [];
            foreach ($estadoRegistrados as $estado) {
                $estadoMapeado[$estado->accionnombre][$estado->fechabateria] = true;
            }

        $accionesDisponibles = $accionesDisponibles ?? $accionesPorArea;
        
        $accionesRegistradas = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clienteauditorianombre', $nombreCliente)
            ->pluck('accionnombre')
            ->toArray();

        $fechasEnEstadoCotizacionSubCliente = EstadoCotizacionSubCliente::where('clienteauditorianombre', $nombreCliente)
            ->distinct()
            ->pluck('fechabateria');

        $fechasBateriaPorAccion = Programacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clienteauditorianombre', $nombreCliente)
            /* ->whereIn('fechabateria', $fechasEnEstadoCotizacionSubCliente) */
            ->distinct()
            ->pluck('fechabateria', 'accionnombre');

        $accionesPorFecha = Programacionsubcliente::where('clienteauditorianombre', $nombreCliente)
            ->where('fechabateria', $fechaSeleccionada)
            ->get(['accionnombre'])
            ->pluck('accionnombre')
            ->toArray();
        $accionesNoRegistradas = array_filter($accionesPorFecha, function ($accion) use ($estadoMapeado, $fechaSeleccionada) {
                return empty($estadoMapeado[$accion][$fechaSeleccionada]);
            });
            
        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accion => $fecha) {
        $accionesPorFecha[$fecha][] = $accion;
        }
        
        $id = ClienteAuditoria::where('nombrecompleto', $clienteauditoria->nombrecompleto)->value('id');

        return view('admin.asociados.estadoprogramacionclienteauditoria', compact('accionesNoRegistradas','estadoMapeado','fechaSeleccionada', 'id','fechas','nombreclienteita','accionesDisponibles', 'clienteauditoria', 'id', 'accionesCliente', 'estadoRegistrados', 'fechasBateriaPorAccion', 'accionesPorFecha', 'accionesRegistradas'));
    }
    public function buscarprogramacionclientesauditoria(ClienteAuditoria $clienteauditoria, Request $request)
    {
        return $this->estadoprogramacionclienteauditoria($clienteauditoria, $request);
    }
    public function generarpdfprogramacionclienteauditoria(ClienteAuditoria $clienteauditoria, Request $request) 
    {
        $fechaSeleccionada = $request->get('buscarpor');

        $accionesDisponibles = Programacionsubcliente::where('clienteauditoriaid', $clienteauditoria->id)
                        ->when($fechaSeleccionada, function ($query) use ($fechaSeleccionada) {
                            return $query->where('fechabateria', $fechaSeleccionada);
                        })
                        ->get();
        
        $fechabateria = $fechaSeleccionada;
                    
        $pdf = PDF::loadView('admin.asociados.pdfprogramacionclienteauditoria', compact('fechabateria','clienteauditoria', 'accionesDisponibles'));
        $pdfName = 'Programación_' . $clienteauditoria->nombrecompleto;
        $pdfName .= '.pdf';
        
        return $pdf->download($pdfName);
    }
    public function guardarestadoprogramacionclienteauditoria(StoreEstadoprogramacionsubclienteRequest $request)
    {
        $accionesSeleccionadas = $request->input('accionesSeleccionadas', []);
        $fechaBateria = $request->input('fechabateria'); // Obtiene la fecha de batería del input oculto
    
        foreach ($accionesSeleccionadas as $accionNombre) {
            Estadoprogramacionsubcliente::create(
                $request->except('accionid') + [
                    'accionnombre' => $accionNombre,
                    'fechabateria' => $fechaBateria // Asegúrate de incluir la fecha aquí
                ]
            );
        }
    
        // Redirige a la vista con la fecha seleccionada
        return redirect()->route('admin.asociados.estadoprogramacionclienteauditoria', [
            'clienteauditoria' => $request->clienteauditoria,
            'buscarpor' => $fechaBateria // Incluye la fecha en la redirección
        ])->with('info', 'El estado se actualizó con éxito');
    }
//
//CREAR DOCUMENTACION DE CLIENTE AUDITORIA
    public function creardocumentacionclienteauditoria(ClienteAuditoria $clienteauditoria, Asociado $asociado)
    {
        $IDcliente = $clienteauditoria->id;

        $accionesCliente = Programacionsubcliente::where('clienteauditoriaid', $IDcliente)
            ->pluck('accionnombre')
            ->unique();

        $accionesRegistradasPorFecha = Documentacionsubcliente::where('clienteauditoriaid', $IDcliente)
            ->get(['accion', 'fechabateria'])
            ->groupBy('fechabateria');

        $accionesNoRegistradasPorFecha = Programacionsubcliente::where('clienteauditoriaid', $IDcliente)
            ->get(['accionnombre', 'fechabateria'])
            ->filter(function($accion) use ($accionesRegistradasPorFecha) {
                $fechabateria = $accion->fechabateria;
                $accionnombre = $accion->accionnombre;
                return !isset($accionesRegistradasPorFecha[$fechabateria]) || !in_array($accionnombre, $accionesRegistradasPorFecha[$fechabateria]->pluck('accion')->toArray());
            })
            ->groupBy('fechabateria');

        $accionesRegistradas = Documentacionsubcliente::whereIn('accion', $accionesCliente)
            ->where('clienteauditoriaid', $IDcliente)
            ->pluck('accion')
            ->toArray();

        $id = $clienteauditoria->nombrecompleto ? ClienteAuditoria::where('nombrecompleto', $clienteauditoria->nombrecompleto)->value('id') : null;

        $fechasBateriaPorAccion = Programacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clienteauditoriaid', $IDcliente)
            ->get(['accionnombre', 'fechabateria', 'proveedornombre'])
            ->groupBy('fechabateria');
        
        $accionesEnEstado = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clienteitaid', $IDcliente)
            ->pluck('accionnombre')
            ->toArray();
        $documentosRegistrados = Documentacionsubcliente::whereIn('accion', $accionesCliente)
            ->where('clienteauditoriaid', $IDcliente)
            ->pluck('accion')->toArray();

        $accionesPorFecha = [];

        foreach ($fechasBateriaPorAccion as $fecha => $acciones) {
            foreach ($acciones as $accion) {
                $accionesPorFecha[$fecha][] = $accion;
            }
        }
        

        $documentosRegistradosPorFecha = Documentacionsubcliente::where('clienteauditoriaid', $IDcliente)
            ->get(['accion', 'fechabateria'])
            ->groupBy('fechabateria');

        $accionesPorFecha2 = Programacionsubcliente::where('clienteauditoriaid', $IDcliente)
            ->get(['accionnombre', 'fechabateria'])
            ->groupBy('fechabateria');

        $accionesConEstadoPorFecha = [];
        foreach ($accionesPorFecha as $fecha => $acciones) {
            foreach ($acciones as $accion) {
                $registrado = isset($documentosRegistradosPorFecha[$fecha]) && 
                            in_array($accion->accionnombre, $documentosRegistradosPorFecha[$fecha]->pluck('accion')->toArray());

                $documento = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clienteauditoriaid', $IDcliente)
                                                        ->value('document') : null;

                $image = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clienteauditoriaid', $IDcliente)
                                                        ->value('image') : null;

                $image2 = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clienteauditoriaid', $IDcliente)
                                                        ->value('image2') : null;
                $id = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clienteauditoriaid', $IDcliente)
                                                        ->value('id') : null;

                $creacionregistro = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre) 
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clienteauditoriaid', $IDcliente)
                                                        ->value('created_at') : null;
                if ($creacionregistro) {
                    $creacionregistro = \Carbon\Carbon::parse($creacionregistro);
                    $creacionregistroFormatted = $creacionregistro->format('Y-m-d') . ' - ' . $creacionregistro->format('H:i:s');
                } else {
                    $creacionregistroFormatted = null;
                }

                $proveedor = $accion->proveedornombre;

                $accionesConEstadoPorFecha[$fecha][] = [
                    'id' => $id,
                    'accionnombre' => $accion->accionnombre,
                    'proveedornombre' => $proveedor,
                    'registrado' => $registrado,
                    'document' => $documento,
                    'image' => $image,
                    'image2' => $image2,
                    'creacionregistro' => $creacionregistroFormatted
                ];
            }
        }
        return view('admin.asociados.creardocumentacionclienteauditoria', compact('accionesConEstadoPorFecha','accionesRegistradasPorFecha','accionesNoRegistradasPorFecha','asociado', 'accionesEnEstado','id', 'clienteauditoria', 'accionesPorFecha', 'accionesRegistradas', 'fechasBateriaPorAccion', 'accionesCliente', 'documentosRegistrados'));
    }
    public function guardardocumentacionclienteauditoria(StoreDocumentacionsubclienteRequest $request, ClienteAuditoria $clienteauditoria)
    {
        $archivo_name = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $carpetaCliente = public_path("/documentacionclientesauditoria/{$clienteauditoria->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }

        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $carpetaCliente = public_path("/documentacionclientesauditoria/{$clienteauditoria->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name);
        }

        $image_name2 = null;
        if ($request->hasFile('picture2')) {
            $file = $request->file('picture2');
            $carpetaCliente = public_path("/documentacionclientesauditoria/{$clienteauditoria->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $image_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name2);
        }

        $nombrecliente = $request->input('nombrecompleto');
        $idcliente = $request->input('clienteauditoriaid');

        // Iterar sobre las acciones seleccionadas (enviadas como array)
        $accionesSeleccionadas = $request->input('acciones', []); // 'acciones' viene de los checkboxes
        
        foreach ($accionesSeleccionadas as $accionId) {
            $accionNombre = Programacionsubcliente::where('id', $accionId)->value('accionnombre');

            // Guardar cada acción con el mismo PDF e imágenes
            Documentacionsubcliente::create(
                $request->except('acciones') + [
                    'document' => $archivo_name,
                    'accion' => $accionId,  // Guardar el ID de la acción
                    'accionnombre' => $accionNombre,  // Guardar el nombre de la acción (opcional)
                    'clienteauditoriaid' => $idcliente,
                    'clienteauditorianombre' => $nombrecliente,
                    'image' => $image_name,
                    'image2' => $image_name2
                ]
            );
        }

        return redirect()->route('admin.asociados.creardocumentacionclienteauditoria', $request->clienteauditoria)->with('info', 'El documento se subió con éxito');
    }
    public function guardardocumentacionclienteauditoriadeproveedor(StoreDocumentacionsubclienteRequest $request, ClienteAuditoria $clienteauditoria)
    {
        $archivo_name = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            
            $carpetaCliente = public_path("/documentacionclientesauditoria/{$clienteauditoria->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }
        
        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $carpetaCliente = public_path("/documentacionclientesauditoria/{$clienteauditoria->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name);
        }

        $image_name2 = null;
        if ($request->hasFile('picture2')) {
            $file = $request->file('picture2');
            $carpetaCliente = public_path("/documentacionclientesauditoria/{$clienteauditoria->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $image_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name2);
        }

        $accionNombre = Programacionsubcliente::where('id', $request->accion)->value('accionnombre');
        $accion = $request->input('accion');
        $nombrecliente = $request->input('nombrecompleto');
        $documentacioncliente = Documentacionsubcliente::create(
            $request->except('accion') + [
                'document' => $archivo_name,
                'accion' => $accion,
                'clienteauditorianombre' => $nombrecliente,
                'image' => $image_name,
                'image2' => $image_name2
            ]
        );
        return redirect()->route('admin.informesfinales.reservasmedicas', $request->clienteauditoria)->with('info', 'El documento se subió con éxito');
    }
    public function listadodocumentacionclienteauditoria(ClienteAuditoria $clienteauditoria, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarpor');
        $fechas = Documentacionsubcliente::where('clienteauditoriaid', $clienteauditoria->id)
                                    ->pluck('fechabateria')
                                    ->unique();
        $documentacionclientes = collect();
        if ($fechaSeleccionada) {
            $documentacionclientes = Documentacionsubcliente::where('clienteauditoriaid', $clienteauditoria->id)
                                                    ->where('fechabateria', $fechaSeleccionada)
                                                    ->simplePaginate(1000);
        }
        /* $documentacionclientes = Documentacionsubcliente::where('clienteitanombre', $cliente->nombrecompleto)->get(); */

        $id = ClienteAuditoria::where('nombrecompleto', $clienteauditoria->nombrecompleto)->value('id');
        return view('admin.asociados.listadodocumentacionclienteauditoria', compact('id','fechas','fechaSeleccionada','clienteauditoria', 'documentacionclientes'));
    }
    public function buscardocumentoclienteauditoria(ClienteAuditoria $clienteauditoria, Request $request)
    {
        return $this->listadodocumentacionclienteauditoria($clienteauditoria, $request);
    }
    public function documentacionmultipleclienteauditoria(Request $request, Asociado $asociado, ClienteAuditoria $clienteauditoria)
    {
        $proveedor = $request->get('buscarpor');

        $clientesauditorias = Programacionsubcliente::where('proveedornombre', 'LIKE', "%$proveedor%")
            ->whereIn('accionnombre', function ($query) use ($proveedor) {
                $query->select('accionnombre')
                    ->from('estadoprogramacionsubclientes')
                    ->where('proveedornombre', 'LIKE', "%$proveedor%");
            })
            ->whereNotNull('clienteauditoriaid')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('documentacionsubclientes')
                    ->whereRaw('documentacionsubclientes.clienteauditoriaid = programacionsubclientes.clienteauditoriaid')
                    ->whereRaw('documentacionsubclientes.accion = programacionsubclientes.accionnombre')
                    ->whereRaw('documentacionsubclientes.fechabateria = programacionsubclientes.fechabateria');
            })
            ->orderBy('proveedornombre')
            ->simplePaginate(10000);

        return view('admin.asociados.documentacionmultipleclienteauditoria', compact('clienteauditoria', 'asociado', 'clientesauditorias'));
    }
    public function guardarhistoriamedicaauditoria(StoreDocumentacionsubclienteRequest $request, ClienteAuditoria $clienteauditoria) 
    {
        $archivo_name = null;
        $archivo_comprimido_name = null;
    
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            
            $carpetaCliente = public_path("/historiamedicaauditoria/{$clienteauditoria->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            
            // Nombre del archivo PDF
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
    
            // Crear un archivo ZIP para comprimir el PDF
            $zip = new \ZipArchive();
            $archivo_comprimido_name = 'HISTORIA_MEDICA_' . $clienteauditoria->nombrecompleto . '.zip';
            $zip_path = $carpetaCliente . '/' . $archivo_comprimido_name;
    
            if ($zip->open($zip_path, \ZipArchive::CREATE) === TRUE) {
                $zip->addFile($carpetaCliente . '/' . $archivo_name, $archivo_name);
                $zip->close();
            } else {
                return redirect()->back()->with('error', 'No se pudo crear el archivo comprimido');
            }
    
            // Eliminar el archivo PDF original después de comprimirlo
            unlink($carpetaCliente . '/' . $archivo_name);
    
            // Descomprimir el archivo en la carpeta `extracted`
            $extractPath = $carpetaCliente . '/extracted';
            if (!file_exists($extractPath)) {
                mkdir($extractPath, 0755, true);
            }
    
            $zip = new \ZipArchive();
            if ($zip->open($zip_path) === TRUE) {
                $zip->extractTo($extractPath);
                $zip->close();
    
                // Obtener el nombre del archivo descomprimido
                $archivosDescomprimidos = scandir($extractPath);
                $archivoPDFDescomprimido = null;
                foreach ($archivosDescomprimidos as $archivo) {
                    if ($archivo !== '.' && $archivo !== '..' && pathinfo($archivo, PATHINFO_EXTENSION) === 'pdf') {
                        $archivoPDFDescomprimido = $archivo;
                        break;
                    }
                }
    
                if ($archivoPDFDescomprimido === null) {
                    return redirect()->back()->with('error', 'No se encontró un archivo PDF en el ZIP');
                }
            } else {
                return redirect()->back()->with('error', 'No se pudo descomprimir el archivo');
            }
        }
    
        $accionNombre = Programacionsubcliente::where('id', $request->accion)->value('accionnombre');
        $accion = $request->input('accion');
        $clienteitaid = $request->input('usuarioid');
        $clienteitanombre = $request->input('usuarioregistro');
    
        $documentacioncliente = Documentacionsubcliente::create(
            $request->except('accion') + [
                'documentfirmado' => $archivo_comprimido_name,
                'document' => $archivoPDFDescomprimido, // Guarda el nombre del archivo PDF descomprimido
                'accion' => $accion,
                'usuarioregistro' => $clienteitanombre,
                'usuarioid' => $clienteitaid,
                'clienteauditoriaid' => $clienteauditoria->id,
                'clienteauditorianombre' => $clienteauditoria->nombrecompleto
            ]
        );
    
        return redirect()->route('admin.asociados.verclienteauditoria', $request->clienteauditoria)->with('info', 'El documento se subió con éxito');
    }
    public function verDocumentoauditoria($id)
    {
        $documentacion = Documentacionsubcliente::find($id);
        $carpetaCliente = public_path("/historiamedicaauditoria/{$documentacion->clienteauditoriaid}");
        $zip_path = $carpetaCliente . '/' . $documentacion->document;

        if (file_exists($zip_path)) {
            $zip = new \ZipArchive();
            if ($zip->open($zip_path) === TRUE) {
                $extract_path = $carpetaCliente . '/extracted/';
                if (!file_exists($extract_path)) {
                    mkdir($extract_path, 0755, true);
                }
                $zip->extractTo($extract_path);
                $zip->close();

                // Asumiendo que el ZIP contiene solo un archivo PDF
                $files = scandir($extract_path);
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                        $pdf_path = $extract_path . $file;
                        return response()->file($pdf_path);
                    }
                }
            }
        }

        return redirect()->back()->with('error', 'El documento no se encontró');
    }
//
//CREAR FORMULARIO DE CLIENTE AUDITORIA
    public function crearformularioclienteauditoria(ClienteAuditoria $clienteauditoria)
    {
        /* $generoCliente = $clienteauditoria->genero; */
        $userRole = auth()->user()->getRoleNames()->first(); 

        return view('admin.asociados.crearformularioclienteauditoria', compact('clienteauditoria', 'userRole'));
    }
    public function generarpdfclienteauditoria(ClienteAuditoria $clienteauditoria, Request $request) 
    {
        $pdf = PDF::loadView('admin.asociados.fichamedicaclienteauditoria', compact('clienteauditoria'));
        $pdfName = 'Fichamedica_'. $clienteauditoria->nombrecompleto;

        $pdfName .= '.pdf';
        $request->validate([
            'fechaatencion' => 'date',
            'antecedentespatologicos' => '',
            //IDENTIFICACION DE PELIGROS
            'preguntas.4.respuesta' => 'nullable|string','detpe4' => 'nullable|string',
            'preguntas.5.respuesta' => 'nullable|string','detpe5' => 'nullable|string',
            'preguntas.6.respuesta' => 'nullable|string','detpe6' => 'nullable|string',
            'preguntas.7.respuesta' => 'nullable|string','detpe7' => 'nullable|string',
            'preguntas.8.respuesta' => 'nullable|string','detpe8' => 'nullable|string',
            'preguntas.9.respuesta' => 'nullable|string','detpe9' => 'nullable|string',
            'preguntas.10.respuesta' => 'nullable|string','detpe10' => 'nullable|string',
            'preguntas.11.respuesta' => 'nullable|string','detpe11' => 'nullable|string',
            'otros' => '',
            //OFTALMOLOGIA
            'preguntas.001.respuesta' => 'nullable|string','hacecuanto001' => 'nullable|string','periodotipo001' => 'nullable|string',
            'preguntas.002.respuesta' => 'nullable|string','hacecuanto002' => 'nullable|string','periodotipo002' => 'nullable|string',
            'preguntas.003.respuesta' => 'nullable|string','hacecuanto003' => 'nullable|string','periodotipo003' => 'nullable|string',
            'preguntas.004.respuesta' => 'nullable|string','hacecuanto004' => 'nullable|string','periodotipo004' => 'nullable|string',
            'preguntas.005.respuesta' => 'nullable|string','hacecuanto005' => 'nullable|string','periodotipo005' => 'nullable|string',
            'preguntas.006.respuesta' => 'nullable|string','hacecuanto006' => 'nullable|string','periodotipo006' => 'nullable|string',
            //OTORRINOLARINGOLOGIA
            'preguntas.007.respuesta' => 'nullable|string','hacecuanto007' => 'nullable|string','periodotipo007' => 'nullable|string',
            'preguntas.008.respuesta' => 'nullable|string','hacecuanto008' => 'nullable|string','periodotipo008' => 'nullable|string',
            'preguntas.009.respuesta' => 'nullable|string','hacecuanto009' => 'nullable|string','periodotipo009' => 'nullable|string',
            'preguntas.010.respuesta' => 'nullable|string','hacecuanto010' => 'nullable|string','periodotipo010' => 'nullable|string',
            //NEUROLOGIA
            'preguntas.011.respuesta' => 'nullable|string','hacecuanto011' => 'nullable|string','periodotipo011' => 'nullable|string',
            'preguntas.012.respuesta' => 'nullable|string','hacecuanto012' => 'nullable|string','periodotipo012' => 'nullable|string',
            'preguntas.013.respuesta' => 'nullable|string','hacecuanto013' => 'nullable|string','periodotipo013' => 'nullable|string',
            'preguntas.014.respuesta' => 'nullable|string','hacecuanto014' => 'nullable|string','periodotipo014' => 'nullable|string',
            'preguntas.015.respuesta' => 'nullable|string','hacecuanto015' => 'nullable|string','periodotipo015' => 'nullable|string',
            'preguntas.016.respuesta' => 'nullable|string','hacecuanto016' => 'nullable|string','periodotipo016' => 'nullable|string',
            'preguntas.017.respuesta' => 'nullable|string','hacecuanto017' => 'nullable|string','periodotipo017' => 'nullable|string',
            'preguntas.018.respuesta' => 'nullable|string','hacecuanto018' => 'nullable|string','periodotipo018' => 'nullable|string',
            //CARDIOLOGIA
            'preguntas.019.respuesta' => 'nullable|string','hacecuanto019' => 'nullable|string','periodotipo019' => 'nullable|string',
            'preguntas.020.respuesta' => 'nullable|string','hacecuanto020' => 'nullable|string','periodotipo020' => 'nullable|string',
            'preguntas.021.respuesta' => 'nullable|string','hacecuanto021' => 'nullable|string','periodotipo021' => 'nullable|string',
            'preguntas.022.respuesta' => 'nullable|string','hacecuanto022' => 'nullable|string','periodotipo022' => 'nullable|string',
            'preguntas.023.respuesta' => 'nullable|string','hacecuanto023' => 'nullable|string','periodotipo023' => 'nullable|string',
            'preguntas.024.respuesta' => 'nullable|string','hacecuanto024' => 'nullable|string','periodotipo024' => 'nullable|string',
            'preguntas.025.respuesta' => 'nullable|string','hacecuanto025' => 'nullable|string','periodotipo025' => 'nullable|string',
            'preguntas.026.respuesta' => 'nullable|string','hacecuanto026' => 'nullable|string','periodotipo026' => 'nullable|string',
            //ENDICRONOLOGIA
            'preguntas.027.respuesta' => 'nullable|string','hacecuanto027' => 'nullable|string','periodotipo027' => 'nullable|string',
            'preguntas.028.respuesta' => 'nullable|string','hacecuanto028' => 'nullable|string','periodotipo028' => 'nullable|string',
            'preguntas.029.respuesta' => 'nullable|string','hacecuanto029' => 'nullable|string','periodotipo029' => 'nullable|string',
            'preguntas.030.respuesta' => 'nullable|string','hacecuanto030' => 'nullable|string','periodotipo030' => 'nullable|string',
            'preguntas.031.respuesta' => 'nullable|string','hacecuanto031' => 'nullable|string','periodotipo031' => 'nullable|string',
            //TRAUMATOLOGIA
            'preguntas.032.respuesta' => 'nullable|string','hacecuanto032' => 'nullable|string','periodotipo032' => 'nullable|string',
            'preguntas.033.respuesta' => 'nullable|string','hacecuanto033' => 'nullable|string','periodotipo033' => 'nullable|string',
            'preguntas.034.respuesta' => 'nullable|string','hacecuanto034' => 'nullable|string','periodotipo034' => 'nullable|string',
            'preguntas.035.respuesta' => 'nullable|string','hacecuanto035' => 'nullable|string','periodotipo035' => 'nullable|string',
            'preguntas.036.respuesta' => 'nullable|string','hacecuanto036' => 'nullable|string','periodotipo036' => 'nullable|string',
            'preguntas.037.respuesta' => 'nullable|string','hacecuanto037' => 'nullable|string','periodotipo037' => 'nullable|string',
            //NEUMOLOGIA
            'preguntas.038.respuesta' => 'nullable|string','hacecuanto038' => 'nullable|string','periodotipo038' => 'nullable|string',
            'preguntas.039.respuesta' => 'nullable|string','hacecuanto039' => 'nullable|string','periodotipo039' => 'nullable|string',
            'preguntas.040.respuesta' => 'nullable|string','hacecuanto040' => 'nullable|string','periodotipo040' => 'nullable|string',
            'preguntas.041.respuesta' => 'nullable|string','hacecuanto041' => 'nullable|string','periodotipo041' => 'nullable|string',
            'preguntas.042.respuesta' => 'nullable|string','hacecuanto042' => 'nullable|string','periodotipo042' => 'nullable|string',
            //GASTROENTEROLOGIA
            'preguntas.043.respuesta' => 'nullable|string','hacecuanto043' => 'nullable|string','periodotipo043' => 'nullable|string',
            'preguntas.044.respuesta' => 'nullable|string','hacecuanto044' => 'nullable|string','periodotipo044' => 'nullable|string',
            'preguntas.045.respuesta' => 'nullable|string','hacecuanto045' => 'nullable|string','periodotipo045' => 'nullable|string',
            'preguntas.046.respuesta' => 'nullable|string','hacecuanto046' => 'nullable|string','periodotipo046' => 'nullable|string',
            'preguntas.047.respuesta' => 'nullable|string','hacecuanto047' => 'nullable|string','periodotipo047' => 'nullable|string',
            'preguntas.048.respuesta' => 'nullable|string','hacecuanto048' => 'nullable|string','periodotipo048' => 'nullable|string',
            'preguntas.049.respuesta' => 'nullable|string','hacecuanto049' => 'nullable|string','periodotipo049' => 'nullable|string',
            'preguntas.050.respuesta' => 'nullable|string','hacecuanto050' => 'nullable|string','periodotipo050' => 'nullable|string',
            //UROLOGIA / NEFROLOGIA
            'preguntas.051.respuesta' => 'nullable|string','hacecuanto051' => 'nullable|string','periodotipo051' => 'nullable|string',
            'preguntas.052.respuesta' => 'nullable|string','hacecuanto052' => 'nullable|string','periodotipo052' => 'nullable|string',
            'preguntas.053.respuesta' => 'nullable|string','hacecuanto053' => 'nullable|string','periodotipo053' => 'nullable|string',
            'preguntas.054.respuesta' => 'nullable|string','hacecuanto054' => 'nullable|string','periodotipo054' => 'nullable|string',
            //DERMATOLOGIA
            'preguntas.055.respuesta' => 'nullable|string','hacecuanto055' => 'nullable|string','periodotipo055' => 'nullable|string',
            'preguntas.056.respuesta' => 'nullable|string','hacecuanto056' => 'nullable|string','periodotipo056' => 'nullable|string',
            'preguntas.057.respuesta' => 'nullable|string','hacecuanto057' => 'nullable|string','periodotipo057' => 'nullable|string',
            'preguntas.058.respuesta' => 'nullable|string','hacecuanto058' => 'nullable|string','periodotipo058' => 'nullable|string',
            'preguntas.059.respuesta' => 'nullable|string','hacecuanto059' => 'nullable|string','periodotipo059' => 'nullable|string',
            'preguntas.060.respuesta' => 'nullable|string','hacecuanto060' => 'nullable|string','periodotipo060' => 'nullable|string',
            //CIRUGIA VASCULAR
            'preguntas.061.respuesta' => 'nullable|string','hacecuanto061' => 'nullable|string','periodotipo061' => 'nullable|string',
            'preguntas.062.respuesta' => 'nullable|string','hacecuanto062' => 'nullable|string','periodotipo062' => 'nullable|string',
            'preguntas.063.respuesta' => 'nullable|string','hacecuanto063' => 'nullable|string','periodotipo063' => 'nullable|string',
            //REUMATOLOGIA
            'preguntas.064.respuesta' => 'nullable|string','hacecuanto064' => 'nullable|string','periodotipo064' => 'nullable|string',
            'preguntas.065.respuesta' => 'nullable|string','hacecuanto065' => 'nullable|string','periodotipo065' => 'nullable|string',
            'preguntas.066.respuesta' => 'nullable|string','hacecuanto066' => 'nullable|string','periodotipo066' => 'nullable|string',
            'preguntas.067.respuesta' => 'nullable|string','hacecuanto067' => 'nullable|string','periodotipo067' => 'nullable|string',
            'preguntas.068.respuesta' => 'nullable|string','hacecuanto068' => 'nullable|string','periodotipo068' => 'nullable|string',
            'preguntas.069.respuesta' => 'nullable|string','hacecuanto069' => 'nullable|string','periodotipo069' => 'nullable|string',
            'preguntas.070.respuesta' => 'nullable|string','hacecuanto070' => 'nullable|string','periodotipo070' => 'nullable|string',
            'preguntas.071.respuesta' => 'nullable|string','hacecuanto071' => 'nullable|string','periodotipo071' => 'nullable|string',
            //ONCOLOGIA
            'preguntas.072.respuesta' => 'nullable|string','hacecuanto072' => 'nullable|string','periodotipo072' => 'nullable|string',
            //CIRUGIA GENERAL
            'preguntas.073.respuesta' => 'nullable|string','hacecuanto073' => 'nullable|string','periodotipo073' => 'nullable|string',
            'preguntas.074.respuesta' => 'nullable|string','hacecuanto074' => 'nullable|string','periodotipo074' => 'nullable|string',
            //GINECOLOGIA
            'preguntas.075.respuesta' => 'nullable|string','hacecuanto075' => 'nullable|string','periodotipo075' => 'nullable|string',
            'preguntas.076.respuesta' => 'nullable|string','hacecuanto076' => 'nullable|string','periodotipo076' => 'nullable|string',
            'preguntas.077.respuesta' => 'nullable|string','hacecuanto077' => 'nullable|string','periodotipo077' => 'nullable|string',
            'preguntas.078.respuesta' => 'nullable|string','hacecuanto078' => 'nullable|string','periodotipo078' => 'nullable|string',
            'preguntas.079.respuesta' => 'nullable|string','hacecuanto079' => 'nullable|string','periodotipo079' => 'nullable|string',
            //ANTECEDENTES PATOLOGICOS ADICIONALES
            'fracturas' => '','alergias' => '','transfusiones' => '','intoxicaciones' => '','enfermedadessexual' => '','alteracionvision' => '','alteracionoido' => '','enfermedaddigestivo' => '','enfermedadurogenital' => '',
            //ANTECEDENTES PERSONALES NO PATOLOGICOS
            //CIGARILLOS
            'estadocigarrillos' => '','suspcigarillos' => '','tiemposuspcigarillos' => '','freccigarillos' => '','tiempofreccigarillos' => '','consumocigarillos' => '','tiempoconscigarillos' => '','numerocigarrillos' => '',
            //ALCOHOL
            'estadoalcoholismo' => '','suspensionalcohol' => '','tiemposuspalcohol' => '','frecuenciaalcohol' => '','tiempofrecalcohol' => '','consumoalcohol' => '','tiempoconsalcohol' => '','tipobebida' => '',
            //COCA
            'estadococa' => '','consumococa' => '','tiempoconscoca' => '','frecuenciacoca' => '','tiempofreccoca' => '',
            //MEDICAMENTOS
            'estadomedicamento' => '','cualesmedicamentos' => '',
            //ADICIONAL
            'vivienda' => '','alimentacion' => '','drogas' => '','deporte' => '','catarsis' => '','diuresis' => '','combe' => '',
            //ANTECEDENTES QUIRURGICOS
            'preguntas.100.antecedente' => 'nullable|string','preguntas.100.periodotiempo' => 'nullable|string',
            'preguntas.200.antecedente' => 'nullable|string','preguntas.200.periodotiempo' => 'nullable|string',
            'preguntas.300.antecedente' => 'nullable|string','preguntas.300.periodotiempo' => 'nullable|string',
            //ANTECEDENTES TRAUMATICOS
            'preguntas.1000.antecedente' => 'nullable|string','preguntas.1000.periodotiempo' => 'nullable|string',
            'preguntas.2000.antecedente' => 'nullable|string','preguntas.2000.periodotiempo' => 'nullable|string',
            'preguntas.3000.antecedente' => 'nullable|string','preguntas.3000.periodotiempo' => 'nullable|string',
            //ANTECEDENTES FAMILIARES
            'estadosaludpadre' => '','edadvivopadre' => '','edadfallecidopadre' => '','causafallecidopadre' => '','enfermedadespadre' => '',
            'estadosaludmadre' => '','edadvivomadre' => '','edadfallecemadre' => '','causafallecemadre' => '','enfermedadesmadre' => '',
            'cantidadhermanos' => '','hermanovivo' => '','hermanofallece' => '','caudafallecehermano' => '','enfermedadeshermano' => '',
            'estadosaludesposo' => '','edadvivoesposo' => '','edadfalleceesposo' => '','causafalleceesposo' => '','enfermedadesesposo' => '',
            'cantidadhijos' => '','hijosvivo' => '','hijosfallece' => '','causafallecehijos' => '','enfermedadeshijos' => '',
            //ANTECENTES FAMILIARES ADICIONALES
            'preguntas.30.respuesta' => 'nullable|string','hacecuanto30' => 'nullable|string','periodotipo30' => 'nullable|string',
            'preguntas.31.respuesta' => 'nullable|string','hacecuanto31' => 'nullable|string','periodotipo31' => 'nullable|string',
            'preguntas.32.respuesta' => 'nullable|string','hacecuanto32' => 'nullable|string','periodotipo32' => 'nullable|string',
            'preguntas.33.respuesta' => 'nullable|string','hacecuanto33' => 'nullable|string','periodotipo33' => 'nullable|string',
            'preguntas.34.respuesta' => 'nullable|string','hacecuanto34' => 'nullable|string','periodotipo34' => 'nullable|string',
            'preguntas.35.respuesta' => 'nullable|string','hacecuanto35' => 'nullable|string','periodotipo35' => 'nullable|string',
            'preguntas.36.respuesta' => 'nullable|string','hacecuanto36' => 'nullable|string','periodotipo36' => 'nullable|string',
            'preguntas.37.respuesta' => 'nullable|string','hacecuanto37' => 'nullable|string','periodotipo37' => 'nullable|string',
            'preguntas.38.respuesta' => 'nullable|string','hacecuanto38' => 'nullable|string','periodotipo38' => 'nullable|string',
            'preguntas.39.respuesta' => 'nullable|string','hacecuanto39' => 'nullable|string','periodotipo39' => 'nullable|string',
            'preguntas.40.respuesta' => 'nullable|string','hacecuanto40' => 'nullable|string','periodotipo40' => 'nullable|string',
            'preguntas.41.respuesta' => 'nullable|string','hacecuanto41' => 'nullable|string','periodotipo41' => 'nullable|string',
            //ANTECEDENTES LABORALES
            'fechainicioatclab' => '','fechafinalatclab' => '',
            'preguntas.1.carac' => 'nullable|string','preguntas.1.denun' => 'nullable|string','preguntas.1.aten' => 'nullable|string',
            'preguntas.2.carac' => 'nullable|string','preguntas.2.denun' => 'nullable|string','preguntas.2.aten' => 'nullable|string',
            'preguntas.3.carac' => 'nullable|string','preguntas.3.denun' => 'nullable|string','preguntas.3.aten' => 'nullable|string',
            //HISTORIA DE LA ENFERMEDAD ACTUAL
            'historiaenfermedad' => '',
            //EXAMEN FISICO
            'examenfisicogeneral' => '','llenadocapilar' => '','lateralidad' => '',
            'pulso' => '','satO2' => '','frespiracion' => '','temperatura' => '','presionarterial' => '',
            'agudezavisual' => '','usalentes' => '',
            'peso' => '','estatura' => '','imc' => '',
            //EXAMEN FISICO SEGMENTADO
            'exficabeza' => '','exfiojos' => '','exfinariz' => '','exfioidos' => '','exfiboca' => '','exficuello' => '','exfitorax' => '','exficorazon' => '','exfipulmones' => '',
            'exfiabdomen' => '','exfiextremidadesmmss' => '','exfiextremidadesmmii' => '','exfineurologico' => '','exfivestibulocereboloso' => '','exfimarcha' => '','exficraneoycolumna' => '','exfiexploracionneuro' => '',
        ]);

        //IDENTIFICACION DE PELIGROS
        Session::put('fechaatencion', $request->fechaatencion);
        Session::put('antecedentespatologicos', $request->antecedentespatologicos);
        Session::put('peligrosfisicos', $request->input('preguntas.4.respuesta'));
        Session::put('descripcionpeligrosfisicos', $request->input('detpe4'));
        Session::put('peligrosquimicos', $request->input('preguntas.5.respuesta'));
        Session::put('descripcionpeligrosquimicos', $request->input('detpe5'));
        Session::put('peligrosergonomicos', $request->input('preguntas.6.respuesta'));
        Session::put('descripcionpeligrosergonomicos', $request->input('detpe6'));
        Session::put('peligrosepps', $request->input('preguntas.7.respuesta'));
        Session::put('descripcionpeligrosepps', $request->input('detpe7'));
        Session::put('peligrosbiologicos', $request->input('preguntas.8.respuesta'));
        Session::put('descripcionpeligrosbiologicos', $request->input('detpe8'));
        Session::put('peligrosmecanicos', $request->input('preguntas.9.respuesta'));
        Session::put('descripcionpeligrosmecanicos', $request->input('detpe9'));
        Session::put('peligrosambientales', $request->input('preguntas.10.respuesta'));
        Session::put('descripcionpeligrosambientales', $request->input('detpe10'));
        Session::put('peligrospsicosociales', $request->input('preguntas.11.respuesta'));
        Session::put('descripcionpeligrospsicosociales', $request->input('detpe11'));
        Session::put('otros', $request->otros);
        //OFTALMOLOGIA
        Session::put('cefalea', $request->input('preguntas.001.respuesta'));
        Session::put('hacecuanto001', $request->input('hacecuanto001'));
        Session::put('periodotipo001', $request->input('periodotipo001'));
        Session::put('defectovisual', $request->input('preguntas.002.respuesta'));
        Session::put('hacecuanto002', $request->input('hacecuanto002'));
        Session::put('periodotipo002', $request->input('periodotipo002'));
        Session::put('irritacionocular', $request->input('preguntas.003.respuesta'));
        Session::put('hacecuanto003', $request->input('hacecuanto003'));
        Session::put('periodotipo003', $request->input('periodotipo003'));
        Session::put('sequedadocular', $request->input('preguntas.004.respuesta'));
        Session::put('hacecuanto004', $request->input('hacecuanto004'));
        Session::put('periodotipo004', $request->input('periodotipo004'));
        Session::put('lagrimeo', $request->input('preguntas.005.respuesta'));
        Session::put('hacecuanto005', $request->input('hacecuanto005'));
        Session::put('periodotipo005', $request->input('periodotipo005'));
        Session::put('visionborrosa', $request->input('preguntas.006.respuesta'));
        Session::put('hacecuanto006', $request->input('hacecuanto006'));
        Session::put('periodotipo006', $request->input('periodotipo006'));
        //OTORRINOLARINGOLOGIA
        Session::put('hipoacuasia', $request->input('preguntas.007.respuesta'));
        Session::put('hacecuanto007', $request->input('hacecuanto007'));
        Session::put('periodotipo007', $request->input('periodotipo007'));
        Session::put('otitismedia', $request->input('preguntas.008.respuesta'));
        Session::put('hacecuanto008', $request->input('hacecuanto008'));
        Session::put('periodotipo008', $request->input('periodotipo008'));
        Session::put('sinusitis', $request->input('preguntas.009.respuesta'));
        Session::put('hacecuanto009', $request->input('hacecuanto009'));
        Session::put('periodotipo009', $request->input('periodotipo009'));
        Session::put('tinitus', $request->input('preguntas.010.respuesta'));
        Session::put('hacecuanto010', $request->input('hacecuanto010'));
        Session::put('periodotipo010', $request->input('periodotipo010'));
        //NEUROLOGIA
        Session::put('convulsiones', $request->input('preguntas.011.respuesta'));
        Session::put('hacecuanto011', $request->input('hacecuanto011'));
        Session::put('periodotipo011', $request->input('periodotipo011'));
        Session::put('epilepsia', $request->input('preguntas.012.respuesta'));
        Session::put('hacecuanto012', $request->input('hacecuanto012'));
        Session::put('periodotipo012', $request->input('periodotipo012'));
        Session::put('lumbalgia', $request->input('preguntas.013.respuesta'));
        Session::put('hacecuanto013', $request->input('hacecuanto013'));
        Session::put('periodotipo013', $request->input('periodotipo013'));
        Session::put('neuropatia', $request->input('preguntas.014.respuesta'));
        Session::put('hacecuanto014', $request->input('hacecuanto014'));
        Session::put('periodotipo014', $request->input('periodotipo014'));
        Session::put('acv', $request->input('preguntas.015.respuesta'));
        Session::put('hacecuanto015', $request->input('hacecuanto015'));
        Session::put('periodotipo015', $request->input('periodotipo015'));
        Session::put('cefaleaneurologia', $request->input('preguntas.016.respuesta'));
        Session::put('hacecuanto016', $request->input('hacecuanto016'));
        Session::put('periodotipo016', $request->input('periodotipo016'));
        Session::put('disformiamuscular', $request->input('preguntas.017.respuesta'));
        Session::put('hacecuanto017', $request->input('hacecuanto017'));
        Session::put('periodotipo017', $request->input('periodotipo017'));
        Session::put('lesionmedulaespinal', $request->input('preguntas.018.respuesta'));
        Session::put('hacecuanto018', $request->input('hacecuanto018'));
        Session::put('periodotipo018', $request->input('periodotipo018'));
        //CARDIOLOGIA
        Session::put('hta', $request->input('preguntas.019.respuesta'));
        Session::put('hacecuanto019', $request->input('hacecuanto019'));
        Session::put('periodotipo019', $request->input('periodotipo019'));
        Session::put('arritmia', $request->input('preguntas.020.respuesta'));
        Session::put('hacecuanto020', $request->input('hacecuanto020'));
        Session::put('periodotipo020', $request->input('periodotipo020'));
        Session::put('chagas', $request->input('preguntas.021.respuesta'));
        Session::put('hacecuanto021', $request->input('hacecuanto021'));
        Session::put('periodotipo021', $request->input('periodotipo021'));
        Session::put('taquicardia', $request->input('preguntas.022.respuesta'));
        Session::put('hacecuanto022', $request->input('hacecuanto022'));
        Session::put('periodotipo022', $request->input('periodotipo022'));
        Session::put('bradicardia', $request->input('preguntas.023.respuesta'));
        Session::put('hacecuanto023', $request->input('hacecuanto023'));
        Session::put('periodotipo023', $request->input('periodotipo023'));
        Session::put('bloqueoderama', $request->input('preguntas.024.respuesta'));
        Session::put('hacecuanto024', $request->input('hacecuanto024'));
        Session::put('periodotipo024', $request->input('periodotipo024'));
        Session::put('stentcoronario', $request->input('preguntas.025.respuesta'));
        Session::put('hacecuanto025', $request->input('hacecuanto025'));
        Session::put('periodotipo025', $request->input('periodotipo025'));
        Session::put('marcapaso', $request->input('preguntas.026.respuesta'));
        Session::put('hacecuanto026', $request->input('hacecuanto026'));
        Session::put('periodotipo026', $request->input('periodotipo026'));
        //ENDICRONOLOGIA
        Session::put('dmt2', $request->input('preguntas.027.respuesta'));
        Session::put('hacecuanto027', $request->input('hacecuanto027'));
        Session::put('periodotipo027', $request->input('periodotipo027'));
        Session::put('lupuseritematoso', $request->input('preguntas.028.respuesta'));
        Session::put('hacecuanto028', $request->input('hacecuanto028'));
        Session::put('periodotipo028', $request->input('periodotipo028'));
        Session::put('colesterolelevado', $request->input('preguntas.029.respuesta'));
        Session::put('hacecuanto029', $request->input('hacecuanto029'));
        Session::put('periodotipo029', $request->input('periodotipo029'));
        Session::put('hipotiroidismo', $request->input('preguntas.030.respuesta'));
        Session::put('hacecuanto030', $request->input('hacecuanto030'));
        Session::put('periodotipo030', $request->input('periodotipo030'));
        Session::put('hipertiroidismo', $request->input('preguntas.031.respuesta'));
        Session::put('hacecuanto031', $request->input('hacecuanto031'));
        Session::put('periodotipo031', $request->input('periodotipo031'));
        //TRAUMATOLOGIA
        Session::put('artritis', $request->input('preguntas.032.respuesta'));
        Session::put('hacecuanto032', $request->input('hacecuanto032'));
        Session::put('periodotipo032', $request->input('periodotipo032'));
        Session::put('doloresarticulares', $request->input('preguntas.033.respuesta'));
        Session::put('hacecuanto033', $request->input('hacecuanto033'));
        Session::put('periodotipo033', $request->input('periodotipo033'));
        Session::put('lumbalgia', $request->input('preguntas.034.respuesta'));
        Session::put('hacecuanto034', $request->input('hacecuanto034'));
        Session::put('periodotipo034', $request->input('periodotipo034'));
        Session::put('cervicalgia', $request->input('preguntas.035.respuesta'));
        Session::put('hacecuanto035', $request->input('hacecuanto035'));
        Session::put('periodotipo035', $request->input('periodotipo035'));
        Session::put('dorsalgia', $request->input('preguntas.036.respuesta'));
        Session::put('hacecuanto036', $request->input('hacecuanto036'));
        Session::put('periodotipo036', $request->input('periodotipo036'));
        Session::put('silicosis', $request->input('preguntas.037.respuesta'));
        Session::put('hacecuanto037', $request->input('hacecuanto037'));
        Session::put('periodotipo037', $request->input('periodotipo037'));
        //NEUMOLOGIA
        Session::put('bronquitis', $request->input('preguntas.038.respuesta'));
        Session::put('hacecuanto038', $request->input('hacecuanto038'));
        Session::put('periodotipo038', $request->input('periodotipo038'));
        Session::put('asma', $request->input('preguntas.039.respuesta'));
        Session::put('hacecuanto039', $request->input('hacecuanto039'));
        Session::put('periodotipo039', $request->input('periodotipo039'));
        Session::put('tuberculosis', $request->input('preguntas.040.respuesta'));
        Session::put('hacecuanto040', $request->input('hacecuanto040'));
        Session::put('periodotipo040', $request->input('periodotipo040'));
        Session::put('epoc', $request->input('preguntas.041.respuesta'));
        Session::put('hacecuanto041', $request->input('hacecuanto041'));
        Session::put('periodotipo041', $request->input('periodotipo041'));
        Session::put('enfisemapulmonar', $request->input('preguntas.042.respuesta'));
        Session::put('hacecuanto042', $request->input('hacecuanto042'));
        Session::put('periodotipo042', $request->input('periodotipo042'));
        //GASTROENTEROLOGIA
        Session::put('gastritis', $request->input('preguntas.043.respuesta'));
        Session::put('hacecuanto043', $request->input('hacecuanto043'));
        Session::put('periodotipo043', $request->input('periodotipo043'));
        Session::put('enfacidopeptica', $request->input('preguntas.044.respuesta'));
        Session::put('hacecuanto044', $request->input('hacecuanto044'));
        Session::put('periodotipo044', $request->input('periodotipo044'));
        Session::put('colonirritable', $request->input('preguntas.045.respuesta'));
        Session::put('hacecuanto045', $request->input('hacecuanto045'));
        Session::put('periodotipo045', $request->input('periodotipo045'));
        Session::put('cololetiasis', $request->input('preguntas.046.respuesta'));
        Session::put('hacecuanto046', $request->input('hacecuanto046'));
        Session::put('periodotipo046', $request->input('periodotipo046'));
        Session::put('distencion', $request->input('preguntas.047.respuesta'));
        Session::put('hacecuanto047', $request->input('hacecuanto047'));
        Session::put('periodotipo047', $request->input('periodotipo047'));
        Session::put('calculosbiliares', $request->input('preguntas.048.respuesta'));
        Session::put('hacecuanto048', $request->input('hacecuanto048'));
        Session::put('periodotipo048', $request->input('periodotipo048'));
        Session::put('ulceraintestinal', $request->input('preguntas.049.respuesta'));
        Session::put('hacecuanto049', $request->input('hacecuanto049'));
        Session::put('periodotipo049', $request->input('periodotipo049'));
        Session::put('hepatitis', $request->input('preguntas.050.respuesta'));
        Session::put('hacecuanto050', $request->input('hacecuanto050'));
        Session::put('periodotipo050', $request->input('periodotipo050'));
        //UROLOGIA / NEFROLOGIA
        Session::put('urolitiasis', $request->input('preguntas.051.respuesta'));
        Session::put('hacecuanto051', $request->input('hacecuanto051'));
        Session::put('periodotipo051', $request->input('periodotipo051'));
        Session::put('infeccionurinaria', $request->input('preguntas.052.respuesta'));
        Session::put('hacecuanto052', $request->input('hacecuanto052'));
        Session::put('periodotipo052', $request->input('periodotipo052'));
        Session::put('prostatitis', $request->input('preguntas.053.respuesta'));
        Session::put('hacecuanto053', $request->input('hacecuanto053'));
        Session::put('periodotipo053', $request->input('periodotipo053'));
        Session::put('varicocele', $request->input('preguntas.054.respuesta'));
        Session::put('hacecuanto054', $request->input('hacecuanto054'));
        Session::put('periodotipo054', $request->input('periodotipo054'));
        //DERMATOLOGIA
        Session::put('dermatitis', $request->input('preguntas.055.respuesta'));
        Session::put('hacecuanto055', $request->input('hacecuanto055'));
        Session::put('periodotipo055', $request->input('periodotipo055'));
        Session::put('lupuseritematosoder', $request->input('preguntas.056.respuesta'));
        Session::put('hacecuanto056', $request->input('hacecuanto056'));
        Session::put('periodotipo056', $request->input('periodotipo056'));
        Session::put('vitiligo', $request->input('preguntas.057.respuesta'));
        Session::put('hacecuanto057', $request->input('hacecuanto057'));
        Session::put('periodotipo057', $request->input('periodotipo057'));
        Session::put('eccema', $request->input('preguntas.058.respuesta'));
        Session::put('hacecuanto058', $request->input('hacecuanto058'));
        Session::put('periodotipo058', $request->input('periodotipo058'));
        Session::put('impetigo', $request->input('preguntas.059.respuesta'));
        Session::put('hacecuanto059', $request->input('hacecuanto059'));
        Session::put('periodotipo059', $request->input('periodotipo059'));
        Session::put('psoriasis', $request->input('preguntas.060.respuesta'));
        Session::put('hacecuanto060', $request->input('hacecuanto060'));
        Session::put('periodotipo060', $request->input('periodotipo060'));
        //CIRUGIA VASCULAR
        Session::put('varicesenpiernas', $request->input('preguntas.061.respuesta'));
        Session::put('hacecuanto061', $request->input('hacecuanto061'));
        Session::put('periodotipo061', $request->input('periodotipo061'));
        Session::put('celulitisenmmii', $request->input('preguntas.062.respuesta'));
        Session::put('hacecuanto062', $request->input('hacecuanto062'));
        Session::put('periodotipo062', $request->input('periodotipo062'));
        Session::put('trombosis', $request->input('preguntas.063.respuesta'));
        Session::put('hacecuanto063', $request->input('hacecuanto063'));
        Session::put('periodotipo063', $request->input('periodotipo063'));
        //REUMATOLOGIA
        Session::put('artritisreumatoidea', $request->input('preguntas.064.respuesta'));
        Session::put('hacecuanto064', $request->input('hacecuanto064'));
        Session::put('periodotipo064', $request->input('periodotipo064'));
        Session::put('artrosisreu', $request->input('preguntas.065.respuesta'));
        Session::put('hacecuanto065', $request->input('hacecuanto065'));
        Session::put('periodotipo065', $request->input('periodotipo065'));
        Session::put('psoriasisreu', $request->input('preguntas.066.respuesta'));
        Session::put('hacecuanto066', $request->input('hacecuanto066'));
        Session::put('periodotipo066', $request->input('periodotipo066'));
        Session::put('lupuseritematosoreu', $request->input('preguntas.067.respuesta'));
        Session::put('hacecuanto067', $request->input('hacecuanto067'));
        Session::put('periodotipo067', $request->input('periodotipo067'));
        Session::put('gota', $request->input('preguntas.068.respuesta'));
        Session::put('hacecuanto068', $request->input('hacecuanto068'));
        Session::put('periodotipo068', $request->input('periodotipo068'));
        Session::put('espondilitisanquilosante', $request->input('preguntas.069.respuesta'));
        Session::put('hacecuanto069', $request->input('hacecuanto069'));
        Session::put('periodotipo069', $request->input('periodotipo069'));
        Session::put('fibromialgia', $request->input('preguntas.070.respuesta'));
        Session::put('hacecuanto070', $request->input('hacecuanto070'));
        Session::put('periodotipo070', $request->input('periodotipo070'));
        Session::put('reumatismo', $request->input('preguntas.071.respuesta'));
        Session::put('hacecuanto071', $request->input('hacecuanto071'));
        Session::put('periodotipo071', $request->input('periodotipo071'));
        //ONCOLOGIA
        Session::put('cancer', $request->input('preguntas.072.respuesta'));
        Session::put('hacecuanto072', $request->input('hacecuanto072'));
        Session::put('periodotipo072', $request->input('periodotipo072'));
        //CIRUGIA GENERAL
        Session::put('herniainguinal', $request->input('preguntas.073.respuesta'));
        Session::put('hacecuanto073', $request->input('hacecuanto073'));
        Session::put('periodotipo073', $request->input('periodotipo073'));
        Session::put('herniaumbilical', $request->input('preguntas.074.respuesta'));
        Session::put('hacecuanto074', $request->input('hacecuanto074'));
        Session::put('periodotipo074', $request->input('periodotipo074'));
        //GINECOLOGIA
        Session::put('endometriosis', $request->input('preguntas.075.respuesta'));
        Session::put('hacecuanto075', $request->input('hacecuanto075'));
        Session::put('periodotipo075', $request->input('periodotipo075'));
        Session::put('miomasuterinos', $request->input('preguntas.076.respuesta'));
        Session::put('hacecuanto076', $request->input('hacecuanto076'));
        Session::put('periodotipo076', $request->input('periodotipo076'));
        Session::put('poliposuterinos', $request->input('preguntas.077.respuesta'));
        Session::put('hacecuanto077', $request->input('hacecuanto077'));
        Session::put('periodotipo077', $request->input('periodotipo077'));
        Session::put('quistesdeovarios', $request->input('preguntas.078.respuesta'));
        Session::put('hacecuanto078', $request->input('hacecuanto078'));
        Session::put('periodotipo078', $request->input('periodotipo078'));
        Session::put('prolapsogenital', $request->input('preguntas.079.respuesta'));
        Session::put('hacecuanto079', $request->input('hacecuanto079'));
        Session::put('periodotipo079', $request->input('periodotipo079'));
        //ANTECEDENTES PATOLOGICOS ADICIONALES
        Session::put('fracturas', $request->fracturas);
        Session::put('alergias', $request->alergias);
        Session::put('transfusiones', $request->transfusiones);
        Session::put('intoxicaciones', $request->intoxicaciones);
        Session::put('enfermedadessexual', $request->enfermedadessexual);
        Session::put('alteracionvision', $request->alteracionvision);
        Session::put('alteracionoido', $request->alteracionoido);
        Session::put('enfermedaddigestivo', $request->enfermedaddigestivo);
        Session::put('enfermedadurogenital', $request->enfermedadurogenital);
        //ANTECEDENTES PERSONALES NO PATOLOGICOS
        //CIGARRILLOS
        Session::put('estadocigarrillos', $request->estadocigarrillos);
        Session::put('suspcigarillos', $request->suspcigarillos);
        Session::put('tiemposuspcigarillos', $request->tiemposuspcigarillos);
        Session::put('freccigarillos', $request->freccigarillos);
        Session::put('tiempofreccigarillos', $request->tiempofreccigarillos);
        Session::put('consumocigarillos', $request->consumocigarillos);
        Session::put('tiempoconscigarillos', $request->tiempoconscigarillos);
        Session::put('numerocigarrillos', $request->numerocigarrillos);
        //ALCOHOL
        Session::put('estadoalcoholismo', $request->estadoalcoholismo);
        Session::put('suspensionalcohol', $request->suspensionalcohol);
        Session::put('tiemposuspalcohol', $request->tiemposuspalcohol);
        Session::put('frecuenciaalcohol', $request->frecuenciaalcohol);
        Session::put('tiempofrecalcohol', $request->tiempofrecalcohol);
        Session::put('consumoalcohol', $request->consumoalcohol);
        Session::put('tiempoconsalcohol', $request->tiempoconsalcohol);
        Session::put('tipobebida', $request->tipobebida);
        //COCA
        Session::put('estadococa', $request->estadococa);
        Session::put('consumococa', $request->consumococa);
        Session::put('tiempoconscoca', $request->tiempoconscoca);
        Session::put('frecuenciacoca', $request->frecuenciacoca);
        Session::put('tiempofreccoca', $request->tiempofreccoca);
        //MEDICAMENTOS
        Session::put('estadomedicamento', $request->estadomedicamento);
        Session::put('cualesmedicamentos', $request->cualesmedicamentos);
        //ADICIONAL
        Session::put('vivienda', $request->vivienda);
        Session::put('alimentacion', $request->alimentacion);
        Session::put('drogas', $request->drogas);
        Session::put('deporte', $request->deporte);
        Session::put('catarsis', $request->catarsis);
        Session::put('diuresis', $request->diuresis);
        Session::put('combe', $request->combe);
        //ANTECEDENTES QUIRUGICOS
        Session::put('atcquirurgico1', $request->input('preguntas.100.antecedente'));
        Session::put('atcperiodo1', $request->input('preguntas.100.periodotiempo'));
        Session::put('atcquirurgico2', $request->input('preguntas.200.antecedente'));
        Session::put('atcperiodo2', $request->input('preguntas.200.periodotiempo'));
        Session::put('atcquirurgico3', $request->input('preguntas.300.antecedente'));
        Session::put('atcperiodo3', $request->input('preguntas.300.periodotiempo'));
        //ANTECEDENTES TRAUMATICOS
        Session::put('atctrau1', $request->input('preguntas.100.antecedente'));
        Session::put('atctrauperiodo1', $request->input('preguntas.1000.periodotiempo'));
        Session::put('atctrau2', $request->input('preguntas.200.antecedente'));
        Session::put('atctrauperiodo2', $request->input('preguntas.2000.periodotiempo'));
        Session::put('atctrau3', $request->input('preguntas.300.antecedente'));
        Session::put('atctrauperiodo3', $request->input('preguntas.3000.periodotiempo'));
        //ANTECEDENTES FAMILIARES
        Session::put('estadosaludpadre', $request->estadosaludpadre);
        Session::put('edadvivopadre', $request->edadvivopadre);
        Session::put('edadfallecidopadre', $request->edadfallecidopadre);
        Session::put('causafallecidopadre', $request->causafallecidopadre);
        Session::put('enfermedadespadre', $request->enfermedadespadre);
        Session::put('estadosaludmadre', $request->estadosaludmadre);
        Session::put('edadvivomadre', $request->edadvivomadre);
        Session::put('edadfallecemadre', $request->edadfallecemadre);
        Session::put('causafallecemadre', $request->causafallecemadre);
        Session::put('enfermedadesmadre', $request->enfermedadesmadre);
        Session::put('cantidadhermanos', $request->cantidadhermanos);
        Session::put('hermanovivo', $request->hermanovivo);
        Session::put('hermanofallece', $request->hermanofallece);
        Session::put('caudafallecehermano', $request->caudafallecehermano);
        Session::put('enfermedadeshermano', $request->enfermedadeshermano);
        Session::put('estadosaludesposo', $request->estadosaludesposo);
        Session::put('edadvivoesposo', $request->edadvivoesposo);
        Session::put('edadfalleceesposo', $request->edadfalleceesposo);
        Session::put('causafalleceesposo', $request->causafalleceesposo);
        Session::put('enfermedadesesposo', $request->enfermedadesesposo);
        Session::put('cantidadhijos', $request->cantidadhijos);
        Session::put('hijosvivo', $request->hijosvivo);
        Session::put('hijosfallece', $request->hijosfallece);
        Session::put('causafallecehijos', $request->causafallecehijos);
        Session::put('enfermedadeshijos', $request->enfermedadeshijos);
        //ANTECEDENTES FAMILIARES ADICIONALES
        Session::put('afhta', $request->input('preguntas.30.respuesta'));
        Session::put('hacecuanto30', $request->input('hacecuanto30'));
        Session::put('periodotipo30', $request->input('periodotipo30'));
        Session::put('afinfarto', $request->input('preguntas.31.respuesta'));
        Session::put('hacecuanto31', $request->input('hacecuanto31'));
        Session::put('periodotipo31', $request->input('periodotipo31'));
        Session::put('afacv', $request->input('preguntas.32.respuesta'));
        Session::put('hacecuanto32', $request->input('hacecuanto32'));
        Session::put('periodotipo32', $request->input('periodotipo32'));
        Session::put('afalergias', $request->input('preguntas.33.respuesta'));
        Session::put('hacecuanto33', $request->input('hacecuanto33'));
        Session::put('periodotipo33', $request->input('periodotipo33'));
        Session::put('afulcerapeptica', $request->input('preguntas.34.respuesta'));
        Session::put('hacecuanto34', $request->input('hacecuanto34'));
        Session::put('periodotipo34', $request->input('periodotipo34'));
        Session::put('afdiabetes', $request->input('preguntas.35.respuesta'));
        Session::put('hacecuanto35', $request->input('hacecuanto35'));
        Session::put('periodotipo35', $request->input('periodotipo35'));
        Session::put('afasma', $request->input('preguntas.36.respuesta'));
        Session::put('hacecuanto36', $request->input('hacecuanto36'));
        Session::put('periodotipo36', $request->input('periodotipo36'));
        Session::put('aftbc', $request->input('preguntas.37.respuesta'));
        Session::put('hacecuanto37', $request->input('hacecuanto37'));
        Session::put('periodotipo37', $request->input('periodotipo37'));
        Session::put('afartritis', $request->input('preguntas.38.respuesta'));
        Session::put('hacecuanto38', $request->input('hacecuanto38'));
        Session::put('periodotipo38', $request->input('periodotipo38'));
        Session::put('afenfermedadmental', $request->input('preguntas.39.respuesta'));
        Session::put('hacecuanto39', $request->input('hacecuanto39'));
        Session::put('periodotipo39', $request->input('periodotipo39'));
        Session::put('afcancer', $request->input('preguntas.40.respuesta'));
        Session::put('hacecuanto40', $request->input('hacecuanto40'));
        Session::put('periodotipo40', $request->input('periodotipo40'));
        Session::put('afotros', $request->input('preguntas.41.respuesta'));
        Session::put('hacecuanto41', $request->input('hacecuanto41'));
        Session::put('periodotipo41', $request->input('periodotipo41'));
        //ANTECEDENTES LABORALES
        Session::put('fechainicioatclab', $request->fechainicioatclab);
        Session::put('fechafinalatclab', $request->fechafinalatclab);
        Session::put('caracatclaboral1', $request->input('preguntas.1.carac'));
        Session::put('denunatclaboral1', $request->input('preguntas.1.denun'));
        Session::put('atenatclaboral1', $request->input('preguntas.1.aten'));
        Session::put('caracatclaboral2', $request->input('preguntas.2.carac'));
        Session::put('denunatclaboral2', $request->input('preguntas.2.denun'));
        Session::put('atenatclaboral2', $request->input('preguntas.2.aten'));
        Session::put('caracatclaboral3', $request->input('preguntas.3.carac'));
        Session::put('denunatclaboral3', $request->input('preguntas.3.denun'));
        Session::put('atenatclaboral3', $request->input('preguntas.3.aten'));
        //HISTORIA DE LA ENFERMEDAD ACTUAL
        Session::put('historiaenfermedad', $request->historiaenfermedad);
        //SIGNOS VITALES
        Session::put('examenfisicogeneral', $request->examenfisicogeneral);
        Session::put('llenadocapilar', $request->llenadocapilar);
        Session::put('lateralidad', $request->lateralidad);
        Session::put('pulso', $request->pulso);
        Session::put('satO2', $request->satO2);
        Session::put('frespiracion', $request->frespiracion);
        Session::put('temperatura', $request->temperatura);
        Session::put('presionarterial', $request->presionarterial);
        Session::put('agudezavisual', $request->agudezavisual);
        Session::put('usalentes', $request->usalentes);
        Session::put('peso', $request->peso);
        Session::put('estatura', $request->estatura);
        Session::put('imc', $request->imc);
        //EXAMEN FISICO SEGMENTADO
        Session::put('exficabeza', $request->exficabeza);
        Session::put('exfiojos', $request->exfiojos);
        Session::put('exfinariz', $request->exfinariz);
        Session::put('exfioidos', $request->exfioidos);
        Session::put('exfiboca', $request->exfiboca);
        Session::put('exficuello', $request->exficuello);
        Session::put('exfitorax', $request->exfitorax);
        Session::put('exficorazon', $request->exficorazon);
        Session::put('exfipulmones', $request->exfipulmones);
        Session::put('exfiabdomen', $request->exfiabdomen);
        Session::put('exfiextremidadesmmss', $request->exfiextremidadesmmss);
        Session::put('exfiextremidadesmmii', $request->exfiextremidadesmmii);
        Session::put('exfineurologico', $request->exfineurologico);
        Session::put('exfivestibulocereboloso', $request->exfivestibulocereboloso);
        Session::put('exfimarcha', $request->exfimarcha);
        Session::put('exficraneoycolumna', $request->exficraneoycolumna);
        Session::put('exfiexploracionneuro', $request->exfiexploracionneuro);

        /* return $pdf->download($pdfName); */
        
        $pdf = PDF::loadView('admin.asociados.fichamedicaclienteauditoria', compact('clienteauditoria'));
        $pdfName = 'Fichamedica_'. $clienteauditoria->nombrecompleto;
        $pdfName .= '.pdf';


        $usuario = auth()->user();
        $clientFolder = public_path('fichamedicaclientesauditoria/' . $clienteauditoria->id);
        $pdfPath = $clientFolder . '/' . $pdfName;
        if (!file_exists($clientFolder)) {
            mkdir($clientFolder, 0755, true);
        }
        $pdf->save($pdfPath);
        Fichamedicasubcliente::create([
            'clienteauditoriaid' => $clienteauditoria->id,
            'clienteauditorianombre' => $clienteauditoria->nombrecompleto,
            'document' =>/*  'fichamedicaclientesita/' . $cliente->id . '/' .  */$pdfName,
            'detalle' => 'FICHA MEDICA',
            'usuarioid' => $usuario->id,
            'usuarioregistro' => $usuario->name,
        ]);

        return $pdf->download($pdfName);
    }
    public function guardarformularioclienteauditoria(ClienteAuditoria $clienteauditoria)
    {
        return view('admin.asociados.crearformularioclienteauditoria');
    }
//
//CONTACTOS CLIENTE AUDITORIA
    public function vercontactoclienteauditoria(ClienteAuditoria $clienteauditoria)
    {
        $nombreclienteita = $clienteauditoria->nombrecompleto;
        $contactos = Contactosubcliente::where('clienteauditorianombre', $nombreclienteita)
                                ->simplePaginate(10000);

        return view('admin.asociados.vercontactoclienteauditoria', compact('contactos', 'clienteauditoria'));
    }
    public function crearcontactoclienteauditoria(ClienteAuditoria $clienteauditoria)
    {
        $parentesco = [
            'ABUEL@' => 'ABUEL@',
            'ESPOS@' => 'ESPOS@',
            'HERMAN@' => 'HERMAN@',
            'HIJ@' => 'HIJ@',
            'MADRE' => 'MADRE',
            'NIET@' => 'NIET@',
            'PADRE' => 'PADRE',
            'PRIM@' => 'PRIM@',
            'SOBRIN@' => 'SOBRIN@',
            'TI@' => 'TI@',
            'UNIÓN LIBRE' => 'UNIÓN LIBRE',
        ];

        $id = $clienteauditoria->id;

        return view('admin.asociados.crearcontactoclienteauditoria', compact('id', 'parentesco', 'clienteauditoria'));
    }
    public function guardarcontactoclienteauditoria(StoreContactosubclienteRequest $request)
    {
        $clienteID = $request->input('clienteauditoriaid');
        $clienteauditoria = ClienteAuditoria::findOrFail($clienteID);

        $clienteData = $request->all();
        $clienteData['clienteauditorianombre'] = $clienteauditoria->nombrecompleto;
        $contacto = Contactosubcliente::create($clienteData);
        return redirect()->route('admin.asociados.vercontactoclienteauditoria', ['clienteauditoria' => $clienteauditoria])->with('info', 'El contacto se creó con éxito');
    }
//
//ETIQUETAS Y REQUISITOS CLIENTE AUDITORIA
    public function generaretiquetaclienteauditoria(Request $request, ClienteAuditoria $clienteauditoria)
        {
            $pdf = PDF::loadView('admin.asociados.generaretiquetaclienteauditoria', compact('clienteauditoria'));
            $pdfName = 'Etiqueta_' . $clienteauditoria->id . '.pdf';
            return $pdf->download($pdfName);
        }
    public function generarchecklistclienteauditoria(ClienteAuditoria $clienteauditoria)
        {
            $tieneRequisitos = Requisitosclientesauditoria::where('clienteauditoriaid', $clienteauditoria->id)->exists();
            $estadoLaboral = strtolower($clienteauditoria->estadolaboral);
            $numHijosMenores = $clienteauditoria->numhijosmenores;
            $estadoCivil = strtolower($clienteauditoria->estadocivil);
            $bancos = Banco::orderBy('nombrebanco')->pluck('nombrebanco', 'nombrebanco');

            $tieneauditoriamedica = Tramitesubcliente::where('clienteauditoriaid', $clienteauditoria->id)
                ->where('tramite', 'AUDITORIA MEDICA')->exists();

            $rolusuario = auth()->user()->getRoleNames()->first(); 

            $registroExistente = Estadocotizacionsubcliente::where('clienteauditoriaid', $clienteauditoria->id)
                ->where('detalle', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS')
                ->first();
            $registroaprobadoExistente = Estadocotizacionsubcliente::where('clienteauditoriaid', $clienteauditoria->id)
                ->where('detalle', 'APROBADO PARA INICIAR A CREAR BATERIA')
                ->first();
        
            return view('admin.asociados.generarchecklistclienteauditoria', compact(
                'clienteauditoria', 
                'tieneRequisitos', 
                'estadoLaboral',
                'numHijosMenores',  
                'estadoCivil', 
                'registroExistente','rolusuario','registroaprobadoExistente','tieneauditoriamedica','bancos'
            ));
        }
    public function descargarchecklistclienteauditoria(Request $request, ClienteAuditoria $clienteauditoria)
        {
            $usuarioAutenticado = Auth::user();
            
            // Guardar requisitos en la base de datos
            $requisito1 = new Requisitosclientesauditoria();
            $requisito1->clienteauditoriaid = $clienteauditoria->id;
            $requisito1->clienteauditorianombre = $clienteauditoria->nombrecompleto;
            $requisito1->usuarioid = $usuarioAutenticado->id;
            $requisito1->usuarioregistro = $usuarioAutenticado->name;
            $requisito1->ciasegurado = 'PENDIENTE';
            $requisito1->cnacasegurado = 'PENDIENTE';
            $requisito1->save();
        
            $numPolizas = $request->input('numPolizas');
            for ($i = 1; $i <= $numPolizas; $i++) {
                $banco = $request->input('banco' . $i);
        
                if (!empty($banco)) { 
                    $requisitoPoliza = new Requisitosclientesauditoria();
                    $requisitoPoliza->clienteauditoriaid = $clienteauditoria->id;
                    $requisitoPoliza->clienteauditorianombre = $clienteauditoria->nombrecompleto;
                    $requisitoPoliza->usuarioid = $usuarioAutenticado->id;
                    $requisitoPoliza->usuarioregistro = $usuarioAutenticado->name;
                    $requisitoPoliza->banco = $banco;
                    $requisitoPoliza->nropolizageneral = $request->input('nropolizageneral' . $i);
                    $requisitoPoliza->polizageneral = $request->input('polizageneral' . $i) ? 'PENDIENTE' : 'NO APLICA';
                    $requisitoPoliza->declasalud = $request->input('declasalud' . $i) ? 'PENDIENTE' : 'NO APLICA';
                    $requisitoPoliza->nropolizadesgravamen = $request->input('nropolizadesgravamen' . $i);
                    $requisitoPoliza->polizasegurodesgravamen = $request->input('polizasegurodesgravamen' . $i) ? 'PENDIENTE' : 'NO APLICA';
                    $requisitoPoliza->save(); 
                }
            }
        
            // Pasar los datos a la vista del PDF
            $pdf = PDF::loadView('admin.asociados.descargarchecklistclienteitaaudi', compact('clienteauditoria', 'numPolizas', 'request'));
            $pdfName = 'Requisitos_AuditoriaMedica_' . $clienteauditoria->nombrecompleto . '.pdf';
            return $pdf->download($pdfName);
        } 
    public function subirdocrequisitosauditoria(ClienteAuditoria $clienteauditoria) 
        {
            $clienteauditoriaid = $clienteauditoria->id; 
            $userRole = auth()->user()->getRoleNames()->first(); 
            $requisitosCliente = Requisitosclientesauditoria::where('clienteauditoriaid', $clienteauditoriaid)->first();

            $ciaseguradoPendiente = $requisitosCliente ? $requisitosCliente->ciasegurado === 'PENDIENTE' : false;
            $cnacaseguradoPendiente = $requisitosCliente ? $requisitosCliente->cnacasegurado === 'PENDIENTE' : false;
            $polizasgenPendiente = $requisitosCliente ? $requisitosCliente->polizageneral === 'PENDIENTE' : false;
            $declasaludPendiente = $requisitosCliente ? $requisitosCliente->declasalud === 'PENDIENTE' : false;
            $polizaseguroPendiente = $requisitosCliente ? $requisitosCliente->polizasegurodesgravamen === 'PENDIENTE' : false;
            
            $requisitosubido = Requisitosclientesauditoria::where('clienteauditoriaid', $clienteauditoria->id)->firstOrFail();
            $ciaseguradoSubido = $requisitosCliente && strpos($requisitosCliente->ciasegurado, '.pdf') !== false ? true : false;
            $cnacaseguradoSubido = $requisitosCliente && strpos($requisitosCliente->cnacasegurado, '.pdf') !== false ? true : false;
            $polizasgenSubido = $requisitosCliente && strpos($requisitosCliente->polizageneral, '.pdf') !== false ? true : false;
            $declasaludSubido = $requisitosCliente && strpos($requisitosCliente->declasalud, '.pdf') !== false ? true : false;
            $polizaseguroSubido = $requisitosCliente && strpos($requisitosCliente->polizasegurodesgravamen, '.pdf') !== false ? true : false;

            $requisitosClientepolizas = Requisitosclientesauditoria::where('clienteauditoriaid', $clienteauditoriaid)->wherenotNull('banco')->get();

            return view('admin.asociados.subirdocrequisitosauditoria', compact('requisitosubido','requisitosClientepolizas','clienteauditoria', 'requisitosCliente', 'cnacaseguradoPendiente', 'ciaseguradoPendiente', 'polizasgenPendiente', 'declasaludPendiente', 'polizaseguroPendiente', 'userRole', 'cnacaseguradoSubido', 'ciaseguradoSubido', 'polizasgenSubido', 'declasaludSubido', 'polizaseguroSubido'));
        }
    public function guardardocrequisitosauditoria(Request $request, ClienteAuditoria $clienteauditoria) 
        {
            // Validar archivos y campos adicionales
            $request->validate([
                'cnacasegurado' => 'nullable|mimes:pdf',
                'ciasegurado' => 'nullable|mimes:pdf',
                'polizageneral.*' => 'nullable|mimes:pdf',
                'declasalud.*' => 'nullable|mimes:pdf',
                'polizasegurodesgravamen.*' => 'nullable|mimes:pdf',
                'nropolizageneral.*' => 'nullable|string',
                'nropolizadesgravamen.*' => 'nullable|string',
            ]);

            // Lista de campos a procesar
            $camposArchivos = ['cnacasegurado', 'ciasegurado', 'polizageneral', 'declasalud', 'polizasegurodesgravamen'];
            $nroPolizas = ['nropolizageneral', 'nropolizadesgravamen'];



            $requisito = Requisitosclientesauditoria::where('clienteauditoriaid', $clienteauditoria->id)->first();
            $camposArchivos3 = [
                'cnacasegurado', 'ciasegurado'
            ];

            foreach ($camposArchivos3 as $campo) {
                $this->manejarArchivoauditoria($request, $campo, $requisito, $clienteauditoria->id);
            }

            // Manejo de archivos
            foreach ($camposArchivos as $campo) {
                if ($request->hasFile($campo)) {
                    foreach ($request->file($campo) as $id => $file) {
                        $requisito = Requisitosclientesauditoria::find($id);
                        if ($requisito) {
                            $this->manejarArchivopolizasauditoria($request, $campo, $requisito);
                        }
                    }
                }
            }

            // Manejo de números de póliza
            foreach ($nroPolizas as $nroPoliza) {
                if ($request->has($nroPoliza)) {
                    foreach ($request->input($nroPoliza) as $id => $nro) {
                        $requisito = Requisitosclientesauditoria::find($id);
                        if ($requisito) {
                            // Actualizar el número de póliza correspondiente
                            $requisito->update([$nroPoliza => $nro]);
                        }
                    }
                }
            }

            return redirect()->route('admin.asociados.subirdocrequisitosauditoria', $clienteauditoria)->with('info', 'Los documentos y números de póliza se subieron con éxito');
        }
    protected function manejarArchivopolizasauditoria(Request $request, string $campo, $requisito)
        {
            if ($request->hasFile($campo)) {
                // Obtén los archivos de la solicitud
                $files = $request->file($campo);

                // Itera sobre cada archivo
                foreach ($files as $file) {
                    // Asegúrate de que $file sea un objeto UploadedFile
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $carpetaCliente = public_path("/requisitosclientesauditoria/{$requisito->clienteauditoriaid}");

                        // Crear la carpeta si no existe
                        if (!file_exists($carpetaCliente)) {
                            mkdir($carpetaCliente, 0755, true);
                        }

                        // Generar un nombre único para el archivo
                        $archivo = time() . '_' . $file->getClientOriginalName();

                        // Mover el archivo a la carpeta
                        $file->move($carpetaCliente, $archivo);

                        // Actualizar el modelo para el requisito específico
                        $requisito->update([$campo => $archivo]);
                    }
                }
            }
        }
    protected function manejarArchivoauditoria(Request $request, $campo, $requisito, $clienteauditoriaId)
        {
            if ($request->hasFile($campo)) {
                $file = $request->file($campo);
                $carpetaCliente = public_path("/requisitosclientesauditoria/{$clienteauditoriaId}");

                // Crear la carpeta si no existe
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }

                // Generar un nombre único para el archivo
                $archivo = time() . '_' . $file->getClientOriginalName();

                // Mover el archivo a la carpeta
                $file->move($carpetaCliente, $archivo);

                // Actualizar el modelo
                $requisito->update([$campo => $archivo]);
            }
        }

    public function generarPDFconsentimientoauditoria(ClienteAuditoria $clienteauditoria, Request $request)
        {
            $nombres = $request->input('nombrecompleto');
            $ci = $request->input('ci');
            $fechahoy = date('Y-m-d'); 
            $clienteitaId = $request->input('clienteauditoriaid');
            $sucursalCliente = $request->input('sucursal');

            $nombreArchivo = "Consentimiento_Informado_Inicial {$nombres}.pdf";

            $nombreCompleto = "{$nombres}";

            $usuarioId = auth()->id();
            $usuarioNombre = auth()->user()->name;

            $documentacion = Estadocotizacionsubcliente::create([
                'clienteauditoriaid' => $clienteitaId,
                'clienteauditorianombre' => $nombreCompleto,
                'detalle' => 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS',
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioNombre,
                'document' => null, 
            ]);
        
            
            // Buscar el proveedor, precio y precio de compra en BateriaProveedor
            $bateriaProveedor = BateriaProveedor::where('sucursal', $sucursalCliente)
                ->where('accion', 'MEDICINA LABORAL')
                ->first();

            if ($bateriaProveedor) {
            $programacion = BateriaSubCliente::create([
                'clienteauditoriaid' => $clienteitaId,
                'clienteauditorianombre' => $nombreCompleto,
                'tipoarea' => 'ESPECIALIDAD',
                'areanombre' => 'MEDICINA LABORAL',
                'accionnombre' => 'MEDICINA LABORAL',
                'precio' => $bateriaProveedor->precio,
                'informe' => 'NO TIENE INFORME',
                'preciocompra' => $bateriaProveedor->preciocompra,
                'proveedorasignado' => $bateriaProveedor->proveedor, 
                'accionid' => $bateriaProveedor->id, 
                'servicio' => $bateriaProveedor->servicio, 
                'fechabateria' => now(),
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioNombre,
            ]);
            } else {

            }

            $data = [
                'nombres' => $nombres,
                'ci' => $ci,
                'fechahoy' => $fechahoy,
            ];

            $pdf = PDF::loadView('admin.asociados.pdfauditoria.pdfconsentimientoclienteauditoria', $data);

            return $pdf->download($nombreArchivo);
        }
    public function generarsoloPDFconsentimientoauditoria(Request $request)
        {
            // Obtener los datos del cliente desde el request
            $nombres = $request->input('nombrecompleto');
            $ci = $request->input('ci');
            $fechahoy = date('Y-m-d'); 
            $clienteitaId = $request->input('clienteauditoriaid');

            // Generar el nombre del archivo PDF
            $nombreArchivo = "Consentimiento_Informado_Inicial {$nombres}.pdf";

            // Crear el nombre completo del cliente
            $nombreCompleto = "{$nombres}";

            // Obtener el ID del usuario autenticado
            $usuarioId = auth()->id();
            $usuarioNombre = auth()->user()->name;

            // Guardar el registro en DocumentacionSubcliente
            $documentacion = Estadocotizacionsubcliente::create([
                'clienteauditoriaid' => $clienteitaId,
                'clienteauditorianombre' => $nombreCompleto,
                'detalle' => 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS',
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioNombre,
                'document' => null, // Inicialmente sin documento
            ]);


            // Pasar los datos a la vista para generar el PDF
            $data = [
                'nombres' => $nombres,
                'ci' => $ci,
                'fechahoy' => $fechahoy,
            ];

            // Cargar la vista para el PDF
            $pdf = PDF::loadView('admin.asociados.pdfauditoria.pdfconsentimientoclienteauditoria', $data);
            
            // Retornar el PDF con el nombre generado
            return $pdf->download($nombreArchivo);
        }
    public function aprobariniciarcrearbateriaauditoria(Request $request, ClienteAuditoria $clienteauditoria)
        {
            // Obtener los datos del cliente desde el request
            $nombres = $request->input('nombrecompleto');
            $clienteitaId = $request->input('clienteauditoriaid');

            // Crear el nombre completo del cliente
            $nombreCompleto = "{$nombres}";

            // Obtener el ID del usuario autenticado
            $usuarioId = auth()->id();
            $usuarioNombre = auth()->user()->name;

            // Guardar el registro en DocumentacionSubcliente
            $documentacion = Estadocotizacionsubcliente::create([
                'clienteauditoriaid' => $clienteitaId,
                'clienteauditorianombre' => $nombreCompleto,
                'detalle' => 'APROBADO PARA INICIAR A CREAR BATERIA',
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioNombre,
                'document' => null, // Inicialmente sin documento
            ]);

            return redirect()->back()->with('info', 'Aprobación exitosa.');
        }
    public function generarPDFguardarconsentimientoauditoria(Request $request)
        {
            $request->validate(['pdf_file' => 'required|mimes:pdf|max:2048']); // Validar archivo

            // Obtener el clienteitaid y la acción
            $clienteitaId = $request->input('clienteauditoriaid');
            $detalle = $request->input('detalle');

            // Buscar el registro existente
            $documentacion = Estadocotizacionsubcliente::where('clienteauditoriaid', $clienteitaId)
                ->where('detalle', $detalle)
                ->first();

            if ($documentacion) {
                // Guardar el archivo
                $file = $request->file('pdf_file');
                $carpetaCliente = public_path("/cotizacionesaprobadasauditoria/{$clienteitaId}");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }
                $archivo_name = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name);

                // Actualizar el campo document en el registro
                $documentacion->document = $archivo_name;
                $documentacion->save();

                return redirect()->back()->with('info', 'PDF guardado exitosamente.');
            }

            return redirect()->back()->with('error', 'Registro no encontrado.');
        }
    public function generarPDFconsentimientoinformadoauditoria(Request $request)
        {
            // Obtener los datos del cliente desde el request
            $nombres = $request->input('nombrecompleto');
            $clienteitaId = $request->input('clienteauditoriaid');
            $ci = $request->input('ci');
            $fechahoy = date('Y-m-d'); 

            // Generar el nombre del archivo PDF
            $nombreArchivo = "Consentimiento_Informado_Evaluaciones_Estudios {$nombres}.pdf";

            // Crear el nombre completo del cliente
            $nombreCompleto = "{$nombres}";

            // Obtener el ID del usuario autenticado
            $usuarioId = auth()->id();
            $usuarioNombre = auth()->user()->name;

            /* $documentacion = DocumentacionSubcliente::create([
                'clienteitaid' => $clienteitaId,
                'clienteitanombre' => $nombreCompleto,
                'accion' => 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS',
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioNombre,
                'document' => null, // Inicialmente sin documento
            ]); */

            // Pasar los datos a la vista para generar el PDF
            $data = [
                'nombres' => $nombres,
                'ci' => $ci,
                'fechahoy' => $fechahoy,
            ];

            // Cargar la vista para el PDF
            $pdf = PDF::loadView('admin.asociados.pdfauditoria.pdfconsentimientoinformadoclienteauditoria', $data);
            
            // Retornar el PDF con el nombre generado
            return $pdf->download($nombreArchivo);
        }
//
//DECLARACION MEDICA
    public function declaracionesmedico(ClienteBanco $clientebanco)
    {
         // Verificar si existe el documento DIGITAL
         $declaracionDigital = Fichamedicasubcliente::where('clienteid', $clientebanco->id)
         ->where('tipodocumento', 'DIGITAL')
         ->first();

     // Verificar si existe el documento FISICO (puede no existir aún)
     $declaracionFisico = Fichamedicasubcliente::where('clienteid', $clientebanco->id)
         ->where('tipodocumento', 'FISICO')
         ->first();

     // Si el documento DIGITAL existe, redirigir a la vista para mostrar ambos documentos
     if ($declaracionDigital) {
         return view('admin.asociados.formularios.mostrardeclaracionesmedico', compact('clientebanco', 'declaracionDigital', 'declaracionFisico'));
     }

     // Si el documento DIGITAL no existe, redirigir al formulario para subirlo
     return view('admin.asociados.formularios.declaracionesmedico', compact('clientebanco'));

    }

    /* public function guardardeclaracion(Request $request, ClienteBanco $clientebanco)
    {
        $preguntas = $request->input('preguntas');

        foreach ($preguntas as $pregunta) {
            // Verificar si la respuesta existe y no está vacía
            if (isset($pregunta['respuesta']) && !empty($pregunta['respuesta'])) {
                $respuesta = $pregunta['respuesta'];

                // Verificar si la respuesta es 'si' para guardar el formulario
                if ($respuesta == 'si') {
                    $formulario = new Formulario();
                    $formulario->cliente_id = $pregunta['cliente_id'];
                    $formulario->pregunta_id = $pregunta['pregunta_id'];
                    $formulario->pregunta_nombre = $pregunta['pregunta_nombre'];

                    // Verificar y asignar campos opcionales
                    if (isset($pregunta['diagnostico'])) {
                        $formulario->diagnostico = $pregunta['diagnostico'];
                    }
                    if (isset($pregunta['fecha'])) {
                        $formulario->fecha = $pregunta['fecha'];
                    }
                    if (isset($pregunta['tiempo'])) {
                        $formulario->tiempo = $pregunta['tiempo'];
                    }
                    if (isset($pregunta['gradorecuperacion'])) {
                        $formulario->gradorecuperacion = $pregunta['gradorecuperacion'];
                    }
                    if (isset($pregunta['medico'])) {
                        $formulario->medico = $pregunta['medico'];
                    }
                    if (isset($pregunta['direccionmedico'])) {
                        $formulario->direccionmedico = $pregunta['direccionmedico'];
                    }
                    // Verificar y asignar campos opcionales
                    if (isset($pregunta['diagnostico2'])) {
                        $formulario->diagnostico2 = $pregunta['diagnostico2'];
                    }
                    if (isset($pregunta['fecha2'])) {
                        $formulario->fecha2 = $pregunta['fecha2'];
                    }
                    if (isset($pregunta['tiempo2'])) {
                        $formulario->tiempo = $pregunta['tiempo'];
                    }
                    if (isset($pregunta['gradorecuperacion'])) {
                        $formulario->tiempo2 = $pregunta['tiempo2'];
                    }
                    if (isset($pregunta['medico2'])) {
                        $formulario->medico2 = $pregunta['medico2'];
                    }
                    if (isset($pregunta['direccionmedico2'])) {
                        $formulario->direccionmedico2 = $pregunta['direccionmedico2'];
                    }
                    if (isset($pregunta['hacecuanto'])) {
                        $formulario->hacecuanto = $pregunta['hacecuanto'];
                    }
                    if (isset($pregunta['cadacuanto'])) {
                        $formulario->cadacuanto = $pregunta['cadacuanto'];
                    }
                    if (isset($pregunta['parentesco2'])) {
                        $formulario->parentesco2 = $pregunta['parentesco2'];
                    }
                    if (isset($pregunta['cuantosmeses'])) {
                        $formulario->cuantosmeses = $pregunta['cuantosmeses'];
                    }
                    if (isset($pregunta['detallescompletos'])) {
                        $formulario->detallescompletos = $pregunta['detallescompletos'];
                    }

                    $formulario->save();
                }
            } else {
                // Si no se seleccionó ninguna respuesta, puedes manejar este caso según tus requisitos
                // Por ejemplo, puedes ignorar este formulario o guardar una marca para indicar que no se seleccionó ninguna respuesta.
            }
        }

        return redirect()->route('admin.asociados.formularios.declaracionesmedico', $clientebanco)->with('info', 'Los formularios se registraron con éxito');
    } */
    public function generarQR(Request $request, Cliente $cliente)
            {
                $datosFormulario = $request->except('_token');
            
                // Convertir los datos del formulario a JSON
                $contenidoQR = json_encode([
                    'nombres' => $cliente->nombres,
                    'apepaterno' => $cliente->apepaterno,
                    'apematerno' => $cliente->apematerno,
                    // Agrega más datos aquí si es necesario
                ]);
                
                // Generar el nombre del archivo QR
                $nombreQR = 'qr_temporal.png';
            
                // Generar la ruta del directorio donde se guardará el QR
                $rutaDirectorio = public_path('temp');
            
                // Verificar si el directorio no existe y crearlo si es necesario
                if (!file_exists($rutaDirectorio)) {
                    mkdir($rutaDirectorio, 0777, true);
                }
            
                // Generar la ruta completa donde se guardará el QR
                $rutaQR = $rutaDirectorio . '/' . $nombreQR;
            
                // Generar el QR con el contenido y guardarlo temporalmente
                QrCode::format('png')->size(300)->generate($contenidoQR, $rutaQR);
            
                // Retornar la vista con la ruta del QR
                return view('admin.clientes.formulario', ['rutaQR' => asset('temp/' . $nombreQR)], compact('cliente'));
            }
//
//PROVEEDOR INFORME FINAL CLIENTE ITA
    public function guardarproveedorinformefinalauditoria(StoreProveedorInformefinalRequest $request, ClienteAuditoria $clienteauditoria)
    {
        $request->validate([
            'fechabateria' => 'required|date',
            'celularproveedor' => 'required|string',
            'precio' => 'required',
            'preciocompra' => 'required',
            'tramite' => 'required',
        ]);

        $usuarioId = auth()->user()->id;
        $usuarioRegistro = auth()->user()->name;

        $proveedorAsignado = Proveedor::findOrFail($request->proveedorasignado)->proveedor;

        ProveedorInformefinal::create([
            'fechabateria' => $request->fechabateria,
            'proveedorasignado' => $proveedorAsignado,
            'celularproveedor' => $request->celularproveedor,
            'clienteauditoriaid' => $clienteauditoria->id,
            'clienteauditorianombre' => $clienteauditoria->nombrecompleto,
            'usuarioid' => $usuarioId,
            'usuarioregistro' => $usuarioRegistro,
            'precio' => $request->precio,
            'preciocompra' => $request->preciocompra,
            'servicio' => $request->tramite,
        ]);

        // Crear el registro en BateriaSubCliente
        BateriaSubCliente::create([
            'fechabateria' => $request->fechabateria,
            'clienteauditoriaid' => $clienteauditoria->id,
            'clienteauditorianombre' => $clienteauditoria->nombrecompleto,
            'usuarioid' => $usuarioId,
            'usuarioregistro' => $usuarioRegistro,
            'tipoarea' => 'INFORME FINAL',
            'informe' => 'NINGUNO',
            'areanombre' => 'INFORME FINAL',
            'accionnombre' => 'INFORME FINAL',
            'precio' => $request->precio,
            'preciocompra' => $request->preciocompra,
            'proveedorasignado' => $proveedorAsignado,
            'servicio' => 'INTERNO',
            'accionid' => 'IF',

        ]);

        return redirect()->route('admin.asociados.verclienteauditoria', $clienteauditoria)->with('info', 'Proveedor asignado exitosamente.');
    }
//



//CLIENTES BANCOS
//CREAR Y EDITAR CLIENTE BANCO
    public function crearclientebanco(Asociado $asociado)
    {
        $suc = [
            'COCHABAMBA' => 'COCHABAMBA',
            'SANTA CRUZ' => 'SANTA CRUZ',
        ];
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
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
        $id = $asociado->asociado ? Asociado::where('asociado', $asociado->asociado)->value('id') : null;
        
        return view('admin.asociados.crearclientebanco', compact('suc','asociado', 'genero', 'departamentos', 'estciv', 'id'));
    }
    public function guardarclientebanco(StoreClienteBancoRequest $request)
    {
        // Obtener la ciudad del request
        $id = $request->input('ciudad');
        $ciudad = Departamento::findOrFail($id);
        $ciudadNombre = $ciudad->departamento;

        // Obtener el ID del asociado del request
        $asociadoId = $request->input('asociadoid');

        // Agregar la ciudad al array de datos del cliente
        $clienteData = $request->all();
        $clienteData['ciudad'] = $ciudadNombre;

        // Crear el cliente bancario
        $clientebanco = ClienteBanco::create($clienteData);

        // Redirigir al listado de clientes bancarios asociados al asociado
        return redirect()->route('admin.asociados.listadoclientebanco', $asociadoId)->with('info', 'El cliente se creó con éxito');
    }
    public function listadoclientebanco(Request $request, Asociado $asociado)
    {
        $clientebancos = ClienteBanco::where('asociadonombre', $asociado->asociado)->get();

        return view('admin.asociados.listadoclientebanco', compact('asociado', 'clientebancos'));
    }
    public function buscarclientesbanco(Request $request, Asociado $asociado)
    {
        $asociado = $asociado->asociado ? Asociado::where('asociado', $asociado->asociado)->value('asociado') : null;
        $busqueda = $request->get('buscarpor');
        $clientebancos = ClienteBanco::where(function ($query) use ($busqueda) {
            $query->where('nombrecompleto', 'like', "%$busqueda%")
                ->orWhere('ci', 'like', "%$busqueda%")
                ->orWhere('ciudad', 'like', "%$busqueda%");
        })->simplePaginate(1000);
        return view('admin.asociados.listadoclientebanco', compact('clientebancos', 'asociado'));
    }
    public function verclientebanco(ClienteBanco $clientebanco, Asociado $asociado)
    {
        $requisitosubclientes = ProveedorInformefinal::where('clientebancoid', $clientebanco->id)->get();

        $proveedores = Proveedor::where('id', 3)->get(['id', 'proveedor', 'celular']);

        $tieneRequisitos = RequisitoSubCliente::where('clientebancoid', $clientebanco->id)->exists();
        $tieneBateria = Bateriasubcliente::where('clienteid', $clientebanco->id)->exists();
        $tieneContactos = ContactoSubCliente::where('clientebancoid', $clientebanco->id)->exists();
        $tieneCotizacionaprobada = Estadocotizacionsubcliente::where('clientebancoid', $clientebanco->id)->exists();
        $tieneProgramacion = Programacionsubcliente::where('clientebancoid', $clientebanco->id)->exists();
        $tieneProgramacionatentido = Estadoprogramacionsubcliente::where('clientebancoid', $clientebanco->id)->exists();

        $fichamedica = Fichamedicasubcliente::where('clientebancoid', $clientebanco->id)
                ->where('detalle', 'FICHA MEDICA')
                ->exists();
        $declaracionmedica = Fichamedicasubcliente::where('clientebancoid', $clientebanco->id)
                ->where('detalle', 'DECLARACIONES HECHAS AL MEDICO EXAMINADOR')
                ->exists();
        $consentimientoinformado = Estadocotizacionsubcliente::where('clientebancoid', $clientebanco->id)
                ->where('detalle', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS')
                ->whereNotNull('document')
                ->exists();

        $tienerequisitosapelacion = RequisitoSubCliente::where('clientebancoid', $clientebanco->id)->exists();
        $tienerequisitossegundasolicitud = RequisitoSubCliente::where('clientebancoid', $clientebanco->id)->exists();

        $rolusuario = auth()->user()->getRoleNames()->first(); 

        $cartaconsentimientoExistente = Estadocotizacionsubcliente::where('clientebancoid', $clientebanco->id) 
            ->where('detalle', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS')
            ->whereNotNull('document')
            ->first();
        $bateriaaprobadaExistente = DocumentacionSubcliente::where('clientebancoid', $clientebanco->id) 
            ->where('accion', 'APROBADO PARA INICIAR A CREAR BATERIA')
            ->first();

        $documentacion = Documentacionsubcliente::where('clientebancoid', $clientebanco->id)
        ->where('accion', 'HISTORIA MÉDICA')
        ->first();

        $nombreCliente = $clientebanco->nombrecompleto;
        $sucursalCliente = $clientebanco->sucursal;

        $accionesCliente = BateriaSubCliente::where('clientenombre', $nombreCliente)->pluck('accionnombre')->toArray();

        $fechasbateriasSubCliente = BateriaSubCliente::where('clientenombre', $nombreCliente)
            ->distinct()
            ->pluck('fechabateria');

        $fechasRegistradas = ProveedorInformefinal::where('clientebancoid', $clientebanco->id)
            ->pluck('fechabateria');

        $fechasDisponibles = $fechasbateriasSubCliente->diff($fechasRegistradas);

        $fechasBateriaPorAccion = BateriaSubCliente::whereIn('accionnombre', $accionesCliente)
            ->where('clientenombre', $nombreCliente)
            ->whereIn('fechabateria', $fechasDisponibles)
            ->distinct()
            ->pluck('fechabateria', 'accionnombre');

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accion => $fecha) {
            $accionesPorFecha[$fecha][] = $accion;
        }

        return view('admin.asociados.verclientebanco', compact('fichamedica','declaracionmedica','consentimientoinformado','rolusuario','tieneProgramacion','tieneProgramacionatentido','tieneCotizacionaprobada','bateriaaprobadaExistente','tieneBateria','cartaconsentimientoExistente','tieneContactos','requisitosubclientes','accionesPorFecha','fechasBateriaPorAccion','proveedores', 'clientebanco', 'tieneRequisitos', 'documentacion'));
    }
    public function editarclientebanco(ClienteBanco $clientebanco)
    {
        $suc = [
            'COCHABAMBA' => 'COCHABAMBA',
            'SANTA CRUZ' => 'SANTA CRUZ',
        ];
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
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'departamento');

        return view('admin.asociados.editarclientebanco', compact('clientebanco','suc', 'genero', 'departamentos', 'estciv', 'gradoins', 'actlab'));
    }
    public function actualizarclientebanco(UpdateClienteBancoRequest $request, ClienteBanco $clientebanco)
    {
        $clienteData = $request->validated();
        $clientebanco->update($clienteData);

        return redirect()->route('admin.asociados.verclientebanco', $clientebanco)->with('info', 'El cliente se actualizó con éxito');
    }
//
//CREAR BATERIA CLIENTE BANCO
    public function crearbateriaclientebanco(ClienteBanco $clientebanco, Request $request)
    {
        $nombreCliente = $clientebanco->nombrecompleto;
        $sucursalCliente = $clientebanco->sucursal;
        $accionesCliente = BateriaSubCliente::where('clientenombre', $nombreCliente)->pluck('accionnombre')->toArray();
        $rolusuario = auth()->user()->getRoleNames()->first(); 
        $asociadoid = $clientebanco->asociadoid;

        $categorias = AreaAccion::where('asociadoid', $asociadoid)
                                ->distinct('categoria')
                                ->pluck('categoria');

        $clientebancos = Areaaccion::where('asociadoid', $asociadoid)
                                ->where('estado', 'ACTIVO')
                                ->where('sucursal', $sucursalCliente)
                                ->orderBy('categoria')
                                ->get();

        $id = $clientebanco->nombrecompleto ? ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('id') : null;

        /* $fechasbateriasSubCliente = BateriaSubCliente::where('clientenombre', $nombreCliente)
            ->distinct()
            ->pluck('fechabateria');
        $fechasBateriaPorAccion = BateriaSubCliente::whereIn('accionnombre', $accionesCliente)
            ->where('clientenombre', $nombreCliente)
            ->whereIn('fechabateria', $fechasbateriasSubCliente)
            ->distinct()
            ->pluck('fechabateria', 'accionnombre');
        $accionesPorFecha = [];
                foreach ($fechasBateriaPorAccion as $accion => $fecha) {
                $accionesPorFecha[$fecha][] = $accion;
                } */

        

                $nombreCliente = $clientebanco->nombrecompleto; 
        $idCliente = $clientebanco->id; 

        $accionesCliente = BateriaSubCliente::where('clienteid', $idCliente)
            ->pluck('accionid')
            ->toArray();

        $fechasbateriasSubCliente = BateriaSubCliente::where('clienteid', $idCliente)
            ->distinct()
            ->pluck('fechabateria');

        $fechasBateriaPorAccion = BateriaSubCliente::whereIn('accionid', $accionesCliente)
            ->where('clienteid', $idCliente)
            ->whereIn('fechabateria', $fechasbateriasSubCliente)
            ->distinct()
            ->pluck('fechabateria', 'accionid');

        $accionesNombres = BateriaSubCliente::whereIn('accionid', $accionesCliente)
            ->pluck('accionnombre', 'accionid')
            ->toArray();

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accionid => $fecha) {
            $idbateirasubcliente = Bateriasubcliente::where('accionid', $accionid)->where('clienteid', $idCliente)->value('id');
            $precioaccion = Bateriasubcliente::where('accionid', $accionid)->where('clienteid', $idCliente)->value('precio');
            $informeaccion = Bateriasubcliente::where('accionid', $accionid)->where('clienteid', $idCliente)->value('informe');
            $proveedorbateria = Bateriasubcliente::where('accionid', $accionid)->where('clienteid', $idCliente)->value('proveedorasignado');

            $accionNombre = $accionesNombres[$accionid] ?? 'Desconocida';

            $accionesPorFecha[$fecha][] = [
                'id' => $idbateirasubcliente,
                'accion' => $accionNombre,
                'proveedor' => $proveedorbateria,
                'precio' => $precioaccion,
                'informe' => $informeaccion
            ];
        }
        return view('admin.asociados.crearbateriaclientebanco', compact('rolusuario','clientebancos','clientebanco', 'categorias','id', 'accionesPorFecha','fechasBateriaPorAccion','accionesCliente'));
    }
    public function guardarbateriaclientebanco(StoreBateriasubclienteRequest $request, ClienteBanco $clientebanco)
    {
        $items = $request->input('items', []);
        foreach ($items as $itemId) {
            $clienteBancoItem = Areaaccion::findOrFail($itemId);

            Bateriasubcliente::create([
                'usuarioid' => $request->usuarioid,
                'usuarioregistro' => $request->usuarioregistro,
                'clienteid' => $request->clienteid,
                'clientenombre' => $request->clientenombre,
                'tipoarea' => $clienteBancoItem->tiponombre,
                'areanombre' => $clienteBancoItem->area,
                'accionnombre' => $clienteBancoItem->accion,
                'servicio' => $clienteBancoItem->servicio,
                'proveedorasignado' => $clienteBancoItem->proveedor,
                'preciocompra' => $clienteBancoItem->preciocompra,
                'informe' => 'NO TIENE INFORME',
                'accionid' => $clienteBancoItem->id,
                'precio' => $clienteBancoItem->precio,
                'fechabateria' => now()->toDateString(),
                'itemid' => $itemId,
            ]);
        }
        return redirect()->route('admin.asociados.crearbateriaclientebanco', ['clientebanco' => $clientebanco])->with('info', 'La batería se creó con éxito');
    }
//
//APROBAR COTIZACION DE PROGRAMACION DE CLIENTE BANCO
    public function aprobacioncotizacionclientebanco(ClienteBanco $clientebanco, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarporfecha');
        $areaSeleccionada = $request->get('buscarporarea');

        $fechas = BateriaSubCliente::where('clienteid', $clientebanco->id)
                                    ->pluck('fechabateria')
                                    ->unique();
        $areas = BateriaSubCliente::where('clienteid', $clientebanco->id)
                                ->pluck('areanombre')
                                ->unique();

        $bateriasubclientes = collect();
        $total = 0;

        $query = BateriaSubCliente::where('clienteid', $clientebanco->id);
        if ($fechaSeleccionada) {
            $query->where('fechabateria', $fechaSeleccionada);
        }
        if ($areaSeleccionada) {
            $query->where('areanombre', $areaSeleccionada);
        }

        if ($fechaSeleccionada || $areaSeleccionada) {
            $bateriasubclientes = $query->simplePaginate(1000);

            $total = $bateriasubclientes->sum(function ($bateriasubcliente) {
                return str_replace(',', '.', $bateriasubcliente->precio);
            });
            $total = number_format($total, 2, '.', '');
        }
        $id = $clientebanco->id;

        return view('admin.asociados.aprobacioncotizacionclientebanco', compact('bateriasubclientes', 'id', 'clientebanco', 'fechas', 'total', 'fechaSeleccionada', 'areas', 'areaSeleccionada'));
    }
    public function buscarbateriaclientebanco(ClienteBanco $clientebanco, Request $request)
    {
        return $this->aprobacioncotizacionclientebanco($clientebanco, $request);
    }
    /* public function generarpdfcotizacionclientebanco(ClienteBanco $clientebanco, Request $request)
    {
        // Obtener la última fechabateria del cliente
        $ultimaFechaBateria = ProgramacionsubCliente::where('clientebancoid', $clientebanco->id)
            ->orderBy('fechabateria', 'desc')
            ->first()->fechabateria;

        // Obtener las acciones asociadas a la última fechabateria
        $bateriasubclientes = ProgramacionsubCliente::where('clientebancoid', $clientebanco->id)
            ->where('fechabateria', $ultimaFechaBateria)
            ->get();

        // Calcular el total de los precios
        $total = $bateriasubclientes->sum('precio');

        // Generar el PDF con la información del cliente y las acciones
        $pdf = PDF::loadView('admin.asociados.pdfcotizacionclientebanco', compact('clientebanco', 'bateriasubclientes', 'total'));

        // Crear un nombre dinámico para el archivo PDF
        $pdfName = 'Cotización_' . $clientebanco->nombrecompleto . '.pdf';
        
        // Descargar el PDF
        return $pdf->download($pdfName);
    } */
    public function generarpdfcotizacionclientebanco(ClienteBanco $clientebanco, Request $request) 
    {
        // Obtener la última fechabateria del cliente
        $ultimaFechaBateria = Bateriasubcliente::where('clienteid', $clientebanco->id)
            ->orderBy('fechabateria', 'desc')
            ->first()->fechabateria;

        // Obtener las acciones asociadas a la última fechabateria
        $bateriasubclientes = Bateriasubcliente::where('clienteid', $clientebanco->id)
            ->where('fechabateria', $ultimaFechaBateria)
            ->get();

        // Calcular el total de los precios
        $total = $bateriasubclientes->sum('precio');

        // Generar el PDF con la información del cliente y las acciones
        $pdf = PDF::loadView('admin.asociados.pdfcotizacionclientebanco', compact('clientebanco', 'bateriasubclientes', 'total'));

        // Crear un nombre dinámico para el archivo PDF
        $pdfName = 'Cotización_' . $clientebanco->nombrecompleto . '.pdf';
        
        // Retornar la vista en lugar de descargar
        return $pdf->stream($pdfName); // Usamos stream() para mostrar el PDF en lugar de descargarlo
    }

    public function aprobarcotizacionprogramacionclientebanco(ClienteBanco $clientebanco)
    {
        $nombreCliente = $clientebanco->nombrecompleto;
        
        $id = $clientebanco->nombrecompleto ? ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('id') : null;

        $fechasRegistradas = EstadoCotizacionSubCliente::where('clientebancoid', $clientebanco->id)
                                        ->pluck('fechabateria')
                                        ->unique();

        $fechasDisponibles = BateriaSubCliente::where('clienteid', $clientebanco->id)
                                        ->pluck('fechabateria')
                                        ->unique();

        $fechas = $fechasDisponibles->filter(function ($fecha) use ($fechasRegistradas) {
            return !$fechasRegistradas->contains($fecha);
        });

        return view('admin.asociados.aprobarcotizacionprogramacionclientebanco', compact('clientebanco', 'id', 'fechas', 'fechasRegistradas','fechasDisponibles'));
    }
    public function guardaraprobacioncotizacionclientebanco(StoreEstadocotizacionsubclienteRequest $request, ClienteBanco $clientebanco)
    {
        $archivo_name = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $carpetaCliente = public_path("/cotizacionesaprobadasbanco/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }
        $documentacioncotizacioncliente = Estadocotizacionsubcliente::create([
            'document' => $archivo_name,
            'usuarioid' => auth()->user()->id,
            'usuarioregistro' => auth()->user()->name,
            'clientebancoid' => $request->clientebancoid,
            'clientebanconombre' => $request->clientebanconombre,
            'fechabateria' => $request->input('fechabateria'),
        ]);

        return redirect()->route('admin.asociados.aprobarcotizacionprogramacionclientebanco', $request->clientebanco)->with('info', 'El documento se subió con éxito');
    }
//
//CREAR PROGRAMACION Y REPROGRAMACION DE CLIENTE COMUN
    public function crearprogramacionclientebanco(ClienteBanco $clientebanco)
    {
        $nombreCliente = $clientebanco->nombrecompleto;
        $idCliente = $clientebanco->id;
        $clienteitaid = $clientebanco->id;
        $sucursalCliente = $clientebanco->sucursal;
        $rolusuario = auth()->user()->getRoleNames()->first(); 
        $esProveedor = ($rolusuario === 'PROVEEDOR');
        $id = $clientebanco->nombrecompleto ? ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('id') : null;

        $accionesCliente = BateriaSubCliente::where('clienteid', $idCliente)
            ->whereIn('accionnombre', function ($query) use ($sucursalCliente) {
                $query->select('accionnombre')->from('clientebancos')->where('sucursal', $sucursalCliente);
            })
            ->pluck('accionnombre')
            ->unique();

        $proveedoresAsignados = BateriaSubCliente::where('clienteid', $idCliente)
            ->whereIn('accionnombre', $accionesCliente)
            ->pluck('proveedorasignado', 'accionnombre')
            ->toArray();
    
        $fechasBateriaPorAccion = BateriaSubCliente::where('clienteid', $idCliente)
            ->whereIn('accionnombre', $accionesCliente)
            ->select('accionnombre', 'fechabateria')
            ->get();
        
        
        $accionesRegistradas = Programacionsubcliente::where('clientebancoid', $idCliente)
            ->pluck('accionnombre', 'fechabateria')
            ->toArray();

        foreach ($accionesRegistradas as $fecha => $accion) {
        if (!isset($accionesRegistradas[$fecha])) {
            $accionesRegistradas[$fecha] = [];
            }

            if (!is_array($accionesRegistradas[$fecha])) {
                $accionesRegistradas[$fecha] = [$accion];
            } else {
                $accionesRegistradas[$fecha][] = $accion;
            }
        }

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $item) {
            $accion = $item->accionnombre;
            $fecha = $item->fechabateria;

            $accionYaRegistrada = Programacionsubcliente::where('clientebancoid', $idCliente)
                ->where('fechabateria', $fecha)
                ->where('accionnombre', $accion)
                ->exists();
        
            if (!isset($accionesPorFecha[$fecha])) {
                $accionesPorFecha[$fecha] = [];
            }

            if (!$accionYaRegistrada) {
                $accionesPorFecha[$fecha][] = $accion;
            }
        }

        
        $proveedoresDetalles = [];
        foreach ($proveedoresAsignados as $accion => $nombreProveedor) {
            $proveedor = BateriaSubCliente::where('accionnombre', $accion)->where('clienteid', $idCliente)
                ->latest()
                ->first();

            if ($proveedor) {
                $proveedoresDetalles[$accion] = [
                    'proveedor' => $proveedor->proveedorasignado,
                    'horarioinicial' => $proveedor->horarioinicial,
                    'horariofinal' => $proveedor->horariofinal,
                    'fechabateria' => $proveedor->fechabateria,
                    'fechaasignada' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clientebancoid', $idCliente)
                        ->value('fechaasignada'),
                    'horadesde' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clientebancoid', $idCliente)
                        ->value('horadesde'),
                    'horahasta' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clientebancoid', $idCliente)
                        ->value('horahasta'),
                    'tiempoatencion' => $proveedor->tiempoatencion,
                    'accion' => $proveedor->accionnombre,
                    'area' => $proveedor->areanombre,
                    'precio' => $proveedor->precio,
                    'preciocompra' => $proveedor->preciocompra,
                    'programacionid' => Programacionsubcliente::where('accionnombre', $accion)
                        ->where('clientebancoid', $idCliente)
                        ->value('id'),
                ];
            }
        }

        $fechasBateria = BateriaSubCliente::where('clienteid', $idCliente)
            ->distinct()
            ->pluck('fechabateria');


        $accionesPorFechaBateria = [];
        foreach ($fechasBateria as $fecha) {
            $accionesBateria = BateriaSubCliente::where('fechabateria', $fecha)
                ->where('clienteid', $idCliente)
                ->pluck('accionnombre')
                ->toArray();

            $accionesPorFechaBateria[$fecha] = $accionesBateria;
        }

        $accionesDetallesPorFecha = [];
        foreach ($fechasBateria as $fecha) {
            $accionesProgramadas = ProgramacionSubCliente::where('fechabateria', $fecha)
                ->where('clientebancoid', $idCliente)
                ->get(['id', 'accionnombre','proveedornombre', 'fechaasignada', 'horadesde', 'horahasta', 'horahasta', 'precio']);

            foreach ($accionesProgramadas as $accion) {
                $accionesDetallesPorFecha[$fecha][$accion->accionnombre] = $accion;
            }
        }

        return view('admin.asociados.crearprogramacionclientebanco', compact('esProveedor','accionesDetallesPorFecha','accionesPorFechaBateria','fechasBateria','id','rolusuario', 'clientebanco', 'accionesPorFecha', 'proveedoresDetalles', 'accionesRegistradas', 'fechasBateriaPorAccion', 'accionesCliente'));
    }
    public function guardarprogramacionclientebanco(StoreProgramacionsubclienteRequest $request)
    {
        // Recoge las acciones seleccionadas
        $accionesSeleccionadas = $request->input('accionesSeleccionadas', []);
        $horaasignada = $request->input('horaasignada');
        $fechaasignada = $request->input('fechaasignada');
        $clientebancoid = $request->input('clientebancoid');
        $clientebanconombre = $request->input('clientebanconombre');
        $fechabateria = $request->input('fechabateria');
        $horadesde = $request->input('horadesde');
        $horahasta = $request->input('horahasta');

        foreach ($accionesSeleccionadas as $accion) {
            // Sanitiza el nombre de la acción
            $accionSanitizada = str_replace([' ', '.'], ['_', '-'], $accion);
            
            // Captura los datos específicos de cada acción
            $proveedornombre = $request->input("proveedor_$accionSanitizada");
            $areanombre = $request->input("areanombre_$accionSanitizada");
            $precio = $request->input("precio_$accionSanitizada");
            $preciocompra = $request->input("preciocompra_$accionSanitizada");

            // Verifica si ya existe la programación
            $existente = Programacionsubcliente::where('accionnombre', $accion)
                ->where('fechabateria', $fechabateria)
                ->where('clientebancoid', $clientebancoid)
                ->exists();

            // Solo crea un nuevo registro si no existe
            if (!$existente) {
                Programacionsubcliente::create([
                    'accionnombre' => $accion,
                    'horaasignada' => $horaasignada,
                    'fechaasignada' => $fechaasignada,
                    'proveedornombre' => $proveedornombre,
                    'clientebancoid' => $clientebancoid,
                    'clientenombre' => $clientebanconombre,
                    'horadesde' => $horadesde,
                    'horahasta' => $horahasta,
                    'fechabateria' => $fechabateria,
                    'areanombre' => $areanombre,
                    'precio' => $precio,
                    'preciocompra' => $preciocompra,
                    'usuarioid' => Auth::id(), // ID del usuario autenticado
                    'usuarioregistro' => Auth::user()->name, // Nombre del usuario autenticado
                ]);
            }
        }

        return redirect()->route('admin.asociados.crearprogramacionclientebanco', $request->clientebanco)->with('info', 'La programación del cliente se creó con éxito');
    }
    public function reprogramacionclientebanco(ClienteBanco $clientebanco, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarpor');
        $fechas = ProgramacionSubCliente::where('clientebancoid', $clientebanco->id)
                                    ->pluck('fechabateria')
                                    ->unique();
        $programacionsubclientes = collect();
        
        $reprogramaciones = ProgramacionSubCliente::where('clientebancoid', $clientebanco->id)
        ->onlyTrashed()
        ->get();
        $total = 0;
        if ($fechaSeleccionada) {
            $programacionsubclientes = ProgramacionSubCliente::where('clientebancoid', $clientebanco->id)
                                                    ->where('fechabateria', $fechaSeleccionada)
                                                    ->simplePaginate(1000);
            $total = $programacionsubclientes->sum(function ($programacionsubcliente) {
                return str_replace(',', '.', $programacionsubcliente->precio);
            });
            $total = number_format($total, 2, '.', '');
        }


        $id = ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('id');
        return view('admin.asociados.reprogramacionclientebanco', compact('reprogramaciones','programacionsubclientes', 'id', 'clientebanco', 'fechas', 'total', 'fechaSeleccionada'));
    }
    public function buscarprogramacionclientebanco(ClienteBanco $clientebanco, Request $request)
    {
        return $this->reprogramacionclientebanco($clientebanco, $request);
    }
    public function guardarreprogramacionclientebanco(Request $request, Programacionsubcliente $programacionsubcliente)
    {
        $request->validate([
            'motivoreprogramacion' => 'required|string|max:255',
            'usuarioactualizacion' => 'required|string',
        ]);
        $usuarioActualizacion = $request->input('usuarioactualizacion');
        $programacionsubcliente->motivoreprogramacion = $request->motivoreprogramacion;
        $programacionsubcliente->usuarioactualizacion = $usuarioActualizacion;
        $programacionsubcliente->save();

        $programacionsubcliente->delete();

        $clientebanco = ClienteBanco::where('nombrecompleto', $programacionsubcliente->clientenombre)->first();

        return redirect()->route('admin.asociados.reprogramacionclientebanco', $clientebanco)->with('eliminar', 'ok');
    }
    public function estadoprogramacionclientebanco(ClienteBanco $clientebanco, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarpor');
        
        $fechas = Programacionsubcliente::where('clientebancoid', $clientebanco->id)
                                    ->pluck('fechabateria')
                                    ->unique();

        $accionesDisponibles = collect();
        
        if ($fechaSeleccionada) {
            $accionesDisponibles = ProgramacionSubCliente::where('clientebancoid', $clientebanco->id)
                                                    ->where('fechabateria', $fechaSeleccionada)
                                                    ->simplePaginate(1000);
        }
        $nombreCliente = $clientebanco->nombrecompleto;
        $accionesCliente = BateriaSubCliente::where('clientenombre', $nombreCliente)->pluck('accionnombre')->toArray();
        $id = $clientebanco->nombrecompleto ? ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('id') : null;
        $nombreclientebanco = $clientebanco->nombrecompleto ? ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('nombrecompleto') : null;

        $accionesPorArea = Programacionsubcliente::where('clientenombre', $nombreCliente)
            ->get(['accionnombre', 'proveedornombre','fechabateria','fechaasignada', 'horadesde', 'horahasta']);

        $estadoRegistrados = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clientebanconombre', $nombreCliente)
            ->pluck('accionnombre')->toArray();

        $accionesDisponibles = $accionesDisponibles ?? $accionesPorArea;
        
        $accionesRegistradas = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesCliente)
        ->where('clientecomunnombre', $nombreCliente)
        ->pluck('accionnombre')
        ->toArray();

        $fechasEnEstadoCotizacionSubCliente = EstadoCotizacionSubCliente::where('clientebanconombre', $nombreCliente)
        ->distinct()
        ->pluck('fechabateria');

        $fechasBateriaPorAccion = Programacionsubcliente::whereIn('accionnombre', $accionesCliente)
        ->where('clientenombre', $nombreCliente)
        ->whereIn('fechabateria', $fechasEnEstadoCotizacionSubCliente)
        ->distinct()
        ->pluck('fechabateria', 'accionnombre');

        $accionesPorFecha = [];
        foreach ($fechasBateriaPorAccion as $accion => $fecha) {
        $accionesPorFecha[$fecha][] = $accion;
        }
        $id = ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('id');
        return view('admin.asociados.estadoprogramacionclientebanco', compact('fechaSeleccionada', 'id','fechas','nombreclientebanco','accionesDisponibles', 'clientebanco', 'id', 'accionesCliente', 'estadoRegistrados', 'fechasBateriaPorAccion', 'accionesPorFecha', 'accionesRegistradas'));
    }
    public function buscarprogramacionclientesbanco(ClienteBanco $clientebanco, Request $request)
    {
        return $this->estadoprogramacionclientebanco($clientebanco, $request);
    }
    public function guardarestadoprogramacionclientebanco(StoreEstadoprogramacionsubclienteRequest $request)
    {
        $accionNombre = $request->input('accionnombre');

        $estadoprogramacioncliente = Estadoprogramacionsubcliente::create(
            $request->except('accionid') + [
                'accionnombre' => $accionNombre
            ]
        );
        return redirect()->route('admin.asociados.estadoprogramacionclientebanco', $request->clientebanco)->with('info', 'El estado se actualizó con éxito');
    }
//
//CREAR DOCUMENTACION DE CLIENTE BANCO
    public function creardocumentacionclientebanco(ClienteBanco $clientebanco, Asociado $asociado)
    {
        $nombreCliente = $clientebanco->nombrecompleto;
        $IdCliente = $clientebanco->id;

        $accionesCliente = Programacionsubcliente::where('clientebancoid', $IdCliente)
            ->pluck('accionnombre')
            ->unique();

        $accionesRegistradasPorFecha = Documentacionsubcliente::where('clientebancoid', $IdCliente)
            ->get(['accion', 'fechabateria'])
            ->groupBy('fechabateria');

        $accionesNoRegistradasPorFecha = Programacionsubcliente::where('clientebancoid', $IdCliente)
            ->get(['accionnombre', 'fechabateria'])
            ->filter(function($accion) use ($accionesRegistradasPorFecha) {
                $fechabateria = $accion->fechabateria;
                $accionnombre = $accion->accionnombre;
                return !isset($accionesRegistradasPorFecha[$fechabateria]) || !in_array($accionnombre, $accionesRegistradasPorFecha[$fechabateria]->pluck('accion')->toArray());
            })
            ->groupBy('fechabateria');

        $accionesRegistradas = Documentacionsubcliente::whereIn('accion', $accionesCliente)
            ->where('clientebancoid', $IdCliente)
            ->pluck('accion')
            ->toArray();

        $id = $clientebanco->nombrecompleto ? ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('id') : null;

        $fechasBateriaPorAccion = Programacionsubcliente::whereIn('accionnombre', $accionesCliente)
            ->where('clientebancoid', $IdCliente)
            ->get(['accionnombre', 'fechabateria', 'proveedornombre'])
            ->groupBy('fechabateria');
        

        $documentosRegistrados = Documentacionsubcliente::whereIn('accion', $accionesCliente)
            ->where('clientebancoid', $IdCliente)
            ->pluck('accion')->toArray();

        $accionesPorFecha = [];

        foreach ($fechasBateriaPorAccion as $fecha => $acciones) {
            foreach ($acciones as $accion) {
                $accionesPorFecha[$fecha][] = $accion;
            }
        }

        $documentosRegistradosPorFecha = Documentacionsubcliente::where('clientebancoid', $IdCliente)
            ->get(['accion', 'fechabateria'])
            ->groupBy('fechabateria');

        $accionesPorFecha2 = Programacionsubcliente::where('clientebancoid', $IdCliente)
            ->get(['accionnombre', 'fechabateria'])
            ->groupBy('fechabateria');

        $accionesConEstadoPorFecha = [];
        foreach ($accionesPorFecha as $fecha => $acciones) {
            foreach ($acciones as $accion) {
                $registrado = isset($documentosRegistradosPorFecha[$fecha]) && 
                            in_array($accion->accionnombre, $documentosRegistradosPorFecha[$fecha]->pluck('accion')->toArray());

                $documento = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clientebancoid', $IdCliente)
                                                        ->value('document') : null;

                $image = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clientebancoid', $IdCliente)
                                                        ->value('image') : null;

                $image2 = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clientebancoid', $IdCliente)
                                                        ->value('image2') : null;
                $id = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre)
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clientebancoid', $IdCliente)
                                                        ->value('id') : null;

                $creacionregistro = $registrado ? Documentacionsubcliente::where('accion', $accion->accionnombre) 
                                                        ->where('fechabateria', $fecha)
                                                        ->where('clientebancoid', $IdCliente)
                                                        ->value('created_at') : null;
                if ($creacionregistro) {
                    $creacionregistro = \Carbon\Carbon::parse($creacionregistro);
                    $creacionregistroFormatted = $creacionregistro->format('Y-m-d') . ' - ' . $creacionregistro->format('H:i:s');
                } else {
                    $creacionregistroFormatted = null;
                }

                $proveedor = $accion->proveedornombre;

                $accionesConEstadoPorFecha[$fecha][] = [
                    'id' => $id,
                    'accionnombre' => $accion->accionnombre,
                    'proveedornombre' => $proveedor,
                    'registrado' => $registrado,
                    'document' => $documento,
                    'image' => $image,
                    'image2' => $image2,
                    'creacionregistro' => $creacionregistroFormatted
                ];
            }
        }
        return view('admin.asociados.creardocumentacionclientebanco', compact('accionesConEstadoPorFecha','accionesRegistradasPorFecha','accionesNoRegistradasPorFecha','asociado','id', 'clientebanco', 'accionesPorFecha', 'accionesRegistradas', 'fechasBateriaPorAccion', 'accionesCliente', 'documentosRegistrados'));
    }
    public function listadodocumentacionclientebanco(ClienteBanco $clientebanco, Request $request)
    {
        $fechaSeleccionada = $request->get('buscarpor');
        $fechas = Documentacionsubcliente::where('clientebancoid', $clientebanco->id)
                                    ->pluck('fechabateria')
                                    ->unique();
        $documentacionclientes = collect();
        if ($fechaSeleccionada) {
            $documentacionclientes = Documentacionsubcliente::where('clientebancoid', $clientebanco->id)
                                                    ->where('fechabateria', $fechaSeleccionada)
                                                    ->simplePaginate(1000);
        }

        $id = ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('id');
        return view('admin.asociados.listadodocumentacionclientebanco', compact('id','fechas','fechaSeleccionada','clientebanco', 'documentacionclientes'));
    }
    public function buscardocumentoclientebanco(ClienteBanco $clientebanco, Request $request)
    {
        return $this->listadodocumentacionclientebanco($clientebanco, $request);
    }
    public function documentacionmultipleclientebanco(Request $request, Asociado $asociado, ClienteBanco $clientebanco)
    {
        $asociado = $asociado->asociado;
        $proveedor = $request->get('buscarpor');

        $clientes = Programacionsubcliente::where('proveedornombre', 'LIKE', "%$proveedor%")
            ->whereIn('accionnombre', function ($query) use ($proveedor) {
                $query->select('accionnombre')
                    ->from('Estadoprogramacionsubclientes')
                    ->where('proveedornombre', 'LIKE', "%$proveedor%");
            })
            ->whereNotNull('clientebancoid')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('documentacionsubclientes')
                    ->whereRaw('documentacionsubclientes.clientebancoid = Programacionsubclientes.clientebancoid')
                    ->whereRaw('documentacionsubclientes.accion = Programacionsubclientes.accionnombre')
                    ->whereRaw('documentacionsubclientes.fechabateria = Programacionsubclientes.fechabateria');
            })
            ->orderBy('proveedornombre')
            ->simplePaginate(10000);

        return view('admin.asociados.documentacionmultipleclientebanco', compact('clientebanco', 'asociado', 'clientes'));
    }
    public function guardardocumentacionclientebanco(StoreDocumentacionsubclienteRequest $request, ClienteBanco $clientebanco) 
    {
        $archivo_name = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $carpetaCliente = public_path("/documentacionclientesbanco/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }

        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $carpetaCliente = public_path("/documentacionclientesbanco/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name);
        }

        $image_name2 = null;
        if ($request->hasFile('picture2')) {
            $file = $request->file('picture2');
            $carpetaCliente = public_path("/documentacionclientesbanco/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            $image_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name2);
        }

        $nombrecliente = $request->input('nombrecompleto');
        $idcliente = $request->input('clientebancoid');

        // Iterar sobre las acciones seleccionadas (enviadas como array)
        $accionesSeleccionadas = $request->input('acciones', []); // 'acciones' viene de los checkboxes
        
        foreach ($accionesSeleccionadas as $accionId) {
            $accionNombre = Programacionsubcliente::where('id', $accionId)->value('accionnombre');

            // Guardar cada acción con el mismo PDF e imágenes
            Documentacionsubcliente::create(
                $request->except('acciones') + [
                    'document' => $archivo_name,
                    'accion' => $accionId,  // Guardar el ID de la acción
                    'accionnombre' => $accionNombre,  // Guardar el nombre de la acción (opcional)
                    'clientebancoid' => $idcliente,
                    'clientebanconombre' => $nombrecliente,
                    'image' => $image_name,
                    'image2' => $image_name2
                ]
            );
        }

        return redirect()->route('admin.asociados.creardocumentacionclientebanco', $request->clientebanco)->with('info', 'El documento se subió con éxito');
    }
//
//CONTACTOS CLIENTE BANCO
    public function vercontactoclientebanco(ClienteBanco $clientebanco)
    {
        $nombreclienteita = $clientebanco->nombrecompleto;
        $contactos = Contactosubcliente::where('clientebanconombre', $nombreclienteita)
                                ->simplePaginate(10000);

        return view('admin.asociados.vercontactoclientebanco', compact('contactos', 'clientebanco'));
    }
    public function crearcontactoclientebanco(ClienteBanco $clientebanco)
    {
        $parentesco = [
            'ABUEL@' => 'ABUEL@',
            'ESPOS@' => 'ESPOS@',
            'HERMAN@' => 'HERMAN@',
            'HIJ@' => 'HIJ@',
            'MADRE' => 'MADRE',
            'NIET@' => 'NIET@',
            'PADRE' => 'PADRE',
            'PRIM@' => 'PRIM@',
            'SOBRIN@' => 'SOBRIN@',
            'TI@' => 'TI@',
            'UNIÓN LIBRE' => 'UNIÓN LIBRE',
        ];

        $id = $clientebanco->id;

        return view('admin.asociados.crearcontactoclientebanco', compact('id', 'parentesco', 'clientebanco'));
    }
    public function guardarcontactoclientebanco(StoreContactosubclienteRequest $request)
    {
        $clienteID = $request->input('clientebancoid');
        $clientebanco = ClienteBanco::findOrFail($clienteID);

        $clienteData = $request->all();
        $clienteData['clientebanconombre'] = $clientebanco->nombrecompleto;
        $contacto = Contactosubcliente::create($clienteData);
        return redirect()->route('admin.asociados.vercontactoclientebanco', ['clientebanco' => $clientebanco])->with('info', 'El contacto se creó con éxito');
    }
//
//ETIQUETAS Y REQUISITOS CLIENTE BANCO
    public function generaretiquetaclientebanco(Request $request, ClienteBanco $clientebanco)
    {
        $pdf = PDF::loadView('admin.asociados.generaretiquetaclientebanco', compact('clientebanco'));
        $pdfName = 'Etiqueta_' . $clientebanco->id . '.pdf';
        return $pdf->download($pdfName);
    }
    public function generarchecklistclientebanco(ClienteBanco $clientebanco)
    {
        $tieneRequisitos = Requisitosubcliente::where('clientebancoid', $clientebanco->id)
            ->where('servicio', 'INVALIDEZ')->exists();
        $estadoLaboral = strtolower($clientebanco->estadolaboral);
        $numHijosMenores = $clientebanco->numhijosmenores;
        $estadoCivil = strtolower($clientebanco->estadocivil);
        $servicio1 = strtolower($clientebanco->tipocliente);
        $rolusuario = auth()->user()->getRoleNames()->first(); 

        $registroExistente = Estadocotizacionsubcliente::where('clientebancoid', $clientebanco->id)
            ->where('detalle', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS')
            ->first();
        $registroaprobadoExistente = Estadocotizacionsubcliente::where('clientebancoid', $clientebanco->id)
            ->where('detalle', 'APROBADO PARA INICIAR A CREAR BATERIA')
            ->first();

        return view('admin.asociados.generarchecklistclientebanco', compact(
            'clientebanco', 
            'tieneRequisitos', 
            'estadoLaboral',
            'numHijosMenores',  
            'estadoCivil', 
            'registroExistente','rolusuario','registroaprobadoExistente','servicio1'
        ));
    }
    public function descargarchecklistclientebanco(Request $request, ClienteBanco $clientebanco)
    {
        $usuarioAutenticado = Auth::user();
        $documentosSeleccionados = json_decode($request->input('documentosSeleccionados'));

        $requisito = new RequisitoSubCliente();
        $requisito->clientebancoid = $clientebanco->id;
        $requisito->clientebanconombre = $clientebanco->nombrecompleto;
        $requisito->usuarioid = $usuarioAutenticado->id;
        $requisito->usuarioregistro = $usuarioAutenticado->name;

        foreach ($documentosSeleccionados as $documento) {
            $valorDocumento = $request->has($documento) && $request->input($documento) === 'NO' ? 'NO' : 'PENDIENTE';
            switch ($documento) {
                case 'poder':
                    $requisito->poder = $valorDocumento;
                    break;
                case 'avcci':
                    $requisito->avcci = $valorDocumento;
                    break;
                case 'cnacasegurado':
                    $requisito->cnacasegurado = $valorDocumento;
                    break;
                case 'ciasegurado':
                    $requisito->ciasegurado = $valorDocumento;
                    break;
                case 'cmatrimonio':
                    $requisito->cmatrimonio = $valorDocumento;
                    break;
                case 'cnacconyuge':
                    $requisito->cnacconyuge = $valorDocumento;
                    break;
                case 'ciconyuge':
                    $requisito->ciconyuge = $valorDocumento;
                    break;
                case 'cnacjihos':
                    $requisito->cnacjihos = $valorDocumento;
                    break;
                case 'cihijos':
                    $requisito->cihijos = $valorDocumento;
                    break;
                case 'denfaccidente':
                    $requisito->denfaccidente = $valorDocumento;
                    break;
                case 'crodomicilio':
                    $requisito->crodomicilio = $valorDocumento;
                    break;
                case 'contrato':
                    $requisito->contrato = $valorDocumento;
                    break;
                default:
                    break;
            }
        }

        $requisito->save();

        $pdf = PDF::loadView('admin.asociados.descargarchecklistclientebanco', compact('clientebanco', 'documentosSeleccionados'));
        $pdfName = 'Requisitos_' . $clientebanco->nombrecompleto . '.pdf';
        return $pdf->download($pdfName);
    }
    public function subirdocrequisitosclientebanco(ClienteBanco $clientebanco)
    {
        $clienteitaid = $clientebanco->id;

        $requisitosCliente = RequisitoSubCliente::where('clienteitaid', $clienteitaid)->first();

        $poderPendiente = $requisitosCliente ? $requisitosCliente->poder === 'PENDIENTE' : false;
        $avcciPendiente = $requisitosCliente ? $requisitosCliente->avcci === 'PENDIENTE' : false;
        $cnacaseguradoPendiente = $requisitosCliente ? $requisitosCliente->cnacasegurado === 'PENDIENTE' : false;
        $ciaseguradoPendiente = $requisitosCliente ? $requisitosCliente->ciasegurado === 'PENDIENTE' : false;
        $cmatrimonioPendiente = $requisitosCliente ? $requisitosCliente->cmatrimonio === 'PENDIENTE' : false;
        $cnacconyugePendiente = $requisitosCliente ? $requisitosCliente->cnacconyuge === 'PENDIENTE' : false;
        $ciconyugePendiente = $requisitosCliente ? $requisitosCliente->ciconyuge === 'PENDIENTE' : false;
        $cnacjihosPendiente = $requisitosCliente ? $requisitosCliente->cnacjihos === 'PENDIENTE' : false;
        $cihijosPendiente = $requisitosCliente ? $requisitosCliente->cihijos === 'PENDIENTE' : false;
        $denfaccidentePendiente = $requisitosCliente ? $requisitosCliente->denfaccidente === 'PENDIENTE' : false;
        $crodomicilioPendiente = $requisitosCliente ? $requisitosCliente->crodomicilio === 'PENDIENTE' : false;
        $contratoPendiente = $requisitosCliente ? $requisitosCliente->contrato === 'PENDIENTE' : false;
        
        $requisito = RequisitoSubCliente::where('clienteitaid', $cliente->id)->firstOrFail();
        $poderSubido = $requisitosCliente && strpos($requisitosCliente->poder, '.pdf') !== false ? true:false;
        $avcciSubido = $requisitosCliente && strpos($requisitosCliente->avcci, '.pdf') !== false ? true:false;
        $cnacaseguradoSubido = $requisitosCliente && strpos($requisitosCliente->cnacasegurado, '.pdf') !== false ? true:false;
        $ciaseguradoSubido = $requisitosCliente && strpos($requisitosCliente->ciasegurado, '.pdf') !== false ? true:false;
        $cmatrimonioSubido = $requisitosCliente && strpos($requisitosCliente->cmatrimonio, '.pdf') !== false ? true:false;
        $cnacconyugeSubido = $requisitosCliente && strpos($requisitosCliente->cnacconyuge, '.pdf') !== false ? true:false;
        $ciconyugeSubido = $requisitosCliente && strpos($requisitosCliente->ciconyuge, '.pdf') !== false ? true:false;
        $cnacjihosSubido = $requisitosCliente && strpos($requisitosCliente->cnacjihos, '.pdf') !== false ? true:false;
        $cihijosSubido = $requisitosCliente && strpos($requisitosCliente->cihijos, '.pdf') !== false ? true:false;
        $denfaccidenteSubido = $requisitosCliente && strpos($requisitosCliente->denfaccidente, '.pdf') !== false ? true:false;
        $crodomicilioSubido = $requisitosCliente && strpos($requisitosCliente->crodomicilio, '.pdf') !== false ? true:false;
        $contratoSubido = $requisitosCliente && strpos($requisitosCliente->contrato, '.pdf') !== false ? true:false;
        
        return view('admin.asociados.subirdocrequisitos', compact('cliente', 'poderPendiente', 'avcciPendiente', 
        'cnacaseguradoPendiente','ciaseguradoPendiente','cmatrimonioPendiente','cnacconyugePendiente','ciconyugePendiente',
        'cnacjihosPendiente','cihijosPendiente','denfaccidentePendiente','crodomicilioPendiente','contratoPendiente', 'requisito'
        , 'poderSubido', 'avcciSubido', 'cnacaseguradoSubido', 'ciaseguradoSubido', 'cmatrimonioSubido', 'cnacconyugeSubido'
        , 'ciconyugeSubido', 'cnacjihosSubido', 'cihijosSubido', 'denfaccidenteSubido', 'crodomicilioSubido', 'contratoSubido'));
    }
    public function guardardocrequisitosclientebanco(Request $request, ClienteBanco $clientebanco)
    {
        $requisito = RequisitoSubCliente::where('clienteitaid', $clientebanco->id)->firstOrFail();

        $request->validate([
            'poder' => 'nullable|mimes:pdf|max:2048',
            'avcci' => 'nullable|mimes:pdf|max:2048',
            'cnacasegurado' => 'nullable|mimes:pdf|max:2048',
            'ciasegurado' => 'nullable|mimes:pdf|max:2048',
            'cmatrimonio' => 'nullable|mimes:pdf|max:2048',
            'cnacconyuge' => 'nullable|mimes:pdf|max:2048',
            'ciconyuge' => 'nullable|mimes:pdf|max:2048',
            'cnacjihos' => 'nullable|mimes:pdf|max:2048',
            'cihijos' => 'nullable|mimes:pdf|max:2048',
            'denfaccidente' => 'nullable|mimes:pdf|max:2048',
            'crodomicilio' => 'nullable|mimes:pdf|max:2048',
            'contrato' => 'nullable|mimes:pdf|max:2048',
        ]);

        if ($request->hasFile('poder')) {
            $file = $request->file('poder');
            $carpetaCliente = public_path("/requisitosclientesita/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_poder = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_poder);
            $requisito->update(['poder' => $archivo_poder]);
        }

        if ($request->hasFile('avcci')) {
            $file = $request->file('avcci');
            $carpetaCliente = public_path("/requisitosclientesita/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_avcci = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_avcci);
            $requisito->update(['avcci' => $archivo_avcci]);
        }

        if ($request->hasFile('cnacasegurado')) {
            $file = $request->file('cnacasegurado');
            $carpetaCliente = public_path("/requisitosclientesita/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_cnacasegurado = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_cnacasegurado);
            $requisito->update(['cnacasegurado' => $archivo_cnacasegurado]);
        }

        if ($request->hasFile('ciasegurado')) {
            $file = $request->file('ciasegurado');
            $carpetaCliente = public_path("/requisitosclientesita/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_ciasegurado = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_ciasegurado);
            $requisito->update(['ciasegurado' => $archivo_ciasegurado]);
        }

        if ($request->hasFile('cmatrimonio')) {
            $file = $request->file('cmatrimonio');
            $carpetaCliente = public_path("/requisitosclientesita/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_cmatrimonio = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_cmatrimonio);
            $requisito->update(['cmatrimonio' => $archivo_cmatrimonio]);
        }

        if ($request->hasFile('cnacconyuge')) {
            $file = $request->file('cnacconyuge');
            $carpetaCliente = public_path("/requisitosclientesita/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_cnacconyuge = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_cnacconyuge);
            $requisito->update(['cnacconyuge' => $archivo_cnacconyuge]);
        }

        if ($request->hasFile('ciconyuge')) {
            $file = $request->file('ciconyuge');
            $carpetaCliente = public_path("/requisitosclientesita/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_ciconyuge = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_ciconyuge);
            $requisito->update(['ciconyuge' => $archivo_ciconyuge]);
        }

        if ($request->hasFile('cnacjihos')) {
            $file = $request->file('cnacjihos');
            $carpetaCliente = public_path("/requisitosclientesita/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_cnacjihos = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_cnacjihos);
            $requisito->update(['cnacjihos' => $archivo_cnacjihos]);
        }

        if ($request->hasFile('cihijos')) {
            $file = $request->file('cihijos');
            $carpetaCliente = public_path("/requisitosclientesita/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_cihijos = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_cihijos);
            $requisito->update(['cihijos' => $archivo_cihijos]);
        }

        if ($request->hasFile('denfaccidente')) {
            $file = $request->file('denfaccidente');
            $carpetaCliente = public_path("/requisitosclientesita/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_denfaccidente = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_denfaccidente);
            $requisito->update(['denfaccidente' => $archivo_denfaccidente]);
        }

        if ($request->hasFile('crodomicilio')) {
            $file = $request->file('crodomicilio');
            $carpetaCliente = public_path("/requisitosclientesita/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_crodomicilio = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_crodomicilio);
            $requisito->update(['crodomicilio' => $archivo_crodomicilio]);
        }

        if ($request->hasFile('contrato')) {
            $file = $request->file('contrato');
            $carpetaCliente = public_path("/requisitosclientesita/{$clientebanco->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_contrato = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_contrato);
            $requisito->update(['contrato' => $archivo_contrato]);
        }

        return redirect()->route('admin.asociados.subirdocrequisitos', $clientebanco)->with('info', 'El documento se subió con éxito');
    }
    public function generarPDFconsentimientobanco(ClienteBanco $clientebanco, Request $request)
        {
            $nombres = $request->input('nombres');
            $ci = $request->input('ci');
            $fechahoy = date('Y-m-d'); 
            $clientebancoId = $request->input('clientebancoid');
            $sucursalCliente = $request->input('sucursal');

            $nombreArchivo = "Consentimiento_Informado_Inicial {$nombres}.pdf";

            $nombreCompleto = "{$nombres}";

            $usuarioId = auth()->id();
            $usuarioNombre = auth()->user()->name;

            $documentacion = Estadocotizacionsubcliente::create([
                'clientebancoid' => $clientebancoId,
                'clientebanconombre' => $nombreCompleto,
                'detalle' => 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS',
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioNombre,
                'document' => null, 
            ]);

            $data = [
                'nombres' => $nombres,
                'ci' => $ci,
                'fechahoy' => $fechahoy,
            ];

            $pdf = PDF::loadView('admin.asociados.consentimientobanco.pdfconsentimientoclientebanco', $data);

            return $pdf->download($nombreArchivo);
        }
    public function generarsoloPDFconsentimientobanco(Request $request)
        {
            // Obtener los datos del cliente desde el request
            $nombres = $request->input('nombres');
            $apellidoPaterno = $request->input('apepaterno');
            $apellidoMaterno = $request->input('apematerno');
            $ci = $request->input('ci');
            $fechahoy = date('Y-m-d'); 
            $clienteitaId = $request->input('clienteitaid');

            // Generar el nombre del archivo PDF
            $nombreArchivo = "Consentimiento_Informado_Inicial {$nombres} {$apellidoPaterno} {$apellidoMaterno}.pdf";

            // Crear el nombre completo del cliente
            $nombreCompleto = "{$nombres} {$apellidoPaterno} {$apellidoMaterno}";

            // Obtener el ID del usuario autenticado
            $usuarioId = auth()->id();
            $usuarioNombre = auth()->user()->name;

            // Guardar el registro en DocumentacionSubcliente
            $documentacion = Estadocotizacionsubcliente::create([
                'clienteitaid' => $clienteitaId,
                'clienteitanombre' => $nombreCompleto,
                'detalle' => 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS',
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioNombre,
                'document' => null, // Inicialmente sin documento
            ]);


            // Pasar los datos a la vista para generar el PDF
            $data = [
                'nombres' => $nombres,
                'apellidoPaterno' => $apellidoPaterno,
                'apellidoMaterno' => $apellidoMaterno,
                'ci' => $ci,
                'fechahoy' => $fechahoy,
            ];

            // Cargar la vista para el PDF
            $pdf = PDF::loadView('admin.asociados.pdfconsentimientoclientebanco', $data);
            
            // Retornar el PDF con el nombre generado
            return $pdf->download($nombreArchivo);
        }
    public function aprobariniciarcrearbateriabanco(Request $request, Cliente $cliente)
        {
            // Obtener los datos del cliente desde el request
            $nombres = $request->input('nombres');
            $apellidoPaterno = $request->input('apepaterno');
            $apellidoMaterno = $request->input('apematerno');
            $clienteitaId = $request->input('clienteitaid');

            // Crear el nombre completo del cliente
            $nombreCompleto = "{$nombres} {$apellidoPaterno} {$apellidoMaterno}";

            // Obtener el ID del usuario autenticado
            $usuarioId = auth()->id();
            $usuarioNombre = auth()->user()->name;

            // Guardar el registro en DocumentacionSubcliente
            $documentacion = Estadocotizacionsubcliente::create([
                'clienteitaid' => $clienteitaId,
                'clienteitanombre' => $nombreCompleto,
                'detalle' => 'APROBADO PARA INICIAR A CREAR BATERIA',
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioNombre,
                'document' => null, // Inicialmente sin documento
            ]);

            return redirect()->back()->with('info', 'Aprobación exitosa.');
        }
    public function generarPDFguardarconsentimientobanco(Request $request)
        {
            $request->validate(['pdf_file' => 'required|mimes:pdf|max:2048']); // Validar archivo

            // Obtener el clienteitaid y la acción
            $clientebancoId = $request->input('clientebancoid');
            $detalle = $request->input('detalle');

            // Buscar el registro existente
            $documentacion = Estadocotizacionsubcliente::where('clientebancoid', $clientebancoId)
                ->where('detalle', $detalle)
                ->first();

            if ($documentacion) {
                // Guardar el archivo
                $file = $request->file('pdf_file');
                $carpetaCliente = public_path("/cotizacionesaprobadasbanco/{$clientebancoId}");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);
                }
                $archivo_name = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name);

                // Actualizar el campo document en el registro
                $documentacion->document = $archivo_name;
                $documentacion->save();

                return redirect()->back()->with('info', 'PDF guardado exitosamente.');
            }

            return redirect()->back()->with('error', 'Registro no encontrado.');
        }
    public function generarPDFconsentimientoinformadobanco(Request $request)
        {
            // Obtener los datos del cliente desde el request
            $nombres = $request->input('nombres');
            $apellidoPaterno = $request->input('apepaterno');
            $apellidoMaterno = $request->input('apematerno');
            $clienteitaId = $request->input('clienteitaid');
            $ci = $request->input('ci');
            $fechahoy = date('Y-m-d'); 

            // Generar el nombre del archivo PDF
            $nombreArchivo = "Consentimiento_Informado_Evaluaciones_Estudios {$nombres} {$apellidoPaterno} {$apellidoMaterno}.pdf";

            // Crear el nombre completo del cliente
            $nombreCompleto = "{$nombres} {$apellidoPaterno} {$apellidoMaterno}";

            // Obtener el ID del usuario autenticado
            $usuarioId = auth()->id();
            $usuarioNombre = auth()->user()->name;

            /* $documentacion = DocumentacionSubcliente::create([
                'clienteitaid' => $clienteitaId,
                'clienteitanombre' => $nombreCompleto,
                'accion' => 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS',
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioNombre,
                'document' => null, // Inicialmente sin documento
            ]); */

            // Pasar los datos a la vista para generar el PDF
            $data = [
                'nombres' => $nombres,
                'apellidoPaterno' => $apellidoPaterno,
                'apellidoMaterno' => $apellidoMaterno,
                'ci' => $ci,
                'fechahoy' => $fechahoy,
            ];

            // Cargar la vista para el PDF
            $pdf = PDF::loadView('admin.asociados.pdfconsentimientoinformadoclientebanco', $data);
            
            // Retornar el PDF con el nombre generado
            return $pdf->download($nombreArchivo);
        }
//
//CREAR FORMULARIO DE CLIENTE BANCO
    public function crearformularioclientebanco(ClienteBanco $clientebanco)
    {
        return view('admin.asociados.crearformularioclientebanco', compact('clientebanco'));
    }
    public function generarpdfclientebanco(ClienteBanco $clientebanco, Request $request) 
    {
        $request->validate([
            'fechaatencion' => 'date',
            'antecedentespatologicos' => '',
            //IDENTIFICACION DE PELIGROS
                'preguntas.4.respuesta' => 'nullable|string','detpe4' => 'nullable|string',
                'preguntas.5.respuesta' => 'nullable|string','detpe5' => 'nullable|string',
                'preguntas.6.respuesta' => 'nullable|string','detpe6' => 'nullable|string',
                'preguntas.7.respuesta' => 'nullable|string','detpe7' => 'nullable|string',
                'preguntas.8.respuesta' => 'nullable|string','detpe8' => 'nullable|string',
                'preguntas.9.respuesta' => 'nullable|string','detpe9' => 'nullable|string',
                'preguntas.10.respuesta' => 'nullable|string','detpe10' => 'nullable|string',
                'preguntas.11.respuesta' => 'nullable|string','detpe11' => 'nullable|string',
                'otros' => '',
            //
            //OFTALMOLOGIA
                'preguntas.001.respuesta' => 'nullable|string','hacecuanto001' => 'nullable|string','periodotipo001' => 'nullable|string',
                'preguntas.002.respuesta' => 'nullable|string','hacecuanto002' => 'nullable|string','periodotipo002' => 'nullable|string',
                'preguntas.003.respuesta' => 'nullable|string','hacecuanto003' => 'nullable|string','periodotipo003' => 'nullable|string',
                'preguntas.004.respuesta' => 'nullable|string','hacecuanto004' => 'nullable|string','periodotipo004' => 'nullable|string',
                'preguntas.005.respuesta' => 'nullable|string','hacecuanto005' => 'nullable|string','periodotipo005' => 'nullable|string',
                'preguntas.006.respuesta' => 'nullable|string','hacecuanto006' => 'nullable|string','periodotipo006' => 'nullable|string',
            //
            //OTORRINOLARINGOLOGIA
                'preguntas.007.respuesta' => 'nullable|string','hacecuanto007' => 'nullable|string','periodotipo007' => 'nullable|string',
                'preguntas.008.respuesta' => 'nullable|string','hacecuanto008' => 'nullable|string','periodotipo008' => 'nullable|string',
                'preguntas.009.respuesta' => 'nullable|string','hacecuanto009' => 'nullable|string','periodotipo009' => 'nullable|string',
                'preguntas.010.respuesta' => 'nullable|string','hacecuanto010' => 'nullable|string','periodotipo010' => 'nullable|string',
            //
            //NEUROLOGIA
                'preguntas.011.respuesta' => 'nullable|string','hacecuanto011' => 'nullable|string','periodotipo011' => 'nullable|string',
                'preguntas.012.respuesta' => 'nullable|string','hacecuanto012' => 'nullable|string','periodotipo012' => 'nullable|string',
                'preguntas.013.respuesta' => 'nullable|string','hacecuanto013' => 'nullable|string','periodotipo013' => 'nullable|string',
                'preguntas.014.respuesta' => 'nullable|string','hacecuanto014' => 'nullable|string','periodotipo014' => 'nullable|string',
                'preguntas.015.respuesta' => 'nullable|string','hacecuanto015' => 'nullable|string','periodotipo015' => 'nullable|string',
                'preguntas.016.respuesta' => 'nullable|string','hacecuanto016' => 'nullable|string','periodotipo016' => 'nullable|string',
                'preguntas.017.respuesta' => 'nullable|string','hacecuanto017' => 'nullable|string','periodotipo017' => 'nullable|string',
                'preguntas.018.respuesta' => 'nullable|string','hacecuanto018' => 'nullable|string','periodotipo018' => 'nullable|string',
            //
            //CARDIOLOGIA
                'preguntas.019.respuesta' => 'nullable|string','hacecuanto019' => 'nullable|string','periodotipo019' => 'nullable|string',
                'preguntas.020.respuesta' => 'nullable|string','hacecuanto020' => 'nullable|string','periodotipo020' => 'nullable|string',
                'preguntas.021.respuesta' => 'nullable|string','hacecuanto021' => 'nullable|string','periodotipo021' => 'nullable|string',
                'preguntas.022.respuesta' => 'nullable|string','hacecuanto022' => 'nullable|string','periodotipo022' => 'nullable|string',
                'preguntas.023.respuesta' => 'nullable|string','hacecuanto023' => 'nullable|string','periodotipo023' => 'nullable|string',
                'preguntas.024.respuesta' => 'nullable|string','hacecuanto024' => 'nullable|string','periodotipo024' => 'nullable|string',
                'preguntas.025.respuesta' => 'nullable|string','hacecuanto025' => 'nullable|string','periodotipo025' => 'nullable|string',
                'preguntas.026.respuesta' => 'nullable|string','hacecuanto026' => 'nullable|string','periodotipo026' => 'nullable|string',
            //
            //ENDICRONOLOGIA
                'preguntas.027.respuesta' => 'nullable|string','hacecuanto027' => 'nullable|string','periodotipo027' => 'nullable|string',
                'preguntas.028.respuesta' => 'nullable|string','hacecuanto028' => 'nullable|string','periodotipo028' => 'nullable|string',
                'preguntas.029.respuesta' => 'nullable|string','hacecuanto029' => 'nullable|string','periodotipo029' => 'nullable|string',
                'preguntas.030.respuesta' => 'nullable|string','hacecuanto030' => 'nullable|string','periodotipo030' => 'nullable|string',
                'preguntas.031.respuesta' => 'nullable|string','hacecuanto031' => 'nullable|string','periodotipo031' => 'nullable|string',
            //
            //TRAUMATOLOGIA
                'preguntas.032.respuesta' => 'nullable|string','hacecuanto032' => 'nullable|string','periodotipo032' => 'nullable|string',
                'preguntas.033.respuesta' => 'nullable|string','hacecuanto033' => 'nullable|string','periodotipo033' => 'nullable|string',
                'preguntas.034.respuesta' => 'nullable|string','hacecuanto034' => 'nullable|string','periodotipo034' => 'nullable|string',
                'preguntas.035.respuesta' => 'nullable|string','hacecuanto035' => 'nullable|string','periodotipo035' => 'nullable|string',
                'preguntas.036.respuesta' => 'nullable|string','hacecuanto036' => 'nullable|string','periodotipo036' => 'nullable|string',
                'preguntas.037.respuesta' => 'nullable|string','hacecuanto037' => 'nullable|string','periodotipo037' => 'nullable|string',
            //
            //NEUMOLOGIA
                'preguntas.038.respuesta' => 'nullable|string','hacecuanto038' => 'nullable|string','periodotipo038' => 'nullable|string',
                'preguntas.039.respuesta' => 'nullable|string','hacecuanto039' => 'nullable|string','periodotipo039' => 'nullable|string',
                'preguntas.040.respuesta' => 'nullable|string','hacecuanto040' => 'nullable|string','periodotipo040' => 'nullable|string',
                'preguntas.041.respuesta' => 'nullable|string','hacecuanto041' => 'nullable|string','periodotipo041' => 'nullable|string',
                'preguntas.042.respuesta' => 'nullable|string','hacecuanto042' => 'nullable|string','periodotipo042' => 'nullable|string',
            //
            //GASTROENTEROLOGIA
                'preguntas.043.respuesta' => 'nullable|string','hacecuanto043' => 'nullable|string','periodotipo043' => 'nullable|string',
                'preguntas.044.respuesta' => 'nullable|string','hacecuanto044' => 'nullable|string','periodotipo044' => 'nullable|string',
                'preguntas.045.respuesta' => 'nullable|string','hacecuanto045' => 'nullable|string','periodotipo045' => 'nullable|string',
                'preguntas.046.respuesta' => 'nullable|string','hacecuanto046' => 'nullable|string','periodotipo046' => 'nullable|string',
                'preguntas.047.respuesta' => 'nullable|string','hacecuanto047' => 'nullable|string','periodotipo047' => 'nullable|string',
                'preguntas.048.respuesta' => 'nullable|string','hacecuanto048' => 'nullable|string','periodotipo048' => 'nullable|string',
                'preguntas.049.respuesta' => 'nullable|string','hacecuanto049' => 'nullable|string','periodotipo049' => 'nullable|string',
                'preguntas.050.respuesta' => 'nullable|string','hacecuanto050' => 'nullable|string','periodotipo050' => 'nullable|string',
            //
            //UROLOGIA / NEFROLOGIA
                'preguntas.051.respuesta' => 'nullable|string','hacecuanto051' => 'nullable|string','periodotipo051' => 'nullable|string',
                'preguntas.052.respuesta' => 'nullable|string','hacecuanto052' => 'nullable|string','periodotipo052' => 'nullable|string',
                'preguntas.053.respuesta' => 'nullable|string','hacecuanto053' => 'nullable|string','periodotipo053' => 'nullable|string',
                'preguntas.054.respuesta' => 'nullable|string','hacecuanto054' => 'nullable|string','periodotipo054' => 'nullable|string',
            //
            //DERMATOLOGIA
                'preguntas.055.respuesta' => 'nullable|string','hacecuanto055' => 'nullable|string','periodotipo055' => 'nullable|string',
                'preguntas.056.respuesta' => 'nullable|string','hacecuanto056' => 'nullable|string','periodotipo056' => 'nullable|string',
                'preguntas.057.respuesta' => 'nullable|string','hacecuanto057' => 'nullable|string','periodotipo057' => 'nullable|string',
                'preguntas.058.respuesta' => 'nullable|string','hacecuanto058' => 'nullable|string','periodotipo058' => 'nullable|string',
                'preguntas.059.respuesta' => 'nullable|string','hacecuanto059' => 'nullable|string','periodotipo059' => 'nullable|string',
                'preguntas.060.respuesta' => 'nullable|string','hacecuanto060' => 'nullable|string','periodotipo060' => 'nullable|string',
            //
            //CIRUGIA VASCULAR
                'preguntas.061.respuesta' => 'nullable|string','hacecuanto061' => 'nullable|string','periodotipo061' => 'nullable|string',
                'preguntas.062.respuesta' => 'nullable|string','hacecuanto062' => 'nullable|string','periodotipo062' => 'nullable|string',
                'preguntas.063.respuesta' => 'nullable|string','hacecuanto063' => 'nullable|string','periodotipo063' => 'nullable|string',
            //
            //REUMATOLOGIA
                'preguntas.064.respuesta' => 'nullable|string','hacecuanto064' => 'nullable|string','periodotipo064' => 'nullable|string',
                'preguntas.065.respuesta' => 'nullable|string','hacecuanto065' => 'nullable|string','periodotipo065' => 'nullable|string',
                'preguntas.066.respuesta' => 'nullable|string','hacecuanto066' => 'nullable|string','periodotipo066' => 'nullable|string',
                'preguntas.067.respuesta' => 'nullable|string','hacecuanto067' => 'nullable|string','periodotipo067' => 'nullable|string',
                'preguntas.068.respuesta' => 'nullable|string','hacecuanto068' => 'nullable|string','periodotipo068' => 'nullable|string',
                'preguntas.069.respuesta' => 'nullable|string','hacecuanto069' => 'nullable|string','periodotipo069' => 'nullable|string',
                'preguntas.070.respuesta' => 'nullable|string','hacecuanto070' => 'nullable|string','periodotipo070' => 'nullable|string',
                'preguntas.071.respuesta' => 'nullable|string','hacecuanto071' => 'nullable|string','periodotipo071' => 'nullable|string',
            //
            //ONCOLOGIA
                'preguntas.072.respuesta' => 'nullable|string','hacecuanto072' => 'nullable|string','periodotipo072' => 'nullable|string',
            //
            //CIRUGIA GENERAL
                'preguntas.073.respuesta' => 'nullable|string','hacecuanto073' => 'nullable|string','periodotipo073' => 'nullable|string',
                'preguntas.074.respuesta' => 'nullable|string','hacecuanto074' => 'nullable|string','periodotipo074' => 'nullable|string',
            //
            //GINECOLOGIA
                'preguntas.075.respuesta' => 'nullable|string','hacecuanto075' => 'nullable|string','periodotipo075' => 'nullable|string',
                'preguntas.076.respuesta' => 'nullable|string','hacecuanto076' => 'nullable|string','periodotipo076' => 'nullable|string',
                'preguntas.077.respuesta' => 'nullable|string','hacecuanto077' => 'nullable|string','periodotipo077' => 'nullable|string',
                'preguntas.078.respuesta' => 'nullable|string','hacecuanto078' => 'nullable|string','periodotipo078' => 'nullable|string',
                'preguntas.079.respuesta' => 'nullable|string','hacecuanto079' => 'nullable|string','periodotipo079' => 'nullable|string',
            //
            //ANTECEDENTES PATOLOGICOS ADICIONALES
                'fracturas' => '','alergias' => '','transfusiones' => '','intoxicaciones' => '','enfermedadessexual' => '','alteracionvision' => '','alteracionoido' => '','enfermedaddigestivo' => '','enfermedadurogenital' => '',
                //ANTECEDENTES PERSONALES NO PATOLOGICOS
                //CIGARILLOS
                'estadocigarrillos' => '','suspcigarillos' => '','tiemposuspcigarillos' => '','freccigarillos' => '','tiempofreccigarillos' => '','consumocigarillos' => '','tiempoconscigarillos' => '','numerocigarrillos' => '',
                //ALCOHOL
                'estadoalcoholismo' => '','suspensionalcohol' => '','tiemposuspalcohol' => '','frecuenciaalcohol' => '','tiempofrecalcohol' => '','consumoalcohol' => '','tiempoconsalcohol' => '','tipobebida' => '',
                //COCA
                'estadococa' => '','consumococa' => '','tiempoconscoca' => '','frecuenciacoca' => '','tiempofreccoca' => '',
                //MEDICAMENTOS
                'estadomedicamento' => '','cualesmedicamentos' => '',
                //ADICIONAL
                'vivienda' => '','alimentacion' => '','drogas' => '','deporte' => '','catarsis' => '','diuresis' => '','combe' => '',
                //ANTECEDENTES QUIRURGICOS
                'preguntas.100.antecedente' => 'nullable|string','preguntas.100.periodotiempo' => 'nullable|string',
                'preguntas.200.antecedente' => 'nullable|string','preguntas.200.periodotiempo' => 'nullable|string',
                'preguntas.300.antecedente' => 'nullable|string','preguntas.300.periodotiempo' => 'nullable|string',
                //ANTECEDENTES TRAUMATICOS
                'preguntas.1000.antecedente' => 'nullable|string','preguntas.1000.periodotiempo' => 'nullable|string',
                'preguntas.2000.antecedente' => 'nullable|string','preguntas.2000.periodotiempo' => 'nullable|string',
                'preguntas.3000.antecedente' => 'nullable|string','preguntas.3000.periodotiempo' => 'nullable|string',
                //ANTECEDENTES FAMILIARES
                'estadosaludpadre' => '','edadvivopadre' => '','edadfallecidopadre' => '','causafallecidopadre' => '','enfermedadespadre' => '',
                'estadosaludmadre' => '','edadvivomadre' => '','edadfallecemadre' => '','causafallecemadre' => '','enfermedadesmadre' => '',
                'cantidadhermanos' => '','hermanovivo' => '','hermanofallece' => '','caudafallecehermano' => '','enfermedadeshermano' => '',
                'estadosaludesposo' => '','edadvivoesposo' => '','edadfalleceesposo' => '','causafalleceesposo' => '','enfermedadesesposo' => '',
                'cantidadhijos' => '','hijosvivo' => '','hijosfallece' => '','causafallecehijos' => '','enfermedadeshijos' => '',
                //ANTECENTES FAMILIARES ADICIONALES
                'preguntas.30.respuesta' => 'nullable|string','hacecuanto30' => 'nullable|string','periodotipo30' => 'nullable|string',
                'preguntas.31.respuesta' => 'nullable|string','hacecuanto31' => 'nullable|string','periodotipo31' => 'nullable|string',
                'preguntas.32.respuesta' => 'nullable|string','hacecuanto32' => 'nullable|string','periodotipo32' => 'nullable|string',
                'preguntas.33.respuesta' => 'nullable|string','hacecuanto33' => 'nullable|string','periodotipo33' => 'nullable|string',
                'preguntas.34.respuesta' => 'nullable|string','hacecuanto34' => 'nullable|string','periodotipo34' => 'nullable|string',
                'preguntas.35.respuesta' => 'nullable|string','hacecuanto35' => 'nullable|string','periodotipo35' => 'nullable|string',
                'preguntas.36.respuesta' => 'nullable|string','hacecuanto36' => 'nullable|string','periodotipo36' => 'nullable|string',
                'preguntas.37.respuesta' => 'nullable|string','hacecuanto37' => 'nullable|string','periodotipo37' => 'nullable|string',
                'preguntas.38.respuesta' => 'nullable|string','hacecuanto38' => 'nullable|string','periodotipo38' => 'nullable|string',
                'preguntas.39.respuesta' => 'nullable|string','hacecuanto39' => 'nullable|string','periodotipo39' => 'nullable|string',
                'preguntas.40.respuesta' => 'nullable|string','hacecuanto40' => 'nullable|string','periodotipo40' => 'nullable|string',
                'preguntas.41.respuesta' => 'nullable|string','hacecuanto41' => 'nullable|string','periodotipo41' => 'nullable|string',
                //ANTECEDENTES LABORALES
                'fechainicioatclab' => '','fechafinalatclab' => '',
                'preguntas.1.carac' => 'nullable|string','preguntas.1.denun' => 'nullable|string','preguntas.1.aten' => 'nullable|string',
                'preguntas.2.carac' => 'nullable|string','preguntas.2.denun' => 'nullable|string','preguntas.2.aten' => 'nullable|string',
                'preguntas.3.carac' => 'nullable|string','preguntas.3.denun' => 'nullable|string','preguntas.3.aten' => 'nullable|string',
                //HISTORIA DE LA ENFERMEDAD ACTUAL
                'historiaenfermedad' => '',
                //EXAMEN FISICO
                'examenfisicogeneral' => '','llenadocapilar' => '','lateralidad' => '',
                'pulso' => '','satO2' => '','frespiracion' => '','temperatura' => '','presionarterial' => '',
                'agudezavisual' => '','usalentes' => '',
                'peso' => '','estatura' => '','imc' => '',
                //EXAMEN FISICO SEGMENTADO
                'exficabeza' => '','exfiojos' => '','exfinariz' => '','exfioidos' => '','exfiboca' => '','exficuello' => '','exfitorax' => '','exficorazon' => '','exfipulmones' => '',
                'exfiabdomen' => '','exfiextremidadesmmss' => '','exfiextremidadesmmii' => '','exfineurologico' => '','exfivestibulocereboloso' => '','exfimarcha' => '','exficraneoycolumna' => '','exfiexploracionneuro' => '',
            //
        ]);

        //IDENTIFICACION DE PELIGROS
            Session::put('fechaatencion', $request->fechaatencion);
            Session::put('antecedentespatologicos', $request->antecedentespatologicos);
            Session::put('peligrosfisicos', $request->input('preguntas.4.respuesta'));
            Session::put('descripcionpeligrosfisicos', $request->input('detpe4'));
            Session::put('peligrosquimicos', $request->input('preguntas.5.respuesta'));
            Session::put('descripcionpeligrosquimicos', $request->input('detpe5'));
            Session::put('peligrosergonomicos', $request->input('preguntas.6.respuesta'));
            Session::put('descripcionpeligrosergonomicos', $request->input('detpe6'));
            Session::put('peligrosepps', $request->input('preguntas.7.respuesta'));
            Session::put('descripcionpeligrosepps', $request->input('detpe7'));
            Session::put('peligrosbiologicos', $request->input('preguntas.8.respuesta'));
            Session::put('descripcionpeligrosbiologicos', $request->input('detpe8'));
            Session::put('peligrosmecanicos', $request->input('preguntas.9.respuesta'));
            Session::put('descripcionpeligrosmecanicos', $request->input('detpe9'));
            Session::put('peligrosambientales', $request->input('preguntas.10.respuesta'));
            Session::put('descripcionpeligrosambientales', $request->input('detpe10'));
            Session::put('peligrospsicosociales', $request->input('preguntas.11.respuesta'));
            Session::put('descripcionpeligrospsicosociales', $request->input('detpe11'));
            Session::put('otros', $request->otros);
            //OFTALMOLOGIA
            Session::put('cefalea', $request->input('preguntas.001.respuesta'));
            Session::put('hacecuanto001', $request->input('hacecuanto001'));
            Session::put('periodotipo001', $request->input('periodotipo001'));
            Session::put('defectovisual', $request->input('preguntas.002.respuesta'));
            Session::put('hacecuanto002', $request->input('hacecuanto002'));
            Session::put('periodotipo002', $request->input('periodotipo002'));
            Session::put('irritacionocular', $request->input('preguntas.003.respuesta'));
            Session::put('hacecuanto003', $request->input('hacecuanto003'));
            Session::put('periodotipo003', $request->input('periodotipo003'));
            Session::put('sequedadocular', $request->input('preguntas.004.respuesta'));
            Session::put('hacecuanto004', $request->input('hacecuanto004'));
            Session::put('periodotipo004', $request->input('periodotipo004'));
            Session::put('lagrimeo', $request->input('preguntas.005.respuesta'));
            Session::put('hacecuanto005', $request->input('hacecuanto005'));
            Session::put('periodotipo005', $request->input('periodotipo005'));
            Session::put('visionborrosa', $request->input('preguntas.006.respuesta'));
            Session::put('hacecuanto006', $request->input('hacecuanto006'));
            Session::put('periodotipo006', $request->input('periodotipo006'));
            //OTORRINOLARINGOLOGIA
            Session::put('hipoacuasia', $request->input('preguntas.007.respuesta'));
            Session::put('hacecuanto007', $request->input('hacecuanto007'));
            Session::put('periodotipo007', $request->input('periodotipo007'));
            Session::put('otitismedia', $request->input('preguntas.008.respuesta'));
            Session::put('hacecuanto008', $request->input('hacecuanto008'));
            Session::put('periodotipo008', $request->input('periodotipo008'));
            Session::put('sinusitis', $request->input('preguntas.009.respuesta'));
            Session::put('hacecuanto009', $request->input('hacecuanto009'));
            Session::put('periodotipo009', $request->input('periodotipo009'));
            Session::put('tinitus', $request->input('preguntas.010.respuesta'));
            Session::put('hacecuanto010', $request->input('hacecuanto010'));
            Session::put('periodotipo010', $request->input('periodotipo010'));
            //NEUROLOGIA
            Session::put('convulsiones', $request->input('preguntas.011.respuesta'));
            Session::put('hacecuanto011', $request->input('hacecuanto011'));
            Session::put('periodotipo011', $request->input('periodotipo011'));
            Session::put('epilepsia', $request->input('preguntas.012.respuesta'));
            Session::put('hacecuanto012', $request->input('hacecuanto012'));
            Session::put('periodotipo012', $request->input('periodotipo012'));
            Session::put('lumbalgia', $request->input('preguntas.013.respuesta'));
            Session::put('hacecuanto013', $request->input('hacecuanto013'));
            Session::put('periodotipo013', $request->input('periodotipo013'));
            Session::put('neuropatia', $request->input('preguntas.014.respuesta'));
            Session::put('hacecuanto014', $request->input('hacecuanto014'));
            Session::put('periodotipo014', $request->input('periodotipo014'));
            Session::put('acv', $request->input('preguntas.015.respuesta'));
            Session::put('hacecuanto015', $request->input('hacecuanto015'));
            Session::put('periodotipo015', $request->input('periodotipo015'));
            Session::put('cefaleaneurologia', $request->input('preguntas.016.respuesta'));
            Session::put('hacecuanto016', $request->input('hacecuanto016'));
            Session::put('periodotipo016', $request->input('periodotipo016'));
            Session::put('disformiamuscular', $request->input('preguntas.017.respuesta'));
            Session::put('hacecuanto017', $request->input('hacecuanto017'));
            Session::put('periodotipo017', $request->input('periodotipo017'));
            Session::put('lesionmedulaespinal', $request->input('preguntas.018.respuesta'));
            Session::put('hacecuanto018', $request->input('hacecuanto018'));
            Session::put('periodotipo018', $request->input('periodotipo018'));
            //CARDIOLOGIA
            Session::put('hta', $request->input('preguntas.019.respuesta'));
            Session::put('hacecuanto019', $request->input('hacecuanto019'));
            Session::put('periodotipo019', $request->input('periodotipo019'));
            Session::put('arritmia', $request->input('preguntas.020.respuesta'));
            Session::put('hacecuanto020', $request->input('hacecuanto020'));
            Session::put('periodotipo020', $request->input('periodotipo020'));
            Session::put('chagas', $request->input('preguntas.021.respuesta'));
            Session::put('hacecuanto021', $request->input('hacecuanto021'));
            Session::put('periodotipo021', $request->input('periodotipo021'));
            Session::put('taquicardia', $request->input('preguntas.022.respuesta'));
            Session::put('hacecuanto022', $request->input('hacecuanto022'));
            Session::put('periodotipo022', $request->input('periodotipo022'));
            Session::put('bradicardia', $request->input('preguntas.023.respuesta'));
            Session::put('hacecuanto023', $request->input('hacecuanto023'));
            Session::put('periodotipo023', $request->input('periodotipo023'));
            Session::put('bloqueoderama', $request->input('preguntas.024.respuesta'));
            Session::put('hacecuanto024', $request->input('hacecuanto024'));
            Session::put('periodotipo024', $request->input('periodotipo024'));
            Session::put('stentcoronario', $request->input('preguntas.025.respuesta'));
            Session::put('hacecuanto025', $request->input('hacecuanto025'));
            Session::put('periodotipo025', $request->input('periodotipo025'));
            Session::put('marcapaso', $request->input('preguntas.026.respuesta'));
            Session::put('hacecuanto026', $request->input('hacecuanto026'));
            Session::put('periodotipo026', $request->input('periodotipo026'));
            //ENDICRONOLOGIA
            Session::put('dmt2', $request->input('preguntas.027.respuesta'));
            Session::put('hacecuanto027', $request->input('hacecuanto027'));
            Session::put('periodotipo027', $request->input('periodotipo027'));
            Session::put('lupuseritematoso', $request->input('preguntas.028.respuesta'));
            Session::put('hacecuanto028', $request->input('hacecuanto028'));
            Session::put('periodotipo028', $request->input('periodotipo028'));
            Session::put('colesterolelevado', $request->input('preguntas.029.respuesta'));
            Session::put('hacecuanto029', $request->input('hacecuanto029'));
            Session::put('periodotipo029', $request->input('periodotipo029'));
            Session::put('hipotiroidismo', $request->input('preguntas.030.respuesta'));
            Session::put('hacecuanto030', $request->input('hacecuanto030'));
            Session::put('periodotipo030', $request->input('periodotipo030'));
            Session::put('hipertiroidismo', $request->input('preguntas.031.respuesta'));
            Session::put('hacecuanto031', $request->input('hacecuanto031'));
            Session::put('periodotipo031', $request->input('periodotipo031'));
            //TRAUMATOLOGIA
            Session::put('artritis', $request->input('preguntas.032.respuesta'));
            Session::put('hacecuanto032', $request->input('hacecuanto032'));
            Session::put('periodotipo032', $request->input('periodotipo032'));
            Session::put('doloresarticulares', $request->input('preguntas.033.respuesta'));
            Session::put('hacecuanto033', $request->input('hacecuanto033'));
            Session::put('periodotipo033', $request->input('periodotipo033'));
            Session::put('lumbalgia', $request->input('preguntas.034.respuesta'));
            Session::put('hacecuanto034', $request->input('hacecuanto034'));
            Session::put('periodotipo034', $request->input('periodotipo034'));
            Session::put('cervicalgia', $request->input('preguntas.035.respuesta'));
            Session::put('hacecuanto035', $request->input('hacecuanto035'));
            Session::put('periodotipo035', $request->input('periodotipo035'));
            Session::put('dorsalgia', $request->input('preguntas.036.respuesta'));
            Session::put('hacecuanto036', $request->input('hacecuanto036'));
            Session::put('periodotipo036', $request->input('periodotipo036'));
            Session::put('silicosis', $request->input('preguntas.037.respuesta'));
            Session::put('hacecuanto037', $request->input('hacecuanto037'));
            Session::put('periodotipo037', $request->input('periodotipo037'));
            //NEUMOLOGIA
            Session::put('bronquitis', $request->input('preguntas.038.respuesta'));
            Session::put('hacecuanto038', $request->input('hacecuanto038'));
            Session::put('periodotipo038', $request->input('periodotipo038'));
            Session::put('asma', $request->input('preguntas.039.respuesta'));
            Session::put('hacecuanto039', $request->input('hacecuanto039'));
            Session::put('periodotipo039', $request->input('periodotipo039'));
            Session::put('tuberculosis', $request->input('preguntas.040.respuesta'));
            Session::put('hacecuanto040', $request->input('hacecuanto040'));
            Session::put('periodotipo040', $request->input('periodotipo040'));
            Session::put('epoc', $request->input('preguntas.041.respuesta'));
            Session::put('hacecuanto041', $request->input('hacecuanto041'));
            Session::put('periodotipo041', $request->input('periodotipo041'));
            Session::put('enfisemapulmonar', $request->input('preguntas.042.respuesta'));
            Session::put('hacecuanto042', $request->input('hacecuanto042'));
            Session::put('periodotipo042', $request->input('periodotipo042'));
            //GASTROENTEROLOGIA
            Session::put('gastritis', $request->input('preguntas.043.respuesta'));
            Session::put('hacecuanto043', $request->input('hacecuanto043'));
            Session::put('periodotipo043', $request->input('periodotipo043'));
            Session::put('enfacidopeptica', $request->input('preguntas.044.respuesta'));
            Session::put('hacecuanto044', $request->input('hacecuanto044'));
            Session::put('periodotipo044', $request->input('periodotipo044'));
            Session::put('colonirritable', $request->input('preguntas.045.respuesta'));
            Session::put('hacecuanto045', $request->input('hacecuanto045'));
            Session::put('periodotipo045', $request->input('periodotipo045'));
            Session::put('cololetiasis', $request->input('preguntas.046.respuesta'));
            Session::put('hacecuanto046', $request->input('hacecuanto046'));
            Session::put('periodotipo046', $request->input('periodotipo046'));
            Session::put('distencion', $request->input('preguntas.047.respuesta'));
            Session::put('hacecuanto047', $request->input('hacecuanto047'));
            Session::put('periodotipo047', $request->input('periodotipo047'));
            Session::put('calculosbiliares', $request->input('preguntas.048.respuesta'));
            Session::put('hacecuanto048', $request->input('hacecuanto048'));
            Session::put('periodotipo048', $request->input('periodotipo048'));
            Session::put('ulceraintestinal', $request->input('preguntas.049.respuesta'));
            Session::put('hacecuanto049', $request->input('hacecuanto049'));
            Session::put('periodotipo049', $request->input('periodotipo049'));
            Session::put('hepatitis', $request->input('preguntas.050.respuesta'));
            Session::put('hacecuanto050', $request->input('hacecuanto050'));
            Session::put('periodotipo050', $request->input('periodotipo050'));
            //UROLOGIA / NEFROLOGIA
            Session::put('urolitiasis', $request->input('preguntas.051.respuesta'));
            Session::put('hacecuanto051', $request->input('hacecuanto051'));
            Session::put('periodotipo051', $request->input('periodotipo051'));
            Session::put('infeccionurinaria', $request->input('preguntas.052.respuesta'));
            Session::put('hacecuanto052', $request->input('hacecuanto052'));
            Session::put('periodotipo052', $request->input('periodotipo052'));
            Session::put('prostatitis', $request->input('preguntas.053.respuesta'));
            Session::put('hacecuanto053', $request->input('hacecuanto053'));
            Session::put('periodotipo053', $request->input('periodotipo053'));
            Session::put('varicocele', $request->input('preguntas.054.respuesta'));
            Session::put('hacecuanto054', $request->input('hacecuanto054'));
            Session::put('periodotipo054', $request->input('periodotipo054'));
            //DERMATOLOGIA
            Session::put('dermatitis', $request->input('preguntas.055.respuesta'));
            Session::put('hacecuanto055', $request->input('hacecuanto055'));
            Session::put('periodotipo055', $request->input('periodotipo055'));
            Session::put('lupuseritematosoder', $request->input('preguntas.056.respuesta'));
            Session::put('hacecuanto056', $request->input('hacecuanto056'));
            Session::put('periodotipo056', $request->input('periodotipo056'));
            Session::put('vitiligo', $request->input('preguntas.057.respuesta'));
            Session::put('hacecuanto057', $request->input('hacecuanto057'));
            Session::put('periodotipo057', $request->input('periodotipo057'));
            Session::put('eccema', $request->input('preguntas.058.respuesta'));
            Session::put('hacecuanto058', $request->input('hacecuanto058'));
            Session::put('periodotipo058', $request->input('periodotipo058'));
            Session::put('impetigo', $request->input('preguntas.059.respuesta'));
            Session::put('hacecuanto059', $request->input('hacecuanto059'));
            Session::put('periodotipo059', $request->input('periodotipo059'));
            Session::put('psoriasis', $request->input('preguntas.060.respuesta'));
            Session::put('hacecuanto060', $request->input('hacecuanto060'));
            Session::put('periodotipo060', $request->input('periodotipo060'));
            //CIRUGIA VASCULAR
            Session::put('varicesenpiernas', $request->input('preguntas.061.respuesta'));
            Session::put('hacecuanto061', $request->input('hacecuanto061'));
            Session::put('periodotipo061', $request->input('periodotipo061'));
            Session::put('celulitisenmmii', $request->input('preguntas.062.respuesta'));
            Session::put('hacecuanto062', $request->input('hacecuanto062'));
            Session::put('periodotipo062', $request->input('periodotipo062'));
            Session::put('trombosis', $request->input('preguntas.063.respuesta'));
            Session::put('hacecuanto063', $request->input('hacecuanto063'));
            Session::put('periodotipo063', $request->input('periodotipo063'));
            //REUMATOLOGIA
            Session::put('artritisreumatoidea', $request->input('preguntas.064.respuesta'));
            Session::put('hacecuanto064', $request->input('hacecuanto064'));
            Session::put('periodotipo064', $request->input('periodotipo064'));
            Session::put('artrosisreu', $request->input('preguntas.065.respuesta'));
            Session::put('hacecuanto065', $request->input('hacecuanto065'));
            Session::put('periodotipo065', $request->input('periodotipo065'));
            Session::put('psoriasisreu', $request->input('preguntas.066.respuesta'));
            Session::put('hacecuanto066', $request->input('hacecuanto066'));
            Session::put('periodotipo066', $request->input('periodotipo066'));
            Session::put('lupuseritematosoreu', $request->input('preguntas.067.respuesta'));
            Session::put('hacecuanto067', $request->input('hacecuanto067'));
            Session::put('periodotipo067', $request->input('periodotipo067'));
            Session::put('gota', $request->input('preguntas.068.respuesta'));
            Session::put('hacecuanto068', $request->input('hacecuanto068'));
            Session::put('periodotipo068', $request->input('periodotipo068'));
            Session::put('espondilitisanquilosante', $request->input('preguntas.069.respuesta'));
            Session::put('hacecuanto069', $request->input('hacecuanto069'));
            Session::put('periodotipo069', $request->input('periodotipo069'));
            Session::put('fibromialgia', $request->input('preguntas.070.respuesta'));
            Session::put('hacecuanto070', $request->input('hacecuanto070'));
            Session::put('periodotipo070', $request->input('periodotipo070'));
            Session::put('reumatismo', $request->input('preguntas.071.respuesta'));
            Session::put('hacecuanto071', $request->input('hacecuanto071'));
            Session::put('periodotipo071', $request->input('periodotipo071'));
            //ONCOLOGIA
            Session::put('cancer', $request->input('preguntas.072.respuesta'));
            Session::put('hacecuanto072', $request->input('hacecuanto072'));
            Session::put('periodotipo072', $request->input('periodotipo072'));
            //CIRUGIA GENERAL
            Session::put('herniainguinal', $request->input('preguntas.073.respuesta'));
            Session::put('hacecuanto073', $request->input('hacecuanto073'));
            Session::put('periodotipo073', $request->input('periodotipo073'));
            Session::put('herniaumbilical', $request->input('preguntas.074.respuesta'));
            Session::put('hacecuanto074', $request->input('hacecuanto074'));
            Session::put('periodotipo074', $request->input('periodotipo074'));
            //GINECOLOGIA
            Session::put('endometriosis', $request->input('preguntas.075.respuesta'));
            Session::put('hacecuanto075', $request->input('hacecuanto075'));
            Session::put('periodotipo075', $request->input('periodotipo075'));
            Session::put('miomasuterinos', $request->input('preguntas.076.respuesta'));
            Session::put('hacecuanto076', $request->input('hacecuanto076'));
            Session::put('periodotipo076', $request->input('periodotipo076'));
            Session::put('poliposuterinos', $request->input('preguntas.077.respuesta'));
            Session::put('hacecuanto077', $request->input('hacecuanto077'));
            Session::put('periodotipo077', $request->input('periodotipo077'));
            Session::put('quistesdeovarios', $request->input('preguntas.078.respuesta'));
            Session::put('hacecuanto078', $request->input('hacecuanto078'));
            Session::put('periodotipo078', $request->input('periodotipo078'));
            Session::put('prolapsogenital', $request->input('preguntas.079.respuesta'));
            Session::put('hacecuanto079', $request->input('hacecuanto079'));
            Session::put('periodotipo079', $request->input('periodotipo079'));
            //ANTECEDENTES PATOLOGICOS ADICIONALES
            Session::put('fracturas', $request->fracturas);
            Session::put('alergias', $request->alergias);
            Session::put('transfusiones', $request->transfusiones);
            Session::put('intoxicaciones', $request->intoxicaciones);
            Session::put('enfermedadessexual', $request->enfermedadessexual);
            Session::put('alteracionvision', $request->alteracionvision);
            Session::put('alteracionoido', $request->alteracionoido);
            Session::put('enfermedaddigestivo', $request->enfermedaddigestivo);
            Session::put('enfermedadurogenital', $request->enfermedadurogenital);
            //ANTECEDENTES PERSONALES NO PATOLOGICOS
            //CIGARRILLOS
            Session::put('estadocigarrillos', $request->estadocigarrillos);
            Session::put('suspcigarillos', $request->suspcigarillos);
            Session::put('tiemposuspcigarillos', $request->tiemposuspcigarillos);
            Session::put('freccigarillos', $request->freccigarillos);
            Session::put('tiempofreccigarillos', $request->tiempofreccigarillos);
            Session::put('consumocigarillos', $request->consumocigarillos);
            Session::put('tiempoconscigarillos', $request->tiempoconscigarillos);
            Session::put('numerocigarrillos', $request->numerocigarrillos);
            //ALCOHOL
            Session::put('estadoalcoholismo', $request->estadoalcoholismo);
            Session::put('suspensionalcohol', $request->suspensionalcohol);
            Session::put('tiemposuspalcohol', $request->tiemposuspalcohol);
            Session::put('frecuenciaalcohol', $request->frecuenciaalcohol);
            Session::put('tiempofrecalcohol', $request->tiempofrecalcohol);
            Session::put('consumoalcohol', $request->consumoalcohol);
            Session::put('tiempoconsalcohol', $request->tiempoconsalcohol);
            Session::put('tipobebida', $request->tipobebida);
            //COCA
            Session::put('estadococa', $request->estadococa);
            Session::put('consumococa', $request->consumococa);
            Session::put('tiempoconscoca', $request->tiempoconscoca);
            Session::put('frecuenciacoca', $request->frecuenciacoca);
            Session::put('tiempofreccoca', $request->tiempofreccoca);
            //MEDICAMENTOS
            Session::put('estadomedicamento', $request->estadomedicamento);
            Session::put('cualesmedicamentos', $request->cualesmedicamentos);
            //ADICIONAL
            Session::put('vivienda', $request->vivienda);
            Session::put('alimentacion', $request->alimentacion);
            Session::put('drogas', $request->drogas);
            Session::put('deporte', $request->deporte);
            Session::put('catarsis', $request->catarsis);
            Session::put('diuresis', $request->diuresis);
            Session::put('combe', $request->combe);
            //ANTECEDENTES QUIRUGICOS
            Session::put('atcquirurgico1', $request->input('preguntas.100.antecedente'));
            Session::put('atcperiodo1', $request->input('preguntas.100.periodotiempo'));
            Session::put('atcquirurgico2', $request->input('preguntas.200.antecedente'));
            Session::put('atcperiodo2', $request->input('preguntas.200.periodotiempo'));
            Session::put('atcquirurgico3', $request->input('preguntas.300.antecedente'));
            Session::put('atcperiodo3', $request->input('preguntas.300.periodotiempo'));
            //ANTECEDENTES TRAUMATICOS
            Session::put('atctrau1', $request->input('preguntas.100.antecedente'));
            Session::put('atctrauperiodo1', $request->input('preguntas.1000.periodotiempo'));
            Session::put('atctrau2', $request->input('preguntas.200.antecedente'));
            Session::put('atctrauperiodo2', $request->input('preguntas.2000.periodotiempo'));
            Session::put('atctrau3', $request->input('preguntas.300.antecedente'));
            Session::put('atctrauperiodo3', $request->input('preguntas.3000.periodotiempo'));
            //ANTECEDENTES FAMILIARES
            Session::put('estadosaludpadre', $request->estadosaludpadre);
            Session::put('edadvivopadre', $request->edadvivopadre);
            Session::put('edadfallecidopadre', $request->edadfallecidopadre);
            Session::put('causafallecidopadre', $request->causafallecidopadre);
            Session::put('enfermedadespadre', $request->enfermedadespadre);
            Session::put('estadosaludmadre', $request->estadosaludmadre);
            Session::put('edadvivomadre', $request->edadvivomadre);
            Session::put('edadfallecemadre', $request->edadfallecemadre);
            Session::put('causafallecemadre', $request->causafallecemadre);
            Session::put('enfermedadesmadre', $request->enfermedadesmadre);
            Session::put('cantidadhermanos', $request->cantidadhermanos);
            Session::put('hermanovivo', $request->hermanovivo);
            Session::put('hermanofallece', $request->hermanofallece);
            Session::put('caudafallecehermano', $request->caudafallecehermano);
            Session::put('enfermedadeshermano', $request->enfermedadeshermano);
            Session::put('estadosaludesposo', $request->estadosaludesposo);
            Session::put('edadvivoesposo', $request->edadvivoesposo);
            Session::put('edadfalleceesposo', $request->edadfalleceesposo);
            Session::put('causafalleceesposo', $request->causafalleceesposo);
            Session::put('enfermedadesesposo', $request->enfermedadesesposo);
            Session::put('cantidadhijos', $request->cantidadhijos);
            Session::put('hijosvivo', $request->hijosvivo);
            Session::put('hijosfallece', $request->hijosfallece);
            Session::put('causafallecehijos', $request->causafallecehijos);
            Session::put('enfermedadeshijos', $request->enfermedadeshijos);
            //ANTECEDENTES FAMILIARES ADICIONALES
            Session::put('afhta', $request->input('preguntas.30.respuesta'));
            Session::put('hacecuanto30', $request->input('hacecuanto30'));
            Session::put('periodotipo30', $request->input('periodotipo30'));
            Session::put('afinfarto', $request->input('preguntas.31.respuesta'));
            Session::put('hacecuanto31', $request->input('hacecuanto31'));
            Session::put('periodotipo31', $request->input('periodotipo31'));
            Session::put('afacv', $request->input('preguntas.32.respuesta'));
            Session::put('hacecuanto32', $request->input('hacecuanto32'));
            Session::put('periodotipo32', $request->input('periodotipo32'));
            Session::put('afalergias', $request->input('preguntas.33.respuesta'));
            Session::put('hacecuanto33', $request->input('hacecuanto33'));
            Session::put('periodotipo33', $request->input('periodotipo33'));
            Session::put('afulcerapeptica', $request->input('preguntas.34.respuesta'));
            Session::put('hacecuanto34', $request->input('hacecuanto34'));
            Session::put('periodotipo34', $request->input('periodotipo34'));
            Session::put('afdiabetes', $request->input('preguntas.35.respuesta'));
            Session::put('hacecuanto35', $request->input('hacecuanto35'));
            Session::put('periodotipo35', $request->input('periodotipo35'));
            Session::put('afasma', $request->input('preguntas.36.respuesta'));
            Session::put('hacecuanto36', $request->input('hacecuanto36'));
            Session::put('periodotipo36', $request->input('periodotipo36'));
            Session::put('aftbc', $request->input('preguntas.37.respuesta'));
            Session::put('hacecuanto37', $request->input('hacecuanto37'));
            Session::put('periodotipo37', $request->input('periodotipo37'));
            Session::put('afartritis', $request->input('preguntas.38.respuesta'));
            Session::put('hacecuanto38', $request->input('hacecuanto38'));
            Session::put('periodotipo38', $request->input('periodotipo38'));
            Session::put('afenfermedadmental', $request->input('preguntas.39.respuesta'));
            Session::put('hacecuanto39', $request->input('hacecuanto39'));
            Session::put('periodotipo39', $request->input('periodotipo39'));
            Session::put('afcancer', $request->input('preguntas.40.respuesta'));
            Session::put('hacecuanto40', $request->input('hacecuanto40'));
            Session::put('periodotipo40', $request->input('periodotipo40'));
            Session::put('afotros', $request->input('preguntas.41.respuesta'));
            Session::put('hacecuanto41', $request->input('hacecuanto41'));
            Session::put('periodotipo41', $request->input('periodotipo41'));
            //ANTECEDENTES LABORALES
            Session::put('fechainicioatclab', $request->fechainicioatclab);
            Session::put('fechafinalatclab', $request->fechafinalatclab);
            Session::put('caracatclaboral1', $request->input('preguntas.1.carac'));
            Session::put('denunatclaboral1', $request->input('preguntas.1.denun'));
            Session::put('atenatclaboral1', $request->input('preguntas.1.aten'));
            Session::put('caracatclaboral2', $request->input('preguntas.2.carac'));
            Session::put('denunatclaboral2', $request->input('preguntas.2.denun'));
            Session::put('atenatclaboral2', $request->input('preguntas.2.aten'));
            Session::put('caracatclaboral3', $request->input('preguntas.3.carac'));
            Session::put('denunatclaboral3', $request->input('preguntas.3.denun'));
            Session::put('atenatclaboral3', $request->input('preguntas.3.aten'));
            //HISTORIA DE LA ENFERMEDAD ACTUAL
            Session::put('historiaenfermedad', $request->historiaenfermedad);
            //SIGNOS VITALES
            Session::put('examenfisicogeneral', $request->examenfisicogeneral);
            Session::put('llenadocapilar', $request->llenadocapilar);
            Session::put('lateralidad', $request->lateralidad);
            Session::put('pulso', $request->pulso);
            Session::put('satO2', $request->satO2);
            Session::put('frespiracion', $request->frespiracion);
            Session::put('temperatura', $request->temperatura);
            Session::put('presionarterial', $request->presionarterial);
            Session::put('agudezavisual', $request->agudezavisual);
            Session::put('usalentes', $request->usalentes);
            Session::put('peso', $request->peso);
            Session::put('estatura', $request->estatura);
            Session::put('imc', $request->imc);
            //EXAMEN FISICO SEGMENTADO
            Session::put('exficabeza', $request->exficabeza);
            Session::put('exfiojos', $request->exfiojos);
            Session::put('exfinariz', $request->exfinariz);
            Session::put('exfioidos', $request->exfioidos);
            Session::put('exfiboca', $request->exfiboca);
            Session::put('exficuello', $request->exficuello);
            Session::put('exfitorax', $request->exfitorax);
            Session::put('exficorazon', $request->exficorazon);
            Session::put('exfipulmones', $request->exfipulmones);
            Session::put('exfiabdomen', $request->exfiabdomen);
            Session::put('exfiextremidadesmmss', $request->exfiextremidadesmmss);
            Session::put('exfiextremidadesmmii', $request->exfiextremidadesmmii);
            Session::put('exfineurologico', $request->exfineurologico);
            Session::put('exfivestibulocereboloso', $request->exfivestibulocereboloso);
            Session::put('exfimarcha', $request->exfimarcha);
            Session::put('exficraneoycolumna', $request->exficraneoycolumna);
            Session::put('exfiexploracionneuro', $request->exfiexploracionneuro);
        //

        $pdf = PDF::loadView('admin.asociados.fichamedicaclientebanco', compact('clientebanco'));
        $pdfName = 'Fichamedica_'. $clientebanco->nombrecompleto;
        $pdfName .= '.pdf';


        $usuario = auth()->user();
        $clientFolder = public_path('fichamedicaclientesbanco/' . $clientebanco->id);
        $pdfPath = $clientFolder . '/' . $pdfName;
        if (!file_exists($clientFolder)) {
            mkdir($clientFolder, 0755, true);
        }
        $pdf->save($pdfPath);
        Fichamedicasubcliente::create([
            'clientebancoid' => $clientebanco->id,
            'clientebanconombre' => $clientebanco->nombrecompleto,
            'document' =>$pdfName,
            'detalle' =>'FICHA MEDICA',
            'usuarioid' => $usuario->id,
            'usuarioregistro' => $usuario->name,
        ]);

        return $pdf->download($pdfName);
        

        /* return view('admin.asociados.fichamedicaclienteita', compact('cliente')); */
    }
    /* public function guardarformularioclienteita(Cliente $cliente)
    {
        return view('admin.asociados.crearformularioclienteita');
    }
    public function regresarclientes()
    {
        return view('admin.asociados.index');
    } */
    public function guardardeclaracion(Request $request, ClienteBanco $clientebanco)
    {
        // Capturar si el documento es digital o físico
        $tipodocumento = $request->input('tipodocumento');

        // Si el documento es físico, manejar la carga del PDF
        if ($tipodocumento == 'FISICO') {
            if ($request->hasFile('pdf_fisico')) {
                $file = $request->file('pdf_fisico');
                $pdfName = 'DeclaracionMedicaFisica_' . $clientebanco->nombrecompleto . '.' . $file->getClientOriginalExtension();
                $clientFolder = public_path('fichamedicaclientesbanco/' . $clientebanco->id);

                if (!file_exists($clientFolder)) {
                    mkdir($clientFolder, 0755, true);
                }

                // Mover el archivo PDF a la carpeta correspondiente
                $file->move($clientFolder, $pdfName);

                // Guardar el registro en la base de datos
                Fichamedicasubcliente::create([
                    'clienteid' => $clientebanco->id,
                    'nombrecompleto' => $clientebanco->nombrecompleto,
                    'document' => $pdfName,
                    'usuarioid' => auth()->user()->id,
                    'usuarioregistro' => auth()->user()->name,
                    'detalle' => 'DECLARACIONES HECHAS AL MEDICO EXAMINADOR',
                    'tipodocumento' => 'FISICO',
                    'clientebancoid' => $clientebanco->id,
                    'clientebanconombre' => $clientebanco->nombrecompleto
                ]);

                // Redirigir o mostrar un mensaje de éxito
                return back()->with('success', 'PDF Físico subido y registrado correctamente.');
            } else {
                return back()->withErrors(['pdf_fisico' => 'Por favor, selecciona un archivo PDF para subir.']);
            }
        }

        // Proceso para el documento digital
        $preguntas = $request->input('preguntas', []);  // Asegurarse de que $preguntas sea un array

        foreach ($preguntas as $pregunta) {
            if (array_key_exists('respuesta', $pregunta) && !empty($pregunta['respuesta'])) {
                $respuesta = $pregunta['respuesta'];

                if ($respuesta == 'si') {
                    $formulario = new Formulario();
                    $formulario->cliente_id = $pregunta['cliente_id'] ?? null;
                    $formulario->pregunta_id = $pregunta['pregunta_id'] ?? null;
                    $formulario->pregunta_nombre = $pregunta['pregunta_nombre'] ?? null;

                    if ($pregunta['pregunta_id'] == 29) {
                        $formulario->detallescompletos = $pregunta['detallescompletos'] ?? null;
                    } else {
                        // Campos para otras preguntas
                        $formulario->diagnostico = $pregunta['diagnostico'] ?? null;
                        $formulario->fecha = $pregunta['fecha'] ?? null;
                        $formulario->tiempo = $pregunta['tiempo'] ?? null;
                        $formulario->gradorecuperacion = $pregunta['gradorecuperacion'] ?? null;
                        $formulario->medico = $pregunta['medico'] ?? null;
                        $formulario->direccionmedico = $pregunta['direccionmedico'] ?? null;
                        $formulario->diagnostico2 = $pregunta['diagnostico2'] ?? null;
                        $formulario->fecha2 = $pregunta['fecha2'] ?? null;
                        $formulario->tiempo2 = $pregunta['tiempo2'] ?? null;
                        $formulario->gradorecuperacion2 = $pregunta['gradorecuperacion2'] ?? null;
                        $formulario->medico2 = $pregunta['medico2'] ?? null;
                        $formulario->direccionmedico2 = $pregunta['direccionmedico2'] ?? null;
                        $formulario->hacecuanto = $pregunta['hacecuanto'] ?? null;
                        $formulario->cadacuanto = $pregunta['cadacuanto'] ?? null;
                        $formulario->parentesco2 = $pregunta['parentesco2'] ?? null;
                        $formulario->cuantosmeses = $pregunta['cuantosmeses'] ?? null;
                        $formulario->detallescompletos = $pregunta['detallescompletos'] ?? null;
                    }

                    // Guardar el formulario
                    $formulario->save();
                }
            }
        }

        // Capturar los campos adicionales para familiares
        $familiares = $request->input('familiares', []);  // Asegurarse de que sea un array

        // Capturar los campos adicionales
        $nombre_medico = $request->input('ND');
        $fecha_consulta = $request->input('FC');
        $tratamiento_medico = $request->input('TM');

        // Capturar estatura y peso
        $estatura = $request->input('estatura');
        $peso = $request->input('peso');

        // Capturar los datos de firma
        $lugar = $request->input('lugar');
        $dia = $request->input('dia');
        $mes = $request->input('mes');
        $anio = $request->input('anio');

        // Capturar las imágenes de las firmas
        $medicoSignature = $request->file('medico_signature');
        $propuestoSignature = $request->file('propuesto_signature');

        // Crear la carpeta del cliente si no existe
        $clientFolder = public_path('fichamedicaclientesbanco/' . $clientebanco->id);
        if (!file_exists($clientFolder)) {
            mkdir($clientFolder, 0755, true);
        }

        // Capturar las imágenes de las firmas
        $medicoSignature = $request->file('medico_signature');
        $propuestoSignature = $request->file('propuesto_signature');

        // Verificar si las firmas fueron subidas
        if (!$medicoSignature || !$propuestoSignature) {
            return back()->withErrors(['error' => 'Por favor, selecciona ambas firmas antes de generar el PDF.']);
        }

        // Guardar las imágenes temporalmente en la carpeta del cliente
        $medicoSignaturePath = $clientFolder . '/medico_signature.png';
        $propuestoSignaturePath = $clientFolder . '/propuesto_signature.png';

        // Guardar la firma del médico
        if ($medicoSignature) {
            $medicoSignature->move($clientFolder, 'medico_signature.png');
            if (!file_exists($medicoSignaturePath)) {
                return back()->withErrors(['error' => 'Error al guardar la firma del médico.']);
            }
        }

        // Guardar la firma del propuesto asegurado
        if ($propuestoSignature) {
            $propuestoSignature->move($clientFolder, 'propuesto_signature.png');
            if (!file_exists($propuestoSignaturePath)) {
                return back()->withErrors(['error' => 'Error al guardar la firma del propuesto asegurado.']);
            }
        }

        // Cargar la vista del PDF con los datos del cliente, preguntas y campos adicionales
        $pdf = PDF::loadView('admin.asociados.formularios.declaracionpdfmedico', compact('clientebanco', 'preguntas', 'nombre_medico', 'fecha_consulta', 'tratamiento_medico', 'familiares', 'estatura', 'peso', 'lugar', 'dia', 'mes', 'anio', 'medicoSignaturePath', 'propuestoSignaturePath'));

        // Crear el nombre del archivo PDF
        $pdfName = 'DeclaracionMedicaDigital_' . $clientebanco->nombrecompleto;
        if ($clientebanco->apepaterno) {
            $pdfName .= ' ' . $clientebanco->apepaterno;
        }
        if ($clientebanco->apematerno) {
            $pdfName .= ' ' . $clientebanco->apematerno;
        }
        $pdfName .= '.pdf';

        // Generación del PDF
        $pdfName = 'DeclaracionMedicaDigital_' . $clientebanco->nombrecompleto . '.pdf';
        $clientFolder = public_path('fichamedicaclientesbanco/' . $clientebanco->id);

        if (!file_exists($clientFolder)) {
            mkdir($clientFolder, 0755, true);
        }

        $pdf->save($clientFolder . '/' . $pdfName);

        // Eliminar las imágenes temporales después de generar el PDF
        if (file_exists($medicoSignaturePath)) {
            unlink($medicoSignaturePath);
        }
        if (file_exists($propuestoSignaturePath)) {
            unlink($propuestoSignaturePath);
        }

        // Guardar el PDF en la base de datos con los datos adicionales
        Fichamedicasubcliente::create([
            'clienteid' => $clientebanco->id,  // ID del cliente
            'nombrecompleto' => $clientebanco->nombrecompleto,  // Nombre completo del cliente
            'document' => $pdfName,  // Nombre del archivo PDF
            'usuarioid' => auth()->user()->id,  // ID del usuario actual
            'usuarioregistro' => auth()->user()->name,  // Nombre del usuario que hace el registro
            'detalle' => 'DECLARACIONES HECHAS AL MEDICO EXAMINADOR',  // Detalle del registro
            'tipodocumento' => $tipodocumento,  // Tipo de documento (DIGITAL o FISICO)
            'clientebancoid' => $clientebanco->id,  // ID del cliente banco
            'clientebanconombre' => $clientebanco->nombrecompleto  // Nombre completo del cliente banco
        ]);

        // Retornar el PDF descargable
        /* return $pdf->download($pdfName); */
        $pdf2 = PDF::loadView('admin.asociados.formularios.declaracionpdfmedico2', compact('clientebanco', 'preguntas', 'nombre_medico', 'fecha_consulta', 'tratamiento_medico', 'familiares', 'estatura', 'peso', 'lugar', 'dia', 'mes', 'anio'));

        $pdfName2 = 'DeclaracionMedicaFisica_' . $clientebanco->nombrecompleto . '.pdf';

        // Descargar el PDF
        return $pdf2->download($pdfName2);
    }
    public function guardarSOLOdeclaracion(Request $request, ClienteBanco $clientebanco)
    {
        // Proceso para capturar preguntas y otros datos (igual que en guardarDigitalDeclaracion)
        $preguntas = $request->input('preguntas', []);

        foreach ($preguntas as $pregunta) {
            if (array_key_exists('respuesta', $pregunta) && !empty($pregunta['respuesta'])) {
                $respuesta = $pregunta['respuesta'];

                if ($respuesta == 'si') {
                    $formulario = new Formulario();
                    $formulario->cliente_id = $pregunta['cliente_id'] ?? null;
                    $formulario->pregunta_id = $pregunta['pregunta_id'] ?? null;
                    $formulario->pregunta_nombre = $pregunta['pregunta_nombre'] ?? null;

                    if ($pregunta['pregunta_id'] == 29) {
                        $formulario->detallescompletos = $pregunta['detallescompletos'] ?? null;
                    } else {
                        // Campos para otras preguntas
                        $formulario->diagnostico = $pregunta['diagnostico'] ?? null;
                        $formulario->fecha = $pregunta['fecha'] ?? null;
                        $formulario->tiempo = $pregunta['tiempo'] ?? null;
                        $formulario->gradorecuperacion = $pregunta['gradorecuperacion'] ?? null;
                        $formulario->medico = $pregunta['medico'] ?? null;
                        $formulario->direccionmedico = $pregunta['direccionmedico'] ?? null;
                        $formulario->diagnostico2 = $pregunta['diagnostico2'] ?? null;
                        $formulario->fecha2 = $pregunta['fecha2'] ?? null;
                        $formulario->tiempo2 = $pregunta['tiempo2'] ?? null;
                        $formulario->gradorecuperacion2 = $pregunta['gradorecuperacion2'] ?? null;
                        $formulario->medico2 = $pregunta['medico2'] ?? null;
                        $formulario->direccionmedico2 = $pregunta['direccionmedico2'] ?? null;
                        $formulario->hacecuanto = $pregunta['hacecuanto'] ?? null;
                        $formulario->cadacuanto = $pregunta['cadacuanto'] ?? null;
                        $formulario->parentesco2 = $pregunta['parentesco2'] ?? null;
                        $formulario->cuantosmeses = $pregunta['cuantosmeses'] ?? null;
                        $formulario->detallescompletos = $pregunta['detallescompletos'] ?? null;
                    }

                    // Guardar el formulario
                    $formulario->save();
                }
            }
        }

        // Capturar los mismos campos adicionales
        $familiares = $request->input('familiares', []);
        $nombre_medico = $request->input('ND');
        $fecha_consulta = $request->input('FC');
        $tratamiento_medico = $request->input('TM');
        $estatura = $request->input('estatura');
        $peso = $request->input('peso');
        $lugar = $request->input('lugar');
        $dia = $request->input('dia');
        $mes = $request->input('mes');
        $anio = $request->input('anio');

        // Generar el PDF sin las firmas
        $pdf = PDF::loadView('admin.asociados.formularios.declaracionpdfmedico2', compact('clientebanco', 'preguntas', 'nombre_medico', 'fecha_consulta', 'tratamiento_medico', 'familiares', 'estatura', 'peso', 'lugar', 'dia', 'mes', 'anio'));

        $pdfName = 'DeclaracionMedicaFisica_' . $clientebanco->nombrecompleto . '.pdf';
        $clientFolder = public_path('fichamedicaclientesbanco/' . $clientebanco->id);

        if (!file_exists($clientFolder)) {
            mkdir($clientFolder, 0755, true);
        }

        $pdf->save($clientFolder . '/' . $pdfName);

        // Descargar el PDF
        return $pdf->download($pdfName);
    }

//



//VER Y EDITAR BATERIAS DE CLIENTES GOOD LIFE
    public function verbateriaasociado(Request $request, Asociado $asociado)
    {
        $nombreasociado = $request->get('buscarpor');
        $asociadoid = $asociado->id;
        $asociados = Areaaccion::where('asociadoid', $asociadoid)
                          ->where('area', 'LIKE', "%$nombreasociado%")
                          ->orderBy('area')
                          ->simplePaginate(1000);
        return view('admin.asociados.verbateriaasociado', compact('asociados', 'asociado'));
    }
    public function editarbateriaasociado(Areaaccion $areaaccion)
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
        return view('admin.proveedores.edit', compact('areaaccion'));
    }
//

//VER, CREAR Y EDITAR BATERIAS DE CLIENTES BANCOS
    public function verbateriasbanco(Request $request, Asociado $asociado)
        {
            $asociadoid = $asociado->id;

            $asociados = Areaaccion::where('asociadoid', $asociadoid)
                            ->orderBy('categoria')
                            ->get();

            $categorias = $asociados->unique('categoria')->pluck('categoria');

            return view('admin.asociados.verbateriasbanco', compact('asociados', 'asociado', 'categorias'));
        }
    public function crearbateriabanco(Areaaccion $areaaccion, Asociado $asociado)
        {
            $tiponombre = Area::orderBy('tipoarea')
                            ->distinct()
                            ->pluck('tipoarea', 'tipoarea')
                            ->toArray();

            $areas = Area::orderBy('nombrearea')->get(); 

            $acciones = Bateriaproveedor::orderBy('accion')->get();
            
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
            $categoria = [
                'A' => 'A',
                'B' => 'B',
                'C' => 'C',
                'D' => 'D',
                'E' => 'E',
                'F' => 'F',
                'G' => 'G',
                'H' => 'H',
                'I' => 'I',
                'J' => 'J',
                'K' => 'K',
                'L' => 'L',
                'M' => 'M',
                'N' => 'N',
                'O' => 'O',
                'P' => 'P',
                'Q' => 'Q',
                'R' => 'R',
                'S' => 'S',
                'T' => 'T',
                'U' => 'U',
                'V' => 'V',
                'W' => 'W',
                'X' => 'X',
                'Y' => 'Y',
                'Z' => 'Z',
            ];
            
            return view('admin.asociados.crearbateriabanco', compact('asociado', 'categoria', 'tiponombre', 'areaaccion', 'areas', 'estado', 'tipocliente', 'sucursal', 'acciones'));
        }
    public function guardarbateriabanco(StoreAreaaccionRequest $request, Asociado $asociado)
        {
            $areaData = $request->all();
            
            $areaccion = Areaaccion::create($areaData);

            return redirect()->route('admin.asociados.verbateriasbanco', $asociado)->with('info', 'La acción se creó con éxito');
        }
    public function editarbateriabanco($areaaccion)
        {
            // Aquí debes buscar el área de acción basado en el $areaaccion recibido
            $areaaccion = Areaaccion::findOrFail($areaaccion);
        
            $tiponombre = Area::orderBy('tipoarea')
                            ->distinct()
                            ->pluck('tipoarea', 'tipoarea')
                            ->toArray();

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
            $categoria = [
                'A' => 'A',
                'B' => 'B',
                'C' => 'C',
                'D' => 'D',
                'E' => 'E',
                'F' => 'F',
                'G' => 'G',
                'H' => 'H',
                'I' => 'I',
                'J' => 'J',
                'K' => 'K',
                'L' => 'L',
                'M' => 'M',
                'N' => 'N',
                'O' => 'O',
                'P' => 'P',
                'Q' => 'Q',
                'R' => 'R',
                'S' => 'S',
                'T' => 'T',
                'U' => 'U',
                'V' => 'V',
                'W' => 'W',
                'X' => 'X',
                'Y' => 'Y',
                'Z' => 'Z',
            ];
            return view('admin.asociados.editarbateriabanco', compact('categoria', 'tiponombre', 'areaaccion', 'areas', 'estado', 'tipocliente', 'sucursal'));
        }
    public function actualizarbateriabanco(Areaaccion $areaaccion, UpdateAreaaccionRequest $request)
        {
            $areaaccion->update($request->all());

            return redirect()->route('admin.asociados.index', $areaaccion)->with('info', 'El área se actualizó con éxito');
        }
//

    public function verProgramacionPendienteAuditoria(Request $request, Asociado $asociado)
    {
        $buscarPor = $request->get('buscarpor');

        // Construir la consulta base
        $query = Bateriasubcliente::query()
            ->whereNotNull('clienteauditoriaid')
            ->whereNotNull('clienteauditorianombre');

        // Aplicar el filtro de búsqueda si existe
        if ($buscarPor) {
            $query->where('accionnombre', 'LIKE', "%$buscarPor%");
        }

        // Filtrar solo las acciones pendientes utilizando whereNotExists
        $accionesPendientes = $query
            ->whereNotExists(function ($subquery) {
                $subquery->select(DB::raw(1))
                    ->from('programacionsubclientes as eps')
                    ->whereRaw('TRIM(LOWER(eps.clienteauditorianombre)) = TRIM(LOWER(bateriasubclientes.clienteauditorianombre))')
                    ->whereRaw('TRIM(LOWER(eps.accionnombre)) = TRIM(LOWER(bateriasubclientes.accionnombre))')
                    ->whereColumn('eps.fechabateria', 'bateriasubclientes.fechabateria');
            })
            ->select(
                'id as ps_id',
                'clienteauditoriaid as ps_clienteauditoriaid',
                'clienteauditorianombre as ps_clienteauditorianombre',
                'proveedorasignado as ps_proveedorasignado',
                'accionnombre as ps_accionnombre',
                'fechabateria as ps_fechabateria',
                /* 'fechaasignada as ps_fechaasignada',
                DB::raw("CONCAT(IFNULL(horadesde, ''), ' - ', IFNULL(horahasta, '')) as ps_hora_asignada"), */
                DB::raw("'Pendiente' as Estado")
            )
            ->orderBy('clienteauditoriaid')
            ->orderBy('fechabateria')
            ->orderBy('id')
            ->simplePaginate(1000);

        return view('admin.asociados.verprogramacionpendienteauditoria', compact('accionesPendientes', 'asociado'));
    }

    public function buscarProgramacionPendienteAuditoria(Request $request, Asociado $asociado)
    {
        // Llamar a la función principal con los parámetros adecuados
        return $this->verProgramacionPendienteAuditoria($request, $asociado);
    }

    public function verProgramacionPendienteComun(Request $request, Asociado $asociado)
    {
        $buscarPor = $request->get('buscarpor');

        // Construir la consulta base
        $query = Bateriasubcliente::query()
            ->whereNotNull('clientecomunid')
            ->whereNotNull('clientecomunnombre');

        // Aplicar el filtro de búsqueda si existe
        if ($buscarPor) {
            $query->where('accionnombre', 'LIKE', "%$buscarPor%");
        }

        // Filtrar solo las acciones pendientes utilizando whereNotExists
        $accionesPendientes = $query
            ->whereNotExists(function ($subquery) {
                $subquery->select(DB::raw(1))
                    ->from('programacionsubclientes as eps')
                    ->whereRaw('TRIM(LOWER(eps.clientecomunnombre)) = TRIM(LOWER(bateriasubclientes.clientecomunnombre))')
                    ->whereRaw('TRIM(LOWER(eps.accionnombre)) = TRIM(LOWER(bateriasubclientes.accionnombre))')
                    ->whereColumn('eps.fechabateria', 'bateriasubclientes.fechabateria');
            })
            ->select(
                'id as ps_id',
                'clientecomunid as ps_clientecomunid',
                'clientecomunnombre as ps_clientecomunnombre',
                'proveedorasignado as ps_proveedorasignado',
                'accionnombre as ps_accionnombre',
                'fechabateria as ps_fechabateria',
                /* 'fechaasignada as ps_fechaasignada',
                DB::raw("CONCAT(IFNULL(horadesde, ''), ' - ', IFNULL(horahasta, '')) as ps_hora_asignada"), */
                DB::raw("'Pendiente' as Estado")
            )
            ->orderBy('clientecomunid')
            ->orderBy('fechabateria')
            ->orderBy('id')
            ->simplePaginate(1000);

        return view('admin.asociados.verprogramacionpendientecomun', compact('accionesPendientes', 'asociado'));
    }

    public function buscarProgramacionPendienteComun(Request $request, Asociado $asociado)
    {
        // Llamar a la función principal con los parámetros adecuados
        return $this->verProgramacionPendienteComun($request, $asociado);
    }

    public function verProgramacionPendienteITA(Request $request, Asociado $asociado)
    {
        $buscarPor = $request->get('buscarpor');

        $query = Bateriasubcliente::query()
            ->whereNotNull('clienteitaid')
            ->whereNotNull('clienteitanombre');

        if ($buscarPor) {
            $query->where('accionnombre', 'LIKE', "%$buscarPor%");
        }

        $accionesPendientes = $query
            ->whereNotExists(function ($subquery) {
                $subquery->select(DB::raw(1))
                    ->from('programacionsubclientes as eps')
                    ->whereRaw('TRIM(LOWER(eps.clienteitanombre)) = TRIM(LOWER(bateriasubclientes.clienteitanombre))')
                    ->whereRaw('TRIM(LOWER(eps.accionnombre)) = TRIM(LOWER(bateriasubclientes.accionnombre))')
                    ->whereColumn('eps.fechabateria', 'bateriasubclientes.fechabateria');
            })
            ->select(
                'id as ps_id',
                'clienteitaid as ps_clienteitaid',
                'clienteitanombre as ps_clienteitanombre',
                'proveedorasignado as ps_proveedorasignado',
                'accionnombre as ps_accionnombre',
                'fechabateria as ps_fechabateria',
                DB::raw("'Pendiente' as Estado")
            )
            ->orderBy('clienteitaid')
            ->orderBy('fechabateria')
            ->orderBy('id')
            ->simplePaginate(1000);

        return view('admin.asociados.verprogramacionpendienteita', compact('accionesPendientes', 'asociado'));
    }

    public function buscarProgramacionPendienteITA(Request $request, Asociado $asociado)
    {
        // Llamar a la función principal con los parámetros adecuados
        return $this->verProgramacionPendienteITA($request, $asociado);
    }





}
