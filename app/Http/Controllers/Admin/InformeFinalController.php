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
use App\Models\Aprobacioninformefinal;
use App\Models\ProveedorInformefinal;
use App\Models\Informefinal;
use App\Models\Proveedor;
use App\Models\Aseguradora;
use App\Models\Contactosubcliente;
use App\Models\Requisitosubcliente;
use App\Models\Requisitosclientesauditoria;
use App\Models\Afp;
use App\Models\Bateriasubcliente;
use App\Models\Estadoprogramacionsubcliente;
use App\Models\Estadocotizacionsubcliente;
use App\Models\Programacionsubcliente;
use App\Models\Documentacionsubcliente;
use App\Http\Requests\StoreInformefinalRequest;
use App\Http\Requests\StoreProveedorInformefinalRequest;
use App\Http\Requests\UpdateProveedorInformefinalRequest;
use PDF;
use App\Http\Requests\StoreDocumentacionsubclienteRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TablaExport;
use App\Models\Fichamedicasubcliente;
use App\Models\Tramitesubcliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

use function Ramsey\Uuid\v1;

class InformeFinalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /* public function __construct() { 
        $this->middleware ('can:admin.empresas.index')->only('index');
    } */

    public function documentosprogramaciones(Request $request)
    {
        // Obtener los registros de Programacionsubcliente junto con sus relaciones
        $programacionclientes = Programacionsubcliente::with(['estadoprogramacionsubcliente', 'documentacionsubcliente'])
            ->whereNotNull('clienteitaid')
            ->get();
    
        // Procesar los datos para agregar las fechas de atención y creación
        $programacionclientes->each(function ($programacioncliente) {
            $programacioncliente->fechaatencionprogramacion = null;
            $programacioncliente->created_at = null;
    
            // Verificar los estados relacionados
            if ($programacioncliente->estadoprogramacionsubcliente->isNotEmpty()) {
                foreach ($programacioncliente->estadoprogramacionsubcliente as $estado) {
                    if ($estado->clienteitaid == $programacioncliente->clienteitaid
                        && $estado->accionnombre == $programacioncliente->accionnombre
                        && $estado->fechabateria == $programacioncliente->fechabateria
                        && $estado->fechaatencionprogramacion) {
                        $programacioncliente->fechaatencionprogramacion = $estado->fechaatencionprogramacion;
                        break; // Salir del bucle una vez que encontramos la coincidencia
                    }
                }
            }
    
            // Verificar los documentos relacionados
            if ($programacioncliente->documentacionsubcliente->isNotEmpty()) {
                foreach ($programacioncliente->documentacionsubcliente as $documento) {
                    if ($documento->clienteitaid == $programacioncliente->clienteitaid
                        && $documento->accion == $programacioncliente->accionnombre
                        && $documento->fechabateria == $programacioncliente->fechabateria
                        && $documento->document) {
                        $programacioncliente->document = $documento->document;
                        break; // Salir del bucle una vez que encontramos la coincidencia
                    }
                }
            }
        });
    
        return view('admin.informesfinales.documentosprogramaciones', compact('programacionclientes'));
    }

    public function index(Cliente $cliente, Request $request)
    {
        $proveedores = Proveedor::orderBy('proveedor')->get(['id', 'proveedor', 'celular']);

        $aprobaciones = AprobacionInformeFinal::all();

        $fechas = Programacionsubcliente::pluck('fechabateria')->unique()->sort()->toArray();

        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
        
        $query = Programacionsubcliente::with(['tramitesubcliente',  'requisitosubcliente', 'estadoprogramacionsubcliente', 'documentacionsubcliente', 'proveedorinformesfinales', 'informesfinales'])
            ->whereNotNull('clienteitaid');

        if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
            $query->where('fechabateria', $request->buscarporfecha);
        }

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('clienteita', function ($q) use ($request) {
                $q->where('clienteitanombre', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        $programacionclientes = $query->get();
        $grouped = $programacionclientes->groupBy(function($item) {
            return $item->clienteitanombre . '|' . $item->fechabateria;
        });

        $result = [];
        foreach ($grouped as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $fechabateria = explode('|', $key)[1];

            $usuarioAutenticado = auth()->user()->name;
            $esProveedor = $usuarioAutenticado->role ?? null;

            $proveedorAsignado = ProveedorInformefinal::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->first();
            $documentosubido = Informefinal::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->first();
            $ultimoInforme = InformeFinal::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('created_at', 'desc')
                ->first();
            $ultimoobservacionInforme = InformeFinal::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('created_at', 'desc')
                ->first();
            $aprobacioninforme = Aprobacioninformefinal::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('created_at', 'desc')
                ->first();
            $ultimoobservacionInforme2 = InformeFinal::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('deleted_at', 'desc')
                ->first();
            $historiamedica = Documentacionsubcliente::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('accion', 'HISTORIA MÉDICA')
                ->first();
            $tramitebateria = Tramitesubcliente::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->first();

            $ultimoEstado = $ultimoInforme ? $ultimoInforme->estado : null;
            $ultimaObservacion = $ultimoobservacionInforme ? $ultimoobservacionInforme->observaciones : null;
            $aprobacioninformefinales = $aprobacioninforme ? $aprobacioninforme->estado : null;
            $ultimaObservacion2 = $ultimoobservacionInforme2 ? $ultimoobservacionInforme2->observaciones : null;
            $historiamedicaclienteita = $historiamedica ? $historiamedica->document : null;
            $tramite = $tramitebateria ? $tramitebateria->id : null;

            $clienteitaid = $items->first()->clienteitaid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {
                $documentacion = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                $accionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';

                $createdatbateria = $item->bateriasubcliente->where('accionnombre', $item->accionnombre)->first();

                $observacion = $documentacion ? $documentacion->observacion : null;

                if ($createdatbateria) {
                    $createdfechabateria = $createdatbateria->created_at;
                    $formattedDate = $createdfechabateria->format('Y-m-d H:i:s');
                } else {
                    $formattedDate = 'Fecha no disponible';
                }


                $poder = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->poder);})->first()->poder ?? null;

                $numeropoder = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->numeropoder);})->first()->numeropoder ?? null;

                $avcci = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->avcci);})->first()->avcci ?? null;

                $cnacasegurado = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->cnacasegurado);})->first()->cnacasegurado ?? null;

                $ciasegurado = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->ciasegurado);})->first()->ciasegurado ?? null;

                $cimatrimonio = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->cimatrimonio);})->first()->cimatrimonio ?? null;

                $cnacconyuge = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->cnacconyuge);})->first()->cnacconyuge ?? null;

                $ciconyuge = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->ciconyuge);})->first()->ciconyuge ?? null;

                $cnachijos = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->cnachijos);})->first()->cnachijos ?? null;

                $cihijos = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->cihijos);})->first()->cihijos ?? null;

                $denfaccidente = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->denfaccidente);})->first()->denfaccidente ?? null;

                $crodomicilio = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->crodomicilio);})->first()->crodomicilio ?? null;

                $contrato = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->contrato);})->first()->contrato ?? null;

                if ($accionEstado === 'PENDIENTE') {
                    $estado = 'INCOMPLETO';
                }
                $accionesConEstado[] = [
                    'accion' => $item->accionnombre,
                    'estado' => $accionEstado,
                    'document' => $documentacion,
                    'proveedornombre' => $item->proveedornombre,
                    'created_at' => $createdfechabateria,
                    'poder' => $poder,
                    'numeropoder' => $numeropoder,
                    'avcci' => $avcci,
                    'cnacasegurado' => $cnacasegurado,
                    'ciasegurado' => $ciasegurado,
                    'cimatrimonio' => $cimatrimonio,
                    'cnacconyuge' => $cnacconyuge,
                    'ciconyuge' => $ciconyuge,
                    'cnachijos' => $cnachijos,
                    'cihijos' => $cihijos,
                    'denfaccidente' => $denfaccidente,
                    'crodomicilio' => $crodomicilio,
                    'contrato' => $contrato,
                    'observacion' => $observacion,
                    'clienteitanombre' => $clienteNombre,
                    'clienteitaid' => $clienteitaid,
                    'fechabateria' => $fechabateria,
                ];
            }
            $result[] = [
                'clienteitanombre' => $clienteNombre,
                'fechabateria' => $fechabateria,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'clienteitaid' => $clienteitaid,
                'proveedornombre' => $proveedorAsignado ? $proveedorAsignado->proveedorasignado : null,
                'celularproveedor' => $proveedorAsignado ? $proveedorAsignado->celularproveedor : null,
                'document' => $documentosubido ? $documentosubido->document : null,
                'idinformefinal' => $documentosubido ? $documentosubido->id : null,
                'ultima_observacion' => $ultimaObservacion,
                'ultima_observacion2' => $ultimaObservacion2,
                'estado_informefinal' => $ultimoEstado,
                'estadoinforme' => $aprobacioninformefinales,
                'proveedorrol' => $esProveedor,
                'historiamedica' => $historiamedicaclienteita,
                'observacion' => $observacion,
                'tramite' => $tramite,
                
            ];
        }

        $asignarCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if (!$item['proveedornombre'] && $item['estado'] === 'COMPLETO') {
                $count++;
            }
            return $count;
        }, 0);
        $aprobarbateriaCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['proveedornombre'] && $item['estadoinforme'] !== 'APROBADO') {
                $count++;
            }
            return $count;
        }, 0);
        $subirinformeCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['proveedornombre'] && $item['estadoinforme'] === 'APROBADO' && !$item['estado_informefinal']) {
                $count++;
            }
            return $count;
        }, 0);
        $subirinformeCount2 = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['proveedornombre'] && $item['estadoinforme'] === 'APROBADO' && !$item['estado_informefinal'] && $item['proveedornombre'] === $usuarioAutenticado) {
                $count++;
            }
            return $count;
        }, 0);
        $enrevisionCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado_informefinal'] === 'EN REVISIÓN') {
                $count++;
            }
            return $count;
        }, 0);
        $enrevisionCount2 = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado_informefinal'] === 'EN REVISIÓN' && $item['proveedornombre'] === $usuarioAutenticado) {
                $count++;
            }
            return $count;
        }, 0);
        $solicitorevisionCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado_informefinal'] === 'SOLICITÓ REVISIÓN') {
                $count++;
            }
            return $count;
        }, 0);
        $solicitorevisionCount2 = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado_informefinal'] === 'SOLICITÓ REVISIÓN' && $item['proveedornombre'] === $usuarioAutenticado) {
                $count++;
            }
            return $count;
        }, 0);
        $aprobadosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado_informefinal'] === 'APROBADO') {
                $count++;
            }
            return $count;
        }, 0);
        $aprobadosCount2 = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado_informefinal'] === 'APROBADO' && $item['proveedornombre'] === $usuarioAutenticado) {
                $count++;
            }
            return $count;
        }, 0);

        $docobservadosCount = array_reduce($result, function ($count, $accion){
            if (isset($accion['observacion']) && !empty($accion['observacion'])) {
                $count++;
            }
            return $count;
        }, 0);

        if ($request->isMethod('post') && $request->routeIs('updateObservacion')) {
            $this->updateObservacion($request);
        }

        return view('admin.informesfinales.index', compact('docobservadosCount','esProveedor','usuarioAutenticado','asignarCount','aprobarbateriaCount','subirinformeCount','subirinformeCount2','enrevisionCount','enrevisionCount2','solicitorevisionCount','solicitorevisionCount2','aprobadosCount','aprobadosCount2','proveedores','result', 'cliente', 'fechas', 'aprobaciones'));
    }

    public function updateObservacion(Request $request)
    {
        $validated = $request->validate([
            'fechabateria' => 'required|date',
            'accion' => 'required|string',
            'observacion' => 'nullable|string',
        ]);
    
        $documentacion = Documentacionsubcliente::where('fechabateria', $validated['fechabateria'])
            ->where('accion', $validated['accion'])
            ->first();
    
        if ($documentacion) {
            $documentacion->observacion = $validated['observacion'];
            $documentacion->save();
        }
        return redirect()->back()->with('info', 'Observación actualizada con éxito.');
    }

    public function updateDocument(Request $request, $id)
{
    // Encontrar el registro en la base de datos
    $documentacion = Documentacionsubcliente::find($id);

    if (!$documentacion) {
        return redirect()->back()->with('error', 'Documento no encontrado.');
    }

    // Validar el archivo
    $request->validate([
        'archivo' => 'required|file|mimes:pdf,doc,docx,jpg,png', // Ajusta los tipos de archivo según tus necesidades
    ]);

    // Manejar el archivo subido
    $archivo_name = null;
    if ($request->hasFile('archivo')) {
        $file = $request->file('archivo');
        $clienteId = $documentacion->clienteitaid;
        // Crear la carpeta si no existe
        $carpetaCliente = public_path("/documentacionclientesita/{$clienteId}");
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }

        // Generar el nombre del archivo y moverlo a la carpeta deseada
        $archivo_name = time() . '_' . $file->getClientOriginalName();
        $file->move($carpetaCliente, $archivo_name);

        // Actualizar la ruta del archivo en la base de datos
        $documentacion->document = $archivo_name;
    }

    // Eliminar la observación
    $documentacion->observacion = null;
    
    // Guardar los cambios
    $documentacion->save();

    return redirect()->back()->with('info', 'Documento actualizado exitosamente.');
} 

    public function estadodocumentacionprogramacion(Cliente $cliente, Request $request)
    {
        $sucursal = $cliente->sucursal;
        $proveedores = Proveedor::orderBy('proveedor')->get(['id', 'proveedor', 'celular']);
        $aprobaciones = AprobacionInformeFinal::all();
        $fechas = Programacionsubcliente::pluck('fechabateria')->unique()->sort()->toArray();
        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
        $userRole = auth()->user()->getRoleNames()->first(); 
        
        $query = Programacionsubcliente::with(['requisitosclienteauditoria', 'requisitosubcliente', 'bateriasubcliente', 'estadoprogramacionsubcliente', 'documentacionsubcliente', 'proveedorinformesfinales', 'informesfinales'])
            ->whereNotNull('clienteitaid');

        if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
            $query->where('fechabateria', $request->buscarporfecha);
        }

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('clienteita', function ($q) use ($request) {
                $q->where('clienteitanombre', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        $programacionclientes = $query->get();
        $grouped = $programacionclientes->groupBy(function($item) {
            return $item->clienteitanombre . '|' . $item->fechabateria;
        });

        $result = [];
        foreach ($grouped as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $fechabateria = explode('|', $key)[1];

            $clienteitaid = $items->first()->clienteitaid;
            $tramites = TramiteSubCliente::where('clienteitaid', $clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->get();
            $tramiteNombre = $tramites->isEmpty() ? ['SIN SERVICIO'] : $tramites->pluck('tramite')->toArray();

            $usuarioAutenticado = auth()->user()->name;
            $esProveedor = $usuarioAutenticado->role ?? null;

            $proveedorAsignado = ProveedorInformeFinal::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->first();
            $documentosubido = Informefinal::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->first();
            $historiamedica = Documentacionsubcliente::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('accion', 'HISTORIA MÉDICA')
                ->first();
            $motivoabandonobateria = Bateriasubcliente::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->first();

            $usuarioRegistro = Cliente::where('id', $items->first()->clienteitaid)
                ->first();

            $historiamedicaclienteita = $historiamedica ? $historiamedica->document : null;
            $usuarioregistro = $usuarioRegistro ? $usuarioRegistro->sucursal : null;
            $motivoabandono = $motivoabandonobateria ? $motivoabandonobateria->motivoabandono : null;
            $clienteitaid = $items->first()->clienteitaid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            /* $requisitos = Requisitosubcliente::where('clienteitaid', $items->first()->clienteitaid)->first(); */
            $requisitos = Requisitosubcliente::where('servicio', 'INVALIDEZ')->where('clienteitaid', $items->first()->clienteitaid)->get();
            
            $requisitosap = Requisitosubcliente::where('servicio', 'APELACION')->where('clienteitaid', $items->first()->clienteitaid)->get();

            $requisitosss = Requisitosubcliente::where('servicio', 'SEGUNDA SOLICITUD')->where('clienteitaid', $items->first()->clienteitaid)->get();

            $requisitosauditoriamedica = Requisitosclientesauditoria::where('clienteitaid', $items->first()->clienteitaid)->get();

            /* if (is_null($requisitos)) {
                $estadoGeneral = 'NO REGISTRADO';
            } else {
                $estadoGeneral = 'COMPLETO';
                $campos = ['poder','numeropoder','avcci','cnacasegurado','ciasegurado','cmatrimonio','cnacconyuge','ciconyuge','cnacjihos',
                'cihijos','denfaccidente','crodomicilio','contrato','usuarioid','usuarioregistro','ctrabajo','boletapago','egestora',
                'actdatos','resolinvhijos','cunionlibre','cnacimientounionlibre','ciunionlibre','cdivorcio','cdefuncion'];
                foreach ($campos as $campo) {
                    if (!is_null($requisitos->$campo) && stripos($requisitos->$campo, 'PENDIENTE') !== false) {
                        $estadoGeneral = 'PENDIENTE';
                        break;
                    }
                }
            } */

            if ($requisitos->isEmpty()) { 
                $estadoGeneral = 'NO REGISTRADO';
            } else {
                $estadoGeneral = 'COMPLETO';
                $campos = ['poder','numeropoder','avcci','cnacasegurado','ciasegurado','cmatrimonio','cnacconyuge','ciconyuge','cnacjihos',
                'cihijos','denfaccidente','crodomicilio','contrato','usuarioid','usuarioregistro','ctrabajo','boletapago','egestora',
                'actdatos','resolinvhijos','cunionlibre','cnacimientounionlibre','ciunionlibre','cdivorcio','cdefuncion'];
            
                foreach ($requisitos as $requisito) {
                    foreach ($campos as $campo) {
                        if (!is_null($requisito->$campo) && stripos($requisito->$campo, 'PENDIENTE') !== false) {
                            $estadoGeneral = 'PENDIENTE';
                            break 2;
                        }
                    }
                }
            }

            if ($requisitosap->isEmpty()) { 
                $estadoGeneralap = 'NO REGISTRADO';
            } else {
                $estadoGeneralap = 'COMPLETO';
                $camposap = ['poder','numeropoder','avcci','cnacasegurado','ciasegurado','cmatrimonio','cnacconyuge','ciconyuge','cnacjihos',
                'cihijos','denfaccidente','crodomicilio','contrato','usuarioid','usuarioregistro','ctrabajo','boletapago','egestora',
                'actdatos','resolinvhijos','cunionlibre','cnacimientounionlibre','ciunionlibre','cdivorcio','cdefuncion'];
            
                foreach ($requisitosap as $requisitoa) {
                    foreach ($camposap as $campoa) {
                        if (!is_null($requisitoa->$campoa) && stripos($requisitoa->$campoa, 'PENDIENTE') !== false) {
                            $estadoGeneralap = 'PENDIENTE';
                            break 2;
                        }
                    }
                }
            }

            if ($requisitosss->isEmpty()) { 
                $estadoGeneralss = 'NO REGISTRADO';
            } else {
                $estadoGeneralss = 'COMPLETO';
                $camposss = ['poder','numeropoder','avcci','cnacasegurado','ciasegurado','cmatrimonio','cnacconyuge','ciconyuge','cnacjihos',
                'cihijos','denfaccidente','crodomicilio','contrato','usuarioid','usuarioregistro','ctrabajo','boletapago','egestora',
                'actdatos','resolinvhijos','cunionlibre','cnacimientounionlibre','ciunionlibre','cdivorcio','cdefuncion'];
            
                foreach ($requisitosss as $requisitoss) {
                    foreach ($camposss as $campos) {
                        if (!is_null($requisitoss->$campos) && stripos($requisitoss->$campos, 'PENDIENTE') !== false) {
                            $estadoGeneralss = 'PENDIENTE';
                            break 2;
                        }
                    }
                }
            }
            
            /* if (is_null($requisitosauditoriamedica)) {
                $estadoGeneralauditoria = 'NO REGISTRADO';
            } else {
                $estadoGeneralauditoria = 'COMPLETO';
                $aucampos = ['cnacasegurado','ciasegurado','banco','nropolizageneral','polizageneral','declasalud','nropolizadesgravamen','polizasegurodesgravamen'];
                foreach ($aucampos as $aucampo) {
                    if (!is_null($requisitosauditoriamedica->$aucampo) && stripos($requisitosauditoriamedica->$aucampo, 'PENDIENTE') !== false) {
                        $estadoGeneralauditoria = 'PENDIENTE';
                        break;
                    }
                }
            } */
            if ($requisitosauditoriamedica->isEmpty()) { 
                $estadoGeneralauditoria = 'NO REGISTRADO';
            } else {
                $estadoGeneralauditoria = 'COMPLETO';
                $aucampos = ['cnacasegurado', 'ciasegurado', 'banco', 'nropolizageneral', 'polizageneral', 'declasalud', 'nropolizadesgravamen', 'polizasegurodesgravamen'];
                
                foreach ($requisitosauditoriamedica as $requisito) {
                    foreach ($aucampos as $aucampo) {
                        if (!is_null($requisito->$aucampo) && stripos($requisito->$aucampo, 'PENDIENTE') !== false) {
                            $estadoGeneralauditoria = 'PENDIENTE';
                            break 2; // Romper ambos bucles si se encuentra "PENDIENTE"
                        }
                    }
                }
            }

            foreach ($items as $item) {
                $documentacion = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                $image = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                $image2 = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                $accionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';
                $documentacionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';

                $estadoProgramacion = $item->estadoprogramacionsubcliente
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();
                $motivoabandono = Bateriasubcliente::where('clienteitaid', $item->clienteitaid)
                    ->where('fechabateria', $item->fechabateria)
                    ->value('motivoabandono');

                $fechaAtencion = $estadoProgramacion ? $estadoProgramacion->fechaatencionprogramacion : null;
                $createdatbateria = $item->bateriasubcliente->where('accionnombre', $item->accionnombre)->first();

                if ($createdatbateria) {
                    $createdfechabateria = $createdatbateria->created_at;
                    $formattedDate = $createdfechabateria->format('Y-m-d H:i:s');
                } else {
                    $formattedDate = 'Fecha no disponible';
                }

            //REQUISITOS INVALIDEZ
                $poder = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->poder) && $requisito->servicio === 'INVALIDEZ';})->first()->poder ?? null;
                $numeropoder = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->numeropoder) && $requisito->servicio === 'INVALIDEZ';})->first()->numeropoder ?? null;
                $avcci = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->avcci) && $requisito->servicio === 'INVALIDEZ';})->first()->avcci ?? null;
                $cnacasegurado = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->cnacasegurado) && $requisito->servicio === 'INVALIDEZ';})->first()->cnacasegurado ?? null;
                $ciasegurado = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->ciasegurado) && $requisito->servicio === 'INVALIDEZ';})->first()->ciasegurado ?? null;
                $cmatrimonio = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->cmatrimonio) && $requisito->servicio === 'INVALIDEZ';})->first()->cmatrimonio ?? null;
                $cnacconyuge = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->cnacconyuge) && $requisito->servicio === 'INVALIDEZ';})->first()->cnacconyuge ?? null;
                $ciconyuge = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->ciconyuge) && $requisito->servicio === 'INVALIDEZ';})->first()->ciconyuge ?? null;
                $cnacjihos = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->cnacjihos) && $requisito->servicio === 'INVALIDEZ';})->first()->cnacjihos ?? null;
                $cihijos = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->cihijos) && $requisito->servicio === 'INVALIDEZ';})->first()->cihijos ?? null;
                $denfaccidente = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->denfaccidente) && $requisito->servicio === 'INVALIDEZ';})->first()->denfaccidente ?? null;
                $crodomicilio = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->crodomicilio) && $requisito->servicio === 'INVALIDEZ';})->first()->crodomicilio ?? null;
                $contrato = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->contrato) && $requisito->servicio === 'INVALIDEZ';})->first()->contrato ?? null;
                $egestora = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->egestora) && $requisito->servicio === 'INVALIDEZ';})->first()->egestora ?? null;
                $dictamencalentenc = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->dictamencalentenc) && $requisito->servicio === 'INVALIDEZ';})->first()->dictamencalentenc ?? null;
                $infomedicasalud = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->infomedicasalud) && $requisito->servicio === 'INVALIDEZ';})->first()->infomedicasalud ?? null;
                $ctrabajo = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->ctrabajo) && $requisito->servicio === 'INVALIDEZ';})->first()->ctrabajo ?? null;
                $boletapago = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->boletapago) && $requisito->servicio === 'INVALIDEZ';})->first()->boletapago ?? null;
                $actdatos = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->actdatos) && $requisito->servicio === 'INVALIDEZ';})->first()->actdatos ?? null;
                $resolinvhijos = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->resolinvhijos) && $requisito->servicio === 'INVALIDEZ';})->first()->resolinvhijos ?? null;
                $cunionlibre = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->cunionlibre) && $requisito->servicio === 'INVALIDEZ';})->first()->cunionlibre ?? null;
                $cnacimientounionlibre = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->cnacimientounionlibre) && $requisito->servicio === 'INVALIDEZ';})->first()->cnacimientounionlibre ?? null;
                $ciunionlibre = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->ciunionlibre) && $requisito->servicio === 'INVALIDEZ';})->first()->ciunionlibre ?? null;
                $cdivorcio = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->cdivorcio) && $requisito->servicio === 'INVALIDEZ';})->first()->cdivorcio ?? null;
                $cdefuncion = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->cdefuncion) && $requisito->servicio === 'INVALIDEZ';})->first()->cdefuncion ?? null;
                $polizasgen = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->polizasgen) && $requisito->servicio === 'INVALIDEZ';})->first()->polizasgen ?? null;
                $declasalud = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->declasalud) && $requisito->servicio === 'INVALIDEZ';})->first()->declasalud ?? null;
                $polizaseguro = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->polizaseguro) && $requisito->servicio === 'INVALIDEZ';})->first()->polizaseguro ?? null;
                $anteriordictamen = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->anteriordictamen) && $requisito->servicio === 'INVALIDEZ';})->first()->anteriordictamen ?? null;
                $poderciapoderado = $item->requisitosubcliente->filter(function ($requisito) {
                    return !empty($requisito->poderciapoderado) && $requisito->servicio === 'INVALIDEZ';})->first()->poderciapoderado ?? null;

            //

            //REQUISITOS AUDITRIA MEDICA
                $cnacaseguradoau = $item->requisitosclienteauditoria->filter(function ($requisitoau) {
                    return !empty($requisitoau->cnacasegurado);})->first()->cnacasegurado ?? null;
                $ciaseguradoau = $item->requisitosclienteauditoria->filter(function ($requisitoau) {
                    return !empty($requisitoau->ciasegurado);})->first()->ciasegurado ?? null;
                $polizasgenau = $item->requisitosclienteauditoria->filter(function ($requisitoau) {
                    return !empty($requisitoau->polizasgen);})->first()->polizasgen ?? null;
                $declasaludau = $item->requisitosclienteauditoria->filter(function ($requisitoau) {
                    return !empty($requisitoau->declasalud);})->first()->declasalud ?? null;
                $polizaseguroau = $item->requisitosclienteauditoria->filter(function ($requisitoau) {
                    return !empty($requisitoau->polizaseguro);})->first()->polizaseguro ?? null;
            //

            //REQUISITOS SEGUNDA SOLICITUD
                $poderss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->poder) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->poder ?? null;
                $numeropoderss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->numeropoder) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->numeropoder ?? null;
                $avcciss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->avcci) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->avcci ?? null;
                $cnacaseguradoss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->cnacasegurado) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->cnacasegurado ?? null;
                $ciaseguradoss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->ciasegurado) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->ciasegurado ?? null;
                $cmatrimonioss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->cmatrimonio) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->cmatrimonio ?? null;
                $cnacconyugess = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->cnacconyuge) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->cnacconyuge ?? null;
                $ciconyugess = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->ciconyuge) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->ciconyuge ?? null;
                $cnacjihosss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->cnacjihos) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->cnacjihos ?? null;
                $cihijosss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->cihijos) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->cihijos ?? null;
                $denfaccidentess = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->denfaccidente) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->denfaccidente ?? null;
                $crodomicilioss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->crodomicilio) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->crodomicilio ?? null;
                $contratoss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->contrato) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->contrato ?? null;
                $egestorass = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->egestora) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->egestora ?? null;
                $dictamencalentencss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->dictamencalentenc) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->dictamencalentenc ?? null;
                $infomedicasaludss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->infomedicasalud) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->infomedicasalud ?? null;
                $ctrabajoss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->ctrabajo) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->ctrabajo ?? null;
                $boletapagoss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->boletapago) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->boletapago ?? null;
                $actdatosss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->actdatos) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->actdatos ?? null;
                $resolinvhijosss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->resolinvhijos) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->resolinvhijos ?? null;
                $cunionlibress = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->cunionlibre) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->cunionlibre ?? null;
                $cnacimientounionlibress = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->cnacimientounionlibre) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->cnacimientounionlibre ?? null;
                $ciunionlibress = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->ciunionlibre) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->ciunionlibre ?? null;
                $cdivorcioss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->cdivorcio) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->cdivorcio ?? null;
                $cdefuncionss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->cdefuncion) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->cdefuncion ?? null;
                $polizasgenss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->polizasgen) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->polizasgen ?? null;
                $declasaludss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->declasalud) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->declasalud ?? null;
                $polizaseguross = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->polizaseguro) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->polizaseguro ?? null;
                $anteriordictamenss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->anteriordictamen) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->anteriordictamen ?? null;
                $poderciapoderadoss = $item->requisitosubcliente->filter(function ($requisitoss) {
                    return !empty($requisitoss->poderciapoderado) && $requisitoss->servicio === 'SEGUNDA SOLICITUD';})->first()->poderciapoderado ?? null;

            //

            //REQUISITOS APELACION
                $poderap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->poder) && $requisitoa->servicio === 'APELACION';})->first()->poder ?? null;
                $numeropoderap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->numeropoder) && $requisitoa->servicio === 'APELACION';})->first()->numeropoder ?? null;
                $avcciap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->avcci) && $requisitoa->servicio === 'APELACION';})->first()->avcci ?? null;
                $cnacaseguradoap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->cnacasegurado) && $requisitoa->servicio === 'APELACION';})->first()->cnacasegurado ?? null;
                $ciaseguradoap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->ciasegurado) && $requisitoa->servicio === 'APELACION';})->first()->ciasegurado ?? null;
                $cmatrimonioap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->cmatrimonio) && $requisitoa->servicio === 'APELACION';})->first()->cmatrimonio ?? null;
                $cnacconyugeap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->cnacconyuge) && $requisitoa->servicio === 'APELACION';})->first()->cnacconyuge ?? null;
                $ciconyugeap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->ciconyuge) && $requisitoa->servicio === 'APELACION';})->first()->ciconyuge ?? null;
                $cnacjihosap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->cnacjihos) && $requisitoa->servicio === 'APELACION';})->first()->cnacjihos ?? null;
                $cihijosap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->cihijos) && $requisitoa->servicio === 'APELACION';})->first()->cihijos ?? null;
                $denfaccidenteap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->denfaccidente) && $requisitoa->servicio === 'APELACION';})->first()->denfaccidente ?? null;
                $crodomicilioap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->crodomicilio) && $requisitoa->servicio === 'APELACION';})->first()->crodomicilio ?? null;
                $contratoap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->contrato) && $requisitoa->servicio === 'APELACION';})->first()->contrato ?? null;
                $egestoraap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->egestora) && $requisitoa->servicio === 'APELACION';})->first()->egestora ?? null;
                $dictamencalentencap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->dictamencalentenc) && $requisitoa->servicio === 'APELACION';})->first()->dictamencalentenc ?? null;
                $infomedicasaludap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->infomedicasalud) && $requisitoa->servicio === 'APELACION';})->first()->infomedicasalud ?? null;
                $ctrabajoap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->ctrabajo) && $requisitoa->servicio === 'APELACION';})->first()->ctrabajo ?? null;
                $boletapagoap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->boletapago) && $requisitoa->servicio === 'APELACION';})->first()->boletapago ?? null;
                $actdatosap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->actdatos) && $requisitoa->servicio === 'APELACION';})->first()->actdatos ?? null;
                $resolinvhijosap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->resolinvhijos) && $requisitoa->servicio === 'APELACION';})->first()->resolinvhijos ?? null;
                $cunionlibreap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->cunionlibre) && $requisitoa->servicio === 'APELACION';})->first()->cunionlibre ?? null;
                $cnacimientounionlibreap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->cnacimientounionlibre) && $requisitoa->servicio === 'APELACION';})->first()->cnacimientounionlibre ?? null;
                $ciunionlibreap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->ciunionlibre) && $requisitoa->servicio === 'APELACION';})->first()->ciunionlibre ?? null;
                $cdivorcioap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->cdivorcio) && $requisitoa->servicio === 'APELACION';})->first()->cdivorcio ?? null;
                $cdefuncionap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->cdefuncion) && $requisitoa->servicio === 'APELACION';})->first()->cdefuncion ?? null;
                $polizasgenap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->polizasgen) && $requisitoa->servicio === 'APELACION';})->first()->polizasgen ?? null;
                $declasaludap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->declasalud) && $requisitoa->servicio === 'APELACION';})->first()->declasalud ?? null;
                $polizaseguroap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->polizaseguro) && $requisitoa->servicio === 'APELACION';})->first()->polizaseguro ?? null;
                $anteriordictamenap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->anteriordictamen) && $requisitoa->servicio === 'APELACION';})->first()->anteriordictamen ?? null;
                $poderciapoderadoap = $item->requisitosubcliente->filter(function ($requisitoa) {
                    return !empty($requisitoa->poderciapoderado) && $requisitoa->servicio === 'APELACION';})->first()->poderciapoderado ?? null;
            //

                if ($accionEstado === 'PENDIENTE') {
                    $estado = 'INCOMPLETO';
                }
                $accionesConEstado[] = [
                    'accion' => $item->accionnombre,
                    'estado' => $accionEstado,
                    'document' => $documentacion,
                    'image' => $image,
                    'image2' => $image2,
                    'proveedornombre' => $item->proveedornombre,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechadocumento' => $documentacion ? $documentacion->created_at : null,
                    'creacionbateria' => $createdatbateria,
                    'poder' => $poder,
                    'numeropoder' => $numeropoder,
                    'avcci' => $avcci,
                    'cnacasegurado' => $cnacasegurado,
                    'ciasegurado' => $ciasegurado,
                    'cmatrimonio' => $cmatrimonio,
                    'cnacconyuge' => $cnacconyuge,
                    'ciconyuge' => $ciconyuge,
                    'cnacjihos' => $cnacjihos,
                    'cihijos' => $cihijos,
                    'denfaccidente' => $denfaccidente,
                    'crodomicilio' => $crodomicilio,
                    'contrato' => $contrato,
                    'motivoabandono' => $motivoabandono,
                    'estadoGeneral' => $estadoGeneral,
                    'egestora' => $egestora,
                    'dictamencalentenc' => $dictamencalentenc,
                    'infomedicasalud' => $infomedicasalud,
                    'ctrabajo' => $ctrabajo,
                    'boletapago' => $boletapago,
                    'actdatos' => $actdatos,
                    'resolinvhijos' => $resolinvhijos,
                    'cunionlibre' => $cunionlibre,
                    'cnacimientounionlibre' => $cnacimientounionlibre,
                    'ciunionlibre' => $ciunionlibre,
                    'cdivorcio' => $cdivorcio,
                    'cdefuncion' => $cdefuncion,
                    'polizasgen' => $polizasgen,
                    'declasalud' => $declasalud,
                    'polizaseguro' => $polizaseguro,
                    'anteriordictamen' => $anteriordictamen,
                    'poderciapoderado' => $poderciapoderado,
                    'estadoGeneralauditoria' => $estadoGeneralauditoria,
                    'cnacaseguradoau' => $cnacaseguradoau,
                    'ciaseguradoau' => $ciaseguradoau,
                    'polizasgenau' => $polizasgenau,
                    'declasaludau' => $declasaludau,
                    'polizaseguroau' => $polizaseguroau,
                    
                    'poderss' => $poderss,
                    'numeropoderss' => $numeropoderss,
                    'avcciss' => $avcciss,
                    'cnacaseguradoss' => $cnacaseguradoss,
                    'ciaseguradoss' => $ciaseguradoss,
                    'cmatrimonioss' => $cmatrimonioss,
                    'cnacconyugess' => $cnacconyugess,
                    'ciconyugess' => $ciconyugess,
                    'cnacjihosss' => $cnacjihosss,
                    'cihijosss' => $cihijosss,
                    'denfaccidentess' => $denfaccidentess,
                    'crodomicilioss' => $crodomicilioss,
                    'contratoss' => $contratoss,
                    'egestorass' => $egestorass,
                    'dictamencalentencss' => $dictamencalentencss,
                    'infomedicasaludss' => $infomedicasaludss,
                    'ctrabajoss' => $ctrabajoss,
                    'boletapagoss' => $boletapagoss,
                    'actdatosss' => $actdatosss,
                    'resolinvhijosss' => $resolinvhijosss,
                    'cunionlibress' => $cunionlibress,
                    'cnacimientounionlibress' => $cnacimientounionlibress,
                    'ciunionlibress' => $ciunionlibress,
                    'cdivorcioss' => $cdivorcioss,
                    'cdefuncionss' => $cdefuncionss,
                    'polizasgenss' => $polizasgenss,
                    'declasaludss' => $declasaludss,
                    'polizaseguross' => $polizaseguross,
                    'anteriordictamenss' => $anteriordictamenss,
                    'poderciapoderadoss' => $poderciapoderadoss,

                    'poderap' => $poderap,
                    'numeropoderap' => $numeropoderap,
                    'avcciap' => $avcciap,
                    'cnacaseguradoap' => $cnacaseguradoap,
                    'ciaseguradoap' => $ciaseguradoap,
                    'cmatrimonioap' => $cmatrimonioap,
                    'cnacconyugeap' => $cnacconyugeap,
                    'ciconyugeap' => $ciconyugeap,
                    'cnacjihosap' => $cnacjihosap,
                    'cihijosap' => $cihijosap,
                    'denfaccidenteap' => $denfaccidenteap,
                    'crodomicilioap' => $crodomicilioap,
                    'contratoap' => $contratoap,
                    'egestoraap' => $egestoraap,
                    'dictamencalentencap' => $dictamencalentencap,
                    'infomedicasaludap' => $infomedicasaludap,
                    'ctrabajoap' => $ctrabajoap,
                    'boletapagoap' => $boletapagoap,
                    'actdatosap' => $actdatosap,
                    'resolinvhijosap' => $resolinvhijosap,
                    'cunionlibreap' => $cunionlibreap,
                    'cnacimientounionlibreap' => $cnacimientounionlibreap,
                    'ciunionlibreap' => $ciunionlibreap,
                    'cdivorcioap' => $cdivorcioap,
                    'cdefuncionap' => $cdefuncionap,
                    'polizasgenap' => $polizasgenap,
                    'declasaludap' => $declasaludap,
                    'polizaseguroap' => $polizaseguroap,
                    'anteriordictamenap' => $anteriordictamenap,
                    'poderciapoderadoap' => $poderciapoderadoap,
                    'estadoGeneralap' => $estadoGeneralap,
                    'estadoGeneralss' => $estadoGeneralss,
                    
                ];
            }
            $result[] = [
                'clienteitanombre' => $clienteNombre,
                'fechabateria' => $fechabateria,
                'tramite' => $tramiteNombre,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'clienteitaid' => $clienteitaid,
                'proveedornombre' => $proveedorAsignado ? $proveedorAsignado->proveedorasignado : null,
                'celularproveedor' => $proveedorAsignado ? $proveedorAsignado->celularproveedor : null,
                'document' => $documentosubido ? $documentosubido->document : null,
                'idinformefinal' => $documentosubido ? $documentosubido->id : null,
                'proveedorrol' => $esProveedor,
                'historiamedica' => $historiamedicaclienteita,
                'motivoabandono' => $motivoabandono,
                'estadoGeneral' => $estadoGeneral,
                'usuarioregistro' => $usuarioregistro,
                'estadoGeneralauditoria' => $estadoGeneralauditoria,
                'estadoGeneralap' => $estadoGeneralap,
                'estadoGeneralss' => $estadoGeneralss,
            ];
        }
 
        $completosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado'] === 'COMPLETO' && $item['estadoGeneral'] === 'COMPLETO') {
                $count++;
            }
            return $count;
        }, 0);

        $resultadosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado'] === 'COMPLETO' && $item['estadoGeneral'] === 'PENDIENTE') {
                $count++;
            }
            return $count;
        }, 0);

        $documentosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estadoGeneral'] === 'COMPLETO' && $item['estado'] === 'INCOMPLETO') {
                $count++;
            }
            return $count;
        }, 0);

        $incompletosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado'] === 'INCOMPLETO' && $item['estadoGeneral'] === 'PENDIENTE') {
                $count++;
            }
            return $count;
        }, 0);

        $abandonaronCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['motivoabandono']) {
                $count++;
            }
            return $count;
        }, 0);
        
        $requisitosClientepolizas = Requisitosclientesauditoria::where('clienteitaid', $clienteitaid)->wherenotNull('banco')->get();

        return view('admin.informesfinales.estadodocumentacionprogramacion', compact('estadoGeneralap','estadoGeneralss','requisitosClientepolizas','estadoGeneralauditoria', 'documentosCount','resultadosCount','userRole','estadoGeneral','abandonaronCount','esProveedor','usuarioAutenticado','completosCount','incompletosCount','proveedores','result', 'cliente', 'fechas', 'aprobaciones'));
    }

    public function resultadosmedicosclientesauditoria(ClienteAuditoria $clienteauditoria, Request $request)
    {
        $sucursal = $clienteauditoria->sucursal;
        $proveedores = Proveedor::orderBy('proveedor')->get(['id', 'proveedor', 'celular']);
        $aprobaciones = AprobacionInformeFinal::all();
        $fechas = Programacionsubcliente::pluck('fechabateria')->unique()->sort()->toArray();
        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
        $userRole = auth()->user()->getRoleNames()->first(); 
        
        $query = Programacionsubcliente::with([ 'estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria','requisitosclienteauditoriamedica', 'requisitosubcliente', 'bateriasubcliente', 'estadoprogramacionsubcliente', 'documentacionsubcliente', 'proveedorinformesfinales', 'informesfinales'])
            ->whereNotNull('clienteauditoriaid');

        if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
            $query->where('fechabateria', $request->buscarporfecha);
        }

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('clienteauditoria', function ($q) use ($request) {
                $q->where('clienteauditorianombre', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        $programacionclientes = $query->get();
        $grouped = $programacionclientes->groupBy(function($item) {
            return $item->clienteauditorianombre . '|' . $item->fechabateria;
        });

        $result = [];
        foreach ($grouped as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $fechabateria = explode('|', $key)[1];

            $clienteauditoriaid = $items->first()->clienteauditoriaid;
            $tramites = TramiteSubCliente::where('clienteauditoriaid', $clienteauditoriaid)
                ->where('fechabateria', $fechabateria)
                ->get();
            $tramiteNombre = $tramites->isEmpty() ? ['SIN SERVICIO'] : $tramites->pluck('tramite')->toArray();

            $usuarioAutenticado = auth()->user()->name;
            $esProveedor = $usuarioAutenticado->role ?? null;

            $proveedorAsignado = ProveedorInformeFinal::where('clienteauditoriaid', $items->first()->clienteauditoriaid)
                ->where('fechabateria', $fechabateria)
                ->first();
            $documentosubido = Informefinal::where('clienteauditoriaid', $items->first()->clienteauditoriaid)
                ->where('fechabateria', $fechabateria)
                ->first();
            $historiamedica = Documentacionsubcliente::withTrashed()
                ->where('clienteauditoriaid', $items->first()->clienteauditoriaid)
                ->where('accion', 'HISTORIA MÉDICA')
                ->first();
            $motivoabandonobateria = Bateriasubcliente::where('clienteauditoriaid', $items->first()->clienteauditoriaid)
                ->where('fechabateria', $fechabateria)
                ->first();

            $usuarioRegistro = ClienteAuditoria::where('id', $items->first()->clienteauditoriaid)
                ->first();

            $historiamedicaclienteita = $historiamedica ? $historiamedica->document : null;
            $usuarioregistro = $usuarioRegistro ? $usuarioRegistro->sucursal : null;
            $motivoabandono = $motivoabandonobateria ? $motivoabandonobateria->motivoabandono : null;
            $clienteauditoriaid = $items->first()->clienteauditoriaid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            $requisitosauditoriamedica = Requisitosclientesauditoria::where('clienteauditoriaid', $items->first()->clienteauditoriaid)->get();

            if ($requisitosauditoriamedica->isEmpty()) { 
                $estadoGeneralauditoria = 'NO REGISTRADO';
            } else {
                $estadoGeneralauditoria = 'COMPLETO';
                $aucampos = ['cnacasegurado', 'ciasegurado', 'banco', 'nropolizageneral', 'polizageneral', 'declasalud', 'nropolizadesgravamen', 'polizasegurodesgravamen'];
                
                foreach ($requisitosauditoriamedica as $requisito) {
                    foreach ($aucampos as $aucampo) {
                        if (!is_null($requisito->$aucampo) && stripos($requisito->$aucampo, 'PENDIENTE') !== false) {
                            $estadoGeneralauditoria = 'PENDIENTE';
                            break 2;
                        }
                    }
                }
            }

            foreach ($items as $item) {
                $documentacion = $item->documentacionsubclienteauditoria->where('accion', $item->accionnombre)->first();
                $image = $item->documentacionsubclienteauditoria->where('accion', $item->accionnombre)->first();
                $image2 = $item->documentacionsubclienteauditoria->where('accion', $item->accionnombre)->first();
                $accionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';
                $documentacionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';

                $estadoProgramacion = $item->estadoprogramacionsubclienteauditoria
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();
                $motivoabandono = Bateriasubcliente::where('clienteauditoriaid', $item->clienteauditoriaid)
                    ->where('fechabateria', $item->fechabateria)
                    ->value('motivoabandono');

                $fechaAtencion = $estadoProgramacion ? $estadoProgramacion->fechaatencionprogramacion : null;
                $createdatbateria = $item->bateriasubcliente->where('accionnombre', $item->accionnombre)->first();

                if ($createdatbateria) {
                    $createdfechabateria = $createdatbateria->created_at;
                    $formattedDate = $createdfechabateria->format('Y-m-d H:i:s');
                } else {
                    $formattedDate = 'Fecha no disponible';
                }

            //REQUISITOS AUDITRIA MEDICA
                $cnacaseguradoau = $item->requisitosclienteauditoriamedica->filter(function ($requisitoau) {
                    return !empty($requisitoau->cnacasegurado);})->first()->cnacasegurado ?? null;
                $ciaseguradoau = $item->requisitosclienteauditoriamedica->filter(function ($requisitoau) {
                    return !empty($requisitoau->ciasegurado);})->first()->ciasegurado ?? null;
                $polizasgenau = $item->requisitosclienteauditoriamedica->filter(function ($requisitoau) {
                    return !empty($requisitoau->polizasgen);})->first()->polizasgen ?? null;
                $declasaludau = $item->requisitosclienteauditoriamedica->filter(function ($requisitoau) {
                    return !empty($requisitoau->declasalud);})->first()->declasalud ?? null;
                $polizaseguroau = $item->requisitosclienteauditoriamedica->filter(function ($requisitoau) {
                    return !empty($requisitoau->polizaseguro);})->first()->polizaseguro ?? null;
            //

                if ($accionEstado === 'PENDIENTE') {
                    $estado = 'INCOMPLETO';
                }
                $accionesConEstado[] = [
                    'accion' => $item->accionnombre,
                    'estado' => $accionEstado,
                    'document' => $documentacion,
                    'image' => $image,
                    'image2' => $image2,
                    'proveedornombre' => $item->proveedornombre,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechadocumento' => $documentacion ? $documentacion->created_at : null,
                    'creacionbateria' => $createdatbateria,
                    'estadoGeneralauditoria' => $estadoGeneralauditoria,
                    'cnacaseguradoau' => $cnacaseguradoau,
                    'ciaseguradoau' => $ciaseguradoau,
                    'polizasgenau' => $polizasgenau,
                    'declasaludau' => $declasaludau,
                    'polizaseguroau' => $polizaseguroau,
                ];
            }
            $result[] = [
                'clienteauditorianombre' => $clienteNombre,
                'fechabateria' => $fechabateria,
                'tramite' => $tramiteNombre,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'clienteauditoriaid' => $clienteauditoriaid,
                'proveedornombre' => $proveedorAsignado ? $proveedorAsignado->proveedorasignado : null,
                'celularproveedor' => $proveedorAsignado ? $proveedorAsignado->celularproveedor : null,
                'document' => $documentosubido ? $documentosubido->document : null,
                'idinformefinal' => $documentosubido ? $documentosubido->id : null,
                'proveedorrol' => $esProveedor,
                'historiamedica' => $historiamedicaclienteita,
                'motivoabandono' => $motivoabandono,
                'usuarioregistro' => $usuarioregistro,
                'estadoGeneralauditoria' => $estadoGeneralauditoria,
            ];
        }
 
        $completosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado'] === 'COMPLETO' && $item['estadoGeneralauditoria'] === 'COMPLETO') {
                $count++;
            }
            return $count;
        }, 0);

        $resultadosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado'] === 'COMPLETO' && $item['estadoGeneralauditoria'] === 'PENDIENTE') {
                $count++;
            }
            return $count;
        }, 0);

        $documentosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estadoGeneralauditoria'] === 'COMPLETO' && $item['estado'] === 'INCOMPLETO') {
                $count++;
            }
            return $count;
        }, 0);

        $incompletosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado'] === 'INCOMPLETO' && $item['estadoGeneralauditoria'] === 'PENDIENTE') {
                $count++;
            }
            return $count;
        }, 0);

        $abandonaronCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['motivoabandono']) {
                $count++;
            }
            return $count;
        }, 0);
        
        $requisitosClientepolizas = Requisitosclientesauditoria::where('clienteauditoriaid', $clienteauditoriaid)->wherenotNull('banco')->get();

        return view('admin.informesfinales.resultadosmedicosclientesauditoria', compact('requisitosClientepolizas','estadoGeneralauditoria', 'documentosCount','resultadosCount','userRole','abandonaronCount','esProveedor','usuarioAutenticado','completosCount','incompletosCount','proveedores','result', 'clienteauditoria', 'fechas', 'aprobaciones'));
    }

    public function resultadosmedicosclientesbancos(ClienteBanco $clientebanco, Request $request)
    {
        $sucursal = $clientebanco->sucursal;
        $proveedores = Proveedor::orderBy('proveedor')->get(['id', 'proveedor', 'celular']);
        $aprobaciones = AprobacionInformeFinal::all();
        $fechas = Programacionsubcliente::pluck('fechabateria')->unique()->sort()->toArray();
        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
        $userRole = auth()->user()->getRoleNames()->first(); 
        
        $query = Programacionsubcliente::with(['requisitosclienteauditoriamedica', 'requisitosubcliente', 'bateriasubcliente', 'estadoprogramacionsubcliente', 'documentacionsubcliente', 'proveedorinformesfinales', 'informesfinales'])
            ->whereNotNull('clientebancoid');

        if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
            $query->where('fechabateria', $request->buscarporfecha);
        }

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('clientebanco', function ($q) use ($request) {
                $q->where('clientebanconombre', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        $programacionclientes = $query->get();
        $grouped = $programacionclientes->groupBy(function($item) {
            return $item->clientenombre . '|' . $item->fechabateria;
        });

        $result = [];
        foreach ($grouped as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $fechabateria = explode('|', $key)[1];

            $clientebancoid = $items->first()->clientebancoid;

            $usuarioAutenticado = auth()->user()->name;
            $esProveedor = $usuarioAutenticado->role ?? null;

            $proveedorAsignado = ProveedorInformeFinal::where('clientebancoid', $items->first()->clientebancoid)
                ->where('fechabateria', $fechabateria)
                ->first();
            $documentosubido = Informefinal::where('clientebancoid', $items->first()->clientebancoid)
                ->where('fechabateria', $fechabateria)
                ->first();
            $fichamedica = Fichamedicasubcliente::withTrashed()
                ->where('clientebancoid', $items->first()->clientebancoid)
                ->where('detalle', 'FICHA MEDICA')
                ->first();
            $declaracionmedica = Fichamedicasubcliente::withTrashed()
                ->where('clientebancoid', $items->first()->clientebancoid)
                ->where('detalle', 'DECLARACIONES HECHAS AL MEDICO EXAMINADOR')
                ->first();

            $consentimientoinformado = Estadocotizacionsubcliente::withTrashed()
                ->where('clientebancoid', $items->first()->clientebancoid)
                ->where('detalle', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS')
                ->whereNotNull('document')
                ->first();

            $informefinal = Informefinal::withTrashed()
                ->where('clientebancoid', $items->first()->clientebancoid)
                /* ->where('detalle', 'DECLARACIONES HECHAS AL MEDICO EXAMINADOR') */
                ->first();
            $motivoabandonobateria = Bateriasubcliente::where('clienteid', $items->first()->clientebancoid)
                ->where('fechabateria', $fechabateria)
                ->first();

            $usuarioRegistro = ClienteBanco::where('id', $items->first()->clientebancoid)
                ->first();

            $fichamedicaclientebanco = $fichamedica ? $fichamedica->document : null;
            $declaracionmedicaclientebanco = $declaracionmedica ? $declaracionmedica->document : null;
            $consentimiento = $consentimientoinformado ? $consentimientoinformado->document : null;
            $informefinalclientebanco = $informefinal ? $informefinal->document : null;
            $usuarioregistro = $usuarioRegistro ? $usuarioRegistro->sucursal : null;
            $empresaregistro = $usuarioRegistro ? $usuarioRegistro->asociadonombre : null;
            $motivoabandono = $motivoabandonobateria ? $motivoabandonobateria->motivoabandono : null;
            $clientebancoid = $items->first()->clientebancoid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {
                $documentacion = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                $image = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                $image2 = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                $accionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';
                $documentacionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';

                $estadoProgramacion = $item->estadoprogramacionsubcliente
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();
                $motivoabandono = Bateriasubcliente::where('clienteid', $item->clientebancoid)
                    ->where('fechabateria', $item->fechabateria)
                    ->value('motivoabandono');

                $fechaAtencion = $estadoProgramacion ? $estadoProgramacion->fechaatencionprogramacion : null;
                $createdatbateria = $item->bateriasubcliente->where('accionnombre', $item->accionnombre)->first();

                if ($createdatbateria) {
                    $createdfechabateria = $createdatbateria->created_at;
                    $formattedDate = $createdfechabateria->format('Y-m-d H:i:s');
                } else {
                    $formattedDate = 'Fecha no disponible';
                }

                if ($accionEstado === 'PENDIENTE') {
                    $estado = 'INCOMPLETO';
                }
                $accionesConEstado[] = [
                    'accion' => $item->accionnombre,
                    'estado' => $accionEstado,
                    'document' => $documentacion,
                    'image' => $image,
                    'image2' => $image2,
                    'proveedornombre' => $item->proveedornombre,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechadocumento' => $documentacion ? $documentacion->created_at : null,
                    'creacionbateria' => $createdatbateria,
                ];
            }
            $result[] = [
                'clientebanconombre' => $clienteNombre,
                'fechabateria' => $fechabateria,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'clientebancoid' => $clientebancoid,
                'proveedornombre' => $proveedorAsignado ? $proveedorAsignado->proveedorasignado : null,
                'celularproveedor' => $proveedorAsignado ? $proveedorAsignado->celularproveedor : null,
                'document' => $documentosubido ? $documentosubido->document : null,
                'idinformefinal' => $documentosubido ? $documentosubido->id : null,
                'proveedorrol' => $esProveedor,
                'fichamedica' => $fichamedicaclientebanco,
                'motivoabandono' => $motivoabandono,
                'usuarioregistro' => $usuarioregistro,
                'empresaregistro' => $empresaregistro,
                'declaracionmedica' => $declaracionmedicaclientebanco,
                'informefinal' => $informefinalclientebanco,
                'consentimiento' => $consentimiento,
            ];
        }
 
        $completosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado'] === 'COMPLETO') {
                $count++;
            }
            return $count;
        }, 0);

        $resultadosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado'] === 'COMPLETO') {
                $count++;
            }
            return $count;
        }, 0);

        $documentosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado'] === 'COMPLETO') {
                $count++;
            }
            return $count;
        }, 0);

        $incompletosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado'] === 'INCOMPLETO') {
                $count++;
            }
            return $count;
        }, 0);

        return view('admin.informesfinales.resultadosmedicosclientesbancos', compact('clientebancoid','documentosCount','resultadosCount','userRole','esProveedor','usuarioAutenticado','completosCount','incompletosCount','proveedores','result', 'clientebanco', 'fechas', 'aprobaciones'));
    }
