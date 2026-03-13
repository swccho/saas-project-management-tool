<?php

use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::put('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::put('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});
