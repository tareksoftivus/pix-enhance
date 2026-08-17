<x-layouts.admin :title="__('View Pricing Plan')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ $pricingPlan->name }}</h1>
            <div class="flex items-center gap-2">
                <x-ui.button variant="outline" href="{{ route('admin.pricing-plans.edit', $pricingPlan) }}">
                    <i class="ph ph-pencil-simple"></i> {{ __('Edit') }}
                </x-ui.button>
                <x-ui.button variant="outline" href="{{ route('admin.pricing-plans.index') }}">
                    <i class="ph ph-arrow-left"></i> {{ __('Back') }}
                </x-ui.button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 2xl:grid-cols-2">
            <div class="section-card">
                <h2 class="heading-5 text-neutral-950 mb-4">{{ __('Plan Details') }}</h2>
                <div class="space-y-3">
                    <div class="f-between py-2 border-b border-neutral-100">
                        <span class="text-sm text-neutral-500">{{ __('Name') }}</span>
                        <span class="text-sm font-semibold text-title">{{ $pricingPlan->name }}</span>
                    </div>
                    <div class="f-between py-2 border-b border-neutral-100">
                        <span class="text-sm text-neutral-500">{{ __('Slug') }}</span>
                        <span class="text-sm font-medium text-neutral-600">{{ $pricingPlan->slug }}</span>
                    </div>
                    @if($pricingPlan->tagline)
                        <div class="f-between py-2 border-b border-neutral-100">
                            <span class="text-sm text-neutral-500">{{ __('Tagline') }}</span>
                            <span class="text-sm text-neutral-600">{{ $pricingPlan->tagline }}</span>
                        </div>
                    @endif
                    <div class="f-between py-2 border-b border-neutral-100">
                        <span class="text-sm text-neutral-500">{{ __('Status') }}</span>
                        @if($pricingPlan->is_active)
                            <span class="badge bg-success/10 text-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-error/10 text-error">{{ __('Inactive') }}</span>
                        @endif
                    </div>
                    <div class="f-between py-2 border-b border-neutral-100">
                        <span class="text-sm text-neutral-500">{{ __('Featured') }}</span>
                        @if($pricingPlan->is_featured)
                            <span class="badge bg-primary/10 text-primary">{{ __('Yes') }}</span>
                        @else
                            <span class="text-sm text-neutral-400">{{ __('No') }}</span>
                        @endif
                    </div>
                    @if($pricingPlan->icon)
                        <div class="f-between py-2 border-b border-neutral-100">
                            <span class="text-sm text-neutral-500">{{ __('Icon') }}</span>
                            <span class="text-sm font-medium text-neutral-600">{{ $pricingPlan->icon }}</span>
                        </div>
                    @endif
                    <div class="f-between py-2 border-b border-neutral-100">
                        <span class="text-sm text-neutral-500">{{ __('Sort Order') }}</span>
                        <span class="text-sm font-medium text-title">{{ $pricingPlan->sort_order }}</span>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h2 class="heading-5 text-neutral-950 mb-4">{{ __('Pricing') }}</h2>
                <div class="space-y-3">
                    <div class="f-between py-2 border-b border-neutral-100">
                        <span class="text-sm text-neutral-500">{{ __('Monthly Price') }}</span>
                        <span class="text-sm font-bold text-title">{{ $pricingPlan->price_monthly > 0 ? '$' . number_format($pricingPlan->price_monthly) : __('Free') }}</span>
                    </div>
                    <div class="f-between py-2 border-b border-neutral-100">
                        <span class="text-sm text-neutral-500">{{ __('Yearly Price') }}</span>
                        <span class="text-sm font-bold text-title">${{ number_format($pricingPlan->price_yearly) }}</span>
                    </div>
                    <div class="f-between py-2 border-b border-neutral-100">
                        <span class="text-sm text-neutral-500">{{ __('Credits / Month') }}</span>
                        <span class="text-sm font-bold text-title">{{ number_format($pricingPlan->credits_monthly) }}</span>
                    </div>
                    <div class="f-between py-2 border-b border-neutral-100">
                        <span class="text-sm text-neutral-500">{{ __('Button Text') }}</span>
                        <span class="text-sm font-medium text-neutral-600">{{ $pricingPlan->cta_label }}</span>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h2 class="heading-5 text-neutral-950 mb-4">{{ __('Features') }}</h2>
                @if(! empty($pricingPlan->features))
                    <ul class="space-y-2">
                        @foreach($pricingPlan->features as $feature)
                            <li class="flex items-center gap-2 text-sm text-neutral-600">
                                <i class="ph-fill ph-check-circle text-success"></i>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-neutral-400">{{ __('No features listed.') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
