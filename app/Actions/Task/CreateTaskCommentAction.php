<?php

namespace App\Actions\Task;

use App\Models\CommentMention;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\NotificationService;
use App\Services\TaskActivityService;
use Illuminate\Support\Facades\DB;

class CreateTaskCommentAction
{
    public function __construct(
        private readonly TaskActivityService $activityService,
        private readonly NotificationService $notificationService,
        private readonly ActivityService $activityServiceGlobal
    ) {}

    public function execute(Task $task, User $user, string $body, ?int $parentId = null): TaskComment
    {
        return DB::transaction(function () use ($task, $user, $body, $parentId) {
            $comment = TaskComment::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'body' => $body,
                'parent_id' => $parentId,
            ]);

            $mentionedUserIds = [];
            $mentionedUsers = $this->parseAndResolveMentions($body, $task);
            foreach ($mentionedUsers as $mentionedUser) {
                if ($mentionedUser->id !== $user->id) {
                    $mentionedUserIds[] = $mentionedUser->id;
                    CommentMention::create([
                        'comment_id' => $comment->id,
                        'mentioned_user_id' => $mentionedUser->id,
                    ]);
                    $this->notificationService->create(
                        $mentionedUser,
                        'comment_mention',
                        'You were mentioned in a comment',
                        "{$user->name} mentioned you in a comment on \"{$task->title}\"",
                        [
                            'task_id' => $task->id,
                            'comment_id' => $comment->id,
                            'project_id' => $task->project_id,
                            'workspace_id' => $task->workspace_id,
                        ]
                    );
                }
            }

            $watcherIds = $task->watchers()->pluck('user_id')->toArray();
            $excludeIds = array_merge([$user->id], $mentionedUserIds, [$task->assigned_to]);
            foreach ($watcherIds as $watcherId) {
                if (in_array($watcherId, $excludeIds, true)) {
                    continue;
                }
                $watcher = \App\Models\User::find($watcherId);
                if ($watcher) {
                    $this->notificationService->create(
                        $watcher,
                        'comment_on_watched_task',
                        'New comment on a task you watch',
                        "{$user->name} commented on \"{$task->title}\"",
                        [
                            'task_id' => $task->id,
                            'comment_id' => $comment->id,
                            'project_id' => $task->project_id,
                            'workspace_id' => $task->workspace_id,
                        ]
                    );
                }
            }

            $this->activityService->log($task, 'commented', $user, 'Added a comment', [
                'comment_id' => $comment->id,
            ]);

            $this->activityServiceGlobal->log($task->project, 'task_commented', $task, $user, [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'comment_id' => $comment->id,
            ]);

            return $comment;
        });
    }

    /**
     * Parse @Name or @Username from body and resolve to project/workspace members.
     *
     * @return array<int, User>
     */
    private function parseAndResolveMentions(string $body, Task $task): array
    {
        if (!preg_match_all('/@([^\s@]+(?:\s+[^\s@]+)*)/u', $body, $matches)) {
            return [];
        }

        $mentionTexts = array_unique(array_map('trim', $matches[1]));
        $project = $task->project;
        $workspace = $project->workspace;

        $memberUserIds = $project->members()->pluck('user_id')
            ->merge($workspace->members()->pluck('user_id'))
            ->unique()
            ->values();

        $memberUsers = User::whereIn('id', $memberUserIds)->get()->keyBy('id');
        $resolved = [];

        foreach ($mentionTexts as $text) {
            $found = $memberUsers->first(fn (User $u) => strcasecmp(trim($u->name), $text) === 0);
            if ($found && !isset($resolved[$found->id])) {
                $resolved[$found->id] = $found;
            }
        }

        return array_values($resolved);
    }
}
