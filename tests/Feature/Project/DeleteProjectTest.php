<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_delete_project(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project] = $this->createProjectWithBoard($workspace, $user);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/v1/workspaces/{$workspace->id}/projects/{$project->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_user_cannot_delete_project_in_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $workspaceB = Workspace::factory()->create();
        $projectB = Project::factory()->for($workspaceB)->withBoard()->create();

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/v1/workspaces/{$workspaceB->id}/projects/{$projectB->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('projects', ['id' => $projectB->id]);
    }
}
