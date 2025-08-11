<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubTramite extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'subprocedimientotramites';
    static $rules = [
        'id' => '',
        'clienteid' => '',
        'clientenombre' => '',
        'tramite' => '',
        'idtramite' => '',
        'tipo' => '',
        'razonsocialempleador' => '',
        'periodo' => '',
        'observacion' => '',
        'estudioespecialidad' => '',
        'fechaprogramacion' => '',
        'horaprogramacion' => '',
        'motivoanulacion' => '',
        'usuarioanulacion' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'fechareprogramacion' => '',
        'horareprogramacion' => '',
        'motivoreprogramacion' => '',
        'ordenprogramacion' => '',
        'informeestudioespecialidad' => '',
        'nombremedico' => '',
        'asistenciaprogramacion' => '',
        'opcionatencion' => '',
    ]; 

    protected $fillable = [
        'id',
        'clienteid',
        'clientenombre',
        'idtramite',
        'tramite',
        'tipo',
        'razonsocialempleador',
        'periodo',
        'observacion',
        'estudioespecialidad',
        'fechaprogramacion',
        'horaprogramacion',
        'motivoanulacion',
        'usuarioanulacion',
        'usuarioregistroid',
        'usuarioregistronombre',
        'fechareprogramacion',
        'horareprogramacion',
        'motivoreprogramacion',
        'ordenprogramacion',
        'informeestudioespecialidad',
        'nombremedico',
        'asistenciaprogramacion',
        'opcionatencion',
    ];
}
