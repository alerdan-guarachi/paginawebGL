<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
    protected $table = 'specialties';

    protected $fillable = [
        'name',
        'normalized_name',
        'synonyms',
        'code',
        'professional_group',
        'specialty_level',
        'care_type',
        'parent_specialty_id',
        'patient_referral_enabled',
        'description',
        'review_notes',
        'state',
        'user_created',
    ];

    protected $casts = [
        'parent_specialty_id' => 'integer',
        'patient_referral_enabled' => 'boolean',
        'state' => 'boolean',
        'user_created' => 'integer',
    ];

    /**
     * Solo especialidades activas.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('state', 1);
    }

    /**
     * Solo especialidades que pueden recomendarse al paciente.
     */
    public function scopeReferralEnabled(Builder $query): Builder
    {
        return $query
            ->where('state', 1)
            ->where('patient_referral_enabled', 1);
    }

    /**
     * Especialidad principal de esta subespecialidad.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            Specialty::class,
            'parent_specialty_id'
        );
    }

    /**
     * Subespecialidades dependientes.
     */
    public function children(): HasMany
    {
        return $this->hasMany(
            Specialty::class,
            'parent_specialty_id'
        );
    }

    /**
     * Reglas relacionadas con esta especialidad.
     */
    public function rules(): BelongsToMany
    {
        return $this->belongsToMany(
            AiRule::class,
            'ai_rule_specialties',
            'specialty_id',
            'rule_id'
        )
            ->withPivot([
                'id',
                'priority',
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