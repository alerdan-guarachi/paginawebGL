<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bateriasubcliente extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules = [
        'id' => '',
        'clienteid' => 'max:45',
        'areaid' => 'max:45',
        'accionid' => 'max:45',
        'clientenombre' => 'max:45',
        'areanombre' => 'max:45',
        'accionnombre' => 'max:45',
        'clientecomunid' => 'max:45',
        'clientecomunnombre' => 'max:45',
        'clienteauditoriaid' => 'max:45',
        'clienteauditorianombre' => 'max:45',
        'clienteitaid' => 'max:45',
        'clienteitanombre' => 'max:45',
        'tipoarea' => '',
        'precio' => '',
        'fechabateria' => '',
        'antecedentes' => '',
        'informe' => '',
        'fechainforme' => '',
        'preciocompra' => '',
        'proveedorasignado' => '',
        'servicio' => '',
        'usuarioid' => '',
        'usuarioregistro' => '',
    ]; 

    protected $fillable = [
        'id',
        'clienteid',
        'areaid',
        'accionid',
        'clientenombre',
        'areanombre',
        'accionnombre',
        'clientecomunid',
        'clientecomunnombre',
        'clienteauditoriaid',
        'clienteauditorianombre',
        'clienteitaid',
        'clienteitanombre',
        'tipoarea',
        'precio',
        'fechabateria',
        'antecedentes',
        'informe',
        'fechainforme',
        'preciocompra',
        'proveedorasignado',
        'servicio',
        'usuarioid',
        'usuarioregistro',
    ];
    public function cliente()
{
    return $this->belongsTo(Cliente::class, 'nombrecompleto', 'clienteitanombre'); // Ajusta los nombres de las claves según tu esquema
}

}
