@php
    $featuresText = old('features', $pricingPlan ? implode("\n", $pricingPlan->features ?? []) : '');
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <x-forms.input :label="__('Name')" name="name" :value="old('name', $pricingPlan?->name)" required :placeholder="__('Plan Name')" />
    <x-forms.input :label="__('Slug')" name="slug" :value="old('slug', $pricingPlan?->slug)" required :placeholder="__('plan-slug')" />
</div>

<x-forms.textarea :label="__('Tagline')" name="tagline" :value="old('tagline', $pricingPlan?->tagline)" :placeholder="__('One line describing who this plan is for...')" />

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <x-forms.input :label="__('Icon')" name="icon" :value="old('icon', $pricingPlan?->icon)" :placeholder="__('ph-rocket')" />
    <x-forms.input :label="__('Sort Order')" name="sort_order" type="number" :value="old('sort_order', $pricingPlan?->sort_order ?? 0)" min="0" :placeholder="__('0')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <x-forms.input :label="__('Monthly Price (USD)')" name="price_monthly" type="number" :value="old('price_monthly', $pricingPlan?->price_monthly ?? 0)" min="0" required :placeholder="__('0')" />
    <x-forms.input :label="__('Yearly Price (USD)')" name="price_yearly" type="number" :value="old('price_yearly', $pricingPlan?->price_yearly ?? 0)" min="0" required :placeholder="__('0')" />
    <x-forms.input :label="__('Credits / Month')" name="credits_monthly" type="number" :value="old('credits_monthly', $pricingPlan?->credits_monthly ?? 0)" min="0" required :placeholder="__('1000')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <x-forms.toggle :label="__('Active')" name="is_active" :checked="old('is_active', $pricingPlan?->is_active ?? true)" />
    <x-forms.toggle :label="__('Featured')" name="is_featured" :checked="old('is_featured', $pricingPlan?->is_featured ?? false)" />
</div>

<x-forms.input :label="__('Button Text')" name="cta_label" :value="old('cta_label', $pricingPlan?->cta_label ?? 'Start free')" :placeholder="__('Start free')" />

<div class="border-t border-neutral-100 pt-4">
    <h2 class="heading-5 text-neutral-950 mb-3">{{ __('Features') }}</h2>
    <x-forms.textarea :label="__('Feature List (one per line)')" name="features" :value="$featuresText" rows="6" :placeholder="__('Unlimited map searches')" />
</div>
