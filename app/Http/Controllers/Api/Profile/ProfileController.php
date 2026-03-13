<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\StoreAvatarRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ProfileService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService
    ) {}

    public function show(Request $request): JsonResponse
    {
        return ApiResponse::success(data: new UserResource($request->user()));
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->profileService->updateProfile($request->user(), $request->validated());

        return ApiResponse::success(data: new UserResource($user));
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $this->profileService->updatePassword($request->user(), $request->validated('password'));

        return ApiResponse::success(message: 'Password updated successfully.');
    }

    public function uploadAvatar(StoreAvatarRequest $request): JsonResponse
    {
        $user = $request->user();
        $this->profileService->uploadAvatar($user, $request->file('avatar'));

        return ApiResponse::success(data: new UserResource($user->fresh()));
    }

    public function avatar(User $user): StreamedResponse
    {
        if (! $user->avatar) {
            abort(404);
        }

        $path = Storage::disk('local')->path($user->avatar);
        if (! file_exists($path)) {
            abort(404);
        }

        $mime = match (strtolower(pathinfo($user->avatar, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            default => 'image/jpeg',
        };

        return response()->streamDownload(function () use ($path): void {
            echo file_get_contents($path);
        }, basename($user->avatar), ['Content-Type' => $mime]);
    }
}
