<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteComunRequest extends FormRequest
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
        'nombrecompleto' => 'required',
        'fechanacimiento' => 'required',
        'ocupacionprofesion' => 'required',
        'estadocivil' => 'required',
        'ci' => 'required|numeric',
        'genero' => 'required',
        'ciudad' => 'required',
        'edad' => 'required',
        'usuarioid' => '',
        'usuarioregistro' => '',
        'celular' => '',
        'sucursal' =>'',
        ]; 
        return $rules;
    }
}
