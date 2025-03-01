<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeccionesInventario extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'seccionesinventario';
    static $rules = [
        'id' => '',
        'codigo' => 'required',
        'tipoinventario' => 'required',
        'seccion' => 'required',
        'tiposeccion' => 'required',
        'subseccion' => 'required',
        'estado' => 'required',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'codigo',
        'tipoinventario',
        'seccion',
        'tiposeccion',
        'subseccion',
        'estado',
        'usuarioregistroid',
        'usuarioregistronombre',
    ];

}
