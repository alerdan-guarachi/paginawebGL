<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anuncio extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'anuncios';
    static $rules = [
        'id' => '',
        'titulo' => '',
        'imagen' => '',
        'link' => '',
        'estado' => '',
        'orden' => '',
        'fecha_inicio' => '',
        'fecha_fin' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'titulo',
        'imagen',
        'link',
        'estado',
        'orden',
        'fecha_inicio',
        'fecha_fin',
        'usuarioregistroid',
        'usuarioregistronombre',
    ];
}
