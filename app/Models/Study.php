<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Study extends Model
{
    protected $table = 'studies';

    protected $fillable = [
        'type',
        'subtype',
        'service_kind',
        'name',
        'normalized_name',
        'synonyms',
        'code',
        'description',
        'preparation',
        'requires_fasting',
        'requires_medical_order',
        'modality',
        'body_site',
        'laterality',
        'contrast',
        'specimen_type',
        'review_notes',
        'state',
        'user_created',
    ];

    protected $casts = [
        'requires_fasting' => 'boolean',
        'requires_medical_order' => 'boolean',
        'state' => 'boolean',
        'user_created' => 'integer',
    ];

    /**
     * Solo estudios activos.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('state', 1);
    }

    /**
     * Estudios que requieren orden médica.
     */
    public function scopeRequiresMedicalOrder(Builder $query): Builder
    {
        return $query
            ->where('state', 1)
            ->where('requires_medical_order', 1);
    }

    /**
     * Reglas relacionadas con este estudio.
     */
    public function rules(): BelongsToMany
    {
        return $this->belongsToMany(
            AiRule::class,
            'ai_rule_studies',
            'study_id',
            'rule_id'
        )
            ->withPivot([
                'id',
                'priority',
                'is_recommended',
                'motive',
                'state',
            ])
            ->withTimestamps()
            ->wherePivot('state', 1)
            ->orderByPivot('priority', 'asc');
    }

    /**
     * Alias para conservar compatibilidad con código anterior.
     */
    public function reglas(): BelongsToMany
    {
        return $this->rules();
    }
}