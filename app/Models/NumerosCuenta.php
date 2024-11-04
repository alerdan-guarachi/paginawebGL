<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumerosCuenta extends Model
{
    use HasFactory;

    protected $table = 'numeroscuentas';

    // Desactivar timestamps, ya que la tabla no contiene `created_at` ni `updated_at`
    public $timestamps = false;

    // Asignación de atributos
    protected $fillable = [
        'banco',
        'numerocuenta',
        'nombrecuenta',
        'titularcuenta',
        'estado'
    ];

    // Reglas de validación
    public static $rules = [
        'banco' => 'required|string|max:255',
        'numerocuenta' => 'required|string|max:45|unique:numeroscuentas,numerocuenta',
        'nombrecuenta' => 'nullable|string|max:255',
        'titularcuenta' => 'nullable|string|max:255',
        'estado' => 'nullable|string|max:45'
    ];
}