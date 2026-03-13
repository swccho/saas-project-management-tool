<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\UpdateWorkspacePreferencesRequest;
use App\Models\Workspace;
use App\Services\AuditLogService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class WorkspacePreferencesController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {}

    public function show(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $preferences = $workspace->preferences ?? [];

        return ApiResponse::success(data: array_merge([
            'date_format' => 'Y-m-d',
            'time_format' => '24h',
            'default_project_view' => 'list',
            'task_number_format' => 'key',
        ], $preferences));
    }

    public function update(UpdateWorkspacePreferencesRequest $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('update', $workspace);

        $preferences = $workspace->preferences ?? [];
        $preferences = array_merge($preferences, $request->validated());
        $workspace->update(['preferences' => $preferences]);

        $this->auditLogService->log(
            $workspace,
            $request->user(),
            'preferences_updated',
            Workspace::class,
            $workspace->id,
            'Workspace preferences updated',
            ['keys' => array_keys($request->validated())]
        );

        return ApiResponse::success(data: $workspace->preferences);
    }
}
