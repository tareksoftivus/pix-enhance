<x-layouts.user :title="__('History')" :search-placeholder="__('Search activity')">
    @php
        $events = $history['events'];
        $counts = $history['counts'];
        $stats = $history['stats'];
        $filters = $history['filters'];
        $activeType = $filters['type'];
        $search = $filters['search'];

        $typeMeta = [
            'render' => ['label' => __('Renders'), 'badge' => 'badge-primary'],
            'billing' => ['label' => __('Billing'), 'badge' => 'badge-success'],
            'support' => ['label' => __('Support'), 'badge' => 'badge-info'],
            'security' => ['label' => __('Security'), 'badge' => 'badge-warning'],
            'account' => ['label' => __('Account'), 'badge' => ''],
        ];

        $tabs = [
            ['key' => 'all', 'icon' => 'layout-grid', 'label' => __('All')],
            ['key' => 'render', 'icon' => 'wand-sparkles', 'label' => __('Renders')],
            ['key' => 'billing', 'icon' => 'credit-card', 'label' => __('Billing')],
            ['key' => 'support', 'icon' => 'life-buoy', 'label' => __('Support')],
            ['key' => 'security', 'icon' => 'shield-check', 'label' => __('Security')],
            ['key' => 'account', 'icon' => 'user', 'label' => __('Account')],
        ];

        $queryFor = fn (string $type) => array_filter([
            'type' => $type === 'all' ? null : $type,
            'search' => $search ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    @endphp

    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('History') }}</h1>
            <p class="dash__subtitle">
                {{ __('A timeline of account, billing, support and security activity from your workspace.') }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="{{ route('user.projects') }}">
                <i data-lucide="folder"></i>
                {{ __('View projects') }}
            </a>
        </div>
    </div>

    <div class="dash-stats">
        <div class="dash-stat">
            <span class="dash-stat__icon" aria-hidden="true"><i data-lucide="activity"></i></span>
            <span>
                <span class="dash-stat__value">{{ number_format($stats['all'] ?? 0) }}</span>
                <span class="dash-stat__label">{{ __('Total events') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon dash-stat__icon-accent" aria-hidden="true"><i data-lucide="life-buoy"></i></span>
            <span>
                <span class="dash-stat__value">{{ number_format($stats['support'] ?? 0) }}</span>
                <span class="dash-stat__label">{{ __('Support events') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon" aria-hidden="true"><i data-lucide="credit-card"></i></span>
            <span>
                <span class="dash-stat__value">{{ number_format($stats['billing'] ?? 0) }}</span>
                <span class="dash-stat__label">{{ __('Billing events') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon dash-stat__icon-accent" aria-hidden="true"><i data-lucide="shield-check"></i></span>
            <span>
                <span class="dash-stat__value">{{ number_format($stats['security'] ?? 0) }}</span>
                <span class="dash-stat__label">{{ __('Security events') }}</span>
            </span>
        </div>
    </div>

    <section class="panel" aria-labelledby="history-title">
        <div class="panel__head">
            <div>
                <h2 class="panel__title" id="history-title">
                    <i data-lucide="clock"></i>
                    {{ __('Activity timeline') }}
                </h2>
                <p class="panel__subtitle">{{ __('Filter by type or search for a ticket, payment, device or account event.') }}</p>
            </div>

            <form class="cluster cluster-sm" action="{{ route('user.history') }}" method="get" role="search">
                @if ($activeType !== 'all')
                    <input type="hidden" name="type" value="{{ $activeType }}">
                @endif

                <label class="sr-only" for="history-search">{{ __('Search activity') }}</label>
                <div class="input-group">
                    <span class="input-group__icon" aria-hidden="true"><i data-lucide="search"></i></span>
                    <input class="input input-sm" type="search" id="history-search" name="search"
                           value="{{ $search }}" placeholder="{{ __('Search activity') }}" autocomplete="off">
                </div>

                <button type="submit" class="btn btn-primary btn-sm" data-ripple>
                    <i data-lucide="search"></i>
                    {{ __('Search') }}
                </button>

                @if ($search !== '')
                    <a class="btn btn-outline btn-sm" href="{{ route('user.history', array_filter([
                        'type' => $activeType === 'all' ? null : $activeType,
                    ])) }}">
                        <i data-lucide="x"></i>
                        {{ __('Clear') }}
                    </a>
                @endif
            </form>
        </div>

        <div class="panel__body">
            <div class="tabs tabs-underline">
                <div class="tabs__list" role="tablist" aria-label="{{ __('Filter activity by type') }}">
                    @foreach ($tabs as $tab)
                        @php
                            $isActive = $activeType === $tab['key'];
                            $tabQuery = $queryFor($tab['key']);
                        @endphp

                        <a class="tabs__tab {{ $isActive ? 'is-active' : '' }}" role="tab"
                           id="history-tab-{{ $tab['key'] }}" aria-selected="{{ $isActive ? 'true' : 'false' }}"
                           aria-controls="history-panel" href="{{ route('user.history', $tabQuery) }}">
                            <i data-lucide="{{ $tab['icon'] }}"></i>
                            {{ $tab['label'] }}
                            @if (($counts[$tab['key']] ?? 0) > 0)
                                <span class="tabs__count">{{ number_format($counts[$tab['key']]) }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div class="tabs__panel" role="tabpanel" id="history-panel" aria-labelledby="history-tab-{{ $activeType }}">
                    @if ($events->isEmpty())
                        <div class="empty-state">
                            <span class="empty-state__icon" aria-hidden="true"><i data-lucide="clock"></i></span>
                            <h3>
                                @if ($search !== '')
                                    {{ __('No activity matches your search') }}
                                @elseif ($activeType === 'all')
                                    {{ __('No activity yet') }}
                                @else
                                    {{ __('No :type activity yet', ['type' => strtolower($typeMeta[$activeType]['label'] ?? $activeType)]) }}
                                @endif
                            </h3>
                            <p>
                                @if ($activeType === 'render')
                                    {{ __('Render history will appear here once the render jobs module is connected.') }}
                                @else
                                    {{ __('New account activity will show up here as it happens.') }}
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="timeline">
                            @foreach ($events as $event)
                                @include('panels.user.partials.history-event', ['event' => $event, 'typeMeta' => $typeMeta])
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if ($events->total() > 0)
            @php
                $currentPage = $events->currentPage();
                $lastPage = $events->lastPage();
                $windowStart = max(2, $currentPage - 1);
                $windowEnd = min($lastPage - 1, $currentPage + 1);
            @endphp

            <div class="panel__foot">
                <p class="pagination__meta">
                    {{ __('Showing :first-:last of :total events', [
                        'first' => number_format($events->firstItem()),
                        'last' => number_format($events->lastItem()),
                        'total' => number_format($events->total()),
                    ]) }}
                </p>

                @if ($lastPage > 1)
                    <nav class="pagination" aria-label="{{ __('Activity pages') }}">
                        @if ($events->onFirstPage())
                            <span class="pagination__item is-disabled" aria-disabled="true" aria-label="{{ __('Previous page') }}">
                                <i data-lucide="chevron-left"></i>
                            </span>
                        @else
                            <a class="pagination__item" href="{{ $events->previousPageUrl() }}" aria-label="{{ __('Previous page') }}">
                                <i data-lucide="chevron-left"></i>
                            </a>
                        @endif

                        <a class="pagination__item {{ $currentPage === 1 ? 'is-active' : '' }}" href="{{ $events->url(1) }}" @if ($currentPage === 1) aria-current="page" @endif>1</a>

                        @if ($windowStart > 2)
                            <span class="pagination__gap">...</span>
                        @endif

                        @for ($page = $windowStart; $page <= $windowEnd; $page++)
                            <a class="pagination__item {{ $currentPage === $page ? 'is-active' : '' }}" href="{{ $events->url($page) }}" @if ($currentPage === $page) aria-current="page" @endif>{{ $page }}</a>
                        @endfor

                        @if ($windowEnd < $lastPage - 1)
                            <span class="pagination__gap">...</span>
                        @endif

                        @if ($lastPage > 1)
                            <a class="pagination__item {{ $currentPage === $lastPage ? 'is-active' : '' }}" href="{{ $events->url($lastPage) }}" @if ($currentPage === $lastPage) aria-current="page" @endif>{{ $lastPage }}</a>
                        @endif

                        @if ($events->hasMorePages())
                            <a class="pagination__item" href="{{ $events->nextPageUrl() }}" aria-label="{{ __('Next page') }}">
                                <i data-lucide="chevron-right"></i>
                            </a>
                        @else
                            <span class="pagination__item is-disabled" aria-disabled="true" aria-label="{{ __('Next page') }}">
                                <i data-lucide="chevron-right"></i>
                            </span>
                        @endif
                    </nav>
                @endif
            </div>
        @endif
    </section>
</x-layouts.user>
