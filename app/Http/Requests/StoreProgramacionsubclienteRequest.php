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
        'clientebancoid' => 'max:45',
        'clientenombre' => 'max:45',
        'proveedornombre' => 'required|max:45',
        'accionnombre' => 'max:45',
        'areanombre' => 'max:45',
        'horaasignada' => 'max:45',
        'fechaasignada' => 'required|max:45',
        'usuarioid' => 'max:45',
        'usuarioregistro' => 'max:45',
        'clientecomunid' => 'max:45',
        'clientecomunnombre' => 'max:45',
        'clienteauditoriaid' => 'max:45',
        'clienteauditorianombre' => 'max:45',
        'clienteitaid' => 'max:45',
        'clienteitanombre' => 'max:45',
        'motivoreprogramacion' => 'max:45',
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
