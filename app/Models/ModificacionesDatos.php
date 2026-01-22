<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModificacionesDatos extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'modificacionesdatos';
    static $rules = [
        'id' => '',
        'tabla' => '',
        'columna' => '',
        'datoantiguo' => '',
        'datonuevo' => '',
        'usuarioedicionid' => '',
        'usuarioedicionnombre' => '',
        'clienteid' => '',
        'clientenombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'tabla',
        'columna',
        'datoantiguo',
        'datonuevo',
        'usuarioedicionid',
        'usuarioedicionnombre',
        'clienteid',
        'clientenombre',
    ];
}
