<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CCyCPdetalles extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'ccycpdetalles';
    static $rules = [
        'id' => '',
        'idusuario' => '',
        'tipocuenta' => '',
        'detalle' => '',
        'precio' => '',
    ]; 

    protected $fillable = [
        'id',
        'idusuario',
        'tipocuenta',
        'detalle',
        'precio',
    ];

}
