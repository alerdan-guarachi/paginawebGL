<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrdenes extends Model
{
    use HasFactory;

    // Especificar el nombre de la tabla
    protected $table = 'preordenes';

    public static $rules = [
        'id' => '',
        'preordenid' => '',
        'detalle' => '',
        'cantidad' => '',
        'preciounitario' => '',
        'descuentounitario' => '',
        'totalunitario' => '',
        'tipoorden' => '',
        'ordenid' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'proveedorid' => '',
        'proveedornombre' => '',
        'estado' => '',
        'observacion' => '',
        'formapago' => '',
        'codigo' => '',
        'tipotransaccion' => '',
        'fechacomprar' => '',
        'fechapagar' => '',
        'sucursal' => '',
        'sucursalgasto' => '',
        'prioridad' => '',
        'anteriorpreordenid' => '',
    ];

    protected $fillable = [
        'id',
        'preordenid',
        'detalle',
        'cantidad',
        'preciounitario',
        'descuentounitario',
        'totalunitario',
        'tipoorden',
        'ordenid',
        'usuarioregistroid',
        'usuarioregistronombre',
        'proveedorid',
        'proveedornombre',
        'estado',
        'observacion',
        'formapago',
        'codigo',
        'tipotransaccion',
        'fechacomprar',
        'fechapagar',
        'sucursal',
        'sucursalgasto',
        'prioridad',
        'anteriorpreordenid',
    ];
    public function portafolio()
    {
        return $this->belongsTo(PortafolioProveedores::class, 'codigo', 'id');
    }
    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'codigo', 'codigo');
    }
    public function proveedorServicio()
{
    return $this->belongsTo(Proveedoresservicios::class, 'proveedorid', 'id'); // Asegúrate que 'id' sea el campo correcto en ProveedoresServicios
}

}