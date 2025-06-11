<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanillaTercerInter extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'planillatercerinter';
    static $rules = [
        'id' => '',
        'seccion' => '',
        'detalle' => '',
        'codigo' => '',
        'oficina' => '',
    ]; 

    protected $fillable = [
        'id',
        'seccion',
        'detalle',
        'codigo',
        'oficina',
    ];

}
