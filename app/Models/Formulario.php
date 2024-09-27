<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formulario extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules = [
        'id',
        'cliente_id' => '',
        'pregunta_id' => '',
        'diagnostico' => '',
        'fecha' => '',
        'tiempo' => '',
        'gradorecuperacion' => '',
        'medico' => '',
        'direccionmedico' => '',
        'detallescompletos' => '',
        'parentesco' => '',
        'cuantosmeses' => '',
        'pregunta_nombre' => '',
        'diagnostico2' => '',
        'fecha2' => '',
        'tiempo2' => '',
        'gradorecuperacion2' => '',
        'medico2' => '',
        'direccionmedico2' => '',
        'hacecuanto' => '',
        'cadacuanto' => '',
        'parentesco2' => '',


    ]; 

    protected $fillable = [
        'id',
        'cliente_id',
        'pregunta_id',
        'diagnostico',
        'fecha',
        'tiempo',
        'gradorecuperacion',
        'medico',
        'direccionmedico',
        'detallescompletos',
        'parentesco',
        'cuantosmeses',
        'pregunta_nombre',
        'diagnostico2',
        'fecha2',
        'tiempo2',
        'gradorecuperacion2',
        'medico2',
        'direccionmedico2',
        'hacecuanto',
        'cadacuanto',
        'parentesco2',
    ];

}
