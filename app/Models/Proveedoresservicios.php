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
    static $rules = [
            'id' => '',
            'razonsocial' => '',
            'direccion' => '',
            'direcccion2' => '',
            'ciudad' => '',
            'ciudad2' => '',
            'ci' => '',
            'nit' => '',
            'celular' => '',
            'correo' => '',
            'estado' => '',
            'contacto' => '',
            'celcontacto' => '',
            'emision' => '',
            'banco' => '',
            'numcuenta' => '',
            'fechavencqr' => '',
            'imagenqr' => '',
            'tipocuenta' => '',
            'cuentaorigen' => '',
            'sucursal' => '',
            'representantelegal' => '',
            'nombrebancotercero' => '',
            'nrocuentatercero' => '',
            'tipocuentatercero' => '',
            'documentorespaldo' => '',
            'tipotransaccion' => '',
            'categoria' => '',
            'usuarioregistroid' => '',
            'usuarioregistronombre' => '',
    ]; 

    protected $fillable = [
            'id',
            'razonsocial',
            'direccion',
            'direcccion2',
            'ciudad',
            'ciudad2',
            'ci',
            'nit',
            'celular',
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
            'cuentaorigen',
            'sucursal',
            'representantelegal',
            'nombrebancotercero',
            'nrocuentatercero',
            'tipocuentatercero',
            'documentorespaldo',
            'tipotransaccion',
            'categoria',
            'usuarioregistroid',
            'usuarioregistronombre',
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

}
