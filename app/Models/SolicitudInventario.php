<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitudInventario extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'solicitudinventario';
    static $rules = [
        'id' => '',
        'usuarioregistroid' => '',
        'usuariosolicitante' => '',
        'productosolicitado' => '',
        'productoofertado' => '',
        'codigoproducto' => '',
        'cantidad' => '',
        'estado' => '',
        'usuarioactualizacion' => '',
        'sucursal' => '',
        'cantidadrecibido' => '',
        'cantidadofertado' => '',
        'documento' => '',
    ]; 

    protected $fillable = [
        'id',
        'usuarioregistroid',
        'usuariosolicitante',
        'productosolicitado',
        'productoofertado',
        'codigoproducto',
        'cantidad',
        'estado',
        'usuarioactualizacion',
        'sucursal',
        'cantidadrecibido',
        'cantidadofertado',
        'documento',
    ];

}
