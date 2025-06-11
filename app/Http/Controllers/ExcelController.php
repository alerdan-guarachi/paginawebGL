<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CustomImport;
use Illuminate\Support\Facades\Session;

class ExcelController extends Controller
{

    public function upload(Request $request) 
{
    // Validar archivo
    $request->validate([
        'archivo' => 'required|file|mimes:csv,txt'
    ]);

    // Guardar archivo
    $archivo = $request->file('archivo');
    $ruta = $archivo->storeAs('uploads', time().'_'.$archivo->getClientOriginalName());
    $rutaCompleta = storage_path('app/' . $ruta);

    // Definir qué columnas deseas extraer (basado en índices de un CSV)
    $indicesDeseados = [0,1,2,3,4,5,6,7,8,9,10];

    // Variables para almacenar datos
    $datos = [];
    $total = 0;
    $encabezados = [];

    if (($gestor = fopen($rutaCompleta, "r")) !== false) {
        // Leer encabezados
        $encabezados = fgetcsv($gestor, 1000, ";"); 

        // Leer filas del CSV
        while (($fila = fgetcsv($gestor, 1000, ";")) !== false) {
            $filaFiltrada = [];
            foreach ($indicesDeseados as $indice) {
                $filaFiltrada[] = isset($fila[$indice]) ? $fila[$indice] : null;
            }

            // Sumar valores de la columna 8 si la columna 4 es "Abono Cta por ACH"
            if (isset($fila[3]) && trim($fila[3]) === "Abono Cta por ACH") {
                $total += isset($fila[8]) ? floatval(str_replace(',', '', $fila[8])) : 0;

            }

            $datos[] = $filaFiltrada;
        }
        fclose($gestor);
    }

    // Retornar vista con los datos y el total calculado
    return view('upload', compact('datos', 'encabezados', 'total'));
}

}
