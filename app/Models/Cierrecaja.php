<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cierrecaja extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'cierrecaja';
    static $rules = [
        'id' => '',
        'usuariocierreid' => '',
        'usuariocierrenombre' => '',
        'cierreefectivo' => '',
        'cierredeposito' => '',
        'cierretransferencia' => '',
        'cierrecheque' => '',
        'cierreatc' => '',
        'cierrecxc' => '',
        'cierrecxp' => '',
    ]; 

    protected $fillable = [
        'id',
        'usuariocierreid',
        'usuariocierrenombre',
        'cierreefectivo',
        'cierredeposito',
        'cierretransferencia',
        'cierrecheque',
        'cierreatc',
        'cierrecxc',
        'cierrecxp',
    ];
}
