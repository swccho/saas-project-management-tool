<?php

namespace App\Http\Controllers\Api\Workspace;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class WorkspaceMemberController extends Controller
{
    public function index(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        $members = $workspace->members()->with('user')->get();

        $data = $members->map(fn ($m) => [
            'id' => $m->id,
            'user_id' => $m->user_id,
            'role' => $m->role,
            'user' => [
                'id' => $m->user->id,
                'name' => $m->user->name,
                'email' => $m->user->email,
            ],
        ]);

        return ApiResponse::success(data: $data);
    }
}
