<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Project;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    public function index(Workspace $workspace, Project $project, Board $board, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id) {
            abort(404);
        }

        $subtasks = $task->subtasks()->orderBy('sort_order')->get();

        return ApiResponse::success(data: $subtasks->map(fn ($s) => [
            'id' => $s->id,
            'title' => $s->title,
            'is_completed' => $s->is_completed,
            'sort_order' => $s->sort_order,
        ]));
    }

    public function store(Request $request, Workspace $workspace, Project $project, Board $board, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:500'],
        ]);

        $sortOrder = $task->subtasks()->max('sort_order') + 1;

        $subtask = Subtask::create([
            'task_id' => $task->id,
            'title' => $validated['title'],
            'sort_order' => $sortOrder,
            'created_by' => $request->user()->id,
        ]);

        return ApiResponse::success(data: [
            'id' => $subtask->id,
            'title' => $subtask->title,
            'is_completed' => $subtask->is_completed,
            'sort_order' => $subtask->sort_order,
        ], status: 201);
    }

    public function update(Request $request, Workspace $workspace, Project $project, Board $board, Task $task, Subtask $subtask): JsonResponse
    {
        $this->authorize('update', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id
            || $subtask->task_id !== $task->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:500'],
            'is_completed' => ['sometimes', 'boolean'],
        ]);

        $subtask->update($validated);

        return ApiResponse::success(data: [
            'id' => $subtask->id,
            'title' => $subtask->title,
            'is_completed' => $subtask->is_completed,
            'sort_order' => $subtask->sort_order,
        ]);
    }

    public function destroy(Workspace $workspace, Project $project, Board $board, Task $task, Subtask $subtask): JsonResponse
    {
        $this->authorize('update', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id
            || $subtask->task_id !== $task->id) {
            abort(404);
        }

        $subtask->delete();

        return ApiResponse::success(message: 'Subtask deleted.', status: 200);
    }
}
