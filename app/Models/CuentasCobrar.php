<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentasCobrar extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'cuentasporcobrar';
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
        'precio' => '',
        'tipoorden' => '',
        'ordenid' => '',
        'estado' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'fechacomprar' => '',
        'cantidad' => '',
        'detalleordenid' => '',
        'ciudad' => '',
        'sucursalcobro' => '',
        'formacobro' => '',
        'observaciones' => '',
        'nrobancodestino' => '',
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
        'precio',
        'tipoorden',
        'ordenid',
        'estado',
        'usuarioregistroid',
        'usuarioregistronombre',
        'fechacomprar',
        'cantidad',
        'detalleordenid',
        'ciudad',
        'sucursalcobro',
        'formacobro',
        'observaciones',
        'nrobancodestino',
    ];

}
