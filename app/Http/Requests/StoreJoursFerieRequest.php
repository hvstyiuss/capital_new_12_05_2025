<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJoursFerieRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'name' => ['required', 'string', 'max:255'],
            'type_jours_ferie_id' => ['nullable', 'exists:type_jours_feries,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'date.required' => 'La date est obligatoire.',
            'date.date' => 'La date doit être une date valide.',
            'name.required' => 'Le nom du jour férié est obligatoire.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'type_jours_ferie_id.exists' => 'Le type de jour férié sélectionné n\'existe pas.',
        ];
    }
}




