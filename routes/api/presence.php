<?php

use App\Http\Controllers\Api\PresenceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('presence/heartbeat', [PresenceController::class, 'heartbeat'])->name('presence.heartbeat');
    Route::get('workspaces/{workspace}/presence', [PresenceController::class, 'index'])->name('workspaces.presence');
});
