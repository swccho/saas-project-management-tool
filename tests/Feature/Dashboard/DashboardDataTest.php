<?php

namespace Tests\Feature\Dashboard;

use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_get_dashboard_data(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        $this->createProjectWithBoard($workspace, $user);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/workspaces/{$workspace->id}/dashboard");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'projects',
                    'my_tasks',
                    'overdue_count',
                    'recent_activity',
                ],
            ]);
    }

    public function test_user_cannot_get_dashboard_for_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $workspaceB = Workspace::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/workspaces/{$workspaceB->id}/dashboard");

        $response->assertStatus(403);
    }
}
