<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cajacentral extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'cajacentral';
    static $rules = [
        'id' => '',
        'tipocliente' => '',
        'clienteid' => '',
        'clientenombre' => '',
        'clienteauditoriaid' => '',
        'clienteauditorianombre' => '',
        'clientecomunid' => '',
        'clientecomunnombre' => '',
        'clientebancoid' => '',
        'clientebanconombre' => '',
        'detalle' => '',
        'area' => '',
        'subtotal' => '',
        'descuento' => '',
        'montototal' => '',
        'saldo' => '',
        'nrorecibo' => '',
        'nrofactura' => '',
        'nrobancarizacion' => '',
        'nrocheque' => '',
        'nrotarjeta' => '',
        'nroap' => '',
        'nroref' => '',
        'nrocuentadeposito' => '',
        'nrocuentaorigen' => '',
        'tipobanco' => '',
        'tipocambio' => '',
        'tipomovimiento' => '',
        'tipotransaccion' => '',
        'ciudadregistro' => '',
        'estado' => '',
        'usuariorevisioncierre' => '',
        'estadorevisioncierre' => '',
        'usuarioregistronombre' => '',
        'nombrebanco' => '',
        'numerobanco' => '',
        'usuarioregistroid' => '',
        'usuarioactualizacion' => '',
        'usuarioanulacion' => '',
    ]; 

    protected $fillable = [
        'id',
        'tipocliente',
        'clienteid',
        'clientenombre',
        'clienteauditoriaid',
        'clienteauditorianombre',
        'clientecomunid',
        'clientecomunnombre',
        'clientebancoid',
        'clientebanconombre',
        'detalle',
        'area',
        'subtotal',
        'descuento',
        'montototal',
        'saldo',
        'nrorecibo',
        'nrofactura',
        'nrobancarizacion',
        'nrocheque',
        'nrotarjeta',
        'nroap',
        'nroref',
        'nrocuentadeposito',
        'nrocuentaorigen',
        'tipobanco',
        'tipocambio',
        'tipomovimiento',
        'tipotransaccion',
        'ciudadregistro',
        'estado',
        'usuariorevisioncierre',
        'estadorevisioncierre',
        'usuarioregistronombre',
        'nombrebanco',
        'numerobanco',
        'usuarioregistroid',
        'usuarioactualizacion',
        'usuarioanulacion',
    ];
}
