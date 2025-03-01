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
use App\Models\Proveedoresservicios;
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
use App\Models\DetalleOrdenVenta;
use App\Models\OrdenVenta;
use App\Models\NumerosCuenta;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use function Ramsey\Uuid\v1;

class OrdenVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:admin.users.index')->only('index');
    }

    public function index(ClienteBanco $clientebanco, Request $request)
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
        $grouped = $programacionclientes->groupBy(function ($item) {
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

        return view('admin.ordenes.ordenesventa.index', compact('empresaUsuario', 'clientebancoid', 'documentosCount', 'resultadosCount', 'userRole', 'esProveedor', 'usuarioAutenticado', 'completosCount', 'incompletosCount', 'proveedores', 'result', 'clientebanco', 'fechas', 'aprobaciones'));
    }

    public function create(ClienteBanco $clientebanco)
    {
        $opcionesCuentas = NumerosCuenta::pluck('nombrecuenta', 'nombrecuenta');
        $usuariosPruebaCredito = Proveedoresservicios::pluck('razonsocial', 'id');

        $ultimaFechaBateria = Bateriasubcliente::where('clienteid', $clientebanco->id)
            ->orderBy('fechabateria', 'desc')
            ->first()
            ->fechabateria ?? null;

        $bateriasubclientes = $ultimaFechaBateria
            ? Bateriasubcliente::where('clienteid', $clientebanco->id)
            ->where('fechabateria', $ultimaFechaBateria)
            ->get()
            : collect();

        $total = $bateriasubclientes->sum('precio');
        $asociado = $clientebanco->asociado;
        $clientebanconombre = $clientebanco->nombrecompleto;

        $empresaDatos = [
            'nit' => '310634022',
            'direccion_santa_cruz' => 'AV. RENE MORENO NRO 484 ESQ. ANA BARBA',
            'direccion_cochabamba' => 'CALLE LANZA NRO 940 ENTRE AV. RAMON RIVERO Y ORURO',
            'telefonos' => '65045401 - 4507269 - 3259385'
        ];

        return view('admin.ordenes.ordenesventa.create', compact('opcionesCuentas', 'bateriasubclientes', 'total', 'usuariosPruebaCredito', 'asociado', 'clientebanconombre', 'empresaDatos', 'clientebanco'));
    }

    public function crearNotadeVenta(ClienteBanco $clientebanco, Request $request)
    {
        // Buscar el registro de la cuenta por el nombre de la cuenta
        $cuenta = NumerosCuenta::where('nombrecuenta', $request->nombrecuenta)->first();

        // Verificar si la cuenta existe y devolver JSON si es una solicitud AJAX
        if ($cuenta) {
            if ($request->ajax()) {
                return response()->json([
                    'banco' => $cuenta->banco,
                    'numerocuenta' => $cuenta->numerocuenta,
                    'titularcuenta' => $cuenta->titularcuenta,
                ]);
            }
        }

        // Obtener nombre completo del cliente
        $clientebanconombre = $clientebanco->nombrecompleto;

        // Obtener la última fecha de batería del cliente
        $ultimaFechaBateria = Bateriasubcliente::where('clienteid', $clientebanco->id)
            ->orderBy('fechabateria', 'desc')
            ->first()->fechabateria;

        // Obtener las acciones asociadas a la última fecha de batería
        $bateriasubclientes = Bateriasubcliente::where('clienteid', $clientebanco->id)
            ->where('fechabateria', $ultimaFechaBateria)
            ->get();

        // Calcular el total de los precios
        $total = $bateriasubclientes->sum('precio');

        // Obtener el asociado relacionado con el ClienteBanco usando el asociadoid
        $asociado = $clientebanco->asociado;

        // Obtener el usuario autenticado
        $usuario = Auth::user();

        // Obtener los datos del personal asociado al usuario autenticado
        $personal = Proveedoresservicios::where('usuarioid', $usuario->id)->first();

        // Verificar si se encontró el asociado
        if (!$asociado) {
            return redirect()->back()->with('error', 'No se encontró el asociado relacionado.');
        }

        // Pasar datos a la vista de creación de nota de venta
        return view('admin.ordenes.ordenesventa.create', compact('bateriasubclientes', 'total', 'clientebanconombre', 'asociado', 'personal'));
    }

    public function generarPDFNotaDeVenta(ClienteBanco $clientebanco, Request $request)
    {
        // Verificar si los valores se reciben correctamente
        Log::info('Datos de cuenta recibidos en generarPDFNotaDeVenta:', [
            'banco' => $request->input('banco'),
            'numerocuenta' => $request->input('numerocuenta'),
            'titularcuenta' => $request->input('titularcuenta')
        ]);

        // Cargar las opciones de cuentas y usuarios de prueba para crédito
        $opcionesCuentas = NumerosCuenta::pluck('nombrecuenta', 'nombrecuenta');
        $usuariosPruebaCredito = Proveedoresservicios::pluck('razonsocial', 'id');

        // Obtener la última fecha de batería y los registros correspondientes
        $ultimaFechaBateria = Bateriasubcliente::where('clienteid', $clientebanco->id)
            ->orderBy('fechabateria', 'desc')
            ->first()
            ->fechabateria ?? null;

        $bateriasubclientes = $ultimaFechaBateria
            ? Bateriasubcliente::where('clienteid', $clientebanco->id)
            ->where('fechabateria', $ultimaFechaBateria)
            ->get()
            : collect();

        // Calcular el total de los precios de baterías
        $total = $bateriasubclientes->sum('precio');
        $descuento = $request->input('descuento', 0);
        $montototal = $total - $descuento;

        // Obtener el asociado relacionado y el nombre completo del cliente
        $asociado = $clientebanco->asociado;
        $clientebanconombre = $clientebanco->nombrecompleto;

        // Obtener el nombre completo de usuario que aprueba el crédito directamente de la colección
        $usuarioAproCredito = $usuariosPruebaCredito[$request->input('usuarioaprocredito')] ?? 'No especificado';

        // Datos estáticos de la empresa para la generación del PDF
        $empresaDatos = [
            'nit' => '310634022',
            'direccion_santa_cruz' => 'AV. RENE MORENO NRO 484 ESQ. ANA BARBA',
            'direccion_cochabamba' => 'CALLE LANZA NRO 940 ENTRE AV. RAMON RIVERO Y ORURO',
            'telefonos' => '65045401 - 4507269 - 3259385'
        ];

        // Crear el nuevo registro de la orden de venta en la base de datos
        $ordenVenta = OrdenVenta::create([
            'modalidadpago' => $request->input('modalidadpago'),
            'formapago' => $request->input('formapago'),
            'fechapago' => $request->input('fechapago'),
            'clienteasociado' => $asociado->asociado ?? 'No disponible',
            'personalcliente' => $clientebanconombre ?? 'No disponible',
            'sucursal' => $request->input('sucursal', 'No especificada'),
            'detalle' => $request->input('detalle'),
            'montoneto' => $total,
            'descuento' => $descuento,
            'montototal' => $montototal,
            'usuarioaprocredito' => $usuarioAproCredito,
            'usuarioid' => auth()->user()->id,
            'usuarioregistro' => auth()->user()->name,
        ]);

        // Registro en `DetalleOrdenVenta` para cada `bateriasubcliente`
        foreach ($bateriasubclientes as $bateriasubcliente) {
            // Cálculo del descuento individual
            $proporcionDescuento = ($bateriasubcliente->precio / $total) * $descuento;
            $preciototal = $bateriasubcliente->precio - $proporcionDescuento;

            DetalleOrdenVenta::create([
                'idordenventa' => $ordenVenta->id,
                'detalle' => $bateriasubcliente->accionnombre,
                'clienteasociado' => $asociado->asociado ?? 'No disponible',
                'personalcliente' => $clientebanconombre ?? 'No disponible',
                'sucursal' => $request->input('sucursal', 'No especificada'),
                'preciounitario' => $bateriasubcliente->precio,
                'descuento' => NULL,
                'preciototal' => NULL,
                'usuarioid' => auth()->user()->id,
                'usuarioregistro' => auth()->user()->name,
            ]);
        }

        // Datos del formulario para el PDF
        $pdfData = [
            'idordenventa' => $ordenVenta->id,
            'opcionesCuentas' => $opcionesCuentas,
            'bateriasubclientes' => $bateriasubclientes,
            'total' => $total,
            'usuariosPruebaCredito' => $usuariosPruebaCredito,
            'asociado' => $asociado,
            'clientebanconombre' => $clientebanconombre,
            'empresaDatos' => $empresaDatos,
            'modalidadpago' => $request->input('modalidadpago'),
            'formapago' => $request->input('formapago'),
            'fechapago' => $request->input('fechapago'),
            'salida' => $request->input('salida'),
            'destino' => $request->input('destino'),
            'descuento' => $request->input('descuento'),
            'observaciones' => $request->input('observaciones'),
            'banco' => $request->input('banco'),
            'numerocuenta' => $request->input('numerocuenta'),
            'titularcuenta' => $request->input('titularcuenta'),
        ];

        // Registrar datos de pdfData
        Log::info('Datos de pdfData para el PDF:', $pdfData);

        // Generar el PDF utilizando la vista `descargarPDFordenVenta`
        $pdf = PDF::loadView('admin.ordenes.ordenesventa.descargarPDFordenVenta', $pdfData);

        // Crear el nombre de archivo con el prefijo `orden_venta_`
        $nombreArchivo = 'orden_venta_' . Str::slug($asociado->asociado . '_' . $clientebanconombre) . '.pdf';

        // Descargar el PDF usando el nombre de archivo personalizado
        return $pdf->download($nombreArchivo);
    }
}
