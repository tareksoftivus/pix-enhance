<x-layouts.admin :title="__('Testimonials')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="heading-4 text-neutral-950">{{ __('Testimonials') }}</h1>
            <x-ui.button variant="primary" href="{{ route('admin.testimonials.create') }}">
                <i class="ph ph-plus-circle"></i> {{ __('Add Testimonial') }}
            </x-ui.button>
        </div>

        <div class="section-card">
            <x-tables.resource :definition="$table" :items="$testimonials" />
        </div>
    </div>
</x-layouts.admin>
