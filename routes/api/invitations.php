<?php

use App\Http\Controllers\Api\Invitation\InvitationAcceptController;
use Illuminate\Support\Facades\Route;

Route::get('invitations/{token}', [InvitationAcceptController::class, 'preview']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('invitations/{token}/accept', [InvitationAcceptController::class, 'accept']);
    Route::post('invitations/{token}/reject', [InvitationAcceptController::class, 'reject']);
});
