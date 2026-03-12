<?php

namespace Tests\Feature\Workspace;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListWorkspacesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_only_their_workspaces(): void
    {
        $user = User::factory()->create();
        $workspace1 = Workspace::factory()->create(['owner_id' => $user->id]);
        WorkspaceMember::factory()->create([
            'workspace_id' => $workspace1->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        $otherUser = User::factory()->create();
        $otherWorkspace = Workspace::factory()->create(['owner_id' => $otherUser->id]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/workspaces');

        $response->assertStatus(200);

        $ids = collect($response->json('data'))->pluck('id')->toArray();
        $this->assertContains($workspace1->id, $ids);
        $this->assertNotContains($otherWorkspace->id, $ids);
    }

    public function test_unauthenticated_user_cannot_list_workspaces(): void
    {
        $response = $this->getJson('/api/v1/workspaces');

        $response->assertStatus(401);
    }
}