public function solrevisioninformefinal(Request $request, Informefinal $informefinal)
{
    // Validar los datos del formulario
    $request->validate([
        'observaciones' => 'required|string|max:255',
    ]);

    // Obtener el informe final por su ID
    $informeFinal = Informefinal::findOrFail($request->idinformefinal);

    // Asignar las observaciones al informe final
    $informeFinal->observaciones = $request->observaciones;
    $informeFinal->estado = 'SOLICITÓ REVISIÓN';
    // Guardar los cambios en el informe final
    $informeFinal->save();

    // Eliminar el informe final
    $informeFinal->delete();

    // No elimines el informe final aquí si planeas usarlo después
    // $informefinal->delete();

    // Redirigir a la ruta adecuada, ajusta según sea necesario
    // Suponiendo que $informefinal->clienteitaid es el ID del cliente
    $cliente = Cliente::find($informefinal->clienteitaid);

    return redirect()->route('admin.informesfinales.index', $informefinal)->with('info', 'Solicitud enviada exitosamente.');
}
public function aprobarinformefinalfs(Request $request, Informefinal $informefinal)
{
    $informeFinal = Informefinal::findOrFail($request->idinformefinal);
    $informeFinal->estado = 'APROBADO';
    $informeFinal->save();

    $cliente = Cliente::find($informefinal->clienteitaid);

    return redirect()->route('admin.informesfinales.index', $informefinal)->with('info', 'Informe aprobado exitosamente.');
}

