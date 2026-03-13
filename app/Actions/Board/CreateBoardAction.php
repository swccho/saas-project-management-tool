<?php

namespace App\Actions\Board;

use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Project;
use App\Models\User;
use App\Services\ActivityService;

class CreateBoardAction
{
    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    private const DEFAULT_COLUMNS = [
        'Backlog',
        'Todo',
        'In Progress',
        'Review',
        'Done',
    ];

    public function execute(User $user, Project $project, array $data): Board
    {
        $isFirst = $project->boards()->count() === 0;

        $board = Board::create([
            'project_id' => $project->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_default' => $isFirst,
            'sort_order' => $project->boards()->max('sort_order') + 1,
            'created_by' => $user->id,
        ]);

        foreach (self::DEFAULT_COLUMNS as $i => $name) {
            BoardColumn::create([
                'board_id' => $board->id,
                'name' => $name,
                'sort_order' => $i,
            ]);
        }

        $this->activityService->log($project, 'board_created', $board, $user, [
            'board_name' => $board->name,
            'board_id' => $board->id,
        ]);

        return $board->load('columns');
    }
}
