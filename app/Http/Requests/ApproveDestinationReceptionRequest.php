<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveDestinationReceptionRequest extends FormRequest
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
            'date_debut_affectation' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'date_debut_affectation.required' => 'La date de début d\'affectation est obligatoire.',
            'date_debut_affectation.date' => 'La date de début d\'affectation doit être une date valide.',
            'date_debut_affectation.after_or_equal' => 'La date de début d\'affectation doit être aujourd\'hui ou une date future.',
        ];
    }
}




