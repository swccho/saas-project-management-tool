<?php

namespace App\Http\Requests\Task;

use App\Services\TaskAttachmentService;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskAttachmentRequest extends FormRequest
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
        $service = app(TaskAttachmentService::class);
        $maxKb = (int) ($service->getMaxSizeBytes() / 1024);

        return [
            'file' => [
                'required',
                'file',
                'max:'.$maxKb,
                'mimetypes:'.implode(',', $service->getAllowedMimes()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please select a file to upload.',
            'file.max' => 'File must not exceed 10MB.',
        ];
    }
}
