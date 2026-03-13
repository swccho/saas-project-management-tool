<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Services\MyTasksService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyTasksController extends Controller
{
    public function __construct(
        private readonly MyTasksService $myTasksService
    ) {}

    public function index(Request $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $view = $request->input('view', 'assigned');
        $validViews = ['assigned', 'created', 'watching', 'overdue', 'due_today', 'due_week'];
        if (!in_array($view, $validViews, true)) {
            $view = 'assigned';
        }

        $paginator = $this->myTasksService->getTasks(
            $workspace,
            $request->user(),
            $view,
            (int) $request->input('per_page', 20)
        );

        $data = $paginator->map(fn ($task) => [
            'id' => $task->id,
            'title' => $task->title,
            'task_number' => $task->task_number,
            'priority' => $task->priority,
            'status' => $task->status,
            'due_date' => $task->due_date?->toDateString(),
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
            'column' => $task->column ? [
                'id' => $task->column->id,
                'name' => $task->column->name,
                'board_id' => $task->column->board_id,
            ] : null,
        ]);

        return ApiResponse::success(data: [
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
}
