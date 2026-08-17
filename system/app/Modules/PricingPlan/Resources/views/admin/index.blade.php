<x-layouts.admin :title="__('Pricing Plans')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('Pricing Plans') }}</h1>
            <div class="flex items-center gap-2">
                <x-ui.button variant="outline" href="{{ route('admin.pricing-plan-subscribers.index') }}">
                    <i class="ph ph-users-three"></i> {{ __('Subscribers') }}
                </x-ui.button>
                <x-ui.button variant="primary" href="{{ route('admin.pricing-plans.create') }}">
                    <i class="ph ph-plus-circle"></i> {{ __('Add Plan') }}
                </x-ui.button>
            </div>
        </div>

        <div class="section-card">
            <x-tables.resource :definition="$table" :items="$pricing_plans" />
        </div>
    </div>
</x-layouts.admin>
