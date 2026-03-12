<?php

namespace App\Http\Controllers\Api\Task;

use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\MoveTaskToColumnAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\MoveTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\Task\TaskResource;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Project;
use App\Models\Task;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function __construct(
        private readonly CreateTaskAction $createTaskAction,
        private readonly MoveTaskToColumnAction $moveTaskToColumnAction
    ) {}

    public function index(Workspace $workspace, Project $project, Board $board): JsonResponse
    {
        $this->authorize('view', $project);

        if ($project->workspace_id !== $workspace->id || $board->project_id !== $project->id) {
            abort(404);
        }

        $columns = $board->columns()
            ->with(['tasks' => fn ($q) => $q->with(['assignee', 'labels'])->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $data = [
            'columns' => $columns->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'color' => $c->color,
                'sort_order' => $c->sort_order,
                'tasks' => TaskResource::collection($c->tasks),
            ]),
        ];

        return ApiResponse::success(data: $data);
    }

    public function store(StoreTaskRequest $request, Workspace $workspace, Project $project, Board $board): JsonResponse
    {
        $column = BoardColumn::findOrFail($request->validated('column_id'));

        if ($column->board_id !== $board->id || $board->project_id !== $project->id || $project->workspace_id !== $workspace->id) {
            abort(404);
        }

        $this->authorize('create', [Task::class, $column]);

        $task = $this->createTaskAction->execute(
            user: $request->user(),
            column: $column,
            data: $request->validated()
        );

        return ApiResponse::success(
            data: new TaskResource($task->load('assignee')),
            status: 201
        );
    }

    public function show(Workspace $workspace, Project $project, Board $board, Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id) {
            abort(404);
        }

        return ApiResponse::success(data: new TaskResource($task->load(['assignee', 'labels', 'subtasks'])));
    }

    public function update(UpdateTaskRequest $request, Workspace $workspace, Project $project, Board $board, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id) {
            abort(404);
        }

        $task->update($request->validated());

        return ApiResponse::success(data: new TaskResource($task->fresh()->load('assignee')));
    }

    public function destroy(Workspace $workspace, Project $project, Board $board, Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id) {
            abort(404);
        }

        $task->delete();

        return ApiResponse::success(message: 'Task deleted.', status: 200);
    }

    public function move(MoveTaskRequest $request, Workspace $workspace, Project $project, Board $board, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        if ($task->workspace_id !== $workspace->id
            || $task->project_id !== $project->id
            || $task->board_id !== $board->id) {
            abort(404);
        }

        $column = BoardColumn::findOrFail($request->validated('column_id'));

        if ($column->board_id !== $board->id) {
            return ApiResponse::error('Column must belong to the same board.', 422);
        }

        $task = $this->moveTaskToColumnAction->execute(
            task: $task,
            column: $column,
            sortOrder: $request->validated('sort_order')
        );

        return ApiResponse::success(data: new TaskResource($task->load(['assignee', 'labels', 'subtasks'])));
    }
}
