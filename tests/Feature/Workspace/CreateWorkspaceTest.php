<?php

namespace Tests\Feature\Workspace;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_workspace(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/workspaces', [
            'name' => 'My Workspace',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'My Workspace')
            ->assertJsonPath('data.owner_id', $user->id);

        $this->assertDatabaseHas('workspaces', [
            'name' => 'My Workspace',
            'owner_id' => $user->id,
        ]);

        $workspace = Workspace::where('name', 'My Workspace')->first();
        $this->assertDatabaseHas('workspace_members', [
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);
    }

    public function test_create_workspace_requires_name(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/workspaces', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_unauthenticated_user_cannot_create_workspace(): void
    {
        $response = $this->postJson('/api/v1/workspaces', [
            'name' => 'My Workspace',
        ]);

        $response->assertStatus(401);
    }
}
