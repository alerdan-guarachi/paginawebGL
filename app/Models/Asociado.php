<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asociado extends Model
{
    use HasFactory;
    use SoftDeletes;

    static $rules = [
        'id' => '',
        'asociado' => 'required|max:45',
        'usuarioid' => 'max:45',
        'usuarioregistro' => 'max:45',
        'direccion' => 'max:45',
        'nit' => 'max:45',
        'banco' => 'max:45',
        'cuenta' => 'max:45',
        'tipocuenta' => 'max:45',
        'telefono' => 'max:45',
        'ciudad' => 'max:45',
        'estadoasociado' => 'max:45',
        'mododepago' => 'max:45',
    ]; 

    protected $fillable = [
        'id',
        'asociado',
        'usuarioid',
        'usuarioregistro',
        'direccion',
        'nit' ,
        'banco',
        'cuenta',
        'tipocuenta',
        'telefono',
        'ciudad',
        'estadoasociado',
        'mododepago',
    ];
}
