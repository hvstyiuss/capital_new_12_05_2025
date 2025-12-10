<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TypeMaladie;

class StoreMaladieRequest extends FormRequest
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
            'type_maladie_id' => 'required|exists:type_maladies,id',
            'date_declaration' => 'required|date',
            'date_constatation' => 'nullable|date',
            'date_depart' => 'required|date',
            'date_retour' => 'nullable|date|after_or_equal:date_depart',
            'nbr_jours_demandes' => 'required|integer|min:1',
            'reference_arret' => 'nullable|string|max:255',
            'observation' => 'nullable|string',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('type_maladie_id') && $this->has('nbr_jours_demandes')) {
                $typeMaladie = TypeMaladie::find($this->input('type_maladie_id'));
                
                if ($typeMaladie && $typeMaladie->max_duration_days) {
                    $maxDuration = $typeMaladie->max_duration_days;
                    $nbrJours = (int) $this->input('nbr_jours_demandes');
                    
                    if ($nbrJours > $maxDuration) {
                        $validator->errors()->add(
                            'nbr_jours_demandes',
                            "La durée maximale pour {$typeMaladie->display_name} est de {$maxDuration} jours."
                        );
                    }
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type_maladie_id.required' => 'Le type de maladie est requis.',
            'type_maladie_id.exists' => 'Le type de maladie sélectionné est invalide.',
            'date_declaration.required' => 'La date de déclaration est requise.',
            'date_declaration.date' => 'La date de déclaration doit être une date valide.',
            'date_constatation.date' => 'La date de constatation doit être une date valide.',
            'date_depart.required' => 'La date de départ est requise.',
            'date_depart.date' => 'La date de départ doit être une date valide.',
            'date_retour.date' => 'La date de retour doit être une date valide.',
            'date_retour.after_or_equal' => 'La date de retour doit être postérieure ou égale à la date de départ.',
            'nbr_jours_demandes.required' => 'Le nombre de jours demandés est requis.',
            'nbr_jours_demandes.integer' => 'Le nombre de jours doit être un nombre entier.',
            'nbr_jours_demandes.min' => 'Le nombre de jours doit être au moins 1.',
            'reference_arret.max' => 'La référence d\'arrêt ne peut pas dépasser 255 caractères.',
        ];
    }
}



