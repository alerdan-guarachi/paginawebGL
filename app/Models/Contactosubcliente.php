<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contactosubcliente extends Model
{
    use HasFactory;
    use SoftDeletes;

    static $rules = [
        'id' => '',
        'clienteitaid' => 'max:45',
        'clienteitanombre' => 'max:90',
        'clientecomunid' => 'max:45',
        'clientecomunnombre' => 'max:90',
        'clientebancoid' => 'max:45',
        'clientebanconombre' => 'max:90',
        'clienteauditoriaid' => 'max:45',
        'clienteauditorianombre' => 'max:90',
        'nombrecontacto' => 'required|max:90',
        'celularcontacto' => 'required|numeric|max:90',
        'telefonocontacto' => 'numeric|max:90',
        'parentesco' => 'required|max:45',
        'usuarioid' => 'required|max:45',
        'usuarioregistro' => 'required|max:45',

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
        'nombrecontacto',
        'celularcontacto',
        'telefonocontacto',
        'parentesco',
        'usuarioid',
        'usuarioregistro',

    ];

}
