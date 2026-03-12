<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class MoveTaskRequest extends FormRequest
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
            'column_id' => ['required', 'integer', 'exists:board_columns,id'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ];
    }
}
