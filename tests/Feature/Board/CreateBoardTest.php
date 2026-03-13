<?php

namespace Tests\Feature\Board;

use App\Models\Board;
use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_create_board(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project] = $this->createProjectWithBoard($workspace, $user);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards", [
            'name' => 'Sprint Board',
            'description' => 'Board for sprints',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Sprint Board');

        $this->assertDatabaseHas('boards', [
            'project_id' => $project->id,
            'name' => 'Sprint Board',
        ]);
    }

    public function test_create_board_requires_name(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project] = $this->createProjectWithBoard($workspace, $user);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_user_cannot_create_board_in_other_workspace_project(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $workspaceB = Workspace::factory()->create();
        $projectB = Project::factory()->for($workspaceB)->withBoard()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/workspaces/{$workspaceB->id}/projects/{$projectB->id}/boards", [
            'name' => 'Sprint Board',
        ]);

        $response->assertStatus(403);
    }
}
