<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IaRegla extends Model
{
    protected $table = 'ia_reglas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'prioridad',
        'peso',
        'min_sintomas',
        'nivel_urgencia',
        'edad_min',
        'edad_max',
        'aplica_embarazo',
        'estado',
        'sexo_aplica',
        'mensaje_alerta',
        'recomendacion'
    ];

    protected $casts = [
        'prioridad' => 'integer',
        'peso' => 'integer',
        'min_sintomas' => 'integer',
        'edad_min' => 'integer',
        'edad_max' => 'integer',
        'aplica_embarazo' => 'boolean',
        'estado' => 'boolean',
    ];

    public function sintomas()
    {
        return $this->belongsToMany(
                Sintoma::class,
                'ia_regla_sintoma',
                'regla_id',
                'sintoma_id'
            )
            ->withPivot('peso', 'obligatorio', 'condicion')
            ->withTimestamps();
    }

    public function estudios()
    {
        return $this->belongsToMany(
                Estudio::class,
                'ia_regla_estudio',
                'regla_id',
                'estudio_id'
            )
            ->withPivot('prioridad', 'recomendado', 'motivo')
            ->withTimestamps();
    }

    public function especialidades()
    {
        return $this->belongsToMany(
                Especialidad::class,
                'ia_regla_especialidad',
                'regla_id',
                'especialidad_id'
            )
            ->withPivot('prioridad')
            ->withTimestamps();
    }
}