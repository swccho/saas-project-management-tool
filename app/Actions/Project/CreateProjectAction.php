<?php

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use App\Models\Workspace;
use App\Services\ActivityService;
use Illuminate\Support\Str;

class CreateProjectAction
{
    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    public function execute(User $user, Workspace $workspace, array $data): Project
    {
        $slug = Str::slug($data['name']);
        $slug = $this->ensureUniqueSlug($workspace, $slug);
        $key = $this->generateUniqueKey($workspace);

        $project = Project::create([
            'workspace_id' => $workspace->id,
            'name' => $data['name'],
            'slug' => $slug,
            'key' => $key,
            'description' => $data['description'] ?? null,
            'color' => $data['color'] ?? null,
            'status' => Project::STATUS_ACTIVE,
            'created_by' => $user->id,
        ]);

        ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => ProjectMember::ROLE_PROJECT_ADMIN,
        ]);

        $this->activityService->log($workspace, 'project_created', $project, $user, [
            'project_name' => $project->name,
            'project_id' => $project->id,
        ]);

        return $project;
    }

    private function ensureUniqueSlug(Workspace $workspace, string $slug): string
    {
        $baseSlug = $slug;
        $counter = 1;

        while ($workspace->projects()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function generateUniqueKey(Workspace $workspace): string
    {
        $count = $workspace->projects()->count();

        return $count === 0 ? 'PROJ' : 'PROJ'.($count + 1);
    }
}
