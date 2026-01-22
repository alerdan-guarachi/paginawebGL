<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CambiosApoderados extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'cambiosapoderados';
    static $rules = [
        'id' => '',
        'clienteid' => '',
        'clientenombre' => '',
        'tramite' => '',
        'tramiteid' => '',
        'apoderadoanterior' => '',
        'apoderadoactual' => '',
        'motivocambio' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'fechaasignacionanterior' => '',
        'fechaasignacionactual' => '',
    ]; 

    protected $fillable = [
        'id',
        'clienteid',
        'clientenombre',
        'tramite',
        'tramiteid',
        'apoderadoanterior',
        'apoderadoactual',
        'motivocambio',
        'usuarioregistroid',
        'usuarioregistronombre',
        'fechaasignacionanterior',
        'fechaasignacionactual',
    ];
}
