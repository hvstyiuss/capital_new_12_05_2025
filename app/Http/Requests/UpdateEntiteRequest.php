<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Entite;
use App\Services\EntiteService;

class UpdateEntiteRequest extends FormRequest
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
        $entite = $this->route('entite');
        $entiteService = app(EntiteService::class);
        
        return [
            'name' => ['sometimes', 'string'],
            'date_debut' => ['sometimes', 'date'],
            'date_fin' => ['sometimes', 'date', 'after_or_equal:date_debut'],
            'parent_id' => [
                'sometimes',
                'nullable',
                'exists:entites,id',
                function ($attribute, $value, $fail) use ($entite, $entiteService) {
                    if ($value === null) {
                        return;
                    }
                    
                    // Prevent entity from being its own parent
                    if ($value == $entite->id) {
                        $fail('Une entité ne peut pas être son propre parent.');
                    }
                    
                    // Prevent circular references (entity cannot be parent if it's a descendant)
                    $descendants = $entiteService->getDescendants($entite);
                    if (in_array($value, $descendants)) {
                        $fail('Cette entité ne peut pas être le parent car elle est un descendant de cette entité.');
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'date_debut.date' => 'La date de début doit être une date valide.',
            'date_fin.date' => 'La date de fin doit être une date valide.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
            'parent_id.exists' => 'L\'entité parent sélectionnée n\'existe pas.',
        ];
    }
}

