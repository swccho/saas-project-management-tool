<?php

namespace Tests\Helpers;

use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Database\Eloquent\Collection;

trait TestHelpers
{
    /**
     * Create a workspace with the given user as owner.
     */
    protected function createWorkspaceWithUser(?User $user = null, array $workspaceAttrs = []): array
    {
        $user = $user ?? User::factory()->create();
        $workspace = Workspace::factory()->create(array_merge(['owner_id' => $user->id], $workspaceAttrs));

        WorkspaceMember::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => WorkspaceMember::ROLE_OWNER,
        ]);

        return ['user' => $user, 'workspace' => $workspace];
    }

    /**
     * Create a project with a default board and columns.
     */
    protected function createProjectWithBoard(Workspace $workspace, ?User $creator = null, array $projectAttrs = []): array
    {
        $creator = $creator ?? $workspace->owner;
        $project = Project::factory()
            ->for($workspace)
            ->withBoard()
            ->create(array_merge(['created_by' => $creator->id], $projectAttrs));

        $board = $project->boards()->first();
        $columns = $board->columns()->orderBy('sort_order')->get();

        return [
            'project' => $project,
            'board' => $board,
            'columns' => $columns,
            'creator' => $creator,
        ];
    }

    /**
     * Create a board with columns and tasks.
     *
     * @param  int  $tasksPerColumn
     * @return array{project: Project, board: Board, columns: Collection, tasks: Collection}
     */
    protected function createBoardWithColumnsAndTasks(
        Workspace $workspace,
        ?User $creator = null,
        int $tasksPerColumn = 2
    ): array {
        $result = $this->createProjectWithBoard($workspace, $creator);
        $project = $result['project'];
        $board = $result['board'];
        $columns = $result['columns'];
        $tasks = collect();

        foreach ($columns as $index => $column) {
            for ($i = 0; $i < $tasksPerColumn; $i++) {
                $task = Task::factory()
                    ->forColumn($column)
                    ->create([
                        'created_by' => $result['creator']->id,
                        'sort_order' => $i,
                    ]);
                $tasks->push($task);
            }
        }

        return [
            'project' => $project,
            'board' => $board,
            'columns' => $columns,
            'tasks' => $tasks,
            'creator' => $result['creator'],
        ];
    }
}
