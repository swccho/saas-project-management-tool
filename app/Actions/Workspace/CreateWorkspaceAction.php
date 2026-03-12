<?php

namespace App\Actions\Workspace;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CreateWorkspaceAction
{
    public function execute(User $user, string $name): Workspace
    {
        return DB::transaction(function () use ($user, $name) {
            $slug = Str::slug($name);
            $uniqueSlug = $this->ensureUniqueSlug($slug);

            $workspace = Workspace::create([
                'name' => $name,
                'slug' => $uniqueSlug,
                'owner_id' => $user->id,
            ]);

            WorkspaceMember::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'role' => WorkspaceMember::ROLE_OWNER,
            ]);

            return $workspace;
        });
    }

    private function ensureUniqueSlug(string $slug): string
    {
        $baseSlug = $slug;
        $counter = 1;

        while (Workspace::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
