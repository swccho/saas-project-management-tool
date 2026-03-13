<?php

namespace Tests\Feature\Invitation;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InviteMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_invite_member(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        $inviteeEmail = 'newuser@example.com';

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/workspaces/{$workspace->id}/invitations", [
            'email' => $inviteeEmail,
            'role' => 'member',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.email', $inviteeEmail)
            ->assertJsonPath('data.role', 'member');

        $this->assertDatabaseHas('workspace_invitations', [
            'workspace_id' => $workspace->id,
            'email' => $inviteeEmail,
            'status' => WorkspaceInvitation::STATUS_PENDING,
        ]);
    }

    public function test_invite_requires_email_and_role(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/workspaces/{$workspace->id}/invitations", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'role']);
    }

    public function test_cannot_invite_existing_member(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        $existingMember = User::factory()->create();
        \App\Models\WorkspaceMember::create([
            'workspace_id' => $workspace->id,
            'user_id' => $existingMember->id,
            'role' => 'member',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/workspaces/{$workspace->id}/invitations", [
            'email' => $existingMember->email,
            'role' => 'member',
        ]);

        $response->assertStatus(422);
    }

    public function test_member_cannot_invite_others(): void
    {
        ['user' => $owner, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        $member = User::factory()->create();
        \App\Models\WorkspaceMember::create([
            'workspace_id' => $workspace->id,
            'user_id' => $member->id,
            'role' => 'member',
        ]);

        Sanctum::actingAs($member);

        $response = $this->postJson("/api/v1/workspaces/{$workspace->id}/invitations", [
            'email' => 'someone@example.com',
            'role' => 'member',
        ]);

        $response->assertStatus(403);
    }
}
