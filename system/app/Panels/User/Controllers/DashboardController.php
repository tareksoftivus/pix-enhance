<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Services\BillingService;
use App\Modules\RenderJobs\Services\RenderJobService;
use App\Modules\Shared\Services\SessionService;
use App\Modules\UserWorkspace\Services\UserWorkspaceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected SessionService $sessionService,
        protected UserWorkspaceService $workspaceService,
        protected RenderJobService $renderJobService,
        protected BillingService $billingService
    ) {}

    public function index(): View
    {
        $workspace = $this->workspaceService->dashboardFor(auth()->user());

        return view('panels.user.dashboard', compact('workspace'));
    }

    public function upscaler(): View
    {
        $recentEnhancements = $this->renderJobService->recentCardsFor(auth()->user(), 'upscaler', 4);

        return view('panels.user.upscaler', compact('recentEnhancements'));
    }

    public function faceRestoration(): View
    {
        $recentEnhancements = $this->renderJobService->recentCardsFor(auth()->user(), 'face-restoration', 4);

        return view('panels.user.face-restoration', compact('recentEnhancements'));
    }

    public function backgroundRemoval(): View
    {
        $recentEnhancements = $this->renderJobService->recentCardsFor(auth()->user(), 'background-removal', 4);

        return view('panels.user.background-removal', compact('recentEnhancements'));
    }

    public function projects(Request $request): View
    {
        $projects = $this->renderJobService->projectsFor(auth()->user(), $request->query(), 12);

        return view('panels.user.projects', compact('projects'));
    }

    public function history(Request $request): View
    {
        $history = $this->workspaceService->historyFor(auth()->user(), $request->query());

        return view('panels.user.history', compact('history'));
    }

    public function billing(): View
    {
        $billing = $this->billingService->workspaceFor(auth()->user());

        return view('panels.user.billing', compact('billing'));
    }

    public function settings(): View
    {
        $user = auth()->user();
        $sessions = $this->sessionService->getActiveSessions($user->id);
        $workspacePreferences = $this->workspaceService->preferencesFor($user);
        $renderSummary = $this->renderJobService->summaryFor($user);

        return view('panels.user.profile.settings', compact('user', 'sessions', 'workspacePreferences', 'renderSummary'));
    }
}