public function guardaraprobacioninformefinal(Request $request, $id)
{
    $request->validate([
        'cliente' => 'required|string',
        'fechabateria' => 'required|date',
        'estado' => 'required|string',
        'proveedornombre' => 'required|string',
        'cliente' => 'required|string',
    ]);
    $usuarioId = auth()->user()->id;
    $usuarioRegistro = auth()->user()->name;

    AprobacionInformeFinal::create([
        'cliente' => $request->cliente,
        'fechabateria' => $request->fechabateria,
        'estado' => $request->estado,
        'proveedorasignado' => $request->proveedornombre,
        'clienteitaid' => $id,
        'clienteitanombre' => $request->cliente,
        'usuarioid' => $usuarioId,
        'usuarioregistro' => $usuarioRegistro,
    ]);

    return redirect()->route('admin.informesfinales.index')->with('info', 'Aprobación guardada exitosamente.');
}
public function guardarproveedorinformefinal(StoreProveedorInformefinalRequest $request, $id)
{
    $request->validate([
        'cliente' => 'required|string',
        'fechabateria' => 'required|date',
        'celularproveedor' => 'required|string',
    ]);

    $usuarioId = auth()->user()->id;
    $usuarioRegistro = auth()->user()->name;

    // Buscar el nombre del proveedor basado en el ID seleccionado
    $proveedorAsignado = Proveedor::findOrFail($request->proveedorasignado)->proveedor;

    // Crear el registro en ProveedorInformefinal utilizando el nombre del proveedor
    ProveedorInformefinal::create([
        'cliente' => $request->cliente,
        'fechabateria' => $request->fechabateria,
        'proveedorasignado' => $proveedorAsignado, // Guardar el nombre del proveedor en lugar del ID
        'celularproveedor' => $request->celularproveedor,
        'clienteitaid' => $id,
        'clienteitanombre' => $request->cliente,
        'usuarioid' => $usuarioId,
        'usuarioregistro' => $usuarioRegistro,
    ]);

    return redirect()->route('admin.informesfinales.index')->with('info', 'Proveedor asignado exitosamente.');
}

