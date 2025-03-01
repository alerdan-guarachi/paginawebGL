<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventario extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'inventario';
    static $rules = [
        'id' => '',
        'tipoinventario' => '',
        'codigo' => '',
        'nombreproducto' => '',
        'materiaprima' => '',
        'especificacionmedida' => '',
        'color' => '',
        'marca' => '',
        'unidadmedida' => '',
        'inventario' => '',
        'seccion' => '',
        'stockinicial' => '',
        'stockactual' => '',
        'precio' => '',
        'deposito' => '',
        'ciudad' => '',
        'modelo' => '',
        'serie' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',

    ]; 

    protected $fillable = [
        'id',
        'tipoinventario',
        'codigo',
        'nombreproducto',
        'materiaprima',
        'especificacionmedida',
        'color',
        'marca',
        'unidadmedida',
        'inventario',
        'seccion',
        'stockinicial',
        'stockactual',
        'precio',
        'deposito',
        'ciudad',
        'modelo',
        'serie',
        'usuarioregistroid',
        'usuarioregistronombre',
    ];

}
