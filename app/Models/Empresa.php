<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules = [
        'id',
        'nombreempresa' => 'required|max:45',
        'contacto' => 'required|max:45',
        'celular' => 'required',
        'telefono' => 'required|max:45',
        'direccion' => 'required|max:45'

    ]; 

    protected $fillable = [
        'id',
        'nombreempresa',
        'contacto',
        'celular',
        'telefono',
        'direccion',

    ];

}
