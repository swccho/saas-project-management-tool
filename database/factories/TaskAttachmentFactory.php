<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskAttachment>
 */
class TaskAttachmentFactory extends Factory
{
    protected $model = TaskAttachment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->word().'.'.fake()->fileExtension();

        return [
            'task_id' => Task::factory(),
            'uploaded_by' => User::factory(),
            'original_name' => $name,
            'stored_name' => fake()->uuid().'_'.$name,
            'mime_type' => fake()->randomElement(['image/png', 'image/jpeg', 'application/pdf']),
            'size' => fake()->numberBetween(1024, 1024 * 1024),
        ];
    }
}
