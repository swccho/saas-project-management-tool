<?php

namespace App\Services;

use App\Models\Workspace;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class WorkspaceBrandingService
{
    public function uploadLogo(Workspace $workspace, UploadedFile $file): string
    {
        $path = "workspaces/{$workspace->id}";
        $this->deleteFile($workspace->logo_path);

        $ext = $file->getClientOriginalExtension() ?: 'png';
        $filename = 'logo.' . $ext;
        $fullPath = $file->storeAs($path, $filename, 'local');

        $workspace->update(['logo_path' => $fullPath]);

        return $fullPath;
    }

    public function uploadIcon(Workspace $workspace, UploadedFile $file): string
    {
        $path = "workspaces/{$workspace->id}";
        $this->deleteFile($workspace->icon_path);

        $ext = $file->getClientOriginalExtension() ?: 'png';
        $filename = 'icon.' . $ext;
        $fullPath = $file->storeAs($path, $filename, 'local');

        $workspace->update(['icon_path' => $fullPath]);

        return $fullPath;
    }

    public function removeLogo(Workspace $workspace): void
    {
        $this->deleteFile($workspace->logo_path);
        $workspace->update(['logo_path' => null]);
    }

    public function removeIcon(Workspace $workspace): void
    {
        $this->deleteFile($workspace->icon_path);
        $workspace->update(['icon_path' => null]);
    }

    public function getLogoUrl(Workspace $workspace): ?string
    {
        if (!$workspace->logo_path) {
            return null;
        }

        return URL::temporarySignedRoute(
            'api.workspace.branding.logo',
            now()->addHour(),
            ['workspace' => $workspace->id]
        );
    }

    public function getIconUrl(Workspace $workspace): ?string
    {
        if (!$workspace->icon_path) {
            return null;
        }

        return URL::temporarySignedRoute(
            'api.workspace.branding.icon',
            now()->addHour(),
            ['workspace' => $workspace->id]
        );
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }
}
