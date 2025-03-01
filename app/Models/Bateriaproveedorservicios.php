<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bateriaproveedorservicios extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'bateriaproveedorservicios';
    static $rules = [
        'id' => '',
        'proveedorid' => '',
        'proveedornombre' => '',
        'compraproducto' => '',
        'compraservicio' => '',
        'ventaproducto' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'proveedorid',
        'proveedornombre',
        'compraproducto',
        'compraservicio',
        'ventaproducto',
        'usuarioregistroid',
        'usuarioregistronombre',
    ];

}
