<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreSuggestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $profiles_id = Auth::id();
        $rules = [
        'message' => 'required|max:255',
        'profiles_id' => 'required',
        'eventos_id' => ['required',
            Rule::unique('suggestions')->where(function ($query) {
                return $query->where('eventos_id', $this->eventos_id)
                    ->where('profiles_id', $this->profiles_id);}),
                        ],
                ]; 
        return $rules;
    }
}