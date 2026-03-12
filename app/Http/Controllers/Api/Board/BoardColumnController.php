<?php

namespace App\Http\Controllers\Api\Board;

use App\Actions\Board\ReorderBoardColumnsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Board\ReorderBoardColumnsRequest;
use App\Http\Requests\Board\StoreBoardColumnRequest;
use App\Http\Requests\Board\UpdateBoardColumnRequest;
use App\Http\Resources\Board\BoardColumnResource;
use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Project;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class BoardColumnController extends Controller
{
    public function __construct(
        private readonly ReorderBoardColumnsAction $reorderBoardColumnsAction
    ) {}

    public function index(Workspace $workspace, Project $project, Board $board): JsonResponse
    {
        $this->authorize('view', $board);

        if ($project->workspace_id !== $workspace->id || $board->project_id !== $project->id) {
            abort(404);
        }

        $columns = $board->columns()->orderBy('sort_order')->get();

        return ApiResponse::success(data: BoardColumnResource::collection($columns));
    }

    public function store(StoreBoardColumnRequest $request, Workspace $workspace, Project $project, Board $board): JsonResponse
    {
        $this->authorize('update', $board);

        if ($project->workspace_id !== $workspace->id || $board->project_id !== $project->id) {
            abort(404);
        }

        $sortOrder = $board->columns()->max('sort_order') + 1;

        $column = BoardColumn::create([
            'board_id' => $board->id,
            'name' => $request->validated('name'),
            'color' => $request->validated('color'),
            'sort_order' => $sortOrder,
        ]);

        return ApiResponse::success(
            data: new BoardColumnResource($column),
            status: 201
        );
    }

    public function update(
        UpdateBoardColumnRequest $request,
        Workspace $workspace,
        Project $project,
        Board $board,
        BoardColumn $column
    ): JsonResponse {
        $this->authorize('update', $board);

        if ($project->workspace_id !== $workspace->id
            || $board->project_id !== $project->id
            || $column->board_id !== $board->id) {
            abort(404);
        }

        $column->update($request->validated());

        return ApiResponse::success(data: new BoardColumnResource($column->fresh()));
    }

    public function destroy(
        Workspace $workspace,
        Project $project,
        Board $board,
        BoardColumn $column
    ): JsonResponse {
        $this->authorize('update', $board);

        if ($project->workspace_id !== $workspace->id
            || $board->project_id !== $project->id
            || $column->board_id !== $board->id) {
            abort(404);
        }

        $column->delete();

        return ApiResponse::success(message: 'Column deleted.', status: 200);
    }

    public function reorder(
        ReorderBoardColumnsRequest $request,
        Workspace $workspace,
        Project $project,
        Board $board
    ): JsonResponse {
        $this->authorize('update', $board);

        if ($project->workspace_id !== $workspace->id || $board->project_id !== $project->id) {
            abort(404);
        }

        $this->reorderBoardColumnsAction->execute($board, $request->validated('column_ids'));

        $columns = $board->columns()->orderBy('sort_order')->get();

        return ApiResponse::success(data: BoardColumnResource::collection($columns));
    }
}
