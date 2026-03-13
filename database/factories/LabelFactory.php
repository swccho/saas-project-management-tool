<?php

namespace Database\Factories;

use App\Models\Label;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Label>
 */
class LabelFactory extends Factory
{
    protected $model = Label::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->randomElement(['Bug', 'Feature', 'Enhancement', 'Documentation', 'Urgent']),
            'color' => fake()->randomElement(['#6366F1', '#22C55E', '#F59E0B', '#EF4444', '#8B5CF6']),
        ];
    }
}
