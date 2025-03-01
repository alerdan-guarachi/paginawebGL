<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProveedoresserviciosRequest extends FormRequest
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
            'id' => '',
            'razonsocial' => '',
            'direccion' => '',
            'direcccion2' => '',
            'ciudad' => '',
            'ciudad2' => '',
            'ci' => '',
            'nit' => '',
            'celular' => '',
            'correo' => '',
            'estado' => '',
            'contacto' => '',
            'celcontacto' => '',
            'emision' => '',
            'banco' => '',
            'numcuenta' => '',
            'fechavencqr' => '',
            'imagenqr' => '',
            'tipocuenta' => '',
            'cuentaorigen' => '',
            'sucursal' => '',
            'representantelegal' => '',
            'nombrebancotercero' => '',
            'nrocuentatercero' => '',
            'tipocuentatercero' => '',
            'documentorespaldo' => '',
            'tipotransaccion' => '',
            'categoria' => '',
            'usuarioregistroid' => '',
            'usuarioregistronombre' => '',
        ]; 
        return $rules;
    }
}
