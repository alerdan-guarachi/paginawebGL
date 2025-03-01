<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordenes extends Model
{
    use HasFactory;

    // Especificar el nombre de la tabla
    protected $table = 'ordenes';
    public static $rules = [
        'detalle' => '',
        'cantidad' => '',
        'fechacomprar' => '',
        'proveedorid' => '',
        'proveedornombre' => '',
        'tipotransaccion' => '',
        'estado' => '',
        'orden' => '',
        'usuarioregitroid' => '',
        'usuarioregistronomre' => '',
    ];

    // Asignación de atributos
    protected $fillable = [
        'detalle',
        'cantidad',
        'fechacomprar',
        'proveedorid',
        'proveedornombre',
        'tipotransaccion',
        'estado',
        'orden',
        'usuarioregitroid',
        'usuarioregistronomre',
    ];

}