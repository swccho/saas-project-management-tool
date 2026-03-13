<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskActivity>
 */
class TaskActivityFactory extends Factory
{
    protected $model = TaskActivity::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['created', 'moved', 'updated', 'commented']),
            'message' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
