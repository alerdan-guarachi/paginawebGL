<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recibo extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'recibos';
    static $rules = [
        'id' => '',
        'ciudadregistro' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'tipocliente' => '',
        'clienteid' => '',
        'clientenombre' => '',
        'tipomovimiento' => '',
        'subtotal' => '',
        'descuentototal' => '',
        'montototal' => '',
        'estado' => '',
    ]; 

    protected $fillable = [
        'id',
        'ciudadregistro',
        'usuarioregistroid',
        'usuarioregistronombre',
        'tipocliente',
        'clienteid',
        'clientenombre',
        'tipomovimiento',
        'subtotal',
        'descuentototal',
        'montototal',
        'estado',
    ];
}
