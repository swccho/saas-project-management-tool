<?php

use App\Http\Controllers\Api\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::put('profile/password', [ProfileController::class, 'updatePassword']);
    Route::post('profile/avatar', [ProfileController::class, 'uploadAvatar']);
});

Route::get('profile/avatar/{user}', [ProfileController::class, 'avatar'])
    ->middleware('signed')
    ->name('api.profile.avatar');
