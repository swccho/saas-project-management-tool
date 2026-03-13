<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Actions\Workspace\CreateWorkspaceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\StoreWorkspaceRequest;
use App\Http\Requests\Workspace\UpdateWorkspaceRequest;
use App\Http\Resources\Workspace\WorkspaceResource;
use App\Models\Project;
use App\Models\Workspace;
use App\Services\AuditLogService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class WorkspaceController extends Controller
{
    public function __construct(
        private readonly CreateWorkspaceAction $createWorkspaceAction,
        private readonly AuditLogService $auditLogService
    ) {}

    public function index(): JsonResponse
    {
        $workspaces = auth()->user()
            ->workspaces()
            ->with('owner')
            ->orderBy('name')
            ->get();

        return ApiResponse::success(data: WorkspaceResource::collection($workspaces));
    }

    public function store(StoreWorkspaceRequest $request): JsonResponse
    {
        $workspace = $this->createWorkspaceAction->execute(
            user: $request->user(),
            name: $request->validated('name'),
        );

        return ApiResponse::success(
            data: new WorkspaceResource($workspace->load('owner')),
            status: 201
        );
    }

    public function show(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        return ApiResponse::success(data: new WorkspaceResource($workspace->load('owner')));
    }

    public function update(UpdateWorkspaceRequest $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('update', $workspace);

        $data = $request->validated();
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }
        $workspace->update($data);

        $this->auditLogService->log(
            $workspace,
            $request->user(),
            'workspace_updated',
            Workspace::class,
            $workspace->id,
            'Workspace settings updated',
            ['changes' => array_keys($data)]
        );

        return ApiResponse::success(data: new WorkspaceResource($workspace->fresh()->load('owner')));
    }

    public function destroy(Workspace $workspace): JsonResponse
    {
        $this->authorize('delete', $workspace);

        $workspace->delete();

        return ApiResponse::success(message: 'Workspace deleted.', status: 200);
    }

    public function favorites(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $projects = auth()->user()
            ->favoriteProjects()
            ->where('projects.workspace_id', $workspace->id)
            ->where('projects.status', Project::STATUS_ACTIVE)
            ->orderBy('projects.name')
            ->get(['projects.id', 'projects.name', 'projects.key', 'projects.color']);

        return ApiResponse::success(data: $projects->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'key' => $p->key,
            'color' => $p->color,
        ]));
    }
}
