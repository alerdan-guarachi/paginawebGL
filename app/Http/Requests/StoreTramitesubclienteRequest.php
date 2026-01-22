<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTramitesubclienteRequest extends FormRequest
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
            'tramite' => 'max:255',
            'clienteitaid' => 'max:45',
            'clienteitanombre' => 'max:45',
            'apoderadoasignado' => 'max:255',
            'usuarioinicial' => 'max:255',
            'usuariofinal' => 'max:45',
            'estado' => 'max:45',
            'observaciones' => 'max:45',
            'ciudad' => 'max:45',
            'usuarioid' => 'max:45',
            'usuarioregistro' => 'max:90',
            'fechabateria' => '',
            'clienteauditoriaid' => '',
            'clienteauditorianombre' => '',
            'clientecomunid' => '',
            'clientecomunnombre' => '',
            'fechaasignacion' => '',
            'fechafinalizacion' => '',
            'usuariointerid' => '',
            'usuariointernombre' => '',
            'motivointerrupcion' => '',
            'archivofinalizado' => '',
            'fechainicio' => '',
        ]; 
        return $rules;
    }
}
