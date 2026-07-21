<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Symptom extends Model
{
    protected $table = 'symptoms';

    protected $fillable = [
        'name',
        'normalized_name',
        'description',
        'category',
        'is_critical',
        'base_severity',
        'urgency_level',
        'synonyms',
        'sex_applicability',
        'age_group',
        'pregnancy_related',
        'alert_message',
        'state',
    ];

    protected $casts = [
        'is_critical' => 'boolean',
        'base_severity' => 'integer',
        'pregnancy_related' => 'boolean',
        'state' => 'boolean',
    ];

    /**
     * Solo síntomas activos.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('state', 1);
    }

    /**
     * Solo síntomas marcados como críticos.
     */
    public function scopeCritical(Builder $query): Builder
    {
        return $query
            ->where('state', 1)
            ->where('is_critical', 1);
    }

    /**
     * Reglas relacionadas con este síntoma.
     */
    public function rules(): BelongsToMany
    {
        return $this->belongsToMany(
            AiRule::class,
            'ai_rule_symptoms',
            'symptom_id',
            'rule_id'
        )
            ->withPivot([
                'id',
                'weight',
                'is_mandatory',
                'condition',
                'state',
            ])
            ->withTimestamps()
            ->wherePivot('state', 1)
            ->orderByPivot('weight', 'desc');
    }

    /**
     * Alias opcional en español.
     */
    public function reglas(): BelongsToMany
    {
        return $this->rules();
    }
}