<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estadoprogramacionsubcliente extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'estadoprogramacionsubclientes';
    static $rules = [
            'id' => '',
            'clientebancoid' => '',
            'clientebanconombre' => '',
            'clientecomunid' => '',
            'clientecomunnombre' => '',
            'clienteitaid' => '',
            'clienteitanombre' => '',
            'clienteauditoriaid' => '',
            'clienteauditorianombre' => '',
            'proveedorasignado' => '',
            'horarioasignado' => '',
            'proveedorid' => '',
            'fechaatencionprogramacion' => '',
            'usuarioid' => '',
            'usuarioregistro' => '',
            'accionnombre' => '',
            'fechabateria' => '',
    ]; 

    protected $fillable = [
            'id',
            'clientebancoid',
            'clientebanconombre',
            'clientecomunid',
            'clientecomunnombre',
            'clienteitaid',
            'clienteitanombre',
            'clienteauditoriaid',
            'clienteauditorianombre',
            'proveedorasignado',
            'horarioasignado',
            'proveedorid',
            'fechaatencionprogramacion',
            'usuarioid',
            'usuarioregistro',
            'accionnombre',
            'fechabateria',
    ];
    
}
