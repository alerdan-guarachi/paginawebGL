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
            'fechanacimiento' => 'required|max:45',
            'ocupacionprofesion' => 'required',
            'estadocivil' => 'required|max:90',
            'ci' => 'required',
            'genero' => 'required|max:45',
            'lugarresidencia' => 'required',
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
            'numerocuenta1' => '',
            'banco2' => '',
            'numerocuenta2' => '',
            'banco3' => '',
            'numerocuenta3' => '',
        ]; 
        return $rules;
    }
}
