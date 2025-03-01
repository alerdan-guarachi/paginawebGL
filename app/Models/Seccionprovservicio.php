<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seccionprovservicio extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'seccionprovservicios';
    static $rules = [
        'id' => '',
        'nombreseccion' => 'required',
        'estado' => 'required',
        'usuarioregistroid' => 'required',
        'usuarioregistronombre' => 'required',
    ]; 

    protected $fillable = [
        'id',
        'nombreseccion',
        'estado',
        'usuarioregistroid',
        'usuarioregistronombre',
    ];
}
