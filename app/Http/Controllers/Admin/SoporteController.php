<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SoporteTecnico;
use App\Notifications\SoportetecnicoNotification;
use App\Notifications\SoportetecnicorespuestaNotification;
use App\Models\User;
use App\Models\PermisoCodigo;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class SoporteController extends Controller
{
    /**
     * Constructor para aplicar middleware de permisos.
     */
    public function __construct()
    {
        $this->middleware('can:admin.soporte.index');
    }

    /**
     * Muestra la vista principal de Caja.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /* $solicitudes = SoporteTecnico::where('usuariosolicitante', auth()->user()->name)
            ->orderBy('id', 'desc') // De mayor a menor ID
            ->get();

        // Obtener las solicitudes del usuario autenticado
        $solicitudes = SoporteTecnico::where('usuariosolicitante', auth()->user()->name)
            ->orderBy('created_at', 'desc')
            ->get(); */

        $solicitudesPendientes = SoporteTecnico::where('usuariosolicitante', auth()->user()->name)
            ->where('estado', 'Pendiente')
            ->orderBy('id', 'desc')
            ->get();

        $solicitudesAtendidos = SoporteTecnico::where('usuariosolicitante', auth()->user()->name)
            ->where('estado', 'Atendido')
            ->orderBy('id', 'desc')
            ->get();

        // Retornar la vista junto con las solicitudes
        return view('admin.soporte.index', compact('solicitudesPendientes', 'solicitudesAtendidos'));
    }

    public function store(Request $request)
    {
        // Validar los datos entrantes
        $request->validate([
            'motivosolicitud' => 'required|string|max:512',
            'nivelprioridad' => 'required|string|max:45',
            'motivoimagen1' => 'required|image|mimes:jpeg,png,jpg,gif|max:8192', // Imagen 1 requerida
            'motivoimagen2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8192', // Imagen 2 opcional
        ]);

        // Crear nueva solicitud
        $solicitud = new SoporteTecnico();
        $solicitud->usuariosolicitante = auth()->user()->name;
        $solicitud->motivosolicitud = $request->motivosolicitud;
        $solicitud->nivelprioridad = $request->nivelprioridad;

        // Crear carpeta de destino en public si no existe
        $folderPath = public_path('soporte-imagenes');
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        // Guardar las imágenes con un nombre único
        if ($request->hasFile('motivoimagen1')) {
            $imagen1 = $request->file('motivoimagen1');
            $filename1 = auth()->user()->name . 'img1' . now()->format('Ymd_His') . '.' . $imagen1->getClientOriginalExtension();
            $imagen1->move($folderPath, $filename1);
            $solicitud->motivoimagen1 = 'soporte-imagenes/' . $filename1;
        }

        if ($request->hasFile('motivoimagen2')) {
            $imagen2 = $request->file('motivoimagen2');
            $filename2 = auth()->user()->name . 'img2' . now()->format('Ymd_His') . '.' . $imagen2->getClientOriginalExtension();
            $imagen2->move($folderPath, $filename2);
            $solicitud->motivoimagen2 = 'soporte-imagenes/' . $filename2;
        }

        $solicitud->estado = 'Pendiente'; // Estado inicial
        $solicitud->save();

        if ($solicitud) {
                $usuariosNotificar = User::whereIn('id', [3])->get();
                foreach ($usuariosNotificar as $usuarioDestino) {
                    $usuarioDestino->notify(new SoportetecnicoNotification($solicitud));
                }
        }
        return redirect()->route('admin.soporte.index')->with('success', '¡Solicitud registrada exitosamente!');
    }
    /* public function historial()
    {
        $solicitudes = SoporteTecnico::where('usuariosolicitante', auth()->user()->name)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.soporte.index', compact('solicitudes'));
    } */
    public function review()
    {
        // Pendientes de menor a mayor ID
        $pendientes = SoporteTecnico::where('estado', 'Pendiente')
            ->orderBy('id', 'asc')
            ->get();

        // Atendidos - de mayor a menor ID
        $atendidos = SoporteTecnico::where('estado', 'Atendido')
            ->orderBy('id', 'desc') // Cambia 'updated_at' por 'id' si deseas por id
            ->get();

        /* // Pendientes
        $pendientes = SoporteTecnico::where('estado', 'Pendiente')
            ->orderBy('created_at', 'desc')
            ->get();

        // Atendidos
        $atendidos = SoporteTecnico::where('estado', 'Atendido')
            ->orderBy('updated_at', 'desc')
            ->get(); */

        return view('admin.soporte.review', compact('pendientes', 'atendidos'));
    }

    public function atender(Request $request, $id)
    {
        $request->validate([
            'descripcionatendida' => 'required|string|max:512',
            'soporteimagen1' => 'required|image|mimes:jpeg,png,jpg,gif|max:8192',
            'soporteimagen2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8192',
        ]);

        $soporte = SoporteTecnico::findOrFail($id);
        $soporte->usuariosoporte = auth()->user()->name;
        $soporte->descripcionatendida = $request->descripcionatendida;

        // Carpeta para imágenes
        $folderPath = public_path('soporte-imagenes');
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        // Guardar imágenes
        if ($request->hasFile('soporteimagen1')) {
            $image1 = $request->file('soporteimagen1');
            $image1Name = 'atencion_' . auth()->user()->name . 'img1' . now()->format('Ymd_His') . '.' . $image1->getClientOriginalExtension();
            $image1->move($folderPath, $image1Name);
            $soporte->soporteimagen1 = 'soporte-imagenes/' . $image1Name;
        }

        if ($request->hasFile('soporteimagen2')) {
            $image2 = $request->file('soporteimagen2');
            $image2Name = 'atencion_' . auth()->user()->name . 'img2' . now()->format('Ymd_His') . '.' . $image2->getClientOriginalExtension();
            $image2->move($folderPath, $image2Name);
            $soporte->soporteimagen2 = 'soporte-imagenes/' . $image2Name;
        }

        $soporte->estado = 'Atendido';
        $soporte->save();
        
        if ($soporte) {
            $usuariosNotificar = User::where('name', $soporte->usuariosolicitante)->get();
            foreach ($usuariosNotificar as $usuarioDestino) {
                $usuarioDestino->notify(new SoportetecnicorespuestaNotification($soporte));
            }
        }

        return redirect()->route('admin.soporte.review')->with('success', 'Solicitud atendida correctamente.');
    }

    public function solicitudcodigo()
    {
        $nombreusuario = auth()->user()->name;
        $registroscodigos = PermisoCodigo::where('usuarioSolicitante', $nombreusuario)
                          ->orderBy('created_at','desc')
                          ->simplePaginate(5);

        $permisos = PermisoCodigo::all();

        $usuarios = User::where('estado', 'ACTIVO')
        ->whereHas('roles', function ($query) {
            $query->whereIn('name', [
                'ADMINISTRADOR',
                'OPERATIVO',
                'CONTABLE',
                'PASANTE',
                'ASISTENTE ADMINISTRATIVO',
                'SUPERVISOR PRESTACIONES',
                'EJECUTIVO PRESTACIONES'
            ]);
        })
        ->orderBy('name')
        ->pluck('name', 'id');

        $user = auth()->user();
        $roles = $user->roles->pluck('name')->toArray();
   
        if (in_array('ADMINISTRADOR', $roles) || in_array('MAESTRO', $roles)) {
            $permisosFiltrados = [
                'admin.asociados.crearbateriaclienteita',
                'admin.ingreso.index',
                'admin.asociados.crearbateriaclienteauditoria',
                'admin.caja.ingresos.concederdescuentosingresos',
                'admin.caja.ingresos.cambiarfecharegistro',
                'admin.caja.egresos.cambiarfecharegistro',
                'admin.inventario.cambiarstockinventario',
                'admin.tramites.cambiarfechaprestaciones',
                'admin.tramites.editararchivoprestaciones',
                'admin.tramites.continuidadtramiteprestaciones',
                'admin.facturasegreso.cambiarrazonsocial',
                'admin.tramites.index'
            ];
        } elseif (in_array('EJECUTIVO PRESTACIONES', $roles)) {
            $permisosFiltrados = [
                'admin.tramites.cambiarfechaprestaciones',
                'admin.tramites.editararchivoprestaciones',
                'admin.tramites.continuidadtramiteprestaciones',
                'admin.tramites.index'
            ];
        } elseif (in_array('CONTABLE', $roles)) {
            $permisosFiltrados = [
                'admin.asociados.crearbateriaclienteita',
                'admin.asociados.crearbateriaclienteauditoria',
                'admin.caja.ingresos.concederdescuentosingresos',
                'admin.caja.ingresos.cambiarfecharegistro',
                'admin.caja.egresos.cambiarfecharegistro',
                'admin.ingreso.index',
                'admin.facturasegreso.cambiarrazonsocial',
                'admin.inventario.cambiarstockinventario'
            ];

        } else {
            $permisosFiltrados = [];
        }

        $permisosSolicitados = Permission::whereIn('name', $permisosFiltrados)
            ->get()
            ->mapWithKeys(function ($permiso) {
                $nombresPersonalizados = [
                    'admin.asociados.crearbateriaclienteita' => 'CREAR BATERIA CLIENTE ITA',
                    'admin.asociados.crearbateriaclienteauditoria' => 'CREAR BATERIA CLIENTE AUDITORIA',
                    'admin.caja.ingresos.concederdescuentosingresos' => 'CONCEDER DESCUENTO INGRESO',
                    'admin.caja.ingresos.cambiarfecharegistro' => 'CAMBIAR FECHA DE CAJA INGRESO',
                    'admin.caja.egresos.cambiarfecharegistro' => 'CAMBIAR FECHA DE CAJA EGRESO',
                    'admin.ingreso.index' => 'DESBLOQUEAR CAJA',
                    'admin.inventario.cambiarstockinventario' => 'CAMBIAR STOCK DE INVENTARIO',
                    'admin.tramites.cambiarfechaprestaciones' => 'MODIFICAR FECHA DE PROCEDIMIENTO TRAMITE',
                    'admin.tramites.editararchivoprestaciones' => 'EDITAR ARCHIVO DE PROCEDIMIENTO TRAMITE',
                    'admin.tramites.continuidadtramiteprestaciones' => 'DAR CONTINUIDAD DE PROCEDIMIENTO TRAMITE',
                    'admin.facturasegreso.cambiarrazonsocial' => 'MODIFICAR RAZON SOCIAL DE FACTURAS IMPUESTOS',
                    'admin.tramites.index' => 'DESBLOQUEAR SECCIÓN DE PRESTACIONES'
                ];

            return [$permiso->name => $nombresPersonalizados[$permiso->name] ?? $permiso->name];
        });

        $descripcionesPermisos = [
            'admin.asociados.crearbateriaclienteita' => 'CREAR BATERIA CLIENTE ITA',
            'admin.asociados.crearbateriaclienteauditoria' => 'CREAR BATERIA CLIENTE AUDITORIA',
            'admin.caja.ingresos.concederdescuentosingresos' => 'CONCEDER DESCUENTO INGRESO',
            'admin.caja.ingresos.cambiarfecharegistro' => 'CAMBIAR FECHA DE CAJA INGRESO',
            'admin.caja.egresos.cambiarfecharegistro' => 'CAMBIAR FECHA DE CAJA EGRESO',
            'admin.ingreso.index' => 'DESBLOQUEAR CAJA',
            'admin.inventario.cambiarstockinventario' => 'CAMBIAR STOCK DE INVENTARIO',
            'admin.tramites.cambiarfechaprestaciones' => 'MODIFICAR FECHA DE PROCEDIMIENTO TRAMITE',
            'admin.tramites.editararchivoprestaciones' => 'EDITAR ARCHIVO DE PROCEDIMIENTO TRAMITE',
            'admin.tramites.continuidadtramiteprestaciones' => 'DAR CONTINUIDAD DE PROCEDIMIENTO TRAMITE',
            'admin.facturasegreso.cambiarrazonsocial' => 'MODIFICAR RAZON SOCIAL DE FACTURAS IMPUESTOS',
            'admin.tramites.index' => 'DESBLOQUEAR SECCIÓN DE PRESTACIONES'
        ];

        $codigoGenerado = strtoupper(Str::random(7));

        return view('admin.soporte.solicitudcodigo', compact('registroscodigos','permisos', 'usuarios', 'permisosSolicitados', 'codigoGenerado', 'descripcionesPermisos'));
    }

    public function guardarsolicitudcodigo(Request $request)
    {
        $request->validate([
            'permisoSolicitado' => 'required',
            'permisoSolicitadoNombre'=> 'required',
            'fechaSolicitada' => 'required|date',
            'motivo' => 'required',
        ]);

        $user = auth()->user();

        $permiso = new PermisoCodigo();
        $permiso->usuarioSolicitante = $user->name;
        $permiso->usuarioAutorizador = null;
        $permiso->codigo = null;
        $permiso->fechaSolicitada = $request->fechaSolicitada;
        $permiso->tiempoLimite = $request->tiempoLimiteMinutos ?? 1;
        $permiso->permisoSolicitado = $request->permisoSolicitadoNombre;
        $permiso->motivo = $request->motivo;
        $permiso->clienteid = $request->clienteid ?? 0;
        $permiso->horaActivacion = null;
        $permiso->estado = 'solicitado';
        $permiso->save();

        $nombresPersonalizados = [
            'admin.asociados.crearbateriaclienteita' => 'CREAR BATERIA CLIENTE ITA',
            'admin.asociados.crearbateriaclienteauditoria' => 'CREAR BATERIA CLIENTE AUDITORIA',
            'admin.caja.ingresos.concederdescuentosingresos' => 'CONCEDER DESCUENTO INGRESO',
            'admin.caja.ingresos.cambiarfecharegistro' => 'CAMBIAR FECHA DE CAJA INGRESO',
            'admin.caja.egresos.cambiarfecharegistro' => 'CAMBIAR FECHA DE CAJA EGRESO',
            'admin.ingreso.index' => 'DESBLOQUEAR CAJA',
            'admin.inventario.cambiarstockinventario' => 'CAMBIAR STOCK DE INVENTARIO',
            'admin.tramites.cambiarfechaprestaciones' => 'MODIFICAR FECHA DE PROCEDIMIENTO TRAMITE',
            'admin.tramites.editararchivoprestaciones' => 'EDITAR ARCHIVO DE PROCEDIMIENTO TRAMITE',
            'admin.tramites.continuidadtramiteprestaciones' => 'DAR CONTINUIDAD DE PROCEDIMIENTO TRAMITE',
            'admin.facturasegreso.cambiarrazonsocial' => 'MODIFICAR RAZON SOCIAL DE FACTURAS IMPUESTOS',
            'admin.tramites.index' => 'DESBLOQUEAR SECCIÓN DE PRESTACIONES'
        ];

        $permisoTexto = $nombresPersonalizados[$request->permisoSolicitado] ?? $request->permisoSolicitado;

        $BOT_TOKEN = '8756856989:AAFwayjLmfVPFu0poFX0G1pnTnifmSSVYQY';
        $CHAT_ID = '-1003947071401';

        $fecha = Carbon::parse($request->fecha);
        $diaSemana = ucfirst($fecha->translatedFormat('l'));
        $fechaFormateada = $fecha->translatedFormat('d F Y');

        $mensaje = "*📍 SOLICITUD DE CÓDIGO*\n\n"
            . "*ID Solicitud:* {$permiso->id}\n"
            . "*Solicitante:* {$user->name}\n"
            . "*Fecha:* {$request->fechaSolicitada}\n"
            . "*Permiso:* {$permisoTexto}\n"
            . "*Motivo:* {$request->motivo}";

        Http::post("https://api.telegram.org/bot{$BOT_TOKEN}/sendMessage", [
            'chat_id' => $CHAT_ID,
            'text' => $mensaje,
            'parse_mode' => 'Markdown'
        ]);

        return redirect()->back()->with('info', 'Solicitud enviada correctamente');
    }
}
