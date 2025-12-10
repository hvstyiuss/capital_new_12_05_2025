<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvisRequest extends FormRequest
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
            'demande_id' => ['required', 'exists:demandes,id'],
            'date_depot' => ['nullable', 'date'],
            'is_validated' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'demande_id.required' => 'L\'ID de la demande est obligatoire.',
            'demande_id.exists' => 'La demande sélectionnée n\'existe pas.',
            'date_depot.date' => 'La date de dépôt doit être une date valide.',
            'is_validated.boolean' => 'Le statut de validation doit être vrai ou faux.',
        ];
    }
}





