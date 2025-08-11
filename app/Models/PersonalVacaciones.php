<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalVacaciones extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'personalvacaciones';
    static $rules = [
        'id' => '',
        'nroboleta' => '',
        'proveedorid' => '',
        'proveedornombre' => '',
        'observacion' => '',
        'fechainicial' => '',
        'fechafinal' => '',
        'cantidaddias' => '',
        'estado' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'usuarioautorizacion' => '',
        'motivorechazo' => '',
        'boleta' => '',
        'boletafirmada' => '',
        'fecharetorno' => '',
        'adelanto' => '',
    ]; 

    protected $fillable = [
        'id',
        'nroboleta',
        'proveedorid',
        'proveedornombre',
        'observacion',
        'fechainicial',
        'fechafinal',
        'cantidaddias',
        'estado',
        'usuarioregistroid',
        'usuarioregistronombre',
        'usuarioautorizacion',
        'motivorechazo',
        'boleta',
        'boletafirmada',
        'fecharetorno',
        'adelanto',
    ];
}
