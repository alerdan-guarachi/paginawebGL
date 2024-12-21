<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CuentasController extends Controller
{
    /**
     * Constructor para aplicar middleware de permisos.
     */
    public function __construct()
    {
        $this->middleware('can:admin.cuentasCobrar.index');
        $this->middleware('can:admin.cuentasPagar.index');
    }

    /**
     * Muestra la vista principal de Egreso.
     *
     * @return \Illuminate\View\View
     */
    
    public function cuentasCobrar()
    {

        return view('admin.caja.cobrar'); // Asegúrate de crear esta vista
    }

    public function aprobacionesCobrar()
    {
        
        return view('admin.caja.aprobacionesCobrar'); // Asegúrate de crear esta vista
    }

    public function cuentasPagar()
    {

        return view('admin.caja.pagar'); // Asegúrate de crear esta vista
    }

    public function aprobacionesPagar()
    {
        
        return view('admin.caja.aprobacionesPagar'); // Asegúrate de crear esta vista
    }
}