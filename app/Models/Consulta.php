<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    protected $table = 'consultas';

    protected $fillable = [
        'usuario_id',
        'edad',
        'sexo',
        'embarazada',
        'nivel_urgencia_calculado',
        'observaciones'
    ];

}