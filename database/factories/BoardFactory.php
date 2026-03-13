<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Board>
 */
class BoardFactory extends Factory
{
    protected $model = Board::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->randomElement(['Main Board', 'Sprint Board', 'Backlog', 'Development']),
            'description' => fake()->optional()->sentence(),
            'is_default' => true,
            'sort_order' => 0,
            'created_by' => User::factory(),
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
