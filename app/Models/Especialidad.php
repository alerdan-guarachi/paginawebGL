<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function reglas()
    {
        return $this->belongsToMany(IaRegla::class, 'ia_regla_especialidad', 'especialidad_id', 'regla_id')
                    ->withPivot('prioridad')
                    ->withTimestamps();
    }
}