<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
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
            'evalue_par' => ['nullable', 'string', 'exists:users,ppr'],
            'total_score' => ['nullable', 'integer', 'min:0'],
            'commentaire' => ['nullable', 'string'],
            'annee' => ['nullable', 'string', 'max:4'],
            'type_prime_id' => ['nullable', 'integer'],
            'observation' => ['nullable', 'boolean'],
            'total_prime' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ppr.required' => 'Le PPR de l\'utilisateur est obligatoire.',
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
