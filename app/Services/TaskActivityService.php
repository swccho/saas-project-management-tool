<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\User;

class TaskActivityService
{
    public function log(Task $task, string $type, ?User $user, string $message, array $meta = []): TaskActivity
    {
        return TaskActivity::create([
            'task_id' => $task->id,
            'user_id' => $user?->id,
            'type' => $type,
            'message' => $message,
            'meta' => $meta ?: null,
            'created_at' => now(),
        ]);
    }
}
