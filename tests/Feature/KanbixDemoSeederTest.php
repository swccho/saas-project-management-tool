<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Demo\CreateDemoWorkspaceService;
use Database\Seeders\KanbixDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class KanbixDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_kanbix_demo_seeder_creates_demo_workspace(): void
    {
        $user = User::factory()->create();

        (new KanbixDemoSeeder())->run($user);

        $workspace = Workspace::where('name', 'Kanbix Demo')->first();
        $this->assertNotNull($workspace);
        $this->assertSame('A sample workspace preloaded with demo projects, boards, and tasks.', $workspace->description);
        $this->assertSame($user->id, $workspace->owner_id);
    }

    public function test_kanbix_demo_seeder_creates_three_projects(): void
    {
        $user = User::factory()->create();

        (new KanbixDemoSeeder())->run($user);

        $workspace = Workspace::where('name', 'Kanbix Demo')->first();
        $projectNames = $workspace->projects()->pluck('name')->all();
        $this->assertContains('Website Redesign', $projectNames);
        $this->assertContains('Mobile App', $projectNames);
        $this->assertContains('Marketing Campaign', $projectNames);
        $this->assertCount(3, $projectNames);
    }

    public function test_each_project_has_board_with_columns_todo_in_progress_review_done(): void
    {
        $user = User::factory()->create();

        (new KanbixDemoSeeder())->run($user);

        $workspace = Workspace::where('name', 'Kanbix Demo')->first();
        foreach ($workspace->projects as $project) {
            $board = $project->boards()->first();
            $this->assertNotNull($board);
            $this->assertTrue($board->is_default);

            $columnNames = $board->columns()->orderBy('sort_order')->pluck('name')->all();
            $this->assertSame(['Todo', 'In Progress', 'Review', 'Done'], $columnNames);
        }
    }

    public function test_seeded_tasks_belong_to_correct_projects_and_columns(): void
    {
        $user = User::factory()->create();

        (new KanbixDemoSeeder())->run($user);

        $workspace = Workspace::where('name', 'Kanbix Demo')->first();
        $taskCount = Task::where('workspace_id', $workspace->id)->count();
        $this->assertGreaterThan(0, $taskCount);

        foreach (Task::where('workspace_id', $workspace->id)->get() as $task) {
            $this->assertSame($workspace->id, $task->workspace_id);
            $this->assertNotNull($task->project_id);
            $this->assertNotNull($task->board_id);
            $this->assertNotNull($task->column_id);
            $project = Project::find($task->project_id);
            $this->assertSame($workspace->id, $project->workspace_id);
            $column = BoardColumn::find($task->column_id);
            $this->assertSame($task->board_id, $column->board_id);
        }
    }

    public function test_create_demo_workspace_service_creates_workspace_for_user(): void
    {
        $user = User::factory()->create();
        $service = app(CreateDemoWorkspaceService::class);

        $workspace = $service->createForUser($user);

        $this->assertSame('Kanbix Demo', $workspace->name);
        $this->assertSame($user->id, $workspace->owner_id);
        $this->assertTrue($user->workspaces()->where('workspaces.id', $workspace->id)->exists());
    }

    public function test_create_demo_workspace_service_returns_existing_when_called_twice(): void
    {
        $user = User::factory()->create();
        $service = app(CreateDemoWorkspaceService::class);

        $workspace1 = $service->createForUser($user);
        $workspace2 = $service->createForUser($user);

        $this->assertSame($workspace1->id, $workspace2->id);
        $this->assertSame(1, Workspace::where('name', 'Kanbix Demo')->where('owner_id', $user->id)->count());
    }

    public function test_post_workspaces_demo_creates_demo_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/workspaces/demo');

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Kanbix Demo')
            ->assertJsonPath('data.owner_id', $user->id);

        $this->assertDatabaseHas('workspaces', [
            'name' => 'Kanbix Demo',
            'owner_id' => $user->id,
        ]);
    }

    public function test_post_workspaces_demo_returns_existing_demo_workspace_on_second_call(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response1 = $this->postJson('/api/v1/workspaces/demo');
        $response2 = $this->postJson('/api/v1/workspaces/demo');

        $response1->assertStatus(201);
        $response2->assertStatus(200);
        $this->assertSame($response1->json('data.id'), $response2->json('data.id'));
    }

    public function test_post_workspaces_demo_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/workspaces/demo');

        $response->assertStatus(401);
    }
}
