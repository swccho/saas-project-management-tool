<?php

namespace App\Actions\Task;

use App\Models\BoardColumn;
use App\Models\Task;
use App\Services\TaskActivityService;
use Illuminate\Support\Facades\DB;

class MoveTaskToColumnAction
{
    public function __construct(
        private readonly TaskActivityService $activityService
    ) {}

    public function execute(Task $task, BoardColumn $column, int $sortOrder): Task
    {
        return DB::transaction(function () use ($task, $column, $sortOrder) {
            $oldColumnId = $task->column_id;

            $task->update([
                'column_id' => $column->id,
                'sort_order' => $sortOrder,
            ]);

            if ($oldColumnId !== $column->id) {
                BoardColumn::find($oldColumnId)?->tasks()
                    ->where('sort_order', '>', $task->getOriginal('sort_order'))
                    ->decrement('sort_order');
            }

            $column->tasks()
                ->where('id', '!=', $task->id)
                ->where('sort_order', '>=', $sortOrder)
                ->increment('sort_order');

            if ($oldColumnId !== $column->id) {
                $this->activityService->log($task, 'moved', auth()->user(), 'Task moved', [
                    'from_column_id' => $oldColumnId,
                    'to_column_id' => $column->id,
                ]);
            }

            return $task->fresh();
        });
    }
}
