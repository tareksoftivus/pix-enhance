<?php

namespace App\Modules\RenderJobs\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use App\Modules\RenderJobs\Http\Requests\StoreRenderJobRequest;
use App\Modules\RenderJobs\Models\RenderJob;
use App\Modules\RenderJobs\Services\RenderJobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class RenderJobsController extends Controller
{
    public function __construct(
        protected RenderJobService $renderJobs
    ) {}

    public function store(StoreRenderJobRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $job = $this->renderJobs->create(
                $request->user(),
                $request->file('source'),
                $request->payload()
            );
        } catch (InsufficientCreditsException $exception) {
            return $this->errorResponse($request, $exception->getMessage(), 422, route('user.billing'));
        } catch (Throwable $exception) {
            report($exception);

            return $this->errorResponse($request, __('The render could not be created. Please try again.'), 500);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'job' => $this->renderJobs->responsePayload($job),
            ], 201);
        }

        return redirect()
            ->route('user.render-jobs.show', $job)
            ->with('success', __('Render completed.'));
    }

    public function show(Request $request, RenderJob $renderJob): View
    {
        $this->authorizeOwner($request, $renderJob);

        return view('render-jobs::user.show', [
            'job' => $renderJob,
            'card' => $this->renderJobs->cardFor($renderJob),
        ]);
    }

    public function download(Request $request, RenderJob $renderJob): StreamedResponse
    {
        $this->authorizeOwner($request, $renderJob);

        abort_unless($renderJob->isCompleted() && $renderJob->output_disk && $renderJob->output_path, 404);

        return Storage::disk($renderJob->output_disk)->download(
            $renderJob->output_path,
            $renderJob->output_name ?: $renderJob->source_name
        );
    }

    public function cancel(Request $request, RenderJob $renderJob): RedirectResponse|JsonResponse
    {
        $this->authorizeOwner($request, $renderJob);

        $job = $this->renderJobs->cancel($renderJob);

        if ($request->expectsJson()) {
            return response()->json(['job' => $this->renderJobs->responsePayload($job)]);
        }

        return back()->with('success', __('Render job cancelled.'));
    }

    public function destroy(Request $request, RenderJob $renderJob): RedirectResponse|JsonResponse
    {
        $this->authorizeOwner($request, $renderJob);

        $this->renderJobs->delete($renderJob);

        if ($request->expectsJson()) {
            return response()->json(['message' => __('Render job deleted.')]);
        }

        return redirect()->route('user.projects')->with('success', __('Render job deleted.'));
    }

    protected function authorizeOwner(Request $request, RenderJob $renderJob): void
    {
        abort_unless((int) $renderJob->user_id === (int) $request->user()->id, 404);
    }

    protected function errorResponse(Request $request, string $message, int $status, ?string $redirectUrl = null): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'redirect_url' => $redirectUrl,
            ], $status);
        }

        return back()->withInput()->with('error', $message);
    }
}
