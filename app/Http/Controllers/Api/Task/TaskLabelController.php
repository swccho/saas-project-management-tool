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

class TaskLabelController extends Controller
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
            'label_ids' => ['required', 'array'],
            'label_ids.*' => ['integer', 'exists:labels,id'],
        ]);

        $labelIds = $validated['label_ids'];
        foreach ($labelIds as $id) {
            $label = \App\Models\Label::find($id);
            if ($label && $label->project_id !== $project->id) {
                return ApiResponse::error('All labels must belong to the project.', 422);
            }
        }

        $task->labels()->sync($labelIds);

        $this->activityService->log($task, 'labels_updated', $request->user(), 'Labels updated', ['label_ids' => $labelIds]);

        return ApiResponse::success(data: new TaskResource($task->fresh()->load(['assignee', 'labels'])));
    }
}
