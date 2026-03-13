<?php

namespace Tests\Feature\Calendar;

use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CalendarTasksTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_get_calendar_tasks(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        $this->createBoardWithColumnsAndTasks($workspace, $user);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/workspaces/{$workspace->id}/calendar?start=2025-03-01&end=2025-03-31");

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_user_cannot_get_calendar_for_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $workspaceB = Workspace::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/workspaces/{$workspaceB->id}/calendar?start=2025-03-01&end=2025-03-31");

        $response->assertStatus(403);
    }
}
