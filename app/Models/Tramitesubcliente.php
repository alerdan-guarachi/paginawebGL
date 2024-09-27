<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tramitesubcliente extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tramitessubclientes';
    static $rules = [
        'id' => '',
        'tramite' => 'max:255',
        'clienteitaid' => 'required|max:45',
        'clienteitanombre' => 'required|max:45',
        'apoderadoasignado' => 'max:255',
        'usuarioinicial' => 'max:255',
        'usuariofinal' => 'max:45',
        'estado' => 'max:45',
        'observaciones' => 'max:45',
        'ciudad' => 'max:45',
        'usuarioid' => 'max:45',
        'usuarioregistro' => 'max:90',
        'fechabateria' => '',
    ]; 

    protected $fillable = [
        'id',
        'tramite',
        'clienteitaid',
        'clienteitanombre',
        'apoderadoasignado',
        'usuarioinicial',
        'usuariofinal',
        'estado',
        'observaciones',
        'ciudad',
        'usuarioid',
        'usuarioregistro',
        'fechabateria',
    ];

    public function areaaccion()
    {
        return $this->hasMany(AreaAccion::class, 'areasid', 'id');
    }
    

}
