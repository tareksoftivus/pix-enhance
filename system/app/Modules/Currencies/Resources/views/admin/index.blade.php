<x-layouts.admin :title="__('Currencies')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('Currencies') }}</h1>
            <x-ui.button variant="primary" href="{{ route('admin.currencies.create') }}">
                <i class="ph ph-plus-circle"></i> {{ __('Add Currency') }}
            </x-ui.button>
        </div>

        <div class="section-card">
            <x-tables.resource :definition="$table" :items="$currencies" />
        </div>
    </div>
</x-layouts.admin>
