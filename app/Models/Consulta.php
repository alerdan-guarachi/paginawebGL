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

    public function sintomas()
    {
        return $this->belongsToMany(Sintoma::class, 'consulta_sintoma', 'consulta_id', 'sintoma_id')
                    ->withPivot('intensidad', 'duracion_dias')
                    ->withTimestamps();
    }

    public function estudios()
    {
        return $this->belongsToMany(Estudio::class, 'consulta_estudio', 'consulta_id', 'estudio_id')
                    ->withPivot('origen', 'notas')
                    ->withTimestamps();
    }

    public function especialidades()
    {
        return $this->belongsToMany(Especialidad::class, 'consulta_especialidad', 'consulta_id', 'especialidad_id')
                    ->withPivot('origen')
                    ->withTimestamps();
    }
}