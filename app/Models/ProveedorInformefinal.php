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
        'fechacredito' =>'',
        'detallecredito' =>'',
        'pagoservicio' =>'',
        'usuarioautorizador' =>'',
        'documentocredito' =>'',
        'atencionservicio' =>'',
        'documentolcambio' => '',
        'nrofactura' => '',
        'comprobante' => '',
        'cheque' => '',
        'factura' => '',
        'usuariocomprobante' => '',
        'codautorizacion' => '',
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
        'fechacredito',
        'detallecredito',
        'pagoservicio',
        'usuarioautorizador',
        'documentocredito',
        'atencionservicio',
        'documentolcambio',
        'nrofactura',
        'comprobante',
        'cheque',
        'factura',
        'usuariocomprobante',
        'codautorizacion',
    ];
    /* public function programacionSubcliente()
    {
        return $this->belongsTo(Programacionsubcliente::class, 'clienteitaid', 'clienteitaid');
    } */

    public function tramitesubcliente()
    {
        return $this->hasMany(Tramitesubcliente::class, 'clienteitaid', 'clienteitaid');
    }
    public function tramitesubclienteauditoria()
    {
        return $this->hasMany(Tramitesubcliente::class, 'clienteauditoriaid', 'clienteauditoriaid');
    }
    public function requisitosubcliente()
    {
        return $this->hasMany(Requisitosubcliente::class, 'clienteitaid', 'clienteitaid');
    }
    public function requisitosclienteauditoriamedica()
    {
        return $this->hasMany(Requisitosclientesauditoria::class, 'clienteauditoriaid', 'clienteauditoriaid');
    }
    public function proveedorinformesfinales()
    {
        return $this->hasMany(ProveedorInformefinal::class, 'clienteitaid', 'clienteitaid');
    }
    public function proveedorinformesfinalesauditoria()
    {
        return $this->hasMany(ProveedorInformefinal::class, 'clienteauditoriaid', 'clienteauditoriaid');
    }
    public function informesfinales()
    {
        return $this->hasMany(Informefinal::class, 'clienteitaid', 'clienteitaid');
    }
    public function informesfinalesauditoria()
    {
        return $this->hasMany(Informefinal::class, 'clienteauditoriaid', 'clienteauditoriaid');
    }
    public function requisitosclienteauditoria()
    {
        return $this->hasMany(Requisitosclientesauditoria::class, 'clienteitaid', 'clienteitaid');
    }
    public function clienteIta()
    {
        return $this->belongsTo(Cliente::class, 'clienteitanombre', 'nombrecompleto');
    }
}
