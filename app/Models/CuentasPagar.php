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
    static $rules = [
        'id' => '',
        'proveedorid' => '',
        'proveedornombre' => '',
        'tipoproveedor' => '',
        'detalle' => '',
        'subtotal' => '',
        'descuentosancion' => '',
        'descuentoafp' => '',
        'montototal' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'estado' => '',
        'fechaasignada' => '',
        'preciocompra' => '',
    ]; 

    protected $fillable = [
        'id',
        'proveedorid',
        'proveedornombre',
        'tipoproveedor',
        'detalle',
        'subtotal',
        'descuentosancion',
        'descuentoafp',
        'montototal',
        'usuarioregistroid',
        'usuarioregistronombre',
        'estado',
        'fechaasignada',
        'preciocompra',
    ];

}
