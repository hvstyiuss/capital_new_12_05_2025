<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddUserToRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller via middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ppr' => ['required', 'string', 'exists:users,ppr'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ppr.required' => 'Le PPR de l\'utilisateur est obligatoire.',
            'ppr.exists' => 'L\'utilisateur avec ce PPR n\'existe pas.',
        ];
    }
}




