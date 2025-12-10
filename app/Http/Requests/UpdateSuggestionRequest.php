<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSuggestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sujet' => 'sometimes|required|string|max:255',
            'message' => 'sometimes|required|string|max:5000',
            'statut' => 'sometimes|required|string|in:pending,responded,archived',
            'reponse' => 'nullable|string|max:5000',
            'repondu_par' => 'nullable|string|exists:users,ppr',
        ];
    }
}




