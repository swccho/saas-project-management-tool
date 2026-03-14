<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return ApiResponse::success(
                message: 'If that email address is in our system, we have sent a password reset link.',
            );
        }

        if ($status === Password::INVALID_USER) {
            return ApiResponse::success(
                message: 'If that email address is in our system, we have sent a password reset link.',
            );
        }

        if ($status === Password::RESET_THROTTLED) {
            return ApiResponse::error('Please wait before requesting another reset link.', 429);
        }

        return ApiResponse::error('Unable to send reset link.', 500);
    }
}
