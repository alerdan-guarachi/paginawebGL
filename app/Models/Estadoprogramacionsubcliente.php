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
            'proveedorasignado' => 'required|max:45',
            'horarioasignado' => 'required|max:45',
            'proveedorid' => 'required|max:45',
            'fechaatencionprogramacion' => 'required|max:45',
            'usuarioid' => 'required|max:45',
            'usuarioregistro' => 'required|max:45',
            'accionnombre' => 'required|max:45',
            'fechabateria' => 'required|max:45',
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
