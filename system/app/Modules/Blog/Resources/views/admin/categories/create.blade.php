<x-layouts.admin :title="__('New Category')">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <h1 class="heading-4 text-neutral-950">{{ __('New Category') }}</h1>
            <x-ui.button variant="outline" href="{{ route('admin.blog-categories.index') }}">
                <i class="ph ph-arrow-left"></i> {{ __('Back') }}
            </x-ui.button>
        </div>

        <div class="section-card">
            <form method="POST" action="{{ route('admin.blog-categories.store') }}" class="max-w-2xl space-y-4">
                @csrf
                <x-forms.input :label="__('Name')" name="name" :value="old('name')" required />
                <x-forms.input :label="__('Slug')" name="slug" :value="old('slug')" :hint="__('Optional — generated from the name if left blank.')" />
                <x-forms.input type="number" :label="__('Sort Order')" name="sort_order" :value="old('sort_order', 0)" />
                <x-forms.toggle :label="__('Active')" name="is_active" :checked="true" />
                <div class="flex items-center gap-3 border-t border-neutral-100 pt-4">
                    <x-forms.submit :label="__('Create Category')" />
                    <x-ui.button variant="ghost" href="{{ route('admin.blog-categories.index') }}">{{ __('Cancel') }}</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
