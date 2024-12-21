<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CajaCentralController extends Controller
{
    /**
     * Constructor para aplicar middleware de permisos.
     */
    public function __construct()
    {
        $this->middleware('can:admin.ingreso.index');
    }

    /**
     * Muestra la vista principal de Egreso.
     *
     * @return \Illuminate\View\View
     */
    
    public function historialFacturas()
    {
        // Aquí puedes obtener los datos necesarios para la vista
        // Por ejemplo:
        // $categorias = Categoria::all();
        return view('admin.caja.historialFacturas'); // Asegúrate de crear esta vista
    }
    public function nuevaFactura()
    {
        return view('admin.caja.nuevaFactura');
    }
}