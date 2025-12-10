<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDemandeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'ppr' => ['required', 'string', 'exists:users,ppr'],
            'date_debut' => ['nullable', 'date'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ppr.required' => 'Le PPR est obligatoire.',
            'ppr.exists' => 'L\'utilisateur avec ce PPR n\'existe pas.',
            'date_debut.date' => 'La date de début doit être une date valide.',
        ];
    }
}
