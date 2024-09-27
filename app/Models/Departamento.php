<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules = [
        'id' => '',
        'departamento' => 'required|max:45',

    ]; 

    protected $fillable = [
        'id',
        'departamento',

    ];

}
