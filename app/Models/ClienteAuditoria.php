<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteAuditoria extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'clienteauditorias';
    static $rules = [
        'id' => '',
        'nombrecompleto' => 'required|max:45',
        'fechanacimiento' => 'required|max:45',
        'ocupacionprofesion' => 'required',
        'estadocivil' => 'required|max:45',
        'ci' => 'required|numeric|max:45',
        'genero' => 'required|max:45',
        'lugarresidencia' => 'required',
        'edad' => 'required',
        'usuarioid' => '',
        'usuarioregistro' => '',
        'celular' => '',
        'gradoinstruccion' => '',
        'lugarnacimiento' => '',
        'direccion' => '',
        'actividadlaboral' => '',
        'sucursal' => '',
        'banco1' => '',
        'numerocuenta1' => '',
        'banco2' => '',
        'numerocuenta2' => '',
        'banco3' => '',
        'numerocuenta3' => '',
    ]; 

    protected $fillable = [
        'id',
        'nombrecompleto',
        'fechanacimiento',
        'ocupacionprofesion',
        'estadocivil',
        'ci',
        'genero',
        'lugarresidencia',
        'edad',
        'usuarioid',
        'usuarioregistro',
        'celular',
        'gradoinstruccion',
        'lugarnacimiento',
        'direccion',
        'actividadlaboral',
        'sucursal',
        'banco1',
        'numerocuenta1',
        'banco2',
        'numerocuenta2',
        'banco3',
        'numerocuenta3',
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function programacionSubClientes()
    {
        return $this->hasMany(ProgramacionSubCliente::class, 'clienteauditorianombre', 'nombrecompleto');
    }
}
