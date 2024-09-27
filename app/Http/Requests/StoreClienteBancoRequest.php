<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteBancoRequest extends FormRequest
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
            'ci' => 'required|numeric',
            'genero' => 'required|max:45',
            'ciudad' => 'required',
            'edad' => 'required',
            'usuarioid' => '',
            'usuarioregistro' => '',
            'celular' => 'required|numeric',
            'asociadoid' => '',
            'asociadonombre' => '',
            'sucursal' => 'required',
        ]; 
        return $rules;
    }
}
