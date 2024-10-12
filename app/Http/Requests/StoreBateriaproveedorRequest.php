<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBateriaproveedorRequest extends FormRequest
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
            'proveedorid' => '',
            'proveedor' => '',
            'area' => '',
            'accion' => '',
            'horarioinicial' => '',
            'horariofinal' => '',
            'tiempoatencion' => '',
            'precio'=>'',
            'tipoarea'=>'',
            'usuarioid'=>'',
            'usuarioregistro'=>'',
            'sucursal'=>'required',
            'preciocompra'=>'',
            'asociado'=>'required',
            'asociadoid'=>'',
            'servicio'=>'',
            'estado'=>'',
            'tipoid'=>'',
            'areasid'=>'',
        ]; 
        return $rules;
    }
}
