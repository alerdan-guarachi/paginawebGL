<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banco extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules = [
        'id' => '',
        'tipobanco' => 'required|max:45',
        'nombrabanco' => 'required|max:45',
        'sigla' => 'required|max:45',
    ]; 

    protected $fillable = [
        'id',
        'departamento',
        'nombrabanco',
        'sigla'
    ];

}
