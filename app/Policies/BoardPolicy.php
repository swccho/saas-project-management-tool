<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\User;

class BoardPolicy
{
    public function view(User $user, Board $board): bool
    {
        return $user->workspaces()->where('workspaces.id', $board->project->workspace_id)->exists();
    }

    public function create(User $user, $project): bool
    {
        return $user->workspaces()->where('workspaces.id', $project->workspace_id)->exists();
    }

    public function update(User $user, Board $board): bool
    {
        return $user->workspaces()->where('workspaces.id', $board->project->workspace_id)->exists();
    }

    public function delete(User $user, Board $board): bool
    {
        return $user->workspaces()->where('workspaces.id', $board->project->workspace_id)->exists();
    }
}
