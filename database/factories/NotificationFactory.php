<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['task_assigned', 'comment_mention', 'task_updated', 'invitation']);

        return [
            'user_id' => User::factory(),
            'type' => $type,
            'title' => fake()->sentence(3),
            'body' => fake()->optional()->sentence(),
            'data' => ['task_id' => fake()->numberBetween(1, 100)],
            'is_read' => false,
        ];
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
