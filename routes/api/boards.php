<?php

use App\Http\Controllers\Api\Board\BoardColumnController;
use App\Http\Controllers\Api\Board\BoardController;
use App\Http\Controllers\Api\Board\BoardViewController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('workspaces.projects.boards', BoardController::class);
    Route::post(
        'workspaces/{workspace}/projects/{project}/boards/{board}/columns/reorder',
        [BoardColumnController::class, 'reorder']
    )->name('workspaces.projects.boards.columns.reorder');
    Route::apiResource('workspaces.projects.boards.columns', BoardColumnController::class)
        ->except(['show']);
    Route::get(
        'workspaces/{workspace}/projects/{project}/boards/{board}/views',
        [BoardViewController::class, 'index']
    )->name('workspaces.projects.boards.views.index');
    Route::post(
        'workspaces/{workspace}/projects/{project}/boards/{board}/views',
        [BoardViewController::class, 'store']
    )->name('workspaces.projects.boards.views.store');
    Route::put(
        'workspaces/{workspace}/projects/{project}/boards/{board}/views/{view}',
        [BoardViewController::class, 'update']
    )->name('workspaces.projects.boards.views.update');
    Route::delete(
        'workspaces/{workspace}/projects/{project}/boards/{board}/views/{view}',
        [BoardViewController::class, 'destroy']
    )->name('workspaces.projects.boards.views.destroy');
});
