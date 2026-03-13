<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'workspace_id' => Workspace::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'key' => 'PROJ'.fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->optional()->sentence(),
            'color' => fake()->optional()->randomElement(['#6366F1', '#22C55E', '#F59E0B', '#EF4444', '#8B5CF6']),
            'status' => Project::STATUS_ACTIVE,
            'created_by' => User::factory(),
        ];
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Project::STATUS_ARCHIVED,
        ]);
    }

    /**
     * Create a project with a default board and columns.
     */
    public function withBoard(): static
    {
        return $this->afterCreating(function (Project $project) {
            if ($project->boards()->count() === 0) {
                $board = Board::factory()->for($project)->create(['is_default' => true]);
                $columns = ['Backlog', 'To Do', 'In Progress', 'Done'];
                foreach ($columns as $i => $name) {
                    BoardColumn::factory()->for($board)->create([
                        'name' => $name,
                        'sort_order' => $i,
                    ]);
                }
            }
        });
    }
}
