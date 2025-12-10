<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectMutationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller via policy
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'rejection_type' => ['required', 'string', 'in:current,destination'],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'rejection_type.required' => 'Le type de rejet est obligatoire.',
            'rejection_type.in' => 'Le type de rejet doit être "current" ou "destination".',
            'rejection_reason.max' => 'La raison du rejet ne doit pas dépasser 1000 caractères.',
        ];
    }
}




