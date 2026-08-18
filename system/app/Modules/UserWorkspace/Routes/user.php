<?php

use App\Modules\UserWorkspace\Http\Controllers\User\WorkspacePreferencesController;
use Illuminate\Support\Facades\Route;

Route::put('workspace/notifications', [WorkspacePreferencesController::class, 'updateNotifications'])
    ->name('workspace.notifications.update');

Route::put('workspace/render-defaults', [WorkspacePreferencesController::class, 'updateRenderDefaults'])
    ->name('workspace.render-defaults.update');
