<?php

namespace Tests\Feature\TaskWatcher;

use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WatchTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_watch_task(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project, 'board' => $board, 'tasks' => $tasks] = $this->createBoardWithColumnsAndTasks($workspace, $user);
        $task = $tasks->first();

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards/{$board->id}/tasks/{$task->id}/watch"
        );

        $response->assertStatus(200)
            ->assertJsonPath('data.is_watching', true);

        $this->assertDatabaseHas('task_watchers', [
            'task_id' => $task->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_member_can_unwatch_task(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project, 'board' => $board, 'tasks' => $tasks] = $this->createBoardWithColumnsAndTasks($workspace, $user);
        $task = $tasks->first();
        $task->watchers()->attach($user->id);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards/{$board->id}/tasks/{$task->id}/watch"
        );

        $response->assertStatus(200)
            ->assertJsonPath('data.is_watching', false);

        $this->assertDatabaseMissing('task_watchers', [
            'task_id' => $task->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_watch_task_in_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $resultB = $this->createBoardWithColumnsAndTasks(Workspace::factory()->create());
        $taskB = $resultB['tasks']->first();
        $projectB = $resultB['project'];
        $boardB = $resultB['board'];
        $workspaceB = $projectB->workspace;

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/workspaces/{$workspaceB->id}/projects/{$projectB->id}/boards/{$boardB->id}/tasks/{$taskB->id}/watch"
        );

        $response->assertStatus(403);
    }
}
