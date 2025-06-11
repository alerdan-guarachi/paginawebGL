<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedoresservicios extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'proveedoresservicios';
    public function getIdAttribute($value)
    {
        return $value;
    }
    static $rules = [
            'id' => '',
            'razonsocial' => '',
            'nombrecompleto' => '',
            'ci' => '',
            'nit' => '',
            'celular' => '',
            'celularcorporativo' => '',
            'telefono' => '',
            'correo' => '',
            'ciudad' => '',
            'ciudad2' => '',
            'direccion' => '',
            'direcccion2' => '',
            'contacto' => '',
            'celcontacto' => '',
            'categoria' => '',
            'estado' => '',
            'emision' => '',
            'banco' => '',
            'banco2' => '',
            'banco3' => '',
            'numcuenta' => '',
            'numcuenta2' => '',
            'numcuenta3' => '',
            'sigla' => '',
            'sigla2' => '',
            'tipobusqueda' => '',
            'tipobusqueda2' => '',
            'tipobusqueda3' => '',
            'tipotransaccion' => '',
            'tipocuenta' => '',
            'tipocuenta2' => '',
            'cuentaorigen' => '',
            'imagenqr' => '',
            'fechavencqr' => '',
            'esquemapago' => '',
            'representantelegal' => '',
            'nombrebancotercero' => '',
            'nrocuentatercero' => '',
            'tipocuentatercero' => '',
            'documentorespaldo' => '',
            'usuarioregistroid' => '',
            'usuarioregistronombre' => '',
            'parentesco' => '',
            'contacto2' => '',
            'celcontacto2' => '',
            'parentesco2' => '',
            'cargo' => '',
            'fechanacimiento' => '',
            'sexo' => '',
            'fechaingreso' => '',
            'fechasalida' => '',
            'nacionalidad' => '',
            'bancoorigen' => '',
            'tipoorden1' => '',
            'tipoorden2' => '',
            'tipoorden3' => '',
            'tipoplanilla' => '',
    ]; 

    protected $fillable = [
            'id',
            'razonsocial',
            'nombrecompleto',
            'direccion',
            'direcccion2',
            'ciudad',
            'ciudad2',
            'ci',
            'nit',
            'celular',
            'celularcorporativo',
            'telefono',
            'correo',
            'estado',
            'contacto',
            'celcontacto',
            'emision',
            'banco',
            'numcuenta',
            'fechavencqr',
            'imagenqr',
            'tipocuenta',
            'tipocuenta2',
            'cuentaorigen',
            'representantelegal',
            'nombrebancotercero',
            'nrocuentatercero',
            'tipocuentatercero',
            'documentorespaldo',
            'tipotransaccion',
            'categoria',
            'usuarioregistroid',
            'usuarioregistronombre',
            'esquemapago',
            'sigla',
            'sigla2',
            'tipobusqueda',
            'tipobusqueda2',
            'tipobusqueda3',
            'numcuenta2',
            'numcuenta3',
            'parentesco',
            'contacto2',
            'celcontacto2',
            'parentesco2',
            'cargo',
            'fechanacimiento',
            'sexo',
            'fechaingreso',
            'fechasalida',
            'nacionalidad',
            'bancoorigen',
            'tipoorden1',
            'tipoorden2',
            'tipoorden3',
            'tipoplanilla',
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function planesServicios()
    {
        return $this->hasMany(PlanesServiciosProv::class, 'proveedorid', 'id');
    }

    public function inventarios()
    {
        return $this->hasMany(PortafolioProveedores::class, 'proveedorid');
    }

}
