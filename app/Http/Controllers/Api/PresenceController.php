<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPresence;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    private const ONLINE_THRESHOLD_MINUTES = 5;

    private const AWAY_THRESHOLD_MINUTES = 30;

    public function heartbeat(Request $request): JsonResponse
    {
        $user = $request->user();
        UserPresence::updateOrCreate(
            ['user_id' => $user->id],
            [
                'status' => UserPresence::STATUS_ONLINE,
                'last_seen_at' => now(),
            ]
        );

        return ApiResponse::success(data: ['status' => 'online']);
    }

    public function index(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $memberIds = $workspace->members()->pluck('user_id');
        $presences = UserPresence::whereIn('user_id', $memberIds)->get()->keyBy('user_id');
        $users = User::whereIn('id', $memberIds)->get()->keyBy('id');

        $data = $memberIds->map(function ($userId) use ($presences, $users) {
            $presence = $presences->get($userId);
            $user = $users->get($userId);
            $lastSeen = $presence?->last_seen_at ?? $user?->updated_at;
            $status = $this->deriveStatus($lastSeen);

            return [
                'user_id' => $userId,
                'name' => $user?->name,
                'status' => $status,
                'last_seen_at' => $lastSeen?->toIso8601String(),
            ];
        })->values();

        return ApiResponse::success(data: $data);
    }

    private function deriveStatus(?Carbon $lastSeen): string
    {
        if (!$lastSeen) {
            return UserPresence::STATUS_OFFLINE;
        }

        $minutesAgo = $lastSeen->diffInMinutes(now());

        if ($minutesAgo <= self::ONLINE_THRESHOLD_MINUTES) {
            return UserPresence::STATUS_ONLINE;
        }

        if ($minutesAgo <= self::AWAY_THRESHOLD_MINUTES) {
            return UserPresence::STATUS_AWAY;
        }

        return UserPresence::STATUS_OFFLINE;
    }
}
