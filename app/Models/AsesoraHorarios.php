<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsesoraHorarios extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'asesorahorarios';
    static $rules = [
        'id' => '',
        'asesorid' => '',
        'asesornombre' => '',
        'dia' => '',
        'horainicio' => '',
        'horafin' => '',
        'duracioncita' => '',
        'estado' => '',
    ]; 

    protected $fillable = [
        'id',
        'asesorid',
        'asesornombre',
        'dia',
        'horainicio',
        'horafin',
        'duracioncita',
        'estado',
    ];
}
