<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'username' => ['required', 'string', 'unique:users,username,'.$this->id],  
            'telephone' => ['string'],                
            'age' => ['required', 'integer'],              
            'email' => ['required', 'email', 'unique:users,email,'.$this->id],
            'password' => ['required', 'string', 'min:6']
        ];
    }
}
