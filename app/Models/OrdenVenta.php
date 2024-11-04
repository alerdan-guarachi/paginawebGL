<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenVenta extends Model
{
    use HasFactory;

    // Especificar el nombre de la tabla
    protected $table = 'ordenventa';

    // Desactivar timestamps, ya que la tabla no contiene `created_at` ni `updated_at`
    public $timestamps = false;

    // Asignación de atributos
    protected $fillable = [
        'modalidadpago',
        'formapago',
        'fechapago',
        'clienteasociado',
        'personalcliente',
        'sucursal',
        'detalle',
        'montoneto',
        'descuento',
        'montototal',
        'usuarioaprocredito',
        'usuarioid',
        'usuarioregistro'
    ];

    // Reglas de validación
    public static $rules = [
        'modalidadpago' => 'nullable|string|max:45',
        'formapago' => 'nullable|string|max:45',
        'fechapago' => 'nullable|string|max:45',
        'clienteasociado' => 'nullable|string|max:255',
        'personalcliente' => 'nullable|string|max:255',
        'sucursal' => 'nullable|string|max:45',
        'detalle' => 'nullable|string|max:255',
        'montoneto' => 'nullable|string|max:45',
        'descuento' => 'nullable|string|max:45',
        'montototal' => 'nullable|string|max:45',
        'usuarioaprocredito' => 'nullable|string|max:255',
        'usuarioid' => 'nullable|exists:users,id|max:45',
        'usuarioregistro' => 'nullable|exists:users,name|max:255'
    ];

    // Relaciones con otros modelos

    // Relación con el modelo ClienteBanco
    public function clienteBanco()
    {
        return $this->belongsTo(ClienteBanco::class, 'personalcliente', 'nombrecompleto');
    }

    // Relación con el modelo Asociado
    public function asociado()
    {
        return $this->belongsTo(Asociado::class, 'clienteasociado', 'asociado');
    }

    // Relación con el modelo User para el campo usuarioid
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuarioid');
    }

    // Relación con el modelo User para el campo usuarioregistro
    public function usuarioRegistro()
    {
        return $this->belongsTo(User::class, 'usuarioregistro', 'name');
    }
}