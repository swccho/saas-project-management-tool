<?php

namespace App\Http\Requests\Project;

use App\Models\ProjectMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectMemberRequest extends FormRequest
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
        $project = $this->route('project');

        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('project_members')->where('project_id', $project->id),
            ],
            'role' => ['required', 'string', Rule::in([
                ProjectMember::ROLE_PROJECT_ADMIN,
                ProjectMember::ROLE_PROJECT_MEMBER,
                ProjectMember::ROLE_PROJECT_VIEWER,
            ])],
        ];
    }
}
