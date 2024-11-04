<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requisitosclientesauditoria extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules = [
        'id' => '',
        'clienteitaid' => '',
        'clienteitanombre' => '',
        'ciasegurado' => '',
        'cnacasegurado' => '',
        'banco' => '',
        'nropolizageneral' => '',
        'polizageneral' => '',
        'declasalud' => '',
        'nropolizadesgravamen' => '',
        'polizasegurodesgravamen' => '',
        'usuarioid' => '',
        'usuarionombre' => '',
        'clienteauditoriaid' => '',
        'clienteauditorianombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'clienteitaid',
        'clienteitanombre',
        'ciasegurado',
        'cnacasegurado',
        'banco',
        'nropolizageneral',
        'polizageneral',
        'declasalud',
        'nropolizadesgravamen',
        'polizasegurodesgravamen',
        'usuarioid',
        'usuarionombre',
        'clienteauditoriaid',
        'clienteauditorianombre',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'clienteitaid', 'id');
    }
}
