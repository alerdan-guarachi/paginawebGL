<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProveedoresDocumentos extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'proveedoresdocumentos';
    static $rules = [
        'id' => '',
        'proveedorid' => '',
        'proveedornombre' => '',
        'tipodocumento' => '',
        'nombredocumento' => '',
        'documento' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'proveedorid',
        'proveedornombre',
        'tipodocumento',
        'nombredocumento',
        'documento',
        'usuarioregistroid',
        'usuarioregistronombre',
    ];
}
