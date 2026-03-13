<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class WorkspaceActivityController extends Controller
{
    public function index(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $activities = Activity::where('subject_type', Workspace::class)
            ->where('subject_id', $workspace->id)
            ->with('actor')
            ->orderByDesc('created_at')
            ->paginate(20);

        $data = $activities->map(fn ($a) => [
            'id' => $a->id,
            'action' => $a->action,
            'actor' => $a->actor ? [
                'id' => $a->actor->id,
                'name' => $a->actor->name,
            ] : null,
            'target_type' => $a->target_type,
            'target_id' => $a->target_id,
            'meta' => $a->meta,
            'message' => $this->formatMessage($a),
            'created_at' => $a->created_at->toIso8601String(),
        ]);

        return ApiResponse::success(data: [
            'data' => $data,
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ],
        ]);
    }

    private function formatMessage(Activity $a): string
    {
        $actor = $a->actor?->name ?? 'Someone';
        $meta = $a->meta ?? [];

        return match ($a->action) {
            'project_created' => "{$actor} created project \"{$meta['project_name']}\"",
            'board_created' => "{$actor} created board \"{$meta['board_name']}\"",
            'task_created' => "{$actor} created task \"{$meta['task_title']}\"",
            'task_moved' => "{$actor} moved a task",
            'task_assigned' => "{$actor} assigned a task",
            'task_commented' => "{$actor} commented on a task",
            'member_added' => "{$actor} added a member to a project",
            default => "{$actor} performed an action",
        };
    }
}
