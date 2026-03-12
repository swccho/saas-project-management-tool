<?php

namespace App\Http\Controllers\Api\Project;

use App\Actions\Project\CreateProjectAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\Project\ProjectResource;
use App\Models\Project;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function __construct(
        private readonly CreateProjectAction $createProjectAction
    ) {}

    public function index(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $projects = $workspace->projects()
            ->with('creator')
            ->orderBy('name')
            ->get();

        return ApiResponse::success(data: ProjectResource::collection($projects));
    }

    public function store(StoreProjectRequest $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('create', $workspace);

        $project = $this->createProjectAction->execute(
            user: $request->user(),
            workspace: $workspace,
            data: $request->validated()
        );

        return ApiResponse::success(
            data: new ProjectResource($project->load('creator')),
            status: 201
        );
    }

    public function show(Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        if ($project->workspace_id !== $workspace->id) {
            abort(404);
        }

        return ApiResponse::success(data: new ProjectResource($project->load('creator')));
    }

    public function update(UpdateProjectRequest $request, Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        if ($project->workspace_id !== $workspace->id) {
            abort(404);
        }

        $project->update($request->validated());

        return ApiResponse::success(data: new ProjectResource($project->fresh()->load('creator')));
    }

    public function destroy(Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        if ($project->workspace_id !== $workspace->id) {
            abort(404);
        }

        $project->delete();

        return ApiResponse::success(message: 'Project deleted.', status: 200);
    }
}
