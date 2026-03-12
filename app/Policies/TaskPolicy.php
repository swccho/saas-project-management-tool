<?php

namespace App\Policies;

use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, mixed $task): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, mixed $task): bool
    {
        return false;
    }

    public function delete(User $user, mixed $task): bool
    {
        return false;
    }
}
