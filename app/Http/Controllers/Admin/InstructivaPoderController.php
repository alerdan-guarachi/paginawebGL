<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Cliente;
use App\Models\ClienteAuditoria;
use App\Models\Proveedoresservicios;
use App\Models\Users;
use App\Models\Asociado;
use App\Models\Empresa;
use PDF;
use App\Models\InstructivasPoder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Notifications\SubirPoderesNotification;
use App\Models\Derechohabientes;

class InstructivaPoderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /* public function __construct() { 
        $this->middleware ('can:admin.empresas.index')->only('index');
    } */

    public function index(Request $request)
    {
        $busqueda = $request->get('buscarpor');
        $clientes = Cliente::where(function ($query) use ($busqueda) {
            $query->where('nombrecompleto', 'like', "%$busqueda%")
                ->orWhere('ciudadresidencia', 'like', "%$busqueda%")
                ->orWhere('ci', 'like', "%$busqueda%")
                ->orWhere('id', 'like', "%$busqueda%");
        })->orderBy('nombrecompleto')->simplePaginate(20);
        return view('admin.instructivaspoder.index', compact('clientes'));
    }

    public function nuevainstructiva(Request $request)
    {
        $busqueda = $request->get('buscarpor');
        $clientes = Cliente::where(function ($query) use ($busqueda) {
            $query->where('nombrecompleto', 'like', "%$busqueda%")
                ->orWhere('ciudadresidencia', 'like', "%$busqueda%")
                ->orWhere('ci', 'like', "%$busqueda%")
                ->orWhere('id', 'like', "%$busqueda%");
        })->orderBy('nombrecompleto')->get();

        $instructivas = InstructivasPoder::where(function ($query) use ($busqueda) {
            $query->where('clientenombre', 'like', "%$busqueda%")
                ->orWhere('clienteid', 'like', "%$busqueda%")
                ->orWhere('tramite', 'like', "%$busqueda%");
        })->orderBy('clientenombre')->get();

        $clientesauditoria = ClienteAuditoria::where(function ($query) use ($busqueda) {
            $query->where('nombrecompleto', 'like', "%$busqueda%")
                ->orWhere('ci', 'like', "%$busqueda%")
                ->orWhere('id', 'like', "%$busqueda%");
        })->orderBy('nombrecompleto')->simplePaginate(20);

        return view('admin.instructivaspoder.nuevainstructiva', compact('clientes', 'clientesauditoria','instructivas'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Cliente $cliente)
    {
        $nombrecompleto = $cliente->nombrecompleto;
        $id = $cliente->id;
        return view('admin.instructivaspoder.create', compact('id','cliente','nombrecompleto'));
    }
    public function buscarclientesitainstructiva(Request $request)
    {
        $busqueda = $request->get('buscarpor');
        $clientes = Cliente::where(function ($query) use ($busqueda) {
            $query->where('nombrecompleto', 'like', "%$busqueda%")
                ->orWhere('ciudadresidencia', 'like', "%$busqueda%")
                ->orWhere('ci', 'like', "%$busqueda%")
                ->orWhere('id', 'like', "%$busqueda%");
        })->orderBy('nombrecompleto')->simplePaginate(20);

        $clientesauditoria = ClienteAuditoria::where(function ($query) use ($busqueda) {
            $query->where('nombrecompleto', 'like', "%$busqueda%")
                ->orWhere('ci', 'like', "%$busqueda%")
                ->orWhere('id', 'like', "%$busqueda%");
        })->orderBy('nombrecompleto')->simplePaginate(20);

        $instructivas = InstructivasPoder::where(function ($query) use ($busqueda) {
            $query->where('clientenombre', 'like', "%$busqueda%")
                ->orWhere('clienteid', 'like', "%$busqueda%")
                ->orWhere('tramite', 'like', "%$busqueda%");
        })->orderBy('clientenombre')->simplePaginate(20);

        return view('admin.instructivaspoder.nuevainstructiva', compact('clientes', 'clientesauditoria','instructivas'));
    }
    public function crearinstructivapoder(Cliente $cliente)
    {
        // Todos los tipos posibles
        $tramitesDisponibles = [
            'INVALIDEZ' => 'INVALIDEZ',
            'APELACIÓN' => 'APELACIÓN',
            'SEGUNDA SOLICITUD' => 'SEGUNDA SOLICITUD',
            'APELACIÓN SEGUNDA SOLICITUD' => 'APELACIÓN SEGUNDA SOLICITUD',
            'TERCERA SOLICITUD' => 'TERCERA SOLICITUD',
            'APELACIÓN TERCERA SOLICITUD' => 'APELACIÓN TERCERA SOLICITUD',
            'RECALIFICACIÓN' => 'RECALIFICACIÓN',
            'APELACIÓN DE RECALIFICACIÓN' => 'APELACIÓN DE RECALIFICACIÓN',
            'RECALIFICACIÓN SEGUNDA SOLICITUD' => 'RECALIFICACIÓN SEGUNDA SOLICITUD',
            'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD' => 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD',
            'JUBILACIÓN' => 'JUBILACIÓN',
            'PENSIÓN POR MUERTE' => 'PENSIÓN POR MUERTE',
            'MASA HEREDITARIA' => 'MASA HEREDITARIA',
            'RETIRO DE APORTES PARCIAL' => 'RETIRO DE APORTES PARCIAL',
            'RETIRO DE APORTES TOTAL' => 'RETIRO DE APORTES TOTAL',
            'COMPENSACIÓN DE COTIZACIONES (SENASIR)' => 'COMPENSACIÓN DE COTIZACIONES (SENASIR)',
            'RECALIFICACIÓN' => 'RECALIFICACIÓN',
        ];

        // Buscar trámites ya registrados para el cliente
        $tramitesYaGenerados = InstructivasPoder::where('clienteid', $cliente->id)->pluck('tramite')->toArray();

        // Filtrar los disponibles
        $tramitesFiltrados = array_diff_key($tramitesDisponibles, array_flip($tramitesYaGenerados));

        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci', 'ciexp', 'ciudad')
            ->where('categoria', 'PROVEEDOR INTERNO')
            ->whereNotIn('razonsocial', [
                'FABRICIO ORLANDO PRADO PARRADO',
                'CARLOS ALEJANDRO GUARACHI SANDOVAL'
            ])
        ->get();
        $personalExtra = collect([
            (object)[
                'id' => 'M1',
                'razonsocial' => 'FERNANDO SALAS VACA',
                'ci' => '5844795',
                'ciexp' => 'SCZ',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M2',
                'razonsocial' => 'RUBEN ARIAS SIÑANIS RODRIGUEZ',
                'ci' => '8654896',
                'ciexp' => '',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M3',
                'razonsocial' => 'LUIS ENRIQUE LEAÑOS TAPIA',
                'ci' => '8992501',
                'ciexp' => '',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M4',
                'razonsocial' => 'JUAN PABLO FERNANDEZ JUSTINIANO',
                'ci' => '8906323',
                'ciexp' => '',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M5',
                'razonsocial' => 'JUAN PABLO MENDOZA ESPINOZA',
                'ci' => '6582177',
                'ciexp' => 'PT',
                'ciudad' => 'POTOSI'
            ],
            (object)[
                'id' => 'M6',
                'razonsocial' => 'CLEVERT RONALD PRADO MONTERO',
                'ci' => '766609',
                'ciexp' => 'SC',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M7',
                'razonsocial' => 'PERCY LEON FERNANDEZ',
                'ci' => '8003064',
                'ciexp' => 'CBA',
                'ciudad' => 'COCHABAMBA'
            ],
            (object)[
                'id' => 'M8',
                'razonsocial' => 'CRISTHIAN GABRIEL SAAVEDRA SOLETO',
                'ci' => '6371870',
                'ciexp' => 'SC',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M9',
                'razonsocial' => 'VIERY CASANOVAS VASQUEZ',
                'ci' => '7635907',
                'ciexp' => 'BE',
                'ciudad' => 'BENI'
            ],
            (object)[
                'id' => 'M10',
                'razonsocial' => 'VALERIA RODRIGO VARGAS',
                'ci' => '5072045',
                'ciexp' => 'PO',
                'ciudad' => 'POTOSI'
            ],
            (object)[
                'id' => 'M11',
                'razonsocial' => 'JUAN IGNACIO AGUILAR TORRICO',
                'ci' => '8183185',
                'ciexp' => 'SC',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M12',
                'razonsocial' => 'JORGE ANDRES PRADO BENAVIDEZ',
                'ci' => '8617825',
                'ciexp' => 'PO',
                'ciudad' => 'POTOSI'
            ],
            (object)[
                'id' => 'M13',
                'razonsocial' => 'JORGE ARMANDO LUCUY BARJA',
                'ci' => '5679038',
                'ciexp' => 'CH',
                'ciudad' => 'SANTA CRUZ'
            ],
        ]);

        // 👇 Unimos los de BD + los manuales
        $personal = $personal->concat($personalExtra);

        return view('admin.instructivaspoder.crearinstructivapoder', compact('cliente', 'personal', 'tramitesFiltrados'));
    }
    public function generarpdfinstructivaspoder(Request $request, Cliente $cliente)
    {
        $tipoPdf = $request->input('tipo_pdf');
        $sucursal = $request->input('sucursal');
        $tipopension = $request->input('tipopension');
        $generofallecido = $request->input('generofallecido');
        $nombrefallecido = $request->input('nombrefallecido');
        $cifallecido = $request->input('cifallecido');
        $fechaactualregistro = $request->input('fechaactual');
        /* $personalIds = explode(',', $request->input('personal_ids'));
        $personal = Proveedoresservicios::whereIn('id', $personalIds)->get(); */
        $personalIds = explode(',', $request->input('personal_ids'));

        // Personas manuales (por defecto)
        $personalExtra = collect([
            (object)[
                'id' => 'M1',
                'razonsocial' => 'FERNANDO SALAS VACA',
                'ci' => '5844795',
                'ciexp' => 'SCZ',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M2',
                'razonsocial' => 'RUBEN ARIAS SIÑANIS RODRIGUEZ',
                'ci' => '8654896',
                'ciexp' => '',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M3',
                'razonsocial' => 'LUIS ENRIQUE LEAÑOS TAPIA',
                'ci' => '8992501',
                'ciexp' => '',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M4',
                'razonsocial' => 'JUAN PABLO FERNANDEZ JUSTINIANO',
                'ci' => '8906323',
                'ciexp' => '',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M5',
                'razonsocial' => 'JUAN PABLO MENDOZA ESPINOZA',
                'ci' => '6582177',
                'ciexp' => 'PT',
                'ciudad' => 'POTOSI'
            ],
            (object)[
                'id' => 'M6',
                'razonsocial' => 'CLEVERT RONALD PRADO MONTERO',
                'ci' => '766609',
                'ciexp' => 'SC',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M7',
                'razonsocial' => 'PERCY LEON FERNANDEZ',
                'ci' => '8003064',
                'ciexp' => 'CBA',
                'ciudad' => 'COCHABAMBA'
            ],
            (object)[
                'id' => 'M8',
                'razonsocial' => 'CRISTHIAN GABRIEL SAAVEDRA SOLETO',
                'ci' => '6371870',
                'ciexp' => 'SC',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M9',
                'razonsocial' => 'VIERY CASANOVAS VASQUEZ',
                'ci' => '7635907',
                'ciexp' => 'BE',
                'ciudad' => 'BENI'
            ],
            (object)[
                'id' => 'M10',
                'razonsocial' => 'VALERIA RODRIGO VARGAS',
                'ci' => '5072045',
                'ciexp' => 'PO',
                'ciudad' => 'POTOSI'
            ],
            (object)[
                'id' => 'M11',
                'razonsocial' => 'JUAN IGNACIO AGUILAR TORRICO',
                'ci' => '8183185',
                'ciexp' => 'SC',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M12',
                'razonsocial' => 'JORGE ANDRES PRADO BENAVIDEZ',
                'ci' => '8617825',
                'ciexp' => 'PO',
                'ciudad' => 'POTOSI'
            ],
            (object)[
                'id' => 'M13',
                'razonsocial' => 'JORGE ARMANDO LUCUY BARJA',
                'ci' => '5679038',
                'ciexp' => 'CH',
                'ciudad' => 'SANTA CRUZ'
            ],
        ]);

        // Registros desde la base de datos
        $personalDb = Proveedoresservicios::whereIn('id', $personalIds)->get();

        // Unir ambos orígenes (BD + Manuales)
        $personalTotal = $personalDb->concat($personalExtra);

        // Mantener el orden de selección del usuario
        $personal = $personalTotal->filter(function ($item) use ($personalIds) {
            return in_array($item->id, $personalIds);
        })->sortBy(function ($item) use ($personalIds) {
            return array_search($item->id, $personalIds);
        })->values();
        $fechaactual = Carbon::parse($request->input('fechaactual'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $tramitesConfig = [
            'INVALIDEZ' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_Invalidez_',
            ],
            'APELACIÓN' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_Apelación_',
            ],
            'SEGUNDA SOLICITUD' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_SegundaSolicitud_',
            ],
            'TERCERA SOLICITUD' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_TerceraSolicitud_',
            ],
            'JUBILACIÓN' => [
                'vista' => 'admin.instructivaspoder.ipoderjubilacion',
                'nombre_archivo' => 'InstructivaPoder_Jubilación_',
            ],
            'PENSIÓN POR MUERTE' => [
                'vista' => 'admin.instructivaspoder.ipoderpensionmuerte',
                'nombre_archivo' => 'InstructivaPoder_PensiónMuerte_',
            ],
            'MASA HEREDITARIA' => [
                'vista' => 'admin.instructivaspoder.ipodermasahereditaria',
                'nombre_archivo' => 'InstructivaPoder_MasaHereditaria_',
            ],
            'RETIRO DE APORTES PARCIAL' => [
                'vista' => 'admin.instructivaspoder.ipoderretiroaportesparcial',
                'nombre_archivo' => 'InstructivaPoder_RetiroAportesParcial_',
            ],
            'RETIRO DE APORTES TOTAL' => [
                'vista' => 'admin.instructivaspoder.ipoderretiroaportestotal',
                'nombre_archivo' => 'InstructivaPoder_RetiroAportesTotal_',
            ],
            'COMPENSACIÓN DE COTIZACIONES (SENASIR)' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_CompensaciónCotizacionesSenasir_',
            ],
            'RECALIFICACIÓN' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_Recalificación_',
            ],
            'APELACIÓN DE RECALIFICACIÓN' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_ApelaciónRecalificación_',
            ],
            'RECALIFICACIÓN SEGUNDA SOLICITUD' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_RecalificaciónSegSol_',
            ],
            'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_ApelaciónRecalificaciónSegSol_',
            ],
            'APELACIÓN SEGUNDA SOLICITUD' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_ApelaciónSegundaSolicitud_',
            ],
            'APELACIÓN TERCERA SOLICITUD' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_ApelaciónTerceraSolicitud_',
            ],
        ];

        if (!isset($tramitesConfig[$tipoPdf])) {
            return redirect()->back()->with('error', 'Trámite no válido.');
        }

        $config = $tramitesConfig[$tipoPdf];

        $nombreClienteMayuscula = strtoupper($cliente->nombrecompleto);
        $nombreFormateado = str_replace(' ', '-', $nombreClienteMayuscula);
        $nombreArchivo = $config['nombre_archivo'] . $nombreFormateado . '.pdf';

        $estadoCivilOriginal = $cliente->estadocivil;
        $genero = strtolower($cliente->genero);
        $estadoCivil = $estadoCivilOriginal;

        if (Str::endsWith(strtolower($estadoCivilOriginal), '@')) {
            $sufijo = $genero === 'femenino' ? 'a' : 'o';
            $estadoCivil = substr($estadoCivilOriginal, 0, -1) . $sufijo;
        }

        $estadoCivil = Str::upper($estadoCivil);

        $derechohabientes = Derechohabientes::where('clienteid', $cliente->id)
        ->where('tramite', $tipoPdf)
        ->get();

        $pdf = PDF::loadView($config['vista'], compact('tipoPdf', 'cliente', 'personal', 'estadoCivil', 'fechaactual', 'sucursal','tipopension'
        ,'generofallecido','nombrefallecido','cifallecido','derechohabientes'));
        $rutaCarpeta = public_path('instructivaspoder/' . $cliente->id);
        if (!file_exists($rutaCarpeta)) {
            mkdir($rutaCarpeta, 0777, true);
        }
        $pdf->save($rutaCarpeta . '/' . $nombreArchivo);

        $registro = new InstructivasPoder();
        $registro->tramite = $tipoPdf;
        $registro->clienteid = $cliente->id;
        $registro->fecharegistro = $fechaactualregistro;
        $registro->clientenombre = $cliente->nombrecompleto;
        $registro->documento = $nombreArchivo;
        $registro->usuarioregistroid = auth()->id();
        $registro->usuarioregistronombre = auth()->user()->name;

        foreach ($personal->take(10)->values() as $index => $persona) {
            $campo = 'apoderado' . ($index + 1);
            $registro->$campo = $persona->razonsocial;
        }

        $registro->save();

        if ($registro) {
            $usuariosNotificar = User::whereHas('roles', function ($q) {
                    $q->whereIn('name', ['ADMINISTRADOR']);
                })
                ->get();
                
            foreach ($usuariosNotificar as $usuarioDestino) {
                $usuarioDestino->notify(new SubirPoderesNotification($registro));
            }
        }

        return response()->download($rutaCarpeta . '/' . $nombreArchivo);
    }
    public function subirinstructivaFirmado(Request $request, $id)
    {
        $request->validate([
            'documentofirmado' => 'required|mimes:pdf,jpg,png|max:2048'
        ]);

        $instructiva = InstructivasPoder::findOrFail($id);

        if ($request->hasFile('documentofirmado')) {
            $file = $request->file('documentofirmado');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('instructivaspoder/' . $instructiva->clienteid), $filename);

            $instructiva->documentofirmado = $filename;
            $instructiva->save();
        }

        return back()->with('info', 'Documento firmado subido correctamente.');
    }

    public function previewInstructiva(Request $request, Cliente $cliente)
    {
        $tipoPdf = $request->input('tipo_pdf');
        $sucursal = $request->input('sucursal');
        $tipopension = $request->input('tipopension');
        $generofallecido = $request->input('generofallecido');
        $nombrefallecido = $request->input('nombrefallecido');
        $cifallecido = $request->input('cifallecido');
        /* $personalIds = explode(',', $request->input('personal_ids'));
        $personal = Proveedoresservicios::whereIn('id', $personalIds)
            ->get()
            ->sortBy(function ($item) use ($personalIds) {
                return array_search($item->id, $personalIds);
            })
            ->values(); */
            $personalIds = explode(',', $request->input('personal_ids'));

        // Personas manuales (por defecto)
        $personalExtra = collect([
            (object)[
                'id' => 'M1',
                'razonsocial' => 'FERNANDO SALAS VACA',
                'ci' => '5844795',
                'ciexp' => 'SCZ',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M2',
                'razonsocial' => 'RUBEN ARIAS SIÑANIS RODRIGUEZ',
                'ci' => '8654896',
                'ciexp' => '',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M3',
                'razonsocial' => 'LUIS ENRIQUE LEAÑOS TAPIA',
                'ci' => '8992501',
                'ciexp' => '',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M4',
                'razonsocial' => 'JUAN PABLO FERNANDEZ JUSTINIANO',
                'ci' => '8906323',
                'ciexp' => '',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M5',
                'razonsocial' => 'JUAN PABLO MENDOZA ESPINOZA',
                'ci' => '6582177',
                'ciexp' => 'PT',
                'ciudad' => 'POTOSI'
            ],
            (object)[
                'id' => 'M6',
                'razonsocial' => 'CLEVERT RONALD PRADO MONTERO',
                'ci' => '766609',
                'ciexp' => 'SC',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M7',
                'razonsocial' => 'PERCY LEON FERNANDEZ',
                'ci' => '8003064',
                'ciexp' => 'CBA',
                'ciudad' => 'COCHABAMBA'
            ],
            (object)[
                'id' => 'M8',
                'razonsocial' => 'CRISTHIAN GABRIEL SAAVEDRA SOLETO',
                'ci' => '6371870',
                'ciexp' => 'SC',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M9',
                'razonsocial' => 'VIERY CASANOVAS VASQUEZ',
                'ci' => '7635907',
                'ciexp' => 'BE',
                'ciudad' => 'BENI'
            ],
            (object)[
                'id' => 'M10',
                'razonsocial' => 'VALERIA RODRIGO VARGAS',
                'ci' => '5072045',
                'ciexp' => 'PO',
                'ciudad' => 'POTOSI'
            ],
            (object)[
                'id' => 'M11',
                'razonsocial' => 'JUAN IGNACIO AGUILAR TORRICO',
                'ci' => '8183185',
                'ciexp' => 'SC',
                'ciudad' => 'SANTA CRUZ'
            ],
            (object)[
                'id' => 'M12',
                'razonsocial' => 'JORGE ANDRES PRADO BENAVIDEZ',
                'ci' => '8617825',
                'ciexp' => 'PO',
                'ciudad' => 'POTOSI'
            ],
            (object)[
                'id' => 'M13',
                'razonsocial' => 'JORGE ARMANDO LUCUY BARJA',
                'ci' => '5679038',
                'ciexp' => 'CH',
                'ciudad' => 'SANTA CRUZ'
            ],
        ]);

        // Registros desde la base de datos
        $personalDb = Proveedoresservicios::whereIn('id', $personalIds)->get();

        // Unir ambos orígenes (BD + Manuales)
        $personalTotal = $personalDb->concat($personalExtra);

        // Mantener el orden de selección del usuario
        $personal = $personalTotal->filter(function ($item) use ($personalIds) {
            return in_array($item->id, $personalIds);
        })->sortBy(function ($item) use ($personalIds) {
            return array_search($item->id, $personalIds);
        })->values();

        $fechaactual = Carbon::parse($request->input('fechaactual'))->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $tramitesConfig = [
            'INVALIDEZ' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_Invalidez_',
            ],
            'APELACIÓN' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_Apelación_',
            ],
            'SEGUNDA SOLICITUD' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_SegundaSolicitud_',
            ],
            'TERCERA SOLICITUD' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_TerceraSolicitud_',
            ],
            'JUBILACIÓN' => [
                'vista' => 'admin.instructivaspoder.ipoderjubilacion',
                'nombre_archivo' => 'InstructivaPoder_Jubilación_',
            ],
            'PENSIÓN POR MUERTE' => [
                'vista' => 'admin.instructivaspoder.ipoderpensionmuerte',
                'nombre_archivo' => 'InstructivaPoder_PensiónMuerte_',
            ],
            'MASA HEREDITARIA' => [
                'vista' => 'admin.instructivaspoder.ipodermasahereditaria',
                'nombre_archivo' => 'InstructivaPoder_MasaHereditaria_',
            ],
            'RETIRO DE APORTES PARCIAL' => [
                'vista' => 'admin.instructivaspoder.ipoderretiroaportesparcial',
                'nombre_archivo' => 'InstructivaPoder_RetiroAportesParcial_',
            ],
            'RETIRO DE APORTES TOTAL' => [
                'vista' => 'admin.instructivaspoder.ipoderretiroaportestotal',
                'nombre_archivo' => 'InstructivaPoder_RetiroAportesTotal_',
            ],
            'COMPENSACIÓN DE COTIZACIONES (SENASIR)' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_CompensaciónCotizacionesSenasir_',
            ],
            'RECALIFICACIÓN' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_Recalificación_',
            ],
            'APELACIÓN DE RECALIFICACIÓN' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_ApelaciónRecalificación_',
            ],
            'RECALIFICACIÓN SEGUNDA SOLICITUD' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_RecalificaciónSegSol_',
            ],
            'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_ApelaciónRecalificaciónSegSol_',
            ],
            'APELACIÓN SEGUNDA SOLICITUD' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_ApelaciónSegundaSolicitud_',
            ],
            'APELACIÓN TERCERA SOLICITUD' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_ApelaciónTerceraSolicitud_',
            ],
        ];

        if (!isset($tramitesConfig[$tipoPdf])) {
            return redirect()->back()->with('error', 'Trámite no válido.');
        }

        $config = $tramitesConfig[$tipoPdf];

        $nombreClienteMayuscula = strtoupper($cliente->nombrecompleto);
        $nombreFormateado = str_replace(' ', '-', $nombreClienteMayuscula);
        $nombreArchivo = $config['nombre_archivo'] . $nombreFormateado . '.pdf';

        $estadoCivilOriginal = $cliente->estadocivil;
        $genero = strtolower($cliente->genero); // 'masculino' o 'femenino'
        $estadoCivil = $estadoCivilOriginal;

        if (Str::endsWith(strtolower($estadoCivilOriginal), '@')) {
            $sufijo = $genero === 'femenino' ? 'a' : 'o';
            $estadoCivil = substr($estadoCivilOriginal, 0, -1) . $sufijo;
        }

        $estadoCivil = Str::upper($estadoCivil);

        $derechohabientes = Derechohabientes::where('clienteid', $cliente->id)
        ->where('tramite', $tipoPdf)
        ->get();

        $pdf = PDF::loadView($config['vista'], compact(
            'tipoPdf',
            'personalIds',
            'personal',
            'estadoCivil',
            'cliente',
            'fechaactual',
            'sucursal',
            'tipopension','generofallecido','nombrefallecido','cifallecido','derechohabientes'
        ));

        return $pdf->stream('preview-instructiva.pdf');
    }


    public function crearinstructivapoderauditoria(ClienteAuditoria $clienteauditoria)
    {
        // Todos los tipos posibles
        $tramitesDisponibles = [
            'INVALIDEZ' => 'INVALIDEZ',
            'APELACION' => 'APELACION',
            'JUBILACION' => 'JUBILACION',
            'APORTE POR MUERTE' => 'APORTE POR MUERTE',
        ];

        // Buscar trámites ya registrados para el cliente
        $tramitesYaGenerados = InstructivasPoder::where('clienteid', $clienteauditoria->id)->pluck('tramite')->toArray();

        // Filtrar los disponibles
        $tramitesFiltrados = array_diff_key($tramitesDisponibles, array_flip($tramitesYaGenerados));

        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci', 'ciexp', 'ciudad')->where('categoria','PROVEEDOR INTERNO')->get();
        return view('admin.instructivaspoder.crearinstructivapoderauditoria', compact('clienteauditoria', 'personal', 'tramitesFiltrados'));
    }
    public function generarpdfinstructivaspoderauditoria(Request $request, ClienteAuditoria $clienteauditoria)
    {
        $tipoPdf = $request->input('tipo_pdf');
        /* $personalIds = explode(',', $request->input('personal_ids'));
        $personal = Proveedoresservicios::whereIn('id', $personalIds)->get(); */
        $personalIds = explode(',', $request->input('personal_ids'));

        // Obtener los registros sin perder el orden original
        $personal = Proveedoresservicios::whereIn('id', $personalIds)
            ->get()
            ->sortBy(function ($item) use ($personalIds) {
                return array_search($item->id, $personalIds);
            })
            ->values(); // reindexar por seguridad

        $tramitesConfig = [
            'INVALIDEZ' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidezauditoria',
                'nombre_archivo' => 'InstructivaPoder_Invalidez_',
            ],
            'APELACION' => [
                'vista' => 'admin.instructivaspoder.ipoderapelacionauditoria',
                'nombre_archivo' => 'InstructivaPoder_Apelacion_',
            ],
            'JUBILACION' => [
                'vista' => 'admin.instructivaspoder.ipoderjubilacionauditoria',
                'nombre_archivo' => 'InstructivaPoder_Jubilacion_',
            ],
            'APORTE POR MUERTE' => [
                'vista' => 'admin.instructivaspoder.ipoderaportemuerteauditoria',
                'nombre_archivo' => 'InstructivaPoder_AporteMuerte_',
            ],
        ];

        if (!isset($tramitesConfig[$tipoPdf])) {
            return redirect()->back()->with('error', 'Trámite no válido.');
        }

        $config = $tramitesConfig[$tipoPdf];

        $nombreClienteMayuscula = strtoupper($clienteauditoria->nombrecompleto);
        $nombreFormateado = str_replace(' ', '-', $nombreClienteMayuscula);
        $nombreArchivo = $config['nombre_archivo'] . $nombreFormateado . '.pdf';

        $pdf = PDF::loadView($config['vista'], compact('clienteauditoria', 'personal'));
        $rutaCarpeta = public_path('instructivaspoder/' . $clienteauditoria->id);
        if (!file_exists($rutaCarpeta)) {
            mkdir($rutaCarpeta, 0777, true);
        }
        $pdf->save($rutaCarpeta . '/' . $nombreArchivo);

        $registro = new InstructivasPoder();
        $registro->tramite = $tipoPdf;
        $registro->clienteid = $clienteauditoria->id;
        $registro->clientenombre = $clienteauditoria->nombrecompleto;
        $registro->documento = $nombreArchivo;
        $registro->usuarioregistroid = auth()->id();
        $registro->usuarioregistronombre = auth()->user()->name;

        foreach ($personal->take(10)->values() as $index => $persona) {
            $campo = 'apoderado' . ($index + 1);
            $registro->$campo = $persona->razonsocial;
        }

        $registro->save();

        return response()->download($rutaCarpeta . '/' . $nombreArchivo);
    }

    /* $pdf = PDF::loadView('admin.instructivaspoder.ipoderinvalidez', compact('cliente', 'personal')); */
    /* $pdfName = 'InstructivaPoder_' . $cliente->nombrecompleto;
    $pdfName .= '.pdf'; */
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Cliente $cliente)
    {
        $pdf = PDF::loadView('admin.instructivaspoder.ipoderinvalidez', compact('cliente'));
        $pdfName = 'InstructivaPoder_' . $cliente->nombrecompleto;
        $pdfName .= '.pdf';
        
        return $pdf->download($pdfName);
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