public function guardarinformefinal(StoreInformefinalRequest $request, $id, Cliente $cliente)
{
    $clienteitaid = $cliente->id;
    
    $archivo_name = null;
    if ($request->hasFile('document')) {
        $file = $request->file('document');
        
        // Ruta de la carpeta del cliente
        $carpetaCliente = public_path("/informesfinalesclientesita/{$clienteitaid}");
        
        // Crear la carpeta si no existe
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }
        
        // Nombre único para el archivo
        $archivo_name = time() . '_' . $file->getClientOriginalName();
        
        // Mover el archivo a la carpeta del cliente
        $file->move($carpetaCliente, $archivo_name);
    }
    $usuarioId = auth()->user()->id;
    $usuarioRegistro = auth()->user()->name;

        InformeFinal::create([
            'cliente' => $request->cliente,
            'fechabateria' => $request->fechabateria,
            'estado' => $request->estado,
            'clienteitaid' => $id,
            'clienteitanombre' => $request->cliente,
            'document' => $archivo_name,
            'usuarioid' => $usuarioId,
            'usuarioregistro' => $usuarioRegistro,
        ]);

    return redirect()->route('admin.informesfinales.index')->with('info', 'Documento subido exitosamente.');
} 

    /* public function reservasmedicas(Cliente $cliente, Request $request)
    {

        $query = Programacionsubcliente::with(['requisitosubcliente', 'bateriasubcliente', 'estadoprogramacionsubcliente', 'documentacionsubcliente', 'proveedorinformesfinales', 'informesfinales'])
                ->whereNotNull('clienteitaid');
                
        if ($request->has('buscarporproveedor') && $request->buscarporproveedor !== '') {
                $query->where('proveedornombre', 'LIKE', '%' . $request->buscarporproveedor . '%');
            }

        $rolusuario = auth()->user()->getRoleNames()->first(); 
        $usuarioautenticado = auth()->user()->name;

        
        if ($rolusuario === 'MAESTRO' || $rolusuario === 'ADMINISTRADOR') {
            $reservasmedicas = Programacionsubcliente::orderby('fechaasignada', 'desc')->get();
        } elseif ($rolusuario === 'PROVEEDOR') {
            $reservasmedicas = Programacionsubcliente::where('proveedornombre', $usuarioautenticado)
                ->orderby('fechaasignada', 'desc')
                ->get();
        } else {
            $reservasmedicas = collect();
        }

        $atencionpendienteCount = 0;
        $informependienteCount = 0;
        $informecompletoCount = 0;

        foreach ($reservasmedicas as $reservasmedica) {
            $reservasmedica->informeDisponible = Estadoprogramacionsubcliente::where('clienteitaid', $reservasmedica->clienteitaid)
            ->where('fechabateria', $reservasmedica->fechabateria)
            ->where('accionnombre', $reservasmedica->accionnombre)
            ->exists();

            $reservasmedica->documentacionDisponible = Documentacionsubcliente::where('clienteitaid', $reservasmedica->clienteitaid)
            ->where('fechabateria', $reservasmedica->fechabateria)
            ->where('accion', $reservasmedica->accionnombre)
            ->exists();

            $documentacion = Documentacionsubcliente::where('clienteitaid', $reservasmedica->clienteitaid)
            ->where('fechabateria', $reservasmedica->fechabateria)
            ->where('accion', $reservasmedica->accionnombre)
            ->first();

            $reservasmedica->documentacionDisponible = $documentacion ? $documentacion->document : null;
            $reservasmedica->imagen1Disponible = $documentacion ? $documentacion->image : null;
            $reservasmedica->imagen2Disponible = $documentacion ? $documentacion->image2 : null;
            $reservasmedica->fechainforme = $documentacion ? $documentacion->created_at : null;

            if (!$reservasmedica->documentacionDisponible && !$reservasmedica->informeDisponible) {
                $atencionpendienteCount++;
            }
            if (!$reservasmedica->documentacionDisponible && $reservasmedica->informeDisponible) {
                $informependienteCount++;
            }
            if ($reservasmedica->documentacionDisponible) {
                $informecompletoCount++;
            }
        }

        return view('admin.informesfinales.reservasmedicas', compact('rolusuario', 'reservasmedicas', 'cliente', 'atencionpendienteCount', 'informependienteCount', 'informecompletoCount'));
    } */

    public function reservasmedicas(Cliente $cliente, Request $request)
{

    $proveedores = Programacionsubcliente::select('proveedornombre')
                    ->distinct()
                    ->get();
    $query = Programacionsubcliente::with([
        'requisitosubcliente', 
        'bateriasubcliente', 
        'estadoprogramacionsubcliente', 
        'documentacionsubcliente', 
        'proveedorinformesfinales', 
        'informesfinales'
    ])->whereNotNull('clienteitaid');
    
    /* if ($request->has('buscarporproveedor') && $request->buscarporproveedor !== '') {
        $query->where('proveedornombre', 'LIKE', '%' . $request->buscarporproveedor . '%');
    } */
    if ($request->has('proveedor') && $request->proveedor !== '') {
        $query->where('proveedornombre', $request->proveedor);
    }

    $rolusuario = auth()->user()->getRoleNames()->first(); 
    $usuarioautenticado = auth()->user()->name;

    // Ajustar la consulta en función del rol del usuario
    if ($rolusuario === 'MAESTRO' || $rolusuario === 'ADMINISTRADOR') {
        // No se necesita filtrar por proveedor
        $reservasmedicas = $query->orderby('fechaasignada', 'desc')->get();
    } elseif ($rolusuario === 'PROVEEDOR') {
        // Filtrar por proveedor autenticado
        $reservasmedicas = $query->where('proveedornombre', $usuarioautenticado)
            ->orderby('fechaasignada', 'desc')
            ->get();
    } else {
        $reservasmedicas = collect();
    }

    $atencionpendienteCount = 0;
    $informependienteCount = 0;
    $informecompletoCount = 0;

    foreach ($reservasmedicas as $reservasmedica) {
        $reservasmedica->informeDisponible = Estadoprogramacionsubcliente::where('clienteitaid', $reservasmedica->clienteitaid)
            ->where('fechabateria', $reservasmedica->fechabateria)
            ->where('accionnombre', $reservasmedica->accionnombre)
            ->exists();

        $reservasmedica->documentacionDisponible = Documentacionsubcliente::where('clienteitaid', $reservasmedica->clienteitaid)
            ->where('fechabateria', $reservasmedica->fechabateria)
            ->where('accion', $reservasmedica->accionnombre)
            ->exists();

        $documentacion = Documentacionsubcliente::where('clienteitaid', $reservasmedica->clienteitaid)
            ->where('fechabateria', $reservasmedica->fechabateria)
            ->where('accion', $reservasmedica->accionnombre)
            ->first();

        $reservasmedica->documentacionDisponible = $documentacion ? $documentacion->document : null;
        $reservasmedica->imagen1Disponible = $documentacion ? $documentacion->image : null;
        $reservasmedica->imagen2Disponible = $documentacion ? $documentacion->image2 : null;
        $reservasmedica->fechainforme = $documentacion ? $documentacion->created_at : null;

        if (!$reservasmedica->documentacionDisponible && !$reservasmedica->informeDisponible) {
            $atencionpendienteCount++;
        }
        if (!$reservasmedica->documentacionDisponible && $reservasmedica->informeDisponible) {
            $informependienteCount++;
        }
        if ($reservasmedica->documentacionDisponible) {
            $informecompletoCount++;
        }
    }

    return view('admin.informesfinales.reservasmedicas', compact('proveedores', 'rolusuario', 'reservasmedicas', 'cliente', 'atencionpendienteCount', 'informependienteCount', 'informecompletoCount'));
}


