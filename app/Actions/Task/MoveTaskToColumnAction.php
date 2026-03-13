<?php

namespace App\Actions\Task;

use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\NotificationService;
use App\Services\TaskActivityService;
use Illuminate\Support\Facades\DB;

class MoveTaskToColumnAction
{
    public function __construct(
        private readonly TaskActivityService $activityService,
        private readonly NotificationService $notificationService,
        private readonly ActivityService $activityServiceGlobal
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
                $actor = auth()->user();
                $this->activityService->log($task, 'moved', $actor, 'Task moved', [
                    'from_column_id' => $oldColumnId,
                    'to_column_id' => $column->id,
                ]);

                $this->activityServiceGlobal->log($task->project, 'task_moved', $task, $actor, [
                    'task_id' => $task->id,
                    'task_title' => $task->title,
                ]);

                $excludeIds = array_filter([$actor?->id, $task->assigned_to]);
                foreach ($task->watchers()->pluck('user_id') as $watcherId) {
                    if (in_array($watcherId, $excludeIds, true)) {
                        continue;
                    }
                    $watcher = User::find($watcherId);
                    if ($watcher) {
                        $this->notificationService->create(
                            $watcher,
                            'task_moved',
                            'Task moved',
                            "{$actor?->name} moved \"{$task->title}\" to another column",
                            [
                                'task_id' => $task->id,
                                'project_id' => $task->project_id,
                                'workspace_id' => $task->workspace_id,
                            ]
                        );
                    }
                }
            }

            return $task->fresh();
        });
    }
}
