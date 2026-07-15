<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Study extends Model
{
    protected $table = 'studies';

    protected $fillable = [
        'name',
        'type',
        'description',
        'preparation',
        'code',
        'requires_fasting', // Cambiado de require_fasting a requires_fasting
        'requires_medical_order', // Cambiado de require_medical_order a requires_medical_order
        'state' // Cambiado de status a state para alinearse con ->state !== 1 del controlador
    ];

    protected $casts = [
        'requires_fasting' => 'boolean',
        'requires_medical_order' => 'boolean',
        'state' => 'boolean',
    ];

    public function reglas()
    {
        // Unificada la tabla pivote a 'ai_rule_studies' y llaves correspondientes
        return $this->belongsToMany(AiRule::class, 'ai_rule_studies', 'study_id', 'rule_id')
                    ->withPivot('priority')
                    ->withTimestamps();
    }
}