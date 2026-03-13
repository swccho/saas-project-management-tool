<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\WorkspaceInvitation;
use App\Services\InvitationService;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly InvitationService $invitationService
    ) {}

    public function register(string $name, string $email, string $password, ?string $invitationToken = null): array
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        $workspaceId = null;
        if ($invitationToken) {
            $invitation = WorkspaceInvitation::where('token', $invitationToken)->first();
            if ($invitation && $invitation->isPending() && !$invitation->isExpired()
                && strtolower($invitation->email) === strtolower($user->email)) {
                $member = $this->invitationService->accept($invitation, $user);
                $workspaceId = $member->workspace_id;
            }
        }

        return [
            'user' => $user,
            'token' => $token,
            'workspace_id' => $workspaceId,
        ];
    }

    public function login(string $email, string $password): ?array
    {
        $user = User::where('email', $email)->first();

        if ($user === null || ! Hash::check($password, $user->password)) {
            return null;
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
