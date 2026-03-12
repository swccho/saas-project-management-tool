<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkspaceMember>
 */
class WorkspaceMemberFactory extends Factory
{
    protected $model = WorkspaceMember::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'user_id' => User::factory(),
            'role' => WorkspaceMember::ROLE_MEMBER,
        ];
    }

    public function owner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => WorkspaceMember::ROLE_OWNER,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => WorkspaceMember::ROLE_ADMIN,
        ]);
    }
}
