<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteAuditoriaRequest extends FormRequest
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
            'nombrecompleto' => 'required|max:255',
            'fechanacimiento' => 'required|max:45',
            'ocupacionprofesion' => 'required',
            'estadocivil' => 'required|max:45',
            'ci' => 'required',
            'genero' => 'required|max:45',
            'lugarresidencia' => 'required',
            'edad' => 'required',
            'usuarioid' => 'required',
            'usuarioregistro' => 'required',
            'celular' => 'required',
            'gradoinstruccion' => 'required',
            'lugarnacimiento' => '',
            'direccion' => 'required',
            'actividadlaboral' => 'required',
            'sucursal' => 'required',
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

        ]; 
        return $rules;
    }
}
