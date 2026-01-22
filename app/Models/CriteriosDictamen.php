<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CriteriosDictamen extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'criteriosdictamen';
    static $rules = [
        'id' => '',
        'clienteid' => '',
        'clientenombre' => '',
        'tramite' => '',
        'idtramite' => '',
        'nivel' => '',
        'subnivel' => '',
        'nrocriterio' => '',
        'porcentaje' => '',
        'subtotal' => '',
        'totalasignar' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'apoderado' => '',
        'vtr' => '',
        'vtr1' => '',
        'vtr2' => '',
        'decisionrecal' => '',
        'mes' => '',
        'anno' => '',
        'fechasiniestro1' => '',
        'fechasiniestro2' => '',
    ]; 

    protected $fillable = [
        'id',
        'clienteid',
        'clientenombre',
        'tramite',
        'idtramite',
        'nivel',
        'subnivel',
        'nrocriterio',
        'porcentaje',
        'subtotal',
        'totalasignar',
        'usuarioregistroid',
        'usuarioregistronombre',
        'apoderado',
        'vtr',
        'vtr1',
        'vtr2',
        'decisionrecal',
        'mes',
        'anno',
        'fechasiniestro1',
        'fechasiniestro2',
    ];
}
