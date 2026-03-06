<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtMedDiario extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'atmeddiario';
    static $rules = [
        'id' => '',
        'proveedorid' => '',
        'proveedornombre' => '',
        'fecha' => '',
        'sucursal' => '',
        'horainicio' => '',
        'horafin' => '',
        'duracioncita' => '',
        'usuregid' => '',
        'usuregnombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'proveedorid',
        'proveedornombre',
        'fecha',
        'sucursal',
        'horainicio',
        'horafin',
        'duracioncita',
        'usuregid',
        'usuregnombre',
    ];
}
