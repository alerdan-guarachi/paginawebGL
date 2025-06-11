<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detallerecibo extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'detallerecibos';
    static $rules = [
        'id' => '',
        'reciboid' => '',
        'area' => '',
        'detalle' => '',
        'subtotal' => '',
        'descuento' => '',
        'montototal' => '',
        'saldo' => '',
        'precioprogramado' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'programacionid' => '',
        'fechabateria' => '',
        'servicio' => '',
        'proveedoratencion' => '',
        'fechaatencion' => '',
        'estado' => '',
        'provinfofinalid' => '',
        'tipomovimiento' => '',
        'cuentapagarid' => '',
        'cuentacobrarid' => '',
        'bateriaid' => '',
        'clienteid' => '',
        'clientenombre' => '',
        'ordenid' => '',
        'tipotransaccion' => '',
        'descuentoatc' => '',
        'sucursalgasto' => '',
        'comprobante' => '',
        'factura' => '',
        'created_at' => '',
        'updated_at' => '',
    ]; 

    protected $fillable = [
        'id',
        'reciboid',
        'area',
        'detalle',
        'subtotal',
        'descuento',
        'montototal',
        'saldo',
        'precioprogramado',
        'usuarioregistroid',
        'usuarioregistronombre',
        'programacionid',
        'fechabateria',
        'servicio',
        'proveedoratencion',
        'fechaatencion',
        'estado',
        'provinfofinalid',
        'tipomovimiento',
        'cuentapagarid',
        'cuentacobrarid',
        'bateriaid',
        'clienteid',
        'clientenombre',
        'ordenid',
        'tipotransaccion',
        'descuentoatc',
        'sucursalgasto',
        'comprobante',
        'factura',
        'created_at',
        'updated_at',
    ];
}
