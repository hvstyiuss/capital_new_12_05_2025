<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJoursFerieRequest extends FormRequest
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
            'date' => ['sometimes', 'date'],
            'name' => ['sometimes', 'string', 'max:255'],
            'type_jours_ferie_id' => ['sometimes', 'nullable', 'exists:type_jours_feries,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'date.date' => 'La date doit être une date valide.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'type_jours_ferie_id.exists' => 'Le type de jour férié sélectionné n\'existe pas.',
        ];
    }
}




