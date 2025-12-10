<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteAnnuelleRequest extends FormRequest
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
            'ppr' => 'sometimes|required|string|exists:users,ppr',
            'annee' => 'sometimes|required|integer|min:2000|max:2100',
            'note' => 'sometimes|required|numeric|min:0|max:20',
            'observation' => 'nullable|string|max:500',
        ];
    }
}




