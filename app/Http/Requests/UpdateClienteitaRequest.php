<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteitaRequest extends FormRequest
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
       /*  $profile = $this->route()->parameter('profile'); */
        $rules = [
            'apepaterno' => '',
            'apematerno' => '',
            'nombrecompleto' => '',
            'nombres' => '',
            'ci' => '',
            'cicomplemento' => '',
            'ciexp' => '',
            'tipoidentificacion' => '',
            'picture' => '',
            'users_id' => '',
            'fechanacimiento' => '',
            'edad' => '',
            'estadocivil' => '',
            'genero' => '',
            'ocupacion' => '',
            'lugarnacimiento' => '',
            'gradoinstruccion' => '',
            'celular' => '',
            'telefono' => '',
            'domicilio' => '',
            'email' => '',
            'nuacua' => '',
            'estadolaboral' => '',
            'empresa' => '',
            'paisresidencia' => '',
            'departamentoresidencia' => '',
            'ciudadresidencia' => '',
            'aseguradora' => '',
            'referenciador' => '',
            'afp' => 'nullable',
            'numhijosmenores' => '',
            'alertas' => 'max:255',
            'usuarioregistro' => '',
            'usuarioultimaactualizacion' => '',
            'sucursal' => '',
            'fechavencci' => '',
            'tipocliente' => '',
            'paisnacimiento' => '',
            'matricula' => '',
            'numhijostotal' => '',
            'derivacion' => '',
            'billeteramovil' => '',
        ]; 
        return $rules;
    }
}
