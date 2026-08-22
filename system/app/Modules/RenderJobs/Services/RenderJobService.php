<?php

namespace App\Modules\RenderJobs\Services;

use App\Models\User;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use App\Modules\Credits\Models\CreditReservation;
use App\Modules\Credits\Services\CreditService;
use App\Modules\RenderJobs\Models\RenderJob;
use App\Modules\SystemNotifications\Services\UserSystemNotificationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class RenderJobService
{
    public function __construct(
        protected CreditService $creditService,
        protected LocalRenderProcessor $localProcessor,
        protected AiImageRenderProcessor $aiProcessor,
        protected UserSystemNotificationService $systemNotifications
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws InsufficientCreditsException
     */
    public function create(User $user, UploadedFile $source, array $payload): RenderJob
    {
        $tool = $this->normalizeTool((string) ($payload['tool'] ?? 'upscaler'));
        $scale = $this->normalizeScale($tool, (int) ($payload['scale'] ?? 1));
        $format = $this->normalizeFormat((string) ($payload['output_format'] ?? $payload['format'] ?? 'png'));
        $disk = (string) config('render-jobs.disk', 'public');
        $uuid = (string) Str::ulid();
        $extension = strtolower($source->getClientOriginalExtension() ?: $source->extension() ?: 'bin');
        $sourceName = $source->getClientOriginalName() ?: 'source.'.$extension;
        $sourcePath = 'render-jobs/'.$user->id.'/'.now()->format('Y/m').'/sources/'.$uuid.'.'.$extension;
        $source->storeAs(dirname($sourcePath), basename($sourcePath), $disk);

        [$width, $height] = $this->imageDimensions(Storage::disk($disk)->path($sourcePath));
        [$targetWidth, $targetHeight] = $this->targetDimensions($tool, $width, $height, $scale);

        $job = null;

        try {
            $job = RenderJob::query()->create([
                'uuid' => $uuid,
                'user_id' => $user->id,
                'tool' => $tool,
                'status' => 'queued',
                'progress' => 0,
                'model' => $payload['model'] ?? null,
                'scale' => $scale,
                'output_format' => $format,
                'settings' => $this->settingsFromPayload($payload),
                'credits_cost' => $this->costFor($tool, $scale),
                'source_disk' => $disk,
                'source_path' => $sourcePath,
                'source_name' => $sourceName,
                'source_mime' => $source->getMimeType(),
                'source_size' => $source->getSize() ?: 0,
                'source_width' => $width,
                'source_height' => $height,
                'target_width' => $targetWidth,
                'target_height' => $targetHeight,
            ]);

            $reservation = $this->creditService->reserve(
                $user,
                $job->credits_cost,
                $job,
                [
                    'render_job_uuid' => $job->uuid,
                    'tool' => $job->tool,
                    'tool_label' => $job->toolLabel(),
                    'source_name' => $job->source_name,
                ],
                'render-job:'.$job->uuid.':reserve'
            );

            $job->forceFill(['credit_reservation_id' => $reservation->id])->save();

        } catch (InsufficientCreditsException $exception) {
            Storage::disk($disk)->delete($sourcePath);
            $job?->forceDelete();

            throw $exception;
        } catch (Throwable $exception) {
            Storage::disk($disk)->delete($sourcePath);
            $job?->forceDelete();

            throw $exception;
        }

        if (config('render-jobs.sync_processing', true)) {
            return $this->process($job->fresh());
        }

        return $job->fresh();
    }

    public function process(RenderJob $job): RenderJob
    {
        if ($job->isTerminal()) {
            return $job;
        }

        $startedAt = now();

        $job->forceFill([
            'status' => 'processing',
            'progress' => 15,
            'started_at' => $job->started_at ?: $startedAt,
            'error_message' => null,
        ])->save();

        try {
            $output = $this->resolveProcessor($job)->process($job);

            $job->forceFill(array_merge($output, [
                'status' => 'completed',
                'progress' => 100,
                'completed_at' => now(),
                'duration_ms' => max(1, (int) $job->started_at->diffInMilliseconds(now())),
            ]))->save();

            $this->captureReservation($job);

            $freshJob = $job->fresh('user') ?? $job;
            $this->systemNotifications->renderCompleted($freshJob);

            if ($freshJob->user) {
                $this->systemNotifications->creditsLow($freshJob->user, $this->creditService->summaryFor($freshJob->user)['available']);
            }

            return $job->fresh();
        } catch (Throwable $exception) {
            $this->fail($job, $exception->getMessage());

            throw $exception;
        }
    }

    public function cancel(RenderJob $job): RenderJob
    {
        if ($job->isTerminal()) {
            return $job;
        }

        $this->releaseReservation($job);

        $job->forceFill([
            'status' => 'cancelled',
            'progress' => 0,
            'cancelled_at' => now(),
        ])->save();

        $this->systemNotifications->renderCancelled($job->fresh('user') ?? $job);

        return $job->fresh();
    }

    public function delete(RenderJob $job): bool
    {
        if (! $job->isTerminal()) {
            $this->cancel($job);
        }

        return (bool) $job->delete();
    }

    /**
     * Clears every finished render job for the user. Jobs still queued or
     * processing are left untouched — clearing history should never cancel
     * an in-flight render out from under someone.
     */
    public function clearHistoryForUser(User $user): int
    {
        return RenderJob::query()
            ->forUser((int) $user->getKey())
            ->whereIn('status', ['completed', 'failed', 'cancelled'])
            ->delete();
    }

    public function fail(RenderJob $job, string $message): RenderJob
    {
        $this->releaseReservation($job);

        $job->forceFill([
            'status' => 'failed',
            'progress' => 0,
            'failed_at' => now(),
            'error_message' => Str::limit($message, 1000, ''),
        ])->save();

        $this->systemNotifications->renderFailed($job->fresh('user') ?? $job);

        return $job->fresh();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function listForUser(User $user, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return RenderJob::query()
            ->forUser((int) $user->getKey())
            ->byStatus($this->normalizeStatus($filters['status'] ?? null))
            ->byTool($this->normalizeNullableTool($filters['tool'] ?? null))
            ->when(! empty($filters['search']), function ($query) use ($filters): void {
                $search = (string) $filters['search'];

                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('source_name', 'like', "%{$search}%")
                        ->orWhere('output_name', 'like', "%{$search}%")
                        ->orWhere('tool', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%");
                });
            })
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function listForAdmin(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return RenderJob::query()
            ->with('user')
            ->byStatus($this->normalizeStatus($filters['status'] ?? null))
            ->byTool($this->normalizeNullableTool($filters['tool'] ?? null))
            ->when(! empty($filters['search']), function ($query) use ($filters): void {
                $search = (string) $filters['search'];

                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('uuid', 'like', "%{$search}%")
                        ->orWhere('source_name', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search): void {
                            $userQuery->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @return array<string, int|float>
     */
    public function summaryFor(User $user): array
    {
        $base = RenderJob::query()->forUser((int) $user->getKey());
        $completed = (clone $base)->where('status', 'completed');
        $averageMs = (int) (clone $completed)->whereNotNull('duration_ms')->avg('duration_ms');

        return [
            'total' => (clone $base)->count(),
            'completed' => (clone $completed)->count(),
            'in_progress' => (clone $base)->whereIn('status', ['queued', 'processing'])->count(),
            'failed' => (clone $base)->where('status', 'failed')->count(),
            'cancelled' => (clone $base)->where('status', 'cancelled')->count(),
            'storage_bytes' => (int) (clone $base)->sum(DB::raw('COALESCE(source_size, 0) + COALESCE(output_size, 0)')),
            'average_ms' => $averageMs,
        ];
    }

    /**
     * @return array<string, int>
     */
    public function statusCountsFor(User $user): array
    {
        $counts = array_fill_keys(['all', 'completed', 'processing', 'queued', 'failed', 'cancelled'], 0);

        foreach (RenderJob::query()->forUser((int) $user->getKey())->selectRaw('status, count(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status') as $status => $count) {
            $counts[$status] = (int) $count;
            $counts['all'] += (int) $count;
        }

        return $counts;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function recentCardsFor(User $user, ?string $tool = null, int $limit = 6): Collection
    {
        return RenderJob::query()
            ->forUser((int) $user->getKey())
            ->byTool($this->normalizeNullableTool($tool))
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(fn (RenderJob $job): array => $this->cardFor($job));
    }

    /**
     * @return array<string, mixed>
     */
    public function projectsFor(User $user, array $filters = [], int $perPage = 12): array
    {
        $jobs = $this->listForUser($user, $filters, $perPage);

        return [
            'jobs' => $jobs,
            'cards' => $jobs->getCollection()->map(fn (RenderJob $job): array => $this->cardFor($job)),
            'counts' => $this->statusCountsFor($user),
            'summary' => $this->summaryFor($user),
            'filters' => [
                'status' => $this->normalizeStatus($filters['status'] ?? null) ?? 'all',
                'tool' => $this->normalizeNullableTool($filters['tool'] ?? null) ?? 'all',
                'search' => is_string($filters['search'] ?? null) ? trim($filters['search']) : '',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function cardFor(RenderJob $job): array
    {
        return [
            'id' => $job->uuid,
            'file' => $job->displayName(),
            'thumb' => $job->outputUrl() ?: $job->sourceUrl(),
            'source_url' => $job->sourceUrl(),
            'output_url' => $job->outputUrl(),
            'download_url' => $job->isCompleted() ? route('user.render-jobs.download', $job) : null,
            'show_url' => route('user.render-jobs.show', $job),
            'alt' => __('Render preview for :file', ['file' => $job->displayName()]),
            'status' => $job->status,
            'progress' => $job->progress,
            'scale' => $job->scale > 1 ? $job->scale.'x' : strtoupper($job->output_format),
            'tool' => $job->toolLabel(),
            'size' => $job->outputDimensions(),
            'when' => $job->created_at?->diffForHumans() ?? __('Date unavailable'),
            'credits' => $job->credits_cost,
            'error' => $job->error_message,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function responsePayload(RenderJob $job): array
    {
        return [
            'id' => $job->uuid,
            'status' => $job->status,
            'status_label' => RenderJob::statuses()[$job->status]['label'] ?? Str::headline($job->status),
            'source_url' => $job->sourceUrl(),
            'output_url' => $job->outputUrl(),
            'download_url' => $job->isCompleted() ? route('user.render-jobs.download', $job) : null,
            'show_url' => route('user.render-jobs.show', $job),
            'file_name' => $job->source_name,
            'output_name' => $job->displayName(),
            'source_size' => $job->source_width && $job->source_height ? $job->source_width.' x '.$job->source_height : null,
            'output_size' => $job->outputDimensions(),
            'credits_cost' => $job->credits_cost,
            'rendered_in' => $this->formatDuration($job->duration_ms),
            'message' => $job->isCompleted() ? __('Enhancement complete') : __('Render job created'),
        ];
    }

    public function costFor(string $tool, int $scale): int
    {
        $definition = config("render-jobs.tools.{$tool}", []);

        if (isset($definition['fixed_cost'])) {
            return (int) $definition['fixed_cost'];
        }

        return (int) ($definition['scale_costs'][$scale] ?? $definition['base_cost'] ?? 1);
    }

    /**
     * Pick the processor for a job: the AI processor when the job's model
     * refers to an enabled, image-capable provider model, otherwise the
     * deterministic local GD processor (also the fallback for 'auto').
     */
    protected function resolveProcessor(RenderJob $job): LocalRenderProcessor|AiImageRenderProcessor
    {
        return $this->aiProcessor->canHandle($job) ? $this->aiProcessor : $this->localProcessor;
    }

    protected function captureReservation(RenderJob $job): void
    {
        $reservation = $job->creditReservation;

        if ($reservation instanceof CreditReservation && $reservation->isActive()) {
            $this->creditService->capture($reservation, 'render_spend');
        }
    }

    protected function releaseReservation(RenderJob $job): void
    {
        $reservation = $job->creditReservation;

        if ($reservation instanceof CreditReservation && $reservation->isActive()) {
            $this->creditService->release($reservation);
        }
    }

    protected function normalizeTool(string $tool): string
    {
        return array_key_exists($tool, config('render-jobs.tools', [])) ? $tool : 'upscaler';
    }

    protected function normalizeNullableTool(mixed $tool): ?string
    {
        return is_string($tool) && array_key_exists($tool, config('render-jobs.tools', [])) ? $tool : null;
    }

    protected function normalizeScale(string $tool, int $scale): int
    {
        $allowed = config("render-jobs.tools.{$tool}.allowed_scales", [1]);

        return in_array($scale, $allowed, true) ? $scale : (int) ($allowed[0] ?? 1);
    }

    protected function normalizeFormat(string $format): string
    {
        return in_array($format, config('render-jobs.output_formats', []), true) ? $format : 'png';
    }

    protected function normalizeStatus(mixed $status): ?string
    {
        return is_string($status) && array_key_exists($status, RenderJob::statuses()) ? $status : null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function settingsFromPayload(array $payload): array
    {
        return [
            'detail' => isset($payload['detail']) ? (int) $payload['detail'] : null,
            'fidelity' => isset($payload['fidelity']) ? (int) $payload['fidelity'] : null,
            'edge' => isset($payload['edge']) ? (int) $payload['edge'] : null,
            'face' => (bool) ($payload['face'] ?? false),
            'denoise' => (bool) ($payload['denoise'] ?? false),
            'colour' => (bool) ($payload['colour'] ?? false),
            'hair' => (bool) ($payload['hair'] ?? false),
            'shadow' => (bool) ($payload['shadow'] ?? false),
            'backdrop' => $payload['backdrop'] ?? null,
        ];
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    protected function imageDimensions(string $path): array
    {
        $size = @getimagesize($path);

        return is_array($size) ? [(int) $size[0], (int) $size[1]] : [null, null];
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    protected function targetDimensions(string $tool, ?int $width, ?int $height, int $scale): array
    {
        if (! $width || ! $height) {
            return [null, null];
        }

        $factor = config("render-jobs.tools.{$tool}.no_scale", false) ? 1 : $scale;

        return [$width * $factor, $height * $factor];
    }

    protected function formatDuration(?int $milliseconds): ?string
    {
        if (! $milliseconds) {
            return null;
        }

        return number_format($milliseconds / 1000, 1).'s';
    }
}
