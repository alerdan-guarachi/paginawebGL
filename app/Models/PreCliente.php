<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreCliente extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'preclientes';
    static $rules = [
        'id' => '',
        'apepaterno' => '',
        'apematerno' => '',
        'nombres' => '',
        'nombrecompleto' => '',
        'ci' => '',
        'email' => '',
        'celular' => '',
        'estado' => '',
        'sucursal' => '',
    ]; 

    protected $fillable = [
        'id',
        'apepaterno',
        'apematerno',
        'nombres',
        'nombrecompleto',
        'ci',
        'email',
        'celular',
        'estado',
        'sucursal',
    ];
}
