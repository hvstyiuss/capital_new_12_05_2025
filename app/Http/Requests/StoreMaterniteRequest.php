<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterniteRequest extends FormRequest
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
            'date_declaration' => 'required|date',
            'date_depart' => 'required|date',
            'date_retour' => 'nullable|date|after_or_equal:date_depart',
            'nbr_jours_demandes' => 'required|integer|min:1|max:98',
            'observation' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'date_declaration.required' => 'La date de déclaration est requise.',
            'date_declaration.date' => 'La date de déclaration doit être une date valide.',
            'date_depart.required' => 'La date de départ est requise.',
            'date_depart.date' => 'La date de départ doit être une date valide.',
            'date_retour.date' => 'La date de retour doit être une date valide.',
            'date_retour.after_or_equal' => 'La date de retour doit être postérieure ou égale à la date de départ.',
            'nbr_jours_demandes.required' => 'Le nombre de jours demandés est requis.',
            'nbr_jours_demandes.integer' => 'Le nombre de jours doit être un nombre entier.',
            'nbr_jours_demandes.min' => 'Le nombre de jours doit être au moins 1.',
            'nbr_jours_demandes.max' => 'Le congé maternité est limité à 98 jours.',
        ];
    }
}



