<?php

use App\Http\Controllers\Api\Project\ProjectController;
use App\Http\Controllers\Api\Project\ProjectMemberController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('workspaces.projects', ProjectController::class);
    Route::apiResource('workspaces.projects.members', ProjectMemberController::class)
        ->except(['show'])
        ->names([
            'index' => 'workspaces.projects.members.index',
            'store' => 'workspaces.projects.members.store',
            'update' => 'workspaces.projects.members.update',
            'destroy' => 'workspaces.projects.members.destroy',
        ]);
});
