<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bateriaclientecomun extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'bateriaclientescomunes';
    static $rules = [
        'id' => '',
        'clientecomunid' => 'max:45',
        'areaid' => 'max:45',
        'accionid' => 'max:45',
        'clientecomunnombre' => 'required|max:45',
        'areanombre' => 'required|max:45',
        'accionnombre' => 'required|max:45',
    ]; 

    protected $fillable = [
        'id',
        'clientecomunid',
        'areaid',
        'accionid',
        'clientecomunnombre',
        'areanombre',
        'accionnombre',

    ];

}
