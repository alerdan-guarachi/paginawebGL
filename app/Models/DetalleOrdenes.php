<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrdenes extends Model
{
    use HasFactory;

    // Especificar el nombre de la tabla
    protected $table = 'detalleordenes';

    public static $rules = [
        'id' => '',
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
        'codigo' => '',
        'tipotransaccion' => '',
        'fechacomprar' => '',
        'fechapagar' => '',
        'usuariopreorden' => '',
        'estado' => '',
        'nrobancoorigen' => '',
        'sucursal' => '',
        'sucursalgasto' => '',
    ];

    protected $fillable = [
        'id',
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
        'codigo',
        'tipotransaccion',
        'fechacomprar',
        'fechapagar',
        'usuariopreorden',
        'estado',
        'nrobancoorigen',
        'sucursal',
        'sucursalgasto',
    ];
    public function cuentasporpagar()
    {
        return $this->hasMany(CuentasPagar::class, 'detalleordenid', 'id');
    }
}