<?php

namespace Tests\Feature\Task;

use App\Models\Task;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_delete_task(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project, 'board' => $board, 'tasks' => $tasks] = $this->createBoardWithColumnsAndTasks($workspace, $user);
        $task = $tasks->first();

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards/{$board->id}/tasks/{$task->id}"
        );

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_delete_task_in_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $resultB = $this->createBoardWithColumnsAndTasks(Workspace::factory()->create());
        $taskB = $resultB['tasks']->first();
        $projectB = $resultB['project'];
        $boardB = $resultB['board'];
        $workspaceB = $projectB->workspace;

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/v1/workspaces/{$workspaceB->id}/projects/{$projectB->id}/boards/{$boardB->id}/tasks/{$taskB->id}"
        );

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $taskB->id]);
    }
}
