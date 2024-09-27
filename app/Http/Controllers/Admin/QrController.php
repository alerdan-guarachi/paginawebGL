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
use App\Models\Etiqueta;
use Illuminate\Support\Facades\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Clientes;


class QrController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /* public function __construct() { 
        $this->middleware('can:admin.profiles.index')->only('index');
    } */

    public function generarQR(Request $request, Cliente $cliente)
    {
        // Generar el contenido del QR con los datos del formulario
        $contenidoQR = json_encode($request->all()); // Convertir el array a JSON
    
        // Generar el nombre del archivo QR
        $nombreQR = 'qr_temporal.png';
    
        // Generar la ruta donde se guardará el QR
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
        return view('admin.clientes.formulario', ['rutaQR' => asset('temp/' . $nombreQR)]);
    }
    
    public function index(Request $request, Cliente $cliente)
    {
        /* $user = Auth::user();
        $cliente = Cliente::where('users_id', $user->id)->first();
        $cliente = Cliente::where('usuarioregistro', $user->name)->first();
        $cliente = Cliente::where('usuarioultimaactualizacion', $user->name)->first();
        $clientes = Cliente::simplePaginate(7);
        

        return view('admin.etiquetas.index', compact ('clientes', 'cliente')); */
        return view('admin.etiquetas.index', compact('cliente'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Empresa $empresa)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClienteRequest $request)
    {
      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Cliente $cliente)
    {
       /*  $pdf = PDF::loadView('admin.etiquetas.show', compact('cliente'));
        return $pdf->download('clientes.PDF'); */

        return view('admin.etiquetas.show', compact('cliente'));
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
}
