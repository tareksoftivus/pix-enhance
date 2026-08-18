<x-layouts.user :title="__('Projects')" :search-placeholder="__('Search projects')">
    @php
        $sample = fn (string $file) => asset("assets/frontend/enhance/img/samples/{$file}");

        $projects = [
            ['file' => 'meadow-sunrise.jpg', 'thumb' => $sample('thumb-1.webp'), 'alt' => __('Enhanced landscape at sunrise'), 'status' => 'processing', 'progress' => 62, 'tool' => __('Upscaler'), 'size' => null, 'when' => __('Just now')],
            ['file' => 'palm-grove.jpg', 'thumb' => $sample('thumb-2.webp'), 'alt' => __('Enhanced palm grove'), 'status' => 'completed', 'scale' => '8×', 'tool' => __('Upscaler'), 'size' => '7680 × 5760', 'when' => __('12 min ago')],
            ['file' => 'studio-portrait.png', 'thumb' => $sample('feature-face.webp'), 'alt' => __('Restored portrait'), 'status' => 'completed', 'scale' => '4×', 'tool' => __('Face restoration'), 'size' => null, 'when' => __('1 hr ago')],
            ['file' => 'alpine-ridge.jpg', 'thumb' => $sample('thumb-3.webp'), 'alt' => __('Enhanced mountain range'), 'status' => 'completed', 'scale' => '4×', 'tool' => __('Upscaler'), 'size' => '3840 × 2880', 'when' => __('3 hrs ago')],
            ['file' => 'lookbook-03.png', 'thumb' => $sample('feature-cutout.webp'), 'alt' => __('Photographer cut out from the background'), 'status' => 'completed', 'scale' => 'PNG', 'tool' => __('Background removal'), 'size' => null, 'when' => __('Yesterday')],
            ['file' => 'golden-field.tif', 'thumb' => $sample('thumb-4.webp'), 'alt' => __('Enhanced field at golden hour'), 'status' => 'queued', 'tool' => __('Upscaler'), 'size' => null, 'when' => __('2 in queue')],
            ['file' => 'beach-cove.jpg', 'thumb' => $sample('beach-after.webp'), 'alt' => __('Enhanced beach cove'), 'status' => 'completed', 'scale' => '4×', 'tool' => __('Upscaler'), 'size' => '3840 × 2880', 'when' => __('2 days ago')],
            ['file' => 'valley-mist.jpg', 'thumb' => $sample('valley-after.webp'), 'alt' => __('Enhanced misty valley'), 'status' => 'completed', 'scale' => '4×', 'tool' => __('Upscaler'), 'size' => '3840 × 2160', 'when' => __('3 days ago')],
            ['file' => 'family-portrait.jpg', 'thumb' => $sample('family-after.webp'), 'alt' => __('Restored family portrait'), 'status' => 'completed', 'scale' => '2×', 'tool' => __('Face restoration'), 'size' => '2400 × 1600', 'when' => __('4 days ago')],
            ['file' => 'product-cutout.png', 'thumb' => $sample('bgr-after.webp'), 'alt' => __('Product photo with background removed'), 'status' => 'failed', 'tool' => __('Background removal'), 'size' => null, 'when' => __('5 days ago')],
        ];

        $statusMeta = [
            'completed' => ['label' => __('Completed'), 'badge' => 'badge-success', 'icon' => 'circle-check'],
            'processing' => ['label' => __('Processing'), 'badge' => 'badge-primary', 'icon' => 'refresh-cw'],
            'queued' => ['label' => __('Queued'), 'badge' => 'badge-warning', 'icon' => 'clock'],
            'failed' => ['label' => __('Failed'), 'badge' => 'badge-danger', 'icon' => 'circle-alert'],
        ];

        $counts = collect($projects)->countBy('status');
    @endphp

    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Projects') }}</h1>
            <p class="dash__subtitle">
                {{ __('Every image you have sent through Enhance, in one gallery — filter by status or jump back into a render.') }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="{{ route('user.dashboard') }}#recent">
                <i data-lucide="clock"></i>
                {{ __('History') }}
            </a>
            <label class="btn btn-primary btn-sm" for="studio-file" data-ripple>
                <i data-lucide="cloud-upload"></i>
                {{ __('New project') }}
            </label>
        </div>
    </div>

    <div class="dash-stats">
        <div class="dash-stat">
            <span class="dash-stat__icon" aria-hidden="true"><i data-lucide="folder"></i></span>
            <span>
                <span class="dash-stat__value">{{ count($projects) }}</span>
                <span class="dash-stat__label">{{ __('Total projects') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon dash-stat__icon-accent" aria-hidden="true"><i data-lucide="circle-check"></i></span>
            <span>
                <span class="dash-stat__value">{{ $counts->get('completed', 0) }}</span>
                <span class="dash-stat__label">{{ __('Completed') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon" aria-hidden="true"><i data-lucide="refresh-cw"></i></span>
            <span>
                <span class="dash-stat__value">{{ $counts->get('processing', 0) + $counts->get('queued', 0) }}</span>
                <span class="dash-stat__label">{{ __('In progress') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon dash-stat__icon-accent" aria-hidden="true"><i data-lucide="database"></i></span>
            <span>
                <span class="dash-stat__value">6.1 GB</span>
                <span class="dash-stat__label">{{ __('Storage used') }}</span>
            </span>
        </div>
    </div>

    <section class="panel" aria-labelledby="projects-title">
        <div class="panel__head">
            <h2 class="panel__title" id="projects-title">
                <i data-lucide="folder"></i>
                {{ __('All projects') }}
            </h2>
            <p class="panel__subtitle">{{ __('Filter by status, then open a project to view, share or download it.') }}</p>
        </div>

        <div class="panel__body">
            <div class="tabs tabs-underline" x-data="tabs('all')">
                <div class="tabs__list" role="tablist" aria-label="{{ __('Filter projects by status') }}" @keydown="onKeydown">
                    @foreach ([
                        ['key' => 'all', 'icon' => 'layout-grid', 'label' => __('All'), 'count' => count($projects)],
                        ['key' => 'completed', 'icon' => 'circle-check', 'label' => __('Completed'), 'count' => $counts->get('completed', 0)],
                        ['key' => 'processing', 'icon' => 'refresh-cw', 'label' => __('Processing'), 'count' => $counts->get('processing', 0)],
                        ['key' => 'queued', 'icon' => 'clock', 'label' => __('Queued'), 'count' => $counts->get('queued', 0)],
                        ['key' => 'failed', 'icon' => 'circle-alert', 'label' => __('Failed'), 'count' => $counts->get('failed', 0)],
                    ] as $tab)
                        <button type="button" class="tabs__tab" role="tab" id="project-tab-{{ $tab['key'] }}"
                                :class="isActive('{{ $tab['key'] }}') && 'is-active'" :aria-selected="isActive('{{ $tab['key'] }}')"
                                :tabindex="isActive('{{ $tab['key'] }}') ? 0 : -1" aria-controls="project-panel-{{ $tab['key'] }}"
                                @click="select('{{ $tab['key'] }}')">
                            <i data-lucide="{{ $tab['icon'] }}"></i>
                            {{ $tab['label'] }}
                            @if ($tab['count'] > 0)
                                <span class="tabs__count">{{ $tab['count'] }}</span>
                            @endif
                        </button>
                    @endforeach
                </div>

                <div class="tabs__panel" role="tabpanel" id="project-panel-all" aria-labelledby="project-tab-all" x-show="isActive('all')" x-cloak>
                    <div class="job-grid">
                        @foreach ($projects as $project)
                            @include('panels.user.partials.project-card', ['project' => $project, 'statusMeta' => $statusMeta])
                        @endforeach
                    </div>
                </div>

                @foreach (['completed', 'processing', 'queued', 'failed'] as $status)
                    <div class="tabs__panel" role="tabpanel" id="project-panel-{{ $status }}" aria-labelledby="project-tab-{{ $status }}"
                         x-show="isActive('{{ $status }}')" x-cloak>
                        @php $filtered = collect($projects)->where('status', $status); @endphp

                        @if ($filtered->isEmpty())
                            <div class="empty-state">
                                <span class="empty-state__icon" aria-hidden="true"><i data-lucide="{{ $statusMeta[$status]['icon'] }}"></i></span>
                                <h3>{{ __('No :status projects', ['status' => strtolower($statusMeta[$status]['label'])]) }}</h3>
                                <p>{{ __('Projects will show up here once they reach this status.') }}</p>
                            </div>
                        @else
                            <div class="job-grid">
                                @foreach ($filtered as $project)
                                    @include('panels.user.partials.project-card', ['project' => $project, 'statusMeta' => $statusMeta])
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="panel__foot">
            <p class="pagination__meta">{{ __('Showing 1-:count of :count projects', ['count' => count($projects)]) }}</p>

            <nav class="pagination" aria-label="{{ __('Project pages') }}">
                <span class="pagination__item is-disabled" aria-disabled="true" aria-label="{{ __('Previous page') }}">
                    <i data-lucide="chevron-left"></i>
                </span>
                <span class="pagination__item is-active" aria-current="page">1</span>
                <span class="pagination__item is-disabled" aria-disabled="true" aria-label="{{ __('Next page') }}">
                    <i data-lucide="chevron-right"></i>
                </span>
            </nav>
        </div>
    </section>
</x-layouts.user>
