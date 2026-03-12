<?php

namespace App\Http\Controllers\Api\Board;

use App\Http\Controllers\Controller;
use App\Http\Requests\Board\StoreBoardViewRequest;
use App\Http\Requests\Board\UpdateBoardViewRequest;
use App\Http\Resources\Board\BoardViewResource;
use App\Models\Board;
use App\Models\BoardView;
use App\Models\Project;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class BoardViewController extends Controller
{
    public function index(Workspace $workspace, Project $project, Board $board): JsonResponse
    {
        $this->authorize('view', $board);

        if ($project->workspace_id !== $workspace->id || $board->project_id !== $project->id) {
            abort(404);
        }

        $views = $board->views()
            ->where(function ($q) {
                $q->where('user_id', auth()->id())->orWhereNull('user_id');
            })
            ->orderByDesc('is_pinned')
            ->orderBy('name')
            ->get();

        return ApiResponse::success(data: BoardViewResource::collection($views));
    }

    public function store(
        StoreBoardViewRequest $request,
        Workspace $workspace,
        Project $project,
        Board $board
    ): JsonResponse {
        $this->authorize('update', $board);

        if ($project->workspace_id !== $workspace->id || $board->project_id !== $project->id) {
            abort(404);
        }

        $view = BoardView::create([
            'board_id' => $board->id,
            'user_id' => auth()->id(),
            'name' => $request->validated('name'),
            'filter_config' => $request->validated('filter_config'),
            'sort_config' => $request->validated('sort_config'),
            'is_pinned' => $request->validated('is_pinned', false),
        ]);

        return ApiResponse::success(
            data: new BoardViewResource($view),
            status: 201
        );
    }

    public function update(
        UpdateBoardViewRequest $request,
        Workspace $workspace,
        Project $project,
        Board $board,
        BoardView $view
    ): JsonResponse {
        $this->authorize('update', $view);

        if ($project->workspace_id !== $workspace->id
            || $board->project_id !== $project->id
            || $view->board_id !== $board->id) {
            abort(404);
        }

        $view->update($request->validated());

        return ApiResponse::success(data: new BoardViewResource($view->fresh()));
    }

    public function destroy(
        Workspace $workspace,
        Project $project,
        Board $board,
        BoardView $view
    ): JsonResponse {
        $this->authorize('delete', $view);

        if ($project->workspace_id !== $workspace->id
            || $board->project_id !== $project->id
            || $view->board_id !== $board->id) {
            abort(404);
        }

        $view->delete();

        return ApiResponse::success(message: 'View deleted.', status: 200);
    }
}
