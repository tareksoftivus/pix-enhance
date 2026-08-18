<?php

namespace App\Modules\RenderJobs\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\RenderJobs\Models\RenderJob;
use App\Modules\RenderJobs\Services\RenderJobService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RenderJobsController extends Controller
{
    public function __construct(
        protected RenderJobService $renderJobs
    ) {
        $this->middleware('permission:render-jobs.view')->only('index');
        $this->middleware('permission:render-jobs.manage')->only(['retry', 'cancel']);
    }

    public function index(Request $request): View
    {
        $jobs = $this->renderJobs->listForAdmin($request->query(), 20);
        $statuses = RenderJob::statuses();
        $tools = config('render-jobs.tools', []);

        return view('render-jobs::admin.index', compact('jobs', 'statuses', 'tools'));
    }

    public function retry(RenderJob $renderJob): RedirectResponse
    {
        if ($renderJob->status !== 'failed') {
            return back()->with('error', __('Only failed render jobs can be retried.'));
        }

        $renderJob->forceFill([
            'status' => 'queued',
            'progress' => 0,
            'failed_at' => null,
            'error_message' => null,
        ])->save();

        $this->renderJobs->process($renderJob->fresh());

        return back()->with('success', __('Render job retried.'));
    }

    public function cancel(RenderJob $renderJob): RedirectResponse
    {
        $this->renderJobs->cancel($renderJob);

        return back()->with('success', __('Render job cancelled.'));
    }
}
