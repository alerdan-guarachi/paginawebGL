<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tramite extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'procedimientotramites';
    static $rules = [
        'id' => '',
        'idtramite' => '',
        'clienteid' => '',
        'clientenombre' => '',
        'nivelprocedimiento' => '',
        'subprocedimiento' => '',
        'document' => '',
        'document2' => '',
        'observaciones' => '',
        'apoderado' => '',
        'fechasubida' => '',
        'tramite' => '',
        'seguro' => '',
        'usuarioid' => '',
        'usuarioregistro' => '',
        'estadodictamen' => '',
        'porcentajeriesgodictamen' => '',
        'viaja' => '',
        'departamentoviaja' => '',

        'fechagestoradictamen' => '',
        'fechasinestro' => '',
        'fechacobrocontrato' => '',
        'montocontrato' => '',
        'motivorechazo' => '',
        'notaseguimiento' => '',
        'estadocomunicado' => '',
        'riesgodictamen' => '',
        'tiporiesgodictamen' => '',
        'mescierre' => '',
        'usuarioingreso' => '',
        'tipodocumento' => '',
        'estadotramite' => '',
        'fechacitenotificacion' => '',
        'fechacitenota' => '',
        'tipomedico' => '',
        'nombremedico' => '',
        'citenotificacion' => '',
        'citenota' => '',
        'motivonoseguro' => '',
        'nombremedico2' => '',
        'fecharetorno' => '',
        'recojodocumentacion' => '',
        'fechainclusion' => '',
        'fechaestadotramite' => '',
        'tipodocumentacion' => '',
        'corsolicitud' => '',
        'opcioncorsolicitud' => '',
        'tipo' => '',
        'nro' => '',
        'decisionviaja' => '',
        'transporteviaja' => '',
        'viaticos' => '',
        'nrodictamen' => '',
    ]; 

    protected $fillable = [
        'id',
        'idtramite',
        'clienteid',
        'clientenombre',
        'nivelprocedimiento',
        'subprocedimiento',
        'document',
        'document2',
        'observaciones',
        'apoderado',
        'fechasubida',
        'tramite',
        'seguro',
        'usuarioid',
        'usuarioregistro',
        'estadodictamen',
        'porcentajeriesgodictamen',
        'viaja',
        'departamentoviaja',

        'fechagestoradictamen',
        'fechasinestro',
        'fechacobrocontrato',
        'montocontrato',
        'motivorechazo',
        'notaseguimiento',
        'estadocomunicado',
        'riesgodictamen',
        'tiporiesgodictamen',
        'mescierre',
        'usuarioingreso',
        'tipodocumento',
        'estadotramite',
        'fechacitenotificacion',
        'fechacitenota',
        'tipomedico',
        'nombremedico',
        'citenotificacion',
        'citenota',
        'motivonoseguro',
        'nombremedico2',
        'fecharetorno',
        'recojodocumentacion',
        'fechainclusion',
        'fechaestadotramite',
        'tipodocumentacion',
        'corsolicitud',
        'opcioncorsolicitud',
        'tipo',
        'nro',
        'decisionviaja',
        'transporteviaja',
        'viaticos',
        'nrodictamen',
    ];

    public function areaaccion()
    {
        return $this->hasMany(AreaAccion::class, 'areasid', 'id');
    }
    

}
