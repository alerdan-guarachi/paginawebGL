<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules = [
        'id',
        'apepaterno' => 'max:45',
        'apematerno' => 'max:45',
        'nombrecompleto' => 'required',
        'nombres' => 'required',
        'ci' => 'required|max:45',
        'cicomplemento' => 'required|max:45',
        'ciexp' => 'required|max:45',
        'tipoidentificacion' => 'required',
        'image' => '',
        'users_id' => 'required',
        'fechanacimiento' => 'required',
        'edad' => 'required',
        'estadocivil' => 'required',
        'genero' => 'required',
        'ocupacion' => 'required',
        'lugarnacimiento' => 'required',
        'gradoinstruccion' => '',
        'celular' => 'required',
        'telefono' => 'required',
        'domicilio' => 'required',
        'email' => 'required',
        'nuacua' => 'required',
        'estadolaboral' => 'required',
        'empresa' => 'required',
        'paisresidencia' => 'required',
        'departamentoresidencia' => 'required',
        'ciudadresidencia' => 'required',
        'aseguradora' => 'required',
        'referenciador' => 'required',
        'afp' => 'required',
        'numhijosmenores' => 'required',
        'alertas' => 'required',
        'usuarioregistro' => 'required',
        'usuarioultimaactualizacion' => 'required',
        'sucursal' => 'required',
        'fechavencci' => 'required',
        'tipocliente' => '',
    ]; 

    protected $fillable = [
        'id',
        'apepaterno',
        'apematerno',
        'nombrecompleto',
        'ci',
        'cicomplemento',
        'ciexp',
        'tipoidentificacion',
        'image',
        'users_id',
        'fechanacimiento',
        'edad',
        'estadocivil',
        'genero',
        'ocupacion',
        'lugarnacimiento',
        'gradoinstruccion',
        'celular',
        'telefono',
        'domicilio',
        'email',
        'nuacua',
        'estadolaboral',
        'empresa',
        'paisresidencia',
        'departamentoresidencia',
        'ciudadresidencia',
        'aseguradora',
        'referenciador',
        'afp',
        'numhijosmenores',
        'alertas',
        'usuarioregistro',
        'usuarioultimaactualizacion',
        'sucursal',
        'fechavencci',
        'tipocliente',
        'nombres'

    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    /* public function empresa(){
        return $this->belongsTo('App\Models\Empresa');
    } */
    public function programacionSubClientes()
    {
        return $this->hasMany(ProgramacionSubCliente::class, 'clienteitanombre', 'nombrecompleto');
    }
    public function empresa()
    {
        return $this->hasMany(Empresa::class, 'nombreempresa','empresa');
    }
    public function ciudadresidencia()
    {
        return $this->hasMany(Ciudad::class, 'ciudad','ciudadresidencia');
    }
    public function departamentoresidencia()
    {
        return $this->hasMany(Departamento::class, 'departamento','departamentoresidencia');
    }
    public function cliente()
{
    return $this->belongsTo(Cliente::class, 'nombrecompleto', 'id'); // Ajusta los nombres de las claves según tu esquema
}
/* public function requisitos()
    {
        return $this->hasMany(RequisitoSubCliente::class);
    } */
    public function requisitos()
    {
        return $this->hasOne(RequisitoSubCliente::class, 'clienteitaid', 'id');
    }
    public function tramites()
    {
        return $this->hasMany(Tramite::class, 'clienteitaid', 'id');
    }
    public function servicios()
    {
        return $this->hasMany(Tramitesubcliente::class, 'clienteitaid');
    }
}
