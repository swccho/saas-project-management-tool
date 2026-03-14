<?php

namespace App\Services\Demo;

use App\Models\Board;
use App\Models\BoardColumn;
use App\Models\Label;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\TaskComment;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceAuditLog;
use App\Models\WorkspaceMember;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateDemoWorkspaceService
{
    private const WORKSPACE_NAME = 'Kanbix Demo';

    private const WORKSPACE_DESCRIPTION = 'A sample workspace preloaded with demo projects, boards, and tasks.';

    private const COLUMN_NAMES = ['Todo', 'In Progress', 'Review', 'Done'];

    private const LABEL_NAMES = ['Design', 'Frontend', 'Backend', 'Bug', 'Review', 'Marketing', 'Urgent', 'Content'];

    /**
     * Create or return existing Kanbix Demo workspace for the user.
     */
    public function createForUser(User $user): Workspace
    {
        if (empty($user->id)) {
            throw new \InvalidArgumentException('User must be persisted (have an id) to create a demo workspace.');
        }

        $existing = $user->workspaces()
            ->where('name', self::WORKSPACE_NAME)
            ->first();

        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($user) {
            $workspace = $this->createWorkspace($user);
            $this->createProjectsAndContent($workspace, $user);

            return $workspace->fresh(['owner']);
        });
    }

    private function createWorkspace(User $user): Workspace
    {
        $slug = $this->ensureUniqueSlug('kanbix-demo');

        $workspace = Workspace::create([
            'name' => self::WORKSPACE_NAME,
            'slug' => $slug,
            'description' => self::WORKSPACE_DESCRIPTION,
            'owner_id' => $user->id,
        ]);

        WorkspaceMember::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => WorkspaceMember::ROLE_OWNER,
        ]);

        return $workspace;
    }

    private function ensureUniqueSlug(string $base): string
    {
        $slug = $base;
        $counter = 1;
        while (Workspace::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function createProjectsAndContent(Workspace $workspace, User $user): void
    {
        $projectsConfig = $this->getProjectsConfig();

        foreach ($projectsConfig as $config) {
            $project = Project::create([
                'workspace_id' => $workspace->id,
                'name' => $config['name'],
                'slug' => Str::slug($config['name']) . '-' . uniqid(),
                'key' => $config['key'],
                'description' => $config['description'] ?? null,
                'status' => Project::STATUS_ACTIVE,
                'created_by' => $user->id,
            ]);

            ProjectMember::create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'role' => ProjectMember::ROLE_PROJECT_ADMIN,
            ]);

            $board = Board::create([
                'project_id' => $project->id,
                'name' => 'Main Board',
                'is_default' => true,
                'sort_order' => 0,
                'created_by' => $user->id,
            ]);

            $columns = [];
            foreach (self::COLUMN_NAMES as $i => $name) {
                $columns[] = BoardColumn::create([
                    'board_id' => $board->id,
                    'name' => $name,
                    'sort_order' => $i,
                ]);
            }

            $labels = [];
            foreach (self::LABEL_NAMES as $labelName) {
                $labels[$labelName] = Label::create([
                    'project_id' => $project->id,
                    'name' => $labelName,
                    'color' => $this->labelColor($labelName),
                ]);
            }

            $taskNumber = 0;
            foreach ($config['tasks'] as $columnIndex => $taskDefs) {
                $column = $columns[$columnIndex];
                foreach ($taskDefs as $sortOrder => $taskDef) {
                    $taskNumber++;
                    $task = $this->createTask(
                        $workspace,
                        $project,
                        $board,
                        $column,
                        $taskNumber,
                        $sortOrder,
                        $taskDef,
                        $user
                    );

                    if (!empty($taskDef['label_names'])) {
                        $task->labels()->attach(
                            collect($taskDef['label_names'])->map(fn (string $n) => $labels[$n]->id)->all()
                        );
                    }

                    if (!empty($taskDef['subtasks'])) {
                        foreach ($taskDef['subtasks'] as $si => $subTitle) {
                            Subtask::create([
                                'task_id' => $task->id,
                                'title' => $subTitle,
                                'is_completed' => $taskDef['subtasks_completed'][$si] ?? false,
                                'sort_order' => $si,
                                'created_by' => $user->id,
                            ]);
                        }
                    }

                    if (!empty($taskDef['comments'])) {
                        foreach ($taskDef['comments'] as $commentBody) {
                            TaskComment::create([
                                'task_id' => $task->id,
                                'user_id' => $user->id,
                                'body' => $commentBody,
                                'parent_id' => null,
                            ]);
                        }
                    }

                    TaskActivity::create([
                        'task_id' => $task->id,
                        'user_id' => $user->id,
                        'type' => 'created',
                        'message' => 'Task created',
                        'meta' => [],
                        'created_at' => now(),
                    ]);
                }
            }

            WorkspaceAuditLog::create([
                'workspace_id' => $workspace->id,
                'actor_user_id' => $user->id,
                'action_type' => 'project_created',
                'target_type' => 'project',
                'target_id' => $project->id,
                'summary' => "Project {$project->name} created",
                'meta' => [],
                'created_at' => now(),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function getProjectsConfig(): array
    {
        return [
            [
                'name' => 'Website Redesign',
                'key' => 'WEB',
                'description' => 'Design, content, and launch preparation for the new site.',
                'tasks' => [
                    0 => [ // Todo
                        [
                            'title' => 'Create new homepage wireframe',
                            'description' => 'Draft wireframe for the new homepage layout including hero, features, and CTA sections.',
                            'priority' => 'high',
                            'due_date' => now()->addDays(5),
                            'assigned' => true,
                            'label_names' => ['Design', 'Content'],
                            'subtasks' => ['Define sections', 'Sketch layout', 'Get stakeholder sign-off'],
                            'subtasks_completed' => [false, false, false],
                            'comments' => ['Let\'s finalize this before Friday.'],
                        ],
                        [
                            'title' => 'Update pricing page copy',
                            'description' => null,
                            'priority' => 'medium',
                            'due_date' => now()->addDays(7),
                            'assigned' => false,
                            'label_names' => ['Content', 'Marketing'],
                            'subtasks' => [],
                            'comments' => [],
                        ],
                    ],
                    1 => [ // In Progress
                        [
                            'title' => 'Prepare brand asset export',
                            'description' => 'Export logos, colors, and typography specs for dev handoff.',
                            'priority' => 'medium',
                            'due_date' => now(),
                            'assigned' => true,
                            'label_names' => ['Design'],
                            'subtasks' => [],
                            'comments' => ['I pushed a new update for review.'],
                        ],
                    ],
                    2 => [ // Review
                        [
                            'title' => 'Review landing page responsiveness',
                            'description' => 'Check breakpoints and touch targets on mobile and tablet.',
                            'priority' => 'high',
                            'due_date' => now()->subDays(1),
                            'assigned' => true,
                            'label_names' => ['Frontend', 'Review'],
                            'subtasks' => [],
                            'comments' => ['Please double-check the spacing on tablet.'],
                        ],
                    ],
                    3 => [ // Done
                        [
                            'title' => 'Publish final hero section design',
                            'description' => null,
                            'priority' => 'urgent',
                            'due_date' => now()->subDays(3),
                            'assigned' => true,
                            'label_names' => ['Design'],
                            'subtasks' => [],
                            'comments' => [],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Mobile App',
                'key' => 'MOB',
                'description' => 'Product development, bug fixing, and release planning.',
                'tasks' => [
                    0 => [
                        [
                            'title' => 'Implement onboarding flow',
                            'description' => 'Build the 3-step onboarding screens and skip logic.',
                            'priority' => 'high',
                            'due_date' => now()->addDays(10),
                            'assigned' => false,
                            'label_names' => ['Frontend'],
                            'subtasks' => [],
                            'comments' => [],
                        ],
                    ],
                    1 => [
                        [
                            'title' => 'Fix login validation issue',
                            'description' => 'Email validation and error messages on login screen.',
                            'priority' => 'urgent',
                            'due_date' => now()->addDays(1),
                            'assigned' => true,
                            'label_names' => ['Bug', 'Frontend'],
                            'subtasks' => ['Reproduce bug', 'Fix validation', 'Add tests'],
                            'subtasks_completed' => [true, false, false],
                            'comments' => [],
                        ],
                        [
                            'title' => 'Test push notification handling',
                            'description' => null,
                            'priority' => 'medium',
                            'due_date' => now()->addDays(3),
                            'assigned' => true,
                            'label_names' => ['Backend', 'Review'],
                            'subtasks' => [],
                            'comments' => [],
                        ],
                    ],
                    2 => [
                        [
                            'title' => 'Review task detail layout on tablet',
                            'description' => 'Ensure task detail drawer and actions work on tablet view.',
                            'priority' => 'medium',
                            'due_date' => now(),
                            'assigned' => false,
                            'label_names' => ['Frontend', 'Review'],
                            'subtasks' => [],
                            'comments' => [],
                        ],
                    ],
                    3 => [
                        [
                            'title' => 'Prepare release checklist for v1.2',
                            'description' => 'Final checklist before app store submission.',
                            'priority' => 'high',
                            'due_date' => now()->subDays(2),
                            'assigned' => true,
                            'label_names' => ['Urgent'],
                            'subtasks' => [],
                            'comments' => [],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Marketing Campaign',
                'key' => 'MKT',
                'description' => 'Content, promotions, outreach, and campaign scheduling.',
                'tasks' => [
                    0 => [
                        [
                            'title' => 'Draft launch email campaign',
                            'description' => 'Write subject lines and body copy for the product launch sequence.',
                            'priority' => 'high',
                            'due_date' => now()->addDays(4),
                            'assigned' => true,
                            'label_names' => ['Content', 'Marketing'],
                            'subtasks' => [],
                            'comments' => [],
                        ],
                        [
                            'title' => 'Design social media banners',
                            'description' => null,
                            'priority' => 'medium',
                            'due_date' => now()->addDays(6),
                            'assigned' => false,
                            'label_names' => ['Design', 'Marketing'],
                            'subtasks' => [],
                            'comments' => [],
                        ],
                    ],
                    1 => [
                        [
                            'title' => 'Review paid ads copy',
                            'description' => 'Review and approve ad copy for Google and Meta campaigns.',
                            'priority' => 'urgent',
                            'due_date' => now()->addDays(1),
                            'assigned' => true,
                            'label_names' => ['Marketing', 'Review'],
                            'subtasks' => [],
                            'comments' => [],
                        ],
                    ],
                    2 => [
                        [
                            'title' => 'Schedule product teaser posts',
                            'description' => null,
                            'priority' => 'medium',
                            'due_date' => now()->addDays(2),
                            'assigned' => false,
                            'label_names' => ['Marketing', 'Content'],
                            'subtasks' => [],
                            'comments' => [],
                        ],
                    ],
                    3 => [
                        [
                            'title' => 'Analyze campaign performance metrics',
                            'description' => 'Pull CTR, conversion, and ROI for the last campaign.',
                            'priority' => 'low',
                            'due_date' => now()->subDays(5),
                            'assigned' => true,
                            'label_names' => ['Marketing'],
                            'subtasks' => [],
                            'comments' => [],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function createTask(
        Workspace $workspace,
        Project $project,
        Board $board,
        BoardColumn $column,
        int $taskNumber,
        int $sortOrder,
        array $def,
        User $user
    ): Task {
        $dueDate = null;
        if (isset($def['due_date']) && $def['due_date'] !== null) {
            $dueDate = $def['due_date'] instanceof Carbon
                ? $def['due_date']
                : (is_string($def['due_date']) ? Carbon::parse($def['due_date']) : $def['due_date']);
        }

        return Task::create([
            'workspace_id' => $workspace->id,
            'project_id' => $project->id,
            'board_id' => $board->id,
            'column_id' => $column->id,
            'title' => $def['title'],
            'description' => $def['description'],
            'task_number' => $taskNumber,
            'created_by' => $user->id,
            'assigned_to' => $def['assigned'] ?? false ? $user->id : null,
            'priority' => $def['priority'] ?? 'medium',
            'due_date' => $dueDate,
            'sort_order' => $sortOrder,
        ]);
    }

    private function labelColor(string $name): string
    {
        $colors = [
            'Design' => '#8B5CF6',
            'Frontend' => '#6366F1',
            'Backend' => '#22C55E',
            'Bug' => '#EF4444',
            'Review' => '#F59E0B',
            'Marketing' => '#EC4899',
            'Urgent' => '#DC2626',
            'Content' => '#0EA5E9',
        ];

        return $colors[$name] ?? '#64748B';
    }
}
