<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvisRetourRequest extends FormRequest
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
            'nbr_jours_consumes' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'date_retour_declaree' => ['sometimes', 'nullable', 'date'],
            'date_retour_effectif' => ['sometimes', 'nullable', 'date'],
            'statut' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nbr_jours_consumes.integer' => 'Le nombre de jours consommés doit être un nombre entier.',
            'nbr_jours_consumes.min' => 'Le nombre de jours consommés ne peut pas être négatif.',
            'date_retour_declaree.date' => 'La date de retour déclarée doit être une date valide.',
            'date_retour_effectif.date' => 'La date de retour effectif doit être une date valide.',
            'statut.max' => 'Le statut ne doit pas dépasser 255 caractères.',
        ];
    }
}




