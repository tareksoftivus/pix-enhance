@php
    $meta = $statusMeta[$project['status']] ?? ['label' => \Illuminate\Support\Str::headline($project['status']), 'badge' => 'badge-primary', 'icon' => 'sparkles'];
    $showUrl = $project['show_url'] ?? '#';
    $downloadUrl = $project['download_url'] ?? null;
@endphp

<article class="job-card">
    <div class="job-card__media">
        <img src="{{ $project['thumb'] }}" alt="{{ $project['alt'] }}" width="320" height="320" loading="lazy" decoding="async">

        <span class="badge badge-sm {{ $meta['badge'] }} job-card__status">
            <i data-lucide="{{ $meta['icon'] }}"></i>
            {{ $project['status'] === 'processing' ? "{$project['progress']}%" : ($project['scale'] ?? $meta['label']) }}
        </span>

        @if ($project['status'] === 'completed')
            <div class="job-card__tools">
                <a class="job-card__tool" href="{{ $showUrl }}" aria-label="{{ __('Open :file', ['file' => $project['file']]) }}">
                    <i data-lucide="eye"></i>
                </a>
                @if ($downloadUrl)
                    <a class="job-card__tool" href="{{ $downloadUrl }}" aria-label="{{ __('Download :file', ['file' => $project['file']]) }}">
                        <i data-lucide="download"></i>
                    </a>
                @endif
            </div>
        @endif
    </div>

    <div class="job-card__body">
        <h3 class="job-card__name">{{ $project['file'] }}</h3>

        @if (in_array($project['status'], ['processing', 'queued'], true))
            <div class="job-card__progress">
                <div class="progress progress-sm" data-progress="{{ $project['progress'] }}">
                    <div class="progress__bar progress__bar-striped"></div>
                </div>
            </div>
        @else
            <div class="job-card__meta">
                <span>{{ $project['size'] ?? $project['tool'] }}</span>
                <span class="job-card__dot" aria-hidden="true"></span>
                <span>{{ $project['when'] }}</span>
            </div>
        @endif
    </div>
</article>
