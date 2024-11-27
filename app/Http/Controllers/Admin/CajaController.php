<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    /**
     * Constructor para aplicar middleware de permisos.
     */
    public function __construct()
    {
        $this->middleware('can:admin.caja.index');
    }

    /**
     * Muestra la vista principal de Caja.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Aquí puedes obtener los datos necesarios para la vista
        // Por ejemplo:
        // $entradas = Entrada::all();
        return view('admin.caja.index'); // Asegúrate de crear esta vista
    }
}