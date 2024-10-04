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
            'apepaterno' => 'max:45',
            'apematerno' => 'max:45',
            'nombrecompleto' => 'required',
            'nombres' => 'required',
            'ci' => 'required|numeric',
            'cicomplemento' => '',
            'ciexp' => '',
            'tipoidentificacion' => 'required|max:45',
            'picture' => '',
            'users_id' => '',
            'fechanacimiento' => 'required',
            'edad' => 'required',
            'estadocivil' => 'required|max:45',
            'genero' => 'required|max:45',
            'ocupacion' => 'required|max:45',
            'lugarnacimiento' => 'required|max:45',
            'gradoinstruccion' => 'max:45',
            'celular' => 'required',
            'telefono' => '',
            'domicilio' => 'required|max:255',
            'email' => 'required|email',
            'nuacua' => 'required|numeric',
            'estadolaboral' => 'required|max:45',
            'empresa' => 'nullable',
            'paisresidencia' => 'required|max:45',
            'departamentoresidencia' => 'required|max:45',
            'ciudadresidencia' => 'required|max:45',
            'aseguradora' => 'nullable',
            'referenciador' => 'required',
            'afp' => 'nullable',
            'numhijosmenores' => '',
            'alertas' => 'max:255',
            'usuarioregistro' => '',
            'usuarioultimaactualizacion' => '',
            'sucursal' => 'required|max:45',
            'fechavencci' => 'required',
            'tipocliente' => '',
        ]; 
        return $rules;
    }
}
