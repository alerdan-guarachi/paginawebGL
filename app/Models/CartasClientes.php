<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartasClientes extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'cartasclientes';
    static $rules = [
        'id' => '',
        'clienteid' => '',
        'clientenombre' => '',
        'detalle' => '',
        'documento' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'fecha' => '',
    ]; 

    protected $fillable = [
        'id',
        'clienteid',
        'clientenombre',
        'detalle',
        'documento',
        'usuarioregistroid',
        'usuarioregistronombre',
        'fecha',
    ];
}
