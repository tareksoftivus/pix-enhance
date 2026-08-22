<?php

namespace App\Modules\UserWorkspace\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\SystemNotifications\Services\UserSystemNotificationService;
use App\Modules\UserWorkspace\Http\Requests\UpdateNotificationPreferencesRequest;
use App\Modules\UserWorkspace\Http\Requests\UpdateRenderDefaultsRequest;
use App\Modules\UserWorkspace\Services\UserWorkspaceService;
use Illuminate\Http\RedirectResponse;

class WorkspacePreferencesController extends Controller
{
    public function __construct(
        protected UserWorkspaceService $workspaceService,
        protected UserSystemNotificationService $systemNotifications
    ) {}

    public function updateNotifications(UpdateNotificationPreferencesRequest $request): RedirectResponse
    {
        $this->workspaceService->updateNotificationPreferences($request->user(), [
            'render_finished' => $request->boolean('render_finished'),
            'credits_low' => $request->boolean('credits_low'),
            'weekly_summary' => $request->boolean('weekly_summary'),
            'product_news' => $request->boolean('product_news'),
            'desktop_notifications_enabled' => $request->boolean('desktop_notifications_enabled'),
            'completion_sound_enabled' => $request->boolean('completion_sound_enabled'),
        ]);
        $this->systemNotifications->workspacePreferencesUpdated($request->user(), __('notification'));

        return back()->with('success', __('Notification preferences updated successfully.'));
    }

    public function updateRenderDefaults(UpdateRenderDefaultsRequest $request): RedirectResponse
    {
        $this->workspaceService->updateRenderDefaults($request->user(), [
            'default_model' => $request->string('default_model')->toString(),
            'default_scale' => $request->integer('default_scale'),
            'default_format' => $request->string('default_format')->toString(),
            'face_restoration' => $request->boolean('face_restoration'),
            'auto_download' => $request->boolean('auto_download'),
            'source_retention_days' => $request->integer('source_retention_days'),
        ]);
        $this->systemNotifications->workspacePreferencesUpdated($request->user(), __('render default'));

        return back()->with('success', __('Render defaults updated successfully.'));
    }
}
