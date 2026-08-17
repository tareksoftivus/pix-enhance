<x-layouts.admin :title="__('Edit Pricing Plan')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('Edit Pricing Plan') }}</h1>
            <x-ui.button variant="outline" href="{{ route('admin.pricing-plans.index') }}">
                <i class="ph ph-arrow-left"></i> {{ __('Back') }}
            </x-ui.button>
        </div>

        <div class="grid grid-cols-1 gap-6 2xl:grid-cols-2">
            <div class="section-card">
                <form method="POST" action="{{ route('admin.pricing-plans.update', $pricingPlan) }}"
                    class="space-y-4 max-w-4xl">
                    @csrf
                    @method('PUT')
                    @include('pricing-plans::admin.partials.form')
                    <div class="flex items-center gap-3 pt-4 border-t border-neutral-100">
                        <x-forms.submit :label="__('Update Plan')" />
                        <x-ui.button variant="ghost"
                            href="{{ route('admin.pricing-plans.index') }}">{{ __('Cancel') }}</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
