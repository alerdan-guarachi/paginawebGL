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
        })->orderBy('nombrecompleto')->simplePaginate(20);

        $clientesauditoria = ClienteAuditoria::where(function ($query) use ($busqueda) {
            $query->where('nombrecompleto', 'like', "%$busqueda%")
                ->orWhere('ci', 'like', "%$busqueda%")
                ->orWhere('id', 'like', "%$busqueda%");
        })->orderBy('nombrecompleto')->simplePaginate(20);

        return view('admin.instructivaspoder.nuevainstructiva', compact('clientes', 'clientesauditoria'));
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

        return view('admin.instructivaspoder.nuevainstructiva', compact('clientes', 'clientesauditoria'));
    }
    public function crearinstructivapoder(Cliente $cliente)
    {
        // Todos los tipos posibles
        $tramitesDisponibles = [
            'INVALIDEZ' => 'INVALIDEZ',
            'APELACION' => 'APELACION',
            'JUBILACION' => 'JUBILACION',
            'APORTE POR MUERTE' => 'APORTE POR MUERTE',
        ];

        // Buscar trámites ya registrados para el cliente
        $tramitesYaGenerados = InstructivasPoder::where('clienteid', $cliente->id)->pluck('tramite')->toArray();

        // Filtrar los disponibles
        $tramitesFiltrados = array_diff_key($tramitesDisponibles, array_flip($tramitesYaGenerados));

        $personal = Proveedoresservicios::select('id', 'razonsocial', 'ci', 'ciexp', 'ciudad')->where('categoria','PROVEEDOR INTERNO')->get();
        return view('admin.instructivaspoder.crearinstructivapoder', compact('cliente', 'personal', 'tramitesFiltrados'));
    }
    public function generarpdfinstructivaspoder(Request $request, Cliente $cliente)
    {
        $tipoPdf = $request->input('tipo_pdf');
        $personalIds = explode(',', $request->input('personal_ids'));
        $personal = Proveedoresservicios::whereIn('id', $personalIds)->get();

        $tramitesConfig = [
            'INVALIDEZ' => [
                'vista' => 'admin.instructivaspoder.ipoderinvalidez',
                'nombre_archivo' => 'InstructivaPoder_Invalidez_',
            ],
            'APELACION' => [
                'vista' => 'admin.instructivaspoder.ipoderapelacion',
                'nombre_archivo' => 'InstructivaPoder_Apelacion_',
            ],
            'JUBILACION' => [
                'vista' => 'admin.instructivaspoder.ipoderjubilacion',
                'nombre_archivo' => 'InstructivaPoder_Jubilacion_',
            ],
            'APORTE POR MUERTE' => [
                'vista' => 'admin.instructivaspoder.ipoderaportemuerte',
                'nombre_archivo' => 'InstructivaPoder_AporteMuerte_',
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
        $genero = strtolower($cliente->genero); // 'masculino' o 'femenino' en minúsculas
        $estadoCivil = $estadoCivilOriginal;

        if (Str::endsWith(strtolower($estadoCivilOriginal), '@')) {
            $sufijo = $genero === 'femenino' ? 'a' : 'o';
            $estadoCivil = substr($estadoCivilOriginal, 0, -1) . $sufijo;
        }

        // Convertir el estado civil procesado a MAYÚSCULAS para el PDF
        $estadoCivil = Str::upper($estadoCivil);

        $pdf = PDF::loadView($config['vista'], compact('cliente', 'personal', 'estadoCivil'));
        $rutaCarpeta = public_path('instructivaspoder/' . $cliente->id);
        if (!file_exists($rutaCarpeta)) {
            mkdir($rutaCarpeta, 0777, true);
        }
        $pdf->save($rutaCarpeta . '/' . $nombreArchivo);

        $registro = new InstructivasPoder();
        $registro->tramite = $tipoPdf;
        $registro->clienteid = $cliente->id;
        $registro->clientenombre = $cliente->nombrecompleto;
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
        $personalIds = explode(',', $request->input('personal_ids'));
        $personal = Proveedoresservicios::whereIn('id', $personalIds)->get();

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
