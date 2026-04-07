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
        'clientenombre' => 'max:255',
        'tipocliente' => 'max:45',
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
        'pagoservicio' => '',
        'fechacredito' => '',
        'usuarioautorizador' => '',
        'documentocredito' => '',
        'documentolcambio' => '',
        'comision' => '',
        'sesiones' => '',
        'provinfofinalid' => '',
        'pagoatencion' => '',
        'motivoanulacion' => '',
        'usuarioanulacion' => '',
        'cantidadcuotas' => '',
        'estadocredito' => '',
        'medicoderivante' => '',
        'orden' => '',
        'tramite' => '',
        'prioridad' => '',
        'nrobancoorigen' => '',
        'fechapago' => '',
        'estadoaprobacion' => '',
        'comprobante' => '',
        'cheque' => '',
        'usuariocomprobante' => '',
        'ordenid' => '',
        'fechamora' => '',
        'idsubproc' => '',
    ]; 

    protected $fillable = [
        'id',
        'clienteid',
        'areaid',
        'accionid',
        'clientenombre',
        'tipocliente',
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
        'pagoservicio',
        'fechacredito',
        'usuarioautorizador',
        'documentocredito',
        'documentolcambio',
        'comision',
        'sesiones',
        'provinfofinalid',
        'pagoatencion',
        'motivoanulacion',
        'usuarioanulacion',
        'cantidadcuotas',
        'estadocredito',
        'medicoderivante',
        'orden',
        'tramite',
        'prioridad',
        'nrobancoorigen',
        'fechapago',
        'estadoaprobacion',
        'comprobante',
        'cheque',
        'usuariocomprobante',
        'ordenid',
        'fechamora',
        'idsubproc',
    ];
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'nombrecompleto', 'clienteitanombre');
    }

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

    public function estadoprogramacionsubcliente()
    {
        return $this->hasMany(Estadoprogramacionsubcliente::class, 'clienteitaid', 'clienteitaid');
    }
    public function documentacionsubcliente()
    {
        return $this->hasMany(Documentacionsubcliente::class, 'clienteitaid', 'clienteitaid');
    }
    public function programacionsubcliente()
    {
        return $this->hasMany(Programacionsubcliente::class, 'clienteitaid', 'clienteitaid');
    }
    public function informesfinales()
    {
        return $this->hasMany(Informefinal::class, 'clienteitaid', 'clienteitaid');
    }
    public function pagoservicio()
    {
        return $this->hasMany(Detallerecibo::class, 'bateriaid', 'id');
    }
    public function pagoservicioinformefinal()
    {
        return $this->hasMany(Detallerecibo::class, 'provinfofinalid', 'provinfofinalid');
    }
    public function provinfofinal()
    {
        return $this->hasMany(ProveedorInformefinal::class, 'clienteitaid', 'clienteitaid');
    }
    public function estadoprogramacionsubclienteauditoria()
    {
        return $this->hasMany(Estadoprogramacionsubcliente::class, 'clienteauditoriaid', 'clienteauditoriaid');
    }
    public function documentacionsubclienteauditoria()
    {
        return $this->hasMany(Documentacionsubcliente::class, 'clienteauditoriaid', 'clienteauditoriaid');
    }
    public function programacionsubclienteauditoria()
    {
        return $this->hasMany(Programacionsubcliente::class, 'clienteauditoriaid', 'clienteauditoriaid');
    }
    public function informesfinalesauditoria()
    {
        return $this->hasMany(Informefinal::class, 'clienteauditoriaid', 'clienteauditoriaid');
    }
    public function provinfofinalauditoria()
    {
        return $this->hasMany(ProveedorInformefinal::class, 'clienteauditoriaid', 'clienteauditoriaid');
    }
    
    public function estadoprogramacionsubclientecomun()
    {
        return $this->hasMany(Estadoprogramacionsubcliente::class, 'clientecomunid', 'clientecomunid');
    }
    public function documentacionsubclientecomun()
    {
        return $this->hasMany(Documentacionsubcliente::class, 'clientecomunid', 'clientecomunid');
    }
    public function programacionsubclientecomun()
    {
        return $this->hasMany(Programacionsubcliente::class, 'clientecomunid', 'clientecomunid');
    }
    public function informesfinalescomun()
    {
        return $this->hasMany(Informefinal::class, 'clientecomunid', 'clientecomunid');
    }
    public function provinfofinalcomun()
    {
        return $this->hasMany(ProveedorInformefinal::class, 'clientecomunid', 'clientecomunid');
    }

    public function programacionproveedor()
    {
        return $this->hasMany(Programacionsubcliente::class, 'proveedornombre', 'proveedorasignado');
    }
    public function proveedorasignado()
    {
        return $this->hasMany(Proveedor::class, 'proveedor', 'proveedorasignado');
    }

    public function programacion()
    {
        return $this->hasOne(Programacionsubcliente::class, 'bateriaid', 'id');
    }
    public function programaciones()
    {
        return $this->hasMany(Programacionsubcliente::class, 'bateriaid');
    }
    public function proveedorinformefinal()
    {
        return $this->hasOne(ProveedorInformefinal::class, 'id', 'provinfofinalid');
    }
        public function proveedoresmedicos()
    {
        return $this->hasOne(Proveedor::class, 'proveedor', 'proveedorasignado');
    }
    public function clienteita2()
    {
        return $this->belongsTo(Cliente::class, 'clienteitaid');
    }

    public function clienteauditoria2()
    {
        return $this->belongsTo(ClienteAuditoria::class, 'clienteauditoriaid');
    }

    public function clientecomun2()
    {
        return $this->belongsTo(ClienteComun::class, 'clientecomunid');
    }
    public function tramitesubclienteita()
    {
        return $this->hasMany(Tramitesubcliente::class, 'clienteitaid', 'clienteitaid');
    }
    public function tramitesubclienteauditoria()
    {
        return $this->hasMany(Tramitesubcliente::class, 'clienteauditoriaid', 'clienteauditoriaid');
    }
    public function tramitesubclientecomun()
    {
        return $this->hasMany(Tramitesubcliente::class, 'clientecomunid', 'clientecomunid');
    }


    public function estadoprogclientes()
    {
        return $this->hasMany(Estadoprogramacionsubcliente::class, 'clienteid', 'clienteid');
    }
    public function infomedicosclientes()
    {
        return $this->hasMany(Documentacionsubcliente::class, 'clienteid', 'clienteid');
    }
    public function progclientes()
    {
        return $this->hasMany(Programacionsubcliente::class, 'clienteid', 'clienteid');
    }
    public function infofinalesclientes()
    {
        return $this->hasMany(Informefinal::class, 'clienteid', 'clienteid');
    }
    public function pagoservicioclientes()
    {
        return $this->hasMany(Detallerecibo::class, 'bateriaid', 'id');
    }
    public function pagoservicioinfofinalclientes()
    {
        return $this->hasMany(Detallerecibo::class, 'provinfofinalid', 'provinfofinalid');
    }
    public function provinfofinalclientes()
    {
        return $this->hasMany(ProveedorInformefinal::class, 'clienteid', 'clienteid');
    }
    public function tramiteclientes()
    {
        return $this->hasMany(Tramitesubcliente::class, 'clienteid', 'clienteid');
    }
}
