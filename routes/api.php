<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    require __DIR__.'/api/auth.php';
    require __DIR__.'/api/workspaces.php';
    require __DIR__.'/api/projects.php';
    require __DIR__.'/api/boards.php';
    require __DIR__.'/api/tasks.php';
    require __DIR__.'/api/labels.php';
});
