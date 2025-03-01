<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartasPolizas extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'cartaspolizas';
    static $rules = [
        'id' => '',
        'nombrecarta' => '',
        'nombreclienteuno' => '',
        'ciclienteuno' => '',
        'nombreclientedos' => '',
        'ciclientedos' => '',
        'banco' => '',
        'fecha' => '',
        'ciudad' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'documentocarta' => '',
    ]; 

    protected $fillable = [
        'id',
        'nombrecarta',
        'nombreclienteuno',
        'ciclienteuno',
        'nombreclientedos',
        'ciclientedos',
        'banco',
        'fecha',
        'ciudad',
        'usuarioregistroid',
        'usuarioregistronombre',
        'documentocarta',
    ];

}
