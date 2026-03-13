<?php

namespace Tests\Feature\TaskAttachment;

use App\Models\TaskAttachment;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteTaskAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_delete_attachment(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        ['project' => $project, 'board' => $board, 'tasks' => $tasks] = $this->createBoardWithColumnsAndTasks($workspace, $user);
        $task = $tasks->first();
        $attachment = TaskAttachment::factory()->create([
            'task_id' => $task->id,
            'uploaded_by' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/v1/workspaces/{$workspace->id}/projects/{$project->id}/boards/{$board->id}/tasks/{$task->id}/attachments/{$attachment->id}"
        );

        $response->assertStatus(200);
        $this->assertDatabaseMissing('task_attachments', ['id' => $attachment->id]);
    }

    public function test_user_cannot_delete_attachment_in_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $resultB = $this->createBoardWithColumnsAndTasks(Workspace::factory()->create());
        $taskB = $resultB['tasks']->first();
        $projectB = $resultB['project'];
        $boardB = $resultB['board'];
        $workspaceB = $projectB->workspace;
        $attachment = TaskAttachment::factory()->create([
            'task_id' => $taskB->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            "/api/v1/workspaces/{$workspaceB->id}/projects/{$projectB->id}/boards/{$boardB->id}/tasks/{$taskB->id}/attachments/{$attachment->id}"
        );

        $response->assertStatus(403);
        $this->assertDatabaseHas('task_attachments', ['id' => $attachment->id]);
    }
}
