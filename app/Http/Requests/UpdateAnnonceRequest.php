<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnonceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller via policy
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ppr' => ['sometimes', 'nullable', 'string', 'exists:users,ppr'],
            'content' => ['sometimes', 'required', 'string', 'min:10'],
            'type_annonce_id' => ['sometimes', 'required', 'exists:type_annonces,id'],
            'statut' => ['sometimes', 'nullable', 'string', 'max:50', 'in:active,inactive'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'entites' => ['sometimes', 'required', 'array', 'min:1'],
            'entites.*' => ['exists:entites,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ppr.exists' => 'L\'utilisateur avec ce PPR n\'existe pas.',
            'content.required' => 'Le contenu de l\'annonce est obligatoire.',
            'content.min' => 'Le contenu doit contenir au moins 10 caractères.',
            'type_annonce_id.required' => 'Le type d\'annonce est obligatoire.',
            'type_annonce_id.exists' => 'Le type d\'annonce sélectionné n\'existe pas.',
            'statut.in' => 'Le statut doit être actif ou inactif.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format JPEG, PNG, JPG ou GIF.',
            'image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
            'entites.required' => 'Au moins une entité doit être sélectionnée.',
            'entites.array' => 'Les entités doivent être fournies sous forme de tableau.',
            'entites.min' => 'Au moins une entité doit être sélectionnée.',
            'entites.*.exists' => 'Une ou plusieurs entités sélectionnées n\'existent pas.',
        ];
    }
}




