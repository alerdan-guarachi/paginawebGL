<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seguroempresa extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'seguroempresas';
    static $rules = [
        'id' => '',
        'nombreseguro' => '',
    ]; 

    protected $fillable = [
        'id',
        'nombreseguro',
    ];
}
