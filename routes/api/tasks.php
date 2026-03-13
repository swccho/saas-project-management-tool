<?php

use App\Http\Controllers\Api\Task\SubtaskController;
use App\Http\Controllers\Api\Task\TaskActivityController;
use App\Http\Controllers\Api\Task\TaskAssigneeController;
use App\Http\Controllers\Api\Task\TaskCommentController;
use App\Http\Controllers\Api\Task\TaskController;
use App\Http\Controllers\Api\Task\TaskMetaController;
use App\Http\Controllers\Api\Task\TaskAttachmentController;
use App\Http\Controllers\Api\Task\TaskWatchController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post(
        'workspaces/{workspace}/projects/{project}/boards/{board}/tasks/{task}/move',
        [TaskController::class, 'move']
    )->name('workspaces.projects.boards.tasks.move');
    Route::put(
        'workspaces/{workspace}/projects/{project}/boards/{board}/tasks/{task}/assignee',
        [TaskAssigneeController::class, 'update']
    )->name('workspaces.projects.boards.tasks.assignee');
    Route::put(
        'workspaces/{workspace}/projects/{project}/boards/{board}/tasks/{task}/meta',
        [TaskMetaController::class, 'update']
    )->name('workspaces.projects.boards.tasks.meta');
    Route::apiResource('workspaces.projects.boards.tasks', TaskController::class);
    Route::apiResource('workspaces.projects.boards.tasks.subtasks', SubtaskController::class)
        ->except(['show']);
    Route::get(
        'workspaces/{workspace}/projects/{project}/boards/{board}/tasks/{task}/activities',
        [TaskActivityController::class, 'index']
    )->name('workspaces.projects.boards.tasks.activities');
    Route::apiResource(
        'workspaces.projects.boards.tasks.comments',
        TaskCommentController::class
    )->except(['show']);
    Route::post(
        'workspaces/{workspace}/projects/{project}/boards/{board}/tasks/{task}/watch',
        [TaskWatchController::class, 'store']
    )->name('workspaces.projects.boards.tasks.watch');
    Route::delete(
        'workspaces/{workspace}/projects/{project}/boards/{board}/tasks/{task}/watch',
        [TaskWatchController::class, 'destroy']
    )->name('workspaces.projects.boards.tasks.unwatch');
    Route::get(
        'workspaces/{workspace}/projects/{project}/boards/{board}/tasks/{task}/attachments',
        [TaskAttachmentController::class, 'index']
    )->name('workspaces.projects.boards.tasks.attachments.index');
    Route::post(
        'workspaces/{workspace}/projects/{project}/boards/{board}/tasks/{task}/attachments',
        [TaskAttachmentController::class, 'store']
    )->name('workspaces.projects.boards.tasks.attachments.store');
    Route::delete(
        'workspaces/{workspace}/projects/{project}/boards/{board}/tasks/{task}/attachments/{attachment}',
        [TaskAttachmentController::class, 'destroy']
    )->name('workspaces.projects.boards.tasks.attachments.destroy');
    Route::get(
        'workspaces/{workspace}/projects/{project}/boards/{board}/tasks/{task}/attachments/{attachment}/download',
        [TaskAttachmentController::class, 'download']
    )->name('workspaces.projects.boards.tasks.attachments.download');
});