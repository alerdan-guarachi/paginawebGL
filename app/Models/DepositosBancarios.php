<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepositosBancarios extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'depositosbancarios';
    static $rules = [
        'id' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'detalle' => '',
        'monto' => '',
        'estado' => '',
        'salida' => '',
        'destino' => '',
        'tipotransaccion' => '',
        'bancarizacion' => '',
        'bancodestino' => '',
        'usuariodestino' => '',
        'documentorespaldo' => '',
        'documentofactura' => '',
        'fecha' => '',
    ]; 

    protected $fillable = [
        'id',
        'usuarioregistroid',
        'usuarioregistronombre',
        'detalle',
        'monto',
        'estado',
        'salida',
        'destino',
        'tipotransaccion',
        'bancarizacion',
        'bancodestino',
        'usuariodestino',
        'documentorespaldo',
        'documentofactura',
        'fecha',
    ];
}
