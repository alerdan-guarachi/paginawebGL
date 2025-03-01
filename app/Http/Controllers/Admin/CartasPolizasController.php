<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Empresa;
use App\Models\CartasPolizas;
use App\Models\Seguroempresa;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class CartasPolizasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() { 
        $this->middleware ('can:admin.cartaspolizas.index')->only('index');
    }

    public function index(Request $request)
{
    // Consultar los datos de ambas tablas
    $bancos = DB::table('bancos')->select('nombreBanco')->get();
    $seguros = DB::table('segurosempresas')->select('nombreSeguro')->get();

    // Combinar los nombres de banco y seguro
    $opciones = $bancos->merge($seguros);

    return view('admin.cartaspolizas.index', [
        'opciones' => $opciones,
    ]);
}
public function listacartaspolizas(Request $request)
{
    $nombrecliente = $request->get('buscarpor');

    $cartaspolizas = CartasPolizas::where('nombreclienteuno', 'LIKE', "%$nombrecliente%")
                    ->orwhere('nombreclientedos', 'LIKE', "%$nombrecliente%")
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(1000);

    return view('admin.cartaspolizas.listacartaspolizas', compact('cartaspolizas'));
}

public function descargarsolicitudpolizas(Request $request)
{
    $fecha = $request->input('fecha');
    $ciudad = $request->input('ciudad');
    $opcion = $request->input('opcion');
    $clienteuno = $request->input('clienteuno');
    $clienteunoci = $request->input('clienteunoci');
    $clientedos = $request->input('clientedos');
    $clientedosci = $request->input('clientedosci');

    $usuario = Auth::user();

    $nuevaCarta = new CartasPolizas();
    $nuevaCarta->fecha = $fecha;
    $nuevaCarta->ciudad = $ciudad;
    $nuevaCarta->banco = $opcion;
    $nuevaCarta->nombreclienteuno = $clienteuno;
    $nuevaCarta->ciclienteuno = $clienteunoci;
    $nuevaCarta->nombreclientedos = $clientedos;
    $nuevaCarta->ciclientedos = $clientedosci;
    $nuevaCarta->nombrecarta = 'SOLICITUD DE POLIZAS';
    $nuevaCarta->usuarioregistroid = $usuario->id;
    $nuevaCarta->usuarioregistronombre = $usuario->name;

    $html = View::make('admin.cartaspolizas.cartasolicitudpolizas', compact('fecha', 'ciudad', 'opcion', 'clienteuno', 'clienteunoci', 'clientedos', 'clientedosci'))->render();

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $dompdf = new Dompdf($options);

    $dompdf->loadHtml($html);
    $dompdf->render();

    $dateTime = now()->format('ymdHis');
    $fileName = $dateTime . '-Solicitud-Polizas-' . str_replace(' ', '_', $clienteuno) . '-' . str_replace(' ', '_', $clientedos) . '.pdf';

    $filePath = public_path('cartaspolizas/' . $fileName);

    file_put_contents($filePath, $dompdf->output());

    $nuevaCarta->documentocarta = $fileName;
    $nuevaCarta->save();


    return response()->download($filePath);
}

public function descargarreclamosolicitudpolizas(Request $request)
{
    $fecha = $request->input('fecha');
    $ciudad = $request->input('ciudad');
    $opcion = $request->input('opcion');
    $clienteuno = $request->input('clienteuno');
    $clienteunoci = $request->input('clienteunoci');
    $clientedos = $request->input('clientedos');
    $clientedosci = $request->input('clientedosci');

    $usuario = Auth::user();

    $nuevaCarta = new CartasPolizas();
    $nuevaCarta->fecha = $fecha;
    $nuevaCarta->ciudad = $ciudad;
    $nuevaCarta->banco = $opcion;
    $nuevaCarta->nombreclienteuno = $clienteuno;
    $nuevaCarta->ciclienteuno = $clienteunoci;
    $nuevaCarta->nombreclientedos = $clientedos;
    $nuevaCarta->ciclientedos = $clientedosci;
    $nuevaCarta->nombrecarta = 'RECLAMO DE SOLICITUD DE POLIZAS';
    $nuevaCarta->usuarioregistroid = $usuario->id;
    $nuevaCarta->usuarioregistronombre = $usuario->name;

    $html = View::make('admin.cartaspolizas.cartareclamosolicitudpolizas', compact('fecha', 'ciudad', 'opcion', 'clienteuno', 'clienteunoci', 'clientedos', 'clientedosci'))->render();

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $dompdf = new Dompdf($options);

    $dompdf->loadHtml($html);
    $dompdf->render();

    $dateTime = now()->format('ymdHis');
    $fileName = $dateTime . 'Reclamo-Solicitud-Polizas-' . str_replace(' ', '_', $clienteuno) . '-' . str_replace(' ', '_', $clientedos) . '.pdf';

    $filePath = public_path('cartaspolizas/' . $fileName);

    file_put_contents($filePath, $dompdf->output());

    $nuevaCarta->documentocarta = $fileName;
    $nuevaCarta->save();


    return response()->download($filePath);
}

public function descargarreclamosolicitudpolizasgenerales(Request $request)
{
    $fecha = $request->input('fecha');
    $ciudad = $request->input('ciudad');
    $opcion = $request->input('opcion');
    $clienteuno = $request->input('clienteuno');
    $clienteunoci = $request->input('clienteunoci');
    $clientedos = $request->input('clientedos');
    $clientedosci = $request->input('clientedosci');

    $usuario = Auth::user();

    $nuevaCarta = new CartasPolizas();
    $nuevaCarta->fecha = $fecha;
    $nuevaCarta->ciudad = $ciudad;
    $nuevaCarta->banco = $opcion;
    $nuevaCarta->nombreclienteuno = $clienteuno;
    $nuevaCarta->ciclienteuno = $clienteunoci;
    $nuevaCarta->nombreclientedos = $clientedos;
    $nuevaCarta->ciclientedos = $clientedosci;
    $nuevaCarta->nombrecarta = 'RECLAMO DE SOLICITUD DE POLIZAS GENERALES';
    $nuevaCarta->usuarioregistroid = $usuario->id;
    $nuevaCarta->usuarioregistronombre = $usuario->name;

    $html = View::make('admin.cartaspolizas.cartareclamosolicitudpolizasgenerales', compact('fecha', 'ciudad', 'opcion', 'clienteuno', 'clienteunoci', 'clientedos', 'clientedosci'))->render();

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $dompdf = new Dompdf($options);

    $dompdf->loadHtml($html);
    $dompdf->render();

    $dateTime = now()->format('ymdHis');
    $fileName = $dateTime . 'Reclamo-Solicitud-Polizas-Generales-' . str_replace(' ', '_', $clienteuno) . '-' . str_replace(' ', '_', $clientedos) . '.pdf';

    $filePath = public_path('cartaspolizas/' . $fileName);

    file_put_contents($filePath, $dompdf->output());

    $nuevaCarta->documentocarta = $fileName;
    $nuevaCarta->save();


    return response()->download($filePath);
}



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Consultar los datos de ambas tablas
    $bancos = DB::table('bancos')->select('nombreBanco')->get();
    $seguros = DB::table('segurosempresas')->select('nombreSeguro')->get();

    // Combinar los nombres de banco y seguro
    $opciones = $bancos->merge($seguros);

    return view('admin.cartaspolizas.create', [
        'opciones' => $opciones,
    ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Empresa $empresa)
    {
        $empresa = Empresa::create($request->all());

        return redirect()->route('admin.empresas.index', $empresa)->with('info', 'La empresa se creó con exito');
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
