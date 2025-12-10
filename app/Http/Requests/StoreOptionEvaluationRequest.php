<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOptionEvaluationRequest extends FormRequest
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
            'critere_id' => ['required', 'exists:criteres,id'],
            'intitule' => ['required', 'string', 'max:255'],
            'score' => ['required', 'integer', 'min:0'],
            'ordre' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'critere_id.required' => 'Le critère est obligatoire.',
            'critere_id.exists' => 'Le critère sélectionné n\'existe pas.',
            'intitule.required' => 'L\'intitulé est obligatoire.',
            'intitule.max' => 'L\'intitulé ne doit pas dépasser 255 caractères.',
            'score.required' => 'Le score est obligatoire.',
            'score.integer' => 'Le score doit être un nombre entier.',
            'score.min' => 'Le score ne peut pas être négatif.',
            'ordre.integer' => 'L\'ordre doit être un nombre entier.',
            'ordre.min' => 'L\'ordre ne peut pas être négatif.',
        ];
    }
}




