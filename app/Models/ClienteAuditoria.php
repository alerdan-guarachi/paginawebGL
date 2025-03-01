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
    public function getIdAttribute($value)
    {
        return $value;
    }
    static $rules = [
        'id' => '',
        'nombrecompleto' => 'required|max:255',
        'fechanacimiento' => 'required|max:45',
        'ocupacionprofesion' => 'required',
        'estadocivil' => 'required|max:90',
        'ci' => 'required|max:90',
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
        'nrocredito1' => '',
        'banco2' => '',
        'nrocredito2' => '',
        'banco3' => '',
        'nrocredito3' => '',
        'nrocredito4' => '',
        'nrocredito5' => '',
        'nrocredito6' => '',
        'nrocredito7' => '',
        'nrocredito8' => '',
        'nrocredito9' => '',
        'nrocredito10' => '',
        'nrocredito11' => '',
        'nrocredito12' => '',
        'nrocredito13' => '',
        'nrocredito14' => '',
        'nrocredito15' => '',
        'nrocredito16' => '',
        'nrocredito17' => '',
        'nrocredito18' => '',
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
        'nrocredito1',
        'banco2',
        'nrocredito2',
        'banco3',
        'nrocredito3',
        'nrocredito4',
        'nrocredito5',
        'nrocredito6',
        'nrocredito7',
        'nrocredito8',
        'nrocredito9',
        'nrocredito10',
        'nrocredito11',
        'nrocredito12',
        'nrocredito13',
        'nrocredito14',
        'nrocredito15',
        'nrocredito16',
        'nrocredito17',
        'nrocredito18',
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function programacionSubClientes()
    {
        return $this->hasMany(ProgramacionSubCliente::class, 'clienteauditorianombre', 'nombrecompleto');
    }
    public function dictamenauditoria()
{
    return $this->hasOne(DictamenAuditoria::class, 'clienteauditoriaid', 'id');
}

}
