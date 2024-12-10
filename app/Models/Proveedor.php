<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'proveedores';
    static $rules = [
        'id' => '',
        'proveedor' => 'required|max:45',
        'usuarioid' => 'required|max:45',
        'usuarioregistro' => 'required|max:45',
        'direccion' => 'required|max:255',
        'nit' => 'required|max:45',
        'banco' => 'required|max:255',
        'cuenta' => 'max:45',
        'tipocuenta' => 'max:45',
        'telefono' => 'required|max:45',
        'celular' => 'required|max:45',
        'ciudad' => 'required|max:45',
        'estadoproveedor' => 'required|max:45',
        'mododepago' => 'required|max:45',
        'personacontacto' => '',
        'celularreferencia' => '',
        'telefonoreferencia' => '',
        'usuarioactualizacion' => '',
        'usuarioeliminacion' => '',
        'linkubicacion' => '',
        'firmadigital' => '',
        'sellodigital' => '',
        'direccion2' => '',
        'linkubicacion2' => '',
        'direccion3' => '',
        'linkubicacion3' => '',
    ]; 

    protected $fillable = [
        'id',
        'proveedor',
        'usuarioid',
        'usuarioregistro',
        'direccion',
        'nit' ,
        'banco',
        'cuenta',
        'tipocuenta',
        'telefono',
        'celular',
        'ciudad',
        'estadoproveedor',
        'mododepago',
        'personacontacto',
        'celularreferencia',
        'telefonoreferencia',
        'usuarioactualizacion',
        'usuarioeliminacion',
        'linkubicacion',
        'firmadigital',
        'sellodigital',
        'direccion2',
        'linkubicacion2',
        'direccion3',
        'linkubicacion3',
    ];
    public function departamento()
    {
        return $this->hasMany(Departamento::class, 'departamento','ciudad');
    }
    public function nombrebanco()
    {
        return $this->hasMany(Banco::class, 'nombrebanco','banco');
    }
    /* public function proveedor()
{
    return $this->belongsTo(Proveedor::class, 'proveedor', 'id'); // Ajusta los nombres de las claves según tu esquema
} */
}
