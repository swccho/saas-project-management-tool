<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    public function view(User $user, Workspace $workspace): bool
    {
        return $this->isMember($user, $workspace);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Workspace $workspace): bool
    {
        return $this->isOwnerOrAdmin($user, $workspace);
    }

    public function delete(User $user, Workspace $workspace): bool
    {
        return $workspace->owner_id === $user->id;
    }

    public function manageMembers(User $user, Workspace $workspace): bool
    {
        return $this->isOwnerOrAdmin($user, $workspace);
    }

    private function isMember(User $user, Workspace $workspace): bool
    {
        return $workspace->members()->where('user_id', $user->id)->exists();
    }

    private function isOwnerOrAdmin(User $user, Workspace $workspace): bool
    {
        $member = $workspace->members()->where('user_id', $user->id)->first();

        return $member !== null && in_array($member->role, ['owner', 'admin'], true);
    }
}
