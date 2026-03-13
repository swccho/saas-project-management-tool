<?php

namespace Tests\Feature\TaskComment;

use App\Models\TaskComment;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateTaskCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_create_comment_on_task(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project, 'board' => $board, 'tasks' => $tasks] = $this->createBoardWithColumnsAndTasks($workspace, $user);
        $task = $tasks->first();

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards/{$board->id}/tasks/{$task->id}/comments",
            ['body' => 'This is a comment']
        );

        $response->assertStatus(201)
            ->assertJsonPath('data.body', 'This is a comment');

        $this->assertDatabaseHas('task_comments', [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'body' => 'This is a comment',
        ]);
    }

    public function test_create_comment_requires_body(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project, 'board' => $board, 'tasks' => $tasks] = $this->createBoardWithColumnsAndTasks($workspace, $user);
        $task = $tasks->first();

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards/{$board->id}/tasks/{$task->id}/comments",
            []
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['body']);
    }

    public function test_user_cannot_comment_on_task_in_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $resultB = $this->createBoardWithColumnsAndTasks(Workspace::factory()->create());
        $taskB = $resultB['tasks']->first();
        $projectB = $resultB['project'];
        $boardB = $resultB['board'];
        $workspaceB = $projectB->workspace;

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/workspaces/{$workspaceB->id}/projects/{$projectB->id}/boards/{$boardB->id}/tasks/{$taskB->id}/comments",
            ['body' => 'Comment from other workspace']
        );

        $response->assertStatus(403);
    }
}
