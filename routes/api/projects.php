<?php

use App\Http\Controllers\Api\Project\ProjectActivityController;
use App\Http\Controllers\Api\Project\ProjectAnalyticsController;
use App\Http\Controllers\Api\Project\ProjectController;
use App\Http\Controllers\Api\Project\ProjectFavoriteController;
use App\Http\Controllers\Api\Project\ProjectMemberController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('workspaces.projects', ProjectController::class);
    Route::get('workspaces/{workspace}/projects/{project}/activities', [ProjectActivityController::class, 'index']);
    Route::get('workspaces/{workspace}/projects/{project}/analytics', [ProjectAnalyticsController::class, 'index']);
    Route::post('workspaces/{workspace}/projects/{project}/favorite', [ProjectFavoriteController::class, 'store']);
    Route::delete('workspaces/{workspace}/projects/{project}/favorite', [ProjectFavoriteController::class, 'destroy']);
    Route::apiResource('workspaces.projects.members', ProjectMemberController::class)
        ->except(['show'])
        ->names([
            'index' => 'workspaces.projects.members.index',
            'store' => 'workspaces.projects.members.store',
            'update' => 'workspaces.projects.members.update',
            'destroy' => 'workspaces.projects.members.destroy',
        ]);
});
