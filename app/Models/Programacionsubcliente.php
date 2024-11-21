<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Programacionsubcliente extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules = [
        'id' => '',
        'clientebancoid' => '',
        'clientenombre' => '',
        /* 'clienteid' => 'required|max:45', */
        'proveedornombre' => '',
        /* 'proveedorid' => 'required|max:45', */
        'horaasignada' => '',
        'fechaasignada' => '',
        'usuarioid' => '',
        'usuarioregistro' => '',
        'accionnombre' => '',
        'areanombre' => '',
        'clientecomunid' => '',
        'clientecomunnombre' => '',
        'clienteauditoriaid' => '',
        'clienteauditorianombre' => '',
        'clienteitaid' => '',
        'clienteitanombre' => '',
        'motivoreprogramacion' => '',
        'precio' => '',
        'preciocompra' =>'',
        'fechabateria' =>'',
        'horadesde' =>'',
        'horahasta' =>'',
        'usuarioactualizacion' =>'',
        'usuarioeliminacion' =>'',
        'pagoatencion' =>'',
    ]; 

    protected $fillable = [
        'id',
        'clientebancoid',
        'clientenombre',
        /* 'clienteid', */
        'proveedornombre',
/*         'proveedorid', */
        'horaasignada',
        'fechaasignada',
        'usuarioid',
        'usuarioregistro',
        'accionnombre',
        'areanombre',
        'clientecomunid',
        'clientecomunnombre',
        'clienteauditoriaid',
        'clienteauditorianombre',
        'clienteitaid',
        'clienteitanombre',
        'motivoreprogramacion',
        'precio',
        'preciocompra',
        'fechabateria',
        'horadesde',
        'horahasta',
        'usuarioactualizacion',
        'usuarioeliminacion',
        'pagoatencion',
    ];
    public function clienteAuditoria()
    {
        return $this->belongsTo(ClienteAuditoria::class, 'clienteauditorianombre', 'nombrecompleto');
    }
    public function clienteComun()
    {
        return $this->belongsTo(ClienteComun::class, 'clientecomunnombre', 'nombrecompleto');
    }
    public function clienteIta()
    {
        return $this->belongsTo(Cliente::class, 'clienteitanombre', 'nombrecompleto');
    }
    public function clienteBanco()
    {
        return $this->belongsTo(ClienteBanco::class, 'clientenombre', 'nombrecompleto');
    }
    public function estadoprogramacionsubcliente()
    {
        return $this->hasMany(Estadoprogramacionsubcliente::class, 'clienteitaid', 'clienteitaid');
    }
    public function documentacionsubcliente()
    {
        return $this->hasMany(Documentacionsubcliente::class, 'clienteitaid', 'clienteitaid');
    }
    public function requisitosubcliente()
    {
        return $this->hasMany(Requisitosubcliente::class, 'clienteitaid', 'clienteitaid');
    }
    public function requisitosclienteauditoria()
    {
        return $this->hasMany(Requisitosclientesauditoria::class, 'clienteitaid', 'clienteitaid');
    }
    public function requisitosclienteauditoriamedica()
    {
        return $this->hasMany(Requisitosclientesauditoria::class, 'clienteauditoriaid', 'clienteauditoriaid');
    }
    public function bateriasubcliente()
    {
        return $this->hasMany(Bateriasubcliente::class, 'clienteitaid', 'clienteitaid');
    }
    public function tramitesubcliente()
    {
        return $this->hasMany(Tramitesubcliente::class, 'clienteitaid', 'clienteitaid');
    }
    public function estadoprogramacionsubclientecomun()
    {
        return $this->hasMany(Estadoprogramacionsubcliente::class, 'clientecomunid', 'clientecomunid');
    }
    public function documentacionsubclientecomun()
    {
        return $this->hasMany(Documentacionsubcliente::class, 'clientecomunid', 'clientecomunid');
    }
    public function estadoprogramacionsubclienteauditoria()
    {
        return $this->hasMany(Estadoprogramacionsubcliente::class, 'clienteauditoriaid', 'clienteauditoriaid');
    }
    public function documentacionsubclienteauditoria()
    {
        return $this->hasMany(Documentacionsubcliente::class, 'clienteauditoriaid', 'clienteauditoriaid');
    }
    public function proveedorinformesfinales()
    {
        return $this->hasMany(ProveedorInformefinal::class, 'clienteitaid', 'clienteitaid');
    }
    public function informesfinales()
    {
        return $this->hasMany(Informefinal::class, 'clienteitaid', 'clienteitaid');
    }
    public function documentacionsubclientebanco()
    {
        return $this->hasMany(Documentacionsubcliente::class, 'clientebancoid', 'clientebancoid');
    }
}
