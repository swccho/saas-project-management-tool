<?php

namespace App\Http\Controllers\Api\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectFavoriteController extends Controller
{
    public function store(Request $request, Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        if ($project->workspace_id !== $workspace->id) {
            abort(404);
        }

        $request->user()->favoriteProjects()->syncWithoutDetaching([$project->id]);

        return ApiResponse::success(data: ['is_favorite' => true]);
    }

    public function destroy(Request $request, Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        if ($project->workspace_id !== $workspace->id) {
            abort(404);
        }

        $request->user()->favoriteProjects()->detach($project->id);

        return ApiResponse::success(data: ['is_favorite' => false]);
    }
}
