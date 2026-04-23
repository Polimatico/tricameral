<?php

namespace App\Http\Requests;

use App\Enums\ForkPermission;
use App\Enums\ProjectVisibility;
use App\Enums\PullPermission;
use App\Enums\PullVisibility;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProjectSettingsRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'visibility' => ['required', Rule::enum(ProjectVisibility::class)],
            'fork_permission' => ['required', Rule::enum(ForkPermission::class)],
            'pull_permission' => ['sometimes', Rule::enum(PullPermission::class)],
            'pull_visibility' => ['sometimes', Rule::enum(PullVisibility::class)],
        ];
    }
}
