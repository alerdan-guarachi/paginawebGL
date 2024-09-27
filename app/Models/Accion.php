<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accion extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'acciones';
    static $rules = [
        'id' => '',
        'area' => 'required|max:90',
        'accion' => 'required|max:250',
    
    ]; 

    protected $fillable = [
        'id',
        'area',
        'accion',

    ];
}
