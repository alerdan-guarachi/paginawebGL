<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aperturacaja extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'aperturacaja';
    static $rules = [
        'id' => '',
        'usuarioaperturaid' => '',
        'usuarioaperturanombre' => '',
        'documentoapertura' => '',
    ]; 

    protected $fillable = [
        'id',
        'usuarioaperturaid',
        'usuarioaperturanombre',
        'documentoapertura',
    ];
}
