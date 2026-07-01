<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoCodigo extends Model
{
    use HasFactory;

    // Nombre de la tabla asociado con el modelo
    protected $table = 'permisos_codigo';

    // Clave primaria de la tabla
    protected $primaryKey = 'id';

    // Deshabilitar timestamps si no se utilizan en la tabla
    public $timestamps = true;

    // Atributos asignables en masa
    protected $fillable = [
        'usuarioSolicitante',
        'usuarioAutorizador',
        'codigo',
        'fechaSolicitada',
        'tiempoLimite',
        'permisoSolicitado',
        'motivo',
        'clienteid',
        'horaActivacion',
        'estado',
    ];

    // Valores por defecto de los atributos
    protected $attributes = [
        'estado' => 'pendiente',
    ];

    // Especificar los tipos de los atributos
    protected $casts = [
        'fechaSolicitada' => 'date',
        'horaActivacion' => 'datetime',
        'tiempoLimite' => 'integer',
    ];
}
