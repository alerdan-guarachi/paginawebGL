<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProveedoresserviciosRequest extends FormRequest
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
       /*  $profile = $this->route()->parameter('profile'); */
        $rules = [
            'id' => '',
            'razonsocial' => '',
            'ci' => '',
            'nit' => '',
            'celular' => '',
            'celularcorporativo' => '',
            'telefono' => '',
            'correo' => '',
            'ciudad' => '',
            'ciudad2' => '',
            'direccion' => '',
            'direcccion2' => '',
            'contacto' => '',
            'celcontacto' => '',
            'categoria' => '',
            'estado' => '',
            'emision' => '',
            'banco' => '',
            'banco2' => '',
            'banco3' => '',
            'numcuenta' => '',
            'numcuenta2' => '',
            'numcuenta3' => '',
            'sigla' => '',
            'sigla2' => '',
            'tipobusqueda' => '',
            'tipobusqueda2' => '',
            'tipobusqueda3' => '',
            'tipotransaccion' => '',
            'tipocuenta' => '',
            'tipocuenta2' => '',
            'cuentaorigen' => '',
            'imagenqr' => '',
            'fechavencqr' => '',
            'esquemapago' => '',
            'representantelegal' => '',
            'nombrebancotercero' => '',
            'nrocuentatercero' => '',
            'tipocuentatercero' => '',
            'documentorespaldo' => '',
            'usuarioregistroid' => '',
            'usuarioregistronombre' => '',
            'parentesco' => '',
            'contacto2' => '',
            'celcontacto2' => '',
            'parentesco2' => '',
            'cargo' => '',
            'fechanacimiento' => '',
            'sexo' => '',
            'fechaingreso' => '',
            'fechasalida' => '',
            'nacionalidad' => '',
            'bancoorigen' => '',
            'tipoorden1' => '',
            'tipoorden2' => '',
            'tipoorden3' => '',
            'tipoplanilla' => '',
            'nroderivtramites' => '',
        ]; 
        return $rules;
    }
}
