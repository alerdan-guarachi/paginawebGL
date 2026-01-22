<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Cliente;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\StoreClienteRequest;
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

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /* public function __construct() { 
        $this->middleware ('can:admin.asociados.index')->only('index');
    } */

    public function index(Request $request)
    {
        $user = Auth::user();
        $cliente = Cliente::where('users_id', $user->id)->first();
        $cliente = Cliente::where('usuarioregistro', $user->name)->first();
        $cliente = Cliente::where('usuarioultimaactualizacion', $user->name)->first();
        $clientes = Cliente::simplePaginate(7);
        

        return view('admin.clientes.index', compact ('clientes', 'cliente'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Empresa $empresa)
{
    $suc = [
        'Cochabamba' => 'Cochabamba',
        'Santa Cruz' => 'Santa Cruz',
    ];
    $tipoide = [
        'CI' => 'CI',
        'Pasaporte' => 'Pasaporte',
        'Libreta servicio militar' => 'Libreta servicio militar',
        'Licencia de conducir' => 'Licencia de conducir',
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
        'Solter@' => 'Solter@',
        'Casad@' => 'Casad@',
        'Union libre' => 'Union libre',
        'Divorciad@' => 'Divorciad@',
        'Viud@' => 'Viud@',
    ];
    $genero = [
        'Masculino' => 'Masculino',
        'Femenino' => 'Femenino',
    ];
    $gradins = [
        'Analfabeto' => 'Analfabeto',
        'Primaria' => 'Primaria',
        'Secundaria' => 'Secundaria',
        'Tecnico' => 'Tecnico',
        'Universitario' => 'Universitario',
        'Postgrado' => 'Postgrado',
    ];
    $estlab = [
        'Activo' => 'Activo',
        'Inactivo' => 'Inactivo',
    ];
    /* $empresas = Empresa::pluck('nombreempresa'); */
    $empresas = Empresa::orderBy('nombreempresa')->pluck('nombreempresa', 'id');
    $pais = Pais::orderBy('pais')->pluck('pais', 'id');
    $ciudades = Ciudad::orderBy('ciudad')->pluck('ciudad', 'id');
    $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
    $aseguradoras = Aseguradora::orderBy('aseguradora')->pluck('aseguradora', 'id');
    $afp = Afp::orderBy('afp')->pluck('afp', 'id');
    return view('admin.clientes.create', compact('suc','tipoide', 'ciudadexp', 'estciv', 'genero', 'gradins', 'estlab', 'empresas', 'pais', 'ciudades', 'departamentos', 'aseguradoras', 'afp'));
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClienteRequest $request)
{
    $image_name = null;
    if ($request->hasFile('picture')) {
        $file = $request->file('picture');
        $image_name = time().'_'.$file->getClientOriginalName();
        $file->move(public_path("/image"), $image_name);
    }

    // Obtener el nombre de la empresa seleccionada
    /* $id = $request->input('empresa');
    $empresa = Empresa::findOrFail($id);
    $empresaNombre = $empresa->nombreempresa; */
    $empresaNombre = $request->input('empresa') ? Empresa::findOrFail($request->input('empresa'))->nombreempresa : null;
    
    $id = $request->input('ciudadresidencia');
    $ciudadresidencia = Ciudad::findOrFail($id);
    $ciudadNombre = $ciudadresidencia->ciudad;

    /* $id = $request->input('afp');
    $afp = Afp::findOrFail($id);
    $afpNombre = $afp->afp; */
    $afpNombre = $request->input('afp') ? Afp::findOrFail($request->input('afp'))->afp : null;

    $id = $request->input('lugarnacimiento');
    $lugarnacimiento = Departamento::findOrFail($id);
    $lugarnacNombre = $lugarnacimiento->departamento;

    /* $id = $request->input('aseguradora');
    $aseguradora = Aseguradora::findOrFail($id);
    $aseguradoraNombre = $aseguradora->aseguradora; */
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
    $clienteData['afp'] = $afpNombre;
    $clienteData['lugarnacimiento'] = $lugarnacNombre;
    $clienteData['aseguradora'] = $aseguradoraNombre;
    $clienteData['paisresidencia'] = $paisNombre;
    $clienteData['departamentoresidencia'] = $departamentoNombre;

    if ($image_name) {
        $clienteData['image'] = $image_name;
    }
    $cliente = Cliente::create($clienteData);
    


    // Redirigir con un mensaje de éxito
    return redirect()->route('admin.clientes.index', $cliente)->with('info', 'El perfil se creó con éxito');
}
/* $cliente = Cliente::create($request->all()+['image'=>$image_name]); */
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Cliente $cliente)
    {
        /* $pdf = PDF::loadView('admin.etiquetas.show', compact('cliente'));
        return $pdf->download('clientes.PDF'); */

        return view('admin.clientes.show', compact('cliente'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Cliente $cliente)
    {
        return view('admin.clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $data = $request->validated();
        if($request->hasFile('picture')){
            $file = $request->file('picture');
            $image_name=time().'_'.$file->getClientOriginalName();
            $file->move(public_path("/image"),$image_name);
            $data['image'] = $image_name;
        }
        
        /* $profile->update($request->all()+['image'=>$image_name,]); */
        $cliente->update($data);

        /* $profile->users()->sync($request->users); */

        return redirect()->route('admin.clientes.show', $cliente)->with('info', 'El perfil se actualizó con éxito');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('admin.clientes.index', $cliente)->with('eliminar', 'ok');
    }
    public function print(Cliente $cliente)
    {
        /* $pdf = PDF::loadView('admin.clientes.print', compact('cliente'));
        return $pdf->download('clientes.PDF'); */

        $pdf = PDF::loadView('admin.clientes.print', compact('cliente'));
    
        // Genera el nombre del archivo PDF con el nombre y, si están disponibles, los apellidos del cliente
        $pdfName = 'Etiqueta_'. $cliente->nombres;
    
        // Verifica si hay apellido paterno y lo incluye
        if ($cliente->apepaterno) {
            $pdfName .= ' ' . $cliente->apepaterno;
        }
    
        // Verifica si hay apellido materno y lo incluye
        if ($cliente->apematerno) {
            $pdfName .= ' ' . $cliente->apematerno;
        }
    
        $pdfName .= '.pdf';
        
        // Descarga el PDF con el nombre del cliente
        return $pdf->download($pdfName);
        /* return view('admin.clientes.print', compact('cliente')); */
    }

public function print2(Request $request, Cliente $cliente)
{
    $documentosSeleccionados = json_decode($request->input('documentosSeleccionados'));

    // Cargar la vista 'pdf.blade.php' y pasarle los datos del cliente y los documentos seleccionados
    $pdf = PDF::loadView('admin.clientes.print2', compact('cliente', 'documentosSeleccionados'));

    // Generar el nombre del archivo PDF
    $pdfName = 'Documento_' . $cliente->id . '.pdf';

    // Descargar el PDF
    return $pdf->download($pdfName);
}

    public function show2(Cliente $cliente)
    {

        return view('admin.clientes.show2', compact('cliente'));
    }
    public function formulario(Cliente $cliente)
    {
        $generoCliente = $cliente->genero;

        return view('admin.clientes.formulario', compact('cliente', 'generoCliente'));
    }


}
