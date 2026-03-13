<?php

namespace Tests\Feature\Notification;

use App\Models\Notification;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_own_notifications(): void
    {
        ['user' => $user, 'workspace' => $workspace] = $this->createWorkspaceWithUser();
        Notification::factory()->for($user)->count(3)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/notifications');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data.data');
    }

    public function test_unauthenticated_user_cannot_list_notifications(): void
    {
        $response = $this->getJson('/api/v1/notifications');

        $response->assertStatus(401);
    }
}
