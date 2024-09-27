<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
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
use App\Models\Empresa;
use App\Models\Pais;
use App\Models\Ciudad;
use App\Models\Cliente;
use App\Models\Aseguradora;
use App\Models\Afp;
use App\Models\Bateriasubcliente;
use App\Models\Estadoprogramacionsubcliente;
use App\Models\Programacionsubcliente;
use App\Models\Documentacionsubcliente;
use App\Http\Requests\StoreAsociadoRequest;
use App\Http\Requests\StoreBateriaproveedorRequest;
use App\Http\Requests\StoreProgramacionsubclienteRequest;
use App\Http\Requests\StoreEstadoprogramacionsubclienteRequest;
use App\Http\Requests\StoreDocumentacionsubclienteRequest;
use App\Http\Requests\StoreBateriasubclienteRequest;
use App\Http\Requests\StoreBateriaclientecomunRequest;
use App\Http\Requests\StoreClienteAuditoriaRequest;
use App\Http\Requests\StoreClienteComunRequest;
use App\Http\Requests\StoreClienteBancoRequest;
use App\Http\Requests\StoreClienteRequest;
use App\Services\WhatsAppService;
class PaginawebController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function contact()
    {
        return view('contact');
    }
    public function asesoramientolegal()
    {
        return view('asesoramientolegal');
    }
    public function welcome()
    {
        return view('welcome');
    }
    public function medicina()
    {
        return view('medicina');
    }
    public function sobrenosotros()
    {
        return view('sobrenosotros');
    }
}
