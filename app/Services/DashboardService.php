<?php

namespace App\Services;

use App\Models\Activity;
use App\Services\ProfileService;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;

class DashboardService
{
    public function getData(Workspace $workspace, User $user): array
    {
        $today = Carbon::today();
        $dueSoonEnd = $today->copy()->addDays(7);

        $projectIds = $workspace->projects()->where('status', Project::STATUS_ACTIVE)->pluck('id');

        $tasksAssigned = Task::where('workspace_id', $workspace->id)
            ->where('assigned_to', $user->id)
            ->whereIn('project_id', $projectIds)
            ->count();

        $tasksOverdue = Task::where('workspace_id', $workspace->id)
            ->where('assigned_to', $user->id)
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->whereIn('project_id', $projectIds)
            ->count();

        $tasksDueSoon = Task::where('workspace_id', $workspace->id)
            ->where('assigned_to', $user->id)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$today, $dueSoonEnd])
            ->whereIn('project_id', $projectIds)
            ->count();

        $recentActivity = $this->getRecentActivity($workspace, 10);

        $activeProjects = $workspace->projects()
            ->where('status', Project::STATUS_ACTIVE)
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'key', 'color']);

        $favoriteProjects = $user->favoriteProjects()
            ->where('projects.workspace_id', $workspace->id)
            ->where('projects.status', Project::STATUS_ACTIVE)
            ->orderBy('projects.name')
            ->get(['projects.id', 'projects.name', 'projects.key', 'projects.color']);

        return [
            'projects_count' => $workspace->projects()->where('status', Project::STATUS_ACTIVE)->count(),
            'tasks_assigned' => $tasksAssigned,
            'tasks_overdue' => $tasksOverdue,
            'tasks_due_soon' => $tasksDueSoon,
            'recent_activity' => $recentActivity,
            'active_projects' => $activeProjects->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'key' => $p->key,
                'color' => $p->color,
            ]),
            'favorite_projects' => $favoriteProjects->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'key' => $p->key,
                'color' => $p->color,
            ]),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getRecentActivity(Workspace $workspace, int $limit): array
    {
        $projectIds = $workspace->projects()->pluck('id');

        $workspaceActivities = Activity::where('subject_type', Workspace::class)
            ->where('subject_id', $workspace->id)
            ->with('actor')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        $projectActivities = Activity::where('subject_type', Project::class)
            ->whereIn('subject_id', $projectIds)
            ->with('actor')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        $merged = $workspaceActivities->merge($projectActivities)
            ->sortByDesc('created_at')
            ->take($limit)
            ->values();

        $profileService = app(ProfileService::class);

        return $merged->map(fn ($a) => [
            'id' => $a->id,
            'action' => $a->action,
            'actor' => $a->actor ? [
                'id' => $a->actor->id,
                'name' => $a->actor->name,
                'avatar_url' => $a->actor->avatar ? $profileService->getAvatarUrl($a->actor) : null,
            ] : null,
            'target_type' => $a->target_type,
            'target_id' => $a->target_id,
            'meta' => array_merge($a->meta ?? [], [
                'project_id' => $a->subject_type === Project::class ? $a->subject_id : ($a->meta['project_id'] ?? null),
            ]),
            'message' => $this->formatMessage($a),
            'created_at' => $a->created_at->toIso8601String(),
        ])->toArray();
    }

    private function formatMessage(Activity $a): string
    {
        $actor = $a->actor?->name ?? 'Someone';
        $meta = $a->meta ?? [];

        return match ($a->action) {
            'project_created' => "{$actor} created project \"{$meta['project_name']}\"",
            'board_created' => "{$actor} created board \"{$meta['board_name']}\"",
            'task_created' => "{$actor} created task \"{$meta['task_title']}\"",
            'task_moved' => "{$actor} moved a task",
            'task_assigned' => "{$actor} assigned a task",
            'task_commented' => "{$actor} commented on a task",
            'member_added' => "{$actor} added a member to a project",
            default => "{$actor} performed an action",
        };
    }
}
