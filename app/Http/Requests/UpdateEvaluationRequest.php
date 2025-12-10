<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEvaluationRequest extends FormRequest
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
            'evalue_par' => ['sometimes', 'nullable', 'string', 'exists:users,ppr'],
            'total_score' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'commentaire' => ['sometimes', 'nullable', 'string'],
            'annee' => ['sometimes', 'nullable', 'string', 'max:4'],
            'type_prime_id' => ['sometimes', 'nullable', 'integer'],
            'observation' => ['sometimes', 'nullable', 'boolean'],
            'total_prime' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ppr.exists' => 'L\'utilisateur avec ce PPR n\'existe pas.',
            'evalue_par.exists' => 'L\'évaluateur avec ce PPR n\'existe pas.',
            'total_score.integer' => 'Le score total doit être un nombre entier.',
            'total_score.min' => 'Le score total ne peut pas être négatif.',
            'annee.max' => 'L\'année ne doit pas dépasser 4 caractères.',
            'total_prime.integer' => 'Le total de la prime doit être un nombre entier.',
            'total_prime.min' => 'Le total de la prime ne peut pas être négatif.',
        ];
    }
}




