<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use App\Http\Resources\Workspace\WorkspaceResource;
use App\Services\Demo\CreateDemoWorkspaceService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class CreateDemoWorkspaceController extends Controller
{
    public function __construct(
        private readonly CreateDemoWorkspaceService $createDemoWorkspaceService
    ) {}

    /**
     * Create or return existing Kanbix Demo workspace for the authenticated user.
     */
    public function store(): JsonResponse
    {
        $workspace = $this->createDemoWorkspaceService->createForUser(
            auth()->user()
        );

        return ApiResponse::success(
            data: new WorkspaceResource($workspace->load('owner')),
            status: $workspace->wasRecentlyCreated ? 201 : 200
        );
    }
}
