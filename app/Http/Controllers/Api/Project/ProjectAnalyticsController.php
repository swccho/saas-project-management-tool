<?php

namespace App\Http\Controllers\Api\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Workspace;
use App\Services\AnalyticsService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectAnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analyticsService
    ) {}

    public function index(Request $request, Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        if ($project->workspace_id !== $workspace->id) {
            abort(404);
        }

        $data = $this->analyticsService->getProjectAnalytics($project, $request->user());

        return ApiResponse::success(data: $data);
    }
}
