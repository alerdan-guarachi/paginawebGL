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
        'usuariosolicitanteid' => '',
        'usuarioregistro' => '',
        'usuariosolicitante' => '',
        'productosolicitado' => '',
        'productoofertado' => '',
        'codigoproducto' => '',
        'cantidad' => '',
        'estado' => '',
        'usuarioactualizacion' => '',
        'usuarioactualizacionid' => '',
        'sucursal' => '',
        'cantidadrecibido' => '',
        'cantidadofertado' => '',
        'documento' => '',
        'motivoanulacion' => '',
        'usuarioanulacion' => '',
        'productosolicitadousuario' => '',
    ]; 

    protected $fillable = [
        'id',
        'usuariosolicitanteid',
        'usuarioregistro',
        'usuariosolicitante',
        'productosolicitado',
        'productoofertado',
        'codigoproducto',
        'cantidad',
        'estado',
        'usuarioactualizacion',
        'usuarioactualizacionid',
        'sucursal',
        'cantidadrecibido',
        'cantidadofertado',
        'documento',
        'motivoanulacion',
        'usuarioanulacion',
        'productosolicitadousuario',
    ];

}
