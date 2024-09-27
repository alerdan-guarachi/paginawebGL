<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mensaje extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules = [
        'id',
        'titulo' => 'required|max:45',
        'mensaje' => 'required|max:45',
        'usuarioid' => 'required',
        'usuarioregistro' => 'required|max:45',
        'usuariodestino' => 'required|max:45',
    ]; 

    protected $fillable = [
        'id',
        'titulo',
        'mensaje',
        'usuarioid',
        'usuarioregistro',
        'usuariodestino',
    ];

}
