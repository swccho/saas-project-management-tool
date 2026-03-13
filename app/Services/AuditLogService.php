<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceAuditLog;

class AuditLogService
{
    public function log(
        Workspace $workspace,
        ?User $actor,
        string $actionType,
        ?string $targetType,
        ?int $targetId,
        string $summary,
        ?array $meta = null
    ): WorkspaceAuditLog {
        return WorkspaceAuditLog::create([
            'workspace_id' => $workspace->id,
            'actor_user_id' => $actor?->id,
            'action_type' => $actionType,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'summary' => $summary,
            'meta' => $meta,
            'created_at' => now(),
        ]);
    }
}
