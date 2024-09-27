<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreSharedfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if($this->user_id == auth()->user()->id){
            return true;
        }else{
            return true;
        }
        
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $profiles_id = Auth::id();
        $rules = [
            'name' => 'required',
            'profiles_id' => 'required',
            'picture' => 'required',
            'eventos_id' => ['required',
            Rule::unique('sharedfiles')->where(function ($query) {
                return $query->where('eventos_id', $this->eventos_id)
                    ->where('profiles_id', $this->profiles_id);}),
                        ],
        ]; 
        return $rules;
    }
}