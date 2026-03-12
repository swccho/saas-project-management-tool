<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Actions\Workspace\CreateWorkspaceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\StoreWorkspaceRequest;
use App\Http\Requests\Workspace\UpdateWorkspaceRequest;
use App\Http\Resources\Workspace\WorkspaceResource;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class WorkspaceController extends Controller
{
    public function __construct(
        private readonly CreateWorkspaceAction $createWorkspaceAction
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
        if (isset($data['name'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }
        $workspace->update($data);

        return ApiResponse::success(data: new WorkspaceResource($workspace->fresh()->load('owner')));
    }

    public function destroy(Workspace $workspace): JsonResponse
    {
        $this->authorize('delete', $workspace);

        $workspace->delete();

        return ApiResponse::success(message: 'Workspace deleted.', status: 200);
    }
}
