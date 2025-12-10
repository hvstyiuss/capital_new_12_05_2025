<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreParcoursRequest extends FormRequest
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
            'ppr' => 'required|string|exists:users,ppr',
            'entite_id' => 'required|integer|exists:entites,id',
            'poste' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'grade_id' => 'nullable|integer|exists:grades,id',
            'reason' => 'nullable|string|max:500',
            'created_by_ppr' => 'nullable|string|exists:users,ppr',
        ];
    }
}




