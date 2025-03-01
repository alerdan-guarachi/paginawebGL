<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrdenes extends Model
{
    use HasFactory;

    // Especificar el nombre de la tabla
    protected $table = 'detalleordenes';

    public static $rules = [
        'idordenventa' => 'nullable|string|max:45',
        'detalle' => 'nullable|string|max:45',
        'clienteasociado' => 'nullable|string|max:255',
        'personalcliente' => 'nullable|string|max:255',
        'sucursal' => 'nullable|string|max:45',
        'preciounitario' => 'nullable|string|max:45',
        'descuento' => 'nullable|string|max:45',
        'preciototal' => 'nullable|string|max:45',
        'usuarioid' => 'nullable|exists:users,id|max:45',
        'usuarioregistro' => 'nullable|exists:users,name|max:255'
    ];

    protected $fillable = [
        'idordenventa',
        'detalle',
        'clienteasociado',
        'personalcliente',
        'sucursal',
        'preciounitario',
        'descuento',
        'preciototal',
        'usuarioid',
        'usuarioregistro'
    ];
}