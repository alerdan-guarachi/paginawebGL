<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteComun extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'clientescomunes';
    static $rules = [
        'id' => '',
        'nombrecompleto' => 'required|max:45',
        'fechanacimiento' => 'required|max:45',
        'ocupacionprofesion' => 'required',
        'estadocivil' => 'required|max:45',
        'ci' => 'required|numeric|max:45',
        'genero' => 'required|max:45',
        'ciudad' => 'required',
        'edad' => 'required',
        'usuarioid' => '',
        'usuarioregistro' => '',
        'celular' => '',
        'sucursal' =>'',
    ]; 

    protected $fillable = [
        'id',
        'nombrecompleto',
        'fechanacimiento',
        'ocupacionprofesion',
        'estadocivil',
        'ci',
        'genero',
        'ciudad',
        'edad',
        'usuarioid',
        'usuarioregistro',
        'celular',
        'sucursal'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function programacionSubClientes()
    {
        return $this->hasMany(ProgramacionSubCliente::class, 'clientecomunnombre', 'nombrecompleto');
    }
    public function departamento()
    {
        return $this->hasMany(Departamento::class, 'departamento','ciudad');
    }
}
