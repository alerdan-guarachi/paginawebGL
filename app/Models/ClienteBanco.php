<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteBanco extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'clientebancos';
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
        'asociadoid' => '',
        'asociadonombre' => '',
        'sucursal' => '',
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
        'asociadoid',
        'asociadonombre',
        'sucursal'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function programacionSubClientes()
    {
        return $this->hasMany(ProgramacionSubCliente::class, 'clientenombre', 'nombrecompleto');
    }
    /* public function bateria()
    {
        return $this->hasMany(AreaAccion::class, 'asociadoid', 'asociadoid');
    } */
   // Relación con Asociado
   public function asociado()
   {
       return $this->belongsTo(Asociado::class, 'asociadoid');
    }

}
