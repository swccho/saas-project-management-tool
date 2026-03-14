<?php

namespace App\Http\Resources\Task;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'workspace_id' => $this->workspace_id,
            'project_id' => $this->project_id,
            'board_id' => $this->board_id,
            'column_id' => $this->column_id,
            'title' => $this->title,
            'description' => $this->description,
            'task_number' => $this->task_number,
            'created_by' => $this->created_by,
            'assigned_to' => $this->assigned_to,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->due_date?->toDateString(),
            'sort_order' => $this->sort_order,
            'assignee' => $this->whenLoaded('assignee', fn () => [
                'id' => $this->assignee->id,
                'name' => $this->assignee->name,
                'email' => $this->assignee->email,
                'avatar_url' => $this->assignee->avatar
                    ? app(\App\Services\ProfileService::class)->getAvatarUrl($this->assignee)
                    : null,
            ]),
            'labels' => $this->whenLoaded('labels', fn () => $this->labels->map(fn ($l) => [
                'id' => $l->id,
                'name' => $l->name,
                'color' => $l->color,
            ])),
            'subtasks' => $this->whenLoaded('subtasks', fn () => $this->subtasks->map(fn ($s) => [
                'id' => $s->id,
                'title' => $s->title,
                'is_completed' => $s->is_completed,
                'sort_order' => $s->sort_order,
            ])),
            'is_watching' => $this->when(isset($this->is_watching), (bool) $this->is_watching),
            'watchers_count' => $this->when(isset($this->watchers_count), (int) $this->watchers_count),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
