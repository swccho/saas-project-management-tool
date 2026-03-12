<?php

namespace Tests\Feature\Workspace;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WorkspaceIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_workspace_they_are_not_member_of(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $otherUser->id]);
        WorkspaceMember::factory()->create([
            'workspace_id' => $workspace->id,
            'user_id' => $otherUser->id,
            'role' => 'owner',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/workspaces/{$workspace->id}");

        $response->assertStatus(403);
    }

    public function test_user_cannot_update_workspace_they_are_not_admin_of(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $otherUser->id]);
        WorkspaceMember::factory()->create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => 'member',
        ]);
        WorkspaceMember::factory()->create([
            'workspace_id' => $workspace->id,
            'user_id' => $otherUser->id,
            'role' => 'owner',
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/workspaces/{$workspace->id}", [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_workspace_they_do_not_own(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
        WorkspaceMember::factory()->create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => 'admin',
        ]);
        WorkspaceMember::factory()->create([
            'workspace_id' => $workspace->id,
            'user_id' => $owner->id,
            'role' => 'owner',
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/v1/workspaces/{$workspace->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('workspaces', ['id' => $workspace->id]);
    }
}
