<?php

namespace App\Services;

use App\Mail\WorkspaceInvitationMail;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use App\Models\WorkspaceMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class InvitationService
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {}

    public function create(Workspace $workspace, User $inviter, string $email, string $role): WorkspaceInvitation
    {
        $existing = $workspace->members()->whereHas('user', fn ($q) => $q->where('email', $email))->exists();
        if ($existing) {
            throw new \InvalidArgumentException('User is already a member of this workspace.');
        }

        $pending = WorkspaceInvitation::where('workspace_id', $workspace->id)
            ->where('email', $email)
            ->where('status', WorkspaceInvitation::STATUS_PENDING)
            ->where('expires_at', '>', now())
            ->first();
        if ($pending) {
            throw new \InvalidArgumentException('An invitation is already pending for this email.');
        }

        $invitation = WorkspaceInvitation::create([
            'workspace_id' => $workspace->id,
            'email' => $email,
            'role' => $role,
            'invited_by' => $inviter->id,
            'token' => WorkspaceInvitation::generateToken(),
            'status' => WorkspaceInvitation::STATUS_PENDING,
            'expires_at' => Carbon::now()->addDays(7),
        ]);

        $this->sendInvitationEmail($invitation, $workspace, $inviter);

        return $invitation;
    }

    public function resend(WorkspaceInvitation $invitation): WorkspaceInvitation
    {
        if (!$invitation->isPending()) {
            throw new \InvalidArgumentException('Cannot resend a non-pending invitation.');
        }

        $invitation->update([
            'token' => WorkspaceInvitation::generateToken(),
            'expires_at' => Carbon::now()->addDays(7),
        ]);

        $invitation = $invitation->fresh()->load('workspace', 'inviter');
        $this->sendInvitationEmail($invitation, $invitation->workspace, $invitation->inviter);

        return $invitation;
    }

    public function revoke(WorkspaceInvitation $invitation): void
    {
        $invitation->update(['status' => WorkspaceInvitation::STATUS_REVOKED]);
    }

    public function accept(WorkspaceInvitation $invitation, User $user): WorkspaceMember
    {
        if (!$invitation->isPending()) {
            throw new \InvalidArgumentException('Invitation is no longer valid.');
        }

        if ($invitation->isExpired()) {
            $invitation->update(['status' => WorkspaceInvitation::STATUS_EXPIRED]);
            throw new \InvalidArgumentException('Invitation has expired.');
        }

        if (strtolower($invitation->email) !== strtolower($user->email)) {
            throw new \InvalidArgumentException('This invitation was sent to a different email address.');
        }

        $existing = $invitation->workspace->members()->where('user_id', $user->id)->exists();
        if ($existing) {
            $invitation->update(['status' => WorkspaceInvitation::STATUS_ACCEPTED]);
            return $invitation->workspace->members()->where('user_id', $user->id)->first();
        }

        $member = WorkspaceMember::create([
            'workspace_id' => $invitation->workspace_id,
            'user_id' => $user->id,
            'role' => $invitation->role,
        ]);

        $invitation->update([
            'status' => WorkspaceInvitation::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        $this->auditLogService->log(
            $invitation->workspace,
            $user,
            'invitation_accepted',
            WorkspaceInvitation::class,
            $invitation->id,
            "{$user->name} accepted the invitation",
            ['email' => $invitation->email]
        );

        return $member;
    }

    public function reject(WorkspaceInvitation $invitation, User $user): void
    {
        if (!$invitation->isPending()) {
            throw new \InvalidArgumentException('Invitation is no longer valid.');
        }

        if (strtolower($invitation->email) !== strtolower($user->email)) {
            throw new \InvalidArgumentException('This invitation was sent to a different email address.');
        }

        $invitation->update(['status' => WorkspaceInvitation::STATUS_REVOKED]);
    }

    private function sendInvitationEmail(WorkspaceInvitation $invitation, Workspace $workspace, User $inviter): void
    {
        Mail::to($invitation->email)->queue(new WorkspaceInvitationMail($invitation, $workspace, $inviter));
    }
}
