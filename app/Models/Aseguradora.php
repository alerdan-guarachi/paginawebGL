<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aseguradora extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules = [
        'id' => '',
        'aseguradora' => 'required|max:45',
        'contacto' => 'required|max:45',
        'cargo' => 'required|max:45',
        'celular' => 'required|max:45',
        'telefono' => 'required|max:45',
        'codigo' => 'required|max:45',

    ]; 

    protected $fillable = [
        'id',
        'aseguradora',
        'contacto',
        'cargo',
        'celular',
        'telefono',
        'codigo',

    ];

}
