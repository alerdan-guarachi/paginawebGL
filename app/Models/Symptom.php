<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Symptom extends Model
{
    protected $table = 'symptoms';

    protected $fillable = [
        'name',
        'description',
        'category',
        'is_critical',
        'base_severity',
        'synonymus',
        'state'
    ];

    protected $casts = [
        'is_critical' => 'boolean',
        'state' => 'boolean',
        'base_severity' => 'integer',
    ];

    public function rules() {
        return $this->belongsToMany(AiRule::class, 'ai_rule_symptoms', 'symptom_id', 'rule_id');
    }
}