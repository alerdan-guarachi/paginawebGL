<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Informefinal extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'informesfinales';
    static $rules = [
        'id',
        'clienteitaid' => '',
        'clienteitanombre' => '',
        'clientecomunid' => '',
        'clientecomunnombre' => '',
        'clientebancoid' => '',
        'clientebanconombre' => '',
        'clienteauditoriaid' => '',
        'clienteauditorianombre' => '',
        'fechabateria' => '',
        'estado' => '',
        'document' => '',
        'observaciones' => '',
        'proveedorasignado' => '',
        'usuarioid' => '',
        'usuarioregistro' => '',
        'observacion' => '',

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
        'document',
        'observaciones',
        'proveedorasignado',
        'usuarioid',
        'usuarioregistro',
        'observacion'
    ];

}
