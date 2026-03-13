<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class MyTasksService
{
    public function getTasks(Workspace $workspace, User $user, string $view, int $perPage = 20): LengthAwarePaginator
    {
        $projectIds = $workspace->projects()->where('status', Project::STATUS_ACTIVE)->pluck('id');

        $query = Task::where('workspace_id', $workspace->id)
            ->whereIn('project_id', $projectIds)
            ->with(['project:id,name,key,color', 'assignee:id,name', 'column.board:id,name']);

        $query = match ($view) {
            'assigned' => $query->where('assigned_to', $user->id),
            'created' => $query->where('created_by', $user->id),
            'watching' => $query->whereHas('watchers', fn ($q) => $q->where('user_id', $user->id)),
            'overdue' => $query->where('assigned_to', $user->id)
                ->whereNotNull('due_date')
                ->where('due_date', '<', Carbon::today()),
            'due_today' => $query->where('assigned_to', $user->id)
                ->whereDate('due_date', Carbon::today()),
            'due_week' => $query->where('assigned_to', $user->id)
                ->whereNotNull('due_date')
                ->whereBetween('due_date', [Carbon::today(), Carbon::today()->addDays(7)]),
            default => $query->where('assigned_to', $user->id),
        };

        return $query->orderByDesc('updated_at')->paginate($perPage);
    }
}
