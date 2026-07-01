<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntradaSalidaInventario extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'entradasalidainventario';
    static $rules = [
        'id' => '',
        'tipo' => '',
        'codigoproducto' => '',
        'codprodtraspaso' => '',
        'nrofactura' => '',
        'nrorecibo' => '',
        'fechamovimiento' => '',
        'precio' => '',
        'cantidad' => '',
        'usuarioreceptor' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'fechacompra' => '',
        'fechavencimiento' => '',
        'garantia' => '',
    ]; 

    protected $fillable = [
        'id',
        'tipo',
        'codigoproducto',
        'codprodtraspaso',
        'nrofactura',
        'nrorecibo',
        'fechamovimiento',
        'precio',
        'cantidad',
        'usuarioreceptor',
        'usuarioregistroid',
        'usuarioregistronombre',
        'fechacompra',
        'fechavencimiento',
        'garantia',
    ];

}
