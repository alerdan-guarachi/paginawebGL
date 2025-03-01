<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detalleseccionprov extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'detalleseccionprov';
    static $rules = [
        'id' => '',
        'seccion' => 'required',
        'subseccion' => 'required',
        'proveedorid' => 'required',
        'proveedornombre' => 'required',
    ]; 

    protected $fillable = [
        'id',
        'seccion',
        'subseccion',
        'proveedorid',
        'proveedornombre',
    ];
}
