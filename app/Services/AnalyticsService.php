<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;

class AnalyticsService
{
    public function getWorkspaceAnalytics(Workspace $workspace, User $user): array
    {
        $projectIds = $workspace->projects()->where('status', Project::STATUS_ACTIVE)->pluck('id');

        return $this->computeAnalytics(
            Task::where('workspace_id', $workspace->id)->whereIn('project_id', $projectIds),
            $user
        );
    }

    public function getProjectAnalytics(Project $project, User $user): array
    {
        return $this->computeAnalytics(
            Task::where('project_id', $project->id),
            $user
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Task> $query
     * @return array<string, int|float>
     */
    private function computeAnalytics($query, User $user): array
    {
        $today = Carbon::today();

        $total = (clone $query)->count();
        $completed = (clone $query)->where('status', 'done')->count();
        $inProgress = (clone $query)->where('status', 'in_progress')->count();
        $overdue = (clone $query)->whereNotNull('due_date')->where('due_date', '<', $today)->count();
        $assignedToUser = (clone $query)->where('assigned_to', $user->id)->count();

        $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0.0;

        return [
            'total_tasks' => $total,
            'completed_tasks' => $completed,
            'in_progress_tasks' => $inProgress,
            'overdue_tasks' => $overdue,
            'assigned_to_user' => $assignedToUser,
            'completion_rate' => $completionRate,
        ];
    }
}
