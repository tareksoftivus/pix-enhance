<x-layouts.user :title="__('History')" :search-placeholder="__('Search activity')">
    @php
        $events = [
            ['type' => 'render', 'icon' => 'wand-sparkles', 'title' => __('Enhanced palm-grove.jpg'), 'detail' => __('Upscaler · 8× · 7680 × 5760'), 'when' => __('12 min ago'), 'meta' => '2 ' . __('credits')],
            ['type' => 'render', 'icon' => 'scan-face', 'title' => __('Restored studio-portrait.png'), 'detail' => __('Face restoration · 4×'), 'when' => __('1 hr ago'), 'meta' => '3 ' . __('credits')],
            ['type' => 'billing', 'icon' => 'credit-card', 'title' => __('Purchased 500 credits'), 'detail' => __('Boost pack · Visa •••• 4242'), 'when' => __('3 hrs ago'), 'meta' => '$25.00'],
            ['type' => 'render', 'icon' => 'eraser', 'title' => __('Removed background from lookbook-03.png'), 'detail' => __('Background removal · PNG'), 'when' => __('Yesterday'), 'meta' => '1 ' . __('credit')],
            ['type' => 'support', 'icon' => 'life-buoy', 'title' => __('Opened ticket TKT-100001'), 'detail' => __('Unable to reset my password'), 'when' => __('Yesterday'), 'meta' => null],
            ['type' => 'security', 'icon' => 'shield-check', 'title' => __('Signed in from a new device'), 'detail' => __('Chrome on macOS · Dhaka, Bangladesh'), 'when' => __('2 days ago'), 'meta' => null],
            ['type' => 'render', 'icon' => 'wand-sparkles', 'title' => __('Enhanced alpine-ridge.jpg'), 'detail' => __('Upscaler · 4× · 3840 × 2880'), 'when' => __('3 days ago'), 'meta' => '2 ' . __('credits')],
            ['type' => 'billing', 'icon' => 'credit-card', 'title' => __('Studio plan renewed'), 'detail' => __('Monthly subscription · Visa •••• 4242'), 'when' => __('4 days ago'), 'meta' => '$49.00'],
            ['type' => 'account', 'icon' => 'user', 'title' => __('Updated profile details'), 'detail' => __('Changed display name and avatar'), 'when' => __('5 days ago'), 'meta' => null],
            ['type' => 'render', 'icon' => 'wand-sparkles', 'title' => __('Enhanced beach-cove.jpg'), 'detail' => __('Upscaler · 4× · 3840 × 2880'), 'when' => __('2 days ago'), 'meta' => '2 ' . __('credits')],
            ['type' => 'support', 'icon' => 'circle-check', 'title' => __('Ticket TKT-099842 resolved'), 'detail' => __('Billing question about invoice #INV-0042'), 'when' => __('1 week ago'), 'meta' => null],
            ['type' => 'security', 'icon' => 'key-round', 'title' => __('Two-factor authentication enabled'), 'detail' => __('Authenticator app added to your account'), 'when' => __('1 week ago'), 'meta' => null],
        ];

        $typeMeta = [
            'render' => ['label' => __('Renders'), 'badge' => 'badge-primary'],
            'billing' => ['label' => __('Billing'), 'badge' => 'badge-success'],
            'support' => ['label' => __('Support'), 'badge' => 'badge-info'],
            'security' => ['label' => __('Security'), 'badge' => 'badge-warning'],
            'account' => ['label' => __('Account'), 'badge' => ''],
        ];

        $counts = collect($events)->countBy('type');
    @endphp

    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('History') }}</h1>
            <p class="dash__subtitle">
                {{ __('A timeline of everything that happened on your account — renders, billing, support and security.') }}
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
                <span class="dash-stat__value">{{ count($events) }}</span>
                <span class="dash-stat__label">{{ __('Total events') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon dash-stat__icon-accent" aria-hidden="true"><i data-lucide="wand-sparkles"></i></span>
            <span>
                <span class="dash-stat__value">{{ $counts->get('render', 0) }}</span>
                <span class="dash-stat__label">{{ __('Renders') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon" aria-hidden="true"><i data-lucide="credit-card"></i></span>
            <span>
                <span class="dash-stat__value">{{ $counts->get('billing', 0) }}</span>
                <span class="dash-stat__label">{{ __('Billing events') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon dash-stat__icon-accent" aria-hidden="true"><i data-lucide="shield-check"></i></span>
            <span>
                <span class="dash-stat__value">{{ $counts->get('security', 0) }}</span>
                <span class="dash-stat__label">{{ __('Security events') }}</span>
            </span>
        </div>
    </div>

    <section class="panel" aria-labelledby="history-title">
        <div class="panel__head">
            <h2 class="panel__title" id="history-title">
                <i data-lucide="clock"></i>
                {{ __('Activity timeline') }}
            </h2>
            <p class="panel__subtitle">{{ __('Filter by type to find a specific render, invoice or account change.') }}</p>
        </div>

        <div class="panel__body">
            <div class="tabs tabs-underline" x-data="tabs('all')">
                <div class="tabs__list" role="tablist" aria-label="{{ __('Filter activity by type') }}" @keydown="onKeydown">
                    @foreach ([
                        ['key' => 'all', 'icon' => 'layout-grid', 'label' => __('All'), 'count' => count($events)],
                        ['key' => 'render', 'icon' => 'wand-sparkles', 'label' => __('Renders'), 'count' => $counts->get('render', 0)],
                        ['key' => 'billing', 'icon' => 'credit-card', 'label' => __('Billing'), 'count' => $counts->get('billing', 0)],
                        ['key' => 'support', 'icon' => 'life-buoy', 'label' => __('Support'), 'count' => $counts->get('support', 0)],
                        ['key' => 'security', 'icon' => 'shield-check', 'label' => __('Security'), 'count' => $counts->get('security', 0)],
                    ] as $tab)
                        <button type="button" class="tabs__tab" role="tab" id="history-tab-{{ $tab['key'] }}"
                                :class="isActive('{{ $tab['key'] }}') && 'is-active'" :aria-selected="isActive('{{ $tab['key'] }}')"
                                :tabindex="isActive('{{ $tab['key'] }}') ? 0 : -1" aria-controls="history-panel-{{ $tab['key'] }}"
                                @click="select('{{ $tab['key'] }}')">
                            <i data-lucide="{{ $tab['icon'] }}"></i>
                            {{ $tab['label'] }}
                            @if ($tab['count'] > 0)
                                <span class="tabs__count">{{ $tab['count'] }}</span>
                            @endif
                        </button>
                    @endforeach
                </div>

                <div class="tabs__panel" role="tabpanel" id="history-panel-all" aria-labelledby="history-tab-all" x-show="isActive('all')" x-cloak>
                    <div class="timeline">
                        @foreach ($events as $event)
                            @include('panels.user.partials.history-event', ['event' => $event, 'typeMeta' => $typeMeta])
                        @endforeach
                    </div>
                </div>

                @foreach (['render', 'billing', 'support', 'security'] as $type)
                    <div class="tabs__panel" role="tabpanel" id="history-panel-{{ $type }}" aria-labelledby="history-tab-{{ $type }}"
                         x-show="isActive('{{ $type }}')" x-cloak>
                        @php $filtered = collect($events)->where('type', $type); @endphp

                        @if ($filtered->isEmpty())
                            <div class="empty-state">
                                <span class="empty-state__icon" aria-hidden="true"><i data-lucide="clock"></i></span>
                                <h3>{{ __('No :type activity yet', ['type' => strtolower($typeMeta[$type]['label'])]) }}</h3>
                                <p>{{ __('Activity of this type will appear here as it happens.') }}</p>
                            </div>
                        @else
                            <div class="timeline">
                                @foreach ($filtered as $event)
                                    @include('panels.user.partials.history-event', ['event' => $event, 'typeMeta' => $typeMeta])
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="panel__foot">
            <p class="pagination__meta">{{ __('Showing 1-:count of 248 events', ['count' => count($events)]) }}</p>

            <nav class="pagination" aria-label="{{ __('Activity pages') }}">
                <span class="pagination__item is-disabled" aria-disabled="true" aria-label="{{ __('Previous page') }}">
                    <i data-lucide="chevron-left"></i>
                </span>
                <span class="pagination__item is-active" aria-current="page">1</span>
                <a class="pagination__item" href="#">2</a>
                <a class="pagination__item" href="#">3</a>
                <span class="pagination__gap">...</span>
                <a class="pagination__item" href="#">21</a>
                <a class="pagination__item" href="#" aria-label="{{ __('Next page') }}">
                    <i data-lucide="chevron-right"></i>
                </a>
            </nav>
        </div>
    </section>
</x-layouts.user>
