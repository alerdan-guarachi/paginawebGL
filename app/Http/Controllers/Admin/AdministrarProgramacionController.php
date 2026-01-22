<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
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
use App\Models\Empresa;
use App\Models\Pais;
use App\Models\Ciudad;
use App\Models\Cliente;
use App\Models\Aseguradora;
use App\Models\Afp;
use App\Models\Bateriasubcliente;
use App\Models\Estadoprogramacionsubcliente;
use App\Models\Programacionsubcliente;
use App\Models\Documentacionsubcliente;
use App\Http\Requests\UpdateProgramacionsubclienteRequest;
use App\Models\Informefinal;
use App\Models\ProveedorInformefinal;
use App\Services\WhatsAppService;
use App\Models\Requisitosubcliente;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\File;

class AdministrarProgramacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /* public function __construct() { 
        $this->middleware('can:admin.users.index')->only('index');
    } */

    public function index(Request $request, ClienteAuditoria $clienteauditoria, ClienteComun $clientecomun, Cliente $cliente, ClienteBanco $clientebanco)
    {
        $busqueda = $request->get('buscarpor');
        $fechaActual = $busqueda ?? now()->toDateString();

        $clientesComunesCount = DB::table('clientescomunes')->count();
        $clientesBancosCount = DB::table('clientebancos')->count();
        $clientesITACount = DB::table('clientes')->count();
        $clientesAuditoriasCount = DB::table('clienteauditorias')->count();

        // FECHAS PROXIMAS DE CLIENTES AUDITORIA
        $programacionclienteauditorias = Programacionsubcliente::whereNotNull('clienteauditorianombre')
            ->where('clienteauditorianombre', '!=', '')
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->orderBy('horadesde', 'asc')
            ->get();

        $nombreClienteAuditoria = $clienteauditoria->nombrecompleto;
        $accionesClienteAuditoria = BateriaSubCliente::where('clienteauditorianombre', $nombreClienteAuditoria)->pluck('accionnombre')->toArray();
        $idAuditoria = $clienteauditoria->nombrecompleto ? ClienteAuditoria::where('nombrecompleto', $clienteauditoria->nombrecompleto)->value('id') : null;
        $accionesPorAreaAuditoria = Programacionsubcliente::where('clienteauditorianombre', $nombreClienteAuditoria)
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->get(['accionnombre', 'proveedornombre', 'fechaasignada', 'horaasignada']);
        $estadoRegistradosAuditoria = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesClienteAuditoria)
            ->where('clienteauditorianombre', $nombreClienteAuditoria)
            ->pluck('accionnombre')->toArray();
        $accionesDisponiblesAuditoria = $accionesPorAreaAuditoria;
        $accionesPorAreasAuditoria = Programacionsubcliente::where('clienteauditorianombre', $nombreClienteAuditoria)->pluck('accionnombre', 'accionnombre');

        // FECHAS PROXIMAS DE CLIENTES COMUNES
        $programacionclientecomunes = Programacionsubcliente::whereNotNull('clientecomunnombre')
            ->where('clientecomunnombre', '!=', '')
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->orderBy('horadesde', 'asc')
            ->get();

        $nombreClienteComun = $clientecomun->nombrecompleto;
        $accionesClienteComun = BateriaSubCliente::where('clientecomunnombre', $nombreClienteComun)->pluck('accionnombre')->toArray();
        $idComun = $clientecomun->nombrecompleto ? ClienteComun::where('nombrecompleto', $clientecomun->nombrecompleto)->value('id') : null;
        $accionesPorAreaComun = Programacionsubcliente::where('clientecomunnombre', $nombreClienteComun)
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->get(['accionnombre', 'proveedornombre', 'fechaasignada', 'horaasignada']);
        $estadoRegistradosComun = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesClienteComun)
            ->where('clientecomunnombre', $nombreClienteComun)
            ->pluck('accionnombre')->toArray();
        $accionesDisponiblesComun = $accionesPorAreaComun;
        $accionesPorAreasComun = Programacionsubcliente::where('clientecomunnombre', $nombreClienteComun)->pluck('accionnombre', 'accionnombre');

        // FECHAS PROXIMAS DE CLIENTES ITA
        $programacionclienteitas = Programacionsubcliente::whereNotNull('clienteitanombre')
            ->where('clienteitanombre', '!=', '')
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->orderBy('horadesde', 'asc')
            ->get();

        $nombreClienteIta = $cliente->nombrecompleto;
        $accionesClienteIta = BateriaSubCliente::where('clienteitanombre', $nombreClienteIta)->pluck('accionnombre')->toArray();
        $idIta = $cliente->nombrecompleto ? Cliente::where('nombrecompleto', $cliente->nombrecompleto)->value('id') : null;
        $accionesPorAreaIta = Programacionsubcliente::where('clienteitanombre', $nombreClienteIta)
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->get(['accionnombre', 'proveedornombre', 'fechaasignada', 'horaasignada']);
        $estadoRegistradosIta = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesClienteIta)
            ->where('clienteitanombre', $nombreClienteIta)
            ->pluck('accionnombre')->toArray();
        $accionesDisponiblesIta = $accionesPorAreaIta;
        $accionesPorAreasIta = Programacionsubcliente::where('clienteitanombre', $nombreClienteIta)->pluck('accionnombre', 'accionnombre');

        // FECHAS PROXIMAS DE CLIENTES BANCOS
        $programacionclientebancos = Programacionsubcliente::whereNotNull('clientenombre')
            ->where('clientenombre', '!=', '')
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->orderBy('horadesde', 'asc')
            ->get();

        $nombreClienteBanco = $clientebanco->nombrecompleto;
        $accionesClienteBanco = BateriaSubCliente::where('clientenombre', $nombreClienteBanco)->pluck('accionnombre')->toArray();
        $idBanco = $clientebanco->nombrecompleto ? ClienteBanco::where('nombrecompleto', $clientebanco->nombrecompleto)->value('id') : null;
        $accionesPorAreaBanco = Programacionsubcliente::where('clientenombre', $nombreClienteBanco)
            ->whereDate('fechaasignada', '=', $fechaActual)
            ->get(['accionnombre', 'proveedornombre', 'fechaasignada', 'horaasignada']);
        $estadoRegistradosBanco = Estadoprogramacionsubcliente::whereIn('accionnombre', $accionesClienteBanco)
            ->where('clientebanconombre', $nombreClienteBanco)
            ->pluck('accionnombre')->toArray();
        $accionesDisponiblesBanco = $accionesPorAreaBanco;
        $accionesPorAreasBanco = Programacionsubcliente::where('clientenombre', $nombreClienteBanco)->pluck('accionnombre', 'accionnombre');

        return view('admin.admprogramaciones.index', compact(
            'programacionclientebancos',
            'programacionclienteitas',
            'programacionclientecomunes',
            'programacionclienteauditorias',
            'clientesComunesCount',
            'clientesBancosCount',
            'clientesITACount',
            'clientesAuditoriasCount',
            'accionesPorAreasAuditoria',
            'accionesPorAreaAuditoria',
            'accionesDisponiblesAuditoria',
            'clienteauditoria',
            'idAuditoria',
            'accionesClienteAuditoria',
            'estadoRegistradosAuditoria',
            'accionesPorAreasComun',
            'accionesPorAreaComun',
            'accionesDisponiblesComun',
            'clientecomun',
            'idComun',
            'accionesClienteComun',
            'estadoRegistradosComun',
            'accionesPorAreasIta',
            'accionesPorAreaIta',
            'accionesDisponiblesIta',
            'cliente',
            'idIta',
            'accionesClienteIta',
            'estadoRegistradosIta',
            'accionesPorAreasBanco',
            'accionesPorAreaBanco',
            'accionesDisponiblesBanco',
            'clientebanco',
            'idBanco',
            'accionesClienteBanco',
            'estadoRegistradosBanco',
            'fechaActual'
        ));
    }
    public function documentacionpendiente(Request $request, Asociado $asociado, Cliente $cliente)
    {
        $buscar = $request->get('buscarpor');

        /* PROGRAMACIONES ITA SIN INFORMES */
        $programaciones = Programacionsubcliente::where('proveedornombre', 'LIKE', "%$buscar%")
        ->whereNotNull('clienteitaid')
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('documentacionsubclientes')
                ->whereRaw('documentacionsubclientes.clienteitaid = programacionsubclientes.clienteitaid')
                ->whereRaw('documentacionsubclientes.fechabateria = programacionsubclientes.fechabateria')
                ->whereRaw('documentacionsubclientes.accion = programacionsubclientes.accionnombre');
        })
        ->simplePaginate(200);
        $informesfinalessin = ProveedorInformefinal::where('proveedorasignado', 'LIKE', "%$buscar%")
        ->whereNotNull('clienteitaid')
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('informesfinales')
                ->whereRaw('informesfinales.clienteitaid = proveedorinformesfinales.clienteitaid')
                ->whereRaw('informesfinales.fechabateria = proveedorinformesfinales.fechabateria');
        })
        ->simplePaginate(200);

        /* PROGRAMACIONES ITA CON INFORMES */
        $documentaciones = Programacionsubcliente::join('documentacionsubclientes', function ($join) {
            $join->on('documentacionsubclientes.clienteitaid', '=', 'programacionsubclientes.clienteitaid')
                 ->on('documentacionsubclientes.fechabateria', '=', 'programacionsubclientes.fechabateria')
                 ->on('documentacionsubclientes.accion', '=', 'programacionsubclientes.accionnombre');
        })
        ->where('programacionsubclientes.proveedornombre', 'LIKE', "%$buscar%")
        ->whereNotNull('programacionsubclientes.clienteitaid')
        ->select('programacionsubclientes.*', 'documentacionsubclientes.document', 'documentacionsubclientes.created_at as document_created_at', 'documentacionsubclientes.id as docid')
        ->simplePaginate(200);
        $informesfinalescon = ProveedorInformefinal::join('informesfinales', function ($join) {
            $join->on('informesfinales.clienteitaid', '=', 'proveedorinformesfinales.clienteitaid')
                 ->on('informesfinales.fechabateria', '=', 'proveedorinformesfinales.fechabateria');
        })
        ->where('proveedorinformesfinales.proveedorasignado', 'LIKE', "%$buscar%")
        ->whereNotNull('proveedorinformesfinales.clienteitaid')
        ->select('proveedorinformesfinales.*', 'informesfinales.document', 'informesfinales.created_at as document_created_at', 'informesfinales.id as docid')
        ->simplePaginate(200);


        /* PROGRAMACIONES AUDITORIA SIN INFORME */
        $programacionesauditoria = Programacionsubcliente::where('proveedornombre', 'LIKE', "%$buscar%")
        ->whereNotNull('clienteauditoriaid')
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('documentacionsubclientes')
                ->whereRaw('documentacionsubclientes.clienteauditoriaid = programacionsubclientes.clienteauditoriaid')
                ->whereRaw('documentacionsubclientes.fechabateria = programacionsubclientes.fechabateria')
                ->whereRaw('documentacionsubclientes.accion = programacionsubclientes.accionnombre');
        })
        ->simplePaginate(200);
        $informesfinalesauditoriasin = ProveedorInformefinal::where('proveedorasignado', 'LIKE', "%$buscar%")
        ->whereNotNull('clienteauditoriaid')
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('informesfinales')
                ->whereRaw('informesfinales.clienteauditoriaid = proveedorinformesfinales.clienteauditoriaid')
                ->whereRaw('informesfinales.fechabateria = proveedorinformesfinales.fechabateria');
        })
        ->simplePaginate(200);

        /* PROGRAMACIONES AUDITORIA CON INFORME */
        $documentacionesauditoria = Programacionsubcliente::join('documentacionsubclientes', function ($join) {
            $join->on('documentacionsubclientes.clienteauditoriaid', '=', 'programacionsubclientes.clienteauditoriaid')
                 ->on('documentacionsubclientes.fechabateria', '=', 'programacionsubclientes.fechabateria')
                 ->on('documentacionsubclientes.accion', '=', 'programacionsubclientes.accionnombre');
        })
        ->where('programacionsubclientes.proveedornombre', 'LIKE', "%$buscar%")
        ->whereNotNull('programacionsubclientes.clienteauditoriaid')
        ->select('programacionsubclientes.*', 'documentacionsubclientes.document', 'documentacionsubclientes.created_at as document_created_at', 'documentacionsubclientes.id as docid')
        ->simplePaginate(200);
        $informesfinalesauditoriacon = ProveedorInformefinal::join('informesfinales', function ($join) {
            $join->on('informesfinales.clienteauditoriaid', '=', 'proveedorinformesfinales.clienteauditoriaid')
                 ->on('informesfinales.fechabateria', '=', 'proveedorinformesfinales.fechabateria');
        })
        ->where('proveedorinformesfinales.proveedorasignado', 'LIKE', "%$buscar%")
        ->whereNotNull('proveedorinformesfinales.clienteauditoriaid')
        ->select('proveedorinformesfinales.*', 'informesfinales.document', 'informesfinales.created_at as document_created_at', 'informesfinales.id as docid')
        ->simplePaginate(200);

        return view('admin.admprogramaciones.documentacionpendiente', compact(
            'asociado',
            'programaciones',
            'documentaciones',
            'programacionesauditoria',
            'documentacionesauditoria',
            'informesfinalessin',
            'informesfinalesauditoriasin',
            'informesfinalescon',
            'informesfinalesauditoriacon',
        ));
    }
    public function documentacionactiva(Request $request, Asociado $asociado, Cliente $cliente)
    {
        $clienteitanombre = $request->get('buscarpor');
        
        $clientes = Documentacionsubcliente::with('estadoprogramacionsubcliente')
                    ->where('clienteitanombre', 'LIKE', "%$clienteitanombre%")
                    ->whereNotNull('clienteitaid')
                    ->orderBy('clienteitanombre')
                    ->simplePaginate(10000);
        $clientes2 = Documentacionsubcliente::with('estadoprogramacionsubclientecomun')
                    ->where('clientecomunnombre', 'LIKE', "%$clienteitanombre%")
                    ->whereNotNull('clientecomunid')
                    ->orderBy('clientecomunnombre')
                    ->simplePaginate(10000);

        return view('admin.admprogramaciones.documentacionactiva', compact('cliente', 'asociado', 'clientes', 'clientes2'));
    }
    public function unirpdf(Request $request)
    {

        return view('admin.admprogramaciones.unirpdf');
    }
    public function upload(Request $request)
    {
        if ($request->hasFile('pdfs')) {
            foreach ($request->file('pdfs') as $pdf) {
                $pdf->storeAs('public/uploads', $pdf->getClientOriginalName());
            }
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
    public function merge(Request $request)
    {
        $pdf = new Fpdi();

        // Obtén el array de nombres de archivos enviados en el JSON
        $files = $request->input('files');

        // Ruta donde se almacenaron los PDFs
        $uploadPath = storage_path('app/public/uploads/');

        foreach ($files as $fileName) {
            $path = $uploadPath . $fileName;
            if (!file_exists($path)) {
                continue; // o manejar el error de archivo no encontrado
            }
            $pageCount = $pdf->setSourceFile($path);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl = $pdf->importPage($i);
                $pdf->AddPage();
                $pdf->useTemplate($tpl);
            }
        }

        return response()->streamDownload(function () use ($pdf) {
            $pdf->Output('I');
        }, 'merged.pdf');
    }
    public function clientescreadoshoy(Request $request)
    {
        $fechaActual = $request->input('buscarpor', now()->toDateString());

        // Clientes creados hoy
        $clientes = Cliente::whereDate('created_at', $fechaActual)->simplePaginate(10000);
        $clientes2 = ClienteAuditoria::whereDate('created_at', $fechaActual)->simplePaginate(10000);
        $clientes3 = ClienteComun::whereDate('created_at', $fechaActual)->simplePaginate(10000);

        // Baterías creadas hoy
        $bateriashoyita = Bateriasubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->simplePaginate(10000);

        $bateriashoycomun = Bateriasubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clientecomunid')
            ->simplePaginate(10000);

        $bateriashoyauditoria = Bateriasubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->simplePaginate(10000);

        // Programaciones creadas hoy
        $programacioneshoyita = Programacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->simplePaginate(10000);

        $programacioneshoycomun = Programacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clientecomunid')
            ->simplePaginate(10000);

        $programacioneshoyauditoria = Programacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->simplePaginate(10000);

        $informeshoyita = Documentacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->simplePaginate(10000);

        $informeshoycomun = Documentacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clientecomunid')
            ->simplePaginate(10000);

        $informeshoyauditoria = Documentacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->simplePaginate(10000);

        $informesfinaleshoyita = Informefinal::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->simplePaginate(10000);

        $informesfinaleshoyauditoria = Informefinal::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->simplePaginate(10000);
            
        $requisitoshoyita = Requisitosubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->simplePaginate(10000);

        $requisitoshoyauditoria = Requisitosubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->simplePaginate(10000);
            

        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
            
        $todosClientes = $clientes->merge($clientes2)->merge($clientes3)->toArray();
        $contadorclientes = array_reduce($todosClientes, function ($count, $item) {
                return $count + 1;
            }, 0);
        
        $todosbaterias = $bateriashoyita->merge($bateriashoycomun)->merge($bateriashoyauditoria)->toArray();
        $contadorbaterias = array_reduce($todosbaterias, function ($count, $item) {
                return $count + 1;
            }, 0);

        $todosprogramaciones = $programacioneshoyita->merge($programacioneshoycomun)->merge($programacioneshoyauditoria)->toArray();
        $contadorprogramaciones = array_reduce($todosprogramaciones, function ($count, $item) {
                return $count + 1;
            }, 0);

        $todosinformes = $informeshoyita->merge($informeshoycomun)->merge($informeshoyauditoria)->toArray();
            $contadorinformes = array_reduce($todosinformes, function ($count, $item) {
                return $count + 1;
            }, 0);
                
        $todosinformesfinales = $informesfinaleshoyita->merge($informesfinaleshoyauditoria)->toArray();
        $contadorinformesfinales = array_reduce($todosinformesfinales, function ($count, $item) {
                return $count + 1;
            }, 0);
            
        $todosrequisitos = $requisitoshoyita->merge($requisitoshoyauditoria)->toArray();
        $contadorrequisitos = array_reduce($todosrequisitos, function ($count, $item) {
                return $count + 1;
            }, 0);

        return view('admin.admprogramaciones.clientescreadoshoy', compact(
            'clientes',
            'clientes2',
            'clientes3',
            'bateriashoyita',
            'bateriashoycomun',
            'bateriashoyauditoria',
            'programacioneshoyita',
            'programacioneshoycomun',
            'programacioneshoyauditoria',
            'fechaActual','contadorclientes','contadorbaterias','contadorprogramaciones'
            ,'informeshoyita'
            ,'informeshoycomun'
            ,'informeshoyauditoria'
            ,'contadorinformes'
            ,'informesfinaleshoyita'
            ,'informesfinaleshoyauditoria'
            ,'contadorinformesfinales'
            ,'requisitoshoyita'
            ,'requisitoshoyauditoria'
            ,'contadorrequisitos'
        ));
    }
    public function eliminarDocumentos(Request $request)
    {
        $motivo = $request->input('motivoanulacion');
        $idsSeleccionados = $request->input('seleccionados');

        // Obtén el nombre del usuario autenticado
        $usuarioAnulacion = auth()->user()->name;

        foreach ($idsSeleccionados as $id) {
            $documento = DocumentacionSubCliente::find($id);
            if ($documento) {
                $documento->deleted_at = now();
                $documento->motivoanulacion = $motivo;
                $documento->usuarioanulacion = $usuarioAnulacion;
                $documento->save();
            }
        }

        return redirect()->back()->with('info', 'Documentos anulados con éxito.');
    }
    public function eliminarinformesfinal(Request $request)
    {
        $motivo = $request->input('motivoanulacion');
        $idsSeleccionados = $request->input('seleccionados2');

        // Obtén el nombre del usuario autenticado
        $usuarioAnulacion = auth()->user()->name;

        foreach ($idsSeleccionados as $id) {
            $documento = Informefinal::find($id);
            if ($documento) {
                $documento->deleted_at = now();
                $documento->motivoanulacion = $motivo;
                $documento->usuarioanulacion = $usuarioAnulacion;
                $documento->estado = null;
                $documento->save();
            }
        }

        return redirect()->back()->with('info', 'Documentos anulados con éxito.');
    }
    public function eliminarbateria(Request $request)
    {
        $motivo = $request->input('motivoanulacion');
        $idsSeleccionados = $request->input('seleccionados3');
        $usuarioAnulacion = auth()->user()->name;

        foreach ($idsSeleccionados as $id) {
            $bateria = Bateriasubcliente::find($id);

            if ($bateria) {
                $programacionIds = ProgramacionSubCliente::where('bateriaid', $id)->pluck('id');

                if ($programacionIds->isNotEmpty()) {
                    EstadoProgramacionSubCliente::whereIn('programacionid', $programacionIds)->update([
                        'deleted_at' => now(),
                        'motivoanulacion' => $motivo,
                        'usuarioanulacion' => $usuarioAnulacion
                    ]);
                    DocumentacionSubCliente::whereIn('programacionid', $programacionIds)->update([
                        'deleted_at' => now(),
                        'motivoanulacion' => $motivo,
                        'usuarioanulacion' => $usuarioAnulacion
                    ]);
                    ProgramacionSubCliente::whereIn('id', $programacionIds)->update([
                        'deleted_at' => now(),
                        'motivoanulacion' => $motivo,
                        'usuarioanulacion' => $usuarioAnulacion
                    ]);
                }

                $bateria->motivoanulacion = $motivo;
                $bateria->usuarioanulacion = $usuarioAnulacion;
                $bateria->save();
                $bateria->delete();
            }
        }


        return redirect()->back()->with('info', 'Registros anulados con éxito.');
    }
    public function anularpendienterequisitos(Request $request) 
    {
        $idRequisito = $request->input('idrequisito');
        $seleccionados = $request->input('seleccionados5', []);
        $accion = $request->input('accion'); // Identificar si es ANULAR o RECHAZAR

        $documento = RequisitosubCliente::find($idRequisito);

        if (!$documento) {
            return redirect()->back()->with('error', 'Registro no encontrado.');
        }

        $valores = ($accion === 'anular') ? null : 'PENDIENTE';

        foreach ($seleccionados as $campo) {
            if (in_array($campo, [
                'poder', 'numeropoder', 'avcci', 'cnacasegurado', 'ciasegurado',
                'cmatrimonio', 'cnacconyuge', 'ciconyuge', 'cnacjihos', 'cihijos',
                'denfaccidente', 'crodomicilio', 'contrato', 'ctrabajo', 'boletapago',
                'egestora', 'actdatos', 'resolinvhijos', 'cunionlibre', 'cnacimientounionlibre',
                'ciunionlibre', 'cdivorcio', 'cdefuncion'
            ])) {
                $documento->$campo = $valores;
            }
        }

        $documento->save();

        return redirect()->back()->with('info', 'Requisitos actualizados correctamente.');
    }
    public function buscarclientesporfecha(Request $request)
    {
        /* $busqueda = $request->get('buscarpor');
        $fechaActual = $busqueda ?: now()->toDateString();

        $clientes = Cliente::whereDate('created_at', $fechaActual)->simplePaginate(10);
        $clientes2 = ClienteAuditoria::whereDate('created_at', $fechaActual)->simplePaginate(10);
        $clientes3 = ClienteComun::whereDate('created_at', $fechaActual)->simplePaginate(10); */
        $fechaActual = $request->input('buscarpor', now()->toDateString());
        $query = $request->input('query');

        //CLIENTES
        $clientes = Cliente::query();
        $clientes2 = ClienteAuditoria::query();
        $clientes3 = ClienteComun::query();

        if (!empty($query)) {
            $clientes->where(function ($q) use ($query) {
                $q->where('id', $query)
                ->orWhere('nombreCompleto', 'like', "%$query%");
            });

            $clientes2->where(function ($q) use ($query) {
                $q->where('id', $query)
                ->orWhere('nombreCompleto', 'like', "%$query%");
            });

            $clientes3->where(function ($q) use ($query) {
                $q->where('id', $query)
                ->orWhere('nombreCompleto', 'like', "%$query%");
            });
        }

        if (!empty($fechaActual)) {
            $clientes->whereDate('created_at', $fechaActual);
            $clientes2->whereDate('created_at', $fechaActual);
            $clientes3->whereDate('created_at', $fechaActual);
        }
        $clientes = $clientes->simplePaginate(10000);
        $clientes2 = $clientes2->simplePaginate(10000);
        $clientes3 = $clientes3->simplePaginate(10000);


        $bateriashoyita = Bateriasubcliente::whereNotNull('clienteitaid');
        $bateriashoycomun = Bateriasubcliente::whereNotNull('clientecomunid');
        $bateriashoyauditoria = Bateriasubcliente::whereNotNull('clienteauditoriaid');

        if (!empty($query)) {
            $bateriashoyita->where(function ($q) use ($query) {
                $q->where('clienteitaid', $query)
                ->orWhere('clienteitanombre', 'like', "%$query%");
            });

            $bateriashoycomun->where(function ($q) use ($query) {
                $q->where('clientecomunid', $query)
                ->orWhere('clientecomunnombre', 'like', "%$query%");
            });

            $bateriashoyauditoria->where(function ($q) use ($query) {
                $q->where('clienteauditoriaid', $query)
                ->orWhere('clienteauditorianombre', 'like', "%$query%");
            });
        }
        if (!empty($fechaActual)) {
            $bateriashoyita->whereDate('created_at', $fechaActual);
            $bateriashoycomun->whereDate('created_at', $fechaActual);
            $bateriashoyauditoria->whereDate('created_at', $fechaActual);
        }

        $bateriashoyita = $bateriashoyita->simplePaginate(10000);
        $bateriashoycomun = $bateriashoycomun->simplePaginate(10000);
        $bateriashoyauditoria = $bateriashoyauditoria->simplePaginate(10000);

        // Baterías
        /* $bateriashoyita = Bateriasubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->simplePaginate(100);

        $bateriashoycomun = Bateriasubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clientecomunid')
            ->simplePaginate(100);

        $bateriashoyauditoria = Bateriasubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->simplePaginate(100); */


        $programacioneshoyita = Programacionsubcliente::whereNotNull('clienteitaid');
        $programacioneshoycomun = Programacionsubcliente::whereNotNull('clientecomunid');
        $programacioneshoyauditoria = Programacionsubcliente::whereNotNull('clienteauditoriaid');

        if (!empty($query)) {
            $programacioneshoyita->where(function ($q) use ($query) {
                $q->where('clienteitaid', $query)
                ->orWhere('clienteitanombre', 'like', "%$query%");
            });

            $programacioneshoycomun->where(function ($q) use ($query) {
                $q->where('clientecomunid', $query)
                ->orWhere('clientecomunnombre', 'like', "%$query%");
            });

            $programacioneshoyauditoria->where(function ($q) use ($query) {
                $q->where('clienteauditoriaid', $query)
                ->orWhere('clienteauditorianombre', 'like', "%$query%");
            });
        }
        if (!empty($fechaActual)) {
            $programacioneshoyita->whereDate('created_at', $fechaActual);
            $programacioneshoycomun->whereDate('created_at', $fechaActual);
            $programacioneshoyauditoria->whereDate('created_at', $fechaActual);
        }

        $programacioneshoyita = $programacioneshoyita->simplePaginate(10000);
        $programacioneshoycomun = $programacioneshoycomun->simplePaginate(10000);
        $programacioneshoyauditoria = $programacioneshoyauditoria->simplePaginate(10000);

        // Programaciones
        /* $programacioneshoyita = Programacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->simplePaginate(100);

        $programacioneshoycomun = Programacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clientecomunid')
            ->simplePaginate(100);

        $programacioneshoyauditoria = Programacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->simplePaginate(100); */

        $informeshoyita = Documentacionsubcliente::whereNotNull('clienteitaid');
        $informeshoycomun = Documentacionsubcliente::whereNotNull('clientecomunid');
        $informeshoyauditoria = Documentacionsubcliente::whereNotNull('clienteauditoriaid');

        if (!empty($query)) {
            $informeshoyita->where(function ($q) use ($query) {
                $q->where('clienteitaid', $query)
                ->orWhere('clienteitanombre', 'like', "%$query%");
            });

            $informeshoycomun->where(function ($q) use ($query) {
                $q->where('clientecomunid', $query)
                ->orWhere('clientecomunnombre', 'like', "%$query%");
            });

            $informeshoyauditoria->where(function ($q) use ($query) {
                $q->where('clienteauditoriaid', $query)
                ->orWhere('clienteauditorianombre', 'like', "%$query%");
            });
        }
        if (!empty($fechaActual)) {
            $informeshoyita->whereDate('created_at', $fechaActual);
            $informeshoycomun->whereDate('created_at', $fechaActual);
            $informeshoyauditoria->whereDate('created_at', $fechaActual);
        }

        $informeshoyita = $informeshoyita->simplePaginate(10000);
        $informeshoycomun = $informeshoycomun->simplePaginate(10000);
        $informeshoyauditoria = $informeshoyauditoria->simplePaginate(10000);

        //INFORMES MEDICOS
        /* $informeshoyita = Documentacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->simplePaginate(10000);

        $informeshoycomun = Documentacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clientecomunid')
            ->simplePaginate(10000);

        $informeshoyauditoria = Documentacionsubcliente::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->simplePaginate(10000); */

        $informesfinaleshoyita = Informefinal::whereNotNull('clienteitaid');
        $informesfinaleshoyauditoria = Informefinal::whereNotNull('clienteauditoriaid');

        if (!empty($query)) {
            $informesfinaleshoyita->where(function ($q) use ($query) {
                $q->where('clienteitaid', $query)
                ->orWhere('clienteitanombre', 'like', "%$query%");
            });

            $informesfinaleshoyauditoria->where(function ($q) use ($query) {
                $q->where('clientecomunid', $query)
                ->orWhere('clientecomunnombre', 'like', "%$query%");
            });

        }
        if (!empty($fechaActual)) {
            $informesfinaleshoyita->whereDate('created_at', $fechaActual);
            $informesfinaleshoyauditoria->whereDate('created_at', $fechaActual);
        }

        $informesfinaleshoyita = $informesfinaleshoyita->simplePaginate(10000);
        $informesfinaleshoyauditoria = $informesfinaleshoyauditoria->simplePaginate(10000);

        //INFORMES FINALES
        /* $informesfinaleshoyita = Informefinal::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteitaid')
            ->simplePaginate(10000);

        $informesfinaleshoyauditoria = Informefinal::whereDate('created_at', $fechaActual)
            ->whereNotNull('clienteauditoriaid')
            ->simplePaginate(10000); */


        $requisitoshoyita = Requisitosubcliente::whereNotNull('clienteitaid');
        $requisitoshoyauditoria = Requisitosubcliente::whereNotNull('clienteauditoriaid');

        if (!empty($query)) {
            $requisitoshoyita->where(function ($q) use ($query) {
                $q->where('clienteitaid', $query)
                ->orWhere('clienteitanombre', 'like', "%$query%");
            });

            $requisitoshoyauditoria->where(function ($q) use ($query) {
                $q->where('clientecomunid', $query)
                ->orWhere('clientecomunnombre', 'like', "%$query%");
            });

        }
        if (!empty($fechaActual)) {
            $requisitoshoyita->whereDate('created_at', $fechaActual);
            $requisitoshoyauditoria->whereDate('created_at', $fechaActual);
        }

        $requisitoshoyita = $requisitoshoyita->simplePaginate(10000);
        $requisitoshoyauditoria = $requisitoshoyauditoria->simplePaginate(10000);
        
        
        
        
        $todosClientes = $clientes->merge($clientes2)->merge($clientes3)->toArray();
            $contadorclientes = array_reduce($todosClientes, function ($count, $item) {
                    return $count + 1;
                }, 0);
        
        $todosbaterias = $bateriashoyita->merge($bateriashoycomun)->merge($bateriashoyauditoria)->toArray();
            $contadorbaterias = array_reduce($todosbaterias, function ($count, $item) {
                    return $count + 1;
                }, 0);

        $todosprogramaciones = $programacioneshoyita->merge($programacioneshoycomun)->merge($programacioneshoyauditoria)->toArray();
            $contadorprogramaciones = array_reduce($todosprogramaciones, function ($count, $item) {
                    return $count + 1;
                }, 0);

        $todosinformes = $informeshoyita->merge($informeshoycomun)->merge($informeshoyauditoria)->toArray();
            $contadorinformes = array_reduce($todosinformes, function ($count, $item) {
                    return $count + 1;
                }, 0);

        $todosinformesfinales = $informesfinaleshoyita->merge($informesfinaleshoyauditoria)->toArray();
            $contadorinformesfinales = array_reduce($todosinformesfinales, function ($count, $item) {
                    return $count + 1;
                }, 0);
                
            $todosrequisitos = $requisitoshoyita->merge($requisitoshoyauditoria)->toArray();
            $contadorrequisitos = array_reduce($todosrequisitos, function ($count, $item) {
                    return $count + 1;
                }, 0);

        return view('admin.admprogramaciones.clientescreadoshoy', compact(
            'clientes',
            'clientes2',
            'clientes3',
            'bateriashoyita',
            'bateriashoycomun',
            'bateriashoyauditoria',
            'programacioneshoyita',
            'programacioneshoycomun',
            'programacioneshoyauditoria',
            'fechaActual', 'contadorclientes'
            ,'contadorbaterias'
            ,'contadorprogramaciones'
            ,'informeshoyita'
            ,'informeshoycomun'
            ,'informeshoyauditoria'
            ,'contadorinformes'
            ,'informesfinaleshoyita'
            ,'informesfinaleshoyauditoria'
            ,'contadorinformesfinales'
            ,'requisitoshoyita'
            ,'requisitoshoyauditoria'
            ,'contadorrequisitos'
        ));
    }
    public function pagosprogramaciones(Request $request)
    {
        $fechaActual = now()->toDateString();

        /* PAGOS PENDIENTES INTERNOS */
        $pagosprogramacionesita = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where(function ($query) {
                $query->whereNull('pagoatencion')
                    ->orWhere('pagoatencion', '');
            })
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'programacionsubclientes.clienteitaid', '=', 'clientes.id')
            ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clientes.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->where('bateriaproveedores.servicio', 'INTERNO')
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                'bateriaproveedores.servicio',
                'clientes.sucursal as cliente_sucursal'
            )
        ->get();

        $pagosprogramacionescomun = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where(function ($query) {
                $query->whereNull('pagoatencion')
                    ->orWhere('pagoatencion', '');
            })
            ->whereNotNull('clientecomunid')
            ->join('clientescomunes', 'programacionsubclientes.clientecomunid', '=', 'clientescomunes.id')
            ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clientescomunes.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->where('bateriaproveedores.servicio', 'INTERNO')
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                'bateriaproveedores.servicio',
                'clientescomunes.sucursal as cliente_sucursal'
            )
        ->get();

        $pagosprogramacionesauditoria = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where(function ($query) {
                $query->whereNull('pagoatencion')
                    ->orWhere('pagoatencion', '');
            })
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'programacionsubclientes.clienteauditoriaid', '=', 'clienteauditorias.id')
            ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clienteauditorias.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->where('bateriaproveedores.servicio', 'INTERNO')
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                'bateriaproveedores.servicio',
                'clienteauditorias.sucursal as cliente_sucursal'
            )
        ->get();


        /* PAGOS PENDIENTES EXTERNOS */
        $pagosexternosprogramacionesita = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where(function ($query) {
                $query->whereNull('pagoatencion')
                    ->orWhere('pagoatencion', '');
            })
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'programacionsubclientes.clienteitaid', '=', 'clientes.id')
            ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clientes.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->where('bateriaproveedores.servicio', 'EXTERNO')
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                'bateriaproveedores.servicio',
                'clientes.sucursal as cliente_sucursal'
            )
        ->get();

        $pagosexternosprogramacionescomun = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where(function ($query) {
                $query->whereNull('pagoatencion')
                    ->orWhere('pagoatencion', '');
            })
            ->whereNotNull('clientecomunid')
            ->join('clientescomunes', 'programacionsubclientes.clientecomunid', '=', 'clientescomunes.id')
            ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clientescomunes.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->where('bateriaproveedores.servicio', 'EXTERNO')
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                'bateriaproveedores.servicio',
                'clientescomunes.sucursal as cliente_sucursal'
            )
        ->get();

        $pagosexternosprogramacionesauditoria = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where(function ($query) {
                $query->whereNull('pagoatencion')
                    ->orWhere('pagoatencion', '');
            })
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'programacionsubclientes.clienteauditoriaid', '=', 'clienteauditorias.id')
            ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clienteauditorias.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->where('bateriaproveedores.servicio', 'EXTERNO')
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                'bateriaproveedores.servicio',
                'clienteauditorias.sucursal as cliente_sucursal'
            )
        ->get();
        

        /* PAGOS PROCESADOS */
        $pagadosprogramacionesita = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where('pagoatencion', 'PAGO PROCESADO')
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'programacionsubclientes.clienteitaid', '=', 'clientes.id')
            ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clientes.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->select(
                'programacionsubclientes.*', 
                'bateriaproveedores.servicio',
                'clientes.sucursal as cliente_sucursal'
            )
        ->get();
        
        $pagadosprogramacionescomun = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where('pagoatencion', 'PAGO PROCESADO')
            ->whereNotNull('clientecomunid')
            ->join('clientescomunes', 'programacionsubclientes.clientecomunid', '=', 'clientescomunes.id')
            ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clientescomunes.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->select(
                'programacionsubclientes.id as programacionsubcliente_id',
                'programacionsubclientes.*', 
                'bateriaproveedores.servicio',
                'clientescomunes.sucursal as cliente_sucursal'
            )
        ->get();

        $pagadosprogramacionesauditoria = Programacionsubcliente::whereDate('fechaasignada', $fechaActual)
            ->where('pagoatencion', 'PAGO PROCESADO')
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'programacionsubclientes.clienteauditoriaid', '=', 'clienteauditorias.id')
            ->join('bateriaproveedores', function ($join) {
                $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                    ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                    ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                    ->on('clienteauditorias.sucursal', '=', 'bateriaproveedores.sucursal');
            })
            ->select(
                'programacionsubclientes.*', 
                'bateriaproveedores.servicio',
                'clienteauditorias.sucursal as cliente_sucursal'
            )
        ->get();

        /* PAGOS PENDIENTES INFORMES FINALES */
        $pagosinformefinalita = ProveedorInformefinal::where(function ($query) {
                $query->whereNull('pagoinforme')
                    ->orWhere('pagoinforme', '');
            })
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'proveedorinformesfinales.clienteitaid', '=', 'clientes.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clientes.sucursal as cliente_sucursal'
            )
        ->get();

        $pagosinformefinalauditoria = ProveedorInformefinal::where(function ($query) {
                $query->whereNull('pagoinforme')
                    ->orWhere('pagoinforme', '');
            })
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'proveedorinformesfinales.clienteauditoriaid', '=', 'clienteauditorias.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clienteauditorias.sucursal as cliente_sucursal'
            )
        ->get();

        /* PAGOS PROCESADOS INFORMES FINALES */
        $pagosprocesadosinformefinalita = ProveedorInformefinal::where('pagoinforme', 'PAGO PROCESADO')
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'proveedorinformesfinales.clienteitaid', '=', 'clientes.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clientes.sucursal as cliente_sucursal'
            )
        ->get();

        $pagosprocesadosinformefinalauditoria = ProveedorInformefinal::where('pagoinforme', 'PAGO PROCESADO')
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'proveedorinformesfinales.clienteauditoriaid', '=', 'clienteauditorias.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clienteauditorias.sucursal as cliente_sucursal'
            )
        ->get();

       // Usar el año y mes actuales si no se pasan
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');

        // Obtener los registros del mes y año especificados
        $records = DB::table('programacionsubclientes')
            ->selectRaw("
                fechaasignada, 
                SUM(CASE WHEN pagoatencion = 'PAGO PROCESADO' THEN 1 ELSE 0 END) as procesados,
                SUM(CASE WHEN pagoatencion IS NULL THEN 1 ELSE 0 END) as sin_pago
            ")
            ->whereYear('fechaasignada', $year)
            ->whereMonth('fechaasignada', $month)
            ->whereNull('deleted_at')
            ->groupBy('fechaasignada')
            ->get();

        // Si es una solicitud AJAX, retornar los registros
        if ($request->ajax()) {
            return response()->json($records);
        }

        return view('admin.admprogramaciones.pagosprogramaciones', compact('year', 'month', 'records','pagosprocesadosinformefinalauditoria','pagosprocesadosinformefinalita','pagosinformefinalauditoria','pagosinformefinalita','pagosexternosprogramacionesauditoria','pagosexternosprogramacionescomun','pagosexternosprogramacionesita','pagadosprogramacionesita','pagadosprogramacionescomun','pagadosprogramacionesauditoria','pagosprogramacionesita','pagosprogramacionescomun','pagosprogramacionesauditoria', 'fechaActual'));
    }

    public function confirmarPagos(Request $request)
    {
        $programacionesIds = $request->input('programaciones', []);

        if (!empty($programacionesIds)) {
            Programacionsubcliente::whereIn('id', $programacionesIds)
                ->update(['pagoatencion' => 'PAGO PROCESADO']);
        }

        return redirect()->back()->with('info', 'Pagos confirmados correctamente.');
    }
    public function buscarprogramacionesporfecha(Request $request)
    {
        $fechaActual = $request->get('fecha') ?: now()->toDateString(); 

        $criterio = $request->get('criterio'); // ID, nombre o CI
        $fecha = $request->get('fecha'); // Fecha seleccionada

        // Función común para filtrar programaciones
        $filtrarProgramaciones = function ($query) use ($criterio, $fecha) {
            if ($criterio) {
                $query->where(function ($subQuery) use ($criterio) {
                    $subQuery->where('clienteitaid', 'like', "%$criterio%")
                            ->orWhere('clienteitanombre', 'like', "%$criterio%")
                            ->orWhere('clienteauditoriaid', 'like', "%$criterio%")
                            ->orWhere('clienteauditorianombre', 'like', "%$criterio%")
                            ->orWhere('clientecomunid', 'like', "%$criterio%")
                            ->orWhere('clientecomunnombre', 'like', "%$criterio%");
                });
            }

            if ($fecha) {
                $query->whereDate('fechaasignada', $fecha);
            }

            $query->where(function ($subQuery) {
                $subQuery->whereNull('pagoatencion')
                        ->orWhere('pagoatencion', '');
            });
            
        };

        /* PAGOS PENDIENTES INTERNOS */
        $pagosprogramacionesita = Programacionsubcliente::where($filtrarProgramaciones)
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'programacionsubclientes.clienteitaid', '=', 'clientes.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clientes.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->where('bateriaproveedores.servicio', 'INTERNO')
                ->select(
                    'programacionsubclientes.id as programacionsubcliente_id',
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clientes.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);

        $pagosprogramacionescomun = Programacionsubcliente::where($filtrarProgramaciones)
            ->whereNotNull('clientecomunid')
            ->join('clientescomunes', 'programacionsubclientes.clientecomunid', '=', 'clientescomunes.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clientescomunes.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->where('bateriaproveedores.servicio', 'INTERNO')
                ->select(
                    'programacionsubclientes.id as programacionsubcliente_id',
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clientescomunes.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);

        $pagosprogramacionesauditoria = Programacionsubcliente::where($filtrarProgramaciones)
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'programacionsubclientes.clienteauditoriaid', '=', 'clienteauditorias.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clienteauditorias.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->where('bateriaproveedores.servicio', 'INTERNO')
                ->select(
                    'programacionsubclientes.id as programacionsubcliente_id',
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clienteauditorias.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);


        /* PAGOS PENDIENTES EXTERNOS */
        $filtrarProgramacionesexternos = function ($query) use ($criterio, $fecha) {
            if ($criterio) {
                $query->where(function ($subQuery) use ($criterio) {
                    $subQuery->where('clienteitaid', 'like', "%$criterio%")
                            ->orWhere('clienteitanombre', 'like', "%$criterio%")
                            ->orWhere('clienteauditoriaid', 'like', "%$criterio%")
                            ->orWhere('clienteauditorianombre', 'like', "%$criterio%")
                            ->orWhere('clientecomunid', 'like', "%$criterio%")
                            ->orWhere('clientecomunnombre', 'like', "%$criterio%");
                });
            }

            if ($fecha) {
                $query->whereDate('fechaasignada', $fecha);
            }

            $query->where(function ($subQuery) {
                $subQuery->whereNull('pagoatencion')
                        ->orWhere('pagoatencion', '');
            });
            
        };

        $pagosexternosprogramacionesita = Programacionsubcliente::where($filtrarProgramacionesexternos)
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'programacionsubclientes.clienteitaid', '=', 'clientes.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clientes.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->where('bateriaproveedores.servicio', 'EXTERNO')
                ->select(
                    'programacionsubclientes.id as programacionsubcliente_id',
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clientes.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);

        $pagosexternosprogramacionescomun = Programacionsubcliente::where($filtrarProgramacionesexternos)
            ->whereNotNull('clientecomunid')
            ->join('clientescomunes', 'programacionsubclientes.clientecomunid', '=', 'clientescomunes.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clientescomunes.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->where('bateriaproveedores.servicio', 'EXTERNO')
                ->select(
                    'programacionsubclientes.id as programacionsubcliente_id',
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clientescomunes.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);

        $pagosexternosprogramacionesauditoria = Programacionsubcliente::where($filtrarProgramacionesexternos)
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'programacionsubclientes.clienteauditoriaid', '=', 'clienteauditorias.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clienteauditorias.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->where('bateriaproveedores.servicio', 'EXTERNO')
                ->select(
                    'programacionsubclientes.id as programacionsubcliente_id',
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clienteauditorias.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);


        // PAGOS PROCESADOS
        $filtrarPagados = function ($query) use ($criterio, $fecha) {
            if ($criterio) {
                $query->where(function ($subQuery) use ($criterio) {
                    $subQuery->where('clienteitaid', 'like', "%$criterio%")
                            ->orWhere('clienteitanombre', 'like', "%$criterio%")
                            ->orWhere('clienteauditoriaid', 'like', "%$criterio%")
                            ->orWhere('clienteauditorianombre', 'like', "%$criterio%")
                            ->orWhere('clientecomunid', 'like', "%$criterio%")
                            ->orWhere('clientecomunnombre', 'like', "%$criterio%");
                });
            }

            if ($fecha) {
                $query->whereDate('fechaasignada', $fecha);
            }

            $query->where('pagoatencion', 'PAGO PROCESADO');
        };

        $pagadosprogramacionesita = Programacionsubcliente::where($filtrarPagados)
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'programacionsubclientes.clienteitaid', '=', 'clientes.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clientes.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->select(
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clientes.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);

        $pagadosprogramacionescomun = Programacionsubcliente::where($filtrarPagados)
            ->whereNotNull('clientecomunid')
            ->join('clientescomunes', 'programacionsubclientes.clientecomunid', '=', 'clientescomunes.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clientescomunes.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->select(
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clientescomunes.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);

        $pagadosprogramacionesauditoria = Programacionsubcliente::where($filtrarPagados)
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'programacionsubclientes.clienteauditoriaid', '=', 'clienteauditorias.id')
                ->join('bateriaproveedores', function ($join) {
                    $join->on('programacionsubclientes.proveedornombre', '=', 'bateriaproveedores.proveedor')
                        ->on('programacionsubclientes.accionnombre', '=', 'bateriaproveedores.accion')
                        ->on('programacionsubclientes.precio', '=', 'bateriaproveedores.precio')
                        ->on('clienteauditorias.sucursal', '=', 'bateriaproveedores.sucursal');
                })
                ->select(
                    'programacionsubclientes.*', 
                    'bateriaproveedores.servicio',
                    'clienteauditorias.sucursal as cliente_sucursal'
                )
        ->simplePaginate(1000);


        /* PAGOS PENDIENTES INFORMES FINALES */
        $filtrarpagospendientesinformefinalita = function ($query) use ($criterio) {
            if ($criterio) {
                $query->where(function ($subQuery) use ($criterio) {
                    $subQuery->where('clienteitaid', 'like', "%$criterio%")
                            ->orWhere('clienteitanombre', 'like', "%$criterio%")
                            ->orWhere('clienteauditoriaid', 'like', "%$criterio%")
                            ->orWhere('clienteauditorianombre', 'like', "%$criterio%");
                });
            }

            $query->where(function ($subQuery) {
                $subQuery->whereNull('pagoinforme')
                        ->orWhere('pagoinforme', '');
            });
            
        };

        $pagosinformefinalita = ProveedorInformefinal::where($filtrarpagospendientesinformefinalita)
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'proveedorinformesfinales.clienteitaid', '=', 'clientes.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clientes.sucursal as cliente_sucursal'
            )
        ->simplePaginate(1000);

        $pagosinformefinalauditoria = ProveedorInformefinal::where($filtrarpagospendientesinformefinalita)
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'proveedorinformesfinales.clienteauditoriaid', '=', 'clienteauditorias.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clienteauditorias.sucursal as cliente_sucursal'
            )
        ->simplePaginate(1000);

        // PAGOS PROCESADOS INFORMES FINALES
        $filtrarPagadosinformesfinales = function ($query) use ($criterio) {
            if ($criterio) {
                $query->where(function ($subQuery) use ($criterio) {
                    $subQuery->where('clienteitaid', 'like', "%$criterio%")
                            ->orWhere('clienteitanombre', 'like', "%$criterio%")
                            ->orWhere('clienteauditoriaid', 'like', "%$criterio%")
                            ->orWhere('clienteauditorianombre', 'like', "%$criterio%");
                });
            }

            $query->where('pagoinforme', 'PAGO PROCESADO');
        };
        $pagosprocesadosinformefinalita = ProveedorInformefinal::where($filtrarPagadosinformesfinales)
            ->whereNotNull('clienteitaid')
            ->join('clientes', 'proveedorinformesfinales.clienteitaid', '=', 'clientes.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clientes.sucursal as cliente_sucursal'
            )
        ->simplePaginate(1000);

        $pagosprocesadosinformefinalauditoria = ProveedorInformefinal::where($filtrarPagadosinformesfinales)
            ->whereNotNull('clienteauditoriaid')
            ->join('clienteauditorias', 'proveedorinformesfinales.clienteauditoriaid', '=', 'clienteauditorias.id')
            ->select(
                'proveedorinformesfinales.id as programacionsubcliente_id',
                'proveedorinformesfinales.*', 
                'clienteauditorias.sucursal as cliente_sucursal'
            )
        ->simplePaginate(1000);

        // Usar el año y mes actuales si no se pasan
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');

        // Obtener los registros del mes y año especificados
        $records = DB::table('programacionsubclientes')
            ->selectRaw("
                fechaasignada, 
                SUM(CASE WHEN pagoatencion = 'PAGO PROCESADO' THEN 1 ELSE 0 END) as procesados,
                SUM(CASE WHEN pagoatencion IS NULL THEN 1 ELSE 0 END) as sin_pago
            ")
            ->whereYear('fechaasignada', $year)
            ->whereMonth('fechaasignada', $month)
            ->whereNull('deleted_at')
            ->groupBy('fechaasignada')
            ->get();

        // Si es una solicitud AJAX, retornar los registros
        if ($request->ajax()) {
            return response()->json($records);
        }

        return view('admin.admprogramaciones.pagosprogramaciones', compact('year', 'month', 'records','pagosexternosprogramacionesauditoria','pagosexternosprogramacionescomun','pagosexternosprogramacionesita',
            'pagadosprogramacionesita',
            'pagadosprogramacionescomun',
            'pagadosprogramacionesauditoria',
            'pagosprogramacionesita',
            'pagosprogramacionescomun',
            'pagosprogramacionesauditoria','fechaActual','pagosinformefinalita','pagosinformefinalauditoria','pagosprocesadosinformefinalita','pagosprocesadosinformefinalauditoria'
        ));
    }
    public function confirmarPagosInformesfinales(Request $request)
    {
        $programacionesIds = $request->input('programaciones', []);

        if (!empty($programacionesIds)) {
            ProveedorInformefinal::whereIn('id', $programacionesIds)
                ->update(['pagoinforme' => 'PAGO PROCESADO']);
        }

        return redirect()->back()->with('info', 'Pagos confirmados correctamente.');
    }
    public function controlregistros(Request $request)
    {
        return view('admin.admprogramaciones.controlregistros');
    }
    public function bateriascreadoshoy(Request $request)
    {
        $fechaActual = now()->toDateString();

        $bateriashoyita = Bateriasubcliente::whereDate('created_at', $fechaActual)
        ->whereNotNull('clienteitaid')
        ->get();

        $bateriashoycomun = Bateriasubcliente::whereDate('created_at', $fechaActual)
        ->whereNotNull('clientecomunid')
        ->get();

        $bateriashoyauditoria = Bateriasubcliente::whereDate('created_at', $fechaActual)
        ->whereNotNull('clienteauditoriaid')
        ->get();

        return view('admin.admprogramaciones.bateriascreadashoy', compact('bateriashoyita', 'bateriashoycomun', 'bateriashoyauditoria', 'fechaActual'));
    }
    public function buscarbateriasporfecha(Request $request)
    {
        $busqueda = $request->get('buscarpor');
        if (!$busqueda) {
            $fechaActual = now()->toDateString();
        } else {
            $fechaActual = $busqueda;
        }
        $bateriashoyita = Bateriasubcliente::where(function ($query) use ($busqueda) {
                    $query->where('created_at', 'like', "%$busqueda%")
                    ->whereNotNull('clienteitaid');
                        })->simplePaginate(1000);
        $bateriashoycomun = Bateriasubcliente::where(function ($query) use ($busqueda) {
                    $query->where('created_at', 'like', "%$busqueda%")
                    ->whereNotNull('clientecomunid');
                        })->simplePaginate(1000);
        $bateriashoyauditoria = Bateriasubcliente::where(function ($query) use ($busqueda) {
                    $query->where('created_at', 'like', "%$busqueda%")
                    ->whereNotNull('clienteauditoriaid');
                        })->simplePaginate(1000);
        return view('admin.admprogramaciones.bateriascreadashoy', compact('bateriashoyita', 'bateriashoycomun', 'bateriashoyauditoria', 'fechaActual'));
    }

    public function contratospendientes(Request $request)
    {
        $requisitos = Requisitosubcliente::where(function ($query) {
            $query->where('contrato', 'PENDIENTE')
                ->orWhere('poder', 'PENDIENTE');
        })
        ->get();

        return view('admin.admprogramaciones.contratospendientes', compact('requisitos'));
    }
    public function tutorialesvideos()
    {
        return view('admin.admprogramaciones.tutorialesvideos');
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
