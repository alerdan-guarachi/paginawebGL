<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Proveedor;
use App\Models\Areaaccion;
use App\Models\Departamento;
use App\Http\Requests\StoreProveedorRequest;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /* public function __construct() { 
        $this->middleware('can:admin.users.index')->only('index');
    } */

        //REQUISITOS CLIENTE ITA PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES
    public function subirdocrequisitospensionderivretiro(Cliente $cliente)
    {
        $clienteitaid = $cliente->id; 
        $userRole = auth()->user()->getRoleNames()->first(); 
        $estadoLaboral = strtolower($cliente->estadolaboral);
        $numHijosMenores = $cliente->numhijosmenores;
        $estadoCivil = strtolower($cliente->estadocivil);
        $genero = strtolower($cliente->genero);
        $ocupacion = strtolower($cliente->ocupacion);
        $rolusuario = auth()->user()->getRoleNames()->first();

        $requisito = RequisitoSubCliente::where('clienteitaid', $cliente->id)
        ->where('servicio', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')
        ->firstOrFail();

        $requisitosCliente = RequisitoSubCliente::where('clienteitaid', $clienteitaid)
            ->where('servicio', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')
            ->first();

        $campos = [
            'poder' => 'PODER Y CARNET IDENTIDAD APODERADO',
            'avcci' => 'AVC/CARNET ASEGURADO',
            'cnacasegurado' => 'CERTIFICADO NACIMIENTO ASEGURADO',
            'ciasegurado' => 'CARNET IDENTIDAD ASEGURADO',
            'cmatrimonio' => 'CERTIFICADO DE MATRIMONIO',
            'cnacconyuge' => 'CERTIFICADO NACIMIENTO CONYUGE',
            'ciconyuge' => 'CARNET IDENTIDAD CONYUGE',
            'cunionlibre' => 'CERTIFICADO DE UNIÓN LIBRE',
            'cnacimientounionlibre' => 'CERTIFICADO DE NAC. DE UNIÓN LIBRE',
            'ciunionlibre' => 'CARNET IDENTIDAD DE UNIÓN LIBRE',
            'cdivorcio' => 'CERTIFICADO DE DIVORCIO',
            'cdefuncion' => 'CERTIFICADO DE DEFUNCIÓN',
            'cnacjihos' => 'CERTIFICADO NACIMIENTO HIJOS < 25',
            'cihijos' => 'CARNET IDENTIDAD HIJOS < 25',
            'denfaccidente' => 'DENUNCIA ENFERMEDAD ACCIDENTE',
            'crodomicilio' => 'CROQUIS DE DOMICILIO',
            'contrato' => 'CONTRATO',
            'ctrabajo' => 'CERTIFICADO DE TRABAJO',
            'boletapago' => 'BOLETA DE PAGO',
            'egestora' => 'EXTRACTO DE GESTORA',
            'actdatos' => 'ACTUALIZACIÓN DE DATOS',
            'resolinvhijos' => 'RESOLUCIÓN INVALIDEZ DE HIJOS < 25',
            'recordservicios' => 'RECORD SERVICIOS',
            'infomedicasalud' => 'INFORMACIÓN MÉDICA',
            'dictamencalentenc' => 'DICTAMEN CALIFICACIÓN ENTIDAD ENCARGADA',
            'anteriordictamen' => 'ANTERIOR DICTAMEN O RESOLUCIÓN',
            'ccompcotsenasir' => 'CERTIFICADO COMPENZACIÓN COTIZACIONES SENASIR',
            'cnactreshijos' => 'CERTIFICADO NACIMIENTO DE 3 HIJOS',
            'ctrabajoinsalubre' => 'CERTIFICADO TRABAJO INSALUBRE',
            'poderciapoderado' => 'PODER Y CARNET IDENTIDAD APODERADO',
            'cmeddifuncion' => 'CERTIFICADO MÉDICO DIFUNCIÓN',
            'cnactitular' => 'CERTIFICADO NACIMIENTO TITULAR',
            'cititular' => 'CARNET IDENTIDAD TITULAR',
            'cestudioshijos' => 'CERTIFICADO ESTUDIOS HIJOS < 25',
            'tdeclaherederos' => 'TESTIMONIO DE DECLARATORIA DE HEREDEROS',
            'cnacherederos' => 'CERTIFICADO NACIMIENTO DECLARADOS HEREDEROS',
            'cideclaherederos' => 'CARNET IDENTIDAD DECLARADOS HEREDEROS',
            'compenzacioncotizacion' => 'CERTIFICADO DE COMPENZACIÓN DE COTIZACIONES',
            'contratopensionderivretiro' => 'CONTRATO DE PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES',
            'csalarioaportes' => 'CERTIFICADO DE SALARIOS Y APORTES',
            'fotofrojoasegurado' => 'ASEGURADO FOTO 4X4 FONDO ROJO',
            'fotofrojoapoderadocroquis' => 'APODERADO FOTO 4X4 FONDO ROJO + CROQUIS DOMICILIO',
            'csalarioaporteslegalizada' => 'CERTIFICADO DE SALARIOS Y APORTES (PLANILLA LEGALIZADA)',
            'finiquito' => 'FINIQUITO',
        ];

        $requisitos = [];

        foreach ($campos as $campo => $label) {
            $valor = $requisitosCliente ? $requisitosCliente->$campo : null;
            $requisitos[] = [
                'campo'    => $campo,
                'label'    => $label,
                'pendiente'=> $valor === 'PENDIENTE',
                'subido'   => $valor && str_ends_with($valor, '.pdf'),
            ];
        }

        $campos2 = [
            'poder' => ['label' => 'PODER Y CARNET IDENTIDAD APODERADO', 'extra' => 'numeropoder'],
            'avcci' => ['label' => 'AVC/CARNET ASEGURADO'],
            'cnacasegurado' => ['label' => 'CERTIFICADO NACIMIENTO ASEGURADO'],
            'ciasegurado' => ['label' => 'CARNET IDENTIDAD ASEGURADO'],
            'cmatrimonio' => ['label' => 'CERTIFICADO DE MATRIMONIO'],
            'cnacconyuge' => ['label' => 'CERTIFICADO NACIMIENTO CONYUGE'],
            'ciconyuge' => ['label' => 'CARNET IDENTIDAD CONYUGE'],
            'cunionlibre' => ['label' => 'CERTIFICADO DE UNIÓN LIBRE'],
            'cnacimientounionlibre' => ['label' => 'CERTIFICADO DE NACIMIENTO DE UNIÓN LIBRE'],
            'ciunionlibre' => ['label' => 'CARNET IDENTIDAD DE UNIÓN LIBRE'],
            'cdivorcio' => ['label' => 'CERTIFICADO DE DIVORCIO'],
            'cdefuncion' => ['label' => 'CERTIFICADO DE DEFUNCIÓN'],
            'cnacjihos' => ['label' => 'CERTIFICADO NACIMIENTO HIJOS < 25'],
            'cihijos' => ['label' => 'CARNET IDENTIDAD HIJOS < 25'],
            'denfaccidente' => ['label' => 'DENUNCIA ENFERMEDAD ACCIDENTE'],
            'crodomicilio' => ['label' => 'CROQUIS DE DOMICILIO'],
            'contrato' => ['label' => 'CONTRATO', 'restricted' => true],
            'recordservicios' => ['label' => 'RECORD SERVICIOS'],
            'infomedicasalud' =>  ['label' => 'INFORMACIÓN MÉDICA'],
            'ctrabajo' => ['label' => 'CERTIFICADO DE TRABAJO'],
            'boletapago' => ['label' => 'BOLETA DE PAGO'],
            'egestora' => ['label' => 'EXTRACTO DE GESTORA'],
            'actdatos' => ['label' => 'ACTUALIZACIÓN DE DATOS'],
            'resolinvhijos' => ['label' => 'RESOL. INVAL. HIJOS < 25'],
            'dictamencalentenc' => ['label' => 'DICTAMEN CALIFICACIÓN ENTIDAD ENCARGADA'],
            'anteriordictamen' => ['label' => 'ANTERIOR DICTAMEN O RESOLUCIÓN'],
            'ccompcotsenasir' => ['label' => 'CERTIFICADO COMPENZACIÓN COTIZACIONES SENASIR'],
            'cnactreshijos' => ['label' => 'CERTIFICADO NACIMIENTO DE 3 HIJOS'],
            'ctrabajoinsalubre' => ['label' => 'CERTIFICADO TRABAJO INSALUBRE'],
            'poderciapoderado' => ['label' => 'PODER Y CARNET IDENTIDAD APODERADO'],
            'cmeddifuncion' => ['label' => 'CERTIFICADO MÉDICO DIFUNCIÓN'],
            'cnactitular' => ['label' => 'CERTIFICADO NACIMIENTO TITULAR'],
            'cititular' => ['label' => 'CARNET IDENTIDAD TITULAR'],
            'cestudioshijos' => ['label' => 'CERTIFICADO ESTUDIOS HIJOS < 25'],
            'tdeclaherederos' => ['label' => 'TESTIMONIO DE DECLARATORIA DE HEREDEROS'],
            'cnacherederos' => ['label' => 'CERTIFICADO NACIMIENTO DECLARADOS HEREDEROS'],
            'cideclaherederos' => ['label' => 'CARNET IDENTIDAD DECLARADOS HEREDEROS'],
            'compenzacioncotizacion' => ['label' => 'CERTIFICADO DE COMPENZACIÓN DE COTIZACIONES'],
            'contratopensionderivretiro' => ['label' => 'CONTRATO DE PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES'],
            'csalarioaportes' => ['label' => 'CERTIFICADO DE SALARIOS Y APORTES'],
            'fotofrojoasegurado' => ['label' => 'ASEGURADO FOTO 4X4 FONDO ROJO'],
            'fotofrojoapoderadocroquis' => ['label' => 'APODERADO FOTO 4X4 FONDO ROJO + CROQUIS DOMICILIO'],
            'csalarioaporteslegalizada' => ['label' => 'CERTIFICADO DE SALARIOS Y APORTES (PLANILLA LEGALIZADA)'],
            'finiquito' => ['label' => 'FINIQUITO'],
        ];

        $requisitosList = [];

        foreach ($campos2 as $campo => $cfg) {
            $valor = $requisito->$campo ?? null;

            $item = [
                'label' => $cfg['label'],
                'file' => $valor,
                'uploaded' => $valor && str_ends_with($valor, '.pdf'),
            ];

            if (isset($cfg['extra'])) {
                $item['extra'] = $requisito->{$cfg['extra']};
            }
            if (isset($cfg['restricted'])) {
                $item['restricted'] = $cfg['restricted'];
            }

            $requisitosList[] = $item;
        }

        $hayPendientes = collect($requisitos)->contains('pendiente', true);

        return view('admin.asociados.subirdocrequisitospensionderivretiro', compact('cliente','requisito','userRole','estadoLaboral',
                    'numHijosMenores','estadoCivil','genero','ocupacion','rolusuario','requisitos','requisitosList','hayPendientes'));
    }
    public function generarchecklistclienteitapensionderivretiro(Cliente $cliente) 
    {
        $tieneRequisitos = RequisitoSubCliente::where('clienteitaid', $cliente->id)
            ->where('servicio', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')->exists();
        $estadoLaboral = strtolower($cliente->estadolaboral);
        $numHijosMenores = $cliente->numhijosmenores;
        $estadoCivil = strtolower($cliente->estadocivil);
        $servicio1 = strtolower($cliente->tipocliente);
        $rolusuario = auth()->user()->getRoleNames()->first();
        $genero = strtolower($cliente->genero);

        $registroExistente = Estadocotizacionsubcliente::where('clienteitaid', $cliente->id)
            ->where('detalle', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS')
            ->where('tramite', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')
            ->first();
        $registroaprobadoExistente = Estadocotizacionsubcliente::where('clienteitaid', $cliente->id)
            ->where('detalle', 'APROBADO PARA INICIAR A CREAR BATERIA')
            ->where('tramite', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')
            ->first();
        $registroaprobadoinformefinalExistente = Estadocotizacionsubcliente::where('clienteitaid', $cliente->id)
            ->where('detalle', 'APROBADO PARA INFORME FINAL DIRECTO')
            ->where('tramite', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')
            ->first();
        $userSucursal = Auth::user()->sucursal;

        $proveedores = Bateriaproveedor::where('accion', 'MEDICINA LABORAL')
            ->where('estado', 'ACTIVO')
            ->when($userSucursal === 'SANTA CRUZ', function ($query) {
                return $query->where('sucursal', 'SANTA CRUZ');
            })
            ->distinct()
            ->pluck('proveedor');

        return view('admin.asociados.generarchecklistclienteitapensionderivretiro', compact('cliente','tieneRequisitos','estadoLaboral',
                    'numHijosMenores','estadoCivil','registroExistente','rolusuario','registroaprobadoExistente','proveedores',
                    'registroaprobadoinformefinalExistente','servicio1','genero'
        ));
    }
    public function guardardocrequisitospensionderivretiro(Request $request, Cliente $cliente)
    {
        $requisito = RequisitoSubCliente::where('clienteitaid', $cliente->id)
        ->where('servicio', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')->firstOrFail();

        $request->validate([
            'poder' => 'nullable|mimes:pdf',
            'numeropoder' => 'nullable|max:45',
            'avcci' => 'nullable|mimes:pdf',
            'cnacasegurado' => 'nullable|mimes:pdf',
            'ciasegurado' => 'nullable|mimes:pdf',
            'cmatrimonio' => 'nullable|mimes:pdf',
            'cnacconyuge' => 'nullable|mimes:pdf',
            'ciconyuge' => 'nullable|mimes:pdf',
            'cnacjihos' => 'nullable|mimes:pdf',
            'cihijos' => 'nullable|mimes:pdf',
            'denfaccidente' => 'nullable|mimes:pdf',
            'crodomicilio' => 'nullable|mimes:pdf',
            'contrato' => 'nullable|mimes:pdf',
            'ctrabajo' => 'nullable|mimes:pdf',
            'boletapago' => 'nullable|mimes:pdf',
            'egestora' => 'nullable|mimes:pdf',
            'actdatos' => 'nullable|mimes:pdf',
            'resolinvhijos' => 'nullable|mimes:pdf',
            'cunionlibre' => 'nullable|mimes:pdf',
            'cnacimientounionlibre' => 'nullable|mimes:pdf',
            'ciunionlibre' => 'nullable|mimes:pdf',
            'cdivorcio' => 'nullable|mimes:pdf',
            'cdefuncion' => 'nullable|mimes:pdf',
            'recordservicios' => 'nullable|mimes:pdf',
            'infomedicasalud' =>  'nullable|mimes:pdf',
            'dictamencalentenc' => 'nullable|mimes:pdf',
            'anteriordictamen' => 'nullable|mimes:pdf',
            'ccompcotsenasir' => 'nullable|mimes:pdf',
            'cnactreshijos' => 'nullable|mimes:pdf',
            'ctrabajoinsalubre' => 'nullable|mimes:pdf',
            'poderciapoderado' => 'nullable|mimes:pdf',
            'cmeddifuncion' => 'nullable|mimes:pdf',
            'cnactitular' => 'nullable|mimes:pdf',
            'cititular' => 'nullable|mimes:pdf',
            'cestudioshijos' => 'nullable|mimes:pdf',
            'tdeclaherederos' => 'nullable|mimes:pdf',
            'cnacherederos' => 'nullable|mimes:pdf',
            'cideclaherederos' => 'nullable|mimes:pdf',
            'compenzacioncotizacion' => 'nullable|mimes:pdf',
            'contratopensionderivretiro' => 'nullable|mimes:pdf',
            'csalarioaportes' => 'nullable|mimes:pdf',
            'fotofrojoasegurado' => 'nullable|mimes:pdf',
            'fotofrojoapoderadocroquis' => 'nullable|mimes:pdf',
            'csalarioaporteslegalizada' => 'nullable|mimes:pdf',
            'finiquito' => 'nullable|mimes:pdf',
        ]);

        $camposArchivos = [
            'poder','avcci','cnacasegurado','ciasegurado','cmatrimonio','cnacconyuge','ciconyuge','cnacjihos','cihijos',
            'denfaccidente','crodomicilio','contrato','ctrabajo','boletapago','egestora','actdatos','resolinvhijos',
            'cunionlibre','cnacimientounionlibre','ciunionlibre','cdivorcio','cdefuncion','recordservicios','dictamencalentenc',
            'anteriordictamen','ccompcotsenasir','cnactreshijos','ctrabajoinsalubre','poderciapoderado','cmeddifuncion',
            'cnactitular','cititular','cestudioshijos','tdeclaherederos','cnacherederos','cideclaherederos','compenzacioncotizacion',
            'contratopensionderivretiro','csalarioaportes','fotofrojoasegurado','fotofrojoapoderadocroquis','csalarioaporteslegalizada',
            'finiquito','infomedicasalud'
        ];

        foreach ($camposArchivos as $campo) {
            $this->manejarArchivo($request, $campo, $requisito, $cliente->id);
        }

        if ($request->filled('numeropoder')) {
            $requisito->update(['numeropoder' => $request->input('numeropoder')]);
        }

        return redirect()->route('admin.asociados.subirdocrequisitospensionderivretiro', $cliente)
                            ->with('info', 'El documento se subió con éxito');
    }
//

    public function index(Request $request)
    {
        $nombreproveedor = $request->get('buscarpor');

        $proveedores = Proveedor::where('proveedor','Like',"%$nombreproveedor%")->simplePaginate(1000);
       

        return view('admin.proveedores.index', compact('proveedores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areas = AreaAccion::select('area')->distinct()->pluck('area');
        $estadoproveedor = [
            'ACTIVO' => 'ACTIVO',
            'INACTIVO' => 'INACTIVO',
        ];
        $departamentos = Departamento::orderBy('departamento')->pluck('departamento', 'id');
        return view('admin.proveedores.create', compact('departamentos', 'estadoproveedor', 'areas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProveedorRequest $request)
{
    $idCiudad = $request->input('ciudad');
    $ciudad = Departamento::findOrFail($idCiudad);
    $ciudadNombre = $ciudad->departamento;

    $areasSeleccionadas = $request->input('area');
    $estadoSeleccionado = $request->input('estadoproveedor');



    foreach ($areasSeleccionadas as $areaNombre) { // Iterar sobre los nombres de las áreas seleccionadas
        $proveedorData = $request->except(['area', '_token']);
        $proveedorData['ciudad'] = $ciudadNombre;
        $proveedorData['area'] = $areaNombre;
        $proveedorData['estadoproveedor'] = $estadoSeleccionado;



        Proveedor::create($proveedorData);
    }

    return redirect()->route('admin.proveedores.index')->with('info', 'Los proveedores se crearon con éxito');
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
    public function edit(Proveedor $proveedore)
{
    // Obtener las áreas asociadas al proveedor
    $areas_proveedor = explode(',', $proveedore->area);
    
    // Obtener las áreas de acciones asociadas al área del proveedor
    $areaacciones_proveedor = AreaAccion::whereIn('area', $areas_proveedor)
                                        ->select('areaaccion')
                                        ->distinct()
                                        ->pluck('areaaccion')
                                        ->toArray();
    
    // Obtener todas las áreas de acciones únicas
    $areaacciones = AreaAccion::select('areaaccion')
                              ->distinct()
                              ->pluck('areaaccion')
                              ->toArray();
    
    // Pasar las áreas de acciones del proveedor a la vista
    return view('admin.proveedores.edit', compact('proveedore', 'areas_proveedor', 'areaacciones', 'areaacciones_proveedor'));
}



    


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proveedor $proveedore)
    {

        $proveedore->update($request->all());

        return redirect()->route('admin.proveedores.index', $proveedore)->with('info', 'El proveedor se actualizó con éxito');
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
    
    public function listacuentaspagar(Proveedor $proveedor, Request $request)
    {
        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;

        $userRole = auth()->user()->getRoleNames()->first(); 
        
        $query = Bateriasubcliente::with(['estadoprogramacionsubcliente', 'documentacionsubcliente', 'programacionsubcliente','informesfinales','pagoservicio','pagoservicioinformefinal','provinfofinal',
            'estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria', 'programacionsubclienteauditoria','informesfinalesauditoria','provinfofinalauditoria',
            'estadoprogramacionsubclientecomun', 'documentacionsubclientecomun', 'programacionsubclientecomun','informesfinalescomun','provinfofinalcomun','tramitesubclienteita','tramitesubclienteauditoria','tramitesubclientecomun'])
            ->whereNotNull('proveedorasignado')
            ->where('preciocompra', '!=', NULL)
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)
            ->where('pagoservicio','<>', 'EXTERNO')
            ->where('proveedorasignado', '<>', 'DIAGNOSTICO MEDICO POR IMAGEN DMI') 
            ->where('proveedorasignado', '<>', 'PROVEEDOR AJENO') 
        ->orderBy('proveedorasignado');

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query->whereHas('proveedorasignado', function ($q) use ($request) {
                $q->where('proveedorasignado', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        $bateriaproveedores = $query->get();
        $grouped = $bateriaproveedores->groupBy(function($item) {
            return $item->proveedorasignado;
        });

        $result = [];
        foreach ($grouped as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {

                    $estadoProgramacion = collect([
                            $item->estadoprogramacionsubcliente, 
                            $item->estadoprogramacionsubclienteauditoria, 
                            $item->estadoprogramacionsubclientecomun
                        ])->filter();
                        
                        $resultadoestado = $estadoProgramacion
                            ->flatMap(function ($estadoprogramacion) { 
                                return $estadoprogramacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();  

                    $programaciones = collect([
                            $item->programacionsubcliente, 
                            $item->programacionsubclienteauditoria, 
                            $item->programacionsubclientecomun
                        ])->filter();
                    
                        $resultadoprog = $programaciones
                            ->flatMap(function ($programacion) { 
                                return $programacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();                    

                    $informesubido = collect([
                            $item->documentacionsubcliente, 
                            $item->documentacionsubclienteauditoria, 
                            $item->documentacionsubclientecomun
                        ])->filter();
                        
                        $resultadoinforme = $informesubido
                            ->flatMap(function ($informe) { 
                                return $informe;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accion', $item->accionnombre)
                    ->first();           

                    $informefinalsubido = collect([
                            $item->informesfinales, 
                            $item->informesfinalesauditoria, 
                            $item->informesfinalescomun
                        ])->filter();
                        
                        $resultadoinformefinal = $informefinalsubido
                            ->flatMap(function ($informefinal) { 
                                return $informefinal;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $provinformes = collect([
                            $item->provinfofinal, 
                            $item->provinfofinalauditoria, 
                            $item->provinfofinalcomun
                        ])->filter();
                        
                        $resultadoprovinformes = $provinformes
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first(); 
                    
                    $provinformes2 = collect([
                            $item->provinfofinal, 
                            $item->provinfofinalauditoria, 
                            $item->provinfofinalcomun
                        ])->filter();
                        
                        $resultadoprovinformes2 = $provinformes2
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('id', $item->provinfofinalid)
                    ->first();

                    $tramitesubcliente = collect([
                            $item->tramitesubclienteita, 
                            $item->tramitesubclienteauditoria, 
                            $item->tramitesubclientecomun
                        ])->filter();
                        
                        $resultadotramitesubcliente = $tramitesubcliente
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();

                    $resultadopagoinformefinal = $item->pagoservicioinformefinal()
                        ->where('provinfofinalid', $item->provinfofinalid)
                        ->where('tipomovimiento', 'EGRESO')
                    ->first();

                    $pagobateria = collect([
                        $item->programacionsubcliente,
                        $item->programacionsubclienteauditoria,
                        $item->programacionsubclientecomun
                    ])->filter();
                    
                    $resultadopago = $pagobateria
                        ->flatMap(fn($pago) => $pago)
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                    ->first();

                    $preciocompra = $item->preciocompra;
                    $pagoservicioinforme = null;

                    if ($resultadopago) {
                        $programacionId = $resultadopago->id;

                        $detallerecibo = Detallerecibo::where('programacionid', $programacionId)
                            ->where('tipomovimiento', 'EGRESO')
                            ->orderByDesc('id')
                            ->first();

                        if ($detallerecibo) {
                            if ($detallerecibo->estado === 'PAGO PROCESADO') {
                                $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                            } elseif ($detallerecibo->estado === 'SALDO PENDIENTE') {
                                $pagoservicioinforme = 'SALDO PENDIENTE';
                                $preciocompra = $detallerecibo->saldo ?? $item->preciocompra;
                            }
                        } else {
                            $pagoservicioinforme = $resultadopago->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : 'PENDIENTE';
                        }
                    } else {
                        $pagoservicioinforme = 'PENDIENTE';
                    }

                $fechaAtencion = $resultadoestado ? $resultadoestado->fechaatencionprogramacion : null;
                $fechaprogramacion = $resultadoprog ? $resultadoprog->fechaasignada : null;
                $idprogramacion = $resultadoprog ? $resultadoprog->id : null;
                $nrofacturaprog = $resultadoprog ? $resultadoprog->nrofactura : null;
                $documentofactura = $resultadoprog ? $resultadoprog->factura : null;
                $codautorizacion = $resultadoprog ? $resultadoprog->codautorizacion : null;
                $informedocumentacion = $resultadoinforme ? $resultadoinforme->created_at->toDateString() : null;
                $informedocumentacionfinal = $resultadoinformefinal ? $resultadoinformefinal->created_at->toDateString() : null;
                $pagoservicioinformefinal = in_array($item->id, [3173, 3178, 3187, 3043]) 
                ? 'PROCESADO' 
                : ($resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null);
                $nrofacturainformefinal = $resultadoprovinformes2 ? $resultadoprovinformes2->nrofactura : null;
                $codautorizacioninfofinal = $resultadoprovinformes2 ? $resultadoprovinformes2->codautorizacion : null;
                $facturainformefinal = $resultadoprovinformes2 ? $resultadoprovinformes2->factura : null;
                $tramiteinformefinal = $resultadoprovinformes2 ? $resultadoprovinformes2->servicio : null;
                $tramitecliente = $resultadotramitesubcliente ? $resultadotramitesubcliente->tramite : null;

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'pagoservicio' => $item->pagoservicio,
                    'preciocompra' => $preciocompra,
                    'clienteitaid' => $item->clienteitaid,
                    'clienteitanombre' => $item->clienteitanombre,
                    'clienteauditoriaid' => $item->clienteauditoriaid,
                    'clienteauditorianombre' => $item->clienteauditorianombre,
                    'clientecomunid' => $item->clientecomunid,
                    'clientecomunnombre' => $item->clientecomunnombre,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechaprogramacion' => $fechaprogramacion,
                    'informedocumentacion' => $informedocumentacion,
                    'informedocumentacionfinal' => $informedocumentacionfinal,
                    'pagoservicioinforme' => $pagoservicioinforme,
                    'pagoservicioinformefinal' => $pagoservicioinformefinal,
                    'idprogramacion' => $idprogramacion,
                    'fechabateria' => $item->fechabateria,
                    'provinfofinalid' => $item->provinfofinalid,
                    'nrofacturaprog' => $nrofacturaprog,
                    'documentofactura' => $documentofactura,
                    'codautorizacion' => $codautorizacion,
                    'nrofacturainformefinal' => $nrofacturainformefinal,
                    'codautorizacioninfofinal' => $codautorizacioninfofinal,
                    'facturainformefinal' => $facturainformefinal,
                    'tramiteinformefinal' => $tramiteinformefinal,
                    'tramitecliente' => $tramitecliente,
                    'prioridad' => $item->prioridad,
                    'estadoaprobacion' => $item->estadoaprobacion,
                ];
            }
            $result[] = [
                'proveedorasignado' => $item->proveedorasignado,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'fechabateria' => $item->fechabateria,
            ];
        }

        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');
        $user = auth()->user()->name;

        $records = DB::table('bateriasubclientes')
            ->selectRaw("
                COALESCE(fechacredito, fechabateria) as fechabateria,  
                SUM(CASE 
                    WHEN precio IS NULL THEN 0
                    ELSE precio 
                END) as total_ingresos,
                SUM(CASE 
                    WHEN preciocompra IS NULL THEN 0
                    ELSE preciocompra 
                END) as total_egresos
            ")
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)
            ->whereNotNull('preciocompra')
            ->whereYear(DB::raw('COALESCE(fechacredito, fechabateria)'), $year)
            ->whereMonth(DB::raw('COALESCE(fechacredito, fechabateria)'), $month)
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('COALESCE(fechacredito, fechabateria)'))
        ->get();

        if ($request->ajax()) {
            return response()->json($records);
        }

        $cuentaspagar = CuentasPagar::with('proveedorServicio')->get();

        $registrosbateria = Bateriasubcliente::where('prioridad', 'CUENTA POR PAGAR')
            ->leftJoin('proveedorinformesfinales', 'bateriasubclientes.provinfofinalid', '=', 'proveedorinformesfinales.id')
            ->leftJoin('informesfinales', function ($join) {
                $join->on(function ($q) {
                    $q->whereColumn('bateriasubclientes.clienteitaid', 'informesfinales.clienteitaid')
                    ->orWhereColumn('bateriasubclientes.clienteauditoriaid', 'informesfinales.clienteauditoriaid')
                    ->orWhereColumn('bateriasubclientes.clientecomunid', 'informesfinales.clientecomunid');
                })
                ->whereColumn('bateriasubclientes.fechabateria', 'informesfinales.fechabateria')
                ->whereColumn('proveedorinformesfinales.servicio', 'informesfinales.servicio')
                ->whereNull('informesfinales.motivoanulacion');
            })
            ->select('bateriasubclientes.*', 'informesfinales.created_at as informe_created_at')
            ->distinct()
            ->with([
                'programacion' => function ($query) {
                    $query->select('id', 'bateriaid', 'fechaasignada', 'nrofactura', 'factura');
                },
                'programacion.documentacion' => function ($query) {
                    $query->select('id', 'programacionid', 'created_at');
                },
                'proveedorinformefinal' => function ($query) {
                    $query->select('id', 'nrofactura', 'factura');
                },
                'clienteita2:id,sucursal',
                'clienteauditoria2:id,sucursal',
                'clientecomun2:id,sucursal',
            ])
        ->get();
        
        foreach ($registrosbateria as $registro) {
            $programacion = \App\Models\Programacionsubcliente::where('bateriaid', $registro->id)
                ->orderBy('id', 'desc')
                ->first();

            if ($programacion) {
                $detallerecibo = \App\Models\Detallerecibo::where('programacionid', $programacion->id)
                    ->where('tipomovimiento', 'EGRESO')
                    ->where('estado', 'SALDO PENDIENTE')
                    ->orderByDesc('created_at')
                    ->first();

                if ($detallerecibo && $detallerecibo->saldo > 0) {
                    $registro->preciocompra = $detallerecibo->saldo;
                }
            }
        }

        $proveedoresServicios = Proveedor::pluck('tipoplanilla', 'proveedor');
        $proveedoresServicioscuenta = Proveedor::pluck('cuenta', 'proveedor');
        $documentosPorFecha = PlanillasPagosGeneradas::select('tipo', 'documento', 'fechapago', 'proveedor')
            ->get()
        ->groupBy('fechapago');

        $cuentasbancos = CuentasBancos::where('estado', 'ACTIVO')->get();


        /* TOTAL DE CAJA INGRESOS Y EGRESOS DE 3000189269 */
        $totalCuenta1Ingreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '3000189269')
                    ->orWhere('nrocuentadestinodeposito', '3000189269')
                    ->orWhere(function ($subquery) {
                        $subquery->where('nrobancodestinoefectivo', '3000189269')
                                ->whereNotNull('nrobancarizacionefectivo');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('tipotransaccion', 'ATC')
                                ->where('nrocuentadestinoatc', '3000189269')
                                ->whereNotNull('nrobancarizacionatc');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('tipotransaccion', 'CHEQUE')
                                ->where('nrocuentadestinocheque', '3000189269')
                                ->whereNotNull('nrobancarizacioncheque');
                    });
            })
            ->where('tipomovimiento', 'INGRESO')
            ->where('estado', '!=', 'ANULADO')
            ->sum(DB::raw("CASE 
                            WHEN tipotransaccion = 'ATC' AND nrobancarizacionatc IS NOT NULL 
                            THEN montototal - descuentoATC 
                            WHEN tipotransaccion = 'EFECTIVO' 
                            THEN montototal + diferenciafavor
                            ELSE montototal 
        END"));

        $totalCuenta1Egreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '3000189269')
                ->orWhere(function ($subquery) {
                    $subquery->where('tipotransaccion', 'CHEQUE')
                                ->where('nrocuentadestinocheque', '3000189269')
                                ->whereNotNull('nrobancarizacioncheque');
                });
            })
            ->where('tipomovimiento', 'EGRESO')
            ->where('estado', '!=', 'ANULADO')
        ->sum('montototal');

        /* TOTAL DE CAJA INGRESOS Y EGRESOS DE 2505314878 */
        $totalCuenta2Ingreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '2505314878')
                    ->orWhere('nrocuentadestinodeposito', '2505314878')
                    ->orWhere(function ($subquery) {
                        $subquery->where('nrobancodestinoefectivo', '2505314878')
                                ->whereNotNull('nrobancarizacionefectivo');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('tipotransaccion', 'ATC')
                                ->where('nrocuentadestinoatc', '2505314878')
                                ->whereNotNull('nrobancarizacionatc');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('tipotransaccion', 'CHEQUE')
                                ->where('nrocuentadestinocheque', '2505314878')
                                ->whereNotNull('nrobancarizacioncheque');
                    });
                })
                ->where('tipomovimiento', 'INGRESO')
                ->where('estado', '!=', 'ANULADO')
                ->sum(DB::raw("CASE 
                    WHEN tipotransaccion = 'ATC' AND nrobancarizacionatc IS NOT NULL 
                    THEN montototal - descuentoATC 
                    WHEN tipotransaccion = 'EFECTIVO' 
                    THEN montototal + diferenciafavor
                    ELSE montototal 
        END"));
        
        $totalCuenta2Egreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '2505314878')
                ->orWhere(function ($subquery) {
                    $subquery->where('tipotransaccion', 'CHEQUE')
                            ->where('nrocuentadestinocheque', '2505314878')
                            ->whereNotNull('nrobancarizacioncheque');
                });
            })
            ->where('tipomovimiento', 'EGRESO')
            ->where('estado', '!=', 'ANULADO')
        ->sum('montototal');

        /* TOTAL DE CAJA INGRESOS Y EGRESOS DE 1031266712 */
        $totalCuenta4Ingreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '1031266712')
                    ->orWhere('nrocuentadestinodeposito', '1031266712')
                    ->orWhere(function ($subquery) {
                        $subquery->where('nrobancodestinoefectivo', '1031266712')
                                ->whereNotNull('nrobancarizacionefectivo');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('tipotransaccion', 'ATC')
                                ->where('nrocuentadestinoatc', '1031266712')
                                ->whereNotNull('nrobancarizacionatc');
                    })
                    ->orWhere(function ($subquery) {
                        $subquery->where('tipotransaccion', 'CHEQUE')
                                ->where('nrocuentadestinocheque', '1031266712')
                                ->whereNotNull('nrobancarizacioncheque');
                    });
            })
            ->where('tipomovimiento', 'INGRESO')
            ->where('estado', '!=', 'ANULADO')
            ->sum(DB::raw("CASE 
                            WHEN tipotransaccion = 'ATC' AND nrobancarizacionatc IS NOT NULL 
                            THEN montototal - descuentoATC 
                            WHEN tipotransaccion = 'EFECTIVO' 
                            THEN montototal + diferenciafavor
                            ELSE montototal 
        END"));

        $totalCuenta4Egreso = DB::table('cajacentral')
            ->where(function ($query) {
                $query->where('nrocuentadestinotransferencia', '1031266712')
                ->orWhere(function ($subquery) {
                    $subquery->where('tipotransaccion', 'CHEQUE')
                                ->where('nrocuentadestinocheque', '1031266712')
                                ->whereNotNull('nrobancarizacioncheque');
                });
            })
            ->where('tipomovimiento', 'EGRESO')
            ->where('estado', '!=', 'ANULADO')
        ->sum('montototal');

        $saldoanteriorcuenta1 = '-174910.55';
        $saldoanteriorcuenta2 = '34508.22';
        $saldoanteriorcuenta4 = '0.00';

        $cuentasConSaldo = [
            '3000189269' => $saldoanteriorcuenta1 + $totalCuenta1Ingreso - $totalCuenta1Egreso,
            '2505314878' => $saldoanteriorcuenta2 + $totalCuenta2Ingreso - $totalCuenta2Egreso,
            '1031266712' => $saldoanteriorcuenta4 + $totalCuenta4Ingreso - $totalCuenta4Egreso,
        ];

        return view('admin.caja.cuentaspagar.listacuentaspagar', compact('registrosbateria','cuentaspagar','year','month',
        'records','usuarioAutenticado','result','proveedor','totalCuenta1Ingreso','totalCuenta1Egreso',
        'totalCuenta2Ingreso','totalCuenta2Egreso','saldoanteriorcuenta1',
        'saldoanteriorcuenta2','saldoanteriorcuenta3','documentosPorFecha','cuentasbancos','proveedoresServicios',
        'proveedoresServicioscuenta','saldoanteriorcuenta4','totalCuenta4Ingreso','totalCuenta4Egreso'));
    }

    public function listacuentascobrar(Cliente $cliente, ClienteAuditoria $clienteauditoria, ClienteComun $clientecomun, Request $request)
    {
        $usuarioAutenticado = auth()->user()->name;
        $esProveedor = $usuarioAutenticado->role ?? null;
        $userRole = auth()->user()->getRoleNames()->first(); 
        $cuentaspagar = CuentasCobrar::all();  

        $query = Bateriasubcliente::with(['estadoprogramacionsubcliente', 
            'documentacionsubcliente', 
            'programacionsubcliente',
            'informesfinales',
            'pagoservicio',
            'pagoservicioinformefinal'])
            ->whereNotNull('clienteitaid')
            ->where('servicio', '<>', 'AJENO') 
        ->orderBy('clienteitanombre');
        
        $query2 = Bateriasubcliente::with(['estadoprogramacionsubclienteauditoria', 
            'documentacionsubclienteauditoria', 
            'programacionsubclienteauditoria',
            'informesfinalesauditoria',
            'pagoservicio',
            'pagoservicioinformefinal'])
            ->whereNotNull('clienteauditoriaid')
            ->where('servicio', '<>', 'AJENO') 
        ->orderBy('clienteauditorianombre');

        $query3 = Bateriasubcliente::with(['estadoprogramacionsubclientecomun', 
            'documentacionsubclientecomun', 
            'programacionsubclientecomun',
            'informesfinalescomun'])
            ->whereNotNull('clientecomunnombre')
            ->where('servicio', '<>', 'AJENO') 
        ->orderBy('clientecomunnombre');

        $query4 = Bateriasubcliente::with(['estadoprogramacionsubcliente', 'documentacionsubcliente', 'programacionsubcliente','informesfinales','pagoservicio','pagoservicioinformefinal','provinfofinal',
            'estadoprogramacionsubclienteauditoria', 'documentacionsubclienteauditoria', 'programacionsubclienteauditoria','informesfinalesauditoria','provinfofinalauditoria',
            'estadoprogramacionsubclientecomun', 'documentacionsubclientecomun', 'programacionsubclientecomun','informesfinalescomun','provinfofinalcomun'])
            ->whereNotNull('proveedorasignado')
            ->where('preciocompra', '!=', NULL)
            ->where('preciocompra', '!=', 0)
            ->where('preciocompra', '!=', 0.00)
            ->where('pagoservicio', '=', 'EXTERNO')
            ->where('proveedorasignado', '<>', 'PROVEEDOR AJENO') 
        ->orderBy('proveedorasignado');

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

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query3->whereHas('clientecomun', function ($q) use ($request) {
                $q->where('clientecomunnombre', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        if ($request->has('buscarporcliente') && $request->buscarporcliente !== '') {
            $query4->whereHas('proveedorasignado', function ($q) use ($request) {
                $q->where('proveedorasignado', 'LIKE', '%' . $request->buscarporcliente . '%');
            });
        }

        $bateriaclientesita = $query->get();
        $grouped = $bateriaclientesita->groupBy(function($item) {
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

            $usuarioRegistro = Cliente::where('id', $items->first()->clienteitaid)
                ->first();

            $usuarioregistro = $usuarioRegistro ? $usuarioRegistro->sucursal : null;
            $clienteitaid = $items->first()->clienteitaid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {
                $estadoProgramacion = $item->estadoprogramacionsubcliente
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                ->first();
                
                $programacionasignada = $item->programacionsubcliente
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                ->first();
                
                $informesubido = $item->documentacionsubcliente
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accion', $item->accionnombre)
                ->first();
                
                $informefinalsubido = $item->informesfinales
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accion', 'INFORME FINAL')
                ->first();

                $resultadopagoinformefinal = $item->pagoservicioinformefinal
                    ->where('provinfofinalid', $item->provinfofinalid)
                    ->where('tipomovimiento', 'INGRESO')
                ->first();

                    $resultadopago = $item->programacionsubcliente
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();
                    
                    if ($resultadopago) {
                        $programacionId = $resultadopago->id;
                        $programacionfechabateria = $resultadopago->fechabateria;
                        $programacioncliente = $resultadopago->clienteitaid;
                        $programacionaccion = $resultadopago->accionnombre;

                        $detallerecibo = Detallerecibo::where('programacionid', $programacionId)
                                                      ->where('tipomovimiento', 'INGRESO')
                                                      ->first();

                        $resultadopagobateria = Bateriasubcliente::where('fechabateria', $programacionfechabateria)
                                                      ->where('accionnombre', $programacionaccion)
                                                      ->where('clienteitaid', $programacioncliente)
                                                      ->first();
                    
                        if ($detallerecibo) {
                            $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                        } else {
                            $pagoservicioinforme = $resultadopagobateria->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : null;
                        }
                    } else {
                        $pagoservicioinforme = null;
                    }

                $fechaAtencion = $estadoProgramacion ? $estadoProgramacion->fechaatencionprogramacion : null;
                $fechaprogramacion = $programacionasignada ? $programacionasignada->fechaasignada : null;
                $informedocumentacion = $informesubido ? $informesubido->created_at->toDateString() : null;
                $informedocumentacionfinal = $informefinalsubido ? $informefinalsubido->created_at->toDateString() : null;
                $pagoservicioinformefinal = in_array($item->id, [5505, 5506, 5507, 5508, 5509, 5510, 5511, 5512, 5513, 5514, 5515, 5516, 5517, 5518, 5519, 5520, 5521, 5522, 5523, 5524, 5525, 5526, 5527, 5528, 5529, 5530, 5531, 5532]) 
                ? 'PROCESADO' 
                : ($resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null);

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'cantidadcuotas' => $item->cantidadcuotas,
                    'precio' => $item->precio,
                    'pagoservicio' => $item->pagoservicio,
                    'preciocompra' => $item->preciocompra,
                    'proveedorasignado' => $item->proveedorasignado,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechaprogramacion' => $fechaprogramacion,
                    'informedocumentacion' => $informedocumentacion,
                    'informedocumentacionfinal' => $informedocumentacionfinal,
                    'clienteitanombre' => $item->clienteitanombre,
                    'fechabateria' => $item->fechabateria,
                    'pagoservicioinforme' => $pagoservicioinforme,
                    'pagoservicioinformefinal' => $pagoservicioinformefinal,
                ];
            }
            $result[] = [
                'clienteitaid' => $clienteitaid,
                'clienteitanombre' => $clienteNombre,
                'fechabateria' => $fechabateria,
                'tramite' => $tramiteNombre,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'usuarioregistro' => $usuarioregistro,
                'pagoservicioinforme' => $pagoservicioinforme,
            ];
        }

        $bateriaclientesauditoria = $query2->get();
        $grouped2 = $bateriaclientesauditoria->groupBy(function($item) {
            return $item->clienteauditorianombre . '|' . $item->fechabateria;
        });
        $result2 = [];
        foreach ($grouped2 as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $fechabateria = explode('|', $key)[1];

            $clienteauditoriaid = $items->first()->clienteauditoriaid;

            $tramites = TramiteSubCliente::where('clienteauditoriaid', $clienteauditoriaid)
                ->where('fechabateria', $fechabateria)
                ->get();
            $tramiteNombre = $tramites->isEmpty() ? ['SIN SERVICIO'] : $tramites->pluck('tramite')->toArray();

            $usuarioRegistro = ClienteAuditoria::where('id', $items->first()->clienteauditoriaid)
                ->first();

            $usuarioregistro = $usuarioRegistro ? $usuarioRegistro->sucursal : null;
            $clienteauditoriaid = $items->first()->clienteauditoriaid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {
                $estadoProgramacion = $item->estadoprogramacionsubclienteauditoria
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();
                
                $programacionasignada = $item->programacionsubclienteauditoria
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();
                
                $informesubido = $item->documentacionsubclienteauditoria
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accion', $item->accionnombre)
                    ->first();
                
                $informefinalsubido = $item->informesfinalesauditoria
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accion', 'INFORME FINAL')
                    ->first();

                $resultadopagoinformefinal = $item->pagoservicioinformefinal
                    ->where('provinfofinalid', $item->provinfofinalid)
                    ->where('tipomovimiento', 'INGRESO')
                ->first();

                $resultadopago = $item->programacionsubclienteauditoria
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();
                    
                    if ($resultadopago) {
                        $programacionId = $resultadopago->id;
                        $programacionfechabateria = $resultadopago->fechabateria;
                        $programacioncliente = $resultadopago->clienteauditoriaid;
                        $programacionaccion = $resultadopago->accionnombre;

                        $detallerecibo = Detallerecibo::where('programacionid', $programacionId)
                                                      ->where('tipomovimiento', 'INGRESO')
                                                      ->first();

                        $resultadopagobateria = Bateriasubcliente::where('fechabateria', $programacionfechabateria)
                                                      ->where('accionnombre', $programacionaccion)
                                                      ->where('clienteauditoriaid', $programacioncliente)
                                                      ->first();
                    
                        if ($detallerecibo) {
                            $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                        } else {
                            $pagoservicioinforme = $resultadopagobateria->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : null;
                        }
                    } else {
                        $pagoservicioinforme = null;
                    }

                $fechaAtencion = $estadoProgramacion ? $estadoProgramacion->fechaatencionprogramacion : null;
                $fechaprogramacion = $programacionasignada ? $programacionasignada->fechaasignada : null;
                $informedocumentacion = $informesubido ? $informesubido->created_at->toDateString() : null;
                $informedocumentacionfinal = $informefinalsubido ? $informefinalsubido->created_at->toDateString() : null;
                $pagoservicioinformefinal = $resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null;

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'cantidadcuotas' => $item->cantidadcuotas,
                    'pagoservicio' => $item->pagoservicio,
                    'preciocompra' => $item->preciocompra,
                    'proveedorasignado' => $item->proveedorasignado,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechaprogramacion' => $fechaprogramacion,
                    'informedocumentacion' => $informedocumentacion,
                    'informedocumentacionfinal' => $informedocumentacionfinal,
                    'clienteauditorianombre' => $item->clienteauditorianombre,
                    'fechabateria' => $item->fechabateria,
                    'pagoservicioinforme' => $pagoservicioinforme,
                    'pagoservicioinformefinal' => $pagoservicioinformefinal,
                ];
            }
            $result2[] = [
                'clienteauditoriaid' => $clienteauditoriaid,
                'clienteauditorianombre' => $clienteNombre,
                'fechabateria' => $fechabateria,
                'tramite' => $tramiteNombre,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'usuarioregistro' => $usuarioregistro,
                'pagoservicioinforme' => $pagoservicioinforme,
            ];
        }
        
        $bateriaclientescomun = $query3->get();
        $grouped3 = $bateriaclientescomun->groupBy(function($item) {
            return $item->clientecomunnombre . '|' . $item->fechabateria;
        });
        $result3 = [];
        foreach ($grouped3 as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $fechabateria = explode('|', $key)[1];

            $clientecomunid = $items->first()->clientecomunid;

            $tramites = TramiteSubCliente::where('clientecomunid', $clientecomunid)
                ->where('fechabateria', $fechabateria)
                ->get();
            $tramiteNombre = $tramites->isEmpty() ? ['SIN SERVICIO'] : $tramites->pluck('tramite')->toArray();

            $usuarioRegistro = ClienteComun::where('id', $items->first()->clientecomunid)
                ->first();

            $usuarioregistro = $usuarioRegistro ? $usuarioRegistro->sucursal : null;
            $clientecomunid = $items->first()->clientecomunid;
        
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {
                $estadoProgramacion = $item->estadoprogramacionsubclientecomun
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();
                
                $programacionasignada = $item->programacionsubclientecomun
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accionnombre', $item->accionnombre)
                    ->first();
                
                $informesubido = $item->documentacionsubclientecomun
                    ->where('fechabateria', $item->fechabateria)
                    ->where('accion', $item->accionnombre)
                    ->first();

                    $resultadopagoinformefinal = $item->pagoservicioinformefinal
                    ->where('provinfofinalid', $item->provinfofinalid)
                    ->where('tipomovimiento', 'INGRESO')
                ->first();

                $resultadopago = $item->programacionsubclientecomun
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();
                    
                    if ($resultadopago) {
                        $programacionId = $resultadopago->id;
                        $programacionfechabateria = $resultadopago->fechabateria;
                        $programacioncliente = $resultadopago->clientecomunid;
                        $programacionaccion = $resultadopago->accionnombre;

                        $detallerecibo = Detallerecibo::where('programacionid', $programacionId)
                                                      ->where('tipomovimiento', 'INGRESO')
                                                      ->first();

                        $resultadopagobateria = Bateriasubcliente::where('fechabateria', $programacionfechabateria)
                                                      ->where('accionnombre', $programacionaccion)
                                                      ->where('clientecomunid', $programacioncliente)
                                                      ->first();
                    
                        if ($detallerecibo) {
                            $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                        } else {
                            $pagoservicioinforme = $resultadopagobateria?->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : null;
                        }
                    } else {
                        $pagoservicioinforme = null;
                    }

                $pagoservicioinformefinal = $resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null;

                $fechaAtencion = $estadoProgramacion ? $estadoProgramacion->fechaatencionprogramacion : null;
                $fechaprogramacion = $programacionasignada ? $programacionasignada->fechaasignada : null;
                $informedocumentacion = $informesubido ? $informesubido->created_at->toDateString() : null;
                $informedocumentacionfinal = $informefinalsubido ? $informefinalsubido->created_at->toDateString() : null;
                $pagoingreso = $informefinalsubido ? $informefinalsubido->estado->toDateString() : null;

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'pagoservicio' => $item->pagoservicio,
                    'cantidadcuotas' => $item->cantidadcuotas,
                    'preciocompra' => $item->preciocompra,
                    'proveedorasignado' => $item->proveedorasignado,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechaprogramacion' => $fechaprogramacion,
                    'informedocumentacion' => $informedocumentacion,
                    'informedocumentacionfinal' => $informedocumentacionfinal,
                    'pagoingreso' => $pagoingreso,
                    'clientecomunnombre' => $item->clientecomunnombre,
                    'fechabateria' => $item->fechabateria,
                    'pagoservicioinforme' => $pagoservicioinforme,
                    'pagoservicioinformefinal' => $pagoservicioinformefinal,
                ];
            }
            $result3[] = [
                'clientecomunid' => $clientecomunid,
                'clientecomunnombre' => $clienteNombre,
                'fechabateria' => $fechabateria,
                'tramite' => $tramiteNombre,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'usuarioregistro' => $usuarioregistro,
                'pagoservicioinforme' => $pagoservicioinforme,
            ];
        }

        $bateriaproveedores = $query4->get();
        $grouped = $bateriaproveedores->groupBy(function($item) {
            return $item->proveedorasignado;
        });

        $result4 = [];
        foreach ($grouped as $key => $items) {
            $clienteNombre = explode('|', $key)[0];
            $estado = 'COMPLETO';
            $accionesConEstado = [];

            foreach ($items as $item) {

                    $estadoProgramacion = collect([
                            $item->estadoprogramacionsubcliente, 
                            $item->estadoprogramacionsubclienteauditoria, 
                            $item->estadoprogramacionsubclientecomun
                        ])->filter();
                        
                        $resultadoestado = $estadoProgramacion
                            ->flatMap(function ($estadoprogramacion) { 
                                return $estadoprogramacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();  

                    $programaciones = collect([
                            $item->programacionsubcliente, 
                            $item->programacionsubclienteauditoria, 
                            $item->programacionsubclientecomun
                        ])->filter();
                    
                        $resultadoprog = $programaciones
                            ->flatMap(function ($programacion) { 
                                return $programacion;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accionnombre', $item->accionnombre)
                    ->first();                    

                    $informesubido = collect([
                            $item->documentacionsubcliente, 
                            $item->documentacionsubclienteauditoria, 
                            $item->documentacionsubclientecomun
                        ])->filter();
                        
                        $resultadoinforme = $informesubido
                            ->flatMap(function ($informe) { 
                                return $informe;
                            })
                            ->where('fechabateria', $item->fechabateria)
                            ->where('accion', $item->accionnombre)
                    ->first();           

                    $informefinalsubido = collect([
                            $item->informesfinales, 
                            $item->informesfinalesauditoria, 
                            $item->informesfinalescomun
                        ])->filter();
                        
                        $resultadoinformefinal = $informefinalsubido
                            ->flatMap(function ($informefinal) { 
                                return $informefinal;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $provinformes = collect([
                            $item->provinfofinal, 
                            $item->provinfofinalauditoria, 
                            $item->provinfofinalcomun
                        ])->filter();
                        
                        $resultadoprovinformes = $provinformes
                            ->flatMap(function ($provinfo) { 
                                return $provinfo;
                            })
                            ->where('fechabateria', $item->fechabateria)
                    ->first();  

                    $resultadopagoinformefinal = $item->pagoservicioinformefinal()
                        ->where('provinfofinalid', $item->provinfofinalid)
                        ->where('tipomovimiento', 'INGRESO')
                    ->first();

                    $pagobateria = collect([
                        $item->programacionsubcliente,
                        $item->programacionsubclienteauditoria,
                        $item->programacionsubclientecomun
                    ])->filter();
                    
                    $resultadopago = $pagobateria
                        ->flatMap(fn($pago) => $pago)
                        ->where('fechabateria', $item->fechabateria)
                        ->where('accionnombre', $item->accionnombre)
                        ->first();
                    
                    if ($resultadopago) {
                        $programacionId = $resultadopago->id;
                        $detallerecibo = Detallerecibo::where('programacionid', $programacionId)
                                                      ->where('tipomovimiento', 'INGRESO')
                                                      ->first();
                    
                        if ($detallerecibo) {
                            $pagoservicioinforme = $detallerecibo->created_at->toDateString();
                        } else {
                            $pagoservicioinforme = $resultadopago->pagoatencion === 'PAGO PROCESADO' ? 'PROCESADO' : null;
                        }
                    } else {
                        $pagoservicioinforme = null;
                    }

                $fechaAtencion = $resultadoestado ? $resultadoestado->fechaatencionprogramacion : null;
                $fechaprogramacion = $resultadoprog ? $resultadoprog->fechaasignada : null;
                $idprogramacion = $resultadoprog ? $resultadoprog->id : null;
                $nrofacturaprog = $resultadoprog ? $resultadoprog->nrofactura : null;
                $informedocumentacion = $resultadoinforme ? $resultadoinforme->created_at->toDateString() : null;
                $informedocumentacionfinal = $resultadoinformefinal ? $resultadoinformefinal->created_at->toDateString() : null;
                $pagoservicioinformefinal = in_array($item->id, [3173, 3178, 3187, 3043]) 
                ? 'PROCESADO' 
                : ($resultadopagoinformefinal ? $resultadopagoinformefinal->created_at->toDateString() : null);
                $nrofacturainformefinal = $resultadoprovinformes ? $resultadoprovinformes->nrofactura : null;

                $accionesConEstado[] = [
                    'id' => $item->id,
                    'accion' => $item->accionnombre,
                    'servicio' => $item->servicio,
                    'precio' => $item->precio,
                    'pagoservicio' => $item->pagoservicio,
                    'preciocompra' => $item->preciocompra,
                    'clienteitaid' => $item->clienteitaid,
                    'clienteitanombre' => $item->clienteitanombre,
                    'clienteauditoriaid' => $item->clienteauditoriaid,
                    'clienteauditorianombre' => $item->clienteauditorianombre,
                    'clientecomunid' => $item->clientecomunid,
                    'clientecomunnombre' => $item->clientecomunnombre,
                    'fechaasignada' => $item->fechaasignada,
                    'created_at' => $item->created_at,
                    'fechaatencionprogramacion' => $fechaAtencion,
                    'fechaprogramacion' => $fechaprogramacion,
                    'informedocumentacion' => $informedocumentacion,
                    'informedocumentacionfinal' => $informedocumentacionfinal,
                    'pagoservicioinforme' => $pagoservicioinforme,
                    'pagoservicioinformefinal' => $pagoservicioinformefinal,
                    'idprogramacion' => $idprogramacion,
                    'fechabateria' => $item->fechabateria,
                    'provinfofinalid' => $item->provinfofinalid,
                    'nrofacturaprog' => $nrofacturaprog,
                    'nrofacturainformefinal' => $nrofacturainformefinal,
                ];
            }
            $result4[] = [
                'proveedorasignado' => $item->proveedorasignado,
                'estado' => $estado,
                'acciones' => $accionesConEstado,
                'fechabateria' => $item->fechabateria,
            ];
        }

        return view('admin.caja.cuentascobrar.listacuentascobrar', compact('cuentaspagar','usuarioAutenticado',
            'result', 'cliente','result2', 'clienteauditoria','result3', 'clientecomun','result4'));
    }
}
