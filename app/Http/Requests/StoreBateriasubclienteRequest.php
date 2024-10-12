<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBateriasubclienteRequest extends FormRequest
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
            'areaid' => '',
            'accionid' => '',
            'clientenombre' => '',
            'areanombre' => '',
            'accionnombre' => '',
            'clientecomunid' => '',
            'clientecomunnombre' => '',
            'clienteauditoriaid' => '',
            'clienteauditorianombre' => '',
            'clienteitaid' => '',
            'clienteitanombre' => '',
            'tipoarea' => '',
            'precio'=>'',
            'fechabateria' => '',
            'antecedentes' => '',
            'informe' => '',
            'fechainforme' => '',
            'preciocompra' => '',
            'proveedorasignado' => '',
            'servicio' => '',
            'usuarioid' => '',
            'usuarioregistro' => '',
        ]; 
        return $rules;
    }
}
