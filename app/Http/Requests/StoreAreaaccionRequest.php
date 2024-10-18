<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAreaaccionRequest extends FormRequest
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
            'tipoid' => '',
            'tiponombre' => 'required|max:45',
            'areasid' => 'max:45',
            'area' => 'required|max:90',
            'accion' => 'required|max:250',
            'sucursal' => 'required|max:45',
            'precio' => 'required|max:45',
            'estado' => 'required|max:45',
            'preciocompra' => 'max:45',
            'asociado' => 'required|max:45',
            'asociadoid' => 'required|max:45',
            'categoria' => 'max:45',
            'proveedorid' => 'max:45',
            'proveedor' => 'max:255'
        ]; 
        return $rules;
    }
}
