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
use App\Models\SubTramite;
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
use App\Models\Detallerecibo;
use App\Models\Fichamedicasubcliente;
use App\Models\Tramitesubcliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;
use FPDF;
use function Ramsey\Uuid\v1;
use Illuminate\Support\Facades\Storage;
use App\Notifications\InformeProveedorNotification;
use App\Notifications\InformeFinalProveedorNotification;
use App\Notifications\DiagnosticoNotification;
use Illuminate\Support\Str;

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

    /* public function index(Cliente $cliente, Request $request)
    {
        $proveedores = Proveedor::orderBy('proveedor')->get(['id', 'proveedor', 'celular']);

        $aprobaciones = AprobacionInformeFinal::all();

        $fechas = Programacionsubcliente::pluck('fechabateria')->unique()->sort()->toArray();

        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
        $userRole = auth()->user()->getRoleNames()->first(); 
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

            $asignarCount = 0;
            $aprobarbateriaCount = 0;
            $subirinformeCount = 0;
            $subirinformeCount2 = 0;
            $enrevisionCount = 0;
            $enrevisionCount2 = 0;
            $solicitorevisionCount = 0;
            $solicitorevisionCount2 = 0;
            $aprobadosCount = 0;
            $aprobadosCount2 = 0;
            $docobservadosCount = 0;

            $result = [];
            
            $hasSearchParams = $request->has('buscarporfecha') || $request->has('buscarporcliente');
            if (!$hasSearchParams && !$request->has('buscartodo')) {
                return view('admin.informesfinales.index', compact(
                    'proveedores', 
                    'fechas', 
                    'aprobaciones', 
                    'asignarCount', 
                    'aprobarbateriaCount', 
                    'subirinformeCount', 
                    'subirinformeCount2', 
                    'solicitorevisionCount', 
                    'solicitorevisionCount2', 
                    'aprobadosCount', 
                    'aprobadosCount2', 
                    'docobservadosCount', 
                    'esProveedor', 
                    'usuarioAutenticado', 
                    'userRole', 
                    'cliente',
                    'result'
                ));
            }
            $query = Programacionsubcliente::with(['tramitesubcliente',  'requisitosubcliente', 'estadoprogramacionsubcliente', 'documentacionsubcliente', 'proveedorinformesfinales', 'informesfinales'])
            ->whereNotNull('clienteitaid');
            if (!$request->has('buscartodo')) {
                if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
                    $query->where('fechabateria', $request->buscarporfecha);
                }
                if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
                    $query->whereHas('clienteita', function ($q) use ($request) {
                        $q->where('clienteitanombre', 'LIKE', '%' . $request->buscarporcliente . '%');
                    });
                }
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
                $documentos = Informefinal::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->get();  // Esto te devolverá todos los documentos que coincidan.

            
            $ultimoobservacionInforme = InformeFinal::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('created_at', 'desc')
                ->first();
            
            $aprobacion = Aprobacioninformefinal::withTrashed()
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
                ->whereNull('motivoanulacion')
                ->first();
            $tramitebateria = Tramitesubcliente::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->pluck('tramite')
                ->toArray();

            $ultimoInforme = InformeFinal::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->whereNull('motivoanulacion')
                ->orderBy('created_at', 'desc')
                ->first();
                
            $aprobacioninforme = Detallerecibo::withTrashed()
                ->where('clienteid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->where('detalle', 'INFORME FINAL')
                ->orderBy('created_at', 'desc')
                ->first();
            

            $ultimoEstado = $ultimoInforme ? $ultimoInforme->estado : null;
            $ultimaObservacion = $ultimoobservacionInforme ? $ultimoobservacionInforme->observaciones : null;
            $aprobacioninformefinales = $aprobacioninforme ? $aprobacioninforme->estado : null;
            $aprob = $aprobacion ? $aprobacion->estado : null;
            $ultimaObservacion2 = $ultimoobservacionInforme2 ? $ultimoobservacionInforme2->observaciones : null;
            $historiamedicaclienteita = $historiamedica ? $historiamedica->document : null;
            $tramiteString = implode(', ', $tramitebateria);

            $clienteitaid = $items->first()->clienteitaid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {
                $documentacion = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                $documentacionfirmado = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                $documentacionword = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                $accionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';

                $observacion = $documentacion ? $documentacion->observacion : null;

                $createdatbateria = $item->bateriasubcliente->where('accionnombre', $item->accionnombre)->first();

                if ($createdatbateria) {
                    $createdfechabateria = $createdatbateria->created_at;
                    $formattedDate = $createdfechabateria->format('Y-m-d H:i:s');
                } else {
                    $formattedDate = 'Fecha no disponible';
                }

            //REQUISITOS
                $campos = [
                                'poder', 'numeropoder', 'avcci', 'cnacasegurado', 'ciasegurado',
                                'cimatrimonio', 'cnacconyuge', 'ciconyuge', 'cnacjihos', 'cihijos',
                                'denfaccidente', 'crodomicilio', 'contrato', 'cmatrimonio', 'egestora',
                                'dictamencalentenc', 'infomedicasalud', 'ctrabajo', 'boletapago', 'actdatos',
                                'resolinvhijos', 'cunionlibre', 'cnacimientounionlibre', 'ciunionlibre',
                                'cdivorcio', 'cdefuncion', 'polizasgen', 'declasalud', 'polizaseguro',
                                'anteriordictamen', 'poderciapoderado', 'servicio'
                            ];
                            
                            foreach ($campos as $campo) {
                                $$campo = $item->requisitosubcliente->first(function ($requisito) use ($campo) {
                                    return !empty($requisito->$campo);
                                })->$campo ?? null;
                            }
            //
            
                if ($accionEstado === 'PENDIENTE') {
                    $estado = 'INCOMPLETO';
                }
                $accionesConEstado[] = [
                    'accion' => $item->accionnombre,
                    'estado' => $accionEstado,
                    'document' => $documentacion,
                    'documentfirmado' => $documentacionfirmado,
                    'documentword' => $documentacionword,
                    'proveedornombre' => $item->proveedornombre,
                    'created_at' => $formattedDate,
                    'poder' => $poder,
                    'numeropoder' => $numeropoder,
                    'avcci' => $avcci,
                    'cnacasegurado' => $cnacasegurado,
                    'ciasegurado' => $ciasegurado,
                    'cimatrimonio' => $cimatrimonio,
                    'cnacconyuge' => $cnacconyuge,
                    'ciconyuge' => $ciconyuge,
                    'cnacjihos' => $cnacjihos,
                    'cihijos' => $cihijos,
                    'denfaccidente' => $denfaccidente,
                    'crodomicilio' => $crodomicilio,
                    'contrato' => $contrato,
                    'observacion' => $observacion,
                    'clienteitanombre' => $clienteNombre,
                    'clienteitaid' => $clienteitaid,
                    'fechabateria' => $fechabateria,
                    
                    'cmatrimonio' => $cmatrimonio,
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
                    'servicio' => $servicio,
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
                'documentos' => $documentos,
                'ultima_observacion' => $ultimaObservacion,
                'ultima_observacion2' => $ultimaObservacion2,
                'estado_informefinal' => $ultimoEstado,
                'estadoinforme' => $aprobacioninformefinales,
                'proveedorrol' => $esProveedor,
                'historiamedica' => $historiamedicaclienteita,
                'observacion' => $observacion,
                'tramite' => $tramiteString,
                'estadoaprob' => $aprob,
                
            ];
        }

        $asignarCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if (!$item['proveedornombre'] && $item['estado'] === 'COMPLETO') {
                $count++;
            }
            return $count;
        }, 0);
        $aprobarbateriaCount = array_reduce($result, function ($count, $item) {
            if (
                $item['proveedornombre'] &&
                $item['estadoaprob'] !== 'APROBADO' &&
                $item['estado'] === 'COMPLETO' &&
                $item['estadoinforme'] !== 'PAGO PROCESADO'
            ) {
                $count++;
            }
            return $count;
        }, 0);
        $subirinformeCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estadoaprob'] === 'APROBADO' && !$item['estado_informefinal'] && $item['estado'] === 'COMPLETO' || $item['estadoinforme'] === 'PAGO PROCESADO' && $item['proveedornombre'] && !$item['estado_informefinal'] && $item['estado'] === 'COMPLETO') {
                $count++;
            }
            return $count;
        }, 0);
        $subirinformeCount2 = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estadoaprob'] === 'APROBADO' && !$item['estado_informefinal'] && $item['estado'] === 'COMPLETO' && $item['proveedornombre'] === $usuarioAutenticado || $item['estadoinforme'] === 'PAGO PROCESADO' && $item['proveedornombre'] && !$item['estado_informefinal']  && $item['estado'] === 'COMPLETO' && $item['proveedornombre'] === $usuarioAutenticado ) {
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

        

        return view('admin.informesfinales.index', compact('programacionclientes','userRole','docobservadosCount','esProveedor','usuarioAutenticado','asignarCount','aprobarbateriaCount','subirinformeCount','subirinformeCount2','enrevisionCount','enrevisionCount2','solicitorevisionCount','solicitorevisionCount2','aprobadosCount','aprobadosCount2','proveedores','result', 'cliente', 'fechas', 'aprobaciones'));
    } */
//INFORMES FINALES ITA
    public function index(Cliente $cliente, Request $request)
    {
        $proveedores = Proveedor::orderBy('proveedor')->get(['id', 'proveedor', 'celular']);
        $aprobaciones = AprobacionInformeFinal::all();
        $fechas = Programacionsubcliente::pluck('fechabateria')->unique()->sort()->toArray();
        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
        $userRole = auth()->user()->getRoleNames()->first(); 

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

        $query2 = ProveedorInformefinal::with(['tramitesubcliente', 'requisitosubcliente', 'proveedorinformesfinales', 'informesfinales'])
            ->whereNotNull('clienteitaid');
            if (!$request->has('buscartodo')) {
                if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
                    $query2->where('fechabateria', $request->buscarporfecha);
                }
                if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
                    $query2->whereHas('clienteita', function ($q) use ($request) {
                        $q->where('clienteitanombre', 'LIKE', '%' . $request->buscarporcliente . '%');
                    });
                }
            }
            $asignarCount = 0;
            $aprobarbateriaCount = 0;
            $subirinformeCount = 0;
            $subirinformeCount2 = 0;
            $enrevisionCount = 0;
            $enrevisionCount2 = 0;
            $solicitorevisionCount = 0;
            $solicitorevisionCount2 = 0;
            $aprobadosCount = 0;
            $aprobadosCount2 = 0;
            $docobservadosCount = 0;
            $result = [];
            $result2 = [];
        
            $hasSearchParams = $request->has('buscarporfecha') || $request->has('buscarporcliente');
            if (!$hasSearchParams && !$request->has('buscartodo')) {
                return view('admin.informesfinales.index', compact(
                    'proveedores', 
                    'fechas', 
                    'aprobaciones', 
                    'asignarCount', 
                    'aprobarbateriaCount', 
                    'subirinformeCount', 
                    'subirinformeCount2', 
                    'solicitorevisionCount', 
                    'solicitorevisionCount2', 
                    'aprobadosCount', 
                    'aprobadosCount2', 
                    'docobservadosCount', 
                    'esProveedor', 
                    'usuarioAutenticado', 
                    'userRole', 
                    'cliente',
                    'result','result2'
                ));
            }

        $query = Programacionsubcliente::with(['tramitesubcliente',  'requisitosubcliente', 'estadoprogramacionsubcliente', 'documentacionsubcliente', 'proveedorinformesfinales', 'informesfinales'])
            ->whereNotNull('clienteitaid');
            if (!$request->has('buscartodo')) {
                if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
                    $query->where('fechabateria', $request->buscarporfecha);
                }
                if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
                    $query->whereHas('clienteita', function ($q) use ($request) {
                        $q->where('clienteitanombre', 'LIKE', '%' . $request->buscarporcliente . '%');
                    });
                }
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
            $documentos = Informefinal::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->get();
            $ultimoobservacionInforme = InformeFinal::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('created_at', 'desc')
                ->first();
            $aprobacion = Aprobacioninformefinal::withTrashed()
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
                ->whereNull('motivoanulacion')
                ->first();
            $tramitebateria = Tramitesubcliente::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->pluck('tramite')
                ->toArray();
            $ultimoInforme = InformeFinal::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->whereNull('motivoanulacion')
                ->orderBy('created_at', 'desc')
                ->first();
            $aprobacioninforme = Detallerecibo::withTrashed()
                ->where('clienteid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->where('detalle', 'INFORME FINAL')
                ->orderBy('created_at', 'desc')
                ->first();
            
            $ultimoEstado = $ultimoInforme ? $ultimoInforme->estado : null;
            $ultimaObservacion = $ultimoobservacionInforme ? $ultimoobservacionInforme->observaciones : null;
            $aprobacioninformefinales = $aprobacioninforme ? $aprobacioninforme->estado : null;
            $aprob = $aprobacion ? $aprobacion->estado : null;
            $ultimaObservacion2 = $ultimoobservacionInforme2 ? $ultimoobservacionInforme2->observaciones : null;
            $historiamedicaclienteita = $historiamedica ? $historiamedica->document : null;
            $tramiteString = implode(', ', $tramitebateria);

            $clienteitaid = $items->first()->clienteitaid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {
                $documentacion = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                $documentacionfirmado = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                $documentacionword = $item->documentacionsubcliente->where('accion', $item->accionnombre)->first();
                $accionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';
                $observacion = $documentacion ? $documentacion->observacion : null;
                $createdatbateria = $item->bateriasubcliente->where('accionnombre', $item->accionnombre)->first();
                if ($createdatbateria) {
                    $createdfechabateria = $createdatbateria->created_at;
                    $formattedDate = $createdfechabateria->format('Y-m-d H:i:s');
                } else {
                    $formattedDate = 'Fecha no disponible';
                }

                $campos = [
                    'poder', 'numeropoder', 'avcci', 'cnacasegurado', 'ciasegurado',
                    'cimatrimonio', 'cnacconyuge', 'ciconyuge', 'cnacjihos', 'cihijos',
                    'denfaccidente', 'crodomicilio', 'contrato', 'cmatrimonio', 'egestora',
                    'dictamencalentenc', 'infomedicasalud', 'ctrabajo', 'boletapago', 'actdatos',
                    'resolinvhijos', 'cunionlibre', 'cnacimientounionlibre', 'ciunionlibre',
                    'cdivorcio', 'cdefuncion', 'polizasgen', 'declasalud', 'polizaseguro',
                    'anteriordictamen', 'poderciapoderado', 'servicio'
                ];
                
                foreach ($campos as $campo) {
                    $$campo = $item->requisitosubcliente->first(function ($requisito) use ($campo) {
                        return !empty($requisito->$campo);
                    })->$campo ?? null;
                }
            
                if ($accionEstado === 'PENDIENTE') {
                    $estado = 'INCOMPLETO';
                }
                $accionesConEstado[] = [
                    'accion' => $item->accionnombre,
                    'estado' => $accionEstado,
                    'document' => $documentacion,
                    'documentfirmado' => $documentacionfirmado,
                    'documentword' => $documentacionword,
                    'proveedornombre' => $item->proveedornombre,
                    'created_at' => $formattedDate,
                    'poder' => $poder,
                    'numeropoder' => $numeropoder,
                    'avcci' => $avcci,
                    'cnacasegurado' => $cnacasegurado,
                    'ciasegurado' => $ciasegurado,
                    'cimatrimonio' => $cimatrimonio,
                    'cnacconyuge' => $cnacconyuge,
                    'ciconyuge' => $ciconyuge,
                    'cnacjihos' => $cnacjihos,
                    'cihijos' => $cihijos,
                    'denfaccidente' => $denfaccidente,
                    'crodomicilio' => $crodomicilio,
                    'contrato' => $contrato,
                    'observacion' => $observacion,
                    'clienteitanombre' => $clienteNombre,
                    'clienteitaid' => $clienteitaid,
                    'fechabateria' => $fechabateria,
                    'cmatrimonio' => $cmatrimonio,
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
                    'servicio' => $servicio,
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
                'documentos' => $documentos,
                'ultima_observacion' => $ultimaObservacion,
                'ultima_observacion2' => $ultimaObservacion2,
                'estado_informefinal' => $ultimoEstado,
                'estadoinforme' => $aprobacioninformefinales,
                'proveedorrol' => $esProveedor,
                'historiamedica' => $historiamedicaclienteita,
                'observacion' => $observacion,
                'tramite' => $tramiteString,
                'estadoaprob' => $aprob,
                
            ];
        }


        $query2 = ProveedorInformefinal::with(['tramitesubcliente', 'requisitosubcliente', 'proveedorinformesfinales', 'informesfinales', 'requisitosclienteauditoria'])
            ->whereNotNull('clienteitaid');
            if (!$request->has('buscartodo')) {
                if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
                    $query2->where('fechabateria', $request->buscarporfecha);
                }
                if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
                    $query2->whereHas('clienteita', function ($q) use ($request) {
                        $q->where('clienteitanombre', 'LIKE', '%' . $request->buscarporcliente . '%');
                    });
                }
            }

        $proveedorinformefinales = $query2->get();
            $grouped2 = $proveedorinformefinales->groupBy(function($item) {
                return $item->clienteitanombre . '|' . $item->fechabateria;
            });

        $result2 = [];
        foreach ($grouped2 as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $fechabateria = explode('|', $key)[1];

            $usuarioAutenticado = auth()->user()->name;
            $esProveedor = $usuarioAutenticado->role ?? null;

            $proveedorAsignado = ProveedorInformefinal::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->first();
            $documentos = Informefinal::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->get();
            $ultimoobservacionInforme = InformeFinal::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('created_at', 'desc')
                ->first();
            $historiamedica = Documentacionsubcliente::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('accion', 'HISTORIA MÉDICA')
                ->whereNull('motivoanulacion')
                ->first();
            $cicliente = Requisitosclientesauditoria::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->first();
                $cicliente2 = Requisitosubcliente::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->first();
            $aprobacion = Aprobacioninformefinal::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('created_at', 'desc')
                ->first();
            $ultimoobservacionInforme2 = InformeFinal::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('deleted_at', 'desc')
                ->first();
            $tramitebateria = Tramitesubcliente::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->pluck('tramite')
                ->toArray();
            $ultimoInforme = InformeFinal::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->whereNull('motivoanulacion')
                ->orderBy('created_at', 'desc')
                ->first();
            $sinProgramacion = ProgramacionSubCliente::where('clienteitaid',  $items->first()->clienteitaid)->where('fechabateria', $fechabateria)->doesntExist();

            $aprobacioninforme = Detallerecibo::withTrashed()
                ->where('clienteid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->where('detalle', 'INFORME FINAL')
                ->orderBy('created_at', 'desc')
                ->first();
            
            $ultimoEstado = $ultimoInforme ? $ultimoInforme->estado : null;
            $ultimaObservacion = $ultimoobservacionInforme ? $ultimoobservacionInforme->observaciones : null;
            $aprobacioninformefinales = $aprobacioninforme ? $aprobacioninforme->estado : null;
            $aprob = $aprobacion ? $aprobacion->estado : null;
            $ultimaObservacion2 = $ultimoobservacionInforme2 ? $ultimoobservacionInforme2->observaciones : null;
            $historiamedicaclienteita = $historiamedica ? $historiamedica->document : null;
            $ciclienteita = $cicliente ? $cicliente->ciasegurado : null;
            $ciclienteita2 = $cicliente2 ? $cicliente2->ciasegurado : null;
            $tramiteString = implode(', ', $tramitebateria);

            $clienteitaid = $items->first()->clienteitaid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            $requisitosauditoriamedica = Requisitosclientesauditoria::where('clienteitaid', $items->first()->cliente)->get();

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

                $campos = [
                    'poder', 'numeropoder', 'avcci', 'cnacasegurado', 'ciasegurado',
                    'cimatrimonio', 'cnacconyuge', 'ciconyuge', 'cnacjihos', 'cihijos',
                    'denfaccidente', 'crodomicilio', 'contrato', 'cmatrimonio', 'egestora',
                    'dictamencalentenc', 'infomedicasalud', 'ctrabajo', 'boletapago', 'actdatos',
                    'resolinvhijos', 'cunionlibre', 'cnacimientounionlibre', 'ciunionlibre',
                    'cdivorcio', 'cdefuncion', 'polizasgen', 'declasalud', 'polizaseguro',
                    'anteriordictamen', 'poderciapoderado', 'servicio'
                ];
                
                foreach ($campos as $campo) {
                    $$campo = $item->requisitosubcliente->first(function ($requisito) use ($campo) {
                        return !empty($requisito->$campo);
                    })->$campo ?? null;
                }
            
                /* if ($accionEstado === 'PENDIENTE') {
                    $estado = 'INCOMPLETO';
                } */
                $accionesConEstado[] = [
                    'accion' => $item->accionnombre,
                    'estado' => '',
                    'document' => '',
                    'documentfirmado' => '',
                    'documentword' => '',
                    'proveedornombre' => $item->proveedornombre,
                    'created_at' => '',
                    'poder' => $poder,
                    'numeropoder' => $numeropoder,
                    'avcci' => $avcci,
                    'cnacasegurado' => $cnacasegurado,
                    'ciasegurado' => $ciasegurado,
                    'cimatrimonio' => $cimatrimonio,
                    'cnacconyuge' => $cnacconyuge,
                    'ciconyuge' => $ciconyuge,
                    'cnacjihos' => $cnacjihos,
                    'cihijos' => $cihijos,
                    'denfaccidente' => $denfaccidente,
                    'crodomicilio' => $crodomicilio,
                    'contrato' => $contrato,
                    'observacion' => '',
                    'clienteitanombre' => $clienteNombre,
                    'clienteitaid' => $clienteitaid,
                    'fechabateria' => $fechabateria,
                    'cmatrimonio' => $cmatrimonio,
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
                    'servicio' => $servicio,
                ];
            }
            $result2[] = [
                'clienteitanombre' => $clienteNombre,
                'fechabateria' => $fechabateria,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'clienteitaid' => $clienteitaid,
                'proveedornombre' => $proveedorAsignado ? $proveedorAsignado->proveedorasignado : null,
                'celularproveedor' => $proveedorAsignado ? $proveedorAsignado->celularproveedor : null,
                'documentos' => $documentos,
                'ultima_observacion' => $ultimaObservacion,
                'ultima_observacion2' => $ultimaObservacion2,
                'estado_informefinal' => $ultimoEstado,
                'estadoinforme' => $aprobacioninformefinales,
                'proveedorrol' => $esProveedor,
                'historiamedica' => $historiamedicaclienteita,
                'cicliente' => $ciclienteita,
                'cicliente2' => $ciclienteita2,
                'sinProgramacion' => $sinProgramacion,
                'observacion' => '',
                'tramite' => $tramiteString,
                'estadoaprob' => $aprob,
                
            ];
        }


        $asignarCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if (!$item['proveedornombre'] && $item['estado'] === 'COMPLETO') {
                $count++;
            }
            return $count;
        }, 0);
        $aprobarbateriaCount = array_reduce($result, function ($count, $item) {
            if (
                $item['proveedornombre'] &&
                $item['estadoaprob'] !== 'APROBADO' &&
                $item['estado'] === 'COMPLETO' &&
                $item['estadoinforme'] !== 'PAGO PROCESADO'
            ) {
                $count++;
            }
            return $count;
        }, 0);
        $subirinformeCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estadoaprob'] === 'APROBADO' && !$item['estado_informefinal'] && $item['estado'] === 'COMPLETO' || $item['estadoinforme'] === 'PAGO PROCESADO' && $item['proveedornombre'] && !$item['estado_informefinal'] && $item['estado'] === 'COMPLETO') {
                $count++;
            }
            return $count;
        }, 0);
        $subirinformeCount2 = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estadoaprob'] === 'APROBADO' && !$item['estado_informefinal'] && $item['estado'] === 'COMPLETO' && $item['proveedornombre'] === $usuarioAutenticado || $item['estadoinforme'] === 'PAGO PROCESADO' && $item['proveedornombre'] && !$item['estado_informefinal']  && $item['estado'] === 'COMPLETO' && $item['proveedornombre'] === $usuarioAutenticado ) {
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

        return view('admin.informesfinales.index', compact('programacionclientes','userRole','docobservadosCount','esProveedor','usuarioAutenticado','asignarCount','aprobarbateriaCount','subirinformeCount','subirinformeCount2','enrevisionCount','enrevisionCount2','solicitorevisionCount','solicitorevisionCount2','aprobadosCount','aprobadosCount2','proveedores','result', 'cliente', 'fechas', 'aprobaciones','result2'));
    }
    public function guardarinformefinal(StoreInformefinalRequest $request, $id, Cliente $cliente)
    {
        $request->validate([
            'document' => 'required|mimes:pdf|max:999999',
        ]);

        $id = $request->input('clienteitaid');
        $carpetaCliente = public_path("informesfinalesclientesita/{$id}");
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }

        $usuario = auth()->user()->name;
        $sucursal = auth()->user()->sucursal;
        $fechaActual = Carbon::now()->locale('es')->isoFormat('D [de] MMMM [del] YYYY');

        $archivo_name = null;
            if ($request->hasFile('archivo2')) {
                $file = $request->file('archivo2');
                
                $carpetaCliente = public_path("/informesfinalesclientesita/{$id}");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);}
                $archivo_name = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name);
            }

        $firmaAnteriorPath = '';
        $selloAnteriorPath = '';
        $firmaUltimaPath = '';
        $selloUltimaPath = '';
        $firmaAnteriorCoords = [];
        $selloAnteriorCoords = [];
        $firmaUltimaCoords = [];
        $selloUltimaCoords = [];
        $texto1 = $texto2 = $texto3 = "";
        switch ($usuario) {
            case 'AGUIRRE VASQUEZ MARIA RENEE':
                $firmaAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                $selloAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 20, 'height' => 37];
                $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL ULTIMA MARIA RENEE.png');
                $selloUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE.png');
                $firmaUltimaCoords = ['x' => 90, 'y' => 186, 'width' => 35, 'height' => 40];
                $selloUltimaCoords = ['x' => 85, 'y' => 199, 'width' => 40, 'height' => 40];
                $texto1 = "DRA. MARIA RENEÉ AGUIRRE VASQUEZ";
                $texto2 = "MEDICINA LABORAL Y SALUD OCUPACIONAL";
                $texto3 = "M.P.A-7676725 CM:11019";
                $textoFecha = "{$sucursal}, {$fechaActual}";
            break;

            default:
                $firmaAnteriorPath = public_path('/glfirmasello/default/firma.png');
                $selloAnteriorPath = public_path('/glfirmasello/default/sello.png');
                $firmaAnteriorCoords = ['x' => 160, 'y' => 230, 'width' => 50, 'height' => 30];
                $selloAnteriorCoords = ['x' => 170, 'y' => 230, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/default/firma.png');
                $selloUltimaPath = public_path('/glfirmasello/default/sello.png');
                $firmaUltimaCoords = ['x' => 85, 'y' => 215, 'width' => 60, 'height' => 40];
                $selloUltimaCoords = ['x' => 85, 'y' => 220, 'width' => 50, 'height' => 50];
                $textoFecha = null; // ✅ aquí explícitamente es null
            break;
        }

        /* $textoFecha = "{$sucursal}, {$fechaActual}"; */

        if (!file_exists($firmaAnteriorPath) || !file_exists($selloAnteriorPath) ||
            !file_exists($firmaUltimaPath) || !file_exists($selloUltimaPath)) {
            return back()->withErrors(['error' => 'Alguna firma o sello no fue encontrada para el usuario autenticado.']);
        }

        $uploadedPdfPath = $request->file('document')->getPathname();
        $nombreDocumento = $request->input('nombre_documento', uniqid());
        $outputFileName = "{$nombreDocumento}.pdf";
        $outputPath = $carpetaCliente . "/$outputFileName";

        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($uploadedPdfPath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($templateId, 0, 0);

            try {
                if ($pageNo == $pageCount) {
                    $pdf->Image($firmaUltimaPath, $firmaUltimaCoords['x'], $firmaUltimaCoords['y'], $firmaUltimaCoords['width'], $firmaUltimaCoords['height']);
                    $pdf->Image($selloUltimaPath, $selloUltimaCoords['x'], $selloUltimaCoords['y'], $selloUltimaCoords['width'], $selloUltimaCoords['height']);
                
                    $xSello = $selloUltimaCoords['x'];
                    $ySello = $selloUltimaCoords['y'];
                    $anchoSello = $selloUltimaCoords['width'];
                    $altoSello = $selloUltimaCoords['height'];
                    $lineHeight = 5;

                    $textos = [$texto1, $texto2, $texto3, $textoFecha];
                    $pdf->SetFont('Helvetica', '', 10);
                    $pdf->SetTextColor(0, 0, 0);
                    $ySelloAjustado = $ySello - 15;

                    foreach ($textos as $index => $lineaTexto) {
                        $anchoTexto = $pdf->GetStringWidth($lineaTexto);
                        $xTextoCentrado = $xSello + ($anchoSello / 2) - ($anchoTexto / 2);

                        $yTexto = $ySelloAjustado + $altoSello + ($lineHeight * $index) + 2;

                        $pdf->SetXY($xTextoCentrado, $yTexto);

                        if ($lineaTexto !== $textoFecha) {
                            $pdf->SetFont('Helvetica', 'BU', 10);
                        } else {
                            $pdf->SetFont('Helvetica', '', 10);
                        }

                        $pdf->Cell($anchoTexto, $lineHeight, utf8_decode($lineaTexto), 0, 1, 'C');
                    }
                } else {
                    $pdf->Image($firmaAnteriorPath, $firmaAnteriorCoords['x'], $firmaAnteriorCoords['y'], $firmaAnteriorCoords['width'], $firmaAnteriorCoords['height']);
                    $pdf->Image($selloAnteriorPath, $selloAnteriorCoords['x'], $selloAnteriorCoords['y'], $selloAnteriorCoords['width'], $selloAnteriorCoords['height']);
                }
            } catch (Exception $e) {
                return back()->withErrors(['error' => 'Error al insertar imágenes: ' . $e->getMessage()]);
            }
        }

        try {
            $pdf->Output($outputPath, 'F');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar el archivo PDF firmado: ' . $e->getMessage()]);
        }

        if (!file_exists($outputPath)) {
            return back()->withErrors(['error' => 'No se pudo guardar el archivo PDF firmado.']);
        }

        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($uploadedPdfPath);

        if ($pageCount < 1) {
            return back()->withErrors(['error' => 'El archivo PDF no contiene páginas.']);
        }

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($templateId, 0, 0);

            if ($pageNo == $pageCount) {
                $xSello = $selloUltimaCoords['x'];
                $ySello = $selloUltimaCoords['y'];
                $anchoSello = $selloUltimaCoords['width'];
                $altoSello = $selloUltimaCoords['height'];

                $lineHeight = 5;
                $textos = [$texto1, $texto2, $texto3, $textoFecha];
                $pdf->SetFont('Helvetica', '', 10);
                $pdf->SetTextColor(0, 0, 0);

                $ySelloAjustado = $ySello - 15;

                foreach ($textos as $index => $lineaTexto) {
                    $anchoTexto = $pdf->GetStringWidth($lineaTexto);
                    $xTextoCentrado = $xSello + ($anchoSello / 2) - ($anchoTexto / 2);
                    $yTexto = $ySelloAjustado + $altoSello + ($lineHeight * $index) + 2;

                    $pdf->SetXY($xTextoCentrado, $yTexto);

                    if ($lineaTexto !== $textoFecha) {
                        $pdf->SetFont('Helvetica', 'BU', 10);
                    } else {
                        $pdf->SetFont('Helvetica', '', 10);
                    }

                    $pdf->Cell($anchoTexto, $lineHeight, utf8_decode($lineaTexto), 0, 1, 'C');
                }
            }
        }

        $outputFileName2 = "{$nombreDocumento}_procesado.pdf";
        $outputPath = $carpetaCliente . "/$outputFileName2";

        try {
            $pdf->Output($outputPath, 'F');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar el archivo PDF procesado: ' . $e->getMessage()]);
        }

        $archivo_name = $outputFileName2;

        if (!file_exists($outputPath)) {
            return back()->withErrors(['error' => 'No se pudo guardar el archivo PDF procesado.']);
        }

        $archivo3_name = null;
        if ($request->hasFile('documentword')) {
            $file = $request->file('documentword');
            $archivo3_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo3_name);
        }

        $usuarioId = auth()->user()->id;
        $usuarioRegistro = auth()->user()->name;

        $informefinalcliente = InformeFinal::create([
                'cliente' => $request->cliente,
                'fechabateria' => $request->fechabateria,
                'estado' => $request->estado,
                'clienteitaid' => $id,
                'clienteitanombre' => $request->cliente,
                'documentfirmado' => $outputFileName,
                'document' => $outputFileName2,
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioRegistro,
                'estado' => 'APROBADO',
                'documentword' => $archivo3_name,
                'servicio' => $request->tramite,
                'proveedorasignado' => $request->proveedornombre,
                'clienteid' => $id,
                'clientenombre' => $request->cliente,
                'tipocliente' => 'ITA'
            ]);
            
            if ($informefinalcliente) {
                $usuarioAutenticado = auth()->user();
            
                if ($usuarioAutenticado->hasRole(['PROVEEDOR', 'PROVEEDOR IF'])) { 
                        $usuariosNotificar = User::whereIn('id', [3, 5, 10, 11, 25, 33])->get();
                        foreach ($usuariosNotificar as $usuarioDestino) {
                            $usuarioDestino->notify(new InformeFinalProveedorNotification($informefinalcliente));
                        }
                }
            }
            

        return back()->with('info', 'El archivo PDF firmado y sellado ha sido guardado correctamente.');
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
    public function buscarprogramacionesclienteita(Cliente $cliente, Request $request)
        {
            return $this->index($cliente, $request);
        }
    public function buscarporproveedor(Cliente $cliente, Request $request)
        {
            return $this->reservasmedicas($cliente, $request);
        }
