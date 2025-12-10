<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectDestinationReceptionRequest extends FormRequest
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
            'rejection_reason_super_rh' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'rejection_reason_super_rh.required' => 'La raison du rejet est obligatoire.',
            'rejection_reason_super_rh.min' => 'La raison du rejet doit contenir au moins 10 caractères.',
            'rejection_reason_super_rh.max' => 'La raison du rejet ne doit pas dépasser 1000 caractères.',
        ];
    }
}




