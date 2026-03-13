<?php

namespace App\Actions\Workspace;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;

class TransferWorkspaceOwnershipAction
{
    public function execute(Workspace $workspace, User $currentOwner, int $newOwnerUserId): WorkspaceMember
    {
        if ($workspace->owner_id !== $currentOwner->id) {
            throw new \InvalidArgumentException('Only the current owner can transfer ownership.');
        }

        $newOwnerMember = $workspace->members()->where('user_id', $newOwnerUserId)->first();
        if (!$newOwnerMember) {
            throw new \InvalidArgumentException('The new owner must be a workspace member.');
        }

        if ($newOwnerMember->user_id === $currentOwner->id) {
            throw new \InvalidArgumentException('You already own this workspace.');
        }

        $workspace->update(['owner_id' => $newOwnerUserId]);
        $newOwnerMember->update(['role' => WorkspaceMember::ROLE_OWNER]);

        $currentOwnerMember = $workspace->members()->where('user_id', $currentOwner->id)->first();
        if ($currentOwnerMember) {
            $currentOwnerMember->update(['role' => WorkspaceMember::ROLE_ADMIN]);
        }

        return $newOwnerMember->fresh();
    }
}
