<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Shared\Services\SessionService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected SessionService $sessionService) {}

    public function index(): View
    {
        return view('panels.user.dashboard');
    }

    public function upscaler(): View
    {
        return view('panels.user.upscaler');
    }

    public function faceRestoration(): View
    {
        return view('panels.user.face-restoration');
    }

    public function backgroundRemoval(): View
    {
        return view('panels.user.background-removal');
    }

    public function projects(): View
    {
        return view('panels.user.projects');
    }

    public function history(): View
    {
        return view('panels.user.history');
    }

    public function billing(): View
    {
        return view('panels.user.billing');
    }

    public function settings(): View
    {
        $user = auth()->user();
        $sessions = $this->sessionService->getActiveSessions($user->id);

        return view('panels.user.profile.settings', compact('user', 'sessions'));
    }
}
