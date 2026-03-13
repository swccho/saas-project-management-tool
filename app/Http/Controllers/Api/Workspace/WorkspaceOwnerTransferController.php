<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Actions\Workspace\TransferWorkspaceOwnershipAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\TransferWorkspaceOwnershipRequest;
use App\Models\Workspace;
use App\Services\AuditLogService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class WorkspaceOwnerTransferController extends Controller
{
    public function __construct(
        private readonly TransferWorkspaceOwnershipAction $transferAction,
        private readonly AuditLogService $auditLogService
    ) {}

    public function store(TransferWorkspaceOwnershipRequest $request, Workspace $workspace): JsonResponse
    {
        if ($workspace->owner_id !== $request->user()->id) {
            return ApiResponse::error('Only the workspace owner can transfer ownership.', 403);
        }

        try {
            $this->transferAction->execute(
                $workspace,
                $request->user(),
                $request->validated('new_owner_id')
            );
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }

        $this->auditLogService->log(
            $workspace->fresh(),
            $request->user(),
            'owner_transferred',
            Workspace::class,
            $workspace->id,
            'Workspace ownership transferred',
            ['new_owner_id' => $request->validated('new_owner_id')]
        );

        return ApiResponse::success(message: 'Ownership transferred successfully.');
    }
}
