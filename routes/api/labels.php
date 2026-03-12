<?php

use App\Http\Controllers\Api\Label\LabelController;
use App\Http\Controllers\Api\Task\TaskLabelController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('workspaces.projects.labels', LabelController::class)->except(['show']);
    Route::put(
        'workspaces/{workspace}/projects/{project}/boards/{board}/tasks/{task}/labels',
        [TaskLabelController::class, 'update']
    )->name('workspaces.projects.boards.tasks.labels');
});