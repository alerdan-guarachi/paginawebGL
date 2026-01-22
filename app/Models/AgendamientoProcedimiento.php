<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgendamientoProcedimiento extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'agendamientoprocedimientos';
    static $rules = [
        'id' => '',
        'clienteid' => '',
        'clientenombre' => '',
        'tramite' => '',
        'fechaprogramacion' => '',
        'horaprogramacion' => '',
        'documentoagendamiento' => '',
        'asistencia' => '',
        'fechaanterior' => '',
        'horaanterior' => '',
        'documentoanterior' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'motivoreprogramacion' => '',
    ]; 

    protected $fillable = [
        'id',
        'clienteid',
        'clientenombre',
        'tramite',
        'fechaprogramacion',
        'horaprogramacion',
        'documentoagendamiento',
        'asistencia',
        'fechaanterior',
        'horaanterior',
        'documentoanterior',
        'usuarioregistroid',
        'usuarioregistronombre',
        'motivoreprogramacion',
    ];
}
