<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CuentasPagar;
use App\Models\DetalleOrdenes;
use App\Models\Ordenes;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Inventario;
use App\Models\SeccionesInventario;
use App\Models\Proveedoresservicios;
use App\Models\PortafolioProveedores;
use App\Models\Detallerecibo;
use App\Models\PreOrdenes;
use App\Models\EntradaSalidaInventario;
use App\Models\SolicitudInventario;
use Illuminate\Support\Facades\Auth;
use App\Notifications\StockBajoNotification;
use App\Notifications\SolicitudProductoNotification;
use Illuminate\Support\Str;
use App\Notifications\ActSolicitudNotification;
use App\Notifications\AceptaSolicitudNotification;
use App\Notifications\AceptoRechazosolicitudNotification;
use App\Notifications\SolicitudInvEsperaNotification;
use Dompdf\Dompdf;
use PDF;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\PlanesServiciosProv;
use App\Models\CuentasBancos;
use App\Models\ClienteAuditoria;
use App\Models\ClienteComun;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Bateriasubcliente;
use App\Models\OpcionesInventario;
use App\Models\PermisoCodigo;
use Carbon\Carbon;

class ProveedorController extends Controller
{
    public function listaordenes(Request $request) 
    {
        $ordenesaprobadas = Ordenes::where('tipoorden', 'ORDEN DE COMPRA')
        ->whereNull('deleted_at')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.ordenid', 'ordenes.id')
                    ->whereNull('detallerecibos.deleted_at')
                    ->groupBy('detallerecibos.ordenid')
                    ->havingRaw('SUM(CASE WHEN estado != "PAGO PROCESADO" THEN 1 ELSE 0 END) = 0');
            })
            ->orderBy('created_at', 'asc')
        ->get();

        $ordenesaprobadasprocesadas = Ordenes::where('tipoorden', 'ORDEN DE COMPRA')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.ordenid', 'ordenes.id');
            })
            ->with('detallesrecibos')
            ->orderBy('created_at', 'asc')
        ->get();


        $ordenesaprobadasservicio = Ordenes::where('tipoorden', 'ORDEN DE SERVICIO')
        ->whereNull('deleted_at')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.ordenid', 'ordenes.id')
                    ->whereNull('detallerecibos.deleted_at')
                    ->groupBy('detallerecibos.ordenid')
                    ->havingRaw('SUM(CASE WHEN estado != "PAGO PROCESADO" THEN 1 ELSE 0 END) = 0');
            })
            ->orderBy('created_at', 'asc')
        ->get();

        $ordenesaprobadasprocesadasservicio = Ordenes::where('tipoorden', 'ORDEN DE SERVICIO')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.ordenid', 'ordenes.id');
            })
            ->with('detallesrecibos')
            ->orderBy('created_at', 'asc')
        ->get();


        $ordenesaprobadaspersonal = Ordenes::where('tipoorden', 'ORDEN DE PERSONAL')
        ->whereNull('deleted_at')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.ordenid', 'ordenes.id')
                    ->whereNull('detallerecibos.deleted_at')
                    ->groupBy('detallerecibos.ordenid')
                    ->havingRaw('SUM(CASE WHEN estado != "PAGO PROCESADO" THEN 1 ELSE 0 END) = 0');
            })
            ->orderBy('created_at', 'asc')
        ->get();

        $ordenesaprobadasprocesadaspersonal = Ordenes::where('tipoorden', 'ORDEN DE PERSONAL')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detallerecibos')
                    ->whereColumn('detallerecibos.ordenid', 'ordenes.id');
            })
            ->with('detallesrecibos')
            ->orderBy('created_at', 'asc')
        ->get();

        return view('admin.inventario.listaordenes', compact('ordenesaprobadasprocesadas','ordenesaprobadas',
        'ordenesaprobadasprocesadasservicio','ordenesaprobadasservicio',
        'ordenesaprobadasprocesadaspersonal','ordenesaprobadaspersonal'));
    }
}
