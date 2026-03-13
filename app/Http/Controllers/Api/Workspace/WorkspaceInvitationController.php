<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\StoreWorkspaceInvitationRequest;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use App\Services\AuditLogService;
use App\Services\InvitationService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkspaceInvitationController extends Controller
{
    public function __construct(
        private readonly InvitationService $invitationService,
        private readonly AuditLogService $auditLogService
    ) {}

    public function index(Workspace $workspace): JsonResponse
    {
        $this->authorize('manageMembers', $workspace);

        $invitations = $workspace->invitations()
            ->where('status', WorkspaceInvitation::STATUS_PENDING)
            ->where('expires_at', '>', now())
            ->with('inviter')
            ->orderByDesc('created_at')
            ->get();

        $data = $invitations->map(fn ($i) => [
            'id' => $i->id,
            'email' => $i->email,
            'role' => $i->role,
            'expires_at' => $i->expires_at->toIso8601String(),
            'invited_by' => $i->inviter ? [
                'id' => $i->inviter->id,
                'name' => $i->inviter->name,
            ] : null,
            'created_at' => $i->created_at->toIso8601String(),
        ]);

        return ApiResponse::success(data: $data);
    }

    public function store(StoreWorkspaceInvitationRequest $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('manageMembers', $workspace);

        $currentMember = $workspace->members()->where('user_id', $request->user()->id)->first();
        if ($currentMember && $currentMember->isAdmin() && $request->validated('role') === 'admin') {
            return ApiResponse::error('Only owners can invite as admin.', 403);
        }

        try {
            $invitation = $this->invitationService->create(
                $workspace,
                $request->user(),
                $request->validated('email'),
                $request->validated('role')
            );
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }

        $this->auditLogService->log(
            $workspace,
            $request->user(),
            'member_invited',
            WorkspaceInvitation::class,
            $invitation->id,
            "Invitation sent to {$invitation->email}",
            ['email' => $invitation->email, 'role' => $invitation->role]
        );

        return ApiResponse::success(data: [
            'id' => $invitation->id,
            'email' => $invitation->email,
            'role' => $invitation->role,
            'token' => $invitation->token,
            'expires_at' => $invitation->expires_at->toIso8601String(),
            'created_at' => $invitation->created_at->toIso8601String(),
        ], status: 201);
    }

    public function resend(Workspace $workspace, WorkspaceInvitation $invitation): JsonResponse
    {
        $this->authorize('manageMembers', $workspace);

        if ($invitation->workspace_id !== $workspace->id) {
            abort(404);
        }

        try {
            $invitation = $this->invitationService->resend($invitation);
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }

        return ApiResponse::success(data: [
            'id' => $invitation->id,
            'token' => $invitation->token,
            'expires_at' => $invitation->expires_at->toIso8601String(),
        ]);
    }

    public function destroy(Request $request, Workspace $workspace, WorkspaceInvitation $invitation): JsonResponse
    {
        $this->authorize('manageMembers', $workspace);

        if ($invitation->workspace_id !== $workspace->id) {
            abort(404);
        }

        $email = $invitation->email;
        $this->invitationService->revoke($invitation);

        $this->auditLogService->log(
            $workspace,
            $request->user(),
            'invitation_revoked',
            WorkspaceInvitation::class,
            $invitation->id,
            "Invitation to {$email} revoked",
            ['email' => $email]
        );

        return ApiResponse::success(message: 'Invitation revoked.', status: 200);
    }
}
