<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Empresa;
use App\Models\Asociado;
use App\Models\Bateriaproveedor;
use App\Models\Bateriaclientecomun;
use App\Models\Areaaccion;
use App\Models\Departamento;
use App\Models\Area;
use App\Models\Banco;
use App\Models\Tipoareaaccion;
use App\Models\ClienteAuditoria;
use App\Models\ClienteComun;
use App\Models\ClienteBanco;
use App\Models\Pais;
use App\Models\Ciudad;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Aseguradora;
use App\Models\Contactosubcliente;
use App\Models\Requisitosubcliente;
use App\Models\Afp;
use App\Models\Bateriasubcliente;
use App\Models\Estadoprogramacionsubcliente;
use App\Models\Estadocotizacionsubcliente;
use App\Models\Programacionsubcliente;
use App\Models\Documentacionsubcliente;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TablaExport;
use Carbon\Carbon;



class ReporteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct() { 
        $this->middleware ('can:admin.reportes.index')->only('index');
    }

    public function index(Request $request)
    {
        return view('admin.reportes.index');
    }
    
    public function generarPDF(Request $request)
    {
        $tabla = $request->input('tabla');
        $fechaInicial = $request->input('fecha_inicial');
        $fechaFinal = $request->input('fecha_final');
        $sucursalProveedor = $request->input('sucursal');
        $sucursalAreaaccion = $request->input('sucursalareaaccion');
        $sucursalBateriaproveedores = $request->input('sucursalbateriaproveedores');

        $fechaInicial = $fechaInicial ? Carbon::parse($fechaInicial)->startOfDay()->toDateTimeString() : null;
        $fechaFinal = $fechaFinal ? Carbon::parse($fechaFinal)->endOfDay()->toDateTimeString() : null;
        
        if ($fechaInicial && $fechaFinal) {

            if ($fechaInicial == $fechaFinal) {
                $fechaFinal = Carbon::parse($fechaFinal)->endOfDay();
            }
            switch ($tabla) {
                case 'bateriasubclientes':
                    $data = Bateriasubcliente::whereBetween('created_at', [$fechaInicial, $fechaFinal])->get();
                    break;
                case 'bateriaproveedores':
                    switch ($sucursalBateriaproveedores) {
                        case 'tipo1':
                            $data = Bateriaproveedor::where('sucursal', 'COCHABAMBA')
                                ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                                ->get();
                            break;
                        case 'tipo2':
                            $data = Bateriaproveedor::where('sucursal', 'SANTA CRUZ')
                                ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                                ->get();
                            break;
                        default:
                            $data = Bateriaproveedor::whereBetween('created_at', [$fechaInicial, $fechaFinal])->get();
                            break;
                    }
                    break;
                case 'areaacciones':
                    switch ($sucursalAreaaccion) {
                        case 'tipo1':
                            $data = Areaaccion::where('sucursal', 'COCHABAMBA')
                                ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                                ->get();
                            break;
                        case 'tipo2':
                            $data = Areaaccion::where('sucursal', 'SANTA CRUZ')
                                ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                                ->get();
                            break;
                        default:
                            $data = Areaaccion::whereBetween('created_at', [$fechaInicial, $fechaFinal])->get();
                            break;
                    }
                    break;
                case 'clientescomunes':
                    $data = ClienteComun::whereBetween('created_at', [$fechaInicial, $fechaFinal])->get();
                    break;
                case 'clientesauditoria':
                    $data = ClienteAuditoria::whereBetween('created_at', [$fechaInicial, $fechaFinal])->get();
                    break;
                case 'clientesbancos':
                    $data = ClienteBanco::whereBetween('created_at', [$fechaInicial, $fechaFinal])->get();
                    break;
                case 'contactosubclientes':
                    $data = Contactosubcliente::whereBetween('created_at', [$fechaInicial, $fechaFinal])->get();
                    break;
                case 'clientes':
                    $data = Cliente::whereBetween('created_at', [$fechaInicial, $fechaFinal])->get();
                    break;
                case 'proveedores':
                    $data = Proveedor::whereBetween('created_at', [$fechaInicial, $fechaFinal])->get();
                    break;
                case 'programacionsubclientesita':
                    $data = Programacionsubcliente::with('estadoprogramacionsubcliente', 'documentacionsubcliente')
                        ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                        ->whereNotNull('clienteitaid')
                        ->get();
                    $data->each(function ($item) {
                        $item->fechaatencionprogramacion = null;
                        $item->created_at = null;
                        if ($item->estadoprogramacionsubcliente->isNotEmpty()) {
                            foreach ($item->estadoprogramacionsubcliente as $estado) {
                                if ($estado->clienteitaid == $item->clienteitaid
                                    && $estado->accionnombre == $item->accionnombre
                                    && $estado->fechabateria == $item->fechabateria
                                    && $estado->fechaatencionprogramacion) {
                                    $item->fechaatencionprogramacion = $estado->fechaatencionprogramacion;
                                    break;
                                }
                            }
                        }
                        if ($item->documentacionsubcliente->isNotEmpty()) {
                            foreach ($item->documentacionsubcliente as $documento) {
                                if ($documento->clienteitaid == $item->clienteitaid
                                    && $documento->accion == $item->accionnombre
                                    && $documento->fechabateria == $item->fechabateria
                                    && $documento->created_at) {
                                    $item->created_at = $documento->created_at;
                                    break;
                                }
                            }
                        }
                    });
                    break;
                case 'programacionsubclientescomunes':
                    $data = Programacionsubcliente::with('estadoprogramacionsubcliente', 'documentacionsubcliente')
                        ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                        ->whereNotNull('clientecomunid')
                        ->get();
                    $data->each(function ($item) {
                        $item->fechaatencionprogramacion = null;
                        $item->created_at = null;
                        if ($item->estadoprogramacionsubcliente->isNotEmpty()) {
                            foreach ($item->estadoprogramacionsubcliente as $estado) {
                                if ($estado->clientecomunid == $item->clientecomunid
                                    && $estado->accionnombre == $item->accionnombre
                                    && $estado->fechabateria == $item->fechabateria
                                    && $estado->fechaatencionprogramacion) {
                                    $item->fechaatencionprogramacion = $estado->fechaatencionprogramacion;
                                    break;
                                }
                            }
                        }
                        if ($item->documentacionsubcliente->isNotEmpty()) {
                            foreach ($item->documentacionsubcliente as $documento) {
                                if ($documento->clientecomunid == $item->clientecomunid
                                    && $documento->accion == $item->accionnombre
                                    && $documento->fechabateria == $item->fechabateria
                                    && $documento->created_at) {
                                    $item->created_at = $documento->created_at;
                                    break;
                                }
                            }
                        }
                    });
                    break;
                case 'programacionsubclientesauditoria':
                    $data = Programacionsubcliente::with('estadoprogramacionsubcliente', 'documentacionsubcliente')
                        ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                        ->whereNotNull('clienteauditoriaid')
                        ->get();
                    $data->each(function ($item) {
                        $item->fechaatencionprogramacion = null;
                        $item->created_at = null;
                        if ($item->estadoprogramacionsubcliente->isNotEmpty()) {
                            foreach ($item->estadoprogramacionsubcliente as $estado) {
                                if ($estado->clienteauditoriaid == $item->clienteauditoriaid
                                    && $estado->accionnombre == $item->accionnombre
                                    && $estado->fechabateria == $item->fechabateria
                                    && $estado->fechaatencionprogramacion) {
                                    $item->fechaatencionprogramacion = $estado->fechaatencionprogramacion;
                                    break;
                                }
                            }
                        }
                        if ($item->documentacionsubcliente->isNotEmpty()) {
                            foreach ($item->documentacionsubcliente as $documento) {
                                if ($documento->clienteauditoriaid == $item->clienteauditoriaid
                                    && $documento->accion == $item->accionnombre
                                    && $documento->fechabateria == $item->fechabateria
                                    && $documento->created_at) {
                                    $item->created_at = $documento->created_at;
                                    break;
                                }
                            }
                        }
                    });
                    break;
                default:
                    $data = [];
                    break;
            }
        } else {
            switch ($tabla) {
                case 'bateriasubclientes':
                    $data = Bateriasubcliente::all();
                    break;
                case 'bateriaproveedores':
                    switch ($sucursalBateriaproveedores) {
                        case 'tipo1':
                            $data = Bateriaproveedor::where('sucursal', 'COCHABAMBA')
                                ->get();
                            break;
                        case 'tipo2':
                            $data = Bateriaproveedor::where('sucursal', 'SANTA CRUZ')
                                ->get();
                            break;
                        default:
                            $data = Bateriaproveedor::all();
                            break;
                    }
                    break;
                case 'areaacciones':
                    switch ($sucursalAreaaccion) {
                        case 'tipo1':
                            $data = Areaaccion::where('sucursal', 'COCHABAMBA')
                                ->get();
                            break;
                        case 'tipo2':
                            $data = Areaaccion::where('sucursal', 'SANTA CRUZ')
                                ->get();
                            break;
                        default:
                            $data = Areaaccion::all();
                            break;
                    }
                    break;
                case 'clientescomunes':
                    $data = ClienteComun::all();
                    break;
                case 'clientesauditoria':
                    $data = ClienteAuditoria::all();
                    break;
                case 'clientesbancos':
                    $data = ClienteBanco::all();
                    break;
                case 'contactosubclientes':
                    $data = Contactosubcliente::all();
                    break;
                case 'clientes':
                    $data = Cliente::all();
                    break;
                case 'proveedores':
                    switch ($sucursalProveedor) {
                        case 'tipo1':
                            $data = Proveedor::where('ciudad', 'COCHABAMBA')->get();
                            break;
                        case 'tipo2':
                            $data = Proveedor::where('ciudad', 'SANTA CRUZ')->get();
                            break;
                        default:
                            $data = Proveedor::all();
                            break;
                    }
                    break;
                case 'programacionsubclientesita':
                    $data = Programacionsubcliente::whereNotNull('clienteitaid')->get();
                    $data->each(function ($item) {
                        $item->fechaatencionprogramacion = null;
                        $item->created_at = null;

                        if ($item->estadoprogramacionsubcliente->isNotEmpty()) {
                            foreach ($item->estadoprogramacionsubcliente as $estado) {
                                if ($estado->clienteitaid == $item->clienteitaid
                                    && $estado->accionnombre == $item->accionnombre
                                    && $estado->fechabateria == $item->fechabateria
                                    && $estado->fechaatencionprogramacion) {
                                    $item->fechaatencionprogramacion = $estado->fechaatencionprogramacion;
                                    break;
                                }
                            }
                        }
                        if ($item->documentacionsubcliente->isNotEmpty()) {
                            foreach ($item->documentacionsubcliente as $documento) {
                                if ($documento->clienteitaid == $item->clienteitaid
                                    && $documento->accion == $item->accionnombre
                                    && $documento->fechabateria == $item->fechabateria
                                    && $documento->created_at) {
                                    $item->created_at = $documento->created_at;
                                    break;
                                }
                            }
                        }
                    });
                    break;
                case 'programacionsubclientescomunes':
                    $data = Programacionsubcliente::whereNotNull('clientecomunid')->get();
                    $data->each(function ($item) {
                        $item->fechaatencionprogramacion = null;
                        $item->created_at = null;

                        if ($item->estadoprogramacionsubcliente->isNotEmpty()) {
                            foreach ($item->estadoprogramacionsubcliente as $estado) {
                                if ($estado->clientecomunid == $item->clientecomunid
                                    && $estado->accionnombre == $item->accionnombre
                                    && $estado->fechabateria == $item->fechabateria
                                    && $estado->fechaatencionprogramacion) {
                                    $item->fechaatencionprogramacion = $estado->fechaatencionprogramacion;
                                    break;
                                }
                            }
                        }
                        if ($item->documentacionsubcliente->isNotEmpty()) {
                            foreach ($item->documentacionsubcliente as $documento) {
                                if ($documento->clientecomunid == $item->clientecomunid
                                    && $documento->accion == $item->accionnombre
                                    && $documento->fechabateria == $item->fechabateria
                                    && $documento->created_at) {
                                    $item->created_at = $documento->created_at;
                                    break;
                                }
                            }
                        }
                    });
                    break;
                case 'programacionsubclientesauditoria':
                    $data = Programacionsubcliente::whereNotNull('clienteauditoriaid')->get();
                    $data->each(function ($item) {
                        $item->fechaatencionprogramacion = null;
                        $item->created_at = null;

                        if ($item->estadoprogramacionsubcliente->isNotEmpty()) {
                            foreach ($item->estadoprogramacionsubcliente as $estado) {
                                if ($estado->clienteauditoriaid == $item->clienteauditoriaid
                                    && $estado->accionnombre == $item->accionnombre
                                    && $estado->fechabateria == $item->fechabateria
                                    && $estado->fechaatencionprogramacion) {
                                    $item->fechaatencionprogramacion = $estado->fechaatencionprogramacion;
                                    break;
                                }
                            }
                        }
                        if ($item->documentacionsubcliente->isNotEmpty()) {
                            foreach ($item->documentacionsubcliente as $documento) {
                                if ($documento->clienteauditoriaid == $item->clienteauditoriaid
                                    && $documento->accion == $item->accionnombre
                                    && $documento->fechabateria == $item->fechabateria
                                    && $documento->created_at) {
                                    $item->created_at = $documento->created_at;
                                    break;
                                }
                            }
                        }
                    });
                    break;
                default:
                    $data = [];
                    break;
            }
        }
        $nombresTablas = [
            'bateriasubclientes' => 'BATERIA DE CLIENTES',
            'bateriaproveedores' => 'BATERIA DE PROVEEDORES',
            'areaacciones' => 'BATERIA GENERAL',
            'clientescomunes' => 'CLIENTES COMUNES',
            'clientesauditoria' => 'CLIENTES DE AUDITORÍA MÉDICA',
            'clientesbancos' => 'CLIENTES DE BANCOS',
            'contactosubclientes' => 'CONTACTOS DE CLIENTES',
            'clientes' => 'CLIENTES ITA',
            'programacionsubclientescomunes' => 'PROGRAMACIÓN DE CLIENTES COMUNES',
            'programacionsubclientesauditoria' => 'PROGRAMACIÓN DE CLIENTES DE AUDITORÍA MÉDICA',
            'programacionsubclientesita' => 'PROGRAMACIÓN DE CLIENTES ITA',
            'proveedores' => 'PROVEEDORES',
        ];
        $nombreTabla = isset($nombresTablas[$tabla]) ? $nombresTablas[$tabla] : 'Tabla Desconocida';
        $nombreArchivo = 'Reporte_' . $nombreTabla . '_' . Carbon::now()->format('Ymd_His') . '.pdf';
        $pdf = PDF::loadView('admin.reportes.reportepdf', compact('nombresTablas', 'data', 'tabla', 'fechaInicial', 'fechaFinal'));
    
        return $pdf->download($nombreArchivo);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.empresas.create');
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
