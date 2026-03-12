<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Http\Resources\Task\TaskResource;
use App\Models\Board;
use App\Models\Project;
use App\Models\Task;
use App\Models\Workspace;
use App\Services\TaskActivityService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskMetaController extends Controller
{
    public function __construct(
        private readonly TaskActivityService $activityService
    ) {}

    public function update(Request $request, Workspace $workspace, Project $project, Board $board, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id) {
            abort(404);
        }

        $validated = $request->validate([
            'due_date' => ['nullable', 'date'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,urgent'],
            'status' => ['nullable', 'string', 'in:todo,in_progress,blocked,done'],
        ]);

        $task->update($validated);

        if (!empty($validated)) {
            $this->activityService->log($task, 'meta_updated', $request->user(), 'Task metadata updated', $validated);
        }

        return ApiResponse::success(data: new TaskResource($task->fresh()->load('assignee')));
    }
}
