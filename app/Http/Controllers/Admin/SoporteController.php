<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SoporteTecnico;

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

        // Redirigir con mensaje de éxito
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
            'soporteimagen1' => 'required|image|mimes:jpeg,png,jpg,gif|max:8192', // Imagen 1 obligatoria
            'soporteimagen2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8192', // Imagen 2 opcional
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

        return redirect()->route('admin.soporte.review')->with('success', 'Solicitud atendida correctamente.');
    }
}
