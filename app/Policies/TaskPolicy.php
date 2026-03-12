<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $user->workspaces()->where('workspaces.id', $task->workspace_id)->exists();
    }

    public function create(User $user, $column): bool
    {
        return $user->workspaces()
            ->where('workspaces.id', $column->board->project->workspace_id)
            ->exists();
    }

    public function update(User $user, Task $task): bool
    {
        return $user->workspaces()->where('workspaces.id', $task->workspace_id)->exists();
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->workspaces()->where('workspaces.id', $task->workspace_id)->exists();
    }
}
