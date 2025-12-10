<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMutationRequest extends FormRequest
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
            'to_entite_id' => ['sometimes', 'exists:entites,id'],
            'mutation_type' => ['sometimes', 'in:interne,externe'],
            'motif' => ['sometimes', 'string', 'max:255'],
            'motif_autre' => ['sometimes', 'nullable', 'string', 'max:255'],
            'approved_by_current_direction' => ['sometimes', 'boolean'],
            'approved_by_destination_direction' => ['sometimes', 'boolean'],
            'approved_by_super_collaborateur_rh' => ['sometimes', 'boolean'],
            'rejected_by_current_direction' => ['sometimes', 'boolean'],
            'rejected_by_destination_direction' => ['sometimes', 'boolean'],
            'rejected_by_super_rh' => ['sometimes', 'boolean'],
            'rejection_reason_current' => ['sometimes', 'nullable', 'string'],
            'rejection_reason_destination' => ['sometimes', 'nullable', 'string'],
            'rejection_reason_super_rh' => ['sometimes', 'nullable', 'string'],
            'date_debut_affectation' => ['sometimes', 'nullable', 'date'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'to_entite_id.exists' => 'L\'entité sélectionnée n\'existe pas.',
            'valide_par.exists' => 'L\'utilisateur avec ce PPR n\'existe pas.',
        ];
    }
}


