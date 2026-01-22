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
        'apoderado' => '',
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
        'informeprogramacion' => '',
        'informeestudioespecialidad' => '',
        'nombremedico' => '',
        'asistenciaprogramacion' => '',
        'opcionatencion' => '',
        'medicoderivador' => '',

        'cantidadcuotas' => '',
        'saldoacumulado' => '',
        'aporteindependiente' => '',
        'anniosservicio' => '',
        'cantidadaporte' => '',
        'fechaaporte' => '',
        'montoaprox' => '',
        'leyannios' => '',
        'leymeses' => '',
        'leyminimo' => '',
        'leymaximo' => '',
        'leyporcentajeref' => '',

        'nombreafiliado' => '',
        'edadfallecimiento' => '',
        'percibiaservicio1' => '',
        'percibiaservicio2' => '',
        'percibiaservicio3' => '',
        'estadolaboralfallec' => '',
        'ultimafechalaboral' => '',

        'solicitante1' => '',
        'solicitante2' => '',
        'dh123grado1' => '',
        'dh123grado2' => '',
        'dh123grado3' => '',
    ]; 

    protected $fillable = [
        'id',
        'clienteid',
        'clientenombre',
        'idtramite',
        'tramite',
        'apoderado',
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
        'informeprogramacion',
        'informeestudioespecialidad',
        'nombremedico',
        'asistenciaprogramacion',
        'opcionatencion',
        'medicoderivador',

        'cantidadcuotas',
        'saldoacumulado',
        'aporteindependiente',
        'anniosservicio',
        'cantidadaporte',
        'fechaaporte',
        'montoaprox',
        'leyannios',
        'leymeses',
        'leyminimo',
        'leymaximo',
        'leyporcentajeref',

        'nombreafiliado',
        'edadfallecimiento',
        'percibiaservicio1',
        'percibiaservicio2',
        'percibiaservicio3',
        'estadolaboralfallec',
        'ultimafechalaboral',

        'solicitante1',
        'solicitante2',
        'dh123grado1',
        'dh123grado2',
        'dh123grado3',
    ];
}
