<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTramiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    /* public function authorize()
    {
        if($this->users_id == auth()->user()->id){
            return true;
        }else{
            return false;
        }
        
    } */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'id' => '',
            'clienteid' => '',
            'clientenombre' => '',
            'nivelprocedimiento' => '',
            'subprocedimiento' => '',
            'archivo' => '',
            'archivo2' => '',
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
            'nrodictamen' => '',
            'nroformulario' => '',
            'accesopension' => '',
            'motivonopension' => '',
            'mescobro' => '',
            'tipocarta' => '',

            'comusuemisor' => '',
            'comusureceptor' => '',
            'commodo' => '',
            'comtipointerac' => '',
            'comdetalle' => '',
            'comtipoentrega' => '',
            'comtipodoc' => '',
            'nombremedico4' => '',
            'nombremedico5' => '',
            'capturacomunicacion' => '',
        ]; 
        return $rules;
    }
}
