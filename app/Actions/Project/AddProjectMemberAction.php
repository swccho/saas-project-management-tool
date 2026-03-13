<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use App\Models\Workspace;
use App\Services\ActivityService;

class AddProjectMemberAction
{
    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    public function execute(Project $project, User $user, string $role): ProjectMember
    {
        $this->ensureUserIsWorkspaceMember($project->workspace, $user);

        $member = ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => $role,
        ]);

        $actor = auth()->user();
        $this->activityService->log($project, 'member_added', $member, $actor, [
            'added_user_id' => $user->id,
            'added_user_name' => $user->name,
        ]);

        return $member;
    }

    private function ensureUserIsWorkspaceMember(Workspace $workspace, User $user): void
    {
        if (!$workspace->members()->where('user_id', $user->id)->exists()) {
            throw new \InvalidArgumentException('User must be a workspace member to be added to the project.');
        }
    }
}
