<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $start = $request->input('start');
        $end = $request->input('end');

        if (!$start || !$end) {
            $start = Carbon::today()->startOfMonth()->toDateString();
            $end = Carbon::today()->endOfMonth()->addMonths(1)->toDateString();
        }

        $projectIds = $workspace->projects()->where('status', Project::STATUS_ACTIVE)->pluck('id');

        $tasks = Task::where('workspace_id', $workspace->id)
            ->whereIn('project_id', $projectIds)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$start, $end])
            ->with(['project:id,name,key,color', 'assignee:id,name'])
            ->orderBy('due_date')
            ->get();

        $data = $tasks->map(fn ($task) => [
            'id' => $task->id,
            'title' => $task->title,
            'task_number' => $task->task_number,
            'due_date' => $task->due_date->toDateString(),
            'priority' => $task->priority,
            'project' => $task->project ? [
                'id' => $task->project->id,
                'name' => $task->project->name,
                'key' => $task->project->key,
                'color' => $task->project->color,
            ] : null,
            'assignee' => $task->assignee ? [
                'id' => $task->assignee->id,
                'name' => $task->assignee->name,
            ] : null,
        ]);

        return ApiResponse::success(data: $data);
    }
}
