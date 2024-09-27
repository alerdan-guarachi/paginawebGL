<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAsociadoRequest extends FormRequest
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
            'asociado' => 'required|max:45',
            'horarioinicial' => '',
            'horariofinal' => '',
            'tiempoatencion' => '',
            'cantidadatencion' => '',
            'usuarioid' => 'max:45',
            'usuarioregistro' => 'max:45',
            'direccion' => 'max:45',
            'nit' => 'max:45',
            'banco' => 'max:45',
            'cuenta' => 'max:45',
            'tipocuenta' => 'max:45',
            'telefono' => 'max:45',
            'ciudad' => 'max:45',
            'estadoasociado' => 'max:45',
            'mododepago' => 'max:45',
            
        ]; 
        return $rules;
    }
}
