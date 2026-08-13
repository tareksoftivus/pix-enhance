<x-layouts.admin :title="$ticket->reference">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="heading-4 text-neutral-950">{{ $ticket->subject }}</h1>
                <p class="mt-1 text-sm text-neutral-500">
                    {{ $ticket->reference }} &middot; {{ $ticket->user?->name ?? __('Unknown') }}
                </p>
            </div>
            <x-ui.button variant="outline" href="{{ route('admin.support-tickets.index') }}">
                <i class="ph ph-arrow-left"></i> {{ __('Back') }}
            </x-ui.button>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Reply + conversation --}}
            <div class="space-y-6 lg:col-span-2">
                <div class="section-card">
                    <h2 class="heading-6 mb-4 text-neutral-950">{{ __('Reply') }}</h2>
                    <form method="POST" action="{{ route('admin.support-tickets.reply', $ticket) }}" class="space-y-3">
                        @csrf
                        <x-forms.textarea name="message" :placeholder="__('Write your reply…')" rows="4" required />
                        <div class="flex justify-end">
                            <x-forms.submit :label="__('Send Reply')">
                                <i class="ph ph-paper-plane-tilt"></i> {{ __('Send Reply') }}
                            </x-forms.submit>
                        </div>
                    </form>
                </div>

                <div class="section-card">
                    <h2 class="heading-6 mb-4 text-neutral-950">{{ __('Conversation') }}</h2>
                    <x-support::thread
                        :ticket="$ticket"
                        :endpoint="route('admin.support-tickets.messages', $ticket)"
                        :first-page="$firstPage"
                    />
                </div>
            </div>

            {{-- Meta + status --}}
            <div class="space-y-6">
                <div class="section-card">
                    <h2 class="heading-6 mb-4 text-neutral-950">{{ __('Ticket Details') }}</h2>

                    {{-- Status: compact select that auto-submits on change --}}
                    <form
                        method="POST"
                        action="{{ route('admin.support-tickets.status', $ticket) }}"
                        x-data
                    >
                        @csrf
                        @method('PATCH')
                        <label for="ticket-status" class="text-xs uppercase tracking-wider text-neutral-400">{{ __('Status') }}</label>
                        <div class="mt-1.5 flex items-center gap-2">
                            <select
                                id="ticket-status"
                                name="status"
                                class="select-field text-sm"
                                x-on:change="$el.form.requestSubmit()"
                            >
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected($ticket->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            {{-- No-JS fallback --}}
                            <noscript>
                                <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                            </noscript>
                        </div>
                    </form>

                    @php
                        $priorityMeta = \App\Modules\Support\Models\SupportTicket::priorities()[$ticket->priority] ?? ['label' => $ticket->priority, 'variant' => 'default'];
                        // Map the badge variant to the icon tile tint.
                        $priorityTint = match ($priorityMeta['variant']) {
                            'danger' => 'bg-error/10 text-error',
                            'warning' => 'bg-warning/10 text-warning',
                            'info' => 'bg-info/10 text-info',
                            default => 'bg-neutral-100 text-neutral-500',
                        };
                    @endphp

                    <div class="mt-5 space-y-3">
                        <div class="flex items-center gap-3 rounded-xl border border-neutral-100 bg-neutral-0 p-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $priorityTint }}">
                                <i class="ph ph-flag"></i>
                            </span>
                            <span class="text-sm text-neutral-500">{{ __('Priority') }}</span>
                            <span class="ms-auto"><x-ui.badge :variant="$priorityMeta['variant']">{{ $priorityMeta['label'] }}</x-ui.badge></span>
                        </div>

                        @if ($ticket->category)
                            <div class="flex items-center gap-3 rounded-xl border border-neutral-100 bg-neutral-0 p-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-info/10 text-info">
                                    <i class="ph ph-tag"></i>
                                </span>
                                <span class="text-sm text-neutral-500">{{ __('Category') }}</span>
                                <span class="ms-auto truncate ps-2 text-sm font-medium text-neutral-900">{{ ucfirst($ticket->category) }}</span>
                            </div>
                        @endif

                        <div class="flex items-center gap-3 rounded-xl border border-neutral-100 bg-neutral-0 p-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <i class="ph ph-user"></i>
                            </span>
                            <span class="text-sm text-neutral-500">{{ __('Requester') }}</span>
                            <span class="ms-auto truncate ps-2 text-sm font-medium text-neutral-900">{{ $ticket->user?->name ?? __('Unknown') }}</span>
                        </div>

                        <div class="flex items-center gap-3 rounded-xl border border-neutral-100 bg-neutral-0 p-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-success/10 text-success">
                                <i class="ph ph-clock"></i>
                            </span>
                            <span class="text-sm text-neutral-500">{{ __('Opened') }}</span>
                            <span class="ms-auto truncate ps-2 text-sm font-medium text-neutral-900" title="{{ format_date($ticket->created_at, true) }}">{{ $ticket->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
