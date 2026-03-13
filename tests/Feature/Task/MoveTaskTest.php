<?php

namespace Tests\Feature\Task;

use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MoveTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_move_task_between_columns(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project, 'board' => $board, 'columns' => $columns, 'tasks' => $tasks] = $this->createBoardWithColumnsAndTasks($workspace, $user);

        $task = $tasks->first();
        $targetColumn = $columns->last();

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards/{$board->id}/tasks/{$task->id}/move",
            [
                'column_id' => $targetColumn->id,
                'sort_order' => 0,
            ]
        );

        $response->assertStatus(200);

        $task->refresh();
        $this->assertEquals($targetColumn->id, $task->column_id);
    }

    public function test_move_task_validates_column_belongs_to_board(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project, 'board' => $board, 'tasks' => $tasks] = $this->createBoardWithColumnsAndTasks($workspace, $user);

        $otherProject = \App\Models\Project::factory()->for($workspace)->withBoard()->create();
        $otherBoard = $otherProject->boards()->first();
        $otherColumn = $otherBoard->columns()->first();

        $task = $tasks->first();

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards/{$board->id}/tasks/{$task->id}/move",
            [
                'column_id' => $otherColumn->id,
                'sort_order' => 0,
            ]
        );

        $response->assertStatus(422);
    }

    public function test_user_cannot_move_task_in_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $resultB = $this->createBoardWithColumnsAndTasks(Workspace::factory()->create());
        $taskB = $resultB['tasks']->first();
        $projectB = $resultB['project'];
        $boardB = $resultB['board'];
        $columnB = $resultB['columns']->last();
        $workspaceB = $projectB->workspace;

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/workspaces/{$workspaceB->id}/projects/{$projectB->id}/boards/{$boardB->id}/tasks/{$taskB->id}/move",
            [
                'column_id' => $columnB->id,
                'sort_order' => 0,
            ]
        );

        $response->assertStatus(403);
    }
}
