<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanillasPagosGeneradas extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'planillaspagosgeneradas';
    static $rules = [
        'id' => '',
        'proveedor' => '',
        'fechapago' => '',
        'tipo' => '',
        'documento' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'proveedor',
        'fechapago',
        'tipo',
        'documento',
        'usuarioregistroid',
        'usuarioregistronombre',
    ];
}
