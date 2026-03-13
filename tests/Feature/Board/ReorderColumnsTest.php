<?php

namespace Tests\Feature\Board;

use App\Models\BoardColumn;
use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReorderColumnsTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_reorder_columns(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project, 'board' => $board, 'columns' => $columns] = $this->createProjectWithBoard($workspace, $user);

        $columnIds = $columns->pluck('id')->reverse()->values()->all();

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards/{$board->id}/columns/reorder",
            ['column_ids' => $columnIds]
        );

        $response->assertStatus(200);

        foreach ($columnIds as $index => $id) {
            $this->assertDatabaseHas('board_columns', [
                'id' => $id,
                'sort_order' => $index,
            ]);
        }
    }
}
