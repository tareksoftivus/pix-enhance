<x-layouts.user :title="__('Support')">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <h1 class="heading-4 text-neutral-950">{{ __('Support') }}</h1>
            <x-ui.button variant="primary" href="{{ route('user.support-tickets.create') }}">
                <i class="ph ph-plus-circle"></i> {{ __('New Ticket') }}
            </x-ui.button>
        </div>

        <div class="section-card">
            <x-tables.resource :definition="$table" :items="$tickets" />
        </div>
    </div>
</x-layouts.user>
