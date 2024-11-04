<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modelocartareclamo extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'modelocartasreclamos';
    static $rules = [
        'id' => '',
        'topocarta' => 'required|max:45',
        'document' => 'required|max:45',
        'estado' => 'required|max:45',

    ]; 

    protected $fillable = [
        'id',
        'topocarta',
        'document',
        'estado',

    ];
}
