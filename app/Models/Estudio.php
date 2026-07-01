<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudio extends Model
{
    protected $table = 'estudios';

    protected $fillable = [
        'nombre',
        'tipo',
        'descripcion',
        'preparacion',
        'costo_referencial',
        'codigo',
        'requiere_ayuno',
        'requiere_orden_medica',
        'estado'
    ];

    protected $casts = [
        'requiere_ayuno' => 'boolean',
        'requiere_orden_medica' => 'boolean',
        'estado' => 'boolean',
        'costo_referencial' => 'decimal:2',
    ];

    public function reglas()
    {
        return $this->belongsToMany(IaRegla::class, 'ia_regla_estudio', 'estudio_id', 'regla_id')
                    ->withPivot('prioridad', 'recomendado', 'motivo')
                    ->withTimestamps();
    }
}