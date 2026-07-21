<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiTextReplacement extends Model
{
    protected $table = 'ai_text_replacements';

    protected $fillable = [
        'context',
        'pattern',
        'replacement',
        'priority',
        'state',
        'notes',
    ];

    protected $casts = [
        'priority' => 'integer',
        'state' => 'boolean',
    ];
}