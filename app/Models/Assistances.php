<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assistances extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'assistances';
    static $rules = [
        'id' => '',
        'partner_id' => '',
        'partner_name' => '',
        'partner_last_name' => '',
        'reason' => '',
        'date_reason' => '',
        'date_attendance' => '',
        'time_attendance' => '',
        'user_register_id' => '',
        'user_register_name' => '',
    ]; 

    protected $fillable = [
        'id',
        'partner_id',
        'partner_name',
        'partner_last_name',
        'reason',
        'date_reason',
        'date_attendance',
        'time_attendance',
        'user_register_id',
        'user_register_name',
    ];
}
