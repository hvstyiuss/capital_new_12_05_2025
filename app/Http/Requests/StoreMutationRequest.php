<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMutationRequest extends FormRequest
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
            'ppr' => ['sometimes', 'string', 'exists:users,ppr'],
            'to_entite_id' => ['required', 'exists:entites,id'],
            'mutation_type' => ['required', 'in:interne,externe'],
            'motif' => ['required', 'string', 'max:255'],
            'motif_autre' => ['nullable', 'string', 'max:255'],
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


