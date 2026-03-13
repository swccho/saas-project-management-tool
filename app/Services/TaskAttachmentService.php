<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentService
{
    private const MAX_SIZE_MB = 10;

    private const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
        'text/csv',
    ];

    public function store(Task $task, UploadedFile $file, int $uploadedBy): TaskAttachment
    {
        $path = $this->buildPath($task);
        $storedName = $file->hashName();
        $file->storeAs($path, $storedName, 'local');

        return TaskAttachment::create([
            'task_id' => $task->id,
            'uploaded_by' => $uploadedBy,
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $storedName,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    public function delete(TaskAttachment $attachment): void
    {
        $path = $this->buildPath($attachment->task).'/'.$attachment->stored_name;
        Storage::disk('local')->delete($path);
        $attachment->delete();
    }

    public function getStoragePath(TaskAttachment $attachment): string
    {
        return $this->buildPath($attachment->task).'/'.$attachment->stored_name;
    }

    public function getMaxSizeBytes(): int
    {
        return self::MAX_SIZE_MB * 1024 * 1024;
    }

    /**
     * @return list<string>
     */
    public function getAllowedMimes(): array
    {
        return self::ALLOWED_MIMES;
    }

    private function buildPath(Task $task): string
    {
        return sprintf(
            'workspaces/%d/projects/%d/tasks/%d',
            $task->workspace_id,
            $task->project_id,
            $task->id
        );
    }
}
