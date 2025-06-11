<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortafolioProveedores extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'portafolioproveedores';
    
    // Indica que el ID no es autoincremental
    public $incrementing = false;

    // El tipo de datos de 'id' es varchar, no es necesario auto incremento
    protected $keyType = 'string';
    
    public function getIdAttribute($value)
    {
        return $value;
    }
    static $rules = [
        'id' => 'required',
        'proveedorid' => '',
        'proveedornombre' => '',
        'ciudad' => '',
        'tipoinventario' => '',
        'seccion' => '',
        'nombreproducto' => '',
        'materiaprima' => '',
        'especificacionmedida' => '',
        'color' => '',
        'marca' => '',
        'unidadmedida' => '',
        'presentacion' => '',
        'unidades' => '',
        'cantidad' => '',
        'precio' => '',
        'preciounitario' => '',
        'modelo' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'estado' => '',
    ]; 

    protected $fillable = [
        'id',
        'proveedorid',
        'proveedornombre',
        'ciudad',
        'tipoinventario',
        'seccion',
        'nombreproducto',
        'materiaprima',
        'especificacionmedida',
        'color',
        'marca',
        'unidadmedida',
        'presentacion',
        'unidades',
        'cantidad',
        'precio',
        'preciounitario',
        'modelo',
        'usuarioregistroid',
        'usuarioregistronombre',
        'estado',
    ];

}
