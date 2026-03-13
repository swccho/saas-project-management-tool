<?php

namespace Tests\Feature\Task;

use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_task_from_other_workspace(): void
    {
        ['user' => $userA, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $resultB = $this->createBoardWithColumnsAndTasks(Workspace::factory()->create());
        $taskB = $resultB['tasks']->first();
        $projectB = $resultB['project'];
        $boardB = $resultB['board'];
        $workspaceB = $projectB->workspace;

        Sanctum::actingAs($userA);

        $response = $this->getJson(
            "/api/v1/workspaces/{$workspaceB->id}/projects/{$projectB->id}/boards/{$boardB->id}/tasks/{$taskB->id}"
        );

        $response->assertStatus(403);
    }

    public function test_user_cannot_list_tasks_from_board_in_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $resultB = $this->createBoardWithColumnsAndTasks(Workspace::factory()->create());
        $projectB = $resultB['project'];
        $boardB = $resultB['board'];
        $workspaceB = $projectB->workspace;

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/v1/workspaces/{$workspaceB->id}/projects/{$projectB->id}/boards/{$boardB->id}/tasks"
        );

        $response->assertStatus(403);
    }
}
