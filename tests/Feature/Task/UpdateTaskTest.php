<?php

namespace Tests\Feature\Task;

use App\Models\Task;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_update_task(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project, 'board' => $board, 'tasks' => $tasks] = $this->createBoardWithColumnsAndTasks($workspace, $user);
        $task = $tasks->first();

        Sanctum::actingAs($user);

        $response = $this->putJson(
            "/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards/{$board->id}/tasks/{$task->id}",
            [
                'title' => 'Updated Task Title',
                'description' => 'Updated description',
            ]
        );

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Task Title');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
        ]);
    }

    public function test_user_cannot_update_task_in_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $resultB = $this->createBoardWithColumnsAndTasks(Workspace::factory()->create());
        $taskB = $resultB['tasks']->first();
        $projectB = $resultB['project'];
        $boardB = $resultB['board'];

        Sanctum::actingAs($user);

        $workspaceB = $projectB->workspace;

        $response = $this->putJson(
            "/api/v1/workspaces/{$workspaceB->id}/projects/{$projectB->id}/boards/{$boardB->id}/tasks/{$taskB->id}",
            ['title' => 'Hacked Title']
        );

        $response->assertStatus(403);
    }
}
