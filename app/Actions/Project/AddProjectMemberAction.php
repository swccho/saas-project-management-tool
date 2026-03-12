<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use App\Models\Workspace;

class AddProjectMemberAction
{
    public function execute(Project $project, User $user, string $role): ProjectMember
    {
        $this->ensureUserIsWorkspaceMember($project->workspace, $user);

        return ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => $role,
        ]);
    }

    private function ensureUserIsWorkspaceMember(Workspace $workspace, User $user): void
    {
        if (!$workspace->members()->where('user_id', $user->id)->exists()) {
            throw new \InvalidArgumentException('User must be a workspace member to be added to the project.');
        }
    }
}
