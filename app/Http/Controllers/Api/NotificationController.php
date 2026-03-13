<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->notifications()->orderByDesc('created_at');

        if ($request->boolean('unread_only')) {
            $query->where('is_read', false);
        }

        $notifications = $query->paginate(20);

        return ApiResponse::success(data: [
            'data' => NotificationResource::collection($notifications),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    public function markRead(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== auth()->id()) {
            abort(404);
        }

        $this->notificationService->markAsRead($notification);

        return ApiResponse::success(data: new NotificationResource($notification->fresh()));
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $count = $this->notificationService->markAllAsRead($request->user());

        return ApiResponse::success(data: ['marked_count' => $count]);
    }
}
