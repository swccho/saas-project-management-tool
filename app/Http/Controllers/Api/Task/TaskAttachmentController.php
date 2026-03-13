<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskAttachmentRequest;
use App\Models\Board;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\Workspace;
use App\Services\TaskAttachmentService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentController extends Controller
{
    public function __construct(
        private readonly TaskAttachmentService $attachmentService
    ) {}

    public function index(Workspace $workspace, Project $project, Board $board, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id) {
            abort(404);
        }

        $attachments = $task->attachments()->with('uploader')->orderByDesc('created_at')->get();

        return ApiResponse::success(data: $attachments->map(fn ($a) => [
            'id' => $a->id,
            'original_name' => $a->original_name,
            'mime_type' => $a->mime_type,
            'size' => $a->size,
            'uploaded_by' => $a->uploader ? [
                'id' => $a->uploader->id,
                'name' => $a->uploader->name,
            ] : null,
            'created_at' => $a->created_at->toIso8601String(),
        ]));
    }

    public function store(
        StoreTaskAttachmentRequest $request,
        Workspace $workspace,
        Project $project,
        Board $board,
        Task $task
    ): JsonResponse {
        $this->authorize('update', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id) {
            abort(404);
        }

        $attachment = $this->attachmentService->store(
            $task,
            $request->file('file'),
            $request->user()->id
        );

        $attachment->load('uploader');

        return ApiResponse::success(data: [
            'id' => $attachment->id,
            'original_name' => $attachment->original_name,
            'mime_type' => $attachment->mime_type,
            'size' => $attachment->size,
            'uploaded_by' => [
                'id' => $attachment->uploader->id,
                'name' => $attachment->uploader->name,
            ],
            'created_at' => $attachment->created_at->toIso8601String(),
        ], status: 201);
    }

    public function destroy(
        Workspace $workspace,
        Project $project,
        Board $board,
        Task $task,
        TaskAttachment $attachment
    ): JsonResponse {
        $this->authorize('update', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id
            || $attachment->task_id !== $task->id) {
            abort(404);
        }

        $this->attachmentService->delete($attachment);

        return ApiResponse::success(message: 'Attachment deleted.', status: 200);
    }

    public function download(
        Request $request,
        Workspace $workspace,
        Project $project,
        Board $board,
        Task $task,
        TaskAttachment $attachment
    ) {
        $this->authorize('view', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id
            || $attachment->task_id !== $task->id) {
            abort(404);
        }

        $path = $this->attachmentService->getStoragePath($attachment);

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download(
            $path,
            $attachment->original_name,
            ['Content-Type' => $attachment->mime_type]
        );
    }
}
