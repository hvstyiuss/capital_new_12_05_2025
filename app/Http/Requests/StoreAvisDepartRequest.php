<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvisDepartRequest extends FormRequest
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
            'avis_id' => ['required', 'exists:avis,id'],
            'nb_jours_demandes' => ['nullable', 'integer', 'min:0'],
            'date_depart' => ['nullable', 'date'],
            'date_retour' => ['nullable', 'date', 'after_or_equal:date_depart'],
            'odf' => ['nullable', 'string', 'max:255'],
            'statut' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'avis_id.required' => 'L\'avis est obligatoire.',
            'avis_id.exists' => 'L\'avis sélectionné n\'existe pas.',
            'nb_jours_demandes.integer' => 'Le nombre de jours doit être un nombre entier.',
            'nb_jours_demandes.min' => 'Le nombre de jours ne peut pas être négatif.',
            'date_depart.date' => 'La date de départ doit être une date valide.',
            'date_retour.date' => 'La date de retour doit être une date valide.',
            'date_retour.after_or_equal' => 'La date de retour doit être postérieure ou égale à la date de départ.',
            'odf.max' => 'L\'ODF ne peut pas dépasser 255 caractères.',
            'statut.max' => 'Le statut ne peut pas dépasser 255 caractères.',
        ];
    }
}













