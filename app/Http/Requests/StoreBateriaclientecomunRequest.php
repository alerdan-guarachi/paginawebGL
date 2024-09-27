<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBateriaclientecomunRequest extends FormRequest
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
            'clientecomunid' => 'max:45',
            'clientecomunnombre' => 'max:45',
            'areaid' => 'max:45',
            'accionid' => 'max:45',
            'areanombre' => 'required|max:45',
            'accionnombre' => 'required|max:45',

            ]; 
            return $rules;
    }
}
