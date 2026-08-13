<?php

namespace App\Panels\User\Controllers;

use App\Http\Controllers\Controller;
use App\Services\WidgetRegistry;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected WidgetRegistry $widgetRegistry) {}

    public function index(): View
    {
        $widgets = $this->widgetRegistry->getForPanel('user', auth()->user());

        return view('panels.user.dashboard', compact('widgets'));
    }
}
