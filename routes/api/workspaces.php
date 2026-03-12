<?php

use App\Http\Controllers\Api\Workspace\WorkspaceController;
use App\Http\Controllers\Api\Workspace\WorkspaceMemberController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('workspaces', WorkspaceController::class);
    Route::get('workspaces/{workspace}/members', [WorkspaceMemberController::class, 'index']);
});
