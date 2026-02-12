<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInformefinalRequest extends FormRequest
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
        'id',
        'clienteitaid' => '',
        'clienteitanombre' => '',
        'clientecomunid' => '',
        'clientecomunnombre' => '',
        'clientebancoid' => '',
        'clientebanconombre' => '',
        'clienteauditoriaid' => '',
        'clienteauditorianombre' => '',
        'fechabateria' => '',
        'estado' => '',
        'document' => '',
        'observaciones' => '5',
        'proveedorasignado' => '',
        'usuarioid' => '',
        'usuarioregistro' => '',
        'observacion' => '',
        'documentfirmado' => '',
        'documentword' => '',
        'motivoanulacion' => '',
        'usuarioanulacion' => '',
        'servicio' => '',
        'clienteid' => '',
        'clientenombre' => '',
        'tipocliente' => '',
    ]; 
    return $rules;
}

}
