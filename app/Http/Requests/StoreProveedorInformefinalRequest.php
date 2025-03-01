<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProveedorInformefinalRequest extends FormRequest
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
        'id',
        'clienteitaid' => '',
        'clienteitanombre' => '',
        'clientecomunid' => '',
        'clientecomunnombre' => '',
        'clientebancoid' => '',
        'clientebanconombre' => '',
        'clienteauditoriaid' => '',
        'clienteauditorianombre' => '',
        'fechabateria' => '',
        'proveedorasignado' => 'required',
        'celularproveedor' => 'required',
        'usuarioid' => 'required',
        'usuarioregistro' => '',
        'pagoinforme' => '',
        'accionnombre' => '',
        'pagoservicio' => '',
        'fechacredito' => '',
        'usuarioautorizador' => '',
        'documentocredito' => '',
        'servicio' => '',
        'atencionservicio' => '',
        'documentolcambio' => '',
        'nrofactura' => '',
    ]; 
    return $rules;
}

}
