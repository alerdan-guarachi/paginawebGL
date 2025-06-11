<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordenes extends Model
{
    use HasFactory;

    // Especificar el nombre de la tabla
    protected $table = 'ordenes';
    protected $primaryKey = 'id'; // Especificar la clave primaria
    public $incrementing = false; // Desactivar auto-incremento
    protected $keyType = 'string'; // Asegurar que se trata como string
    public static $rules = [
        'id' => '',
        'detalle' => '',
        'montototal' => '',
        'subtotal' => '',
        'descuento' => '',
        'fechacomprar' => '',
        'proveedorid' => '',
        'proveedornombre' => '',
        'tipotransaccion' => '',
        'estado' => '',
        'orden' => '',
        'documentoorden' => '',
        'usuarioregistroid' => '',
        'tipoorden' => '',
        'usuarioregistronombre' => '',
        'observacion' => '',
        'formapago' => '',
        'fechapagar' => '',
        'usuariopreorden' => '',
        'sucursal' => '',
        'sucursalgasto' => '',
    ];

    // Asignación de atributos
    protected $fillable = [
        'id',
        'detalle',
        'montototal',
        'subtotal',
        'descuento',
        'fechacomprar',
        'proveedorid',
        'proveedornombre',
        'tipotransaccion',
        'estado',
        'orden',
        'documentoorden',
        'usuarioregistroid',
        'tipoorden',
        'usuarioregistronombre',
        'observacion',
        'formapago',
        'fechapagar',
        'usuariopreorden',
        'sucursal',
        'sucursalgasto',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleOrdenes::class, 'ordenid', 'id');
    }
    public function detallesrecibos()
    {
        return $this->hasMany(Detallerecibo::class, 'ordenid', 'id');
    }
    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'codigo', 'id');
    }
}
