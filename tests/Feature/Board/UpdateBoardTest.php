<?php

namespace Tests\Feature\Board;

use App\Models\Board;
use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_update_board(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project, 'board' => $board] = $this->createProjectWithBoard($workspace, $user);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards/{$board->id}", [
            'name' => 'Updated Board Name',
            'description' => 'Updated description',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Board Name');

        $this->assertDatabaseHas('boards', [
            'id' => $board->id,
            'name' => 'Updated Board Name',
        ]);
    }

    public function test_user_cannot_update_board_in_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $workspaceB = Workspace::factory()->create();
        $projectB = Project::factory()->for($workspaceB)->withBoard()->create();
        $boardB = $projectB->boards()->first();

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/workspaces/{$workspaceB->id}/projects/{$projectB->id}/boards/{$boardB->id}", [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
    }
}
