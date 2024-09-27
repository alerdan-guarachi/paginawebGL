<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aprobacioninformefinal extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'aprobacioninformesfinales';
    static $rules = [
        'id',
        'clienteitaid' => 'max:45',
        'clienteitanombre' => 'max:45',
        'clientecomunid' => 'max:45',
        'clientecomunnombre' => 'max:45',
        'clientebancoid' => 'max:45',
        'clientebanconombre' => 'max:45',
        'clienteauditoriaid' => 'max:45',
        'clienteauditorianombre' => 'max:45',
        'fechabateria' => '',
        'estado' => 'max:45',
        'proveedorasignado' => 'max:45',
        'usuarioid' => 'required',
        'usuarioregistro' => 'max:45',

    ]; 

    protected $fillable = [
        'id',
        'clienteitaid',
        'clienteitanombre',
        'clientecomunid',
        'clientecomunnombre',
        'clientebancoid',
        'clientebanconombre',
        'clienteauditoriaid',
        'clienteauditorianombre',
        'fechabateria',
        'estado',
        'proveedorasignado',
        'usuarioid',
        'usuarioregistro'
    ];

}
