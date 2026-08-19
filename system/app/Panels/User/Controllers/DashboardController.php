<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AiSettings\Services\AiSettingsService;
use App\Modules\Billing\Services\BillingService;
use App\Modules\Credits\Services\CreditCheckoutService;
use App\Modules\RenderJobs\Services\RenderJobService;
use App\Modules\Shared\Services\SessionService;
use App\Modules\UserWorkspace\Services\UserWorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected SessionService $sessionService,
        protected UserWorkspaceService $workspaceService,
        protected RenderJobService $renderJobService,
        protected BillingService $billingService,
        protected AiSettingsService $aiSettingsService,
        protected CreditCheckoutService $creditCheckoutService
    ) {}

    public function index(): View
    {
        $workspace = $this->workspaceService->dashboardFor(auth()->user());
        $imageModels = $this->aiSettingsService->getEnabledImageModels();

        return view('panels.user.dashboard', compact('workspace', 'imageModels'));
    }

    public function upscaler(): View
    {
        $recentEnhancements = $this->renderJobService->recentCardsFor(auth()->user(), 'upscaler', 4);
        $imageModels = $this->aiSettingsService->getEnabledImageModels();

        return view('panels.user.upscaler', compact('recentEnhancements', 'imageModels'));
    }

    public function faceRestoration(): View
    {
        $recentEnhancements = $this->renderJobService->recentCardsFor(auth()->user(), 'face-restoration', 4);
        $imageModels = $this->aiSettingsService->getEnabledImageModels();

        return view('panels.user.face-restoration', compact('recentEnhancements', 'imageModels'));
    }

    public function backgroundRemoval(): View
    {
        $recentEnhancements = $this->renderJobService->recentCardsFor(auth()->user(), 'background-removal', 4);
        $imageModels = $this->aiSettingsService->getEnabledImageModels();

        return view('panels.user.background-removal', compact('recentEnhancements', 'imageModels'));
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

    public function checkout(Request $request): View|RedirectResponse
    {
        $type = $request->query('type') === 'plan' ? 'plan' : 'pack';
        $identifier = $request->query($type === 'plan' ? 'plan' : 'pack');
        $gateway = $request->query('gateway');

        $order = $this->creditCheckoutService->orderFor($type, $identifier, $gateway);

        if (! $order) {
            return redirect()->route('user.billing')
                ->with('status', __('Choose a credit pack or plan to check out.'));
        }

        $creditSummary = $this->billingService->workspaceFor(auth()->user())['credit_summary'] ?? [];

        return view('panels.user.checkout', compact('order', 'creditSummary'));
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
