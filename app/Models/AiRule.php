<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiRule extends Model
{
    protected $table = 'ai_rules';

    protected $fillable = [
        'name',
        'description',
        'priority',
        'weight',
        'min_symptoms',
        'urgency_level',
        'age_min',
        'age_max',
        'applies_pregnancy',
        'state',
        'sex_applies',
        'alert_message',
        'recommendation'
    ];

    protected $casts = [
        'priority' => 'integer',
        'weight' => 'integer',
        'min_symptoms' => 'integer',
        'age_min' => 'integer',
        'age_max' => 'integer',
        'applies_pregnancy' => 'boolean',
        'state' => 'boolean',
    ];

    public function symptoms() {
        return $this->belongsToMany(Symptom::class, 'ai_rule_symptoms', 'rule_id', 'symptom_id')
                    ->withPivot('weight', 'is_mandatory', 'condition');
    }

    public function studies() {
        return $this->belongsToMany(Study::class, 'ai_rule_studies', 'rule_id', 'study_id')
                    ->withPivot('priority');
    }

    public function specialties() {
        return $this->belongsToMany(Specialty::class, 'ai_rule_specialties', 'rule_id', 'specialty_id')
                    ->withPivot('priority');
    }
}