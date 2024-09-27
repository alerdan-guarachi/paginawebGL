<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Serviciosrequisito extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules = [
        'id' => '',
        'servicioid' => '',
        'servicionombre' => '',
        'importancia' => '',
        'requisitodetallado' => '',
        'requisito' => '',
        'usuarioid' => '',
        'usuarioregistro' => '',
    ]; 

    protected $fillable = [
        'id',
        'servicioid',
        'servicionombre',
        'importancia',
        'requisitodetallado',
        'requisito',
        'usuarioid',
        'usuarioregistro',
    ];

}
