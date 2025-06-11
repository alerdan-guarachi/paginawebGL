<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacturasEgreso extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'facturasegreso';
    static $rules = [
        'id' => '',
        'especificacion' => '',
        'nitci' => '',
        'razonsocial' => '',
        'codigoautorizacion' => '',
        'nrofactura' => '',
        'nroduidim' => '',
        'fechafacturaduidim' => '',
        'total' => '',
        'ice' => '',
        'iehd' => '',
        'ipj' => '',
        'tasas' => '',
        'otronosujcredfiscaloiva' => '',
        'importeyexporteexterno' => '',
        'tasacero' => '',
        'subtotal' => '',
        'descuento' => '',
        'giftcard' => '',
        'cfdf' => '',
        'creditodebitofiscal' => '',
        'tipo' => '',
        'complemento' => '',
        'estado' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'ciudad' => '',
        'importesujetocfdf' => '',
        'codigocontrol' => '',
        'idcaja' => '', 
    ]; 

    protected $fillable = [
        'id',
        'especificacion',
        'nitci',
        'razonsocial',
        'codigoautorizacion',
        'nrofactura',
        'nroduidim',
        'fechafacturaduidim',
        'total',
        'ice',
        'iehd',
        'ipj',
        'tasas',
        'otronosujcredfiscaloiva',
        'importeyexporteexterno',
        'tasacero',
        'subtotal',
        'descuento',
        'giftcard',
        'cfdf',
        'creditodebitofiscal',
        'tipo',
        'complemento',
        'estado',
        'usuarioregistroid',
        'usuarioregistronombre',
        'ciudad',
        'importesujetocfdf',
        'codigocontrol',
        'idcaja',
    ];

}
