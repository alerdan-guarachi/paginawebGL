<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estadocotizacionsubcliente extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'fechabateria' => '',
        'document' => 'required',
        'documentconsinfo' => '',
        'usuarioid' => '',
        'usuarioregistro' => '',
        'nrofactura' => '',
        'detalle' => '',
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
        'fechabateria',
        'document',
        'documentconsinfo',
        'usuarioid',
        'usuarioregistro',
        'nrofactura',
        'detalle',
    ];

}
