<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tipoareaaccion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tipoareaacciones';
    static $rules = [
        'id' => '',
        'nombre' => 'required|max:45',

    ]; 

    protected $fillable = [
        'id',
        'nombre',

    ];

}
