<?php

namespace Tests\Feature\Notification;

use App\Models\Notification;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MarkReadNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_mark_notification_as_read(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        $notification = Notification::factory()->for($user)->create(['is_read' => false]);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/notifications/{$notification->id}/read");

        $response->assertStatus(200);

        $notification->refresh();
        $this->assertTrue($notification->is_read);
    }

    public function test_user_cannot_mark_other_users_notification_as_read(): void
    {
        ['user' => $userA, 'workspace' => $workspaceA] = $this->createWorkspaceWithUser();
        $userB = User::factory()->create();
        $notification = Notification::factory()->for($userB)->create();

        Sanctum::actingAs($userA);

        $response = $this->putJson("/api/v1/notifications/{$notification->id}/read");

        $response->assertStatus(404);
    }
}
