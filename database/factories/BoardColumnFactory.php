<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\BoardColumn;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoardColumn>
 */
class BoardColumnFactory extends Factory
{
    protected $model = BoardColumn::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = ['Backlog', 'To Do', 'In Progress', 'Review', 'Done'];

        return [
            'board_id' => Board::factory(),
            'name' => fake()->randomElement($names),
            'color' => fake()->optional()->randomElement(['#6366F1', '#22C55E', '#F59E0B', '#EF4444']),
            'sort_order' => 0,
        ];
    }
}
