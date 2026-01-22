<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecomendacionBaterias extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'recomendacionbaterias';
    static $rules = [
        'id' => '',
        'clienteid' => '',
        'clientenombre' => '',
        'tipoarea' => '',
        'area' => '',
        'estudioespecialidad' => '',
        'tramite' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'clienteid',
        'clientenombre',
        'tipoarea',
        'area',
        'estudioespecialidad',
        'tramite',
        'usuarioregistroid',
        'usuarioregistronombre',
    ];
}
