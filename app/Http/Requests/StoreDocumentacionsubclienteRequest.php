<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentacionsubclienteRequest extends FormRequest
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
            'proveedorid' => '',
            'proveedornombre' => '',
            'area' => '',
            'accion' => '',
            'archivo' => '',
            'usuarioid' => '',
            'usuarioregistro' => '',
            'fechabateria' => '',
            'picture' => '',
            'picture2' => '',
            'archivo2' => '',
            'archivo3' => '',
            'programacionid' => '',
            'motivoanulacion' => '',
            'usuarioanulacion' => '',
        ]; 
        return $rules;
    }
}
