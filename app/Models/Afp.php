<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Afp extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'afp';
    static $rules = [
        'id' => '',
        'afp' => 'required|max:45',
        'contacto' => 'required|max:45',
        'celular' => 'required|max:45',
        'telefono' => 'required|max:45',
        'codigo' => 'required|max:45',

    ]; 

    protected $fillable = [
        'id',
        'ciudad',
        'contacto',
        'celulat',
        'telefono',
        'codigo',

    ];

}
