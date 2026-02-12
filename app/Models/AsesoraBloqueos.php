<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsesoraBloqueos extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'asesorabloqueos';
    static $rules = [
        'id' => '',
        'asesorid' => '',
        'asesornombre' => '',
        'fecha' => '',
        'horainicio' => '',
        'horafin' => '',
        'motivo' => '',
    ]; 

    protected $fillable = [
        'id',
        'asesorid',
        'asesornombre',
        'fecha',
        'horainicio',
        'horafin',
        'motivo',
    ];
}
