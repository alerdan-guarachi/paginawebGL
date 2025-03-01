<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DictamenAuditoria extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'dictamenauditoria';
    static $rules = [
        'id' => '',
        'clienteauditoriaid' => '',
        'clienteauditorianombre' => '',
        'nrodictamen' => '',
        'fechadictamen' => '',
        'porcentajeinvalidez' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
        'documento' => '',

    ]; 

    protected $fillable = [
        'id',
        'clienteauditoriaid',
        'clienteauditorianombre',
        'nrodictamen',
        'fechadictamen',
        'porcentajeinvalidez',
        'usuarioregistroid',
        'usuarioregistronombre',
        'documento',
    ];
}
