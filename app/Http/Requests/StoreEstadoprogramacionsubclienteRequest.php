<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEstadoprogramacionsubclienteRequest extends FormRequest
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
            'tipocliente' => '',
            'clientecomunid' => '',
            'clientecomunnombre' => '',
            'clienteitaid' => '',
            'clienteitanombre' => '',
            'clienteauditoriaid' => '',
            'clienteauditorianombre' => '',
            'proveedorasignado' => '',
            'horarioasignado' => '',
            'proveedorid' => '',
            'fechaatencionprogramacion' => '',
            'usuarioid' => '',
            'usuarioregistro' => '',
            'accionnombre' => '',
            'fechabateria' => '',
            'programacionid' => '',
            'motivoanulacion' => '',
            'usuarioanulacion' => '',
            'nrosesion' => '',
            'idsubproc' => '',
            'clientebanconombre' => '',
        ]; 
        return $rules;
    }
}
