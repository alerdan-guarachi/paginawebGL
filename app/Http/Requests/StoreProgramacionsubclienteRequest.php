<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProgramacionsubclienteRequest extends FormRequest
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
        'clientenombre' => '',
        'proveedornombre' => '',
        'accionnombre' => '',
        'areanombre' => '',
        'horaasignada' => '',
        'fechaasignada' => 'required',
        'usuarioid' => '',
        'usuarioregistro' => '',
        'clientecomunid' => '',
        'clientecomunnombre' => '',
        'clienteauditoriaid' => '',
        'clienteauditorianombre' => '',
        'clienteitaid' => '',
        'clienteitanombre' => '',
        'motivoreprogramacion' => '',
        'precio' =>'',
        'preciocompra' =>'',
        'fechabateria' =>'required',
        'horadesde' =>'required',
        'horahasta' =>'required',
        'usuarioactualizacion' =>'',
        'usuarioeliminacion' =>'',
        
    ]; 
    return $rules;
}

}
