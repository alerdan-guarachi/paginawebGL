<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreCertificateRequest extends FormRequest
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
        $user_id = Auth::id();
        $rules = [
        'eventos_id' => ['required',
        Rule::unique('certificates')->where(function ($query) use ($user_id) {
            return $query->where('user_id', $user_id);}),],
        'user_id' => 'required',
        ]; 
        return $rules;
    }
}