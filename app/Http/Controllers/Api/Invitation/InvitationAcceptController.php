<?php

namespace App\Http\Controllers\Api\Invitation;

use App\Http\Controllers\Controller;
use App\Models\WorkspaceInvitation;
use App\Services\InvitationService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvitationAcceptController extends Controller
{
    public function __construct(
        private readonly InvitationService $invitationService
    ) {}

    public function preview(string $token): JsonResponse
    {
        $invitation = WorkspaceInvitation::where('token', $token)
            ->with(['workspace', 'inviter'])
            ->first();

        if (!$invitation || !$invitation->isPending() || $invitation->isExpired()) {
            return ApiResponse::error('Invalid or expired invitation.', 404);
        }

        return ApiResponse::success(data: [
            'workspace_name' => $invitation->workspace->name,
            'inviter_name' => $invitation->inviter?->name ?? 'Someone',
            'email' => $invitation->email,
        ]);
    }

    public function accept(Request $request, string $token): JsonResponse
    {
        $invitation = WorkspaceInvitation::where('token', $token)->first();

        if (!$invitation) {
            return ApiResponse::error('Invalid invitation token.', 404);
        }

        $user = $request->user();
        if (!$user) {
            return ApiResponse::error('You must be logged in to accept an invitation.', 401);
        }

        try {
            $member = $this->invitationService->accept($invitation, $user);
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }

        return ApiResponse::success(data: [
            'workspace_id' => $member->workspace_id,
            'message' => 'Invitation accepted. You have joined the workspace.',
        ]);
    }

    public function reject(Request $request, string $token): JsonResponse
    {
        $invitation = WorkspaceInvitation::where('token', $token)->first();

        if (!$invitation) {
            return ApiResponse::error('Invalid invitation token.', 404);
        }

        $user = $request->user();
        if (!$user) {
            return ApiResponse::error('You must be logged in to reject an invitation.', 401);
        }

        try {
            $this->invitationService->reject($invitation, $user);
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }

        return ApiResponse::success(message: 'Invitation declined.');
    }
}
