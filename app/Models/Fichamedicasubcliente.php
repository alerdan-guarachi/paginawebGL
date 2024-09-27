<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fichamedicasubcliente extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fichamedicasubclientes';
    static $rules = [
        'id' => '',
        'clienteid' => '',
        'nombrecompleto' => '',
        'document' => '',
        'usuarioid' => '',
        'usuarioregistro' => '',
    ]; 

    protected $fillable = [
        'id',
        'clienteid',
        'nombrecompleto',
        'document',
        'usuarioid',
        'usuarioregistro',
    ];

}
