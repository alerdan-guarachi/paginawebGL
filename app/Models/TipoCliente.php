<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoCliente extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules =[
        'nombre' => 'required|max:45',
    ];

    protected $perPage = 20;

    protected $fillable = ['nombre'];

    /* public function eventos(){

        return $this->hasMany('App\Models\Evento');
    } */
}
