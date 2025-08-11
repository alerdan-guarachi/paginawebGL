<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
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
            'apepaterno' => '',
            'apematerno' => '',
            'nombrecompleto' => 'required',
            'nombres' => 'required',
            'ci' => 'required',
            'cicomplemento' => '',
            'ciexp' => '',
            'tipoidentificacion' => 'required',
            'picture' => 'required',
            'users_id' => '',
            'fechanacimiento' => 'required',
            'edad' => 'required',
            'estadocivil' => 'required',
            'genero' => 'required',
            'ocupacion' => 'required',
            'lugarnacimiento' => '',
            'gradoinstruccion' => 'required',
            'celular' => 'required',
            'telefono' => 'required',
            'domicilio' => 'required',
            'email' => 'required',
            'nuacua' => 'required',
            'estadolaboral' => 'required',
            'empresa' => 'nullable',
            'paisresidencia' => 'required',
            'departamentoresidencia' => 'required',
            'ciudadresidencia' => 'required',
            'aseguradora' => 'required',
            'referenciador' => 'required',
            'afp' => '',
            'numhijosmenores' => 'required',
            'alertas' => '',
            'usuarioregistro' => '',
            'usuarioultimaactualizacion' => '',
            'sucursal' => 'required',
            'fechavencci' => '',
            'tipocliente' => '',
            'paisnacimiento' => '',
            'matricula' => '',
        ]; 
        return $rules;
    }
}
