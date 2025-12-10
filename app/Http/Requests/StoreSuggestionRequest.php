<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuggestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // User must be authenticated (handled by middleware)
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'sujet' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'sujet.required' => 'Le sujet de la suggestion est obligatoire.',
            'sujet.max' => 'Le sujet ne doit pas dépasser 255 caractères.',
            'message.required' => 'Le message de la suggestion est obligatoire.',
            'message.min' => 'Le message doit contenir au moins 10 caractères.',
        ];
    }
}




