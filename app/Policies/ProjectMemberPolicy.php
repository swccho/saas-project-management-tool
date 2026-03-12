<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;

class ProjectMemberPolicy
{
    public function viewAny(User $user, Project $project): bool
    {
        return $this->canManageMembers($user, $project);
    }

    public function create(User $user, Project $project): bool
    {
        return $this->canManageMembers($user, $project);
    }

    public function update(User $user, ProjectMember $member, Project $project): bool
    {
        return $this->canManageMembers($user, $project);
    }

    public function delete(User $user, ProjectMember $member, Project $project): bool
    {
        return $this->canManageMembers($user, $project);
    }

    private function canManageMembers(User $user, Project $project): bool
    {
        $workspaceMember = $project->workspace->members()->where('user_id', $user->id)->first();
        if ($workspaceMember && in_array($workspaceMember->role, ['owner', 'admin'], true)) {
            return true;
        }

        $projectMember = $project->members()->where('user_id', $user->id)->first();

        return $projectMember?->role === ProjectMember::ROLE_PROJECT_ADMIN;
    }
}
