<?php

namespace App\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkspacePreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'date_format' => ['sometimes', 'string', Rule::in(['Y-m-d', 'm/d/Y', 'd/m/Y'])],
            'time_format' => ['sometimes', 'string', Rule::in(['24h', '12h'])],
            'default_project_view' => ['sometimes', 'string', Rule::in(['list', 'grid'])],
            'task_number_format' => ['sometimes', 'string', Rule::in(['numeric', 'key'])],
        ];
    }
}
