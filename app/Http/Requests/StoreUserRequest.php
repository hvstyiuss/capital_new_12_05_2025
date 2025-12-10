<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add policy later
    }

    public function rules(): array
    {
        return [
            'ppr' => 'required|string|max:50|unique:users,ppr',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'email' => 'nullable|email|max:255',
            'image' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ];
    }
}
