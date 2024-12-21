<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProveedorInformefinal extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'proveedorinformesfinales';
    static $rules = [
        'id',
        'clienteitaid' => '',
        'clienteitanombre' => '',
        'clientecomunid' => '',
        'clientecomunnombre' => '',
        'clientebancoid' => '',
        'clientebanconombre' => '',
        'clienteauditoriaid' => '',
        'clienteauditorianombre' => '',
        'fechabateria' => '',
        'proveedorasignado' => 'required',
        'celularproveedor' => 'required',
        'usuarioid' => 'required',
        'usuarioregistro' => '',
        'precio' => 'required',
        'preciocompra' => 'required',
        'servicio' => 'required',
        'pagoinforme' => '',
        'accionnombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'clienteitaid',
        'clienteitanombre',
        'clientecomunid',
        'clientecomunnombre',
        'clientebancoid',
        'clientebanconombre',
        'clienteauditoriaid',
        'clienteauditorianombre',
        'fechabateria',
        'proveedorasignado',
        'celularproveedor',
        'usuarioid',
        'usuarioregistro',
        'precio',
        'preciocompra',
        'servicio',
        'pagoinforme',
        'accionnombre',
    ];
    /* public function programacionSubcliente()
    {
        return $this->belongsTo(Programacionsubcliente::class, 'clienteitaid', 'clienteitaid');
    } */

}
