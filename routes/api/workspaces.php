<?php

use App\Http\Controllers\Api\Workspace\CalendarController;
use App\Http\Controllers\Api\Workspace\DashboardController;
use App\Http\Controllers\Api\Workspace\MyTasksController;
use App\Http\Controllers\Api\Workspace\WorkspaceActivityController;
use App\Http\Controllers\Api\Workspace\WorkspaceAnalyticsController;
use App\Http\Controllers\Api\Workspace\WorkspaceController;
use App\Http\Controllers\Api\Workspace\WorkspaceInvitationController;
use App\Http\Controllers\Api\Workspace\WorkspaceMemberController;
use App\Http\Controllers\Api\Workspace\WorkspaceOwnerTransferController;
use App\Http\Controllers\Api\Workspace\WorkspaceAuditLogController;
use App\Http\Controllers\Api\Workspace\WorkspaceBrandingController;
use App\Http\Controllers\Api\Workspace\WorkspacePreferencesController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('workspaces', WorkspaceController::class);
    Route::get('workspaces/{workspace}/members', [WorkspaceMemberController::class, 'index']);
    Route::put('workspaces/{workspace}/members/{member}/role', [WorkspaceMemberController::class, 'updateRole']);
    Route::delete('workspaces/{workspace}/members/{member}', [WorkspaceMemberController::class, 'destroy']);
    Route::get('workspaces/{workspace}/invitations', [WorkspaceInvitationController::class, 'index']);
    Route::post('workspaces/{workspace}/invitations', [WorkspaceInvitationController::class, 'store']);
    Route::post('workspaces/{workspace}/invitations/{invitation}/resend', [WorkspaceInvitationController::class, 'resend']);
    Route::delete('workspaces/{workspace}/invitations/{invitation}', [WorkspaceInvitationController::class, 'destroy']);
    Route::post('workspaces/{workspace}/owner-transfer', [WorkspaceOwnerTransferController::class, 'store']);
    Route::get('workspaces/{workspace}/preferences', [WorkspacePreferencesController::class, 'show']);
    Route::put('workspaces/{workspace}/preferences', [WorkspacePreferencesController::class, 'update']);
    Route::get('workspaces/{workspace}/branding', [WorkspaceBrandingController::class, 'show']);
    Route::put('workspaces/{workspace}/branding', [WorkspaceBrandingController::class, 'update']);
    Route::post('workspaces/{workspace}/branding/logo', [WorkspaceBrandingController::class, 'uploadLogo']);
    Route::post('workspaces/{workspace}/branding/icon', [WorkspaceBrandingController::class, 'uploadIcon']);
    Route::delete('workspaces/{workspace}/branding/logo', [WorkspaceBrandingController::class, 'removeLogo']);
    Route::delete('workspaces/{workspace}/branding/icon', [WorkspaceBrandingController::class, 'removeIcon']);
    Route::get('workspaces/{workspace}/dashboard', [DashboardController::class, 'index']);
    Route::get('workspaces/{workspace}/favorites', [WorkspaceController::class, 'favorites']);
    Route::get('workspaces/{workspace}/analytics', [WorkspaceAnalyticsController::class, 'index']);
    Route::get('workspaces/{workspace}/my-tasks', [MyTasksController::class, 'index']);
    Route::get('workspaces/{workspace}/calendar', [CalendarController::class, 'index']);
    Route::get('workspaces/{workspace}/activities', [WorkspaceActivityController::class, 'index']);
    Route::get('workspaces/{workspace}/audit-logs', [WorkspaceAuditLogController::class, 'index']);
});

Route::get('workspaces/{workspace}/branding/logo', [WorkspaceBrandingController::class, 'logo'])
    ->middleware('signed')
    ->name('api.workspace.branding.logo');
Route::get('workspaces/{workspace}/branding/icon', [WorkspaceBrandingController::class, 'icon'])
    ->middleware('signed')
    ->name('api.workspace.branding.icon');
