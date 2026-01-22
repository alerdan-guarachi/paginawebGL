<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdjuntoaCartas extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'adjuntoacartas';
    static $rules = [
        'id' => '',
        'clienteid' => '',
        'cientenombre' => '',
        'tipo' => '',
        'idcarta' => '',
        'bancoadjunto' => '',
        'idadjunto' => '',
        'nroorden' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'clienteid',
        'cientenombre',
        'tipo',
        'idcarta',
        'bancoadjunto',
        'idadjunto',
        'nroorden',
        'usuarioregistroid',
        'usuarioregistronombre',
    ];
}
