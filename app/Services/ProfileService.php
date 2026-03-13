<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ProfileService
{
    public function updateProfile(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        return $user->fresh();
    }

    public function updatePassword(User $user, string $newPassword): void
    {
        $user->update(['password' => Hash::make($newPassword)]);
    }

    public function uploadAvatar(User $user, UploadedFile $file): string
    {
        $path = "avatars/{$user->id}";
        Storage::disk('local')->deleteDirectory($path);

        $path = $file->store($path, 'local');
        $user->update(['avatar' => $path]);

        return $path;
    }

    public function getAvatarUrl(User $user): ?string
    {
        if (! $user->avatar) {
            return null;
        }

        return URL::temporarySignedRoute(
            'api.profile.avatar',
            now()->addHour(),
            ['user' => $user->id]
        );
    }
}
