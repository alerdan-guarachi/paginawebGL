<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    /* public function authorize()
    {
        if($this->user_id == auth()->user()->id){
            return true;
        }else{
            return true;
        }
        
    } */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $profiles_id = Auth::id();
        $rules = [
            'profiles_id' => 'required',
            /* 'projects_id' => 'required', */
            'projects_id' => ['required',
            Rule::unique('members')->where(function ($query) {
                return $query->where('projects_id', $this->projects_id)
                    ->where('profiles_id', $this->profiles_id);}),
                        ],
    
        ]; 
        return $rules;
    }
}