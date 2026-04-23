<?php

namespace App\Http\Requests;

use App\Enums\ProjectRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AddProjectMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('project')->user_id === Auth::id();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'role' => ['required', Rule::enum(ProjectRole::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'Nessun utente trovato con questa email.',
        ];
    }
}
