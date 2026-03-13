<?php

namespace App\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkspaceIconRequest extends FormRequest
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
            'icon' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:512'],
        ];
    }
}
