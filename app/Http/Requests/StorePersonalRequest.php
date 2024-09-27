<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePersonalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if($this->usuarioid == auth()->user()->id){
            return true;
        }else{
            return false;
        }
        
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    
    public function rules()
    {
        $rules = [
            'id' => 'max:45',
            'nombrecompleto' => 'required|max:45',
            'email' => 'required',
            'cargo' => 'required|max:45',
            'celular' => 'required|max:45',
            'direccion' => 'required|max:45',
            'ci' => 'required',
            'picture' => '',
            'sucursal' => 'required',
            'ciexp' => '',
            'nit' => '',
            'banco' => '',
            'numcuenta' => '',
            'fechaingreso' => 'required|',
            'fechasalida' => '',
            'estado' => 'required|',
            'contacto' => 'required|',
            'celcontacto' => 'required|',
            'usuarioregistro' => '',
            'usuarioid' => ['required',
            Rule::unique('personal')->ignore($this->route('personal'))],

    
    
        ]; 
        return $rules;
    }
}
