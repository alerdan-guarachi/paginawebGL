<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use App\Models\Partners;
use App\Models\Assistances;
use Illuminate\Support\Facades\File;

class PartnersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
{
    $partners = Partners::all();
    return view('partners.index', compact('partners'));
}

    public function create()
    {
        return view('partners.create');
    }

    public function store(Request $request)
{
    $persona = Partners::create($request->all());

    // Generar un código único
    $codigo = Str::random(10);
    $persona->code_qr = $codigo;
    $persona->save();

    // Definir la carpeta donde se guardarán los QR
    $folderPath = public_path('code');

    // Crear la carpeta si no existe
    if (!File::exists($folderPath)) {
        File::makeDirectory($folderPath, 0755, true);
    }

    // URL codificada en el QR
    $url = route('partners.show', $codigo);

    // Ruta completa del archivo QR
    $qrPath = $folderPath . '/' . $codigo . '.png';

    // Generar y guardar el QR
    QrCode::format('png')->size(300)->generate($url, $qrPath);

    return view('partners.create', compact('persona', 'url'));
}

    public function show($codigo)
{
    $persona = Partners::where('code_qr', $codigo)->firstOrFail();

    if (request()->has('scanner') && request('scanner') == '1') {
    $reason = request()->input('reason', 'REUNIÓN'); 
    $dateReason = request()->input('date_reason', now()->toDateString());

    $attendance = Assistances::create([
        'partner_id' => $persona->id,
        'partner_name' => $persona->name,
        'partner_last_name' => $persona->last_name,
        'date_attendance' => now()->toDateString(),
        'time_attendance' => now()->toTimeString(),
        'reason' => $reason,
        'date_reason' => $dateReason, // Nuevo campo
    ]);

    return response()->json([
        'status' => 'Asistencia registrada',
        'partner_name' => $attendance->partner_name,
        'partner_last_name' => $attendance->partner_last_name,
        'reason' => $attendance->reason,
        'date_reason' => $attendance->date_reason
    ]);
}


    // Si es cámara normal → mostrar información
    return view('partners.show', compact('persona'));
}


public function scanner()
{
    return view('partners.scanner');
}



}
