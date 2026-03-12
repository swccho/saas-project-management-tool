<?php

namespace App\Http\Controllers\Api\Label;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Project;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function index(Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        if ($project->workspace_id !== $workspace->id) {
            abort(404);
        }

        $labels = $project->labels()->orderBy('name')->get();

        return ApiResponse::success(data: $labels->map(fn ($l) => [
            'id' => $l->id,
            'name' => $l->name,
            'color' => $l->color,
        ]));
    }

    public function store(Request $request, Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        if ($project->workspace_id !== $workspace->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $label = Label::create([
            'project_id' => $project->id,
            'name' => $validated['name'],
            'color' => $validated['color'] ?? '#6366F1',
        ]);

        return ApiResponse::success(data: [
            'id' => $label->id,
            'name' => $label->name,
            'color' => $label->color,
        ], status: 201);
    }

    public function update(Request $request, Workspace $workspace, Project $project, Label $label): JsonResponse
    {
        $this->authorize('view', $project);

        if ($project->workspace_id !== $workspace->id || $label->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $label->update($validated);

        return ApiResponse::success(data: [
            'id' => $label->id,
            'name' => $label->name,
            'color' => $label->color,
        ]);
    }

    public function destroy(Workspace $workspace, Project $project, Label $label): JsonResponse
    {
        $this->authorize('view', $project);

        if ($project->workspace_id !== $workspace->id || $label->project_id !== $project->id) {
            abort(404);
        }

        $label->delete();

        return ApiResponse::success(message: 'Label deleted.', status: 200);
    }
}
