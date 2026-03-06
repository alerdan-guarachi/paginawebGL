<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtMedBloqueos extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'atmedbloqueos';
    static $rules = [
        'id' => '',
        'proveedorid' => '',
        'proveedornombre' => '',
        'fecha' => '',
        'sucursal' => '',
        'horainicio' => '',
        'horafin' => '',
        'motivo' => '',
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
        'motivo',
        'usuregid',
        'usuregnombre',
    ];
}
