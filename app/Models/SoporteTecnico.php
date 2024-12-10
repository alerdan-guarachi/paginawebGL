<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoporteTecnico extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'soportetecnico';

    protected $fillable = [
        'usuariosolicitante',
        'motivosolicitud',
        'nivelprioridad',
        'motivoimagen1',
        'motivoimagen2',
        'usuariosoporte',
        'descripcionatendida',
        'soporteimagen1',
        'soporteimagen2',
        'estado',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
