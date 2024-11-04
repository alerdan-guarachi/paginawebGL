<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProveedorRequest extends FormRequest
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
            'proveedor' => 'required|max:255',
            'usuarioid' => 'required|max:45',
            'usuarioregistro' => 'required|max:255',
            'direccion' => 'required|max:255',
            'nit' => 'max:45',
            'banco' => 'max:255',
            'cuenta' => 'max:45',
            'tipocuenta' => 'max:45',
            'telefono' => 'required|max:45',
            'celular' => 'required|max:45',
            'ciudad' => 'required|max:45',
            'estadoproveedor' => 'required|max:45',
            'mododepago' => 'required|max:45',
            'personacontacto' => '',
            'celularreferencia' => '',
            'telefonoreferencia' => '',
            'usuarioactualizacion' => '',
            'usuarioeliminacion' => '',
            'linkubicacion' => '',
            
        ]; 
        return $rules;
    }
}
