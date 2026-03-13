<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Project;
use App\Models\Task;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskWatchController extends Controller
{
    public function store(Request $request, Workspace $workspace, Project $project, Board $board, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id) {
            abort(404);
        }

        $task->watchers()->syncWithoutDetaching([$request->user()->id]);

        return ApiResponse::success(data: [
            'is_watching' => true,
            'watchers_count' => $task->watchers()->count(),
        ]);
    }

    public function destroy(Request $request, Workspace $workspace, Project $project, Board $board, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id) {
            abort(404);
        }

        $task->watchers()->detach($request->user()->id);

        return ApiResponse::success(data: [
            'is_watching' => false,
            'watchers_count' => $task->watchers()->count(),
        ]);
    }
}
