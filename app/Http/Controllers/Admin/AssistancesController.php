<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use App\Models\Partners;
use App\Models\Assistances;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class AssistancesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $query = Assistances::query();

        // Filtrar por reason
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        // Filtrar por date_reason
        if ($request->filled('date_reason')) {
            $query->where('date_reason', $request->date_reason);
        }

        $assistances = $query->orderBy('date_attendance', 'desc')->get();

        // Obtener lista de motivos únicos para el select
        $reasons = Assistances::select('reason')->distinct()->pluck('reason');

        return view('assistances.index', compact('assistances', 'reasons'));
    }

    public function export(Request $request)
{
    $query = Assistances::query();

    if ($request->filled('reason')) {
        $query->where('reason', $request->reason);
    }

    if ($request->filled('date_reason')) {
        $query->where('date_reason', $request->date_reason);
    }

    $assistances = $query->orderBy('date_attendance', 'desc')->get();

    $filename = 'assistances_' . date('Ymd_His') . '.csv';
    $columns = ['id', 'partner_id', 'partner_name', 'partner_last_name', 'reason', 'date_reason', 'date_attendance', 'time_attendance'];

    $callback = function() use ($assistances, $columns) {
        $file = fopen('php://output', 'w');

        // Agregar BOM UTF-8 para Excel
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        // Encabezados
        // Usar ; como delimitador para Excel
fputcsv($file, $columns, ';');

foreach ($assistances as $row) {
    $data = [];
    foreach ($columns as $column) {
        $data[] = mb_convert_encoding($row->$column, 'UTF-8', 'UTF-8');
    }
    fputcsv($file, $data, ';');
}

        fclose($file);
    };

    return Response::stream($callback, 200, [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ]);
}

}
