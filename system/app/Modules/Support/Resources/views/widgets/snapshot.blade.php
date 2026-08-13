@php
    $statuses = \App\Modules\Support\Models\SupportTicket::statuses();

    // Count tiles shown above the ticket list — full soft-colour backgrounds.
    $stats = [
        ['label' => __('Open'), 'value' => $counts['open'], 'bg' => 'bg-info/10', 'text' => 'text-info'],
        ['label' => __('Pending'), 'value' => $counts['pending'], 'bg' => 'bg-warning/10', 'text' => 'text-warning'],
        ['label' => __('Urgent'), 'value' => $counts['urgent'], 'bg' => 'bg-error/10', 'text' => 'text-error'],
    ];
@endphp

<div class="section-card">
    <div class="flex items-center justify-between gap-3">
        <h2 class="heading-5 text-neutral-950">{{ __('Support Tickets') }}</h2>
        @if(Route::has('admin.support-tickets.index'))
            <a href="{{ route('admin.support-tickets.index') }}" class="text-sm text-primary hover:underline">{{ __('View All') }}</a>
        @endif
    </div>

    <div class="mt-4 grid grid-cols-3 gap-3">
        @foreach($stats as $stat)
            <div class="rounded-xl px-3 py-2.5 text-center {{ $stat['bg'] }}">
                <span class="block text-lg font-bold {{ $stat['text'] }}">{{ $stat['value'] }}</span>
                <span class="mt-0.5 block text-[11px] uppercase tracking-wider {{ $stat['text'] }}">{{ $stat['label'] }}</span>
            </div>
        @endforeach
    </div>

    @if($latestTickets->isNotEmpty())
        <div class="mt-3 space-y-3">
            @foreach($latestTickets as $ticket)
                @php($statusMeta = $statuses[$ticket->status] ?? ['label' => ucfirst($ticket->status), 'variant' => 'default'])
                <a href="{{ Route::has('admin.support-tickets.show') ? route('admin.support-tickets.show', $ticket) : '#' }}" class="flex items-center gap-3 rounded-xl border border-neutral-100 bg-neutral-0 p-3 transition-colors hover:border-neutral-200">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <i class="ph ph-chat-centered-text"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-neutral-900">{{ $ticket->subject }}</p>
                        <p class="truncate text-xs text-neutral-400">
                            {{ $ticket->reference }}
                            <span class="text-neutral-300">&middot;</span>
                            {{ $ticket->user?->name ?? __('Unknown') }}
                            <span class="text-neutral-300">&middot;</span>
                            {{ ($ticket->last_reply_at ?? $ticket->created_at)->diffForHumans() }}
                        </p>
                    </div>
                    <div class="shrink-0">
                        <x-ui.badge :variant="$statusMeta['variant']">{{ $statusMeta['label'] }}</x-ui.badge>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <p class="mt-3 text-sm text-neutral-400">{{ __('No open tickets. All caught up!') }}</p>
    @endif
</div>
