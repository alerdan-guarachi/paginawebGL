<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Areaaccion extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'areaacciones';
    static $rules = [
            'id' => '',
            'tipoid' => '',
            'tiponombre' => 'required|max:45',
            'areasid' => 'max:45',
            'area' => 'required|max:45',
            'accion' => 'required|max:250',
            'sucursal' => 'required|max:45',
            'precio' => 'required|max:45',
            'estado' => 'required|max:45',
            'preciocompra' => 'max:45',
            'asociado' => 'required|max:45',
            'asociadoid' => 'required|max:45',
            'categoria' => 'max:45',
            'proveedorid' => 'max:45',
            'proveedor' => 'max:255',
    ]; 
    
    protected $fillable = [
            'id',
            'tipoid',
            'tiponombre',
            'areasid',
            'area',
            'accion',
            'sucursal',
            'precio',
            'estado',
            'preciocompra',
            'asociado',
            'asociadoid',
            'categoria',
            'proveedorid',
            'proveedor',
    ];
    public function clienteBanco()
    {
        return $this->belongsTo(ClienteBanco::class, 'asociadoid', 'asociadoid');
    }
    
}
