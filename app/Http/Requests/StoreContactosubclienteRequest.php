<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactosubclienteRequest extends FormRequest
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
            'clienteitaid' => 'max:45',
            'clienteitanombre' => 'max:90',
            'clientecomunid' => 'max:45',
            'clientecomunnombre' => 'max:90',
            'clientebancoid' => 'max:45',
            'clientebanconombre' => 'max:90',
            'clienteauditoriaid' => 'max:45',
            'clienteauditorianombre' => 'max:90',
            'nombrecontacto' => 'required|max:90',
            'celularcontacto' => 'required|numeric',
            'telefonocontacto' => 'numeric',
            'parentesco' => 'required|max:45',
            'usuarioid' => 'max:45',
            'usuarioregistro' => 'max:255',
            
        ]; 
        return $rules;
    }
}
