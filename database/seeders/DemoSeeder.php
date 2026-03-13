<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Label;
use App\Models\Project;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\TaskComment;
use App\Models\User;
use App\Models\UserFavoriteProject;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Models\WorkspaceAuditLog;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $users = $this->createUsers();
        $workspaces = $this->createWorkspaces($users);
        $this->createProjectsAndTasks($users, $workspaces);
    }

    private function createUsers(): array
    {
        $users = [];
        $names = [
            ['Alex Chen', 'alex@acme.io'],
            ['Jordan Smith', 'jordan@acme.io'],
            ['Sam Wilson', 'sam@acme.io'],
            ['Taylor Brown', 'taylor@startup.co'],
            ['Casey Davis', 'casey@startup.co'],
        ];

        foreach ($names as [$name, $email]) {
            $users[] = User::factory()->create([
                'name' => $name,
                'email' => $email,
            ]);
        }

        return $users;
    }

    private function createWorkspaces(array $users): array
    {
        $workspaces = [];

        $acme = Workspace::factory()->create([
            'name' => 'Acme Inc',
            'slug' => 'acme-inc',
            'owner_id' => $users[0]->id,
        ]);
        WorkspaceMember::create([
            'workspace_id' => $acme->id,
            'user_id' => $users[0]->id,
            'role' => 'owner',
        ]);
        WorkspaceMember::create([
            'workspace_id' => $acme->id,
            'user_id' => $users[1]->id,
            'role' => 'admin',
        ]);
        WorkspaceMember::create([
            'workspace_id' => $acme->id,
            'user_id' => $users[2]->id,
            'role' => 'member',
        ]);
        $workspaces[] = $acme;

        $startup = Workspace::factory()->create([
            'name' => 'Startup Co',
            'slug' => 'startup-co',
            'owner_id' => $users[3]->id,
        ]);
        WorkspaceMember::create([
            'workspace_id' => $startup->id,
            'user_id' => $users[3]->id,
            'role' => 'owner',
        ]);
        WorkspaceMember::create([
            'workspace_id' => $startup->id,
            'user_id' => $users[4]->id,
            'role' => 'member',
        ]);
        $workspaces[] = $startup;

        return $workspaces;
    }

    private function createProjectsAndTasks(array $users, array $workspaces): void
    {
        $projectNames = [
            'Website Redesign',
            'Mobile App v2',
            'Marketing Campaign',
            'API Integration',
            'Customer Portal',
            'Internal Tools',
        ];

        $columnNames = ['Backlog', 'To Do', 'In Progress', 'Review', 'Done'];
        $labels = ['Bug', 'Feature', 'Enhancement', 'Documentation', 'Urgent'];

        foreach ($workspaces as $wi => $workspace) {
            $workspaceUsers = WorkspaceMember::where('workspace_id', $workspace->id)->pluck('user_id')->all();
            $memberUsers = User::whereIn('id', $workspaceUsers)->get();

            for ($pi = 0; $pi < 3; $pi++) {
                $creator = $memberUsers->random();
                $project = Project::factory()->for($workspace)->create([
                    'name' => $projectNames[($wi * 3 + $pi) % count($projectNames)],
                    'slug' => Str::slug($projectNames[($wi * 3 + $pi) % count($projectNames)]).'-'.uniqid(),
                    'key' => 'PROJ'.(($wi * 3 + $pi) + 1),
                    'created_by' => $creator->id,
                ]);

                \App\Models\ProjectMember::create([
                    'project_id' => $project->id,
                    'user_id' => $creator->id,
                    'role' => 'project_admin',
                ]);

                $board = Board::factory()->for($project)->create([
                    'name' => 'Main Board',
                    'is_default' => true,
                    'created_by' => $creator->id,
                ]);

                $columns = [];
                foreach ($columnNames as $ci => $name) {
                    $columns[] = BoardColumn::factory()->for($board)->create([
                        'name' => $name,
                        'sort_order' => $ci,
                    ]);
                }

                $projectLabels = [];
                foreach (array_slice($labels, 0, 3) as $labelName) {
                    $projectLabels[] = Label::factory()->for($project)->create(['name' => $labelName]);
                }

                $taskTitles = [
                    'Implement user authentication',
                    'Design landing page',
                    'Set up CI/CD pipeline',
                    'Write API documentation',
                    'Fix login redirect bug',
                    'Add dark mode support',
                    'Optimize database queries',
                    'Create admin dashboard',
                ];

                $taskNumber = 0;
                foreach ($columns as $colIndex => $column) {
                    $tasksInColumn = $colIndex === 0 ? 2 : ($colIndex < 4 ? 3 : 2);
                    for ($ti = 0; $ti < $tasksInColumn; $ti++) {
                        $taskNumber++;
                        $assignee = $memberUsers->random();
                        $task = Task::create([
                            'workspace_id' => $workspace->id,
                            'project_id' => $project->id,
                            'board_id' => $board->id,
                            'column_id' => $column->id,
                            'title' => $taskTitles[$taskNumber % count($taskTitles)],
                            'task_number' => $taskNumber,
                            'created_by' => $creator->id,
                            'assigned_to' => $assignee->id,
                            'priority' => ['low', 'medium', 'high', 'urgent'][$taskNumber % 4],
                            'due_date' => $taskNumber % 3 === 0 ? now()->subDays(1) : now()->addDays(rand(1, 14)),
                            'sort_order' => $ti,
                        ]);

                        if ($projectLabels && $taskNumber % 2 === 0) {
                            $task->labels()->attach($projectLabels[array_rand($projectLabels)]->id);
                        }

                        if ($taskNumber % 4 === 0) {
                            Subtask::factory()->for($task)->count(2)->create(['created_by' => $creator->id]);
                        }

                        TaskComment::factory()->for($task)->create([
                            'user_id' => $assignee->id,
                            'body' => 'Working on this. Will update soon.',
                        ]);

                        TaskActivity::create([
                            'task_id' => $task->id,
                            'user_id' => $creator->id,
                            'type' => 'created',
                            'message' => 'Task created',
                            'meta' => [],
                            'created_at' => now(),
                        ]);

                        Notification::factory()->for($assignee)->create([
                            'type' => 'task_assigned',
                            'title' => "You were assigned to {$task->title}",
                            'body' => "{$creator->name} assigned you to this task.",
                            'data' => ['task_id' => $task->id, 'project_id' => $project->id],
                        ]);

                    }
                }

                if ($pi === 0) {
                    UserFavoriteProject::create([
                        'user_id' => $creator->id,
                        'project_id' => $project->id,
                    ]);
                }

                WorkspaceAuditLog::create([
                    'workspace_id' => $workspace->id,
                    'actor_user_id' => $creator->id,
                    'action_type' => 'project_created',
                    'target_type' => 'project',
                    'target_id' => $project->id,
                    'summary' => "Project {$project->name} created",
                    'meta' => [],
                    'created_at' => now(),
                ]);
            }
        }
    }
}
