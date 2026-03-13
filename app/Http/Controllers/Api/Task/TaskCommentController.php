<?php

namespace App\Http\Controllers\Api\Task;

use App\Actions\Task\CreateTaskCommentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskCommentRequest;
use App\Http\Requests\Task\UpdateTaskCommentRequest;
use App\Http\Resources\Task\TaskCommentResource;
use App\Models\Board;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class TaskCommentController extends Controller
{
    public function __construct(
        private readonly CreateTaskCommentAction $createTaskCommentAction
    ) {}

    public function index(Workspace $workspace, Project $project, Board $board, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id) {
            abort(404);
        }

        $comments = $task->comments()
            ->with(['user', 'mentions', 'replies.user'])
            ->orderBy('created_at')
            ->get();

        return ApiResponse::success(data: TaskCommentResource::collection($comments));
    }

    public function store(
        StoreTaskCommentRequest $request,
        Workspace $workspace,
        Project $project,
        Board $board,
        Task $task
    ): JsonResponse {
        $this->authorize('view', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id) {
            abort(404);
        }

        $comment = $this->createTaskCommentAction->execute(
            task: $task,
            user: $request->user(),
            body: $request->validated('body'),
            parentId: $request->validated('parent_id')
        );

        return ApiResponse::success(
            data: new TaskCommentResource($comment->load('user')),
            status: 201
        );
    }

    public function update(
        UpdateTaskCommentRequest $request,
        Workspace $workspace,
        Project $project,
        Board $board,
        Task $task,
        TaskComment $comment
    ): JsonResponse {
        $this->authorize('update', $comment);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id
            || $comment->task_id !== $task->id) {
            abort(404);
        }

        $comment->update([
            'body' => $request->validated('body'),
            'edited_at' => now(),
        ]);

        return ApiResponse::success(data: new TaskCommentResource($comment->fresh()->load('user')));
    }

    public function destroy(
        Workspace $workspace,
        Project $project,
        Board $board,
        Task $task,
        TaskComment $comment
    ): JsonResponse {
        $this->authorize('delete', $comment);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id
            || $comment->task_id !== $task->id) {
            abort(404);
        }

        $comment->delete();

        return ApiResponse::success(message: 'Comment deleted.', status: 200);
    }
}
