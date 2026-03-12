<?php

namespace App\Http\Controllers\Api\Board;

use App\Actions\Board\CreateBoardAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Board\StoreBoardRequest;
use App\Http\Requests\Board\UpdateBoardRequest;
use App\Http\Resources\Board\BoardResource;
use App\Models\Board;
use App\Models\Project;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class BoardController extends Controller
{
    public function __construct(
        private readonly CreateBoardAction $createBoardAction
    ) {}

    public function index(Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        if ($project->workspace_id !== $workspace->id) {
            abort(404);
        }

        $boards = $project->boards()->with('columns')->orderBy('sort_order')->get();

        return ApiResponse::success(data: BoardResource::collection($boards));
    }

    public function store(StoreBoardRequest $request, Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('createBoard', $project);

        if ($project->workspace_id !== $workspace->id) {
            abort(404);
        }

        $board = $this->createBoardAction->execute(
            user: $request->user(),
            project: $project,
            data: $request->validated()
        );

        return ApiResponse::success(
            data: new BoardResource($board),
            status: 201
        );
    }

    public function show(Workspace $workspace, Project $project, Board $board): JsonResponse
    {
        $this->authorize('view', $board);

        if ($project->workspace_id !== $workspace->id || $board->project_id !== $project->id) {
            abort(404);
        }

        return ApiResponse::success(data: new BoardResource($board->load('columns')));
    }

    public function update(UpdateBoardRequest $request, Workspace $workspace, Project $project, Board $board): JsonResponse
    {
        $this->authorize('update', $board);

        if ($project->workspace_id !== $workspace->id || $board->project_id !== $project->id) {
            abort(404);
        }

        $board->update($request->validated());

        return ApiResponse::success(data: new BoardResource($board->fresh()->load('columns')));
    }

    public function destroy(Workspace $workspace, Project $project, Board $board): JsonResponse
    {
        $this->authorize('delete', $board);

        if ($project->workspace_id !== $workspace->id || $board->project_id !== $project->id) {
            abort(404);
        }

        $board->delete();

        return ApiResponse::success(message: 'Board deleted.', status: 200);
    }
}
