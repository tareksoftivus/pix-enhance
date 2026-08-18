<x-layouts.user :title="$job->displayName()" :search-placeholder="__('Search projects')">
    @php
        $statusMeta = \App\Modules\RenderJobs\Models\RenderJob::statuses()[$job->status] ?? ['label' => \Illuminate\Support\Str::headline($job->status), 'badge' => 'badge-primary', 'icon' => 'sparkles'];
    @endphp

    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ $job->displayName() }}</h1>
            <p class="dash__subtitle">
                {{ $job->toolLabel() }} · {{ $job->created_at?->format('M j, Y g:i A') }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="{{ route('user.projects') }}">
                <i data-lucide="folder"></i>
                {{ __('Projects') }}
            </a>

            @if ($job->isCompleted() && $job->outputUrl())
                <a class="btn btn-primary btn-sm" href="{{ route('user.render-jobs.download', $job) }}" data-ripple>
                    <i data-lucide="download"></i>
                    {{ __('Download') }}
                </a>
            @endif
        </div>
    </div>

    <section class="panel">
        <div class="panel__head">
            <h2 class="panel__title">
                <i data-lucide="{{ $statusMeta['icon'] }}"></i>
                {{ __('Render details') }}
            </h2>
            <span class="badge badge-sm {{ $statusMeta['badge'] }}">
                {{ $statusMeta['label'] }}
            </span>
        </div>

        <div class="panel__body">
            <div class="compare" data-compare data-compare-start="50">
                <div class="compare__frame">
                    <img class="compare__layer"
                         src="{{ $job->sourceUrl() }}"
                         alt="{{ __('Original source image') }}"
                         width="1200" height="900" decoding="async">
                    <img class="compare__layer compare__layer-after"
                         src="{{ $job->outputUrl() ?: $job->sourceUrl() }}"
                         alt="{{ __('Rendered output image') }}"
                         width="1200" height="900" decoding="async">
                </div>

                <label class="sr-only" for="render-compare">{{ __('Reveal the processed image') }}</label>
                <input class="compare__range" type="range" id="render-compare" data-compare-range
                       min="0" max="100" value="50" step="0.1"
                       aria-label="{{ __('Compare the original and result') }}">

                <span class="compare__tag compare__tag-before">{{ __('Original') }}</span>
                <span class="compare__tag compare__tag-after">
                    <i data-lucide="sparkles"></i>
                    {{ __('Result') }}
                </span>

                @if ($job->source_width && $job->source_height)
                    <span class="compare__meta compare__meta-before">{{ number_format($job->source_width) }} x {{ number_format($job->source_height) }}</span>
                @endif

                @if ($job->outputDimensions())
                    <span class="compare__meta compare__meta-after">{{ $job->outputDimensions() }}</span>
                @endif

                <span class="compare__handle" aria-hidden="true">
                    <span class="compare__grip"><i data-lucide="move-horizontal"></i></span>
                </span>
            </div>

            <div class="dash-stats mt-lg">
                <div class="dash-stat">
                    <span class="dash-stat__icon" aria-hidden="true"><i data-lucide="coins"></i></span>
                    <span>
                        <span class="dash-stat__value">{{ number_format($job->credits_cost) }}</span>
                        <span class="dash-stat__label">{{ __('Credits') }}</span>
                    </span>
                </div>

                <div class="dash-stat">
                    <span class="dash-stat__icon" aria-hidden="true"><i data-lucide="maximize-2"></i></span>
                    <span>
                        <span class="dash-stat__value">{{ $job->scale }}x</span>
                        <span class="dash-stat__label">{{ __('Scale') }}</span>
                    </span>
                </div>

                <div class="dash-stat">
                    <span class="dash-stat__icon dash-stat__icon-accent" aria-hidden="true"><i data-lucide="file-text"></i></span>
                    <span>
                        <span class="dash-stat__value">{{ strtoupper($job->output_format) }}</span>
                        <span class="dash-stat__label">{{ __('Output') }}</span>
                    </span>
                </div>

                <div class="dash-stat">
                    <span class="dash-stat__icon dash-stat__icon-accent" aria-hidden="true"><i data-lucide="timer"></i></span>
                    <span>
                        <span class="dash-stat__value">{{ $job->duration_ms ? number_format($job->duration_ms / 1000, 1).'s' : __('Pending') }}</span>
                        <span class="dash-stat__label">{{ __('Duration') }}</span>
                    </span>
                </div>
            </div>

            @if ($job->error_message)
                <p class="field__error mt-md">
                    <i data-lucide="circle-alert"></i>
                    {{ $job->error_message }}
                </p>
            @endif
        </div>
    </section>
</x-layouts.user>
