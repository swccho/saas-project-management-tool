<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_member_can_create_project(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/workspaces/{$workspace->id}/projects", [
            'name' => 'New Project',
            'description' => 'Project description',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'New Project')
            ->assertJsonPath('data.description', 'Project description');

        $this->assertDatabaseHas('projects', [
            'workspace_id' => $workspace->id,
            'name' => 'New Project',
        ]);
    }

    public function test_create_project_requires_name(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/workspaces/{$workspace->id}/projects", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_unauthenticated_user_cannot_create_project(): void
    {
        $workspace = Workspace::factory()->create();

        $response = $this->postJson("/api/v1/workspaces/{$workspace->id}/projects", [
            'name' => 'New Project',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_cannot_create_project_in_workspace_they_are_not_member_of(): void
    {
        $user = \App\Models\User::factory()->create();
        $workspace = Workspace::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/workspaces/{$workspace->id}/projects", [
            'name' => 'New Project',
        ]);

        $response->assertStatus(403);
    }
}