//
//INFORMES FINALES AUDITORIA
    public function informesfinalesauditoria(ClienteAuditoria $clienteauditoria, Request $request)
    {
        $proveedores = Proveedor::orderBy('proveedor')->get(['id', 'proveedor', 'celular']);

        $aprobaciones = AprobacionInformeFinal::all();

        $fechas = Programacionsubcliente::pluck('fechabateria')->unique()->sort()->toArray();

        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
        $userRole = auth()->user()->getRoleNames()->first(); 
        $query = Programacionsubcliente::with(['tramitesubclienteauditoria', 'requisitosclienteauditoriamedica', 'estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria', 'proveedorinformesfinalesauditoria', 'informesfinalesauditoria'])
            ->whereNotNull('clienteauditoriaid');

        if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
            $query->where('fechabateria', $request->buscarporfecha);
        }

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('clienteauditoria', function ($q) use ($request) {
                $q->where('clienteauditorianombre', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        /* $programacionclientes = $query->simplePaginate(300);
        $grouped = $programacionclientes->groupBy(function($item) {
            return $item->clienteauditorianombre . '|' . $item->fechabateria;
        }); */
            $asignarCount = 0;
            $aprobarbateriaCount = 0;
            $subirinformeCount = 0;
            $subirinformeCount2 = 0;
            $enrevisionCount = 0;
            $enrevisionCount2 = 0;
            $solicitorevisionCount = 0;
            $solicitorevisionCount2 = 0;
            $aprobadosCount = 0;
            $aprobadosCount2 = 0;
            $docobservadosCount = 0;

            $result = [];
            
            $hasSearchParams = $request->has('buscarporfecha') || $request->has('buscarporcliente');
            if (!$hasSearchParams && !$request->has('buscartodo')) {
                return view('admin.informesfinales.informesfinalesauditoria', compact(
                    /* 'nombreusuario',  */
                    'proveedores', 
                    'fechas', 
                    'aprobaciones', 
                    /* 'estadoinforme',  */
                    'asignarCount', 
                    'aprobarbateriaCount', 
                    'subirinformeCount', 
                    'subirinformeCount2', 
                    'solicitorevisionCount', 
                    'solicitorevisionCount2', 
                    'aprobadosCount', 
                    'aprobadosCount2', 
                    'docobservadosCount', 
                    'esProveedor', 
                    'usuarioAutenticado', 
                    'userRole', 
                    'clienteauditoria',
                    'result'
                ));
            }
            $query = Programacionsubcliente::with(['tramitesubclienteauditoria', 'requisitosclienteauditoriamedica', 'estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria', 'proveedorinformesfinalesauditoria', 'informesfinalesauditoria'])
            ->whereNotNull('clienteauditoriaid');
            if (!$request->has('buscartodo')) {
                if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
                    $query->where('fechabateria', $request->buscarporfecha);
                }
                if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
                    $query->whereHas('clienteauditoria', function ($q) use ($request) {
                        $q->where('clienteauditorianombre', 'LIKE', '%' . $request->buscarporcliente . '%');
                    });
                }
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
                /* ->whereNull('deleted_at') */
                ->first();
            $historiamedica = Documentacionsubcliente::withTrashed()
                ->where('clienteauditoriaid', $items->first()->clienteauditoriaid)
                ->where('accion', 'HISTORIA MÉDICA')
                ->whereNull('motivoanulacion')
                ->first();
            $motivoabandonobateria = Bateriasubcliente::where('clienteauditoriaid', $items->first()->clienteauditoriaid)
                ->where('fechabateria', $fechabateria)
                ->first();

            $usuarioRegistro = ClienteAuditoria::where('id', $items->first()->clienteauditoriaid)
                ->first();

            /* $aprobacioninforme = Aprobacioninformefinal::withTrashed()
                ->where('clienteauditoriaid', $items->first()->clienteauditoriaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('created_at', 'desc')
                ->first(); */
            $aprobacioninforme = Detallerecibo::withTrashed()
                ->where('clienteid', $items->first()->clienteauditoriaid)
                ->where('fechabateria', $fechabateria)
                ->where('detalle', 'INFORME FINAL')
                ->orderBy('created_at', 'desc')
                ->first();
                
           $aprobacion = Aprobacioninformefinal::withTrashed()
                ->where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('created_at', 'desc')
                ->first();

            $aprobacioninformefinales = $aprobacioninforme ? $aprobacioninforme->estado : null;

            $ultimoInforme = InformeFinal::withTrashed()
                ->where('clienteauditoriaid', $items->first()->clienteauditoriaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('created_at', 'desc')
                ->first();
            $ultimoEstado = $ultimoInforme ? $ultimoInforme->estado : null;

            $tramitebateria = Tramitesubcliente::where('clienteauditoriaid', $items->first()->clienteauditoriaid)
                ->where('fechabateria', $fechabateria)
                ->first();
            $tramite = $tramitebateria ? $tramitebateria->tramite : null;

            $ultimoobservacionInforme = InformeFinal::withTrashed()
                ->where('clienteauditoriaid', $items->first()->clienteauditoriaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('created_at', 'desc')
                ->first();
            $ultimoobservacionInforme2 = InformeFinal::withTrashed()
                ->where('clienteauditoriaid', $items->first()->clienteauditoriaid)
                ->where('fechabateria', $fechabateria)
                ->orderBy('deleted_at', 'desc')
                ->first();
            $ultimaObservacion = $ultimoobservacionInforme ? $ultimoobservacionInforme->observaciones : null;
            $ultimaObservacion2 = $ultimoobservacionInforme2 ? $ultimoobservacionInforme2->observaciones : null;
            $aprob = $aprobacion ? $aprobacion->estado : null;
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
                $documentacionfirmado = $item->documentacionsubclienteauditoria->where('accion', $item->accionnombre)->first();
                $documentacionword = $item->documentacionsubclienteauditoria->where('accion', $item->accionnombre)->first();
                $image = $item->documentacionsubclienteauditoria->where('accion', $item->accionnombre)->first();
                $image2 = $item->documentacionsubclienteauditoria->where('accion', $item->accionnombre)->first();
                $accionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';
                $documentacionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';

                $observacion = $documentacion ? $documentacion->observacion : null;

                $estadoProgramacion = $item->estadoprogramacionsubclienteauditoria
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();
                $motivoabandono = Bateriasubcliente::where('clienteauditoriaid', $item->clienteauditoriaid)
                    ->where('fechabateria', $item->fechabateria)
                    ->value('motivoabandono');

                    $diagnosticomedicoauditoria = Documentacionsubcliente::where('clienteauditoriaid', $item->clienteauditoriaid)
                ->where('fechabateria', $item->fechabateria)
                ->where('accion', 'DIAGNÓSTICO MÉDICO')
                ->first();

            $diagnosticomedicoaudi = $diagnosticomedicoauditoria ? $diagnosticomedicoauditoria->document : null;

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
                    'documentfirmado' => $documentacionfirmado,
                    'documentword' => $documentacionword,
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
                'diagnosticoauditoria' => $diagnosticomedicoaudi,

                'estadoinforme' => $aprobacioninformefinales,
                'estado_informefinal' => $ultimoEstado,
                'tramite' => $tramite,
                'estadoaprob' => $aprob,
                'documentfirmado' => $documentosubido ? $documentosubido->documentfirmado : null,
                'documentword' => $documentosubido ? $documentosubido->documentword : null,
                'ultima_observacion' => $ultimaObservacion,
                'ultima_observacion2' => $ultimaObservacion2,
                'observacion' => $observacion,
            ];
        }
 
        $asignarCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if (
                ($item['proveedornombre'] && $item['estadoaprob'] !== 'APROBADO' && $item['estado'] === 'COMPLETO') 
                || 
                ($item['estadoinforme'] !== 'PAGO PROCESADO' && $item['proveedornombre'] && $item['estado'] === 'COMPLETO')
            ) {
                $count++;
            }
            return $count;
        }, 0);
        $aprobarbateriaCount = array_reduce($result, function ($count, $item) {
            if (
                $item['proveedornombre'] &&
                $item['estadoaprob'] !== 'APROBADO' &&
                $item['estado'] === 'COMPLETO' &&
                $item['estadoinforme'] !== 'PAGO PROCESADO'
            ) {
                $count++;
            }
            return $count;
        }, 0);
        $subirinformeCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estadoaprob'] === 'APROBADO' && !$item['estado_informefinal'] && $item['estado'] === 'COMPLETO' || $item['estadoinforme'] === 'PAGO PROCESADO' && $item['proveedornombre'] && !$item['estado_informefinal'] && $item['estado'] === 'COMPLETO') {
                $count++;
            }
            return $count;
        }, 0);
        $subirinformeCount2 = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estadoaprob'] === 'APROBADO' && !$item['estado_informefinal'] && $item['estado'] === 'COMPLETO' && $item['proveedornombre'] === $usuarioAutenticado || $item['estadoinforme'] === 'PAGO PROCESADO' && !$item['estado_informefinal'] && $item['estado'] === 'COMPLETO' && $item['proveedornombre'] === $usuarioAutenticado ) {
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

        if (isset($clienteauditoriaid)) {
            $requisitosClientepolizas = Requisitosclientesauditoria::where('clienteauditoriaid', $clienteauditoriaid)->wherenotNull('banco')->get();
        } else {
            $requisitosClientepolizas = collect();
            }

        return view('admin.informesfinales.informesfinalesauditoria', compact('programacionclientes','requisitosClientepolizas','userRole','docobservadosCount','esProveedor','usuarioAutenticado','asignarCount','aprobarbateriaCount','subirinformeCount','subirinformeCount2','enrevisionCount','enrevisionCount2','solicitorevisionCount','solicitorevisionCount2','aprobadosCount','aprobadosCount2','proveedores','result', 'clienteauditoria', 'fechas', 'aprobaciones'));
    }
    public function guardaraprobacioninformefinalauditoria(Request $request, $id)
    {
        $request->validate([
            'clienteauditorianombre' => 'required|string',
            'fechabateria' => 'required|date',
            'estado' => 'required|string',
            'proveedornombre' => 'required|string',
            'clienteauditoriaid' => 'required|string',
        ]);
        $usuarioId = auth()->user()->id;
        $usuarioRegistro = auth()->user()->name;

        AprobacionInformeFinal::create([
            'fechabateria' => $request->fechabateria,
            'estado' => $request->estado,
            'proveedorasignado' => $request->proveedornombre,
            'clienteauditoriaid' => $id,
            'clienteauditorianombre' => $request->clienteauditorianombre,
            'usuarioid' => $usuarioId,
            'usuarioregistro' => $usuarioRegistro,
        ]);

        return redirect()->route('admin.informesfinales.informesfinalesauditoria')->with('info', 'Aprobación guardada exitosamente.');
    }
    public function guardarinformefinalauditoria(StoreInformefinalRequest $request, $id, ClienteAuditoria $clienteauditoria)
    {
        $request->validate([
            'document' => 'required|mimes:pdf|max:999999',
        ]);

        $id = $request->input('clienteauditoriaid');
        $clienteauditorianombre = $request->input('clienteauditorianombre');
        $carpetaCliente = public_path("informesfinalesclientesauditoria/{$id}");
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }

        $usuario = auth()->user()->name;
        $sucursal = auth()->user()->sucursal; // Asegúrate de que el usuario autenticado tenga el atributo `sucursal`.
        $fechaActual = Carbon::now()->locale('es')->isoFormat('D [de] MMMM [del] YYYY');

        // Procesar y almacenar el archivo original
        $archivo_name = null;
            if ($request->hasFile('archivo2')) {
                $file = $request->file('archivo2');
                
                $carpetaCliente = public_path("/informesfinalesclientesauditoria/{$id}");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);}
                $archivo_name = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name);
            }

        // VARIABLES PARA FIRMAS Y SELLOS
        $firmaAnteriorPath = '';
        $selloAnteriorPath = '';
        $firmaUltimaPath = '';
        $selloUltimaPath = '';
        $firmaAnteriorCoords = [];
        $selloAnteriorCoords = [];
        $firmaUltimaCoords = [];
        $selloUltimaCoords = [];
        $texto1 = $texto2 = $texto3 = "";
        switch ($usuario) {
            case 'AGUIRRE VASQUEZ MARIA RENEE':
                $firmaAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                $selloAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 20, 'height' => 37];
                $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL ULTIMA MARIA RENEE.png');
                $selloUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE.png');
                $firmaUltimaCoords = ['x' => 90, 'y' => 186, 'width' => 35, 'height' => 40];
                $selloUltimaCoords = ['x' => 85, 'y' => 199, 'width' => 40, 'height' => 40];
                $texto1 = "DRA. MARIA RENEÉ AGUIRRE VASQUEZ";
                $texto2 = "MEDICINA LABORAL Y SALUD OCUPACIONAL";
                $texto3 = "M.P.A-7676725 CM:11019";
                $textoFecha = "{$sucursal}, {$fechaActual}";
            break;

            default:
                $firmaAnteriorPath = public_path('/glfirmasello/default/firma.png');
                $selloAnteriorPath = public_path('/glfirmasello/default/sello.png');
                $firmaAnteriorCoords = ['x' => 160, 'y' => 230, 'width' => 50, 'height' => 30];
                $selloAnteriorCoords = ['x' => 170, 'y' => 230, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/default/firma.png');
                $selloUltimaPath = public_path('/glfirmasello/default/sello.png');
                $firmaUltimaCoords = ['x' => 85, 'y' => 215, 'width' => 60, 'height' => 40];
                $selloUltimaCoords = ['x' => 85, 'y' => 220, 'width' => 50, 'height' => 50];
                $textoFecha = null;
            break;
        }

        // Agregar el texto de la sucursal y la fecha
        /* $textoFecha = "{$sucursal}, {$fechaActual}"; */

        // Verificar si los archivos de firma y sello existen
        if (!file_exists($firmaAnteriorPath) || !file_exists($selloAnteriorPath) ||
            !file_exists($firmaUltimaPath) || !file_exists($selloUltimaPath)) {
            return back()->withErrors(['error' => 'Alguna firma o sello no fue encontrada para el usuario autenticado.']);
        }

        $uploadedPdfPath = $request->file('document')->getPathname();
        $nombreDocumento = $request->input('nombre_documento', uniqid());
        $outputFileName = "{$nombreDocumento}.pdf";
        $outputPath = $carpetaCliente . "/$outputFileName";

        // Crear instancia de FPDI y procesar el PDF
        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($uploadedPdfPath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($templateId, 0, 0);

            try {
                if ($pageNo == $pageCount) {
                    // Última página: firma y sello diferentes
                    $pdf->Image($firmaUltimaPath, $firmaUltimaCoords['x'], $firmaUltimaCoords['y'], $firmaUltimaCoords['width'], $firmaUltimaCoords['height']);
                    $pdf->Image($selloUltimaPath, $selloUltimaCoords['x'], $selloUltimaCoords['y'], $selloUltimaCoords['width'], $selloUltimaCoords['height']);
                
                    
                    // Coordenadas del sello 
                    $xSello = $selloUltimaCoords['x'];
                    $ySello = $selloUltimaCoords['y'];
                    $anchoSello = $selloUltimaCoords['width'];
                    $altoSello = $selloUltimaCoords['height'];

                    // Reducir interlineado
                    $lineHeight = 5;

                    // Textos a centrar
                    $textos = [$texto1, $texto2, $texto3, $textoFecha];
                    $pdf->SetFont('Helvetica', '', 10);
                    $pdf->SetTextColor(0, 0, 0);

                    // Ajustar la distancia entre el grupo de textos y el sello
                    $ySelloAjustado = $ySello - 15; // Ajusta este valor para acercar o alejar todo el grupo de textos

                    // Calcular el centro horizontal tomando en cuenta el sello
                    foreach ($textos as $index => $lineaTexto) {
                        $anchoTexto = $pdf->GetStringWidth($lineaTexto); // Calcular el ancho del texto
                        $xTextoCentrado = $xSello + ($anchoSello / 2) - ($anchoTexto / 2); // Centrar el texto debajo del sello

                        // Ajustar la posición vertical debajo del sello
                        $yTexto = $ySelloAjustado + $altoSello + ($lineHeight * $index) + 2; // Mueve todo el grupo más cerca del sello

                        $pdf->SetXY($xTextoCentrado, $yTexto);

                        // Aplicar negrita y subrayado, excepto para el texto de la fecha
                        if ($lineaTexto !== $textoFecha) {
                            $pdf->SetFont('Helvetica', 'BU', 10); // Negrita y subrayado
                        } else {
                            $pdf->SetFont('Helvetica', '', 10); // Solo normal para la fecha
                        }

                        $pdf->Cell($anchoTexto, $lineHeight, utf8_decode($lineaTexto), 0, 1, 'C');
                    }
                } else {
                    // Páginas anteriores
                    $pdf->Image($firmaAnteriorPath, $firmaAnteriorCoords['x'], $firmaAnteriorCoords['y'], $firmaAnteriorCoords['width'], $firmaAnteriorCoords['height']);
                    $pdf->Image($selloAnteriorPath, $selloAnteriorCoords['x'], $selloAnteriorCoords['y'], $selloAnteriorCoords['width'], $selloAnteriorCoords['height']);
                }
            } catch (Exception $e) {
                return back()->withErrors(['error' => 'Error al insertar imágenes: ' . $e->getMessage()]);
            }
        }

        // Guardar el PDF firmado
        try {
            $pdf->Output($outputPath, 'F');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar el archivo PDF firmado: ' . $e->getMessage()]);
        }

        if (!file_exists($outputPath)) {
            return back()->withErrors(['error' => 'No se pudo guardar el archivo PDF firmado.']);
        }

        // Crear instancia de FPDI y procesar todas las páginas
        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($uploadedPdfPath);

        // Validar que haya al menos una página en el archivo
        if ($pageCount < 1) {
            return back()->withErrors(['error' => 'El archivo PDF no contiene páginas.']);
        }

        // Procesar cada página
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($templateId, 0, 0);

            // Si es la última página, añadir solo los textos
            if ($pageNo == $pageCount) {
                $xSello = $selloUltimaCoords['x'];
                $ySello = $selloUltimaCoords['y'];
                $anchoSello = $selloUltimaCoords['width'];
                $altoSello = $selloUltimaCoords['height'];

                $lineHeight = 5;
                $textos = [$texto1, $texto2, $texto3, $textoFecha];
                $pdf->SetFont('Helvetica', '', 10);
                $pdf->SetTextColor(0, 0, 0);

                // Ajustar distancia entre textos y sello
                $ySelloAjustado = $ySello - 15;

                foreach ($textos as $index => $lineaTexto) {
                    $anchoTexto = $pdf->GetStringWidth($lineaTexto);
                    $xTextoCentrado = $xSello + ($anchoSello / 2) - ($anchoTexto / 2);
                    $yTexto = $ySelloAjustado + $altoSello + ($lineHeight * $index) + 2;

                    $pdf->SetXY($xTextoCentrado, $yTexto);

                    if ($lineaTexto !== $textoFecha) {
                        $pdf->SetFont('Helvetica', 'BU', 10); // Negrita y subrayado
                    } else {
                        $pdf->SetFont('Helvetica', '', 10); // Texto normal
                    }

                    $pdf->Cell($anchoTexto, $lineHeight, utf8_decode($lineaTexto), 0, 1, 'C');
                }
            }
        }

        // Guardar el PDF procesado
        $outputFileName2 = "{$nombreDocumento}_procesado.pdf";
        $outputPath = $carpetaCliente . "/$outputFileName2";

        try {
            $pdf->Output($outputPath, 'F');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar el archivo PDF procesado: ' . $e->getMessage()]);
        }

        // Actualizar la variable $archivo_name para referirse al nuevo archivo
        $archivo_name = $outputFileName2;

        if (!file_exists($outputPath)) {
            return back()->withErrors(['error' => 'No se pudo guardar el archivo PDF procesado.']);
        }

        $archivo3_name = null;
        if ($request->hasFile('documentword')) {
            $file = $request->file('documentword');
            $archivo3_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo3_name);
        }

        $usuarioId = auth()->user()->id;
        $usuarioRegistro = auth()->user()->name;

            $informefinalcliente = InformeFinal::create([
                'cliente' => $request->clienteauditoria,
                'fechabateria' => $request->fechabateria,
                'estado' => $request->estado,
                'clienteauditoriaid' => $id,
                'clienteauditorianombre' => $clienteauditorianombre,
                'documentfirmado' => $outputFileName,
                'document' => $outputFileName2,
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioRegistro,
                'estado' => 'APROBADO',
                'documentword' => $archivo3_name,
                'servicio' => $request->tramite,
                'proveedorasignado' => $request->proveedornombre,
                'clienteid' => $id,
                'clientenombre' => $clienteauditorianombre,
                'tipocliente' => 'AUDITORIA'
            ]);

            if ($informefinalcliente) {
                $usuarioAutenticado = auth()->user();
            
                if ($usuarioAutenticado->hasRole(['PROVEEDOR', 'PROVEEDOR IF'])) { 
                        $usuariosNotificar = User::whereIn('id', [3, 5, 10, 11, 25, 33])->get();
                        foreach ($usuariosNotificar as $usuarioDestino) {
                            $usuarioDestino->notify(new InformeFinalProveedorNotification($informefinalcliente));
                        }
                }
            }


        return back()->with('info', 'El archivo PDF firmado y sellado ha sido guardado correctamente.');
    } 
    public function solrevisioninformefinalauditoria(Request $request, Informefinal $informefinal)
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
        $clienteauditoria = ClienteAuditoria::find($informefinal->clienteauditoriaid);

        return redirect()->route('admin.informesfinales.informesfinalesauditoria', $informefinal)->with('info', 'Solicitud enviada exitosamente.');
    }
    public function aprobarinformefinalfsauditoria(Request $request, Informefinal $informefinal)
    {
        $informeFinal = Informefinal::findOrFail($request->idinformefinal);
        $informeFinal->estado = 'APROBADO';
        $informeFinal->save();

        $clienteauditoriaid = ClienteAuditoria::find($informefinal->clienteauditoriaid);

        return redirect()->route('admin.informesfinales.informesfinalesauditoria', $informefinal)->with('info', 'Informe aprobado exitosamente.');
    }
    public function buscarprogramacionesclienteauditoria(ClienteAuditoria $clienteauditoria, Request $request)
        {
            return $this->informesfinalesauditoria($clienteauditoria, $request);
        }
