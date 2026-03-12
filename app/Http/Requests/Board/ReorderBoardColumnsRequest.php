<?php

namespace App\Http\Requests\Board;

use Illuminate\Foundation\Http\FormRequest;

class ReorderBoardColumnsRequest extends FormRequest
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
            'column_ids' => ['required', 'array'],
            'column_ids.*' => ['integer', 'exists:board_columns,id'],
        ];
    }
}
