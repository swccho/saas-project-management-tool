<?php

namespace App\Actions\Task;

use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskActivityService;

class CreateTaskAction
{
    public function __construct(
        private readonly TaskActivityService $activityService
    ) {}

    public function execute(User $user, BoardColumn $column, array $data): Task
    {
        $board = $column->board;
        $project = $board->project;

        $taskNumber = $project->tasks()->max('task_number') + 1;
        $sortOrder = $column->tasks()->max('sort_order') + 1;

        $task = Task::create([
            'workspace_id' => $project->workspace_id,
            'project_id' => $project->id,
            'board_id' => $board->id,
            'column_id' => $column->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'task_number' => $taskNumber,
            'created_by' => $user->id,
            'sort_order' => $sortOrder,
        ]);

        $this->activityService->log($task, 'created', $user, 'Task created', [
            'column_id' => $column->id,
        ]);

        return $task;
    }
}
