<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalViajesItinerario extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'personalviajesitinerario';
    static $rules = [
        'id' => '',
        'viajeid' => '',
        'transporte' => '',
        'numerovuelo' => '',
        'fechahorasalida' => '',
        'fechahorallegada' => '',
        'nombrehotel' => '',
        'direccionhotel' => '',
        'ingresohotel' => '',
        'salidahotel' => '',
        'boletotransporte' => '',
        'reservahotel' => '',
        'montotransporte' => '',
        'montoalimentacion' => '',
        'montootrosgastos' => '',
        'montototal' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'rendicionmontotransporte' => '',
        'rendicionmontoalimentacion' => '',
        'rendicionmontootrosgastos' => '',
        'rendicionmontototal' => '',
        'rendicioncomprobante' => '',
        'rendicionmontodiferencia' => '',
        'rendicionresultado' => '',
    ]; 

    protected $fillable = [
        'id',
        'viajeid',
        'transporte',
        'numerovuelo',
        'fechahorasalida',
        'fechahorallegada',
        'nombrehotel',
        'direccionhotel',
        'ingresohotel',
        'salidahotel',
        'boletotransporte',
        'reservahotel',
        'montotransporte',
        'montoalimentacion',
        'montootrosgastos',
        'montototal',
        'usuarioregistroid',
        'usuarioregistronombre',
        'rendicionmontotransporte',
        'rendicionmontoalimentacion',
        'rendicionmontootrosgastos',
        'rendicionmontototal',
        'rendicioncomprobante',
        'rendicionmontodiferencia',
        'rendicionresultado',
    ];
}
