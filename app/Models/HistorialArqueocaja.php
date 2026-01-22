<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistorialArqueocaja extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'historialarqueocaja';
    static $rules = [
        'id' => '',
        'usuarioarqueoid' => '',
        'usuarioarqueonombre' => '',
        'fechaarqueo' => '',
        'billetecorte200' => '',
        'billetecorte100' => '',
        'billetecorte50' => '',
        'billetecorte20' => '',
        'billetecorte10' => '',
        'monedacorte5' => '',
        'monedacorte2' => '',
        'monedacorte1' => '',
        'monedacorte050' => '',
        'monedacorte020' => '',
        'monedacorte010' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'usuarioarqueoid',
        'usuarioarqueonombre',
        'fechaarqueo',
        'billetecorte200',
        'billetecorte100',
        'billetecorte50',
        'billetecorte20',
        'billetecorte10',
        'monedacorte5',
        'monedacorte2',
        'monedacorte1',
        'monedacorte050',
        'monedacorte020',
        'monedacorte010',
        'usuarioregistroid',
        'usuarioregistronombre',

    ];
}
