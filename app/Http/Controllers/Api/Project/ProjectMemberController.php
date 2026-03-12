<?php

namespace App\Http\Controllers\Api\Project;

use App\Actions\Project\AddProjectMemberAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectMemberRequest;
use App\Http\Requests\Project\UpdateProjectMemberRequest;
use App\Http\Resources\Project\ProjectMemberResource;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ProjectMemberController extends Controller
{
    public function __construct(
        private readonly AddProjectMemberAction $addProjectMemberAction
    ) {}

    public function index(Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        if ($project->workspace_id !== $workspace->id) {
            abort(404);
        }

        $members = $project->members()->with('user')->orderBy('created_at')->get();

        return ApiResponse::success(data: ProjectMemberResource::collection($members));
    }

    public function store(StoreProjectMemberRequest $request, Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('manageMembers', $project);

        if ($project->workspace_id !== $workspace->id) {
            abort(404);
        }

        $user = \App\Models\User::findOrFail($request->validated('user_id'));
        $member = $this->addProjectMemberAction->execute(
            project: $project,
            user: $user,
            role: $request->validated('role')
        );

        return ApiResponse::success(
            data: new ProjectMemberResource($member->load('user')),
            status: 201
        );
    }

    public function update(
        UpdateProjectMemberRequest $request,
        Workspace $workspace,
        Project $project,
        ProjectMember $member
    ): JsonResponse {
        $this->authorize('update', [$member, $project]);

        if ($project->workspace_id !== $workspace->id || $member->project_id !== $project->id) {
            abort(404);
        }

        $member->update($request->validated());

        return ApiResponse::success(data: new ProjectMemberResource($member->fresh()->load('user')));
    }

    public function destroy(Workspace $workspace, Project $project, ProjectMember $member): JsonResponse
    {
        $this->authorize('manageMembers', $project);

        if ($project->workspace_id !== $workspace->id || $member->project_id !== $project->id) {
            abort(404);
        }

        $member->delete();

        return ApiResponse::success(message: 'Member removed.', status: 200);
    }
}
