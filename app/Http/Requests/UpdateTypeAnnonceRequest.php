<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TypeAnnonce;

class UpdateTypeAnnonceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller via middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $typeAnnonce = $this->route('typeAnnonce');
        
        return [
            'nom' => ['required', 'string', 'max:255', 'unique:type_annonces,nom,' . $typeAnnonce->id],
            'description' => ['nullable', 'string'],
            'couleur' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du type d\'annonce est obligatoire.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'nom.unique' => 'Ce nom de type d\'annonce existe déjà.',
            'couleur.regex' => 'La couleur doit être au format hexadécimal (#RRGGBB).',
            'couleur.max' => 'La couleur ne doit pas dépasser 7 caractères.',
        ];
    }
}




