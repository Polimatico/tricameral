<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<mixed>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'nickname' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_\-]+$/', Rule::unique('users', 'nickname')->ignore($this->user()->id)],
            'show_name' => ['nullable', 'boolean'],
            'current_password' => ['nullable', 'string', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed', 'required_with:current_password'],
        ];
    }
}
