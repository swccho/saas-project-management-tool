<?php

namespace App\Http\Requests\Board;

use Illuminate\Foundation\Http\FormRequest;

class StoreBoardViewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'filter_config' => ['nullable', 'array'],
            'filter_config.assignee' => ['nullable', 'integer', 'exists:users,id'],
            'filter_config.priority' => ['nullable', 'string', 'in:low,medium,high,urgent'],
            'filter_config.label' => ['nullable', 'integer', 'exists:labels,id'],
            'filter_config.status' => ['nullable', 'string', 'in:todo,in_progress,blocked,done'],
            'filter_config.overdue_only' => ['nullable', 'boolean'],
            'sort_config' => ['nullable', 'array'],
            'sort_config.mode' => ['nullable', 'string', 'in:manual,newest,oldest,due_asc,due_desc,priority,alpha,updated'],
            'is_pinned' => ['nullable', 'boolean'],
        ];
    }
}
