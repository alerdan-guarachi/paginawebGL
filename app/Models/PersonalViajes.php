<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalViajes extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'personalviajes';
    static $rules = [
        'id' => '',
        'proveedorid' => '',
        'proveedornombre' => '',
        'destino' => '',
        'motivoviaje' => '',
        'fechasalida' => '',
        'fecharetorno' => '',
        'cantidaddias' => '',
        'mediotransporte' => '',
        'requierehospedaje' => '',
        'montosolicitado' => '',
        'observaciones' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'estado' => '',
        'usuarioautorizacion' => '',
        'motivorechazo' => '',
    ]; 

    protected $fillable = [
        'id',
        'proveedorid',
        'proveedornombre',
        'destino',
        'motivoviaje',
        'fechasalida',
        'fecharetorno',
        'cantidaddias',
        'mediotransporte',
        'requierehospedaje',
        'montosolicitado',
        'observaciones',
        'usuarioregistroid',
        'usuarioregistronombre',
        'estado',
        'usuarioautorizacion',
        'motivorechazo',
    ];

    public function itinerario()
    {
        return $this->hasOne(PersonalViajesItinerario::class, 'viajeid', 'id');
    }

    public function cronograma()
    {
        return $this->hasMany(PersonalViajesCronograma::class, 'viajeid', 'id');
    }

}
