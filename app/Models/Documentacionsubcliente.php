<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documentacionsubcliente extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'documentacionsubclientes';
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
        'accion' => '',
        'document' => '',
        'usuarioid' => '',
        'usuarioregistro' => '',
        'fechabateria' => '',
        'image' => '',
        'image2' => '',
        'documentfirmado' => '',
        'documentword' => '',
        'programacionid' => '',
        'motivoanulacion' => '',
        'usuarioanulacion' => '',
        'idsubproc' => '',
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
        'accion',
        'document',
        'usuarioid',
        'usuarioregistro',
        'fechabateria',
        'image',
        'image2',
        'documentfirmado',
        'documentword',
        'programacionid',
        'motivoanulacion',
        'usuarioanulacion',
        'idsubproc',
    ];
    public function estadoprogramacionsubcliente()
    {
        return $this->hasOne(Estadoprogramacionsubcliente::class, 'clienteitaid', 'clienteitaid');
    }
    public function estadoprogramacionsubclientecomun()
    {
        return $this->hasOne(Estadoprogramacionsubcliente::class, 'clientecomunid', 'clientecomunid');
    }
}
