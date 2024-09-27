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
        'clienteitaid' => 'max:45',
        'clienteitanombre' => 'max:45',
        'clientecomunid' => 'max:45',
        'clientecomunnombre' => 'max:45',
        'clientebancoid' => 'max:45',
        'clientebanconombre' => 'max:45',
        'clienteauditoriaid' => 'max:45',
        'clienteauditorianombre' => 'max:45',
        'fechabateria' => '',
        'estado' => 'max:45',
        'document' => 'required|file|max:2048', // Ajusta el tamaño según tus necesidades
        'observaciones' => 'max:255',
        'proveedorasignado' => 'max:45',
        'usuarioid' => 'required',
        'usuarioregistro' => 'max:45',
        'observacion' => 'max:255',
        
    ]; 
    return $rules;
}

}
