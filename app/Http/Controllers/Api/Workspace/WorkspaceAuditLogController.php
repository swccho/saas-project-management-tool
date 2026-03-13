<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Models\WorkspaceAuditLog;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkspaceAuditLogController extends Controller
{
    public function index(Request $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $query = $workspace->auditLogs()->with('actor')->orderByDesc('created_at');

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        if ($request->filled('actor_id')) {
            $query->where('actor_user_id', $request->actor_id);
        }

        $logs = $query->paginate(20);

        $data = $logs->map(fn ($log) => [
            'id' => $log->id,
            'action_type' => $log->action_type,
            'summary' => $log->summary,
            'actor' => $log->actor ? [
                'id' => $log->actor->id,
                'name' => $log->actor->name,
            ] : null,
            'target_type' => $log->target_type,
            'target_id' => $log->target_id,
            'meta' => $log->meta,
            'created_at' => $log->created_at->toIso8601String(),
        ]);

        return ApiResponse::success(data: [
            'data' => $data,
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ],
        ]);
    }
}
