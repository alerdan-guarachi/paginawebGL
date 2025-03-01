<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Proveedor;
use App\Models\Bateriaproveedor;
use App\Models\Areaaccion;
use App\Models\Departamento;
use App\Models\Area;
use App\Models\Banco;
use App\Models\Tipoareaaccion;
use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Http\Requests\StoreBateriaproveedorRequest;
use Illuminate\Support\Facades\Auth;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $nombreasociado = $request->get('buscarpor');

        $proveedores = Proveedor::where('proveedor', 'LIKE', "%$nombreasociado%")
                          ->orderBy('proveedor')
                          ->simplePaginate(1000);
        $nombreusuario = auth()->user()->name;
        return view('admin.proveedores.index', compact('proveedores','nombreusuario'));
    }
    public function buscarproveedor(Request $request)
    {
        $busqueda = $request->get('buscarpor');
        $proveedores = Proveedor::where(function ($query) use ($busqueda) {
                    $query->where('proveedor', 'like', "%$busqueda%")
                            ->orWhere('ciudad', 'like', "%$busqueda%")
                            ->orWhere('id', 'like', "%$busqueda%")
                            ->orderBy('proveedor');
                          })->simplePaginate(1000);

        return view('admin.proveedores.index', compact('proveedores'));
    }
    public function getAcciones($areaId)
    {
        $acciones = AreaAccion::where('id', $areaId)->pluck('accion');
        return response()->json($acciones);
    } 
    public function create(Proveedor $proveedor)
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
        $mododepago = [
            'EFECTIVO' => 'EFECTIVO',
            'TRANSFERENCIA BANCARIA' => 'TRANSFERENCIA BANCARIA',
            'CHEQUE' => 'CHEQUE',
        ];
        $tipocuenta = [
            'CUENTA CORRIENTE' => 'CUENTA CORRIENTE',
            'CUENTA DE AHORRO' => 'CUENTA DE AHORRO',
            'CUENTA INFANTIL' => 'CUENTA INFANTIL',
            'CUENTA JOVEN' => 'CUENTA JOVEN',
            'CUENTA MANCOMUNADA' => 'CUENTA MANCOMUNADA',
            'CUENTA NÓMINA' => 'CUENTA NÓMINA',
            'CUENTA NO NÓMINA' => 'CUENTA NO NÓMINA',
            'CUENTA ONLINE' => 'CUENTA ONLINE',
            'CUENTA REMUNERADA' => 'CUENTA REMUNERADA', 
        ];
        $departamentos = Departamento::whereIn('id', [1, 3])
                                 ->orderBy('departamento')
                                 ->pluck('departamento', 'id');
        $bancos = Banco::orderBy('nombrebanco')->pluck('nombrebanco', 'id');

        return view('admin.proveedores.create', compact('proveedor','departamentos', 'estadoproveedor', 'areas', 'accionesPorArea', 'mododepago', 'bancos', 'tipocuenta'));
    }
    public function show(Proveedor $proveedor)
    {
        return view('admin.proveedores.show', compact('proveedor'));
    }
    public function store(StoreProveedorRequest $request)
    {
        $idCiudad = $request->input('ciudad');
        $ciudad = Departamento::findOrFail($idCiudad);
        $ciudadNombre = $ciudad->departamento;

        $idBanco = $request->input('banco');
        if ($idBanco) {
            $banco = Banco::findOrFail($idBanco);
            $bancoNombre = $banco->nombrebanco;
        } else {
            $bancoNombre = "NO APLICA";
        }

        $proveedorData = $request->all();
        $estadoSeleccionado = $request->input('estadoproveedor');
        $modopagoSeleccionado = $request->input('mododepago');

        $proveedorData['ciudad'] = $ciudadNombre;
        $proveedorData['banco'] = $bancoNombre;
        $proveedorData['estadoproveedor'] = $estadoSeleccionado;
        $proveedorData['mododepago'] = $modopagoSeleccionado;

        $proveedor = Proveedor::create($proveedorData);

        return redirect()->route('admin.proveedores.index', $proveedor)->with('info', 'El proveedor se creó con éxito');
    }
    public function edit(Proveedor $proveedor)
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
        $mododepago = [
            'EFECTIVO' => 'EFECTIVO',
            'TRANSFERENCIA BANCARIA' => 'TRANSFERENCIA BANCARIA',
            'CHEQUE' => 'CHEQUE',
        ];
        $tipocuenta = [
            'CUENTA CORRIENTE' => 'CUENTA CORRIENTE',
            'CUENTA DE AHORRO' => 'CUENTA DE AHORRO',
            'CUENTA INFANTIL' => 'CUENTA INFANTIL',
            'CUENTA JOVEN' => 'CUENTA JOVEN',
            'CUENTA MANCOMUNADA' => 'CUENTA MANCOMUNADA',
            'CUENTA NÓMINA' => 'CUENTA NÓMINA',
            'CUENTA NO NÓMINA' => 'CUENTA NO NÓMINA',
            'CUENTA ONLINE' => 'CUENTA ONLINE',
            'CUENTA REMUNERADA' => 'CUENTA REMUNERADA', 
        ];
        
        $departamentos = Departamento::whereIn('id', [1, 3]) 
                                 ->orderBy('departamento')
                                 ->pluck('departamento', 'id');
        $departamentoActual = $proveedor->departamento;
        
        $bancos = Banco::orderBy('nombrebanco')->pluck('nombrebanco', 'id');
        $bancoActual = $proveedor->nombrebanco;

        return view('admin.proveedores.edit', compact('bancoActual','departamentoActual','proveedor','departamentos', 'estadoproveedor', 'areas', 'accionesPorArea', 'mododepago', 'bancos', 'tipocuenta'));
    }
    public function update(UpdateProveedorRequest $request, Proveedor $proveedor)
    {
        $idCiudad = $request->input('ciudad');
        $ciudad = Departamento::findOrFail($idCiudad);
        $ciudadNombre = $ciudad->departamento;
        
        $idBanco = $request->input('banco');
        if ($idBanco) {
            $banco = Banco::findOrFail($idBanco);
            $bancoNombre = $banco->nombrebanco;
        } else {
            $bancoNombre = "NO APLICA";
        }
        $proveedorData = $request->validated();
        $proveedorData['ciudad'] = $ciudadNombre;
        $proveedorData['banco'] = $bancoNombre;

        $proveedor->update($proveedorData);

        return redirect()->route('admin.proveedores.show', $proveedor)->with('info', 'El proveedor se actualizó con éxito');
    }
    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return redirect()->route('admin.proveedores.index', $proveedor)->with('eliminar', 'ok');
    }

    //CREAR BATERIA  DE PROVEEDOR
    public function crearbateriaproveedor(Proveedor $proveedor)
    {
        $nombreProveedor = $proveedor->proveedor;
        $sucursalProveedor = $proveedor->ciudad;
        $accionesProveedor = BateriaProveedor::where('proveedor', $nombreProveedor)->pluck('accion')->toArray();
        
        /* $idTipoAreaEstudio = 2;
        $accionesProveedorEstudios = BateriaProveedor::where('proveedor', $nombreProveedor)
            ->whereIn('accion', function ($query) use ($idTipoAreaEstudio) {
                $query->select('accion')->from('areaacciones')->where('tipoid', $idTipoAreaEstudio);
            })
            ->pluck('accion');
        
        $idTipoAreaEspecialidad = 1;
        $accionesProveedorEspecialidad = BateriaProveedor::where('proveedor', $nombreProveedor)
                ->whereIn('accion', function ($query) use ($idTipoAreaEspecialidad) {
                    $query->select('accion')->from('areaacciones')->where('tipoid', $idTipoAreaEspecialidad);
                })
                ->pluck('accion'); */

        $idTipoAreaEstudio = 2; 
        $accionesProveedorEstudios = BateriaProveedor::where('proveedor', $nombreProveedor)
            ->whereIn('accion', function ($query) use ($idTipoAreaEstudio) {
                $query->select('accion')->from('areaacciones')->where('tipoid', $idTipoAreaEstudio);
            })
            ->get(['accion', 'precio', 'preciocompra', 'asociado']);

        $idTipoAreaEspecialidad = 1;
        $accionesProveedorEspecialidad = BateriaProveedor::where('proveedor', $nombreProveedor)
            ->whereIn('accion', function ($query) use ($idTipoAreaEspecialidad) {
                $query->select('accion')->from('areaacciones')->where('tipoid', $idTipoAreaEspecialidad);
            })
            ->get(['accion', 'precio', 'preciocompra', 'asociado']);

        $areasConAcciones = AreaAccion::where('sucursal', $sucursalProveedor)
        ->where('estado', 'ACTIVO')
        ->pluck('areasid')
        ->unique()
        ->toArray();

        $areas = Area::orderBy('nombrearea', 'asc')
            ->where('idtipoarea', 2)
            ->whereIn('id', $areasConAcciones)
            ->whereNotIn('nombrearea', function($query) use ($nombreProveedor) {
                $query->select('accion')->from('bateriaproveedores')->where('proveedor', $nombreProveedor);
            })
            ->pluck('nombrearea', 'id');

        $accionesPorArea = [];
        foreach ($areas as $id => $nombreArea) {
            $accionesPorArea[$id] = AreaAccion::where('areasid', $id)
                ->where('sucursal', $sucursalProveedor)
                ->whereNotIn('accion', $accionesProveedor)
                ->orderBy('accion', 'asc')
                ->pluck('accion');
        }

        $areasConAcciones2 = AreaAccion::where('sucursal', $sucursalProveedor)
                    ->where('estado', 'ACTIVO')
                    ->pluck('areasid')
                    ->unique()
                    ->toArray();
                    
        $areas2 = Area::orderBy('nombrearea', 'asc')
                    ->where('idtipoarea', 1)
                    ->whereIn('id', $areasConAcciones)
                    ->whereNotIn('nombrearea', function($query) use ($nombreProveedor) {
                        $query->select('area')->from('bateriaproveedores')->where('proveedor', $nombreProveedor);
                    })
                    ->pluck('nombrearea', 'id');

        $estadoproveedor = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ]; 
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
        $id = $proveedor->nombrecompleto ? Proveedor::where('nombrecompleto', $proveedor->nombrecompleto)->value('id') : null;
        
        return view('admin.proveedores.crearbateriaproveedor', compact('accionesProveedorEspecialidad','accionesProveedorEstudios','departamentos', 'estadoproveedor', 'areas', 'accionesPorArea', 'proveedor', 'id', 'accionesProveedor', 'areas2'));
    }
    public function guardarbateriaproveedor(StoreBateriaproveedorRequest $request)
    {
        $proveedorID = $request->input('proveedorid');
        $proveedor = Proveedor::findOrFail($proveedorID);
        $accionesSeleccionadas = $request->input('accion');
        $tipoArea = $request->input('tipoarea');
        $sucursal = $request->input('sucursal');
        $asociado = $request->input('asociado');
        $asociadoid = $request->input('asociadoid');
        $sucursalProveedor = $proveedor->ciudad;
        if ($tipoArea === 'Estudios') {
            $areasSeleccionadas = $request->input('area');
            if (!is_array($areasSeleccionadas)) {
                $areasSeleccionadas = [$areasSeleccionadas];
            }
 
            foreach ($areasSeleccionadas as $areaId) {
                $area = Area::findOrFail($areaId);
                $areaNombre = $area->nombrearea;

                foreach ($accionesSeleccionadas as $accionNombre) {
                    $areaAccion = AreaAccion::where('areasid', $areaId)
                                            ->where('accion', $accionNombre)
                                            ->where('sucursal', $sucursalProveedor)
                                            ->first(); 

                    $precioAccion = $areaAccion ? $areaAccion->precio : 0;
                    $precioAccioncompra = $areaAccion ? $areaAccion->preciocompra : 0;

                    $proveedorData = $request->except(['accion', '_token']);
                    $proveedorData['accion'] = $accionNombre;
                    $proveedorData['area'] = $areaNombre;
                    $proveedorData['proveedornombre'] = $proveedor->proveedor;
                    $proveedorData['tipoarea'] = 'ESTUDIOS';
                    $proveedorData['precio'] = $precioAccion;
                    $proveedorData['preciocompra'] = $precioAccioncompra;
                    $proveedorData['sucursal'] = $sucursal;
                    $proveedorData['asociado'] = $asociado;
                    $proveedorData['asociadoid'] = $asociadoid;
                    $proveedorData['usuarioid'] = Auth::id();
                    $proveedorData['usuarioregistro'] = Auth::user()->name;

                    Bateriaproveedor::create($proveedorData);
                }
            }
        } elseif ($tipoArea === 'Especialidades') {
            foreach ($accionesSeleccionadas as $accionNombre) {
                $areaAccion = AreaAccion::where('accion', $accionNombre)
                                        ->where('sucursal', $sucursalProveedor)
                                        ->first();

                $precioAccion = $areaAccion ? $areaAccion->precio : 0;
                $precioAccioncompra = $areaAccion ? $areaAccion->preciocompra : 0;
                
                $proveedorData = $request->except(['accion', '_token']);
                $proveedorData['accion'] = $accionNombre;
                $proveedorData['area'] = $accionNombre;
                $proveedorData['proveedor'] = $proveedor->proveedor;
                $proveedorData['tipoarea'] = 'ESPECIALIDADES';
                $proveedorData['precio'] = $precioAccion;
                $proveedorData['preciocompra'] = $precioAccioncompra;
                $proveedorData['sucursal'] = $sucursal;
                $proveedorData['asociado'] = $asociado;
                $proveedorData['asociadoid'] = $asociadoid;
                $proveedorData['usuarioid'] = Auth::id();
                $proveedorData['usuarioregistro'] = Auth::user()->name;

                Bateriaproveedor::create($proveedorData);
            }
        }

        return redirect()->route('admin.proveedores.crearbateriaproveedor', ['proveedor' => $proveedor])->with('info', 'La batería se creó con éxito');
    }
    public function verbateriaproveedor(Proveedor $proveedor)
    {
        $nombreProveedor = $proveedor->proveedor;
        
        $idTipoAreaEstudio = 2;
        $accionesProveedorEstudios = BateriaProveedor::where('proveedor', $nombreProveedor)
            ->whereIn('accion', function ($query) use ($idTipoAreaEstudio) {
                $query->select('accion')->from('areaacciones')->where('tipoid', $idTipoAreaEstudio);
            })
            ->pluck('accion')->toArray();
        
        $idTipoAreaEspecialidad = 1;
        $accionesProveedorEspecialidad = BateriaProveedor::where('proveedor', $nombreProveedor)
                ->whereIn('accion', function ($query) use ($idTipoAreaEspecialidad) {
                    $query->select('accion')->from('areaacciones')->where('tipoid', $idTipoAreaEspecialidad);
                })
                ->pluck('accion');

        return view('admin.proveedores.verbateriaproveedor', compact('accionesProveedorEstudios','accionesProveedorEspecialidad','proveedor'));
    }
    public function eliminaraccionproveedor(Bateriaproveedor $accion)
    {
        $accion->delete();

        return redirect()->route('admin.proveedores.index', $accion)->with('eliminar', 'ok');
    }
    public function edit2(Proveedor $proveedor)
    {
        $nombreProveedor = $proveedor->proveedor;
            $accionesProveedor = BateriaProveedor::where('proveedornombre', $nombreProveedor)->pluck('accion')->toArray();
        return view('admin.proveedores.edit2', compact('proveedor', 'accionesProveedor'));
    }
    public function update2(Request $request, Proveedor $proveedor)
        {
            $proveedor->update($request->all());

            $proveedor->acciones()->sync($request->acciones);

            return redirect()->route('admin.proveedores.index', $proveedor)->with('info', 'El proveedor se actualizó con éxito');
        }
    
    
    
}
