<?php

use App\Http\Controllers\Api\Board\BoardColumnController;
use App\Http\Controllers\Api\Board\BoardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('workspaces.projects.boards', BoardController::class);
    Route::post(
        'workspaces/{workspace}/projects/{project}/boards/{board}/columns/reorder',
        [BoardColumnController::class, 'reorder']
    )->name('workspaces.projects.boards.columns.reorder');
    Route::apiResource('workspaces.projects.boards.columns', BoardColumnController::class)
        ->except(['show']);
});
