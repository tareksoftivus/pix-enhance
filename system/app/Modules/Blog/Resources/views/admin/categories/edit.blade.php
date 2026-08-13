<x-layouts.admin :title="__('Edit Category')">
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <h1 class="heading-4 text-neutral-950">{{ __('Edit Category') }}</h1>
            <x-ui.button variant="outline" href="{{ route('admin.blog-categories.index') }}">
                <i class="ph ph-arrow-left"></i> {{ __('Back') }}
            </x-ui.button>
        </div>

        <div class="section-card">
            <form method="POST" action="{{ route('admin.blog-categories.update', $category) }}" class="max-w-2xl space-y-4">
                @csrf
                @method('PUT')
                <x-forms.input :label="__('Name')" name="name" :value="old('name', $category->name)" required />
                <x-forms.input :label="__('Slug')" name="slug" :value="old('slug', $category->slug)" />
                <x-forms.input type="number" :label="__('Sort Order')" name="sort_order" :value="old('sort_order', $category->sort_order)" />
                <x-forms.toggle :label="__('Active')" name="is_active" :checked="old('is_active', $category->is_active)" />
                <div class="flex items-center gap-3 border-t border-neutral-100 pt-4">
                    <x-forms.submit :label="__('Update Category')" />
                    <x-ui.button variant="ghost" href="{{ route('admin.blog-categories.index') }}">{{ __('Cancel') }}</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
