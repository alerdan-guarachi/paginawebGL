<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credito extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'creditos';
    static $rules = [
        'id' => '',
        'bateriaid' => '',
        'detalle' => '',
        'clienteid' => '',
        'precioreal' => '',
        'clientenombre' => '',
        'proveedor' => '',
        'fechacredito' => '',
        'montocuota' => '',
        'usuarioautorizador' => '',
        'docrespaldo' => '',
        'letracambio' => '',
        'cartacredito' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'estado' => '',
        'nrocredito' => '',
        'tramite' => '',
    ]; 

    protected $fillable = [
        'id',
        'bateriaid',
        'detalle',
        'precioreal',
        'clienteid',
        'clientenombre',
        'proveedor',
        'fechacredito',
        'montocuota',
        'usuarioautorizador',
        'docrespaldo',
        'letracambio',
        'cartacredito',
        'usuarioregistroid',
        'usuarioregistronombre',
        'estado',
        'nrocredito',
        'tramite',
    ];
}
