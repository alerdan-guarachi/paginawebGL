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
        'apepaterno' => '',
        'apematerno' => '',
        'nombrecompleto' => '',
        'nombres' => '',
        'ci' => '',
        'cicomplemento' => '',
        'ciexp' => '',
        'tipoidentificacion' => '',
        'image' => '',
        'users_id' => '',
        'fechanacimiento' => '',
        'edad' => '',
        'estadocivil' => '',
        'genero' => '',
        'ocupacion' => '',
        'lugarnacimiento' => '',
        'gradoinstruccion' => '',
        'celular' => '',
        'telefono' => '',
        'domicilio' => '',
        'email' => '',
        'nuacua' => '',
        'estadolaboral' => '',
        'empresa' => '',
        'paisresidencia' => '',
        'departamentoresidencia' => '',
        'ciudadresidencia' => '',
        'aseguradora' => '',
        'referenciador' => '',
        'afp' => '',
        'numhijosmenores' => '',
        'alertas' => '',
        'usuarioregistro' => '',
        'usuarioultimaactualizacion' => '',
        'sucursal' => '',
        'fechavencci' => '',
        'tipocliente' => '',
        'paisnacimiento' => '',
        'matricula' => '',
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
        'nombres',
        'paisnacimiento',
        'matricula',
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
        return $this->hasMany(Tramite::class, 'clienteid', 'id');
    }
    public function servicios()
    {
        return $this->hasMany(Tramitesubcliente::class, 'clienteitaid');
    }
}
