<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consolidadocaja extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'consolidadocaja';
    static $rules = [
        'id' => '',
        'usuarioconsolidadoid' => '',
        'usuarioconsolidadonombre' => '',
        'consolidadoefectivo' => '',
        'consolidadodeposito' => '',
        'consolidadotransferencia' => '',
        'consolidadocheque' => '',
        'consolidadoatc' => '',
        'consolidadocxc' => '',
        'consolidadocpp' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'usuarioconsolidadoid',
        'usuarioconsolidadonombre',
        'consolidadoefectivo',
        'consolidadodeposito',
        'consolidadotransferencia',
        'consolidadocheque',
        'consolidadoatc',
        'consolidadocxc',
        'consolidadocpp',
        'usuarioregistroid',
        'usuarioregistronombre',

    ];
}
