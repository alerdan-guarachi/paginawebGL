<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanesServiciosProv extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'planesserviciosprov';
    static $rules = [
        'id' => '',
        'proveedorid' => '',
        'razonsocial' => '',
        'plan' => '',
        'codigo' => '',
        'sigla' => '',
        'codigo' => '',
        'contrato' => '',
        'linea' => '',
        'cuenta' => '',
        'servicio' => '',
        'motivo' => '',
        'montofijo' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'ciudad' => '',
        'estado' => '',
    ]; 

    protected $fillable = [
        'id',
        'proveedorid',
        'razonsocial',
        'plan',
        'codigo',
        'sigla',
        'codigo',
        'contrato',
        'linea',
        'cuenta',
        'servicio',
        'motivo',
        'montofijo',
        'usuarioregistroid',
        'usuarioregistronombre',
        'ciudad',
        'estado',
    ];
}
