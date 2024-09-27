<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personal extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'personal';
    static $rules = [
        'id' => 'max:45',
            'nombrecompleto' => 'required|max:45',
            'email' => 'required',
            'cargo' => 'required|max:45',
            'celular' => 'required|max:45',
            'direccion' => 'required|max:45',
            'ci' => 'required',
            'picture' => '',
            'sucursal' => 'required',
            'ciexp' => '',
            'nit' => '',
            'banco' => '',
            'numcuenta' => '',
            'fechaingreso' => 'required|',
            'fechasalida' => '',
            'estado' => 'required|',
            'contacto' => 'required|',
            'celcontacto' => 'required|',
        'usuarioid' => '',
        'usuarioregistro' => ''
    ]; 

    protected $fillable = [
        'id',
        'nombrecompleto',
        'email',
        'cargo',
        'celular',
        'direccion',
        'ci',
        'image',
        'sucursal',
        'ciexp',
        'nit',
        'banco',
        'numcuenta',
        'fechaingreso',
        'fechasalida',
        'estado',
        'contacto',
        'celcontacto',
        'usuarioid',
        'usuarioregistro'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

}