//
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
    public function estadodocumentacionprogramacion(Cliente $cliente, Request $request)
    {
        $estadoGeneral = 'NO REGISTRADO';
        $sucursal = $cliente->sucursal;

        $proveedores = Proveedor::orderBy('proveedor')->get(['id', 'proveedor', 'celular']);

        $aprobaciones = AprobacionInformeFinal::all();

        $fechas = Programacionsubcliente::pluck('fechabateria')->unique()->sort()->toArray();

        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
        $nombreusuario = auth()->user()->name;

        $userRole = auth()->user()->getRoleNames()->first(); 
        
        $query = Programacionsubcliente::with(['requisitosubcliente', 'bateriasubcliente', 'estadoprogramacionsubcliente', 'documentacionsubcliente', 'proveedorinformesfinales', 'informesfinales'])
            ->whereNotNull('clienteitaid');

        if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
            $query->where('fechabateria', $request->buscarporfecha);
        }

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('clienteita', function ($q) use ($request) {
                $q->where('clienteitanombre', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }
        
        /* $programacionclientes = $query->get();
        $grouped = $programacionclientes->groupBy(function($item) {
            return $item->clienteitanombre . '|' . $item->fechabateria;
        }); */

        // BUSCAR POR CLIENTE
            $documentosCount = 0;
            $resultadosCount = 0;
            $abandonaronCount = 0;
            $completosCount = 0;
            $incompletosCount = 0;
            $result = [];
            
            $hasSearchParams = $request->has('buscarporfecha') || $request->has('buscarporcliente');
            if (!$hasSearchParams && !$request->has('buscartodo')) {
                return view('admin.informesfinales.estadodocumentacionprogramacion', compact(
                    'nombreusuario', 
                    'proveedores', 
                    'fechas', 
                    'aprobaciones', 
                    'estadoGeneral', 
                    'documentosCount', 
                    'resultadosCount', 
                    'abandonaronCount', 
                    'completosCount', 
                    'incompletosCount', 
                    'esProveedor', 
                    'usuarioAutenticado', 
                    'userRole', 
                    'cliente',
                    'result'
                ));
            }
            $query = Programacionsubcliente::with([
                'requisitosubcliente', 'bateriasubcliente', 'estadoprogramacionsubcliente', 
                'documentacionsubcliente', 'proveedorinformesfinales', 'informesfinales'
            ])->whereNotNull('clienteitaid');
            if (!$request->has('buscartodo')) {
                if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
                    $query->where('fechabateria', $request->buscarporfecha);
                }
                if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
                    $query->whereHas('clienteita', function ($q) use ($request) {
                        $q->where('clienteitanombre', 'LIKE', '%' . $request->buscarporcliente . '%');
                    });
                }
            }
            $programacionclientes = $query->get();
            $grouped = $programacionclientes->groupBy(function($item) {
                return $item->clienteitanombre . '|' . $item->fechabateria;
            });
        //

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
                ->whereNull('motivoanulacion')
                ->first();
            $motivoabandonobateria = Bateriasubcliente::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->first();
            $usuarioRegistro = Cliente::where('id', $items->first()->clienteitaid)
                ->first();
            $diagnosticomedico = Documentacionsubcliente::where('clienteitaid', $items->first()->clienteitaid)
                ->where('fechabateria', $fechabateria)
                ->where('accion', 'DIAGNÓSTICO MÉDICO')
                ->first();

            $historiamedicaclienteita = $historiamedica ? $historiamedica->document : null;
            $diagnosticomedicoita = $diagnosticomedico ? $diagnosticomedico->document : null;
            $usuarioregistro = $usuarioRegistro ? $usuarioRegistro->sucursal : null;
            $motivoabandono = $motivoabandonobateria ? $motivoabandonobateria->motivoabandono : null;
            $clienteitaid = $items->first()->clienteitaid;
        
            $estado = 'COMPLETO';
            $estadoGeneral = 'NO REGISTRADO';
            $accionesConEstado = [];

            $requisitos = Requisitosubcliente::where('clienteitaid', $items->first()->clienteitaid)->first();

            if (is_null($requisitos)) {
                $estadoGeneral = 'NO REGISTRADO';
            } else {
                $estadoGeneral = 'COMPLETO';

                $campos = ['poder','numeropoder','avcci','cnacasegurado','ciasegurado','cmatrimonio','cnacconyuge','ciconyuge','cnacjihos',
                'cihijos','denfaccidente','crodomicilio','contrato','usuarioid','usuarioregistro','ctrabajo','boletapago','egestora',
                'actdatos','resolinvhijos','cunionlibre','cnacimientounionlibre','ciunionlibre','cdivorcio','cdefuncion','servicio'];
                foreach ($campos as $campo) {
                    if (!is_null($requisitos->$campo) && stripos($requisitos->$campo, 'PENDIENTE') !== false) {
                        $estadoGeneral = 'PENDIENTE';
                        break;
                    }
                }
            }

            foreach ($items as $item) {
                $documentacion = $item->documentacionsubcliente->where('accion', $item->accionnombre)->where('fechabateria', $item->fechabateria)->first();
                $documentacionfirmado = $item->documentacionsubcliente->where('accion', $item->accionnombre)->where('fechabateria', $item->fechabateria)->first();
                $documentacionword = $item->documentacionsubcliente->where('accion', $item->accionnombre)->where('fechabateria', $item->fechabateria)->first();
                $image = $item->documentacionsubcliente->where('accion', $item->accionnombre)->where('fechabateria', $item->fechabateria)->first();
                $image2 = $item->documentacionsubcliente->where('accion', $item->accionnombre)->where('fechabateria', $item->fechabateria)->first();
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

                //REQUISITOS
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
                    $cmatrimonio = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->cmatrimonio);})->first()->cmatrimonio ?? null;
                    $cnacconyuge = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->cnacconyuge);})->first()->cnacconyuge ?? null;
                    $ciconyuge = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->ciconyuge);})->first()->ciconyuge ?? null;
                    $cnacjihos = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->cnacjihos);})->first()->cnacjihos ?? null;
                    $cihijos = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->cihijos);})->first()->cihijos ?? null;
                    $denfaccidente = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->denfaccidente);})->first()->denfaccidente ?? null;
                    $crodomicilio = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->crodomicilio);})->first()->crodomicilio ?? null;
                    $contrato = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->contrato);})->first()->contrato ?? null;
                    $egestora = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->egestora);})->first()->egestora ?? null;
                    $dictamencalentenc = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->dictamencalentenc);})->first()->dictamencalentenc ?? null;
                    $infomedicasalud = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->infomedicasalud);})->first()->infomedicasalud ?? null;
                    $ctrabajo = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->ctrabajo);})->first()->ctrabajo ?? null;
                    $boletapago = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->boletapago);})->first()->boletapago ?? null;
                    $actdatos = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->actdatos);})->first()->actdatos ?? null;
                    $resolinvhijos = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->resolinvhijos);})->first()->resolinvhijos ?? null;
                    $cunionlibre = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->cunionlibre);})->first()->cunionlibre ?? null;
                    $cnacimientounionlibre = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->cnacimientounionlibre);})->first()->cnacimientounionlibre ?? null;
                    $ciunionlibre = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->ciunionlibre);})->first()->ciunionlibre ?? null;
                    $cdivorcio = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->cdivorcio);})->first()->cdivorcio ?? null;
                    $cdefuncion = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->cdefuncion);})->first()->cdefuncion ?? null;
                    $polizasgen = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->polizasgen);})->first()->polizasgen ?? null;
                    $declasalud = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->declasalud);})->first()->declasalud ?? null;
                    $polizaseguro = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->polizaseguro);})->first()->polizaseguro ?? null;
                    $anteriordictamen = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->anteriordictamen);})->first()->anteriordictamen ?? null;
                    $poderciapoderado = $item->requisitosubcliente->filter(function ($requisito) {
                        return !empty($requisito->poderciapoderado);})->first()->poderciapoderado ?? null;
                    $servicio = $item->requisitosubcliente->filter(function ($requisito) {
                            return !empty($requisito->servicio);})->first()->servicio ?? null;
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
                    'documentfirmado' => $documentacionfirmado,
                    'documentword' => $documentacionword,
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
                    'servicio' => $servicio,
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
                'servicio' => $servicio,
                'diagnostico' => $diagnosticomedicoita,
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


        return view('admin.informesfinales.estadodocumentacionprogramacion', compact('programacionclientes','nombreusuario', 'documentosCount','resultadosCount','userRole','estadoGeneral','abandonaronCount','esProveedor','usuarioAutenticado','completosCount','incompletosCount','proveedores','result', 'cliente', 'fechas', 'aprobaciones'));
    }
    public function resultadosmedicosclientesauditoria(ClienteAuditoria $clienteauditoria, Request $request)
    {
        $estadoGeneralauditoria = 'NO REGISTRADO';
        $sucursal = $clienteauditoria->sucursal;
        $proveedores = Proveedor::orderBy('proveedor')->get(['id', 'proveedor', 'celular']);
        $aprobaciones = AprobacionInformeFinal::all();
        $fechas = Programacionsubcliente::pluck('fechabateria')->unique()->sort()->toArray();
        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
        $userRole = auth()->user()->getRoleNames()->first(); 
        $nombreusuario = auth()->user()->name;
        
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

        /* $programacionclientes = $query->simplePaginate(500);
        $grouped = $programacionclientes->groupBy(function($item) {
            return $item->clienteauditorianombre . '|' . $item->fechabateria;
        }); */
            $documentosCount = 0;
            $resultadosCount = 0;
            $abandonaronCount = 0;
            $completosCount = 0;
            $incompletosCount = 0;
            $result = [];
            
            $hasSearchParams = $request->has('buscarporfecha') || $request->has('buscarporcliente');
            if (!$hasSearchParams && !$request->has('buscartodo')) {
                return view('admin.informesfinales.resultadosmedicosclientesauditoria', compact(
                    'nombreusuario', 
                    'proveedores', 
                    'fechas', 
                    'aprobaciones', 
                    'estadoGeneralauditoria', 
                    'documentosCount', 
                    'resultadosCount', 
                    'abandonaronCount', 
                    'completosCount', 
                    'incompletosCount', 
                    'esProveedor', 
                    'usuarioAutenticado', 
                    'userRole', 
                    'clienteauditoria',
                    'result'
                ));
            }
            $query = Programacionsubcliente::with([ 'estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria','requisitosclienteauditoriamedica', 'requisitosubcliente', 'bateriasubcliente', 'estadoprogramacionsubcliente', 'documentacionsubcliente', 'proveedorinformesfinales', 'informesfinales'])
            ->whereNotNull('clienteauditoriaid');
            if (!$request->has('buscartodo')) {
                if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
                    $query->where('fechabateria', $request->buscarporfecha);
                }
                if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
                    $query->whereHas('clienteauditoria', function ($q) use ($request) {
                        $q->where('clienteauditorianombre', 'LIKE', '%' . $request->buscarporcliente . '%');
                    });
                }
            }
            $programacionclientes = $query->simplePaginate(500);
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
                ->whereNull('motivoanulacion')
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
                $documentacion = $item->documentacionsubclienteauditoria->where('accion', $item->accionnombre)->where('fechabateria', $item->fechabateria)->first();
                $documentacionfirmado = $item->documentacionsubclienteauditoria->where('accion', $item->accionnombre)->where('fechabateria', $item->fechabateria)->first();
                $documentacionword = $item->documentacionsubclienteauditoria->where('accion', $item->accionnombre)->where('fechabateria', $item->fechabateria)->first();
                $image = $item->documentacionsubclienteauditoria->where('accion', $item->accionnombre)->where('fechabateria', $item->fechabateria)->first();
                $image2 = $item->documentacionsubclienteauditoria->where('accion', $item->accionnombre)->where('fechabateria', $item->fechabateria)->first();
                $accionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';
                $documentacionEstado = $documentacion && $documentacion->created_at !== null ? 'COMPLETO' : 'PENDIENTE';

                $estadoProgramacion = $item->estadoprogramacionsubclienteauditoria
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();
                $motivoabandono = Bateriasubcliente::where('clienteauditoriaid', $item->clienteauditoriaid)
                    ->where('fechabateria', $item->fechabateria)
                    ->value('motivoabandono');

                    $diagnosticomedicoauditoria = Documentacionsubcliente::where('clienteauditoriaid', $item->clienteauditoriaid)
                ->where('fechabateria', $item->fechabateria)
                ->where('accion', 'DIAGNÓSTICO MÉDICO')
                ->first();

            $diagnosticomedicoaudi = $diagnosticomedicoauditoria ? $diagnosticomedicoauditoria->document : null;

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
                    'documentfirmado' => $documentacionfirmado,
                    'documentword' => $documentacionword,
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
                'diagnosticoauditoria' => $diagnosticomedicoaudi,
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
        
        /* $requisitosClientepolizas = Requisitosclientesauditoria::where('clienteauditoriaid', $clienteauditoriaid)->wherenotNull('banco')->get(); */

        if (isset($clienteauditoriaid)) {
            $requisitosClientepolizas = Requisitosclientesauditoria::where('clienteauditoriaid', $clienteauditoriaid)->wherenotNull('banco')->get();
        } else {
            $requisitosClientepolizas = collect(); // Colección vacía si no hay clienteauditoriaid
            }

        return view('admin.informesfinales.resultadosmedicosclientesauditoria', compact('programacionclientes','nombreusuario','requisitosClientepolizas','estadoGeneralauditoria', 'documentosCount','resultadosCount','userRole','abandonaronCount','esProveedor','usuarioAutenticado','completosCount','incompletosCount','proveedores','result', 'clienteauditoria', 'fechas', 'aprobaciones'));
    }
    public function buscarresultadosmedicosclientesauditoria(ClienteAuditoria $clienteauditoria, Request $request)
    {
        return $this->resultadosmedicosclientesauditoria($clienteauditoria, $request);
    }
    public function resultadosmedicosclientesbancos(ClienteBanco $clientebanco, Request $request)
    {
        $sucursal = $clientebanco->sucursal;
        $clientebancoid = $clientebanco->id;
        $proveedores = Proveedor::orderBy('proveedor')->get(['id', 'proveedor', 'celular']);
        $aprobaciones = AprobacionInformeFinal::all();
        $fechas = Programacionsubcliente::pluck('fechabateria')->unique()->sort()->toArray();
        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
        $userRole = auth()->user()->getRoleNames()->first(); 
        $empresaUsuario = auth()->user()->empresa;

        $query = Programacionsubcliente::with(['documentacionsubclientebanco', 'requisitosclienteauditoriamedica', 'requisitosubcliente', 'bateriasubcliente', 'estadoprogramacionsubcliente', 'documentacionsubcliente', 'proveedorinformesfinales', 'informesfinales'])
            ->whereNotNull('clientebancoid');

        if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
            $query->where('fechabateria', $request->buscarporfecha);
        }

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('clientebanco', function ($q) use ($request) {
                $q->where('clientenombre', 'LIKE', '%' . $request->buscarporcliente . '%');
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
                ->where('tipodocumento', 'DIGITAL')
                ->first();

            $declaracionmedicafisica = Fichamedicasubcliente::withTrashed()
                ->where('clientebancoid', $items->first()->clientebancoid)
                ->where('detalle', 'DECLARACIONES HECHAS AL MEDICO EXAMINADOR')
                ->where('tipodocumento', 'FISICO')
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
            $declaracionmedicaclientebancofisica = $declaracionmedicafisica ? $declaracionmedicafisica->document : null;
            $consentimiento = $consentimientoinformado ? $consentimientoinformado->document : null;
            $informefinalclientebanco = $informefinal ? $informefinal->document : null;
            $usuarioregistro = $usuarioRegistro ? $usuarioRegistro->sucursal : null;
            $empresaregistro = $usuarioRegistro ? $usuarioRegistro->asociadonombre : null;
            $motivoabandono = $motivoabandonobateria ? $motivoabandonobateria->motivoabandono : null;
            $clientebancoid = $items->first()->clientebancoid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {
                $documentacion = $item->documentacionsubclientebanco->where('accion', $item->accionnombre)->first();
                $image = $item->documentacionsubclientebanco->where('accion', $item->accionnombre)->first();
                $image2 = $item->documentacionsubclientebanco->where('accion', $item->accionnombre)->first();
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
                'declaracionmedicafisica' => $declaracionmedicaclientebancofisica,
                'informefinal' => $informefinalclientebanco,
                'consentimiento' => $consentimiento,
            ];
        }
 
        $completosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado'] === 'COMPLETO' && $item['declaracionmedica'] && $item['fichamedica'] && $item['consentimiento'] && $item['informefinal']) {
                $count++;
            }
            return $count;
        }, 0);

        $resultadosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado'] === 'COMPLETO' && $item['declaracionmedica'] && $item['fichamedica'] && $item['consentimiento'] && !$item['informefinal']) {
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
            if ($item['estado'] === 'INCOMPLETO' || !$item['declaracionmedica'] && !$item['fichamedica'] && !$item['consentimiento']) {
                $count++;
            }
            return $count;
        }, 0);

        return view('admin.informesfinales.resultadosmedicosclientesbancos', compact('empresaUsuario','clientebancoid','documentosCount','resultadosCount','userRole','esProveedor','usuarioAutenticado','completosCount','incompletosCount','proveedores','result', 'clientebanco', 'fechas', 'aprobaciones'));
    }
    public function buscarresultadosclientebanco(ClienteBanco $clientebanco, Request $request)
    {
        return $this->resultadosmedicosclientesbancos($clientebanco, $request);
    }
    public function guardarinformefinalclientebanco(StoreInformefinalRequest $request, ClienteBanco $clientebanco)
    {
        $clientebancoid = $clientebanco->id;
        $clientebanconombre = $clientebanco->nombrecompleto;
        $fechabateria = $request->input('fechabateria');

        $archivo_name = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $carpetaCliente = public_path("/informesfinalesclientesbanco/{$clientebancoid}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }
        $usuarioId = auth()->user()->id;
        $usuarioRegistro = auth()->user()->name;
    
            InformeFinal::create([
                'clientebanco' => $request->clientebanco,
                'fechabateria' => $fechabateria,
                'estado' => 'APROBADO',
                'clientebancoid' => $clientebancoid,
                'clientebanconombre' => $clientebanconombre,
                'document' => $archivo_name,
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioRegistro,
            ]);
    
        return redirect()->route('admin.informesfinales.resultadosmedicosclientesbancos')->with('info', 'Documento subido exitosamente.');
    } 
    public function consiliacionesclientesbanco(ClienteBanco $clientebanco, Request $request)
    {
        $sucursal = $clientebanco->sucursal;
        $clientebancoid = $clientebanco->id;
        $proveedores = Proveedor::orderBy('proveedor')->get(['id', 'proveedor', 'celular']);
        $aprobaciones = AprobacionInformeFinal::all();
        $fechas = Programacionsubcliente::pluck('fechabateria')->unique()->sort()->toArray();
        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
        $userRole = auth()->user()->getRoleNames()->first(); 
        $empresaUsuario = auth()->user()->empresa;
        
        $query = Programacionsubcliente::with(['documentacionsubclientebanco', 'requisitosclienteauditoriamedica', 'requisitosubcliente', 'bateriasubcliente', 'estadoprogramacionsubcliente', 'documentacionsubcliente', 'proveedorinformesfinales', 'informesfinales'])
            ->whereNotNull('clientebancoid');

        if ($request->has('buscarporfecha') && $request->buscarporfecha !== '') {
            $query->where('fechabateria', $request->buscarporfecha);
        }

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('clientebanco', function ($q) use ($request) {
                $q->where('clientenombre', 'LIKE', '%' . $request->buscarporcliente . '%');
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

            $consiliacioncompletada = Estadocotizacionsubcliente::withTrashed()
                ->where('clientebancoid', $items->first()->clientebancoid)
                ->where('detalle', 'CONSILIACION PAGADA')
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
            $consiliacion = $consiliacioncompletada ? $consiliacioncompletada->document : null;
            $consentimiento = $consentimientoinformado ? $consentimientoinformado->document : null;
            $informefinalclientebanco = $informefinal ? $informefinal->document : null;
            $usuarioregistro = $usuarioRegistro ? $usuarioRegistro->sucursal : null;
            $empresaregistro = $usuarioRegistro ? $usuarioRegistro->asociadonombre : null;
            $motivoabandono = $motivoabandonobateria ? $motivoabandonobateria->motivoabandono : null;
            $clientebancoid = $items->first()->clientebancoid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {
                $documentacion = $item->documentacionsubclientebanco->where('accion', $item->accionnombre)->first();
                $image = $item->documentacionsubclientebanco->where('accion', $item->accionnombre)->first();
                $image2 = $item->documentacionsubclientebanco->where('accion', $item->accionnombre)->first();
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
                'consiliacion' => $consiliacion,
            ];
        }
 
        $completosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['consiliacion']) {
                $count++;
            }
            return $count;
        }, 0);

        $resultadosCount = array_reduce($result, function ($count, $item) use ($usuarioAutenticado) {
            if ($item['estado'] === 'COMPLETO' && $item['declaracionmedica'] && $item['fichamedica'] && $item['consentimiento'] && !$item['informefinal']) {
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
            if (!$item['consiliacion']) {
                $count++;
            }
            return $count;
        }, 0);

        return view('admin.informesfinales.consiliacionesclientesbanco', compact('empresaUsuario','clientebancoid','documentosCount','resultadosCount','userRole','esProveedor','usuarioAutenticado','completosCount','incompletosCount','proveedores','result', 'clientebanco', 'fechas', 'aprobaciones'));
    }
    public function buscarconsiliacionclientebanco(ClienteBanco $clientebanco, Request $request)
    {
        return $this->consiliacionesclientesbanco($clientebanco, $request);
    }
    public function guardarconsiliacionclientebanco(StoreInformefinalRequest $request, ClienteBanco $clientebanco)
    {
        $clientebancoid = $request->input('clientebancoid');
        $clientebanconombre = $request->input('clientebanconombre');
        $fechabateria = $request->input('fechabateria');

        $archivo_name = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $carpetaCliente = public_path("/cotizacionesaprobadasbanco/{$clientebancoid}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }

            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
        }
        $usuarioId = auth()->user()->id;
        $usuarioRegistro = auth()->user()->name;
    
            Estadocotizacionsubcliente::create([
                'clientebanco' => $request->clientebanco,
                'fechabateria' => $fechabateria,
                'detalle' => 'CONSILIACION PAGADA',
                'clientebancoid' => $clientebancoid,
                'clientebanconombre' => $clientebanconombre,
                'document' => $archivo_name,
                'usuarioid' => $usuarioId,
                'usuarioregistro' => $usuarioRegistro,
            ]);
    
        return redirect()->route('admin.informesfinales.consiliacionesclientesbanco')->with('info', 'Documento subido exitosamente.');
    } 
    public function generarordenventaclientebanco(ClienteBanco $clientebanco, Request $request) 
    {
        $clientebanconombre = $clientebanco->nombrecompleto;
        // Obtener la última fechabateria del cliente
        $ultimaFechaBateria = Bateriasubcliente::where('clienteid', $clientebanco->id)
            ->orderBy('fechabateria', 'desc')
            ->first()->fechabateria;

        // Obtener las acciones asociadas a la última fechabateria
        $bateriasubclientes = Bateriasubcliente::where('clienteid', $clientebanco->id)
            ->where('fechabateria', $ultimaFechaBateria)
            ->get();

        // Calcular el total de los precios
        $total = $bateriasubclientes->sum('precio');

        // Generar el PDF con la información del cliente y las acciones
        $pdf = PDF::loadView('admin.informesfinales.ordenes.pdfordenventaclientebanco', compact('clientebanconombre', 'clientebanco', 'bateriasubclientes', 'total'));

        // Crear un nombre dinámico para el archivo PDF
        $pdfName = 'Cotización_' . $clientebanco->nombrecompleto . '.pdf';
        
        // Retornar la vista en lugar de descargar
        return $pdf->stream($pdfName); // Usamos stream() para mostrar el PDF en lugar de descargarlo
    }
    
    public function reservasmedicas(Cliente $cliente, ClienteAuditoria $clienteauditoria, Request $request)
    {
        $usuario = auth()->user();
        $nombreusuario = auth()->user()->name;
        $proveedores = Programacionsubcliente::select('proveedornombre')
                        ->distinct()
                        ->get();

        $tienefichamedica = Fichamedicasubcliente::where('clienteid', $cliente->id)->exists();
        $tienefichamedicaauditoria = Fichamedicasubcliente::where('clienteauditoriaid', $clienteauditoria->id)->exists();

        
        $query = Programacionsubcliente::with([
            'requisitosubcliente', 
            'bateriasubcliente', 
            'estadoprogramacionsubcliente', 
            'documentacionsubcliente', 
            'proveedorinformesfinales', 
            'informesfinales'
        ])->whereNotNull('clienteitaid');

        $query2 = Programacionsubcliente::with([
            'requisitosubcliente', 
            'bateriasubcliente', 
            'estadoprogramacionsubcliente', 
            'documentacionsubcliente', 
            'proveedorinformesfinales', 
            'informesfinales'
        ])->whereNotNull('clienteauditoriaid');
        
        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('clienteita', function ($q) use ($request) {
                $q->where('clienteitanombre', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }
        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query2->whereHas('clienteauditoria', function ($q) use ($request) {
                $q->where('clienteauditorianombre', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        if ($request->has('proveedor') && $request->proveedor !== '') {
            $query->where('proveedornombre', $request->proveedor);
        }
        $rolusuario = auth()->user()->getRoleNames()->first(); 
        $usuarioautenticado = auth()->user()->name;

        if ($rolusuario === 'MAESTRO' || $rolusuario === 'ADMINISTRADOR') {
            $reservasmedicas = $query->orderby('fechaasignada', 'desc')->get();
        } elseif ($rolusuario === 'PROVEEDOR') {
            $reservasmedicas = $query->where('proveedornombre', $usuarioautenticado)
                ->orderby('fechaasignada', 'desc')
                ->get();
        } else {
            $reservasmedicas = collect();
        }


        if ($request->has('proveedor') && $request->proveedor !== '') {
            $query2->where('proveedornombre', $request->proveedor);
        }
        if ($rolusuario === 'MAESTRO' || $rolusuario === 'ADMINISTRADOR') {
            $reservasmedicasauditorias = $query2->orderby('fechaasignada', 'desc')->get();
        } elseif ($rolusuario === 'PROVEEDOR') {
            $reservasmedicasauditorias = $query2->where('proveedornombre', $usuarioautenticado)
                ->orderby('fechaasignada', 'desc')
                ->get();
        } else {
            $reservasmedicasauditorias = collect();
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

            $reservasmedica->documentacionfirmadaDisponible = Documentacionsubcliente::where('clienteitaid', $reservasmedica->clienteitaid)
                ->where('fechabateria', $reservasmedica->fechabateria)
                ->where('accion', $reservasmedica->accionnombre)
                ->exists();

            $documentacion = Documentacionsubcliente::where('clienteitaid', $reservasmedica->clienteitaid)
                ->where('fechabateria', $reservasmedica->fechabateria)
                ->where('accion', $reservasmedica->accionnombre)
                ->first();

            $reservasmedica->fichamedicaita = Fichamedicasubcliente::where('clienteid', $reservasmedica->clienteitaid)
                ->where('detalle', 'FICHA MEDICA')
                ->exists();
            $fichaita = Fichamedicasubcliente::where('clienteid', $reservasmedica->clienteitaid)
                ->where('detalle', 'FICHA MEDICA')
                ->first();

            $ciasegurado = Requisitosubcliente::where('clienteitaid', $reservasmedica->clienteitaid)
                ->where('ciasegurado', '!=', 'PENDIENTE')
                ->latest()
                ->first();

            $reservasmedica->diagnosticomedicoita = Documentacionsubcliente::where('clienteitaid', $reservasmedica->clienteitaid)
                ->where('accion', 'DIAGNÓSTICO MÉDICO')
                ->exists();
            $diagnosticoita = Documentacionsubcliente::where('clienteitaid', $reservasmedica->clienteitaid)
                ->where('accion', 'DIAGNÓSTICO MÉDICO')
                ->first();

            $reservasmedica->documentacionDisponible = $documentacion ? $documentacion->document : null;
            $reservasmedica->documentacionfirmadaDisponible = $documentacion ? $documentacion->documentfirmado : null;
            $reservasmedica->documentacionworditaDisponible = $documentacion ? $documentacion->documentword : null;
            $reservasmedica->imagen1Disponible = $documentacion ? $documentacion->image : null;
            $reservasmedica->imagen2Disponible = $documentacion ? $documentacion->image2 : null;
            $reservasmedica->fechainforme = $documentacion ? $documentacion->created_at : null;
            $reservasmedica->fichamedicaita = $fichaita ? $fichaita->document : null;
            $reservasmedica->ciasegurado = $ciasegurado ? $ciasegurado->ciasegurado : null;
            $reservasmedica->diagnosticomedicoita = $diagnosticoita ? $diagnosticoita->document : null;

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
        
        $atencionpendienteauditoriaCount = 0;
        $informependienteauditoriaCount = 0;
        $informecompletoauditoriaCount = 0;

        foreach ($reservasmedicasauditorias as $reservasmedicaauditoria) {
            $reservasmedicaauditoria->informeDisponibleauditoria = Estadoprogramacionsubcliente::where('clienteauditoriaid', $reservasmedicaauditoria->clienteauditoriaid)
                ->where('fechabateria', $reservasmedicaauditoria->fechabateria)
                ->where('accionnombre', $reservasmedicaauditoria->accionnombre)
                ->exists();

            $reservasmedicaauditoria->documentacionDisponibleauditoria = Documentacionsubcliente::where('clienteauditoriaid', $reservasmedicaauditoria->clienteauditoriaid)
                ->where('fechabateria', $reservasmedicaauditoria->fechabateria)
                ->where('accion', $reservasmedicaauditoria->accionnombre)
                ->exists();

            $reservasmedicaauditoria->documentacionfirmadaauditoriaDisponible = Documentacionsubcliente::where('clienteauditoriaid', $reservasmedicaauditoria->clienteauditoriaid)
                ->where('fechabateria', $reservasmedicaauditoria->fechabateria)
                ->where('accion', $reservasmedicaauditoria->accionnombre)
                ->exists();

            $documentacionauditoria = Documentacionsubcliente::where('clienteauditoriaid', $reservasmedicaauditoria->clienteauditoriaid)
                ->where('fechabateria', $reservasmedicaauditoria->fechabateria)
                ->where('accion', $reservasmedicaauditoria->accionnombre)
                ->first();

            $reservasmedicaauditoria->fichamedicaauditoria = Fichamedicasubcliente::where('clienteauditoriaid', $reservasmedicaauditoria->clienteauditoriaid)
                ->where('detalle', 'FICHA MEDICA')
                ->exists();
            $fichaauditoria = Fichamedicasubcliente::where('clienteauditoriaid', $reservasmedicaauditoria->clienteauditoriaid)
                ->where('detalle', 'FICHA MEDICA')
                ->first();
            
            $reservasmedicaauditoria->diagnosticomedicoauditoria = Documentacionsubcliente::where('clienteauditoriaid', $reservasmedicaauditoria->clienteauditoriaid)
                ->where('accion', 'DIAGNÓSTICO MÉDICO')
                ->exists();
            $diagnosticoauditoria = Documentacionsubcliente::where('clienteauditoriaid', $reservasmedicaauditoria->clienteauditoriaid)
                ->where('accion', 'DIAGNÓSTICO MÉDICO')
                ->first();

            $ciasegurado = Requisitosclientesauditoria::where('clienteauditoriaid', $reservasmedicaauditoria->clienteauditoriaid)
            ->where('ciasegurado', '!=', 'PENDIENTE')
                ->first();

            $reservasmedicaauditoria->documentacionDisponibleauditoria = $documentacionauditoria ? $documentacionauditoria->document : null;
            $reservasmedicaauditoria->documentacionfirmadaauditoriaDisponible = $documentacionauditoria ? $documentacionauditoria->documentfirmado : null;
            $reservasmedicaauditoria->documentacionwordauditoriaDisponible = $documentacionauditoria ? $documentacionauditoria->documentword : null;
            $reservasmedicaauditoria->imagen1Disponibleauditoria = $documentacionauditoria ? $documentacionauditoria->image : null;
            $reservasmedicaauditoria->imagen2Disponibleauditoria = $documentacionauditoria ? $documentacionauditoria->image2 : null;
            $reservasmedicaauditoria->fechainformeauditoria = $documentacionauditoria ? $documentacionauditoria->created_at : null;
            $reservasmedicaauditoria->fichamedicaauditoria = $fichaauditoria ? $fichaauditoria->document : null;
            $reservasmedicaauditoria->diagnosticomedicoauditoria = $diagnosticoauditoria ? $diagnosticoauditoria->document : null;
            $reservasmedicaauditoria->ciasegurado = $ciasegurado ? $ciasegurado->ciasegurado : null;

            if (!$reservasmedicaauditoria->documentacionDisponibleauditoria && !$reservasmedicaauditoria->informeDisponibleauditoria) {
                $atencionpendienteauditoriaCount++;
            }
            if (!$reservasmedicaauditoria->documentacionDisponibleauditoria && $reservasmedicaauditoria->informeDisponibleauditoria) {
                $informependienteauditoriaCount++;
            }
            if ($reservasmedicaauditoria->documentacionDisponibleauditoria) {
                $informecompletoauditoriaCount++;
            }
        }

        return view('admin.informesfinales.reservasmedicas', compact('usuario','nombreusuario','tienefichamedicaauditoria','tienefichamedica','reservasmedicasauditorias','proveedores', 'rolusuario', 'reservasmedicas', 'cliente', 'atencionpendienteCount', 'informependienteCount', 'informecompletoCount', 'atencionpendienteauditoriaCount', 'informependienteauditoriaCount', 'informecompletoauditoriaCount'));
    }
    public function generarPDForden($clienteitaid, $fechabateria, $clienteitanombre, $proveedornombre)
    {
        $clienteitanombre = urldecode($clienteitanombre); // Decodificar el nombre recibido en la URL

        // Filtrar los registros por CLIENTEITAID y FECHABATERIA
        $registros = DB::table('programacionsubclientes as p')
        ->select('p.id', 'p.medicoderivante', 'p.accionnombre')
        ->leftJoin('estadoprogramacionsubclientes as d', 'p.id', '=', 'd.programacionid')
        ->where('p.clienteitaid', $clienteitaid)
        ->where('p.fechabateria', $fechabateria)
        ->whereNull('p.motivoreprogramacion')
        ->whereNull('p.motivoanulacion')
        ->where('p.proveedornombre', $proveedornombre)
        ->whereNull('d.programacionid') // Solo los que no tienen documentación
        ->get();


        if ($registros->isEmpty()) {
            return back()->with('error', 'No hay registros para los datos seleccionados.');
        }

        $pdf = PDF::loadView('admin.informesfinales.ordenes.ordenderivacion', compact('registros', 'clienteitaid', 'fechabateria', 'clienteitanombre', 'proveedornombre'));

        // Descargar el PDF
        return $pdf->download("Orden_de_{$clienteitanombre}.pdf");
    }
    public function generarPDFordenauditoria($clienteauditoriaid, $fechabateria, $clienteauditorianombre, $proveedornombre)
    {
        $clienteauditorianombre = urldecode($clienteauditorianombre); // Decodificar el nombre recibido en la URL

        // Filtrar los registros por CLIENTEITAID y FECHABATERIA
        $registros = DB::table('programacionsubclientes as p')
        ->select('p.id', 'p.medicoderivante', 'p.accionnombre')
        ->leftJoin('documentacionsubclientes as d', 'p.id', '=', 'd.programacionid')
        ->where('p.clienteauditoriaid', $clienteauditoriaid)
        ->where('p.fechabateria', $fechabateria)
        ->whereNull('p.motivoreprogramacion')
        ->whereNull('p.motivoanulacion')
        ->where('p.proveedornombre', $proveedornombre)
        ->whereNull('d.programacionid') // Solo los que no tienen documentación
        ->get();


        if ($registros->isEmpty()) {
            return back()->with('error', 'No hay registros para los datos seleccionados.');
        }

        $pdf = PDF::loadView('admin.informesfinales.ordenes.ordenderivacionauditoria', compact('registros', 'clienteauditoriaid', 'fechabateria', 'clienteauditorianombre', 'proveedornombre'));

        // Descargar el PDF
        return $pdf->download("Orden_de_{$clienteauditorianombre}.pdf");
    }
    public function buscarclientereservamedica(Cliente $cliente, ClienteAuditoria $clienteauditoria, Request $request)
        {
            return $this->reservasmedicas($cliente, $clienteauditoria, $request);
        }
    public function guardardocumentacionclienteita(StoreDocumentacionsubclienteRequest $request, Cliente $cliente)
    {
        $archivo_name = null;
    
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            
            // Crear carpeta del cliente si no existe
            $carpetaCliente = public_path("/documentacionclientesita/{$cliente->id}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
    
            // Subir archivo original
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
    
            // Agregar firma y sello al PDF
            $archivoFirmado = $this->agregarFirmaYSelloPDF($carpetaCliente, $archivo_name, $request);
        }
    
        // Continuar con el resto de la lógica
        return redirect()->route('admin.informesfinales.guardardocumentacionclienteita', $request->cliente)
                         ->with('info', 'El documento se subió con éxito y se agregó la firma y el sello.');
    }
    public function procesarInforme(Request $request, Cliente $cliente) 
    {
        $request->validate([
            'archivo' => 'required|mimes:pdf|max:15120',
        ]);

        $id = $request->input('clienteitaid');
        $carpetaCliente = public_path("documentacionclientesita/{$id}");
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }

        $usuario = auth()->user()->name;
        $sucursal = auth()->user()->sucursal;
        $fechaActual = Carbon::now()->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $accion = $request->input('accion');
        
        $archivo_name = null;
            if ($request->hasFile('archivo2')) {
                $file = $request->file('archivo2');
                
                $carpetaCliente = public_path("/documentacionclientesita/{$id}");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);}
                $archivo_name = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name);
            }

        $firmaAnteriorPath = '';
        $selloAnteriorPath = '';
        $firmaUltimaPath = '';
        $selloUltimaPath = '';
        $firmaAnteriorCoords = [];
        $selloAnteriorCoords = [];
        $firmaUltimaCoords = [];
        $selloUltimaCoords = [];
        $texto1 = $texto2 = $texto3 = "";
        switch ($usuario) {
            case 'CARLOS ALEJANDRO GUARACHI SANDOVAL':
                if (Str::startsWith($accion, 'ESPIROMETRIA') || Str::startsWith($accion, 'ESPIROMETRÍA') || Str::startsWith($accion, 'AUDIOMETRÍA') || Str::startsWith($accion, 'AUDIOMETRIA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                    $selloAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 20, 'height' => 37];
                    $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                    $selloUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO3 ORIGINAL MARIA RENEE.png');
                    $firmaUltimaCoords = ['x' => 31, 'y' => 240, 'width' => 25, 'height' => 30];
                    $selloUltimaCoords = ['x' => 25, 'y' => 252, 'width' => 40, 'height' => 13];
                    $texto1 = null;
                    $texto2 = null;
                    $texto3 = null;

                } else {
                    $firmaAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                    $selloAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 20, 'height' => 37];
                    $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL ULTIMA MARIA RENEE.png');
                    $selloUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE.png');
                    $firmaUltimaCoords = ['x' => 90, 'y' => 186, 'width' => 35, 'height' => 40];
                    $selloUltimaCoords = ['x' => 85, 'y' => 199, 'width' => 40, 'height' => 40];
                    $texto1 = "DRA. MARIA RENEÉ AGUIRRE VASQUEZ";
                    $texto2 = "MEDICINA LABORAL Y SALUD OCUPACIONAL";
                    $texto3 = "M.P.A-7676725 CM:11019";
                }
            break;

            case 'AGUIRRE VASQUEZ MARIA RENEE':
                if (Str::startsWith($accion, 'ESPIROMETRIA') || Str::startsWith($accion, 'ESPIROMETRÍA') || Str::startsWith($accion, 'AUDIOMETRÍA') || Str::startsWith($accion, 'AUDIOMETRIA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                    $selloAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 20, 'height' => 37];
                    $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                    $selloUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO3 ORIGINAL MARIA RENEE.png');
                    $firmaUltimaCoords = ['x' => 31, 'y' => 240, 'width' => 25, 'height' => 30];
                    $selloUltimaCoords = ['x' => 25, 'y' => 252, 'width' => 40, 'height' => 13];
                    $texto1 = null;
                    $texto2 = null;
                    $texto3 = null;

                } else {
                    $firmaAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                    $selloAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 20, 'height' => 37];
                    $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL ULTIMA MARIA RENEE.png');
                    $selloUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE.png');
                    $firmaUltimaCoords = ['x' => 90, 'y' => 186, 'width' => 35, 'height' => 40];
                    $selloUltimaCoords = ['x' => 85, 'y' => 199, 'width' => 40, 'height' => 40];
                    $texto1 = "DRA. MARIA RENEÉ AGUIRRE VASQUEZ";
                    $texto2 = "MEDICINA LABORAL Y SALUD OCUPACIONAL";
                    $texto3 = "M.P.A-7676725 CM:11019";
                }
            break;

            case 'MARICELA COLQUE SANDOVAL':
                $firmaAnteriorPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/FIRMA ORIGINAL MARICELA COLQUE VERTICAL.png');
                $selloAnteriorPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/SELLO ORIGINAL MARICELA COLQUE VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 183, 'y' => 205, 'width' => 20, 'height' => 25];
                $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/FIRMA ORIGINAL MARICELA COLQUE.png');
                $selloUltimaPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/SELLO ORIGINAL MARICELA COLQUE.png');
                $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                $texto1 = "LIC. MARICELA COLQUE SANDOVAL";
                $texto2 = "TRABAJADORA SOCIAL";
                $texto3 = "MAT. PROF. N° 0496";
            break;

            case 'MONICA MACOÑO FLORES':
                $firmaAnteriorPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/FIRMA ORIGINAL MONICA MACOÑO VERTICAL.png');
                $selloAnteriorPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/SELLO ORIGINAL MONICA MAÑOCO VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 185, 'y' => 205, 'width' => 20, 'height' => 25];
                $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/FIRMA ORIGINAL MONICA MACOÑO.png');
                $selloUltimaPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/SELLO ORIGINAL MONICA MAÑOCO.png');
                $firmaUltimaCoords = ['x' => 90, 'y' => 192, 'width' => 30, 'height' => 35];
                $selloUltimaCoords = ['x' => 82, 'y' => 200, 'width' => 40, 'height' => 40];
                $texto1 = "LIC. MONICA MACOÑO FLORES";
                $texto2 = "FISIOTERAPEUTA KINESIÓLOGA";
                $texto3 = "MAT.: 8237722";
            break;

            case 'KUCHARSKY RUIZ PATRICIA CELIA':
                $firmaAnteriorPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/FIRMA ORIGINAL PATRICIA VERTICAL.png');
                $selloAnteriorPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/SELLO ORIGINAL PATRICIA VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 188, 'y' => 205, 'width' => 15, 'height' => 25];
                $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/FIRMA ORIGINAL PATRICIA.png');
                $selloUltimaPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/SELLO ORIGINAL PATRICIA.png');
                $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                $texto1 = "LIC. PATRICIA KUCHARSKY RUIZ";
                $texto2 = "PSICÓLOGA";
                $texto3 = "MAT. PROF. K-2";
            break;

            case 'INSTITUTO DE CARDIOLOGIA INTERVENCIONISTA CARDIOVIDA S.R.L.':
                if (Str::startsWith($accion, 'ELECTROCARDIOGRAMA') || Str::startsWith($accion, 'ERGOMETRIA') || Str::startsWith($accion, 'CARDIOLOGIA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/CARDIOVIDA/FIRMA ORIGINAL CARDIOVIDA VERTICAL.png');
                    $selloAnteriorPath = public_path('/glfirmasello/CARDIOVIDA/SELLO ORIGINAL CARDIOVIDA VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 185, 'y' => 205, 'width' => 15, 'height' => 25];
                    $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/CARDIOVIDA/FIRMA ORIGINAL CARDIOVIDA.png');
                    $selloUltimaPath = public_path('/glfirmasello/CARDIOVIDA/SELLO ORIGINAL CARDIOVIDA.png');
                    $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                    $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                    $texto1 = "DR. IGNACIO VACA VELARDE";
                    $texto2 = "CARDIÓLOGO - HEMODINAMISTA";
                    $texto3 = "V-4762072 M22-8858";

                } elseif (Str::startsWith($accion, 'ECOCARDIOGRAMA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/CARDIOVIDA ECO/FIRMA ORIGINAL CARDIOVIDA ECO VERTICAL.png');
                    $selloAnteriorPath = public_path('/glfirmasello/CARDIOVIDA ECO/SELLO ORIGINAL CARDIOVIDA ECO VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 15, 'height' => 25];
                    $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/CARDIOVIDA ECO/FIRMA ORIGINAL CARDIOVIDA ECO.png');
                    $selloUltimaPath = public_path('/glfirmasello/CARDIOVIDA ECO/SELLO ORIGINAL CARDIOVIDA ECO.png');
                    $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                    $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                    $texto1 = "DRA. ANA PAOLA ESPINOZA R.";
                    $texto2 = "E-6289025";
                    $texto3 = "";
                }
            break;

            default:
                $firmaAnteriorPath = public_path('/glfirmasello/default/firma.png');
                $selloAnteriorPath = public_path('/glfirmasello/default/sello.png');
                $firmaAnteriorCoords = ['x' => 160, 'y' => 230, 'width' => 1, 'height' => 1];
                $selloAnteriorCoords = ['x' => 170, 'y' => 230, 'width' => 1, 'height' => 1];

                $firmaUltimaPath = public_path('/glfirmasello/default/firma.png');
                $selloUltimaPath = public_path('/glfirmasello/default/sello.png');
                $firmaUltimaCoords = ['x' => 85, 'y' => 215, 'width' => 1, 'height' => 1];
                $selloUltimaCoords = ['x' => 85, 'y' => 220, 'width' => 1, 'height' => 1];
                break;
        }

        /* $textoFecha = "{$sucursal}, {$fechaActual}"; */

        /* if ($usuario === 'INSTITUTO DE CARDIOLOGIA INTERVENCIONISTA CARDIOVIDA S.R.L.') {
            $textoFecha = null;
        } else {
            $textoFecha = "{$sucursal}, {$fechaActual}";
        } */

        if ( 
            $usuario === 'AGUIRRE VASQUEZ MARIA RENEE' && 
            (Str::startsWith($accion, 'ESPIROMETRIA') || Str::startsWith($accion, 'ESPIROMETRÍA') || Str::startsWith($accion, 'AUDIOMETRÍA') || Str::startsWith($accion, 'AUDIOMETRIA'))
        ) {
            $textoFecha = null;
            
        } elseif ($usuario === 'INSTITUTO DE CARDIOLOGIA INTERVENCIONISTA CARDIOVIDA S.R.L.') {
            $textoFecha = null;
            
        } else {
            $textoFecha = "{$sucursal}, {$fechaActual}";
        }

        if (!file_exists($firmaAnteriorPath) || !file_exists($selloAnteriorPath) ||
            !file_exists($firmaUltimaPath) || !file_exists($selloUltimaPath)) {
            return back()->withErrors(['error' => 'Alguna firma o sello no fue encontrada para el usuario autenticado.']);
        }

        $uploadedPdfPath = $request->file('archivo')->getPathname();
        $nombreDocumento = $request->input('nombre_documento', uniqid());
        $outputFileName = "{$nombreDocumento}.pdf";
        $outputPath = $carpetaCliente . "/$outputFileName";

        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($uploadedPdfPath);

        /* INFORME CON PIE Y FIRMA Y SELLO */
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {  
            $templateId = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($templateId, 0, 0);
        
            try {
                $isPenultima = ($pageNo == $pageCount - 1);
                $isUltima = ($pageNo == $pageCount);
                $isAntesDePenultima = ($pageNo < $pageCount - 1);
                $isAntesDeUltima = ($pageNo < $pageCount);
        
                if (strtoupper($usuario) == 'MONICA MACOÑO FLORES') {
                    if ($isAntesDePenultima) {
                        $pdf->Image($firmaAnteriorPath, $firmaAnteriorCoords['x'], $firmaAnteriorCoords['y'], $firmaAnteriorCoords['width'], $firmaAnteriorCoords['height']);
                        $pdf->Image($selloAnteriorPath, $selloAnteriorCoords['x'], $selloAnteriorCoords['y'], $selloAnteriorCoords['width'], $selloAnteriorCoords['height']);
                    } elseif ($isPenultima) {
                        $pdf->Image($firmaUltimaPath, $firmaUltimaCoords['x'], $firmaUltimaCoords['y'], $firmaUltimaCoords['width'], $firmaUltimaCoords['height']);
                        $pdf->Image($selloUltimaPath, $selloUltimaCoords['x'], $selloUltimaCoords['y'], $selloUltimaCoords['width'], $selloUltimaCoords['height']);

                        $xSello = $selloUltimaCoords['x'];
                        $ySello = $selloUltimaCoords['y'];
                        $anchoSello = $selloUltimaCoords['width'];
                        $altoSello = $selloUltimaCoords['height'];
                        $lineHeight = 5;
                        $textos = [$texto1, $texto2, $texto3, $textoFecha];
                        $pdf->SetFont('Helvetica', '', 10);
                        $pdf->SetTextColor(0, 0, 0);
                        $ySelloAjustado = $ySello - 15;
        
                        foreach ($textos as $index => $lineaTexto) {
                            $anchoTexto = $pdf->GetStringWidth($lineaTexto);
                            $xTextoCentrado = $xSello + ($anchoSello / 2) - ($anchoTexto / 2);
                            $yTexto = $ySelloAjustado + $altoSello + ($lineHeight * $index) + 2;
        
                            $pdf->SetXY($xTextoCentrado, $yTexto);
                            $pdf->SetFont('Helvetica', ($lineaTexto !== $textoFecha ? 'BU' : ''), 10);
                            $pdf->Cell($anchoTexto, $lineHeight, utf8_decode($lineaTexto), 0, 1, 'C');
                        }
                    }
                } else {
                    if ($isAntesDeUltima) {
                        $pdf->Image($firmaAnteriorPath, $firmaAnteriorCoords['x'], $firmaAnteriorCoords['y'], $firmaAnteriorCoords['width'], $firmaAnteriorCoords['height']);
                        $pdf->Image($selloAnteriorPath, $selloAnteriorCoords['x'], $selloAnteriorCoords['y'], $selloAnteriorCoords['width'], $selloAnteriorCoords['height']);
                    } elseif ($isUltima) {
                        $pdf->Image($firmaUltimaPath, $firmaUltimaCoords['x'], $firmaUltimaCoords['y'], $firmaUltimaCoords['width'], $firmaUltimaCoords['height']);
                        $pdf->Image($selloUltimaPath, $selloUltimaCoords['x'], $selloUltimaCoords['y'], $selloUltimaCoords['width'], $selloUltimaCoords['height']);
        
                        $xSello = $selloUltimaCoords['x'];
                        $ySello = $selloUltimaCoords['y'];
                        $anchoSello = $selloUltimaCoords['width'];
                        $altoSello = $selloUltimaCoords['height'];
                        $lineHeight = 5;
                        $textos = [$texto1, $texto2, $texto3, $textoFecha];
                        $pdf->SetFont('Helvetica', '', 10);
                        $pdf->SetTextColor(0, 0, 0);
                        $ySelloAjustado = $ySello - 15;
        
                        foreach ($textos as $index => $lineaTexto) {
                            $anchoTexto = $pdf->GetStringWidth($lineaTexto);
                            $xTextoCentrado = $xSello + ($anchoSello / 2) - ($anchoTexto / 2);
                            $yTexto = $ySelloAjustado + $altoSello + ($lineHeight * $index) + 2;
        
                            $pdf->SetXY($xTextoCentrado, $yTexto);
                            $pdf->SetFont('Helvetica', ($lineaTexto !== $textoFecha ? 'BU' : ''), 10);
                            $pdf->Cell($anchoTexto, $lineHeight, utf8_decode($lineaTexto), 0, 1, 'C');
                        }
                    }
                }
            } catch (Exception $e) {
                return back()->withErrors(['error' => 'Error al insertar imágenes: ' . $e->getMessage()]);
            }
        }

        try {
            $pdf->Output($outputPath, 'F');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar el archivo PDF firmado: ' . $e->getMessage()]);
        }

        if (!file_exists($outputPath)) {
            return back()->withErrors(['error' => 'No se pudo guardar el archivo PDF firmado.']);
        }

        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($uploadedPdfPath);

        if ($pageCount < 1) {
            return back()->withErrors(['error' => 'El archivo PDF no contiene páginas.']);
        }

        /* INFORME SOLO CON PIE DE FIRMA Y SELLO */
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($templateId, 0, 0);
        
            try {
                $mostrarSoloTexto = false;
                $omitirTodo = false;
        
                if ($usuario == 'MONICA MACOÑO FLORES') {
                    if ($pageNo == $pageCount) {

                        $omitirTodo = true;
                    } elseif ($pageNo == $pageCount - 1) {

                        $mostrarSoloTexto = true;
                    } else {

                    }
                } else {
                    if ($pageNo == $pageCount) {

                        $mostrarSoloTexto = true;
                    } else {
            
                    }
                }

                if ($omitirTodo) {
                    continue;
                }

                if ($mostrarSoloTexto) {
                    $xSello = $selloUltimaCoords['x'];
                    $ySello = $selloUltimaCoords['y'];
                    $anchoSello = $selloUltimaCoords['width'];
                    $altoSello = $selloUltimaCoords['height'];
                    $lineHeight = 5;
                    $textos = [$texto1, $texto2, $texto3, $textoFecha];
                    $pdf->SetFont('Helvetica', '', 10);
                    $pdf->SetTextColor(0, 0, 0);
                    $ySelloAjustado = $ySello - 15;
        
                    foreach ($textos as $index => $lineaTexto) {
                        $anchoTexto = $pdf->GetStringWidth($lineaTexto);
                        $xTextoCentrado = $xSello + ($anchoSello / 2) - ($anchoTexto / 2);
                        $yTexto = $ySelloAjustado + $altoSello + ($lineHeight * $index) + 2;
        
                        $pdf->SetXY($xTextoCentrado, $yTexto);
                        if ($lineaTexto !== $textoFecha) {
                            $pdf->SetFont('Helvetica', 'BU', 10);
                        } else {
                            $pdf->SetFont('Helvetica', '', 10);
                        }
                        $pdf->Cell($anchoTexto, $lineHeight, utf8_decode($lineaTexto), 0, 1, 'C');
                    }
                }
        
            } catch (Exception $e) {
                return back()->withErrors(['error' => 'Error al insertar imágenes: ' . $e->getMessage()]);
            }
        }
        
        $outputFileName2 = "{$nombreDocumento}_procesado.pdf";
        $outputPath = $carpetaCliente . "/$outputFileName2";

        try {
            $pdf->Output($outputPath, 'F');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar el archivo PDF procesado: ' . $e->getMessage()]);
        }

        $archivo_name = $outputFileName2;

        if (!file_exists($outputPath)) {
            return back()->withErrors(['error' => 'No se pudo guardar el archivo PDF procesado.']);
        }

        $archivo3_name = null;
        if ($request->hasFile('archivo3')) {
            $file = $request->file('archivo3');
            $archivo3_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo3_name);
        }
        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name);
        }
        $image_name2 = null;
        if ($request->hasFile('picture2')) {
            $file = $request->file('picture2');
            $image_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name2);
        }
        $accionNombre = Programacionsubcliente::where('id', $request->accion)->value('accionnombre');
        $accion = $request->input('accion');
        $nombrecliente = $request->input('clienteitanombre');
        $idcliente = $request->input('clienteitaid');
        $programacionid = $request->input('programacionid');
        $usuarioid = $request->input('usuarioid');
        $usuarioregistro = $request->input('usuarioregistro');
        $idsubproc = Programacionsubcliente::where('id', $programacionid)->value('idsubproc');

        $documentacioncliente = Documentacionsubcliente::create(
            $request->except('accion') + [
                'documentfirmado' => $outputFileName,
                'document' => $outputFileName2,
                'accion' => $accion,
                'clienteitanombre' => $nombrecliente,
                'clienteitaid' => $idcliente,
                'programacionid' => $programacionid,
                'image' => $image_name,
                'image2' => $image_name2,
                'documentword' => $archivo3_name,
                'usuarioid' => $usuarioid,
                'usuarioregistro' => $usuarioregistro,
                'idsubproc' => $idsubproc,
                'clienteid' => $idcliente,
                'clientenombre' => $nombrecliente,
                'tipocliente' => 'ITA'
            ]
        );

        // 🔄 ACTUALIZAR SUBTRAMITE IGUAL QUE EN EL CÓDIGO ANTERIOR
        if ($idsubproc) {
            SubTramite::where('id', $idsubproc)
                ->update([
                    'informeprogramacion' => $outputFileName2,
                    'solicitante1' => $image_name,
                    'solicitante2' => $image_name2
                ]);
        }

        if ($documentacioncliente) {
            $usuarioAutenticado = auth()->user();

            if ($usuarioAutenticado->hasRole(['PROVEEDOR', 'PROVEEDOR IF'])) {
                $sucursalCliente = Cliente::where('id', $idcliente)->value('sucursal');

                $usuariosNotificar = User::whereHas('roles', function ($q) {
                        $q->whereIn('name', ['MAESTRO','ADMINISTRADOR','OPERATIVO']);
                    })
                    ->where('sucursal', $sucursalCliente)
                    ->get();

                foreach ($usuariosNotificar as $usuarioDestino) {
                    $usuarioDestino->notify(new InformeProveedorNotification($documentacioncliente));
                }
            }
        }

        return back()->with('info', 'El archivo PDF firmado y sellado ha sido guardado correctamente.');
    }
    public function procesarInformeMultiple(Request $request, Cliente $cliente) 
    {
        $request->validate([
            'archivo' => 'required|mimes:pdf|max:15120',
        ]);

        $id = $request->input('clienteitaid');
        $carpetaCliente = public_path("documentacionclientesita/{$id}");
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }

        $usuario = auth()->user()->name;
        $sucursal = auth()->user()->sucursal;
        $fechaActual = Carbon::now()->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $accion = $request->input('accion');
        
        $archivo_name = null;
            if ($request->hasFile('archivo2')) {
                $file = $request->file('archivo2');
                
                $carpetaCliente = public_path("/documentacionclientesita/{$id}");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);}
                $archivo_name = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name);
            }

        $firmaAnteriorPath = '';
        $selloAnteriorPath = '';
        $firmaUltimaPath = '';
        $selloUltimaPath = '';
        $firmaAnteriorCoords = [];
        $selloAnteriorCoords = [];
        $firmaUltimaCoords = [];
        $selloUltimaCoords = [];
        $texto1 = $texto2 = $texto3 = "";
        switch ($usuario) {
            case 'CARLOS ALEJANDRO GUARACHI SANDOVAL':
                $firmaAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                $selloAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO ORIGINAL MARIA RENEE VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 186, 'y' => 205, 'width' => 20, 'height' => 37];
                $selloAnteriorCoords = ['x' => 175, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL ULTIMA MARIA RENEE.png');
                $selloUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO ORIGINAL MARIA RENEE.png');
                $firmaUltimaCoords = ['x' => 90, 'y' => 190, 'width' => 35, 'height' => 40];
                $selloUltimaCoords = ['x' => 85, 'y' => 199, 'width' => 40, 'height' => 40];
                $texto1 = "DRA. ANA PAOLA ESPINOZA R.";
                $texto2 = "E-6289025";
                $texto3 = "";
            break;

            case 'AGUIRRE VASQUEZ MARIA RENEE':
                $firmaAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                $selloAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 20, 'height' => 37];
                $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL ULTIMA MARIA RENEE.png');
                $selloUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE.png');
                $firmaUltimaCoords = ['x' => 90, 'y' => 186, 'width' => 35, 'height' => 40];
                $selloUltimaCoords = ['x' => 85, 'y' => 199, 'width' => 40, 'height' => 40];
                $texto1 = "DRA. MARIA RENEÉ AGUIRRE VASQUEZ";
                $texto2 = "MEDICINA LABORAL Y SALUD OCUPACIONAL";
                $texto3 = "M.P.A-7676725 CM:11019";
            break;

            case 'MARICELA COLQUE SANDOVAL':
                $firmaAnteriorPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/FIRMA ORIGINAL MARICELA COLQUE VERTICAL.png');
                $selloAnteriorPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/SELLO ORIGINAL MARICELA COLQUE VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 183, 'y' => 205, 'width' => 20, 'height' => 25];
                $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/FIRMA ORIGINAL MARICELA COLQUE.png');
                $selloUltimaPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/SELLO ORIGINAL MARICELA COLQUE.png');
                $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                $texto1 = "LIC. MARICELA COLQUE SANDOVAL";
                $texto2 = "TRABAJADORA SOCIAL";
                $texto3 = "MAT. PROF. N° 0496";
            break;

            case 'MONICA MACOÑO FLORES':
                $firmaAnteriorPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/FIRMA ORIGINAL MONICA MACOÑO VERTICAL.png');
                $selloAnteriorPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/SELLO ORIGINAL MONICA MAÑOCO VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 185, 'y' => 205, 'width' => 20, 'height' => 25];
                $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/FIRMA ORIGINAL MONICA MACOÑO.png');
                $selloUltimaPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/SELLO ORIGINAL MONICA MAÑOCO.png');
                $firmaUltimaCoords = ['x' => 90, 'y' => 132, 'width' => 30, 'height' => 35];
                $selloUltimaCoords = ['x' => 82, 'y' => 140, 'width' => 40, 'height' => 40];
                $texto1 = "LIC. MONICA MACOÑO FLORES";
                $texto2 = "FISIOTERAPEUTA KINESIÓLOGA";
                $texto3 = "MAT.: 8237722";
            break;

            case 'KUCHARSKY RUIZ PATRICIA CELIA':
                $firmaAnteriorPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/FIRMA ORIGINAL PATRICIA VERTICAL.png');
                $selloAnteriorPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/SELLO ORIGINAL PATRICIA VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 188, 'y' => 205, 'width' => 15, 'height' => 25];
                $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/FIRMA ORIGINAL PATRICIA.png');
                $selloUltimaPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/SELLO ORIGINAL PATRICIA.png');
                $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                $texto1 = "LIC. PATRICIA KUCHARSKY RUIZ";
                $texto2 = "PSICÓLOGA";
                $texto3 = "MAT. PROF. K-2";
            break;

            /* case 'CARDIOVIDA':
                if (Str::startsWith($accion, 'ELECTROCARDIOGRAMA') || Str::startsWith($accion, 'ERGOMETRIA') || Str::startsWith($accion, 'CARDIOLOGIA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/CARDIOVIDA/FIRMA ORIGINAL CARDIOVIDA VERTICAL.png');
                    $selloAnteriorPath = public_path('/glfirmasello/CARDIOVIDA/SELLO ORIGINAL CARDIOVIDA VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 185, 'y' => 205, 'width' => 15, 'height' => 25];
                    $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/CARDIOVIDA/FIRMA ORIGINAL CARDIOVIDA.png');
                    $selloUltimaPath = public_path('/glfirmasello/CARDIOVIDA/SELLO ORIGINAL CARDIOVIDA.png');
                    $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                    $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                    $texto1 = "DR. IGNACIO VACA VELARDE";
                    $texto2 = "CARDIÓLOGO - HEMODINAMISTA";
                    $texto3 = "V-4762072 M22-8858";

                } elseif (Str::startsWith($accion, 'ECOCARDIOGRAMA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/CARDIOVIDA ECO/FIRMA ORIGINAL CARDIOVIDA ECO VERTICAL.png');
                    $selloAnteriorPath = public_path('/glfirmasello/CARDIOVIDA ECO/SELLO ORIGINAL CARDIOVIDA ECO VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 15, 'height' => 25];
                    $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/CARDIOVIDA ECO/FIRMA ORIGINAL CARDIOVIDA ECO.png');
                    $selloUltimaPath = public_path('/glfirmasello/CARDIOVIDA ECO/SELLO ORIGINAL CARDIOVIDA ECO.png');
                    $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                    $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                    $texto1 = "DRA. ANA PAOLA ESPINOZA R.";
                    $texto2 = "E-6289025";
                    $texto3 = "";
                }
            break; */

            case 'INSTITUTO DE CARDIOLOGIA INTERVENCIONISTA CARDIOVIDA S.R.L.':
                if (Str::startsWith($accion, 'ELECTROCARDIOGRAMA') || Str::startsWith($accion, 'ERGOMETRIA') || Str::startsWith($accion, 'CARDIOLOGIA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/CARDIOVIDA/FIRMA ORIGINAL CARDIOVIDA VERTICAL.png');
                    $selloAnteriorPath = public_path('/glfirmasello/CARDIOVIDA/SELLO ORIGINAL CARDIOVIDA VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 185, 'y' => 205, 'width' => 15, 'height' => 25];
                    $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/CARDIOVIDA/FIRMA ORIGINAL CARDIOVIDA.png');
                    $selloUltimaPath = public_path('/glfirmasello/CARDIOVIDA/SELLO ORIGINAL CARDIOVIDA.png');
                    $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                    $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                    $texto1 = "DR. IGNACIO VACA VELARDE";
                    $texto2 = "CARDIÓLOGO - HEMODINAMISTA";
                    $texto3 = "V-4762072 M22-8858";

                } elseif (Str::startsWith($accion, 'ECOCARDIOGRAMA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/CARDIOVIDA ECO/FIRMA ORIGINAL CARDIOVIDA ECO VERTICAL.png');
                    $selloAnteriorPath = public_path('/glfirmasello/CARDIOVIDA ECO/SELLO ORIGINAL CARDIOVIDA ECO VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 15, 'height' => 25];
                    $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/CARDIOVIDA ECO/FIRMA ORIGINAL CARDIOVIDA ECO.png');
                    $selloUltimaPath = public_path('/glfirmasello/CARDIOVIDA ECO/SELLO ORIGINAL CARDIOVIDA ECO.png');
                    $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                    $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                    $texto1 = "DRA. ANA PAOLA ESPINOZA R.";
                    $texto2 = "E-6289025";
                    $texto3 = "";
                }
            break;

            default:
                $firmaAnteriorPath = public_path('/glfirmasello/default/firma.png');
                $selloAnteriorPath = public_path('/glfirmasello/default/sello.png');
                $firmaAnteriorCoords = ['x' => 160, 'y' => 230, 'width' => 1, 'height' => 1];
                $selloAnteriorCoords = ['x' => 170, 'y' => 230, 'width' => 1, 'height' => 1];

                $firmaUltimaPath = public_path('/glfirmasello/default/firma.png');
                $selloUltimaPath = public_path('/glfirmasello/default/sello.png');
                $firmaUltimaCoords = ['x' => 85, 'y' => 215, 'width' => 1, 'height' => 1];
                $selloUltimaCoords = ['x' => 85, 'y' => 220, 'width' => 1, 'height' => 1];
                break;
        }

        /* $textoFecha = "{$sucursal}, {$fechaActual}"; */

        if ($usuario === 'INSTITUTO DE CARDIOLOGIA INTERVENCIONISTA CARDIOVIDA S.R.L.') {
            $textoFecha = null;
        } else {
            $textoFecha = "{$sucursal}, {$fechaActual}";
        }

        if (!file_exists($firmaAnteriorPath) || !file_exists($selloAnteriorPath) ||
            !file_exists($firmaUltimaPath) || !file_exists($selloUltimaPath)) {
            return back()->withErrors(['error' => 'Alguna firma o sello no fue encontrada para el usuario autenticado.']);
        }

        $uploadedPdfPath = $request->file('archivo')->getPathname();
        $nombreDocumento = $request->input('nombre_documento', uniqid());
        $outputFileName = "{$nombreDocumento}.pdf";
        $outputPath = $carpetaCliente . "/$outputFileName";

        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($uploadedPdfPath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($templateId, 0, 0);

            try {
                if ($pageNo == $pageCount) {
                    if (basename($firmaUltimaPath) === 'firma.png' && basename($selloUltimaPath) === 'sello.png') {
                        continue;
                    }
                    $pdf->Image($firmaUltimaPath, $firmaUltimaCoords['x'], $firmaUltimaCoords['y'], $firmaUltimaCoords['width'], $firmaUltimaCoords['height']);
                    $pdf->Image($selloUltimaPath, $selloUltimaCoords['x'], $selloUltimaCoords['y'], $selloUltimaCoords['width'], $selloUltimaCoords['height']);

                    $xSello = $selloUltimaCoords['x'];
                    $ySello = $selloUltimaCoords['y'];
                    $anchoSello = $selloUltimaCoords['width'];
                    $altoSello = $selloUltimaCoords['height'];

                    $lineHeight = 5;

                    $textos = [$texto1, $texto2, $texto3, $textoFecha];
                    $pdf->SetFont('Helvetica', '', 10);
                    $pdf->SetTextColor(0, 0, 0);

                    // Ajustar la distancia entre el grupo de textos y el sello
                    $ySelloAjustado = $ySello - 15; // Ajusta este valor para acercar o alejar todo el grupo de textos

                    // Calcular el centro horizontal tomando en cuenta el sello
                    foreach ($textos as $index => $lineaTexto) {
                        $anchoTexto = $pdf->GetStringWidth($lineaTexto); // Calcular el ancho del texto
                        $xTextoCentrado = $xSello + ($anchoSello / 2) - ($anchoTexto / 2); // Centrar el texto debajo del sello

                        // Ajustar la posición vertical debajo del sello
                        $yTexto = $ySelloAjustado + $altoSello + ($lineHeight * $index) + 2; // Mueve todo el grupo más cerca del sello

                        $pdf->SetXY($xTextoCentrado, $yTexto);

                        // Aplicar negrita y subrayado, excepto para el texto de la fecha
                        if ($lineaTexto !== $textoFecha) {
                            $pdf->SetFont('Helvetica', 'BU', 10); // Negrita y subrayado
                        } else {
                            $pdf->SetFont('Helvetica', '', 10); // Solo normal para la fecha
                        }

                        $pdf->Cell($anchoTexto, $lineHeight, utf8_decode($lineaTexto), 0, 1, 'C');
                    }
                } else {
                    // Páginas anteriores
                    $pdf->Image($firmaAnteriorPath, $firmaAnteriorCoords['x'], $firmaAnteriorCoords['y'], $firmaAnteriorCoords['width'], $firmaAnteriorCoords['height']);
                    $pdf->Image($selloAnteriorPath, $selloAnteriorCoords['x'], $selloAnteriorCoords['y'], $selloAnteriorCoords['width'], $selloAnteriorCoords['height']);
                }
            } catch (Exception $e) {
                return back()->withErrors(['error' => 'Error al insertar imágenes: ' . $e->getMessage()]);
            }
        }

        try {
            $pdf->Output($outputPath, 'F');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar el archivo PDF firmado: ' . $e->getMessage()]);
        }

        if (!file_exists($outputPath)) {
            return back()->withErrors(['error' => 'No se pudo guardar el archivo PDF firmado.']);
        }

        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($uploadedPdfPath);

        if ($pageCount < 1) {
            return back()->withErrors(['error' => 'El archivo PDF no contiene páginas.']);
        }

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($templateId, 0, 0);

            // Si es la última página, añadir solo los textos
            if ($pageNo == $pageCount) {
                // Si los archivos son "firma.png" y "sello.png", no agregar texto
                if (basename($firmaUltimaPath) === 'firma.png' && basename($selloUltimaPath) === 'sello.png') {
                    continue;
                }

                $xSello = $selloUltimaCoords['x'];
                $ySello = $selloUltimaCoords['y'];
                $anchoSello = $selloUltimaCoords['width'];
                $altoSello = $selloUltimaCoords['height'];

                $lineHeight = 5;
                $textos = [$texto1, $texto2, $texto3, $textoFecha];
                $pdf->SetFont('Helvetica', '', 10);
                $pdf->SetTextColor(0, 0, 0);

                // Ajustar distancia entre textos y sello
                $ySelloAjustado = $ySello - 15;

                foreach ($textos as $index => $lineaTexto) {
                    $anchoTexto = $pdf->GetStringWidth($lineaTexto);
                    $xTextoCentrado = $xSello + ($anchoSello / 2) - ($anchoTexto / 2);
                    $yTexto = $ySelloAjustado + $altoSello + ($lineHeight * $index) + 2;

                    $pdf->SetXY($xTextoCentrado, $yTexto);

                    if ($lineaTexto !== $textoFecha) {
                        $pdf->SetFont('Helvetica', 'BU', 10); // Negrita y subrayado
                    } else {
                        $pdf->SetFont('Helvetica', '', 10); // Texto normal
                    }

                    $pdf->Cell($anchoTexto, $lineHeight, utf8_decode($lineaTexto), 0, 1, 'C');
                }
            }
        }

        $outputFileName2 = "{$nombreDocumento}_procesado.pdf";
        $outputPath = $carpetaCliente . "/$outputFileName2";

        try {
            $pdf->Output($outputPath, 'F');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar el archivo PDF procesado: ' . $e->getMessage()]);
        }

        $archivo_name = $outputFileName2;

        if (!file_exists($outputPath)) {
            return back()->withErrors(['error' => 'No se pudo guardar el archivo PDF procesado.']);
        }

        $archivo3_name = null;
        if ($request->hasFile('archivo3')) {
            $file = $request->file('archivo3');
            $archivo3_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo3_name);
        }
        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name);
        }
        $image_name2 = null;
        if ($request->hasFile('picture2')) {
            $file = $request->file('picture2');
            $image_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name2);
        }
        $accionNombre = Programacionsubcliente::where('id', $request->accion)->value('accionnombre');
        // Obtén las acciones seleccionadas del campo oculto 'acciones_seleccionadas' en lugar de 'acciones'
        $acciones = $request->has('acciones_seleccionadas') ? json_decode($request->input('acciones_seleccionadas'), true) : [];

        if (!is_array($acciones)) {
            $acciones = [];
        }

        $nombrecliente = $request->input('clienteitanombre');
        $idcliente = $request->input('clienteitaid');
        $programacionid = $request->input('programacionid');
        $usuarioid = $request->input('usuarioid');
        $usuarioregistro = $request->input('usuarioregistro');
        $fechabateria = $request->input('fechabateria');

        if (!empty($acciones)) {
            foreach ($acciones as $accion) {

                $accionNombreReal = Programacionsubcliente::where('id', $accion)
                                        ->value('accionnombre');

                $registro = Programacionsubcliente::where('clienteitaid', $idcliente)
                                ->where('fechabateria', $fechabateria)
                                ->where('accionnombre', 'LIKE', "%{$accionNombreReal}%")
                                ->first();

                if (!$registro) {
                    continue;
                }

                $programacionReal = $registro->id;
                $idsubproc = $registro->idsubproc;

                $documentacioncliente = Documentacionsubcliente::create([
                    'documentfirmado' => $outputFileName ?? null,
                    'document' => $outputFileName2 ?? null,
                    'accion' => $accion,
                    'fechabateria' => $fechabateria,
                    'clienteitanombre' => $nombrecliente,
                    'clienteitaid' => $idcliente,
                    'programacionid' => $programacionReal,
                    'idsubproc' => $idsubproc,
                    'image' => $image_name ?? null,
                    'image2' => $image_name2 ?? null,
                    'documentword' => $archivo3_name ?? null,
                    'usuarioid' => $usuarioid,
                    'usuarioregistro' => $usuarioregistro,
                    'clienteid' => $idcliente,
                    'clientenombre' => $nombrecliente,
                    'tipocliente' => 'ITA'
                ]);

                SubTramite::where('id', $idsubproc)->update([
                    'informeprogramacion' => $outputFileName2 ?? null,
                    'solicitante1' => $image_name ?? null,
                    'solicitante2' => $image_name2 ?? null
                ]);
            }
        }


        /* if ($documentacioncliente) {
            $usuarioAutenticado = auth()->user();

            if ($usuarioAutenticado->hasRole(['PROVEEDOR', 'PROVEEDOR IF'])) { 
                $usuariosNotificar = User::whereIn('id', [3, 5, 10, 11, 25, 33])->get();
                foreach ($usuariosNotificar as $usuarioDestino) {
                    $usuarioDestino->notify(new InformeProveedorNotification($documentacioncliente));
                }
            }
        } */
        if ($documentacioncliente) {
            $usuarioAutenticado = auth()->user();

            if ($usuarioAutenticado->hasRole(['MAESTRO', 'PROVEEDOR IF'])) {
                $sucursalCliente = Cliente::where('id', $idcliente)->value('sucursal');

                $usuariosNotificar = User::whereHas('roles', function ($q) {
                        $q->whereIn('name', ['MAESTRO','ADMINISTRADOR','OPERATIVO']);
                    })
                    ->where('sucursal', $sucursalCliente)
                    ->get();

                foreach ($usuariosNotificar as $usuarioDestino) {
                    $usuarioDestino->notify(new InformeProveedorNotification($documentacioncliente));
                }
            }

        }

        return back()->with('info', 'El archivo PDF firmado y sellado ha sido guardado correctamente.');
    }
    public function procesarInformeMultipleauditoria(Request $request, ClienteAuditoria $clienteauditoria) 
    {
        $request->validate([
            'archivo' => 'required|mimes:pdf|max:15120',
        ]);

        $id = $request->input('clienteauditoriaid');
        $carpetaCliente = public_path("documentacionclientesauditoria/{$id}");
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }

        $usuario = auth()->user()->name;
        $sucursal = auth()->user()->sucursal;
        $fechaActual = Carbon::now()->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $accion = $request->input('accion');
        
        $archivo_name = null;
            if ($request->hasFile('archivo2')) {
                $file = $request->file('archivo2');
                
                $carpetaCliente = public_path("/documentacionclientesauditoria/{$id}");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);}
                $archivo_name = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name);
            }

        $firmaAnteriorPath = '';
        $selloAnteriorPath = '';
        $firmaUltimaPath = '';
        $selloUltimaPath = '';
        $firmaAnteriorCoords = [];
        $selloAnteriorCoords = [];
        $firmaUltimaCoords = [];
        $selloUltimaCoords = [];
        $texto1 = $texto2 = $texto3 = "";
        switch ($usuario) {
            case 'CARLOS ALEJANDRO GUARACHI SANDOVAL':
                $firmaAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                $selloAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO ORIGINAL MARIA RENEE VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 186, 'y' => 205, 'width' => 20, 'height' => 37];
                $selloAnteriorCoords = ['x' => 175, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL ULTIMA MARIA RENEE.png');
                $selloUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO ORIGINAL MARIA RENEE.png');
                $firmaUltimaCoords = ['x' => 90, 'y' => 190, 'width' => 35, 'height' => 40];
                $selloUltimaCoords = ['x' => 85, 'y' => 199, 'width' => 40, 'height' => 40];
                $texto1 = "DRA. ANA PAOLA ESPINOZA R.";
                $texto2 = "E-6289025";
                $texto3 = "";
            break;

            case 'AGUIRRE VASQUEZ MARIA RENEE':
                $firmaAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                $selloAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 20, 'height' => 37];
                $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL ULTIMA MARIA RENEE.png');
                $selloUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE.png');
                $firmaUltimaCoords = ['x' => 90, 'y' => 186, 'width' => 35, 'height' => 40];
                $selloUltimaCoords = ['x' => 85, 'y' => 199, 'width' => 40, 'height' => 40];
                $texto1 = "DRA. MARIA RENEÉ AGUIRRE VASQUEZ";
                $texto2 = "MEDICINA LABORAL Y SALUD OCUPACIONAL";
                $texto3 = "M.P.A-7676725 CM:11019";
            break;

            case 'MARICELA COLQUE SANDOVAL':
                $firmaAnteriorPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/FIRMA ORIGINAL MARICELA COLQUE VERTICAL.png');
                $selloAnteriorPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/SELLO ORIGINAL MARICELA COLQUE VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 183, 'y' => 205, 'width' => 20, 'height' => 25];
                $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/FIRMA ORIGINAL MARICELA COLQUE.png');
                $selloUltimaPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/SELLO ORIGINAL MARICELA COLQUE.png');
                $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                $texto1 = "LIC. MARICELA COLQUE SANDOVAL";
                $texto2 = "TRABAJADORA SOCIAL";
                $texto3 = "MAT. PROF. N° 0496";
            break;

            case 'MONICA MACOÑO FLORES':
                $firmaAnteriorPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/FIRMA ORIGINAL MONICA MACOÑO VERTICAL.png');
                $selloAnteriorPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/SELLO ORIGINAL MONICA MAÑOCO VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 185, 'y' => 205, 'width' => 20, 'height' => 25];
                $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/FIRMA ORIGINAL MONICA MACOÑO.png');
                $selloUltimaPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/SELLO ORIGINAL MONICA MAÑOCO.png');
                $firmaUltimaCoords = ['x' => 90, 'y' => 132, 'width' => 30, 'height' => 35];
                $selloUltimaCoords = ['x' => 82, 'y' => 140, 'width' => 40, 'height' => 40];
                $texto1 = "LIC. MONICA MACOÑO FLORES";
                $texto2 = "FISIOTERAPEUTA KINESIÓLOGA";
                $texto3 = "MAT.: 8237722";
            break;

            case 'KUCHARSKY RUIZ PATRICIA CELIA':
                $firmaAnteriorPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/FIRMA ORIGINAL PATRICIA VERTICAL.png');
                $selloAnteriorPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/SELLO ORIGINAL PATRICIA VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 188, 'y' => 205, 'width' => 15, 'height' => 25];
                $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/FIRMA ORIGINAL PATRICIA.png');
                $selloUltimaPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/SELLO ORIGINAL PATRICIA.png');
                $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                $texto1 = "LIC. PATRICIA KUCHARSKY RUIZ";
                $texto2 = "PSICÓLOGA";
                $texto3 = "MAT. PROF. K-2";
            break;

            /* case 'CARDIOVIDA':
                if (Str::startsWith($accion, 'ELECTROCARDIOGRAMA') || Str::startsWith($accion, 'ERGOMETRIA') || Str::startsWith($accion, 'CARDIOLOGIA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/CARDIOVIDA/FIRMA ORIGINAL CARDIOVIDA VERTICAL.png');
                    $selloAnteriorPath = public_path('/glfirmasello/CARDIOVIDA/SELLO ORIGINAL CARDIOVIDA VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 185, 'y' => 205, 'width' => 15, 'height' => 25];
                    $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/CARDIOVIDA/FIRMA ORIGINAL CARDIOVIDA.png');
                    $selloUltimaPath = public_path('/glfirmasello/CARDIOVIDA/SELLO ORIGINAL CARDIOVIDA.png');
                    $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                    $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                    $texto1 = "DR. IGNACIO VACA VELARDE";
                    $texto2 = "CARDIÓLOGO - HEMODINAMISTA";
                    $texto3 = "V-4762072 M22-8858";

                } elseif (Str::startsWith($accion, 'ECOCARDIOGRAMA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/CARDIOVIDA ECO/FIRMA ORIGINAL CARDIOVIDA ECO VERTICAL.png');
                    $selloAnteriorPath = public_path('/glfirmasello/CARDIOVIDA ECO/SELLO ORIGINAL CARDIOVIDA ECO VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 15, 'height' => 25];
                    $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/CARDIOVIDA ECO/FIRMA ORIGINAL CARDIOVIDA ECO.png');
                    $selloUltimaPath = public_path('/glfirmasello/CARDIOVIDA ECO/SELLO ORIGINAL CARDIOVIDA ECO.png');
                    $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                    $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                    $texto1 = "DRA. ANA PAOLA ESPINOZA R.";
                    $texto2 = "E-6289025";
                    $texto3 = "";
                }
            break; */

            case 'INSTITUTO DE CARDIOLOGIA INTERVENCIONISTA CARDIOVIDA S.R.L.':
                if (Str::startsWith($accion, 'ELECTROCARDIOGRAMA') || Str::startsWith($accion, 'ERGOMETRIA') || Str::startsWith($accion, 'CARDIOLOGIA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/CARDIOVIDA/FIRMA ORIGINAL CARDIOVIDA VERTICAL.png');
                    $selloAnteriorPath = public_path('/glfirmasello/CARDIOVIDA/SELLO ORIGINAL CARDIOVIDA VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 185, 'y' => 205, 'width' => 15, 'height' => 25];
                    $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/CARDIOVIDA/FIRMA ORIGINAL CARDIOVIDA.png');
                    $selloUltimaPath = public_path('/glfirmasello/CARDIOVIDA/SELLO ORIGINAL CARDIOVIDA.png');
                    $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                    $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                    $texto1 = "DR. IGNACIO VACA VELARDE";
                    $texto2 = "CARDIÓLOGO - HEMODINAMISTA";
                    $texto3 = "V-4762072 M22-8858";

                } elseif (Str::startsWith($accion, 'ECOCARDIOGRAMA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/CARDIOVIDA ECO/FIRMA ORIGINAL CARDIOVIDA ECO VERTICAL.png');
                    $selloAnteriorPath = public_path('/glfirmasello/CARDIOVIDA ECO/SELLO ORIGINAL CARDIOVIDA ECO VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 15, 'height' => 25];
                    $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/CARDIOVIDA ECO/FIRMA ORIGINAL CARDIOVIDA ECO.png');
                    $selloUltimaPath = public_path('/glfirmasello/CARDIOVIDA ECO/SELLO ORIGINAL CARDIOVIDA ECO.png');
                    $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                    $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                    $texto1 = "DRA. ANA PAOLA ESPINOZA R.";
                    $texto2 = "E-6289025";
                    $texto3 = "";
                }
            break;

            default:
                $firmaAnteriorPath = public_path('/glfirmasello/default/firma.png');
                $selloAnteriorPath = public_path('/glfirmasello/default/sello.png');
                $firmaAnteriorCoords = ['x' => 160, 'y' => 230, 'width' => 1, 'height' => 1];
                $selloAnteriorCoords = ['x' => 170, 'y' => 230, 'width' => 1, 'height' => 1];

                $firmaUltimaPath = public_path('/glfirmasello/default/firma.png');
                $selloUltimaPath = public_path('/glfirmasello/default/sello.png');
                $firmaUltimaCoords = ['x' => 85, 'y' => 215, 'width' => 1, 'height' => 1];
                $selloUltimaCoords = ['x' => 85, 'y' => 220, 'width' => 1, 'height' => 1];
                break;
        }

        /* $textoFecha = "{$sucursal}, {$fechaActual}"; */

        if ($usuario === 'INSTITUTO DE CARDIOLOGIA INTERVENCIONISTA CARDIOVIDA S.R.L.') {
            $textoFecha = null;
        } else {
            $textoFecha = "{$sucursal}, {$fechaActual}";
        }

        if (!file_exists($firmaAnteriorPath) || !file_exists($selloAnteriorPath) ||
            !file_exists($firmaUltimaPath) || !file_exists($selloUltimaPath)) {
            return back()->withErrors(['error' => 'Alguna firma o sello no fue encontrada para el usuario autenticado.']);
        }

        $uploadedPdfPath = $request->file('archivo')->getPathname();
        $nombreDocumento = $request->input('nombre_documento', uniqid());
        $outputFileName = "{$nombreDocumento}.pdf";
        $outputPath = $carpetaCliente . "/$outputFileName";

        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($uploadedPdfPath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($templateId, 0, 0);

            try {
                if ($pageNo == $pageCount) {
                    if (basename($firmaUltimaPath) === 'firma.png' && basename($selloUltimaPath) === 'sello.png') {
                        continue;
                    }
                    $pdf->Image($firmaUltimaPath, $firmaUltimaCoords['x'], $firmaUltimaCoords['y'], $firmaUltimaCoords['width'], $firmaUltimaCoords['height']);
                    $pdf->Image($selloUltimaPath, $selloUltimaCoords['x'], $selloUltimaCoords['y'], $selloUltimaCoords['width'], $selloUltimaCoords['height']);

                    $xSello = $selloUltimaCoords['x'];
                    $ySello = $selloUltimaCoords['y'];
                    $anchoSello = $selloUltimaCoords['width'];
                    $altoSello = $selloUltimaCoords['height'];

                    $lineHeight = 5;

                    $textos = [$texto1, $texto2, $texto3, $textoFecha];
                    $pdf->SetFont('Helvetica', '', 10);
                    $pdf->SetTextColor(0, 0, 0);

                    // Ajustar la distancia entre el grupo de textos y el sello
                    $ySelloAjustado = $ySello - 15; // Ajusta este valor para acercar o alejar todo el grupo de textos

                    // Calcular el centro horizontal tomando en cuenta el sello
                    foreach ($textos as $index => $lineaTexto) {
                        $anchoTexto = $pdf->GetStringWidth($lineaTexto); // Calcular el ancho del texto
                        $xTextoCentrado = $xSello + ($anchoSello / 2) - ($anchoTexto / 2); // Centrar el texto debajo del sello

                        // Ajustar la posición vertical debajo del sello
                        $yTexto = $ySelloAjustado + $altoSello + ($lineHeight * $index) + 2; // Mueve todo el grupo más cerca del sello

                        $pdf->SetXY($xTextoCentrado, $yTexto);

                        // Aplicar negrita y subrayado, excepto para el texto de la fecha
                        if ($lineaTexto !== $textoFecha) {
                            $pdf->SetFont('Helvetica', 'BU', 10); // Negrita y subrayado
                        } else {
                            $pdf->SetFont('Helvetica', '', 10); // Solo normal para la fecha
                        }

                        $pdf->Cell($anchoTexto, $lineHeight, utf8_decode($lineaTexto), 0, 1, 'C');
                    }
                } else {
                    // Páginas anteriores
                    $pdf->Image($firmaAnteriorPath, $firmaAnteriorCoords['x'], $firmaAnteriorCoords['y'], $firmaAnteriorCoords['width'], $firmaAnteriorCoords['height']);
                    $pdf->Image($selloAnteriorPath, $selloAnteriorCoords['x'], $selloAnteriorCoords['y'], $selloAnteriorCoords['width'], $selloAnteriorCoords['height']);
                }
            } catch (Exception $e) {
                return back()->withErrors(['error' => 'Error al insertar imágenes: ' . $e->getMessage()]);
            }
        }

        try {
            $pdf->Output($outputPath, 'F');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar el archivo PDF firmado: ' . $e->getMessage()]);
        }

        if (!file_exists($outputPath)) {
            return back()->withErrors(['error' => 'No se pudo guardar el archivo PDF firmado.']);
        }

        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($uploadedPdfPath);

        if ($pageCount < 1) {
            return back()->withErrors(['error' => 'El archivo PDF no contiene páginas.']);
        }

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($templateId, 0, 0);

            // Si es la última página, añadir solo los textos
            if ($pageNo == $pageCount) {
                // Si los archivos son "firma.png" y "sello.png", no agregar texto
                if (basename($firmaUltimaPath) === 'firma.png' && basename($selloUltimaPath) === 'sello.png') {
                    continue;
                }

                $xSello = $selloUltimaCoords['x'];
                $ySello = $selloUltimaCoords['y'];
                $anchoSello = $selloUltimaCoords['width'];
                $altoSello = $selloUltimaCoords['height'];

                $lineHeight = 5;
                $textos = [$texto1, $texto2, $texto3, $textoFecha];
                $pdf->SetFont('Helvetica', '', 10);
                $pdf->SetTextColor(0, 0, 0);

                // Ajustar distancia entre textos y sello
                $ySelloAjustado = $ySello - 15;

                foreach ($textos as $index => $lineaTexto) {
                    $anchoTexto = $pdf->GetStringWidth($lineaTexto);
                    $xTextoCentrado = $xSello + ($anchoSello / 2) - ($anchoTexto / 2);
                    $yTexto = $ySelloAjustado + $altoSello + ($lineHeight * $index) + 2;

                    $pdf->SetXY($xTextoCentrado, $yTexto);

                    if ($lineaTexto !== $textoFecha) {
                        $pdf->SetFont('Helvetica', 'BU', 10); // Negrita y subrayado
                    } else {
                        $pdf->SetFont('Helvetica', '', 10); // Texto normal
                    }

                    $pdf->Cell($anchoTexto, $lineHeight, utf8_decode($lineaTexto), 0, 1, 'C');
                }
            }
        }

        $outputFileName2 = "{$nombreDocumento}_procesado.pdf";
        $outputPath = $carpetaCliente . "/$outputFileName2";

        try {
            $pdf->Output($outputPath, 'F');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar el archivo PDF procesado: ' . $e->getMessage()]);
        }

        $archivo_name = $outputFileName2;

        if (!file_exists($outputPath)) {
            return back()->withErrors(['error' => 'No se pudo guardar el archivo PDF procesado.']);
        }

        $archivo3_name = null;
        if ($request->hasFile('archivo3')) {
            $file = $request->file('archivo3');
            $archivo3_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo3_name);
        }
        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name);
        }
        $image_name2 = null;
        if ($request->hasFile('picture2')) {
            $file = $request->file('picture2');
            $image_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name2);
        }
        $accionNombre = Programacionsubcliente::where('id', $request->accion)->value('accionnombre');
        // Obtén las acciones seleccionadas del campo oculto 'acciones_seleccionadas' en lugar de 'acciones'
        $acciones = $request->has('acciones_seleccionadas2') ? json_decode($request->input('acciones_seleccionadas2'), true) : [];

        if (!is_array($acciones)) {
            $acciones = []; // En caso de que sea null, lo convierte en array
        }

        $nombrecliente = $request->input('clienteauditorianombre');
        $idcliente = $request->input('clienteauditoriaid');
        $programacionid = $request->input('programacionid');
        $usuarioid = $request->input('usuarioid');
        $usuarioregistro = $request->input('usuarioregistro');
        $fechabateria = $request->input('fechabateria');

        if (!empty($acciones)) {
            foreach ($acciones as $accion) {
                $documentacioncliente = Documentacionsubcliente::create([
                    'documentfirmado' => $outputFileName ?? null,
                    'document' => $outputFileName2 ?? null,
                    'accion' => $accion, 
                    'fechabateria' => $fechabateria,
                    'clienteauditorianombre' => $nombrecliente,
                    'clienteauditoriaid' => $idcliente,
                    'programacionid' => $programacionid,
                    'image' => $image_name ?? null,
                    'image2' => $image_name2 ?? null,
                    'documentword' => $archivo3_name ?? null,
                    'usuarioid' => $usuarioid,
                    'usuarioregistro' => $usuarioregistro,
                    'clienteid' => $idcliente,
                    'clientenombre' => $nombrecliente,
                    'tipocliente' => 'AUDITORIA'
                ]);
            }
            /* if ($documentacioncliente) {
                $usuarioAutenticado = auth()->user();
    
                if ($usuarioAutenticado->hasRole(['PROVEEDOR', 'PROVEEDOR IF'])) { 
                    $usuariosNotificar = User::whereIn('id', [3, 5, 10, 11, 25, 33])->get();
                    foreach ($usuariosNotificar as $usuarioDestino) {
                        $usuarioDestino->notify(new InformeProveedorNotification($documentacioncliente));
                    }
                }
            } */
            
        }
        if ($documentacioncliente) {
            $usuarioAutenticado = auth()->user();

            if ($usuarioAutenticado->hasRole(['PROVEEDOR', 'PROVEEDOR IF'])) {
                $sucursalCliente = ClienteAuditoria::where('id', $idcliente)->value('sucursal');

                $usuariosNotificar = User::whereHas('roles', function ($q) {
                        $q->whereIn('name', ['MAESTRO','ADMINISTRADOR','OPERATIVO']);
                    })
                    ->where('sucursal', $sucursalCliente)
                    ->get();

                foreach ($usuariosNotificar as $usuarioDestino) {
                    $usuarioDestino->notify(new InformeProveedorNotification($documentacioncliente));
                }
            }

        }

        return back()->with('info', 'El archivo PDF firmado y sellado ha sido guardado correctamente.');
    }
    public function procesardiagnostico(StoreDocumentacionsubclienteRequest $request, Cliente $cliente) 
    {
        $archivo_name = null;
        $clienteitaid = $request->input('clienteitaid');
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            
            $carpetaCliente = public_path("/diagnosticos/{$clienteitaid}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            
            // Nombre del archivo PDF
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
    
        }
    
        $accionNombre = Programacionsubcliente::where('id', $request->accion)->value('accionnombre');
        $accion = $request->input('accion');
        $usuarioid = $request->input('usuarioid');
        $usuarioregistro = $request->input('usuarioregistro');
        $clienteitaid = $request->input('clienteitaid');
        $clienteitanombre = $request->input('clienteitanombre');
        $usuarioregistro = $request->input('usuarioregistro');
        $fechabateria = $request->input('fechabateria');
    
        $documentacioncliente = Documentacionsubcliente::create(
            $request->except('accion') + [
                'document' => $archivo_name,
                'accion' => $accion,
                'usuarioregistro' => $usuarioregistro,
                'usuarioid' => $usuarioid,
                'clienteitaid' => $clienteitaid,
                'clienteitanombre' => $clienteitanombre,
                'fechabateria' => $fechabateria,
                'clienteid' => $clienteitaid,
                'clientenombre' => $clienteitanombre,
                'tipocliente' => 'ITA'
            ]
        );

        if ($documentacioncliente) {
            $usuarioAutenticado = auth()->user();

            /* if ($usuarioAutenticado->hasRole(['PROVEEDOR', 'PROVEEDOR IF'])) { 
                $usuariosNotificar = User::whereIn('id', [3, 5, 10, 11, 25, 33])->get();
                foreach ($usuariosNotificar as $usuarioDestino) {
                    $usuarioDestino->notify(new DiagnosticoNotification($documentacioncliente));
                }
            } */
            if ($usuarioAutenticado->hasRole(['PROVEEDOR', 'PROVEEDOR IF'])) {
                $sucursalCliente = Cliente::where('id', $clienteitaid)->value('sucursal');

                $usuariosNotificar = User::whereHas('roles', function ($q) {
                        $q->whereIn('name', ['OPERATIVO']);
                    })
                    ->where('sucursal', $sucursalCliente)
                    ->get();

                foreach ($usuariosNotificar as $usuarioDestino) {
                    $usuarioDestino->notify(new DiagnosticoNotification($documentacioncliente));
                }
            }

        }
    
        return redirect()->route('admin.informesfinales.estadodocumentacionprogramacion')->with('info', 'El documento se subió con éxito');
    }
    public function procesarInformeauditoria(Request $request, ClienteAuditoria $clienteauditoria) 
    {
        $request->validate([
            'archivo' => 'required|mimes:pdf|max:15120',
        ]);

        $id = $request->input('clienteauditoriaid');
        $carpetaCliente = public_path("documentacionclientesauditoria/{$id}");
        if (!file_exists($carpetaCliente)) {
            mkdir($carpetaCliente, 0755, true);
        }

        $usuario = auth()->user()->name;
        $sucursal = auth()->user()->sucursal;
        $fechaActual = Carbon::now()->locale('es')->isoFormat('D [de] MMMM [del] YYYY');
        $accion = $request->input('accion');

        $archivo_name = null;
            if ($request->hasFile('archivo2')) {
                $file = $request->file('archivo2');
                
                $carpetaCliente = public_path("/documentacionclientesauditoria/{$id}");
                if (!file_exists($carpetaCliente)) {
                    mkdir($carpetaCliente, 0755, true);}
                $archivo_name = time() . '_' . $file->getClientOriginalName();
                $file->move($carpetaCliente, $archivo_name);
            }

        $firmaAnteriorPath = '';
        $selloAnteriorPath = '';
        $firmaUltimaPath = '';
        $selloUltimaPath = '';
        $firmaAnteriorCoords = [];
        $selloAnteriorCoords = [];
        $firmaUltimaCoords = [];
        $selloUltimaCoords = [];
        $texto1 = $texto2 = $texto3 = "";
        switch ($usuario) {
            case 'CARLOS ALEJANDRO GUARACHI SANDOVAL':
                $firmaAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                $selloAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO ORIGINAL MARIA RENEE VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 184, 'y' => 205, 'width' => 20, 'height' => 37];
                $selloAnteriorCoords = ['x' => 175, 'y' => 200, 'width' => 35, 'height' => 45];

                $firmaUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL ULTIMA MARIA RENEE.png');
                $selloUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO ORIGINAL MARIA RENEE.png');
                $firmaUltimaCoords = ['x' => 95, 'y' => 190, 'width' => 35, 'height' => 40];
                $selloUltimaCoords = ['x' => 85, 'y' => 199, 'width' => 50, 'height' => 45];
                $texto1 = "DRA. MARIA RENEÉ AGUIRRE VASQUEZ";
                $texto2 = "MÉDICO CIRUJANO";
                $texto3 = "M.P.A - 7676725";
            break;

            case 'AGUIRRE VASQUEZ MARIA RENEE':
                if (Str::startsWith($accion, 'ESPIROMETRIA') || Str::startsWith($accion, 'ESPIROMETRÍA') || Str::startsWith($accion, 'AUDIOMETRÍA') || Str::startsWith($accion, 'AUDIOMETRIA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                    $selloAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 20, 'height' => 37];
                    $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                    $selloUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO3 ORIGINAL MARIA RENEE.png');
                    $firmaUltimaCoords = ['x' => 31, 'y' => 240, 'width' => 25, 'height' => 30];
                    $selloUltimaCoords = ['x' => 25, 'y' => 252, 'width' => 40, 'height' => 13];
                    $texto1 = null;
                    $texto2 = null;
                    $texto3 = null;

                } else {
                    $firmaAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL MARIA RENEE.png');
                    $selloAnteriorPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 20, 'height' => 37];
                    $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/FIRMA ORIGINAL ULTIMA MARIA RENEE.png');
                    $selloUltimaPath = public_path('/glfirmasello/MARIA RENEE AGUIRRE VASQUEZ/SELLO2 ORIGINAL MARIA RENEE.png');
                    $firmaUltimaCoords = ['x' => 90, 'y' => 186, 'width' => 35, 'height' => 40];
                    $selloUltimaCoords = ['x' => 85, 'y' => 199, 'width' => 40, 'height' => 40];
                    $texto1 = "DRA. MARIA RENEÉ AGUIRRE VASQUEZ";
                    $texto2 = "MEDICINA LABORAL Y SALUD OCUPACIONAL";
                    $texto3 = "M.P.A-7676725 CM:11019";
                }
            break;

            case 'MARICELA COLQUE SANDOVAL':
                $firmaAnteriorPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/FIRMA ORIGINAL MARICELA COLQUE VERTICAL.png');
                $selloAnteriorPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/SELLO ORIGINAL MARICELA COLQUE VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 183, 'y' => 205, 'width' => 20, 'height' => 25];
                $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/FIRMA ORIGINAL MARICELA COLQUE.png');
                $selloUltimaPath = public_path('/glfirmasello/MARICELA COLQUE SANDOVAL/SELLO ORIGINAL MARICELA COLQUE.png');
                $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                $texto1 = "LIC. MARICELA COLQUE SANDOVAL";
                $texto2 = "TRABAJADORA SOCIAL";
                $texto3 = "MAT. PROF. N° 0496";
            break;

            case 'MONICA MACOÑO FLORES':
                $firmaAnteriorPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/FIRMA ORIGINAL MONICA MACOÑO VERTICAL.png');
                $selloAnteriorPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/SELLO ORIGINAL MONICA MAÑOCO VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 185, 'y' => 205, 'width' => 20, 'height' => 25];
                $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/FIRMA ORIGINAL MONICA MACOÑO.png');
                $selloUltimaPath = public_path('/glfirmasello/MONICA MACOÑO FLORES/SELLO ORIGINAL MONICA MAÑOCO.png');
                $firmaUltimaCoords = ['x' => 90, 'y' => 192, 'width' => 30, 'height' => 35];
                $selloUltimaCoords = ['x' => 82, 'y' => 200, 'width' => 40, 'height' => 40];
                $texto1 = "LIC. MONICA MACOÑO FLORES";
                $texto2 = "FISIOTERAPEUTA KINESIÓLOGA";
                $texto3 = "MAT.: 8237722";
            break;

            case 'KUCHARSKY RUIZ PATRICIA CELIA':
                $firmaAnteriorPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/FIRMA ORIGINAL PATRICIA VERTICAL.png');
                $selloAnteriorPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/SELLO ORIGINAL PATRICIA VERTICAL.png');
                $firmaAnteriorCoords = ['x' => 188, 'y' => 205, 'width' => 15, 'height' => 25];
                $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                $firmaUltimaPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/FIRMA ORIGINAL PATRICIA.png');
                $selloUltimaPath = public_path('/glfirmasello/KUCHARSKY RUIZ PATRICIA CELIA/SELLO ORIGINAL PATRICIA.png');
                $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                $texto1 = "LIC. PATRICIA KUCHARSKY RUIZ";
                $texto2 = "PSICÓLOGA";
                $texto3 = "MAT. PROF. K-2";
            break;

            case 'INSTITUTO DE CARDIOLOGIA INTERVENCIONISTA CARDIOVIDA S.R.L.':
                if (Str::startsWith($accion, 'ELECTROCARDIOGRAMA') || Str::startsWith($accion, 'ERGOMETRIA') || Str::startsWith($accion, 'CARDIOLOGIA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/CARDIOVIDA/FIRMA ORIGINAL CARDIOVIDA VERTICAL.png');
                    $selloAnteriorPath = public_path('/glfirmasello/CARDIOVIDA/SELLO ORIGINAL CARDIOVIDA VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 185, 'y' => 205, 'width' => 15, 'height' => 25];
                    $selloAnteriorCoords = ['x' => 178, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/CARDIOVIDA/FIRMA ORIGINAL CARDIOVIDA.png');
                    $selloUltimaPath = public_path('/glfirmasello/CARDIOVIDA/SELLO ORIGINAL CARDIOVIDA.png');
                    $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                    $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                    $texto1 = "DR. IGNACIO VACA VELARDE";
                    $texto2 = "CARDIÓLOGO - HEMODINAMISTA";
                    $texto3 = "V-4762072 M22-8858";

                } elseif (Str::startsWith($accion, 'ECOCARDIOGRAMA')) {
                    $firmaAnteriorPath = public_path('/glfirmasello/CARDIOVIDA ECO/FIRMA ORIGINAL CARDIOVIDA ECO VERTICAL.png');
                    $selloAnteriorPath = public_path('/glfirmasello/CARDIOVIDA ECO/SELLO ORIGINAL CARDIOVIDA ECO VERTICAL.png');
                    $firmaAnteriorCoords = ['x' => 187, 'y' => 205, 'width' => 15, 'height' => 25];
                    $selloAnteriorCoords = ['x' => 180, 'y' => 200, 'width' => 40, 'height' => 40];

                    $firmaUltimaPath = public_path('/glfirmasello/CARDIOVIDA ECO/FIRMA ORIGINAL CARDIOVIDA ECO.png');
                    $selloUltimaPath = public_path('/glfirmasello/CARDIOVIDA ECO/SELLO ORIGINAL CARDIOVIDA ECO.png');
                    $firmaUltimaCoords = ['x' => 97, 'y' => 205, 'width' => 30, 'height' => 20];
                    $selloUltimaCoords = ['x' => 90, 'y' => 205, 'width' => 40, 'height' => 40];
                    $texto1 = "DRA. ANA PAOLA ESPINOZA R.";
                    $texto2 = "E-6289025";
                    $texto3 = "";
                }
            break;
                    
            default:
                $firmaAnteriorPath = public_path('/glfirmasello/default/firma.png');
                $selloAnteriorPath = public_path('/glfirmasello/default/sello.png');
                $firmaAnteriorCoords = ['x' => 160, 'y' => 230, 'width' => 1, 'height' => 1];
                $selloAnteriorCoords = ['x' => 170, 'y' => 230, 'width' => 1, 'height' => 1];

                $firmaUltimaPath = public_path('/glfirmasello/default/firma.png');
                $selloUltimaPath = public_path('/glfirmasello/default/sello.png');
                $firmaUltimaCoords = ['x' => 85, 'y' => 215, 'width' => 1, 'height' => 1];
                $selloUltimaCoords = ['x' => 85, 'y' => 220, 'width' => 1, 'height' => 1];
            break;
        }

        /* $textoFecha = "{$sucursal}, {$fechaActual}"; */

        if ( 
            $usuario === 'AGUIRRE VASQUEZ MARIA RENEE' && 
            (Str::startsWith($accion, 'ESPIROMETRIA') || Str::startsWith($accion, 'ESPIROMETRÍA') || Str::startsWith($accion, 'AUDIOMETRÍA') || Str::startsWith($accion, 'AUDIOMETRIA'))
        ) {
            $textoFecha = null;
            
        } elseif ($usuario === 'INSTITUTO DE CARDIOLOGIA INTERVENCIONISTA CARDIOVIDA S.R.L.') {
            $textoFecha = null;
            
        } else {
            $textoFecha = "{$sucursal}, {$fechaActual}";
        }

        if (!file_exists($firmaAnteriorPath) || !file_exists($selloAnteriorPath) ||
            !file_exists($firmaUltimaPath) || !file_exists($selloUltimaPath)) {
            return back()->withErrors(['error' => 'Alguna firma o sello no fue encontrada para el usuario autenticado.']);
        }

        $uploadedPdfPath = $request->file('archivo')->getPathname();
        $nombreDocumento = $request->input('nombre_documento', uniqid());
        $outputFileName = "{$nombreDocumento}.pdf";
        $outputPath = $carpetaCliente . "/$outputFileName";

        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($uploadedPdfPath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {  
            $templateId = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($templateId, 0, 0);
        
            try {
                $isPenultima = ($pageNo == $pageCount - 1);
                $isUltima = ($pageNo == $pageCount);
                $isAntesDePenultima = ($pageNo < $pageCount - 1);
                $isAntesDeUltima = ($pageNo < $pageCount);
        
                if (strtoupper($usuario) == 'MONICA MACOÑO FLORES') {
                    if ($isAntesDePenultima) {
                        // Páginas anteriores a la penúltima → firma/sello anteriores
                        $pdf->Image($firmaAnteriorPath, $firmaAnteriorCoords['x'], $firmaAnteriorCoords['y'], $firmaAnteriorCoords['width'], $firmaAnteriorCoords['height']);
                        $pdf->Image($selloAnteriorPath, $selloAnteriorCoords['x'], $selloAnteriorCoords['y'], $selloAnteriorCoords['width'], $selloAnteriorCoords['height']);
                    } elseif ($isPenultima) {
                        // Penúltima hoja → firma/sello finales + textos
                        $pdf->Image($firmaUltimaPath, $firmaUltimaCoords['x'], $firmaUltimaCoords['y'], $firmaUltimaCoords['width'], $firmaUltimaCoords['height']);
                        $pdf->Image($selloUltimaPath, $selloUltimaCoords['x'], $selloUltimaCoords['y'], $selloUltimaCoords['width'], $selloUltimaCoords['height']);
        
                        // Textos finales
                        $xSello = $selloUltimaCoords['x'];
                        $ySello = $selloUltimaCoords['y'];
                        $anchoSello = $selloUltimaCoords['width'];
                        $altoSello = $selloUltimaCoords['height'];
                        $lineHeight = 5;
                        $textos = [$texto1, $texto2, $texto3, $textoFecha];
                        $pdf->SetFont('Helvetica', '', 10);
                        $pdf->SetTextColor(0, 0, 0);
                        $ySelloAjustado = $ySello - 15;
        
                        foreach ($textos as $index => $lineaTexto) {
                            $anchoTexto = $pdf->GetStringWidth($lineaTexto);
                            $xTextoCentrado = $xSello + ($anchoSello / 2) - ($anchoTexto / 2);
                            $yTexto = $ySelloAjustado + $altoSello + ($lineHeight * $index) + 2;
        
                            $pdf->SetXY($xTextoCentrado, $yTexto);
                            $pdf->SetFont('Helvetica', ($lineaTexto !== $textoFecha ? 'BU' : ''), 10);
                            $pdf->Cell($anchoTexto, $lineHeight, utf8_decode($lineaTexto), 0, 1, 'C');
                        }
                    }
                    // Última hoja: no se hace nada
                } else {
                    if ($isAntesDeUltima) {
                        // Todas menos la última → firma/sello anteriores
                        $pdf->Image($firmaAnteriorPath, $firmaAnteriorCoords['x'], $firmaAnteriorCoords['y'], $firmaAnteriorCoords['width'], $firmaAnteriorCoords['height']);
                        $pdf->Image($selloAnteriorPath, $selloAnteriorCoords['x'], $selloAnteriorCoords['y'], $selloAnteriorCoords['width'], $selloAnteriorCoords['height']);
                    } elseif ($isUltima) {
                        // Última hoja → firma/sello finales + textos
                        $pdf->Image($firmaUltimaPath, $firmaUltimaCoords['x'], $firmaUltimaCoords['y'], $firmaUltimaCoords['width'], $firmaUltimaCoords['height']);
                        $pdf->Image($selloUltimaPath, $selloUltimaCoords['x'], $selloUltimaCoords['y'], $selloUltimaCoords['width'], $selloUltimaCoords['height']);
        
                        // Textos finales
                        $xSello = $selloUltimaCoords['x'];
                        $ySello = $selloUltimaCoords['y'];
                        $anchoSello = $selloUltimaCoords['width'];
                        $altoSello = $selloUltimaCoords['height'];
                        $lineHeight = 5;
                        $textos = [$texto1, $texto2, $texto3, $textoFecha];
                        $pdf->SetFont('Helvetica', '', 10);
                        $pdf->SetTextColor(0, 0, 0);
                        $ySelloAjustado = $ySello - 15;
        
                        foreach ($textos as $index => $lineaTexto) {
                            $anchoTexto = $pdf->GetStringWidth($lineaTexto);
                            $xTextoCentrado = $xSello + ($anchoSello / 2) - ($anchoTexto / 2);
                            $yTexto = $ySelloAjustado + $altoSello + ($lineHeight * $index) + 2;
        
                            $pdf->SetXY($xTextoCentrado, $yTexto);
                            $pdf->SetFont('Helvetica', ($lineaTexto !== $textoFecha ? 'BU' : ''), 10);
                            $pdf->Cell($anchoTexto, $lineHeight, utf8_decode($lineaTexto), 0, 1, 'C');
                        }
                    }
                }
        
            } catch (Exception $e) {
                return back()->withErrors(['error' => 'Error al insertar imágenes: ' . $e->getMessage()]);
            }
        }

        try {
            $pdf->Output($outputPath, 'F');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar el archivo PDF firmado: ' . $e->getMessage()]);
        }

        if (!file_exists($outputPath)) {
            return back()->withErrors(['error' => 'No se pudo guardar el archivo PDF firmado.']);
        }

        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($uploadedPdfPath);

        if ($pageCount < 1) {
            return back()->withErrors(['error' => 'El archivo PDF no contiene páginas.']);
        }

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($templateId, 0, 0);
        
            try {
                $mostrarSoloTexto = false;
                $omitirTodo = false;
        
                if ($usuario == 'MONICA MACOÑO FLORES') {
                    if ($pageNo == $pageCount) {

                        $omitirTodo = true;
                    } elseif ($pageNo == $pageCount - 1) {

                        $mostrarSoloTexto = true;
                    } else {

                    }
                } else {
                    if ($pageNo == $pageCount) {

                        $mostrarSoloTexto = true;
                    } else {
            
                    }
                }

                if ($omitirTodo) {
                    continue;
                }

                if ($mostrarSoloTexto) {
                    $xSello = $selloUltimaCoords['x'];
                    $ySello = $selloUltimaCoords['y'];
                    $anchoSello = $selloUltimaCoords['width'];
                    $altoSello = $selloUltimaCoords['height'];
                    $lineHeight = 5;
                    $textos = [$texto1, $texto2, $texto3, $textoFecha];
                    $pdf->SetFont('Helvetica', '', 10);
                    $pdf->SetTextColor(0, 0, 0);
                    $ySelloAjustado = $ySello - 15;
        
                    foreach ($textos as $index => $lineaTexto) {
                        $anchoTexto = $pdf->GetStringWidth($lineaTexto);
                        $xTextoCentrado = $xSello + ($anchoSello / 2) - ($anchoTexto / 2);
                        $yTexto = $ySelloAjustado + $altoSello + ($lineHeight * $index) + 2;
        
                        $pdf->SetXY($xTextoCentrado, $yTexto);
                        if ($lineaTexto !== $textoFecha) {
                            $pdf->SetFont('Helvetica', 'BU', 10);
                        } else {
                            $pdf->SetFont('Helvetica', '', 10);
                        }
        
                        $pdf->Cell($anchoTexto, $lineHeight, utf8_decode($lineaTexto), 0, 1, 'C');
                    }
                }
        
            } catch (Exception $e) {
                return back()->withErrors(['error' => 'Error al insertar imágenes: ' . $e->getMessage()]);
            }
        }

        $outputFileName2 = "{$nombreDocumento}_procesado.pdf";
        $outputPath = $carpetaCliente . "/$outputFileName2";

        try {
            $pdf->Output($outputPath, 'F');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al guardar el archivo PDF procesado: ' . $e->getMessage()]);
        }

        $archivo_name = $outputFileName2;

        if (!file_exists($outputPath)) {
            return back()->withErrors(['error' => 'No se pudo guardar el archivo PDF procesado.']);
        }

        $archivo3_name = null;
        if ($request->hasFile('archivo3')) {
            $file = $request->file('archivo3');
            $archivo3_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo3_name);
        }
        $image_name = null;
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $image_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name);
        }
        $image_name2 = null;
        if ($request->hasFile('picture2')) {
            $file = $request->file('picture2');
            $image_name2 = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $image_name2);
        }
        $accionNombre = Programacionsubcliente::where('id', $request->accion)->value('accionnombre');
        $accion = $request->input('accion');
        $nombrecliente = $request->input('clienteauditorianombre');
        $idcliente = $request->input('clienteauditoriaid');
        $programacionid = $request->input('programacionid');
        $usuarioid = $request->input('usuarioid');
        $usuarioregistro = $request->input('usuarioregistro');

        $documentacioncliente = Documentacionsubcliente::create(
            $request->except('accion') + [
                'documentfirmado' => $outputFileName,
                'document' => $outputFileName2,
                'accion' => $accion,
                'clienteauditorianombre' => $nombrecliente,
                'programacionid' => $programacionid,
                'clienteauditoriaid' => $idcliente,
                'image' => $image_name,
                'image2' => $image_name2,
                'documentword' => $archivo3_name,
                'usuarioid' => $usuarioid,
                'usuarioregistro' => $usuarioregistro,
                'clienteid' => $idcliente,
                'clientenombre' => $nombrecliente,
                'tipocliente' => 'AUDITORIA'
            ]
        );

        /* if ($documentacioncliente) {
            $usuarioAutenticado = auth()->user();

            if ($usuarioAutenticado->hasRole(['PROVEEDOR', 'PROVEEDOR IF'])) { 
                $usuariosNotificar = User::whereIn('id', [3, 5, 10, 11, 25, 33])->get();
                foreach ($usuariosNotificar as $usuarioDestino) {
                    $usuarioDestino->notify(new InformeProveedorNotification($documentacioncliente));
                }
            }
        } */
        if ($documentacioncliente) {
                $usuarioAutenticado = auth()->user();

                if ($usuarioAutenticado->hasRole(['PROVEEDOR', 'PROVEEDOR IF'])) {
                    $sucursalCliente = ClienteAuditoria::where('id', $idcliente)->value('sucursal');

                    $usuariosNotificar = User::whereHas('roles', function ($q) {
                            $q->whereIn('name', ['MAESTRO','ADMINISTRADOR','OPERATIVO']);
                        })
                        ->where('sucursal', $sucursalCliente)
                        ->get();

                    foreach ($usuariosNotificar as $usuarioDestino) {
                        $usuarioDestino->notify(new InformeProveedorNotification($documentacioncliente));
                    }
                }

            }

        return back()->with('info', 'El archivo PDF firmado y sellado ha sido guardado correctamente.');
    }
    public function procesardiagnosticoauditoria(StoreDocumentacionsubclienteRequest $request, ClienteAuditoria $clienteauditoria) 
    {
        $archivo_name = null;
        $clienteauditoriaid = $request->input('clienteauditoriaid');
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            
            $carpetaCliente = public_path("/diagnosticosauditoria/{$clienteauditoriaid}");
            if (!file_exists($carpetaCliente)) {
                mkdir($carpetaCliente, 0755, true);
            }
            
            // Nombre del archivo PDF
            $archivo_name = time() . '_' . $file->getClientOriginalName();
            $file->move($carpetaCliente, $archivo_name);
    
        }
    
        $accionNombre = Programacionsubcliente::where('id', $request->accion)->value('accionnombre');
        $accion = $request->input('accion');
        $usuarioid = $request->input('usuarioid');
        $usuarioregistro = $request->input('usuarioregistro');
        $clienteauditoriaid = $request->input('clienteauditoriaid');
        $clienteauditorianombre = $request->input('clienteauditorianombre');
        $usuarioregistro = $request->input('usuarioregistro');
        $fechabateria = $request->input('fechabateria');
    
        $documentacioncliente = Documentacionsubcliente::create(
            $request->except('accion') + [
                'document' => $archivo_name,
                'accion' => $accion,
                'usuarioregistro' => $usuarioregistro,
                'usuarioid' => $usuarioid,
                'clienteauditoriaid' => $clienteauditoriaid,
                'clienteauditorianombre' => $clienteauditorianombre,
                'fechabateria' => $fechabateria,
                'clienteid' => $clienteauditoriaid,
                'clientenombre' => $clienteauditorianombre,
                'tipocliente' => 'AUDITORIA'
            ]
        );

        if ($documentacioncliente) {
            $usuarioAutenticado = auth()->user();

            if ($usuarioAutenticado->hasRole(['PROVEEDOR', 'PROVEEDOR IF'])) {
                $sucursalCliente = ClienteAuditoria::where('id', $clienteauditoriaid)->value('sucursal');

                $usuariosNotificar = User::whereHas('roles', function ($q) {
                        $q->whereIn('name', ['OPERATIVO']);
                    })
                    ->where('sucursal', $sucursalCliente)
                    ->get();

                foreach ($usuariosNotificar as $usuarioDestino) {
                    $usuarioDestino->notify(new DiagnosticoNotification($documentacioncliente));
                }
            }

        }
    
        return redirect()->route('admin.informesfinales.resultadosmedicosclientesauditoria')->with('info', 'El documento se subió con éxito');
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
