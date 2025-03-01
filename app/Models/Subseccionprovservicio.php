<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subseccionprovservicio extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'subseccionprovservicios';
    static $rules = [
        'id' => '',
        'seccionid' => '',
        'seccionnombre' => '',
        'subseccion' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'seccionid',
        'seccionnombre',
        'subseccion',
        'usuarioregistroid',
        'usuarioregistronombre',
    ];
}
