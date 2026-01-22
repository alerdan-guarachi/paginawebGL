<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteAuditoriaRequest extends FormRequest
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
            'id' => '',
            'nombrecompleto' => 'required|max:255',
            'fechanacimiento' => '',
            'ocupacionprofesion' => '',
            'estadocivil' => 'required|max:90',
            'ci' => 'required',
            'genero' => '',
            'lugarresidencia' => '',
            'edad' => '',
            'usuarioid' => '',
            'usuarioregistro' => '',
            'celular' => '',
            'gradoinstruccion' => '',
            'lugarnacimiento' => '',
            'direccion' => '',
            'actividadlaboral' => '',
            'sucursal' => '',
            'banco1' => '',
            'nrocredito1' => '',
            'banco2' => '',
            'nrocredito2' => '',
            'banco3' => '',
            'nrocredito3' => '',
            'nrocredito4' => '',
            'nrocredito5' => '',
            'nrocredito6' => '',
            'nrocredito7' => '',
            'nrocredito8' => '',
            'nrocredito9' => '',
            'nrocredito10' => '',
            'nrocredito11' => '',
            'nrocredito12' => '',
            'nrocredito13' => '',
            'nrocredito14' => '',
            'nrocredito15' => '',
            'nrocredito16' => '',
            'nrocredito17' => '',
            'nrocredito18' => '',
            'tipocliente' => '',
            'nombreespcon' => '',
            'ciespcon' => '',
        ]; 
        return $rules;
    }
}
