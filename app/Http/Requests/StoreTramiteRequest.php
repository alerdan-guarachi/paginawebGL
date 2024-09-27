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
            'clienteitaid' => 'required|max:45',
            'clienteitanombre' => 'required|max:45',
            'nivelprocedimiento' => 'max:255',
            'subprocedimiento' => 'max:255',
            'archivo' => 'max:45',
            'apoderado' => 'max:45',
            'fechasubida' => 'max:45',
            'tramite' => 'max:45',
            'seguro' => 'max:45',
            'usuarioid' => 'max:45',
            'usuarioregistro' => 'max:45',
            'estadodictamen' => 'max:45',
            'porcentajeaceptorechazodictamen' => 'max:45',
            'viaja' => 'max:45',
            'departamentoviaja' => 'max:45',

            'dep1viaja' => 'max:255',
            'fechadep1viaja' => 'max:45',
            'dep2viaja' => 'max:255',
            'fechadep2viaja' => 'max:45',
            'dep3viaja' => 'max:255',
            'fechadep3viaja' => 'max:45',
            'dep4viaja' => 'max:255',
            'fechadep4viaja' => 'max:45',
            'dep5viaja' => 'max:255',
            'fechadep5viaja' => 'max:45',
            'dep6viaja' => 'max:255',
            'fechadep6viaja' => 'max:45',
            'dep7viaja' => 'max:255',
            'fechadep7viaja' => 'max:45',
            'dep8viaja' => 'max:255',
            'fechadep8viaja' => 'max:45',

            'fechagestoradictamen' => 'max:45',
            'fechasinestro' => 'max:45',
            'fechacobrocontrato' => 'max:45',
            'montocontrato' => 'max:45',
            'motivorechazo' => 'max:255',
            'notaseguimiento' => 'max:255',
            'estadocomunicado' => 'max:255',
            'riesgodictamen' => 'max:255',
            'tiporiesgodictamen' => 'max:255',
        ]; 
        return $rules;
    }
}
