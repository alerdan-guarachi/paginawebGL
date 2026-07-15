<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    protected $table = 'specialties';

    protected $fillable = [
        'name',
        'description',
        'code',
        'state' // Cambiado de status a state para alinearse con ->state !== 1 del controlador
    ];

    protected $casts = [
        'state' => 'boolean',
    ];

    public function reglas()
    {
        // Unificada la tabla pivote a 'ai_rule_specialties'
        return $this->belongsToMany(AiRule::class, 'ai_rule_specialties', 'specialty_id', 'rule_id')
                    ->withPivot('priority')
                    ->withTimestamps();
    }
}