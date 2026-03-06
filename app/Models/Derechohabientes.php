<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Derechohabientes extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'derechohabientes';
    static $rules = [
        'id' => '',
        'clienteid' => '',
        'clientenombre' => '',
        'tipocliente' => '',
        'nombrecompleto' => '',
        'genero' => '',
        'tramite' => '',
        'ci' => '',
        'estadocivil' => '',
        'ocupacion' => '',
        'domicilio' => '',
        'ciudadresidencia' => '',
        'parentesco' => '',
        'usuarioid' => '',
        'usuarioregistro' => '',
    ]; 

    protected $fillable = [
        'id',
        'clienteid',
        'clientenombre',
        'tipocliente',
        'nombrecompleto',
        'genero',
        'tramite',
        'ci',
        'estadocivil',
        'ocupacion',
        'domicilio',
        'ciudadresidencia',
        'parentesco',
        'usuarioid',
        'usuarioregistro',
    ];

}
