<x-layouts.admin :title="__('Blog Categories')">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <h1 class="heading-4 text-neutral-950">{{ __('Blog Categories') }}</h1>
            <x-ui.button variant="primary" href="{{ route('admin.blog-categories.create') }}">
                <i class="ph ph-plus-circle"></i> {{ __('New Category') }}
            </x-ui.button>
        </div>

        <div class="section-card">
            <x-tables.resource :definition="$table" :items="$categories" />
        </div>
    </div>
</x-layouts.admin>
