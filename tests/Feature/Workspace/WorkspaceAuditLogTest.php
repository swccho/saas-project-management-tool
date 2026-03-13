<?php

namespace Tests\Feature\Workspace;

use App\Models\Workspace;
use App\Models\WorkspaceAuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WorkspaceAuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_list_audit_logs(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        WorkspaceAuditLog::create([
            'workspace_id' => $workspace->id,
            'actor_user_id' => $user->id,
            'action_type' => 'project_created',
            'target_type' => 'project',
            'target_id' => 1,
            'summary' => 'Project created',
            'created_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/workspaces/{$workspace->id}/audit-logs");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'action_type', 'summary', 'actor', 'created_at'],
                ],
            ]);
    }

    public function test_user_cannot_list_audit_logs_for_other_workspace(): void
    {
        ['user' => $user, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $workspaceB = Workspace::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/workspaces/{$workspaceB->id}/audit-logs");

        $response->assertStatus(403);
    }
}
