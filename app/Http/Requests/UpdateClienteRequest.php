<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if($this->users_id == auth()->user()->id){
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
            'apepaterno' => 'required|max:45',
        'apematerno' => 'required|max:45',
        'nombres' => 'required',
        'ci' => 'required|max:45',
        'cicomplemento' => 'required|max:45',
        'ciexp' => 'required|max:45',
        'tipoidentificacion' => 'required',
        'image' => 'required',
        'users_id' => 'required',
        'fechanacimiento' => 'required',
        'edad' => 'required',
        'estadocivil' => 'required',
        'genero' => 'required',
        'ocupacion.' => 'required',
        'lugarnacimiento' => '',
        'gradoinstruccion' => '',
        'celular' => 'required',
        'telefono' => 'required',
        'domicilio' => 'required',
        'email' => 'required',
        'nuacua' => 'required',
        'estadolaboral' => 'required',
        'empresa' => 'required',
        'paisresidencia' => 'required',
        'departamentoresidencia' => 'required',
        'ciudadresidencia' => 'required',
        'aseguradora' => 'required',
        'referenciador' => 'required',
        'afp' => 'required',
        'numhijosmenores' => 'required',
        'alertas' => 'required',
        'usuarioregistro' => 'required',
        'usuarioultimaactualizacion' => 'required',
        'sucursal' => 'required',
        'fechavencci' => '',
        'tipocliente' => '',
        'paisnacimiento' => '',
        'matricula' => '',
    
        ]; 
        return $rules;
    }
}
