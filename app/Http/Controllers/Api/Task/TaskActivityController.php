<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Project;
use App\Models\Task;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class TaskActivityController extends Controller
{
    public function index(Workspace $workspace, Project $project, Board $board, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id) {
            abort(404);
        }

        $activities = $task->activities()->with('user')->orderByDesc('created_at')->limit(50)->get();

        $data = $activities->map(fn ($a) => [
            'id' => $a->id,
            'type' => $a->type,
            'message' => $a->message,
            'meta' => $a->meta,
            'user' => $a->user ? [
                'id' => $a->user->id,
                'name' => $a->user->name,
            ] : null,
            'created_at' => $a->created_at->toIso8601String(),
        ]);

        return ApiResponse::success(data: $data);
    }
}
