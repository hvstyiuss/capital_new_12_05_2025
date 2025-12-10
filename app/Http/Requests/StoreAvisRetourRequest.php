<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvisRetourRequest extends FormRequest
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
            'avis_id' => ['required', 'exists:avis,id'],
            'nbr_jours_consumes' => ['nullable', 'integer', 'min:0'],
            'date_retour_declaree' => ['nullable', 'date'],
            'date_retour_effectif' => ['nullable', 'date'],
            'statut' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'avis_id.required' => 'L\'ID de l\'avis est obligatoire.',
            'avis_id.exists' => 'L\'avis sélectionné n\'existe pas.',
            'nbr_jours_consumes.integer' => 'Le nombre de jours consommés doit être un nombre entier.',
            'nbr_jours_consumes.min' => 'Le nombre de jours consommés ne peut pas être négatif.',
            'date_retour_declaree.date' => 'La date de retour déclarée doit être une date valide.',
            'date_retour_effectif.date' => 'La date de retour effectif doit être une date valide.',
            'statut.max' => 'Le statut ne doit pas dépasser 255 caractères.',
        ];
    }
}




