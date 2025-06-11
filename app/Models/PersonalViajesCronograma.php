<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalViajesCronograma extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'personalviajescronograma';
    static $rules = [
        'id' => '',
        'viajeid' => '',
        'nroactividad' => '',
        'fechahoraactividad' => '',
        'ubicacionactividad' => '',
        'descripcionactividad' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'estado' => '',
    ]; 

    protected $fillable = [
        'id',
        'viajeid',
        'nroactividad',
        'fechahoraactividad',
        'ubicacionactividad',
        'descripcionactividad',
        'usuarioregistroid',
        'usuarioregistronombre',
        'estado',
    ];
}
