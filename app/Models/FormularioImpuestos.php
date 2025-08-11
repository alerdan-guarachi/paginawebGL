<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormularioImpuestos extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'formularioimpuestos';
    static $rules = [
        'id' => '',
        'nombre' => '',
        'periodo' => '',
        'monto' => '',
        'archivo' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'montocontador' => '',
        'registrocontadorid' => '',
        'registrocontadornombre' => '',
        'fecharegistrocontador' => '',
    ]; 

    protected $fillable = [
        'id',
        'nombre',
        'periodo',
        'monto',
        'archivo',
        'usuarioregistroid',
        'usuarioregistronombre',
        'montocontador',
        'registrocontadorid',
        'registrocontadornombre',
        'fecharegistrocontador',
    ];

}
