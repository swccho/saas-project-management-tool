<?php

namespace App\Policies;

use App\Models\BoardView;
use App\Models\User;

class BoardViewPolicy
{
    public function view(User $user, BoardView $view): bool
    {
        return $user->workspaces()
            ->where('workspaces.id', $view->board->project->workspace_id)
            ->exists();
    }

    public function update(User $user, BoardView $view): bool
    {
        return $view->user_id === $user->id
            && $user->workspaces()
                ->where('workspaces.id', $view->board->project->workspace_id)
                ->exists();
    }

    public function delete(User $user, BoardView $view): bool
    {
        return $view->user_id === $user->id
            && $user->workspaces()
                ->where('workspaces.id', $view->board->project->workspace_id)
                ->exists();
    }
}
