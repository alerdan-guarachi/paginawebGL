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
        $sucursalbateria = $request->input('sucursalbateria');
        $sucursalbateriaauditoria = $request->input('sucursalbateriaauditoria');
        $sucursalbateriacomunes = $request->input('sucursalbateriacomunes');
        $sucursalAreaaccion = $request->input('sucursalareaaccion');
        $sucursalBateriaproveedores = $request->input('sucursalbateriaproveedores');

        $fechaInicial = $fechaInicial ? Carbon::parse($fechaInicial)->startOfDay()->toDateTimeString() : null;
        $fechaFinal = $fechaFinal ? Carbon::parse($fechaFinal)->endOfDay()->toDateTimeString() : null;
        
        if ($fechaInicial && $fechaFinal) {

            if ($fechaInicial == $fechaFinal) {
                $fechaFinal = Carbon::parse($fechaFinal)->endOfDay();
            }
            switch ($tabla) {
                case 'bateriasubclientesita':
                    $data = Bateriasubcliente::whereBetween('created_at', [$fechaInicial, $fechaFinal])
                    ->whereNotNull('clienteitaid')
                    ->where('clienteitaid', '!=', '')
                    ->orderBy('created_at', 'asc')
                    ->get();
                    break;
                case 'bateriasubclientesauditoria':
                    $data = Bateriasubcliente::whereBetween('created_at', [$fechaInicial, $fechaFinal])
                    ->whereNotNull('clienteauditoriaid')
                    ->where('clienteauditoriaid', '!=', '')
                    ->orderBy('created_at', 'asc')
                    ->get();
                    break;
                case 'bateriasubclientescomunes': 
                    $data = Bateriasubcliente::whereBetween('created_at', [$fechaInicial, $fechaFinal])
                    ->whereNotNull('clientecomunid')
                    ->where('clientecomunid', '!=', '')
                    ->orderBy('created_at', 'asc')
                    ->get();
                break;
                    
                case 'bateriaproveedores':
                    switch ($sucursalBateriaproveedores) {
                        case 'tipo1':
                            $data = Bateriaproveedor::where('sucursal', 'COCHABAMBA')
                                ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                                ->orderBy('created_at', 'asc')
                                ->get();
                            break;
                        case 'tipo2':
                            $data = Bateriaproveedor::where('sucursal', 'SANTA CRUZ')
                                ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                                ->orderBy('created_at', 'asc')
                                ->get();
                            break;
                        default:
                            $data = Bateriaproveedor::whereBetween('created_at', [$fechaInicial, $fechaFinal])
                                ->orderBy('created_at', 'asc')
                                ->get();
                            break;
                    }
                    break;
                case 'clientescomunes':
                    $data = ClienteComun::whereBetween('created_at', [$fechaInicial, $fechaFinal])
                    ->orderBy('created_at', 'asc')
                    ->get();
                    break;
                case 'clientesauditoria':
                    $data = ClienteAuditoria::whereBetween('created_at', [$fechaInicial, $fechaFinal])
                    ->orderBy('created_at', 'asc')
                    ->get();
                    break;
                case 'clientes':
                    $data = Cliente::whereBetween('created_at', [$fechaInicial, $fechaFinal])->get();
                    break;
                case 'proveedores':
                    $data = Proveedor::whereBetween('created_at', [$fechaInicial, $fechaFinal])
                    ->orderBy('created_at', 'asc')
                    ->get();
                    break;
                case 'programacionsubclientesita':
                    $data = Programacionsubcliente::with('estadoprogramacionsubcliente', 'documentacionsubcliente')
                        ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                        ->whereNotNull('clienteitaid')
                        ->orderBy('created_at', 'asc')
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
                    $data = $data->sortBy('clienteitanombre');
                    break;
                case 'programacionsubclientescomunes':
                    $data = Programacionsubcliente::with('estadoprogramacionsubclientecomun', 'documentacionsubclientecomun')
                        ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                        ->whereNotNull('clientecomunid')
                        ->orderBy('created_at', 'asc')
                        ->get();
                    $data->each(function ($item) {
                        $item->fechaatencionprogramacion = null;
                        $item->created_at = null;
                        if ($item->estadoprogramacionsubclientecomun->isNotEmpty()) {
                            foreach ($item->estadoprogramacionsubclientecomun as $estado) {
                                if ($estado->clientecomunid == $item->clientecomunid
                                    && $estado->accionnombre == $item->accionnombre
                                    && $estado->fechabateria == $item->fechabateria
                                    && $estado->fechaatencionprogramacion) {
                                    $item->fechaatencionprogramacion = $estado->fechaatencionprogramacion;
                                    break;
                                }
                            }
                        }
                        if ($item->documentacionsubclientecomun->isNotEmpty()) {
                            foreach ($item->documentacionsubclientecomun as $documento) {
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
                    $data = $data->sortBy('clientecomunnombre');
                    break;
                case 'programacionsubclientesauditoria':
                    $data = Programacionsubcliente::with('estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria')
                        ->whereBetween('created_at', [$fechaInicial, $fechaFinal])
                        ->whereNotNull('clienteauditoriaid')
                        ->orderBy('created_at', 'asc')
                        ->get();
                    $data->each(function ($item) {
                        $item->fechaatencionprogramacion = null;
                        $item->created_at = null;
                        if ($item->estadoprogramacionsubclienteauditoria->isNotEmpty()) {
                            foreach ($item->estadoprogramacionsubclienteauditoria as $estado) {
                                if ($estado->clienteauditoriaid == $item->clienteauditoriaid
                                    && $estado->accionnombre == $item->accionnombre
                                    && $estado->fechabateria == $item->fechabateria
                                    && $estado->fechaatencionprogramacion) {
                                    $item->fechaatencionprogramacion = $estado->fechaatencionprogramacion;
                                    break;
                                }
                            }
                        }
                        if ($item->documentacionsubclienteauditoria->isNotEmpty()) {
                            foreach ($item->documentacionsubclienteauditoria as $documento) {
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
                    $data = $data->sortBy('clienteauditorianombre');
                    break;
                default:
                    $data = [];
                    break;
            }
        } else {
            switch ($tabla) {
                case 'bateriasubclientesita':
                    $data = Bateriasubcliente::join('clientes', 'bateriasubclientes.clienteitaid', '=', 'clientes.id')
                        ->whereNotNull('bateriasubclientes.clienteitaid')
                        ->orderBy('created_at', 'asc')
                        ->select(
                            'bateriasubclientes.id as id',
                            'bateriasubclientes.*', 
                            'clientes.sucursal'
                        );
                
                    switch ($sucursalbateria) {
                        case 'tipo1':
                            $data = $data->where('clientes.sucursal', 'COCHABAMBA');
                            break;
                        case 'tipo2':
                            $data = $data->where('clientes.sucursal', 'SANTA CRUZ');
                            break;
                    }
                    $data = $data->get();
                    break;

                case 'bateriasubclientesauditoria':
                    $data = Bateriasubcliente::join('clienteauditorias', 'bateriasubclientes.clienteauditoriaid', '=', 'clienteauditorias.id')
                        ->whereNotNull('bateriasubclientes.clienteauditoriaid')
                        ->orderBy('created_at', 'asc')
                        ->select(
                            'bateriasubclientes.id as id',
                            'bateriasubclientes.*', 
                            'clienteauditorias.sucursal'
                        );
                
                    switch ($sucursalbateriaauditoria) {
                        case 'tipo1':
                            $data = $data->where('clienteauditorias.sucursal', 'COCHABAMBA');
                            break;
                        case 'tipo2':
                            $data = $data->where('clienteauditorias.sucursal', 'SANTA CRUZ');
                            break;
                    }
                    $data = $data->get();
                    break;

                case 'bateriasubclientescomunes':  
                        $data = Bateriasubcliente::join('clientescomunes', 'bateriasubclientes.clientecomunid', '=', 'clientescomunes.id')
                            ->whereNotNull('bateriasubclientes.clientecomunid')
                            ->orderBy('created_at', 'asc')
                            ->select(
                                'bateriasubclientes.id as id',
                                'bateriasubclientes.*', 
                                'clientescomunes.sucursal'
                            );
                    
                        switch ($sucursalbateriacomunes) {
                            case 'tipo1':
                                $data = $data->where('clientescomunes.sucursal', 'COCHABAMBA');
                                break;
                            case 'tipo2':
                                $data = $data->where('clientescomunes.sucursal', 'SANTA CRUZ');
                                break;
                        }
                    
                    $data = $data->get();
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
                case 'clientescomunes':
                    $data = ClienteComun::all();
                    break;
                case 'clientesauditoria':
                    $data = ClienteAuditoria::all();
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
                    $data = Programacionsubcliente::whereNotNull('clienteitaid')
                    ->orderBy('created_at', 'asc')
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
                    $data = $data->sortBy('clienteitanombre');
                    break;
                case 'programacionsubclientescomunes':
                    $data = Programacionsubcliente::whereNotNull('clientecomunid')
                    ->orderBy('created_at', 'asc')
                    ->get();
                    $data->each(function ($item) {
                        $item->fechaatencionprogramacion = null;
                        $item->created_at = null;

                        if ($item->estadoprogramacionsubclientecomun->isNotEmpty()) {
                            foreach ($item->estadoprogramacionsubclientecomun as $estado) {
                                if ($estado->clientecomunid == $item->clientecomunid
                                    && $estado->accionnombre == $item->accionnombre
                                    && $estado->fechabateria == $item->fechabateria
                                    && $estado->fechaatencionprogramacion) {
                                    $item->fechaatencionprogramacion = $estado->fechaatencionprogramacion;
                                    break;
                                }
                            }
                        }
                        if ($item->documentacionsubclientecomun->isNotEmpty()) {
                            foreach ($item->documentacionsubclientecomun as $documento) {
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
                    $data = $data->sortBy('clientecomunnombre');
                    break;
                case 'programacionsubclientesauditoria':
                    $data = Programacionsubcliente::whereNotNull('clienteauditoriaid')
                    ->orderBy('created_at', 'asc')
                    ->get();
                    $data->each(function ($item) {
                        $item->fechaatencionprogramacion = null;
                        $item->created_at = null;

                        if ($item->estadoprogramacionsubclienteauditoria->isNotEmpty()) {
                            foreach ($item->estadoprogramacionsubclienteauditoria as $estado) {
                                if ($estado->clienteauditoriaid == $item->clienteauditoriaid
                                    && $estado->accionnombre == $item->accionnombre
                                    && $estado->fechabateria == $item->fechabateria
                                    && $estado->fechaatencionprogramacion) {
                                    $item->fechaatencionprogramacion = $estado->fechaatencionprogramacion;
                                    break;
                                }
                            }
                        }
                        if ($item->documentacionsubclienteauditoria->isNotEmpty()) {
                            foreach ($item->documentacionsubclienteauditoria as $documento) {
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
                    $data = $data->sortBy('clienteauditorianombre');
                    break;
                default:
                    $data = [];
                    break;
            }
        }
        $nombresTablas = [
            'bateriasubclientesita' => 'BATERIA DE CLIENTES ITA',
            'bateriasubclientesauditoria' => 'BATERIA DE CLIENTES AUDITORIA',
            'bateriasubclientescomunes' => 'BATERIA DE CLIENTES COMUNES',
            'bateriaproveedores' => 'BATERIA DE PROVEEDORES',
            'clientescomunes' => 'CLIENTES COMUNES',
            'clientesauditoria' => 'CLIENTES DE AUDITORÍA MÉDICA',
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
