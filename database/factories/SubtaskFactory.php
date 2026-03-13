<?php

namespace Database\Factories;

use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subtask>
 */
class SubtaskFactory extends Factory
{
    protected $model = Subtask::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'title' => fake()->sentence(3),
            'is_completed' => fake()->boolean(20),
            'sort_order' => 0,
            'created_by' => User::factory(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
        ]);
    }
}
