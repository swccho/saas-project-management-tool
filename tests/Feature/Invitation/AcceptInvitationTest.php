<?php

namespace Tests\Feature\Invitation;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AcceptInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_accept_invitation_with_matching_email(): void
    {
        ['user' => $owner, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        $invitee = User::factory()->create(['email' => 'invitee@example.com']);
        $invitation = WorkspaceInvitation::factory()->for($workspace)->create([
            'email' => 'invitee@example.com',
            'invited_by' => $owner->id,
            'status' => WorkspaceInvitation::STATUS_PENDING,
        ]);

        Sanctum::actingAs($invitee);

        $response = $this->postJson("/api/v1/invitations/{$invitation->token}/accept");

        $response->assertStatus(200)
            ->assertJsonPath('data.workspace_id', $workspace->id);

        $this->assertDatabaseHas('workspace_members', [
            'workspace_id' => $workspace->id,
            'user_id' => $invitee->id,
        ]);

        $invitation->refresh();
        $this->assertEquals(WorkspaceInvitation::STATUS_ACCEPTED, $invitation->status);
    }

    public function test_user_cannot_accept_invitation_with_different_email(): void
    {
        ['user' => $owner, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        $wrongUser = User::factory()->create(['email' => 'wrong@example.com']);
        $invitation = WorkspaceInvitation::factory()->for($workspace)->create([
            'email' => 'invitee@example.com',
            'invited_by' => $owner->id,
            'status' => WorkspaceInvitation::STATUS_PENDING,
        ]);

        Sanctum::actingAs($wrongUser);

        $response = $this->postJson("/api/v1/invitations/{$invitation->token}/accept");

        $response->assertStatus(422);
    }

    public function test_invalid_token_returns_404(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/invitations/invalid-token-123/accept');

        $response->assertStatus(404);
    }
}
