<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\UpdateWorkspaceMemberRequest;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Services\AuditLogService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkspaceMemberController extends Controller
{
    public function index(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $members = $workspace->members()->with('user')->orderBy('created_at')->get();

        $profileService = app(\App\Services\ProfileService::class);
        $data = $members->map(fn ($m) => [
            'id' => $m->id,
            'user_id' => $m->user_id,
            'role' => $m->role,
            'joined_at' => $m->created_at->toIso8601String(),
            'user' => [
                'id' => $m->user->id,
                'name' => $m->user->name,
                'email' => $m->user->email,
                'avatar_url' => $m->user->avatar ? $profileService->getAvatarUrl($m->user) : null,
            ],
        ]);

        return ApiResponse::success(data: $data);
    }

    public function updateRole(UpdateWorkspaceMemberRequest $request, Workspace $workspace, WorkspaceMember $member): JsonResponse
    {
        $this->authorize('manageMembers', $workspace);

        if ($member->workspace_id !== $workspace->id) {
            abort(404);
        }

        if ($member->isOwner()) {
            return ApiResponse::error('Cannot change owner role.', 403);
        }

        $currentUserMember = $workspace->members()->where('user_id', $request->user()->id)->first();
        if ($currentUserMember && $currentUserMember->isMember() && $request->validated('role') === 'admin') {
            return ApiResponse::error('Only owners can assign admin role.', 403);
        }

        $member->update(['role' => $request->validated('role')]);

        $this->auditLogService->log(
            $workspace,
            $request->user(),
            'member_role_changed',
            WorkspaceMember::class,
            $member->id,
            "Member role changed to {$request->validated('role')}",
            ['user_id' => $member->user_id, 'new_role' => $request->validated('role')]
        );

        $profileService = app(\App\Services\ProfileService::class);
        return ApiResponse::success(data: [
            'id' => $member->id,
            'user_id' => $member->user_id,
            'role' => $member->role,
            'joined_at' => $member->created_at->toIso8601String(),
            'user' => [
                'id' => $member->user->id,
                'name' => $member->user->name,
                'email' => $member->user->email,
                'avatar_url' => $member->user->avatar ? $profileService->getAvatarUrl($member->user) : null,
            ],
        ]);
    }

    public function destroy(Request $request, Workspace $workspace, WorkspaceMember $member): JsonResponse
    {
        $this->authorize('manageMembers', $workspace);

        if ($member->workspace_id !== $workspace->id) {
            abort(404);
        }

        if ($member->isOwner()) {
            return ApiResponse::error('Cannot remove workspace owner.', 403);
        }

        if ($member->user_id === $request->user()->id) {
            return ApiResponse::error('Cannot remove yourself. Transfer ownership first.', 403);
        }

        $userId = $member->user_id;
        $userName = $member->user->name ?? 'Unknown';
        $member->delete();

        $this->auditLogService->log(
            $workspace,
            $request->user(),
            'member_removed',
            WorkspaceMember::class,
            null,
            "Member {$userName} removed from workspace",
            ['removed_user_id' => $userId]
        );

        return ApiResponse::success(message: 'Member removed.', status: 200);
    }
}
