<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentasPagar extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'cuentasporpagar';
    public function getIdAttribute($value)
    {
        return $value;
    }
    static $rules = [
        'id' => '',
        'proveedorid' => '',
        'proveedornombre' => '',
        'tipoproveedorservicio' => '',
        'detalleproducto' => '',
        'fechaasignada' => '',
        'subtotal' => '',
        'descuento' => '',
        'montototal' => '',
        'preciocompra' => '',
        'tipoorden' => '',
        'ordenid' => '',
        'estado' => '',
        'ciudad' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'fechacomprar' => '',
        'cantidad' => '',
        'detalleordenid' => '',
        'sucursalgasto' => '',
        'nrobancoorigen' => '',
        'estadoaprobacion' => '',
        'comprobante' => '',
        'factura' => '',
        'usuariocomprobante' => '',
        'fechamora' => '',
    ]; 

    protected $fillable = [
        'id',
        'proveedorid',
        'proveedornombre',
        'tipoproveedorservicio',
        'detalleproducto',
        'fechaasignada',
        'subtotal',
        'descuento',
        'montototal',
        'preciocompra',
        'tipoorden',
        'ordenid',
        'estado',
        'ciudad',
        'usuarioregistroid',
        'usuarioregistronombre',
        'fechacomprar',
        'cantidad',
        'detalleordenid',
        'sucursalgasto',
        'nrobancoorigen',
        'estadoaprobacion',
        'comprobante',
        'factura',
        'usuariocomprobante',
        'fechamora',
    ];

    public function proveedorServicio()
    {
        return $this->hasOne(Proveedoresservicios::class, 'id', 'proveedorid');
    }
}
