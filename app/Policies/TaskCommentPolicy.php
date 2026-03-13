<?php

namespace App\Policies;

use App\Models\TaskComment;
use App\Models\User;

class TaskCommentPolicy
{
    public function view(User $user, TaskComment $comment): bool
    {
        return $user->workspaces()
            ->where('workspaces.id', $comment->task->project->workspace_id)
            ->exists();
    }

    public function update(User $user, TaskComment $comment): bool
    {
        return $comment->user_id === $user->id
            && $user->workspaces()
                ->where('workspaces.id', $comment->task->project->workspace_id)
                ->exists();
    }

    public function delete(User $user, TaskComment $comment): bool
    {
        return $comment->user_id === $user->id
            && $user->workspaces()
                ->where('workspaces.id', $comment->task->project->workspace_id)
                ->exists();
    }
}
