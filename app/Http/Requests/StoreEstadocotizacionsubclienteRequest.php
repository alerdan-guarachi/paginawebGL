<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEstadocotizacionsubclienteRequest extends FormRequest
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
            'clientebancoid' => '',
            'clientebanconombre' => '',
            'clientecomunid' => '',
            'clientecomunnombre' => '',
            'clienteitaid' => '',
            'clienteitanombre' => '',
            'clienteauditoriaid' => '',
            'clienteauditorianombre' => '',
            'fechabateria' => 'required',
            'archivo' => 'required',
            'archivo2' => '',
            'usuarioid' => '',
            'usuarioregistro' => '',
            'nrofactura' => '',
            'detalle' => '',
            'tramite' => '',
            'clienteid' => '',
            'clientenombre' => '',
            'tipocliente' => '',
        ]; 
        return $rules;
    }
}
