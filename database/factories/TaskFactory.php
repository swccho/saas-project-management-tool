<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $project = Project::factory()->withBoard()->create();
        $board = $project->boards()->first();
        $column = $board->columns()->first();

        return [
            'workspace_id' => $project->workspace_id,
            'project_id' => $project->id,
            'board_id' => $board->id,
            'column_id' => $column->id,
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'task_number' => 1,
            'created_by' => User::factory(),
            'assigned_to' => null,
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => 'todo',
            'due_date' => fake()->optional()->dateTimeBetween('now', '+30 days'),
            'sort_order' => 0,
        ];
    }

    /**
     * Configure for an existing project with board and column.
     */
    public function forColumn(BoardColumn $column): static
    {
        $board = $column->board;
        $project = $board->project;
        $taskNumber = $project->tasks()->max('task_number') + 1;

        return $this->state(fn () => [
            'workspace_id' => $project->workspace_id,
            'project_id' => $project->id,
            'board_id' => $board->id,
            'column_id' => $column->id,
            'task_number' => $taskNumber,
        ]);
    }

    public function assignedTo(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_to' => $user->id,
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => now()->subDays(1),
        ]);
    }
}
