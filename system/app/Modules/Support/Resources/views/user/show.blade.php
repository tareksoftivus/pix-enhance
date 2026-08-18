@php
    $statuses = \App\Modules\Support\Models\SupportTicket::statuses();
    $priorities = \App\Modules\Support\Models\SupportTicket::priorities();
    $statusMeta = $statuses[$ticket->status] ?? ['label' => $ticket->status];
    $priorityMeta = $priorities[$ticket->priority] ?? ['label' => $ticket->priority];
    $statusBadge = match ($ticket->status) {
        'open' => 'badge-info',
        'pending' => 'badge-warning',
        'resolved' => 'badge-success',
        'closed' => '',
        default => '',
    };
    $priorityBadge = match ($ticket->priority) {
        'urgent' => 'badge-danger',
        'high' => 'badge-warning',
        'medium' => 'badge-primary',
        default => '',
    };
@endphp

<x-layouts.user :title="$ticket->reference" :search-placeholder="__('Search tickets')">
    <div class="dash__head">
        <div>
            <h1 class="dash__title">{{ $ticket->subject }}</h1>
            <p class="dash__subtitle">
                {{ $ticket->reference }} · {{ __('Opened :date', ['date' => format_date($ticket->created_at, true)]) }}
            </p>
        </div>

        <div class="cluster cluster-sm">
            <a class="btn btn-outline btn-sm" href="{{ route('user.support-tickets.index') }}">
                <i data-lucide="arrow-left"></i>
                {{ __('Back to support') }}
            </a>
            <a class="btn btn-soft btn-sm" href="{{ route('user.support-tickets.create') }}">
                <i data-lucide="plus"></i>
                {{ __('New ticket') }}
            </a>
        </div>
    </div>

    <section class="plan-summary" aria-labelledby="ticket-summary-title">
        <div>
            <h2 class="plan-summary__name" id="ticket-summary-title">
                <i data-lucide="life-buoy"></i>
                {{ $ticket->reference }}
                <span class="badge badge-sm {{ $statusBadge }}">{{ $statusMeta['label'] }}</span>
            </h2>

            <p class="plan-summary__price">
                <strong>{{ $priorityMeta['label'] }}</strong> {{ __('priority') }} · {{ $ticket->category ?: __('General') }}
            </p>

            <div class="plan-summary__actions">
                <span class="badge badge-sm {{ $priorityBadge }}">
                    <i data-lucide="flag"></i>
                    {{ $priorityMeta['label'] }}
                </span>
                <span class="badge badge-sm {{ $statusBadge }}">
                    <i data-lucide="activity"></i>
                    {{ $statusMeta['label'] }}
                </span>
            </div>
        </div>

        <div>
            <div class="plan-summary__meter-head">
                <span class="plan-summary__meter-label">{{ __('Last activity') }}</span>
                <span class="plan-summary__meter-value">
                    {{ $ticket->last_reply_at ? $ticket->last_reply_at->diffForHumans() : __('No replies yet') }}
                </span>
            </div>

            <div class="progress" data-progress="{{ $ticket->isClosed() ? 100 : ($ticket->status === 'pending' ? 70 : 35) }}">
                <div class="progress__bar"></div>
            </div>

            <p class="plan-summary__meter-note">
                {{ $ticket->isClosed() ? __('This request is closed.') : __('The conversation remains open for replies from you and support.') }}
            </p>
        </div>
    </section>

    <div class="form-grid form-grid-2 mt-lg">
        <section class="panel" aria-labelledby="conversation-title">
            <div class="panel__head">
                <h2 class="panel__title" id="conversation-title">
                    <i data-lucide="messages-square"></i>
                    {{ __('Conversation') }}
                </h2>
                <p class="panel__subtitle">{{ __('Newest messages are shown first. Older messages load as you scroll.') }}</p>
            </div>

            <div class="panel__body">
                <x-support::thread
                    :ticket="$ticket"
                    :endpoint="route('user.support-tickets.messages', $ticket)"
                    :first-page="$firstPage"
                />
            </div>
        </section>

        <aside>
            @if ($ticket->isClosed())
                <section class="panel">
                    <div class="panel__head">
                        <h2 class="panel__title">
                            <i data-lucide="circle-check"></i>
                            {{ __('Ticket closed') }}
                        </h2>
                    </div>
                    <div class="panel__body">
                        <div class="setting-row">
                            <span class="setting-row__icon setting-row__icon-accent" aria-hidden="true"><i data-lucide="check"></i></span>
                            <span class="setting-row__text">
                                <span class="setting-row__hint">
                                    {{ __('This ticket is closed. Open a new ticket if you still need help with this issue.') }}
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="panel__foot">
                        <p class="panel__note">{{ __('Closed tickets stay available for reference.') }}</p>
                        <a class="btn btn-primary btn-sm" href="{{ route('user.support-tickets.create') }}" data-ripple>
                            <i data-lucide="plus"></i>
                            {{ __('Open new ticket') }}
                        </a>
                    </div>
                </section>
            @else
                <section class="panel" aria-labelledby="reply-title">
                    <div class="panel__head">
                        <h2 class="panel__title" id="reply-title">
                            <i data-lucide="reply"></i>
                            {{ __('Reply') }}
                        </h2>
                        <p class="panel__subtitle">{{ __('Add context, screenshots links, job IDs or follow-up questions.') }}</p>
                    </div>

                    <form method="POST" action="{{ route('user.support-tickets.reply', $ticket) }}">
                        @csrf

                        <div class="panel__body">
                            <div class="field">
                                <label class="field__label" for="ticket-reply">{{ __('Message') }}</label>
                                <textarea class="textarea @error('message') is-invalid @enderror" id="ticket-reply" name="message"
                                          rows="7" required placeholder="{{ __('Write your reply') }}">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="field__error"><i data-lucide="circle-alert"></i>{{ $message }}</p>
                                @else
                                    <p class="field__hint">{{ __('Maximum 5,000 characters.') }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="panel__foot">
                            <p class="panel__note">{{ __('Support will see this in the same thread.') }}</p>
                            <button type="submit" class="btn btn-primary btn-sm" data-ripple>
                                <i data-lucide="send"></i>
                                {{ __('Send reply') }}
                            </button>
                        </div>
                    </form>
                </section>
            @endif

            <section class="panel">
                <div class="panel__head">
                    <h2 class="panel__title">
                        <i data-lucide="info"></i>
                        {{ __('Ticket details') }}
                    </h2>
                </div>

                <div class="panel__body">
                    <div class="setting-row">
                        <span class="setting-row__text">
                            <span class="setting-row__label">{{ __('Reference') }}</span>
                            <span class="setting-row__hint">{{ $ticket->reference }}</span>
                        </span>
                    </div>
                    <div class="setting-row">
                        <span class="setting-row__text">
                            <span class="setting-row__label">{{ __('Category') }}</span>
                            <span class="setting-row__hint">{{ $ticket->category ?: __('General') }}</span>
                        </span>
                    </div>
                    <div class="setting-row">
                        <span class="setting-row__text">
                            <span class="setting-row__label">{{ __('Created') }}</span>
                            <span class="setting-row__hint">{{ format_date($ticket->created_at, true) }}</span>
                        </span>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</x-layouts.user>
