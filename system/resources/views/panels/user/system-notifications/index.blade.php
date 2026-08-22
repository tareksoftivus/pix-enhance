<x-layouts.user :title="__('Notifications')" :search-placeholder="__('Search notifications')">
    @php
        $activeStatus = request('status', 'all');
        $tabs = [
            ['key' => 'all', 'icon' => 'inbox', 'label' => __('All'), 'query' => []],
            ['key' => 'unread', 'icon' => 'mail', 'label' => __('Unread'), 'query' => ['status' => 'unread']],
            ['key' => 'read', 'icon' => 'mail-open', 'label' => __('Read'), 'query' => ['status' => 'read']],
        ];

        $typeMeta = [
            'success' => ['badge' => 'badge-success', 'icon' => 'circle-check'],
            'warning' => ['badge' => 'badge-warning', 'icon' => 'triangle-alert'],
            'danger' => ['badge' => 'badge-danger', 'icon' => 'circle-alert'],
            'info' => ['badge' => 'badge-info', 'icon' => 'info'],
        ];
    @endphp

    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Notifications') }}</h1>
            <p class="dash__subtitle">
                {{ __('Review system updates, render alerts and account messages from your workspace.') }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <button type="button" class="btn btn-outline btn-sm"
                    x-data
                    @click="
                        fetch('{{ route('user.system-notifications.mark-all-read') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                        }).then(() => window.location.reload())
                    ">
                <i data-lucide="check-check"></i>
                {{ __('Mark all read') }}
            </button>
        </div>
    </div>

    <section class="panel" aria-labelledby="notifications-title">
        <div class="panel__head">
            <div>
                <h2 class="panel__title" id="notifications-title">
                    <i data-lucide="bell"></i>
                    {{ __('System notifications') }}
                </h2>
                <p class="panel__subtitle">{{ __('Newest messages appear first so you can catch up quickly.') }}</p>
            </div>

            @if ($notifications->total() > 0)
                <span class="badge badge-sm badge-primary">
                    <i data-lucide="inbox"></i>
                    {{ trans_choice(':count notification|:count notifications', $notifications->total(), ['count' => number_format($notifications->total())]) }}
                </span>
            @endif
        </div>

        <div class="panel__body panel__body-flush">
            <div class="tabs tabs-underline notifications-tabs">
                <div class="tabs__list" role="tablist" aria-label="{{ __('Filter notifications by status') }}">
                    @foreach ($tabs as $tab)
                        @php $isActive = $activeStatus === $tab['key'] || ($tab['key'] === 'all' && ! request('status')); @endphp

                        <a class="tabs__tab {{ $isActive ? 'is-active' : '' }}" role="tab"
                           aria-selected="{{ $isActive ? 'true' : 'false' }}"
                           href="{{ route('user.system-notifications.index', $tab['query']) }}">
                            <i data-lucide="{{ $tab['icon'] }}"></i>
                            {{ $tab['label'] }}
                        </a>
                    @endforeach
                </div>

                <div class="tabs__panel" role="tabpanel">
                    @if ($notifications->isEmpty())
                        <div class="notifications-empty">
                            <div class="empty-state">
                                <span class="empty-state__icon" aria-hidden="true"><i data-lucide="bell-off"></i></span>
                                <h3>{{ __('No notifications found') }}</h3>
                                <p>{{ __('System messages, alerts and account updates will appear here when they arrive.') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="notification-list">
                            @foreach ($notifications as $notification)
                                @php
                                    $type = $notification->getType();
                                    $meta = $typeMeta[$type] ?? ['badge' => 'badge-primary', 'icon' => 'bell'];
                                    $isRead = $notification->isRead();
                                @endphp

                                <a href="{{ $notification->getUrl() ?? '#' }}"
                                   class="notification-row {{ ! $isRead ? 'is-unread' : '' }}">
                                    <span class="notification-row__icon {{ $meta['badge'] }}" aria-hidden="true">
                                        <i data-lucide="{{ $meta['icon'] }}"></i>
                                    </span>

                                    <span class="notification-row__content">
                                        <span class="notification-row__head">
                                            <span class="notification-row__title">{{ $notification->getTitle() }}</span>
                                            <span class="notification-row__meta">
                                                @unless ($isRead)
                                                    <span class="notification-row__dot" aria-label="{{ __('Unread') }}"></span>
                                                @endunless
                                                <time datetime="{{ $notification->created_at?->toIso8601String() }}">
                                                    {{ $notification->created_at?->diffForHumans() }}
                                                </time>
                                            </span>
                                        </span>

                                        <span class="notification-row__body">{{ $notification->getBody() }}</span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if ($notifications->total() > 0)
            @php
                $currentPage = $notifications->currentPage();
                $lastPage = $notifications->lastPage();
                $windowStart = max(2, $currentPage - 1);
                $windowEnd = min($lastPage - 1, $currentPage + 1);
            @endphp

            <div class="panel__foot">
                <p class="pagination__meta">
                    {{ __('Showing :first-:last of :total notifications', [
                        'first' => number_format($notifications->firstItem()),
                        'last' => number_format($notifications->lastItem()),
                        'total' => number_format($notifications->total()),
                    ]) }}
                </p>

                @if ($lastPage > 1)
                    <nav class="pagination" aria-label="{{ __('Notification pages') }}">
                        @if ($notifications->onFirstPage())
                            <span class="pagination__item is-disabled" aria-disabled="true" aria-label="{{ __('Previous page') }}">
                                <i data-lucide="chevron-left"></i>
                            </span>
                        @else
                            <a class="pagination__item" href="{{ $notifications->previousPageUrl() }}" aria-label="{{ __('Previous page') }}">
                                <i data-lucide="chevron-left"></i>
                            </a>
                        @endif

                        <a class="pagination__item {{ $currentPage === 1 ? 'is-active' : '' }}" href="{{ $notifications->url(1) }}" @if ($currentPage === 1) aria-current="page" @endif>1</a>

                        @if ($windowStart > 2)
                            <span class="pagination__gap">...</span>
                        @endif

                        @for ($page = $windowStart; $page <= $windowEnd; $page++)
                            <a class="pagination__item {{ $currentPage === $page ? 'is-active' : '' }}" href="{{ $notifications->url($page) }}" @if ($currentPage === $page) aria-current="page" @endif>{{ $page }}</a>
                        @endfor

                        @if ($windowEnd < $lastPage - 1)
                            <span class="pagination__gap">...</span>
                        @endif

                        @if ($lastPage > 1)
                            <a class="pagination__item {{ $currentPage === $lastPage ? 'is-active' : '' }}" href="{{ $notifications->url($lastPage) }}" @if ($currentPage === $lastPage) aria-current="page" @endif>{{ $lastPage }}</a>
                        @endif

                        @if ($notifications->hasMorePages())
                            <a class="pagination__item" href="{{ $notifications->nextPageUrl() }}" aria-label="{{ __('Next page') }}">
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
