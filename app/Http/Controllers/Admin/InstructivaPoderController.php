<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Cliente;
use App\Models\Personal;
use App\Models\Users;
use App\Models\Asociado;
use PDF;

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
        $nombrecompleto = $request->get('buscarpor');
        $clientes = Cliente::where('nombrecompleto', 'LIKE', "%$nombrecompleto%")
                            ->orderBy('nombrecompleto')
                            ->simplePaginate(10000);
        return view('admin.instructivaspoder.index', compact('clientes'));
    }
    public function buscarclientesita(Request $request)
    {
        $busqueda = $request->get('buscarpor');
        $clientes = Cliente::where(function ($query) use ($busqueda) {
            $query->where('nombrecompleto', 'like', "%$busqueda%")
                ->orWhere('ciudadresidencia', 'like', "%$busqueda%")
                ->orWhere('ci', 'like', "%$busqueda%")
                ->orWhere('id', 'like', "%$busqueda%");
        })->simplePaginate(1000);
        return view('admin.instructivaspoder.index', compact('clientes'));
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
    public function crearinspoderinvalidez(Cliente $cliente)
    {
        $personal = Personal::select('id', 'nombrecompleto', 'ci', 'ciexp', 'sucursal')->get();
        return view('admin.instructivaspoder.crearinspoderinvalidez', compact('cliente', 'personal'));
    }
    public function generarpdfinspoderinvalidez(Request $request, Cliente $cliente)
{
    $tipoPdf = $request->input('tipo_pdf');
    $personalIds = explode(',', $request->input('personal_ids'));
    $personal = Personal::whereIn('id', $personalIds)->get(); // Obtener personal seleccionado

    if ($tipoPdf === 'INVALIDEZ') {
        $pdfName = 'InstructivaPoder_' . $cliente->nombrecompleto . '.pdf';
        $pdf = PDF::loadView('admin.instructivaspoder.ipoderinvalidez', compact('cliente', 'personal'));
    } elseif ($tipoPdf === 'APELACION') {
        $pdfName = 'InstructivaApelacion_' . $cliente->nombrecompleto . '.pdf';
        $pdf = PDF::loadView('admin.instructivaspoder.ipoderapelacion', compact('cliente', 'personal'));
    }

    /* $pdf = PDF::loadView('admin.instructivaspoder.ipoderinvalidez', compact('cliente', 'personal')); */
    /* $pdfName = 'InstructivaPoder_' . $cliente->nombrecompleto;
    $pdfName .= '.pdf'; */
    
    return $pdf->download($pdfName);
}


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
