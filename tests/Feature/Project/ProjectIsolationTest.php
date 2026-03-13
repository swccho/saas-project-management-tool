<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_projects_from_other_workspace(): void
    {
        ['user' => $userA, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $workspaceB = Workspace::factory()->create();
        $projectB = Project::factory()->for($workspaceB)->withBoard()->create();

        Sanctum::actingAs($userA);

        $response = $this->getJson("/api/v1/workspaces/{$workspaceB->id}/projects/{$projectB->id}");

        $response->assertStatus(403);
    }

    public function test_user_cannot_list_projects_from_workspace_they_are_not_member_of(): void
    {
        $user = \App\Models\User::factory()->create();
        $workspace = Workspace::factory()->create();
        Project::factory()->for($workspace)->withBoard()->count(2)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/workspaces/{$workspace->id}/projects");

        $response->assertStatus(403);
    }
}
