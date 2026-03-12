<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use App\Services\TaskActivityService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskAssigneeController extends Controller
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
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ]);

        $userId = $validated['user_id'] ?? null;

        if ($userId !== null) {
            $isProjectMember = $project->members()->where('user_id', $userId)->exists();
            $isWorkspaceMember = $project->workspace->members()->where('user_id', $userId)->exists();
            if (!$isProjectMember && !$isWorkspaceMember) {
                return ApiResponse::error('User must be a project or workspace member.', 422);
            }
        }

        $oldAssignee = $task->assigned_to;
        $task->update(['assigned_to' => $userId]);

        $this->activityService->log($task, 'assignee_changed', $request->user(), 'Assignee changed', [
            'old_user_id' => $oldAssignee,
            'new_user_id' => $userId,
        ]);

        return ApiResponse::success(data: new \App\Http\Resources\Task\TaskResource($task->fresh()->load('assignee')));
    }
}
