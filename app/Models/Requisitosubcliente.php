<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requisitosubcliente extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules = [
        'id' => '',
        'clientebancoid' => '',
        'clientebanconombre' => '',
        'clientecomunid' => '',
        'clientecomunnombre' => '',
        'clienteitaid' => '',
        'clienteitanombre' => '',
        'clienteauditoriaid' => '',
        'clienteauditorianombre' => '',
        'servicio' => '',
        'poder' => '',
        'numeropoder' => '',
        'avcci' => '',
        'cnacasegurado' => '',
        'ciasegurado' => '',
        'cmatrimonio' => '',
        'cnacconyuge' => '',
        'ciconyuge' => '',
        'cnacjihos' => '',
        'cihijos' => '',
        'denfaccidente' => '',
        'crodomicilio' => '',
        'contrato' => '',
        'egestora' => '',
        'dictamencalentenc' => '',
        'infomedicasalud' => '',
        'usuarioid' => '',
        'usuarioregistro' => '',

        'ctrabajo' => '',
        'boletapago' => '',
        'actdatos' => '',
        'resolinvhijos' => '',
        'cunionlibre' => '',
        'cnacimientounionlibre' => '',
        'ciunionlibre' => '',
        'cdivorcio' => '',
        'cdefuncion' => '',
        'polizasgen' => '',
        'declasalud' => '',
        'polizaseguro' => '',
        'anteriordictamen' => '',
        'poderciapoderado' => '',
    ]; 

    protected $fillable = [
        'id',
        'clientebancoid',
        'clientebanconombre',
        'clientecomunid',
        'clientecomunnombre',
        'clienteitaid',
        'clienteitanombre',
        'clienteauditoriaid',
        'clienteauditorianombre',
        'servicio',
        'poder',
        'numeropoder',
        'avcci',
        'cnacasegurado',
        'ciasegurado',
        'cmatrimonio',
        'cnacconyuge',
        'ciconyuge',
        'cnacjihos',
        'cihijos',
        'denfaccidente',
        'crodomicilio',
        'contrato',
        'usuarioid',
        'usuarioregistro',
        'ctrabajo',
        'boletapago',
        'egestora',
        'actdatos',
        'resolinvhijos',
        'cunionlibre',
        'cnacimientounionlibre',
        'ciunionlibre',
        'cdivorcio',
        'cdefuncion',
        'polizasgen',
        'declasalud',
        'polizaseguro',
        'dictamencalentenc',
        'infomedicasalud',
        'anteriordictamen',
        'poderciapoderado',
    ];
    /* public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    } */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'clienteitaid', 'id');
    }
}
