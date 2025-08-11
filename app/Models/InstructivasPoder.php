<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstructivasPoder extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'instructivaspoder';
    static $rules = [
        'id' => '',
        'tramite' => '',
        'clienteid' => '',
        'clientenombre' => '',
        'documento' => '',
        'apoderado1' => '',
        'apoderado2' => '',
        'apoderado3' => '',
        'apoderado4' => '',
        'apoderado5' => '',
        'apoderado6' => '',
        'apoderado7' => '',
        'apoderado8' => '',
        'apoderado9' => '',
        'apoderado10' => '',
        'usuarioregistroid' => '',
        'usuarioregistronombre' => '',
    ]; 

    protected $fillable = [
        'id',
        'tramite',
        'clienteid',
        'clientenombre',
        'documento',
        'apoderado1',
        'apoderado2',
        'apoderado3',
        'apoderado4',
        'apoderado5',
        'apoderado6',
        'apoderado7',
        'apoderado8',
        'apoderado9',
        'apoderado10',
        'usuarioregistroid',
        'usuarioregistronombre',
    ];
}
