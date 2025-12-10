<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add policy later
    }

    public function rules(): array
    {
        $userId = $this->route('user')->ppr ?? null;
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|nullable|email|max:255',
            'image' => 'sometimes|nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
            'password' => 'sometimes|string|min:8',
        ];
    }
}
