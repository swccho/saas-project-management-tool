<?php

namespace Tests\Feature\Invitation;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RejectInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_reject_invitation_with_matching_email(): void
    {
        ['user' => $owner, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        $invitee = User::factory()->create(['email' => 'invitee@example.com']);
        $invitation = WorkspaceInvitation::factory()->for($workspace)->create([
            'email' => 'invitee@example.com',
            'invited_by' => $owner->id,
            'status' => WorkspaceInvitation::STATUS_PENDING,
        ]);

        Sanctum::actingAs($invitee);

        $response = $this->postJson("/api/v1/invitations/{$invitation->token}/reject");

        $response->assertStatus(200);

        $invitation->refresh();
        $this->assertEquals(WorkspaceInvitation::STATUS_REVOKED, $invitation->status);
    }

    public function test_user_cannot_reject_invitation_with_different_email(): void
    {
        ['user' => $owner, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        $wrongUser = User::factory()->create(['email' => 'wrong@example.com']);
        $invitation = WorkspaceInvitation::factory()->for($workspace)->create([
            'email' => 'invitee@example.com',
            'invited_by' => $owner->id,
            'status' => WorkspaceInvitation::STATUS_PENDING,
        ]);

        Sanctum::actingAs($wrongUser);

        $response = $this->postJson("/api/v1/invitations/{$invitation->token}/reject");

        $response->assertStatus(422);
    }
}
