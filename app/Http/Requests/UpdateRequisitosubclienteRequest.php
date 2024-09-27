<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequisitosubclienteRequest extends FormRequest
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
        'clientebancoid' => '',
        'clientebanconombre' => '',
        'clientecomunid' => '',
        'clientecomunnombre' => '',
        'clienteitaid' => '',
        'clienteitanombre' => '',
        'clienteauditoriaid' => '',
        'clienteauditorianombre' => '',
        'poder' => '',
        'avcci' => '',
        'cnacasegurado' => '',
        'ciasegurado' => '',
        'cmatrimonio' => '',
        'cnacconyuge' => '',
        'ciconyuge' => '',
        'cnacjihos' => '',
        'cihijos' => '',
        'denfaccidente' => '',
        'crodomicilio' => '',
        'contrato' => '',
        'usuarioid' => '',
        'usuarioregistro' => '',
        ]; 
        return $rules;
    }
}
