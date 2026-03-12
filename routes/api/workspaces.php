<?php

use App\Http\Controllers\Api\Workspace\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('workspaces', WorkspaceController::class);
});
