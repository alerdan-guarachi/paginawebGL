<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sintoma extends Model
{
    protected $table = 'sintomas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria',
        'es_critico',
        'severidad_base',
        'sinonimos',
        'estado'
    ];

    protected $casts = [
        'es_critico' => 'boolean',
        'estado' => 'boolean',
        'severidad_base' => 'integer',
    ];

    public function reglas()
    {
        return $this->belongsToMany(IaRegla::class, 'ia_regla_sintoma', 'sintoma_id', 'regla_id')
                    ->withPivot('peso', 'obligatorio', 'condicion')
                    ->withTimestamps();
    }
}