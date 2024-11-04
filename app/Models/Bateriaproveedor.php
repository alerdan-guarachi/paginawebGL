<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bateriaproveedor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'bateriaproveedores';
    static $rules = [
        'id' => '',
        'proveedorid' => 'max:45',
        'proveedor' => 'required|max:45',
        'area' => 'required|max:45',
        'accion' => 'required|max:45',
        'horarioinicial' => '',
        'horariofinal' => '',
        'tiempoatencion' => '',
        'precio'=>'',
        'tipoarea'=>'',
        'usuarioid'=>'',
        'usuarioregistro'=>'',
        'sucursal'=>'required',
        'preciocompra'=>'',
        'asociado'=>'required',
        'asociadoid'=>'',
        'servicio'=>'',
        'estado'=>'required',
        'tipoid'=>'',
        'areasid'=>'',
    ]; 

    protected $fillable = [
        'id',
        'proveedorid',
        'proveedor',
        'area',
        'accion',
        'horarioinicial',
        'horariofinal',
        'tiempoatencion',
        'precio',
        'tipoarea',
        'usuarioid',
        'usuarioregistro',
        'sucursal',
        'preciocompra',
        'asociado',
        'asociadoid',
        'servicio',
        'estado',
        'tipoid',
        'areasid',
    ];
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor', 'proveedor');
    }
}
