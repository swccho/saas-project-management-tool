<?php

namespace App\Policies;

use App\Models\User;

class BoardPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, mixed $board): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, mixed $board): bool
    {
        return false;
    }

    public function delete(User $user, mixed $board): bool
    {
        return false;
    }
}
