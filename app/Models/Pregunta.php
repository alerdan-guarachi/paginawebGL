<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pregunta extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules = [
        'id',
        'pregunta' => '',

    ]; 

    protected $fillable = [
        'id',
        'pregunta',
    ];

}
