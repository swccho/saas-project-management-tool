<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_update_project(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project] = $this->createProjectWithBoard($workspace, $user);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/workspaces/{$workspace->id}/projects/{$project->id}", [
            'name' => 'Updated Project Name',
            'description' => 'Updated description',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Project Name');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name',
        ]);
    }

    public function test_user_cannot_update_project_in_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $workspaceB = Workspace::factory()->create();
        $projectB = Project::factory()->for($workspaceB)->withBoard()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/workspaces/{$workspaceB->id}/projects/{$projectB->id}", [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
    }
}
