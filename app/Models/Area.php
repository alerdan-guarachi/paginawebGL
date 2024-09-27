<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use HasFactory;
    use SoftDeletes;
    static $rules = [
        'id' => '',
        'idtipoarea' => 'required|max:45',
        'tipoarea' => 'required|max:45',
        'nombrearea' => 'required|max:45',

    ]; 

    protected $fillable = [
        'id',
        'idtipoarea',
        'tipoarea',
        'nombrearea',

    ];

    public function areaaccion()
    {
        return $this->hasMany(AreaAccion::class, 'areasid', 'id');
    }
    

}
