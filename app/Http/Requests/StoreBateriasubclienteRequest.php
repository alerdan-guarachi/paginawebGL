<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBateriasubclienteRequest extends FormRequest
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
        if ($this->filled('codigo')) {
            // Reglas de validación para 'INGRESAR CÓDIGO'
            return [
                'codigo' => 'required|max:15',
            ];
        }else{
    
        $rolusuario = strtolower(auth()->user()->getRoleNames()->first());
        $esProveedorOMaestro = in_array($rolusuario, ['proveedor'/* , 'maestro' */]);

        $rules = [
            'id' => '',
            'clienteid' => '',
            'areaid' => '',
            'accionid' => '',
            'clientenombre' => '',
            'tipocliente' => '',
            'areanombre' => 'required_if:tipoarea,Estudios|nullable',
            'accionnombre' => '',
            'clientecomunid' => '',
            'clientecomunnombre' => '',
            'clienteauditoriaid' => '',
            'clienteauditorianombre' => '',
            'clienteitaid' => '',
            'clienteitanombre' => '',
            'tipoarea' => 'required',
            'precio'=>'',
            'fechabateria' => '',
            'antecedentes' => '',
            'informe' => '',
            'fechainforme' => '',
            'preciocompra' => '',
            'proveedorasignado' => '',
            'servicio' => '',
            'usuarioid' => '',
            'usuarioregistro' => '',
            'pagoservicio' => '',
            'fechacredito' => '',
            'usuarioautorizador' => '',
            'documentocredito' => '',
            'documentolcambio' => '',
            'comision' => '',
            'sesiones'=>'',
            'provinfofinalid' => '',
            'motivoanulacion' => '',
            'usuarioanulacion' => '',
            'medicoderivante' => '',
            'orden' => /* $esProveedorOMaestro ? '' :  */'',
            'tramite' => '',
            'prioridad' => '',
            'nrobancoorigen' => '',
            'fechapago' => '',
            'estadoaprobacion' => '',
            'comprobante' => '',
            'cheque' => '',
            'usuariocomprobante' => '',
            'ordenid' => '',
            'fechamora' => '',
            'idsubproc' => '',
        ]; 
        return $rules;
        }
    }
    
}
