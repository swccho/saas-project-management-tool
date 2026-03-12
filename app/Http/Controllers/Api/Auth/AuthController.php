<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
        );

        return ApiResponse::success(
            data: [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ],
            status: 201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );

        if ($result === null) {
            return ApiResponse::error('Invalid credentials.', 401);
        }

        return ApiResponse::success(data: [
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(message: 'Logged out successfully.');
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(data: new UserResource($request->user()));
    }
}
