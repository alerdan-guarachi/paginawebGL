<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartaReclamo extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'cartasreclamosgestoraaps';
    static $rules = [
        'id' => '',
        'clienteitaid' => 'max:45',
        'clienteitanombre' => 'max:45',
        'apoderado' => 'max:45',
        'cartareclamo' => 'max:45',
        'document' => 'max:45',
        'usuarioid' => 'max:45',
        'usuarioregistro' => 'max:45',
    ]; 

    protected $fillable = [
        'id',
        'clienteitaid',
        'clienteitanombre',
        'apoderado',
        'cartareclamo',
        'document',
        'usuarioid',
        'usuarioregistro',
    ];

}
