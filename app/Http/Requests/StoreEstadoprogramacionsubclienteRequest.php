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
            'clientebancoid' => '',
            'clientebanconombre' => '',
            'clientecomunid' => '',
            'clientecomunnombre' => '',
            'clienteitaid' => '',
            'clienteitanombre' => '',
            'clienteauditoriaid' => '',
            'clienteauditorianombre' => '',
            'proveedorasignado' => 'max:45',
            'horarioasignado' => 'max:45',
            'proveedorid' => 'max:45',
            'fechaatencionprogramacion' => 'max:45',
            'usuarioid' => 'max:45',
            'usuarioregistro' => 'max:45',
            'accionnombre' => 'required|max:45',
            'fechabateria' => 'required|max:45',


        ]; 
        return $rules;
    }
}
