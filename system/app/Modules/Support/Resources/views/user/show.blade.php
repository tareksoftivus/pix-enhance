<x-layouts.user :title="$ticket->reference">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="heading-4 text-neutral-950">{{ $ticket->subject }}</h1>
                <p class="mt-1 flex items-center gap-2 text-sm text-neutral-500">
                    {{ $ticket->reference }}
                    @php($statusMeta = \App\Modules\Support\Models\SupportTicket::statuses()[$ticket->status] ?? ['label' => $ticket->status, 'variant' => 'default'])
                    <x-ui.badge :variant="$statusMeta['variant']">{{ $statusMeta['label'] }}</x-ui.badge>
                </p>
            </div>
            <x-ui.button variant="outline" href="{{ route('user.support-tickets.index') }}">
                <i class="ph ph-arrow-left"></i> {{ __('Back') }}
            </x-ui.button>
        </div>

        @if ($ticket->isClosed())
            <div class="section-card text-center text-sm text-neutral-500">
                {{ __('This ticket is closed. Open a new ticket if you still need help.') }}
            </div>
        @else
            <div class="section-card">
                <h2 class="heading-6 mb-4 text-neutral-950">{{ __('Reply') }}</h2>
                <form method="POST" action="{{ route('user.support-tickets.reply', $ticket) }}" class="space-y-3">
                    @csrf
                    <x-forms.textarea name="message" :placeholder="__('Write your reply…')" :value="old('message')" rows="4" required />
                    <div class="flex justify-end">
                        <x-forms.submit :label="__('Send Reply')">
                            <i class="ph ph-paper-plane-tilt"></i> {{ __('Send Reply') }}
                        </x-forms.submit>
                    </div>
                </form>
            </div>
        @endif

        <div class="section-card">
            <h2 class="heading-6 mb-4 text-neutral-950">{{ __('Conversation') }}</h2>
            <x-support::thread
                :ticket="$ticket"
                :endpoint="route('user.support-tickets.messages', $ticket)"
                :first-page="$firstPage"
            />
        </div>
    </div>
</x-layouts.user>