public function guardardocumentacionclienteita(StoreDocumentacionsubclienteRequest $request, Cliente $cliente)
    {
        $archivo_name = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            
            $carpetaCliente = public_path("/documentacionclientesita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }
        
        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $carpetaCliente = public_path("/documentacionclientesita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name);
        }

        $image_name2 = null;
        if ($request->hasFile('picture2')) {
            $file = $request->file('picture2');
            $carpetaCliente = public_path("/documentacionclientesita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);}
            $image_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name2);
        }

        $accionNombre = Programacionsubcliente::where('id', $request->accion)->value('accionnombre');
        $accion = $request->input('accion');
        $nombrecliente = $request->input('nombrecompleto');
        $documentacioncliente = Documentacionsubcliente::create(
            $request->except('accion') + [
                'document' => $archivo_name,
                'accion' => $accion,
                'clienteitanombre' => $nombrecliente,
                'image' => $image_name,
                'image2' => $image_name2
            ]
        );
        return redirect()->route('admin.informesfinales.guardardocumentacionclienteita', $request->cliente)->with('info', 'El documento se subió con éxito');
    }


public function buscarprogramacionesclienteita(Cliente $cliente, Request $request)
    {
        return $this->index($cliente, $request);
    }
public function buscarporproveedor(Cliente $cliente, Request $request)
    {
        return $this->reservasmedicas($cliente, $request);
    }

    public function buscarprogramacionescomclienteita(Cliente $cliente, Request $request)
    {
        return $this->estadodocumentacionprogramacion($cliente, $request);
    }
    public function buscarreservamedicaclienteita(Cliente $cliente, Request $request)
    {
        return $this->reservasmedicas($cliente, $request);
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
