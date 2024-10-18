<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAreaaccionRequest extends FormRequest
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
            'area' => 'required|max:45',
            'accion' => 'required|max:45',
            'sucursal' => 'required|max:45',
            'precio' => 'required|max:45',
            'estado' => 'required|max:45',
            'preciocompra' => 'max:45',
            'asociado' => 'max:45',
            'asociadoid' => 'max:45',
            'categoria' => 'required|max:45',
            'proveedorid' => 'required|max:45',
            'proveedor' => 'required|max:255'
        ]; 
        return $rules;
    }
}
