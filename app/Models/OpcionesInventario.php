<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpcionesInventario extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'opcionesinventario';
    static $rules = [
        'id' => '',
        'tipo' => '',
        'seccion' => '',
        'tiposeccion' => '',
        'opcion' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'tipo',
        'seccion',
        'tiposeccion',
        'opcion',
        'usuarioregistroid',
        'usuarioregistronombre',
    ];

}
