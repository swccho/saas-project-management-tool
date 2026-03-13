<?php

namespace Tests\Feature\MyTasks;

use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MyTasksListTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_list_my_tasks(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        $this->createBoardWithColumnsAndTasks($workspace, $user);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/workspaces/{$workspace->id}/my-tasks");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'assigned',
                    'due_today',
                    'due_week',
                ],
            ]);
    }

    public function test_user_cannot_list_my_tasks_for_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $workspaceB = Workspace::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/workspaces/{$workspaceB->id}/my-tasks");

        $response->assertStatus(403);
    }
}
