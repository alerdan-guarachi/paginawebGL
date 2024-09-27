<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ciudad extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'ciudades';
    static $rules = [
        'id' => '',
        'ciudad' => 'required|max:45',

    ]; 

    protected $fillable = [
        'id',
        'ciudad',

    ];

}
