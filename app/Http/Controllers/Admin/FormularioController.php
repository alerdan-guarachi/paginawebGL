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
use Illuminate\Support\Str;

class FormularioController extends Controller
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
    public function create()
{
    return view('admin.clientes.formulario');
}

/* public function store(Request $request, Formulario $formulario, StoreClienteRequest $cliente)
        {
            $formulario = Formulario::create($request->all());
    
            return redirect()->route('admin.clientes.index', $formulario , compact('cliente'))->with('info', 'El formulario se registro con exito');
        } */

        /* public function store(Request $request)
        {
            // Recuperar los datos del formulario
            $cliente_id = $request->input('cliente_id'); // Asegúrate de tener este campo en tu formulario
            $pregunta_id = $request->input('pregunta_id'); // Asegúrate de tener este campo en tu formulario
            $diagnostico = $request->input('diagnostico');
            $fecha = $request->input('fecha');
            $tiempo = $request->input('tiempo');
            $gradorecuperacion = $request->input('gradorecuperacion');
            $medico = $request->input('medico');
            $direccionmedico = $request->input('direccionmedico');
    
            // Guardar los datos en la base de datos
            $formulario = new Formulario();
            $formulario->cliente_id = $cliente_id;
            $formulario->pregunta_id = $pregunta_id;
            $formulario->diagnostico = $diagnostico;
            $formulario->fecha = $fecha;
            $formulario->tiempo = $tiempo;
            $formulario->gradorecuperacion = $gradorecuperacion;
            $formulario->medico = $medico;
            $formulario->direccionmedico = $direccionmedico;
            $formulario->save();
    
            return redirect()->route('admin.clientes.index', $formulario )->with('info', 'El formulario se registro con exito');

        } */

        public function store(Request $request)
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

    return redirect()->route('admin.clientes.index')->with('info', 'Los formularios se registraron con éxito');
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
        
    }

    public function create2(StoreClienteRequest $cliente)
        {
            $preguntas = Pregunta::all();
            $formularios = Formulario::all();
            return view('admin.clientes.formulario2', compact('preguntas', 'formularios', 'cliente'));
        }
    public function diagnostico(Request $request, Formulario $formulario, StoreClienteRequest $cliente)
        {
            $formulario = Formulario::create($request->all());
    
            return redirect()->route('admin.clientes.index', $formulario , compact('cliente'))->with('info', 'El formulario se registro con exito');
        }

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
            public function mostrarFormulario(Cliente $cliente)
    {
        // Aquí obtienes $cliente de alguna manera, puede ser de la base de datos u otro origen.
        return view('formulario', ['cliente' => $cliente]);
    }

}
