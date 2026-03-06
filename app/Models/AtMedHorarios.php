<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtMedHorarios extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'atmedhorarios';
    static $rules = [
        'id' => '',
        'proveedorid' => '',
        'proveedornombre' => '',
        'dia' => '',
        'sucursal' => '',
        'horainicio' => '',
        'horafin' => '',
        'duracioncita' => '',
        'estado' => '',
        'tipo' => '',
        'usuregid' => '',
        'usuregnombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'proveedorid',
        'proveedornombre',
        'dia',
        'sucursal',
        'horainicio',
        'horafin',
        'duracioncita',
        'estado',
        'tipo',
        'usuregid',
        'usuregnombre',
    ];
}
