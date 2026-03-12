<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $this->isWorkspaceMember($user, $project->workspace);
    }

    public function create(User $user, Workspace $workspace): bool
    {
        return $this->isWorkspaceMember($user, $workspace);
    }

    public function manageMembers(User $user, Project $project): bool
    {
        return $this->isWorkspaceAdminOrOwner($user, $project->workspace)
            || $project->members()->where('user_id', $user->id)->where('role', \App\Models\ProjectMember::ROLE_PROJECT_ADMIN)->exists();
    }

    public function update(User $user, Project $project): bool
    {
        return $this->isWorkspaceMember($user, $project->workspace);
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->isWorkspaceMember($user, $project->workspace);
    }

    public function archive(User $user, Project $project): bool
    {
        return $this->isWorkspaceAdminOrOwner($user, $project->workspace);
    }

    public function createBoard(User $user, Project $project): bool
    {
        return $this->view($user, $project);
    }

    private function isWorkspaceMember(User $user, Workspace $workspace): bool
    {
        return $workspace->members()->where('user_id', $user->id)->exists();
    }

    private function isWorkspaceAdminOrOwner(User $user, Workspace $workspace): bool
    {
        $member = $workspace->members()->where('user_id', $user->id)->first();

        return $member !== null && in_array($member->role, ['owner', 'admin'], true);
    }
}
