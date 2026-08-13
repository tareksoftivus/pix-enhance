<x-layouts.admin :title="__('Support Tickets')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('Support Tickets') }}</h1>
        </div>

        <div class="section-card">
            <x-tables.resource :definition="$table" :items="$tickets" />
        </div>
    </div>
</x-layouts.admin>
