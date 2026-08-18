@php
    $statuses = \App\Modules\Support\Models\SupportTicket::statuses();
    $priorities = \App\Modules\Support\Models\SupportTicket::priorities();
    $statusBadge = fn (string $status) => match ($status) {
        'open' => 'badge-info',
        'pending' => 'badge-warning',
        'resolved' => 'badge-success',
        'closed' => '',
        default => '',
    };
    $priorityBadge = fn (string $priority) => match ($priority) {
        'urgent' => 'badge-danger',
        'high' => 'badge-warning',
        'medium' => 'badge-primary',
        default => '',
    };
    $tickets->appends(request()->query());
    $pageWindow = collect([1, $tickets->currentPage() - 1, $tickets->currentPage(), $tickets->currentPage() + 1, $tickets->lastPage()])
        ->filter(fn (int $page) => $page >= 1 && $page <= $tickets->lastPage())
        ->unique()
        ->sort()
        ->values();
@endphp

<x-layouts.user :title="__('Support')" :search-placeholder="__('Search tickets')">
    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ __('Support') }}</h1>
            <p class="dash__subtitle">
                {{ __('Track conversations with the support team, open a new ticket, and review previous answers.') }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="{{ route('user.settings') }}">
                <i data-lucide="settings"></i>
                {{ __('Settings') }}
            </a>
            <a class="btn btn-primary btn-sm" href="{{ route('user.support-tickets.create') }}" data-ripple>
                <i data-lucide="plus"></i>
                {{ __('New ticket') }}
            </a>
        </div>
    </div>

    <div class="dash-stats">
        <div class="dash-stat">
            <span class="dash-stat__icon" aria-hidden="true"><i data-lucide="life-buoy"></i></span>
            <span>
                <span class="dash-stat__value">{{ number_format($stats['total'] ?? $tickets->total()) }}</span>
                <span class="dash-stat__label">{{ __('Total tickets') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon dash-stat__icon-accent" aria-hidden="true"><i data-lucide="message-circle"></i></span>
            <span>
                <span class="dash-stat__value">{{ number_format($stats['active'] ?? 0) }}</span>
                <span class="dash-stat__label">{{ __('Active conversations') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon" aria-hidden="true"><i data-lucide="circle-check"></i></span>
            <span>
                <span class="dash-stat__value">{{ number_format($stats['resolved'] ?? 0) }}</span>
                <span class="dash-stat__label">{{ __('Resolved or closed') }}</span>
            </span>
        </div>

        <div class="dash-stat">
            <span class="dash-stat__icon dash-stat__icon-accent" aria-hidden="true"><i data-lucide="timer"></i></span>
            <span>
                <span class="dash-stat__value">
                    {{ ($stats['lastActivity'] ?? null) ? $stats['lastActivity']->diffForHumans() : __('None') }}
                </span>
                <span class="dash-stat__label">{{ __('Last activity') }}</span>
            </span>
        </div>
    </div>

    <section class="panel mt-lg" aria-labelledby="support-tickets-title">
        <div class="panel__head">
            <h2 class="panel__title" id="support-tickets-title">
                <i data-lucide="inbox"></i>
                {{ __('Ticket queue') }}
            </h2>
            <p class="panel__subtitle">
                {{ __('Use filters to find a request, then open the thread for replies and status history.') }}
            </p>
        </div>

        <form class="panel__body" action="{{ route('user.support-tickets.index') }}" method="get">
            <div class="form-grid form-grid-4">
                <div class="field">
                    <label class="field__label" for="support-search">{{ __('Search') }}</label>
                    <div class="input-group">
                        <span class="input-group__icon" aria-hidden="true"><i data-lucide="search"></i></span>
                        <input class="input" type="search" id="support-search" name="search"
                               value="{{ request('search') }}" placeholder="{{ __('Reference, subject or message') }}">
                    </div>
                </div>

                <div class="field">
                    <label class="field__label" for="support-status">{{ __('Status') }}</label>
                    <select class="select" id="support-status" name="status">
                        <option value="">{{ __('All statuses') }}</option>
                        @foreach ($statuses as $value => $meta)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label class="field__label" for="support-priority">{{ __('Priority') }}</label>
                    <select class="select" id="support-priority" name="priority">
                        <option value="">{{ __('All priorities') }}</option>
                        @foreach ($priorities as $value => $meta)
                            <option value="{{ $value }}" @selected(request('priority') === $value)>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label class="field__label" for="support-sort">{{ __('Sort') }}</label>
                    <select class="select" id="support-sort" name="sort_by">
                        @foreach ([
                            'created_at' => __('Created date'),
                            'last_reply_at' => __('Last activity'),
                            'priority' => __('Priority'),
                            'status' => __('Status'),
                            'subject' => __('Subject'),
                        ] as $value => $label)
                            <option value="{{ $value }}" @selected(request('sort_by', 'created_at') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">

            <div class="cluster cluster-sm mt-md">
                <button type="submit" class="btn btn-primary btn-sm" data-ripple>
                    <i data-lucide="filter"></i>
                    {{ __('Apply filters') }}
                </button>
                <a class="btn btn-ghost btn-sm" href="{{ route('user.support-tickets.index') }}">
                    {{ __('Reset') }}
                </a>
            </div>
        </form>

        <div class="panel__body panel__body-flush">
            <div class="table-scroll">
                <table class="data-table">
                    <caption class="sr-only">{{ __('Support ticket history') }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Ticket') }}</th>
                            <th scope="col">{{ __('Category') }}</th>
                            <th scope="col">{{ __('Priority') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col">{{ __('Last activity') }}</th>
                            <th scope="col"><span class="sr-only">{{ __('Open') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $ticket)
                            @php
                                $statusMeta = $statuses[$ticket->status] ?? ['label' => $ticket->status];
                                $priorityMeta = $priorities[$ticket->priority] ?? ['label' => $ticket->priority];
                            @endphp
                            <tr>
                                <td>
                                    <a class="data-table__strong" href="{{ route('user.support-tickets.show', $ticket) }}">
                                        {{ $ticket->reference }}
                                    </a>
                                    <span class="setting-row__hint">{{ \Illuminate\Support\Str::limit($ticket->subject, 72) }}</span>
                                </td>
                                <td>{{ $ticket->category ?: __('General') }}</td>
                                <td>
                                    <span class="badge badge-sm {{ $priorityBadge($ticket->priority) }}">
                                        {{ $priorityMeta['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-sm {{ $statusBadge($ticket->status) }}">
                                        {{ $statusMeta['label'] }}
                                    </span>
                                </td>
                                <td class="data-table__num">
                                    {{ $ticket->last_reply_at ? format_date($ticket->last_reply_at, true) : __('No replies yet') }}
                                </td>
                                <td class="data-table__end">
                                    <a class="icon-btn" href="{{ route('user.support-tickets.show', $ticket) }}" aria-label="{{ __('Open ticket :reference', ['reference' => $ticket->reference]) }}">
                                        <i data-lucide="arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <span class="empty-state__icon" aria-hidden="true"><i data-lucide="inbox"></i></span>
                                        <h3>{{ __('No support tickets found') }}</h3>
                                        <p>{{ __('Open a new ticket when you need help with renders, billing or your account.') }}</p>
                                        <a class="btn btn-primary btn-sm" href="{{ route('user.support-tickets.create') }}" data-ripple>
                                            <i data-lucide="plus"></i>
                                            {{ __('New ticket') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel__foot">
            <p class="pagination__meta">
                @if ($tickets->total() > 0)
                    {{ __('Showing :first-:last of :total tickets', ['first' => $tickets->firstItem(), 'last' => $tickets->lastItem(), 'total' => $tickets->total()]) }}
                @else
                    {{ __('Showing 0 tickets') }}
                @endif
            </p>

            @if ($tickets->hasPages())
                <nav class="pagination" aria-label="{{ __('Ticket pages') }}">
                    @if ($tickets->onFirstPage())
                        <span class="pagination__item is-disabled" aria-disabled="true" aria-label="{{ __('Previous page') }}">
                            <i data-lucide="chevron-left"></i>
                        </span>
                    @else
                        <a class="pagination__item" href="{{ $tickets->previousPageUrl() }}" aria-label="{{ __('Previous page') }}">
                            <i data-lucide="chevron-left"></i>
                        </a>
                    @endif

                    @php($previousRenderedPage = null)
                    @foreach ($pageWindow as $page)
                        @if ($previousRenderedPage && $page > $previousRenderedPage + 1)
                            <span class="pagination__gap">...</span>
                        @endif

                        @if ($page === $tickets->currentPage())
                            <span class="pagination__item is-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="pagination__item" href="{{ $tickets->url($page) }}">{{ $page }}</a>
                        @endif

                        @php($previousRenderedPage = $page)
                    @endforeach

                    @if ($tickets->hasMorePages())
                        <a class="pagination__item" href="{{ $tickets->nextPageUrl() }}" aria-label="{{ __('Next page') }}">
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
    </section>
</x-layouts.user>
