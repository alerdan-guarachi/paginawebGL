<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partners extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'partners';
    static $rules = [
        'id' => '',
        'name' => '',
        'last_name' => '',
        'ci' => '',
        'category' => '',
        'code_qr' => '',
        'user_register_id' => '',
        'user_register_name' => '',
    ]; 

    protected $fillable = [
        'id',
        'name',
        'last_name',
        'ci',
        'category',
        'code_qr',
        'user_register_id',
        'user_register_name',
    ];
}
