<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentasBancos extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'cuentasbancos';
    static $rules = [
        'id' => '',
        'nombrebanco' => '',
        'numerocuenta' => '',
        'nombrecuenta' => '',
        'estado' => '',
        'tipocuenta' => '',
        'sigla' => '',
    ]; 

    protected $fillable = [
        'id',
        'nombrebanco',
        'numerocuenta',
        'nombrecuenta',
        'estado',
        'tipocuenta',
        'sigla',
    ];

}
