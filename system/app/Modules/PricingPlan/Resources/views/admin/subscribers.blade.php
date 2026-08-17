<x-layouts.admin :title="__('Subscribers')">
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="heading-4 text-neutral-950">{{ __('Subscribers') }}</h1>
                <p class="mt-1 text-sm text-neutral-500">{{ __('Latest active pricing plan for each user based on granted plan credits.') }}</p>
            </div>
            <x-ui.button variant="outline" href="{{ route('admin.pricing-plans.index') }}">
                <i class="ph ph-arrow-left"></i> {{ __('Pricing Plans') }}
            </x-ui.button>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <x-ui.kpi-card
                :title="__('Total Subscribers')"
                :value="number_format($stats['total'])"
                icon="ph-users-three"
                color="primary"
            />
            <x-ui.kpi-card
                :title="__('Paid Subscribers')"
                :value="number_format($stats['paid'])"
                icon="ph-credit-card"
                color="success"
                :change="$stats['free'] . ' ' . __('on free plans')"
                changeType="neutral"
            />
            <x-ui.kpi-card
                :title="__('Credits Granted')"
                :value="number_format($stats['credits_granted'])"
                icon="ph-coins"
                color="warning"
            />
        </div>

        <div class="section-card">
            <x-tables.resource :definition="$table" :items="$subscribers" />
        </div>
    </div>
</x-layouts.admin>
