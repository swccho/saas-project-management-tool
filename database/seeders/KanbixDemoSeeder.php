<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\Demo\CreateDemoWorkspaceService;
use Illuminate\Database\Seeder;

class KanbixDemoSeeder extends Seeder
{
    /**
     * @param  User|null  $user  Optional persisted user (must have id). If null or invalid, a user is resolved or created.
     */
    public function run($user = null): void
    {
        if (! $user instanceof User || empty($user->id)) {
            $user = $this->resolveOrCreateUser();
        }

        if (! $user) {
            return;
        }

        $service = app(CreateDemoWorkspaceService::class);
        $service->createForUser($user);
    }

    private function resolveOrCreateUser(): ?User
    {
        $existing = User::first();
        if ($existing && $existing->id !== null) {
            return $existing;
        }

        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@kanbix.example',
        ]);

        return $user->refresh();
    }
}
