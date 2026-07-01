<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ArchivosController extends Controller
{
    /* public function __construct()
    {
        $this->middleware('can:admin.codigo.index')->only('index');
    } */

    public function index()
    {
        return redirect()->route('admin.archivos.explorador');
    }

    public function explorador($ruta = null)
    {
        $basePath = public_path('tramitesclientesita');

        $rutaActual = $ruta
            ? $basePath . DIRECTORY_SEPARATOR . $ruta
            : $basePath;

        if (!File::exists($rutaActual)) {
            abort(404, 'Carpeta no encontrada');
        }

        $carpetas = File::directories($rutaActual);
        $archivos = File::files($rutaActual);

        return view('admin.archivos.explorador', compact(
            'carpetas',
            'archivos',
            'ruta'
        ));
    }
}
