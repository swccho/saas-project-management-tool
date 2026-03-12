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
            $oldSortOrder = (int) $task->getOriginal('sort_order');
            $sameColumn = $oldColumnId === $column->id;

            $task->update([
                'column_id' => $column->id,
                'sort_order' => $sortOrder,
            ]);

            if (!$sameColumn) {
                BoardColumn::find($oldColumnId)?->tasks()
                    ->where('sort_order', '>', $oldSortOrder)
                    ->decrement('sort_order');
                $column->tasks()
                    ->where('id', '!=', $task->id)
                    ->where('sort_order', '>=', $sortOrder)
                    ->increment('sort_order');
            } else {
                if ($oldSortOrder < $sortOrder) {
                    $column->tasks()
                        ->where('id', '!=', $task->id)
                        ->where('sort_order', '>', $oldSortOrder)
                        ->where('sort_order', '<=', $sortOrder)
                        ->decrement('sort_order');
                } elseif ($oldSortOrder > $sortOrder) {
                    $column->tasks()
                        ->where('id', '!=', $task->id)
                        ->where('sort_order', '>=', $sortOrder)
                        ->where('sort_order', '<', $oldSortOrder)
                        ->increment('sort_order');
                }
            }

            if (!$sameColumn) {
                $this->activityService->log($task, 'moved', auth()->user(), 'Task moved', [
                    'from_column_id' => $oldColumnId,
                    'to_column_id' => $column->id,
                ]);
            }

            return $task->fresh();
        });
    }
}
