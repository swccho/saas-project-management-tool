<?php

namespace App\Http\Requests\Project;

use App\Models\ProjectMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', 'string', Rule::in([
                ProjectMember::ROLE_PROJECT_ADMIN,
                ProjectMember::ROLE_PROJECT_MEMBER,
                ProjectMember::ROLE_PROJECT_VIEWER,
            ])],
        ];
    }
}
