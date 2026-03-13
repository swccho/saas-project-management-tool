<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\StoreWorkspaceIconRequest;
use App\Http\Requests\Workspace\StoreWorkspaceLogoRequest;
use App\Http\Requests\Workspace\UpdateWorkspaceBrandingRequest;
use App\Models\Workspace;
use App\Services\AuditLogService;
use App\Services\WorkspaceBrandingService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WorkspaceBrandingController extends Controller
{
    public function __construct(
        private readonly WorkspaceBrandingService $brandingService,
        private readonly AuditLogService $auditLogService
    ) {}

    public function show(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        return ApiResponse::success(data: [
            'logo_url' => $this->brandingService->getLogoUrl($workspace),
            'icon_url' => $this->brandingService->getIconUrl($workspace),
            'accent_color' => $workspace->accent_color,
            'short_description' => $workspace->short_description,
        ]);
    }

    public function update(UpdateWorkspaceBrandingRequest $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('update', $workspace);

        $workspace->update($request->validated());

        $this->auditLogService->log(
            $workspace,
            $request->user(),
            'branding_updated',
            Workspace::class,
            $workspace->id,
            'Workspace branding updated',
            null
        );

        return ApiResponse::success(data: [
            'logo_url' => $this->brandingService->getLogoUrl($workspace->fresh()),
            'icon_url' => $this->brandingService->getIconUrl($workspace->fresh()),
            'accent_color' => $workspace->accent_color,
            'short_description' => $workspace->short_description,
        ]);
    }

    public function uploadLogo(StoreWorkspaceLogoRequest $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('update', $workspace);

        $this->brandingService->uploadLogo($workspace, $request->file('logo'));

        return ApiResponse::success(data: [
            'logo_url' => $this->brandingService->getLogoUrl($workspace->fresh()),
        ]);
    }

    public function uploadIcon(StoreWorkspaceIconRequest $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('update', $workspace);

        $this->brandingService->uploadIcon($workspace, $request->file('icon'));

        return ApiResponse::success(data: [
            'icon_url' => $this->brandingService->getIconUrl($workspace->fresh()),
        ]);
    }

    public function removeLogo(Workspace $workspace): JsonResponse
    {
        $this->authorize('update', $workspace);

        $this->brandingService->removeLogo($workspace);

        return ApiResponse::success(data: ['logo_url' => null]);
    }

    public function removeIcon(Workspace $workspace): JsonResponse
    {
        $this->authorize('update', $workspace);

        $this->brandingService->removeIcon($workspace);

        return ApiResponse::success(data: ['icon_url' => null]);
    }

    public function logo(Request $request, Workspace $workspace): StreamedResponse
    {
        if (!$workspace->logo_path) {
            abort(404);
        }

        return $this->streamFile($workspace->logo_path);
    }

    public function icon(Request $request, Workspace $workspace): StreamedResponse
    {
        if (!$workspace->icon_path) {
            abort(404);
        }

        return $this->streamFile($workspace->icon_path);
    }

    private function streamFile(string $path): StreamedResponse
    {
        $fullPath = Storage::disk('local')->path($path);
        if (!file_exists($fullPath)) {
            abort(404);
        }

        $mime = match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'image/png',
        };

        return response()->streamDownload(function () use ($fullPath): void {
            echo file_get_contents($fullPath);
        }, basename($path), ['Content-Type' => $mime]);
    }
}
