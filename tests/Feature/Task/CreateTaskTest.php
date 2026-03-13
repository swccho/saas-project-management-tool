<?php

namespace Tests\Feature\Task;

use App\Models\BoardColumn;
use App\Models\Task;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_create_task(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project, 'board' => $board, 'columns' => $columns] = $this->createProjectWithBoard($workspace, $user);
        $column = $columns->first();

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards/{$board->id}/tasks",
            [
                'column_id' => $column->id,
                'title' => 'New Task',
                'description' => 'Task description',
            ]
        );

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'New Task');

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'board_id' => $board->id,
            'column_id' => $column->id,
            'title' => 'New Task',
        ]);
    }

    public function test_create_task_requires_column_id_and_title(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project, 'board' => $board] = $this->createProjectWithBoard($workspace, $user);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards/{$board->id}/tasks",
            []
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['column_id', 'title']);
    }

    public function test_user_cannot_create_task_in_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $workspaceB = Workspace::factory()->create();
        $projectB = \App\Models\Project::factory()->for($workspaceB)->withBoard()->create();
        $boardB = $projectB->boards()->first();
        $columnB = $boardB->columns()->first();

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/workspaces/{$workspaceB->id}/projects/{$projectB->id}/boards/{$boardB->id}/tasks",
            [
                'column_id' => $columnB->id,
                'title' => 'New Task',
            ]
        );

        $response->assertStatus(403);
    }
}
