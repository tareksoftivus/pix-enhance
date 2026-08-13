<x-layouts.admin :title="__('Blog Posts')">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <h1 class="heading-4 text-neutral-950">{{ __('Blog Posts') }}</h1>
            <div class="flex items-center gap-3">
                <x-ui.button variant="outline" href="{{ route('admin.blog-categories.index') }}">
                    <i class="ph ph-folders"></i> {{ __('Categories') }}
                </x-ui.button>
                <x-ui.button variant="primary" href="{{ route('admin.blog-posts.create') }}">
                    <i class="ph ph-plus-circle"></i> {{ __('New Post') }}
                </x-ui.button>
            </div>
        </div>

        <div class="section-card">
            <x-tables.resource :definition="$table" :items="$posts" />
        </div>
    </div>
</x-layouts.admin>
